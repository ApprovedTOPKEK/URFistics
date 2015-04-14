<?php

//Include necessary things
include_once 'VarInclude.php';

//Calculate if a new fetch is needed. If yes, fetch one. //TODO: Cronjob to call this all 10 min in order to decrease page loading time
$currentTime = time();
if(($currentTime - ($currentTime % $settings['FetchDelay'])) - $settings['LastFetched'] >= $settings['FetchDelay']){

	query("UPDATE `Settings` SET `Value` = '".($currentTime-($currentTime % $settings['FetchDelay']))."' WHERE `Setting`='LastFetched';");
	updateSettings();
    //TODO do it in another thread
    $regions = query("SELECT `ID`, `Region` FROM regions");
    foreach($regions as $r) {
	    //todo add gamemode parameter to URL when Riot will support it
		$sql="https://".$r['Region'].".api.pvp.net/api/lol/".strtolower($r['Region'])."/v4.1/game/ids?api_key=".$apiKey."&beginDate=".($currentTime - ($currentTime % $settings['FetchDelay']) - $settings['FetchDelay']);
        echo "<br />".$sql."<br />";
	    $matchCurl = cURLRequest($sql);
	    if($matchCurl == "false") return;//continue;
		$matchBucket = json_decode($matchCurl);
		//2064254690
		foreach($matchBucket as $matchid){
			$msql = "https://".$r['Region'].".api.pvp.net/api/lol/".strtolower($r['Region'])."/v2.2/match/".$matchid."?includeTimeline=false&api_key=".$apiKey;
            $match = json_decode(cURLRequest($msql), true);
            foreach($match['participants'] as $participant){
                saveGame($participant, $match, -1, $r);
                /*$team = $participant['teamId'];
                $league = $participant['highestAchievedSeasonTier'];
                $champ = $participant['championId'];
                $sumSpell1 = $participant['spell1Id'];
                $sumSpell2 = $participant['spell2Id'];
                $ban1 = $match['teams'][$team==100?0:1]['bans'][0]['championId'];
                $ban2 = $match['teams'][$team==100?0:1]['bans'][1]['championId'];
                $ban3 = $match['teams'][$team==100?0:1]['bans'][2]['championId'];
                $item0 = $participant['stats']['item0'];
                $item1 = $participant['stats']['item1'];
                $item2 = $participant['stats']['item2'];
                $item3 = $participant['stats']['item3'];
                $item4 = $participant['stats']['item4'];
                $item5 = $participant['stats']['item5'];
                $wards = $participant['stats']['wardsPlaced'];
                $kills = $participant['stats']['kills'];
                $deaths = $participant['stats']['deaths'];
                $assists = $participant['stats']['assists'];
                $gold = $participant['stats']['goldEarned'];
                $cs = $participant['stats']['minionsKilled'] + $participant['stats']['neutralMinionsKilled'];
                $doubleKills = $participant['stats']['doubleKills'];
                $tripleKills = $participant['stats']['tripleKills'];
                $quadraKills = $participant['stats']['quadraKills'];
                $pentaKills = $participant['stats']['pentaKills'];
                $largestKillingSpree = $participant['stats']['largestKillingSpree'];
                $dragons = $match['teams'][$team==100?0:1]['dragonKills'];
                $barons = $match['teams'][$team==100?0:1]['baronKills'];
                $score = calcScore($kills, $deaths, $assists, $cs, $dragons, $barons, $largestKillingSpree);
                $gm = query("SELECT id FROM Gamemodes WHERE Gamemode='".$match['queueType']."';");
                $l = query("SELECT id FROM Leagues WHERE League='".$league."';");
				$iq = "REPLACE INTO statistics (MatchId, Region, Gamemode, League, UserId, Score, Ban1, Ban2, Ban3, Pick, Spell1, Spell2, Item0, Item1, Item2, Item3, Item4, Item5, Kills, Deaths, Assists, Wards, Gold, CS, Doubles, Triples, Quadras, Pentas, LargestSpree, Drakes, Barons)"
					." VALUES ('"
						.$matchid."', '"
						.$r['ID']."', '"
						.$gm[0]['id']."', '"
						.$l[0]['id']."', '', '".$score."', '".$ban1."', '".$ban2."', '".$ban3."', '".$champ."', '".$sumSpell1."', '".$sumSpell2."', '".$item0."', '".$item1."', '".$item2."', '".$item3."', '".$item4."', '".$item5."', '".$kills."', '".$deaths."', '".$assists."', '".$wards."', '".$gold."', '".$cs."', '".$doubleKills."', '".$tripleKills."', '".$quadraKills."', '".$pentaKills."', '".$largestKillingSpree."', '".$dragons."', '".$barons."');";
                $conn->query($iq);*/

            }
        }
    }

}
?>