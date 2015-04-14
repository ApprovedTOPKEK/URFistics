<?php
/**
 * Script fetching all the player statistics for given arguments. Either gives them back as JSON or doesnt do anything (Variables used in other scripts)
 */
/**
 * If called by a HTTP-Request directly, $settings won't be initialized. Make sure to build a valid URL.
 */

//Include necessary scripts
include_once "VarInclude.php";

//Include utility method to build query. In another PHP File for clarity (MANAPROBLEMS INCOMING) n stuff
include_once 'StatQuery.php';

//Selected region
$ps_region = isset($_GET['pregion']) ? $_GET['pregion'] : $settings['defaultRegion'];

//Selected gamemode
$ps_gameMode = isset($_GET['gamemode']) ? $_GET['gamemode'] : $settings['defaultGamemode'];

//User to fetch
$ps_Username = $_GET['username'];

//TODO CHECK IF GET EMPTY ETC
//TODO: cURL: add timeout loop (cuz of restricted apikeys)

////// MAKE API CALLS, STORE USER-DATA IN DB TO FETCH LATER (A few unnecessary extra calls but who cares)
$summoner = json_decode(cURLRequest("https://".$ps_region.".api.pvp.net/api/lol/".$ps_region."/v1.4/summoner/by-name/".rawurlencode($ps_Username)."?api_key=".$apiKey));
$games = json_decode(cURLRequest("https://".$ps_region.".api.pvp.net/api/lol/".$ps_region."/v1.3/game/by-summoner/".$summoner[0]["id"]."/recent?api_key=".$apiKey), true);

//maybe store teammates too in order to gain data?
foreach($games['games'] as $game){
	$match = json_decode(cURLRequest("https://".$ps_region.".api.pvp.net/api/lol/".$ps_region."/v2.2/match/".$game['gameId']."?includeTimeline=false&api_key=".$apiKey));
	foreach($match['participants'] as $participant){
		foreach($game['fellowPlayers'] as $participant2){
			if($participant2["championId"] == $participant['championId']){
				$sumID = $participant2['summonerId'];
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

				query("REPLACE INTO `".$settings['DefaultTable']."` (MatchId, Region, Gamemode, League, UserId, Score, Ban1, Ban2, Ban3, Pick, Spell1, Spell2, Item0, Item1, Item2, Item3, Item4, Item5, Kills, Deaths, Assists, Wards, Gold, CS, Doubles, Triples, Quadras, Pentas, LargestSpree, Drakes, Barons)"
				." VALUES ('".$game['gameId']."', '".$ps_region."', '".$ps_gameMode."', '".$league."', '".$sumID."', '".$score."', '".$ban1."', '".$ban2."', '".$ban3."', '".$champ."', '".$sumSpell1."', '".$sumSpell2."', '".$item0."', '".$item1."', '".$item2."', '".$item3."', '".$item4."', '".$item5."', '".$kills."', '".$deaths."', '".$assists."', '".$wards."', '".$gold."', '".$cs."', '".$doubleKills."', '".$tripleKills."', '".$quadraKills."', '".$pentaKills."', '".$largestKillingSpree."', '".$dragons."', '".$barons."');");*/
				saveGame($participant, $match, $sumID, $ps_region);
			}
		}
	}
}

//UNUSED
/*//Get structure of data-table to help build the query statement
$vanilla_select = query("SELECT * FROM (SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$dbName."' AND `TABLE_NAME`='".$dataTableName."') AS t1 WHERE COLUMN_NAME LIKE 'v_%';");
$avg_select = query("SELECT * FROM (SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$dbName."' AND `TABLE_NAME`='".$dataTableName."') AS t1 WHERE COLUMN_NAME LIKE 'v_%';");*/


//Get all DB-Entries corresponding to the chosen region and gamemode for player X.
$playerStats = statquery($region, $gameMode, $summoner[0]['id'], -1);
$playerStats['Summoner'] = $summoner[0];
echo $playerStats;

//Return as JSON?
if(isset($_GET['return']) && $_GET['return'] == "json") return json_encode($playerStats);


?>