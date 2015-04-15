<?php

//Include necessary things
include_once 'VarInclude.php';


//Process 5 MatchIDs in the database queue
$ids = query("SELECT matchid FROM matchidqueue LIMIT 0, 5;");
//for

//Calculate if a new fetch is needed. If yes, fetch one. //TODO: Cronjob to call this all 10 min in order to decrease page loading time
$currentTime = time();
if(($currentTime - ($currentTime % $settings['FetchDelay'])) - $settings['LastFetched'] >= $settings['FetchDelay']){
	updateSettings();
    //TODO do it in another thread
    $regions = query("SELECT `ID`, `Region` FROM regions");
    foreach($regions as $r) {
	    //todo add gamemode parameter to URL when Riot will support it
		$sql="https://".$r['Region'].".api.pvp.net/api/lol/".strtolower($r['Region'])."/v4.1/game/ids?api_key=".$apiKey."&beginDate=".($currentTime - ($currentTime % $settings['FetchDelay']) - $settings['FetchDelay']);
	    $matchCurl = cURLRequest($sql);
	    if($matchCurl == "false") return;//continue;
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