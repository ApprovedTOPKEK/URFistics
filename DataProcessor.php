<?php

//Include necessary things
include_once 'VarInclude.php';


/**
 * Process some MatchIDs in the database queue
 **/

//Select the to-be-processed ID's
$ids = query("SELECT matchid, region FROM matchidqueue LIMIT 0, ".$settings['SavedIDsProcessorCount'].";");

//Loop through them
foreach($ids as $id){
	//String: The URL to the Match corresponding to the ID.
	$msql = "https://" . $regions[$id['region']-1]['Region'] . ".api.pvp.net/api/lol/" . strtolower($regions[$id['region']-1]['Region']) . "/v2.2/match/" . $id['matchid'] . "?includeTimeline=false&api_key=" . $apiKey;

	//JSON-Response of Rito-API
	$matchCurl = cURLRequest($msql);

	//Wrong HTTP-Result? Just continue as if nothing happened
	if ($matchCurl == "false") continue;

	//Convert JSON to Array
	$match = json_decode($matchCurl, true);

	//Loop through participants of the match
	foreach ($match['participants'] as $participant) {

		//Save data for each participant.
		saveGame($participant, $match, -1, $regions[$id['region']-1]);
	}

	//Delete the MatchID from the queue, not to process it again.
	$conn->query("DELETE FROM matchidqueue WHERE MatchId='".$id['matchid']."';");
}

//Calculate if a new fetch is needed. If yes, fetch a bucket. Not working anymore since URF is over. WE WANT URF BACK!!!! //TODO: Cronjob to call this all 10 min in order to decrease page loading time
$currentTime = time();
if($settings['DoFetch'] == "DoIt" && ($currentTime - ($currentTime % $settings['FetchDelay'])) - $settings['LastFetched'] >= $settings['FetchDelay']){

	//Update settings to store the time we fetched
	$conn->query("UPDATE `Settings` SET `Value` = '" . ($currentTime - ($currentTime % $settings['FetchDelay'])) . "' WHERE `Setting`='LastFetched';");
	updateSettings();

    //TODO do it in another thread
    foreach($regions as $r) {

	    //Loop through matches and participants of those matches, then save data (No time for comments)
	    //todo add gamemode parameter to URL when Riot will support it
		$sql="https://".$r['Region'].".api.pvp.net/api/lol/".strtolower($r['Region'])."/v4.1/game/ids?api_key=".$apiKey."&beginDate=".($currentTime - ($currentTime % $settings['FetchDelay']) - $settings['FetchDelay']);
	    $matchCurl = cURLRequest($sql);
	    if($matchCurl == "false") continue;
		$matchBucket = json_decode($matchCurl);
		//2064254690
		foreach($matchBucket as $matchid){
			$msql = "https://".$r['Region'].".api.pvp.net/api/lol/".strtolower($r['Region'])."/v2.2/match/".$matchid."?includeTimeline=false&api_key=".$apiKey;
            $match = json_decode(cURLRequest($msql), true);
            foreach($match['participants'] as $participant){
                saveGame($participant, $match, -1, $r);
            }
        }
    }

}
?>