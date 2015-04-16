<?php

//Include necessary things
include_once 'VarInclude.php';


//Process 5 MatchIDs in the database queue
$ids = query("SELECT matchid, region FROM matchidqueue LIMIT 0, 5;");
foreach($ids as $id){
	$msql = "https://" . $regions[$id['region']-1]['Region'] . ".api.pvp.net/api/lol/" . strtolower($regions[$id['region']-1]['Region']) . "/v2.2/match/" . $id['matchid'] . "?includeTimeline=false&api_key=" . $apiKey;
	$matchCurl = cURLRequest($msql);
	if ($matchCurl == "false") continue;
	$match = json_decode($matchCurl, true);
	foreach ($match['participants'] as $participant) {
		saveGame($participant, $match, -1, $regions[$id['region']-1]);
	}
	$conn->query("DELETE FROM matchidqueue WHERE MatchId='".$id['matchid']."';");
}

//Calculate if a new fetch is needed. If yes, fetch one. //TODO: Cronjob to call this all 10 min in order to decrease page loading time
$currentTime = time();
if(($currentTime - ($currentTime % $settings['FetchDelay'])) - $settings['LastFetched'] >= $settings['FetchDelay']){
	updateSettings();
    //TODO do it in another thread
    foreach($regions as $r) {
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