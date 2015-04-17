<?php
/**
 * Script fetching all the player statistics for given arguments. Either gives them back as JSON or doesnt do anything (Variables used in other scripts)
 */
/**
 * If called by a HTTP-Request directly, $settings won't be initialized. Make sure to build a valid URL.
 */

//Include necessary scripts
include_once "VarInclude.php";

//Include utility method to build query. In another PHP File for clarity (MANAPROBLEMS INCOMING) n stuff (outdated comment)
include_once 'StatQuery.php';

//Selected region
$ps_region = isset($_GET['pregion']) ? $_GET['pregion'] : $settings['defaultRegion'];

//Selected gamemode
$ps_gameMode = isset($_GET['gamemode']) ? $_GET['gamemode'] : $settings['defaultGamemode'];

//User to fetch. $veriefiedUser Variable used to see if the username-parameter is valid & output HTML accordingly.
$verifiedUser = true;
$ps_Username = isset($_GET['username'])?$_GET['username']:-123;
if($ps_Username == -123 || strlen($ps_Username) < 4) $verifiedUser = false;

//Process data in another thread? TODO!!! THIS WOULD BE AWESOME. No idea how to do it in PhP since it is one of my first times using this language...
$ps_async = isset($_GET['async']);

$ps_r = query("SELECT `ID`, `Region` FROM regions WHERE `ID` = ".$ps_region.";")[0];

////// MAKE API CALLS, STORE USER-DATA IN DB TO FETCH LATER (A few unnecessary extra calls but who cares)

//If the user is valid, get his matchhistory and save data. SAVE ALL THE DATAS!
if($verifiedUser){

	//Get the summoner data. If there is a problem with the request, set $verifiedUser to false just because we can.
	$curlr = cURLRequest("https://" . strtolower($ps_r['Region']) . ".api.pvp.net/api/lol/" . strtolower($ps_r['Region']) . "/v1.4/summoner/by-name/" . rawurlencode($ps_Username) . "?api_key=" . $apiKey);
	if($curlr != "false"){
		$summoner = json_decode($curlr, true);
		$games = json_decode(cURLRequest("https://" . strtolower($ps_r['Region']) . ".api.pvp.net/api/lol/" . strtolower($ps_r['Region']) . "/v1.3/game/by-summoner/" . $summoner[strtolower($ps_Username)]["id"] . "/recent?api_key=" . $apiKey), true);
		//maybe store teammates too in order to gain data?

		foreach($games['games'] as $game){

			//Add the player-ID&champion-ID of the requested player to the fellowPlayers array which doesnt contain the original player.
			array_push($game['fellowPlayers'], array("teamId" => $game['teamId'], "championId" => $game['championId'], "summonerId" => $summoner[strtolower($ps_Username)]['id']));

			//Get match, loop through participants & matchhistory-match participants (different backends, different results - Why the hell does "gamev13" contain summoner IDs but not the match backend?
			$match = json_decode(cURLRequest("https://".strtolower($ps_r['Region']).".api.pvp.net/api/lol/".strtolower($ps_r['Region'])."/v2.2/match/".$game['gameId']."?includeTimeline=false&api_key=".$apiKey), true);
			foreach($match['participants'] as $participant){
				foreach($game['fellowPlayers'] as $participant2){
					//Check if the two participants are the same, if yes, save data. If not, continue looping. ATTENTION: THIS CODE WONT WORK FOR "one for all"
					if($participant2["championId"] == $participant['championId'] && $participant2['teamId'] == $participant['teamId']){
						$sumID = $participant2['summonerId'];
						/*$team = $participant['teamId'];$league = $participant['highestAchievedSeasonTier'];$champ = $participant['championId'];$sumSpell1 = $participant['spell1Id'];$sumSpell2 = $participant['spell2Id'];$ban1 = $match['teams'][$team==100?0:1]['bans'][0]['championId'];$ban2 = $match['teams'][$team==100?0:1]['bans'][1]['championId'];$ban3 = $match['teams'][$team==100?0:1]['bans'][2]['championId'];$item0 = $participant['stats']['item0'];$item1 = $participant['stats']['item1'];$item2 = $participant['stats']['item2'];$item3 = $participant['stats']['item3'];$item4 = $participant['stats']['item4'];$item5 = $participant['stats']['item5'];$wards = $participant['stats']['wardsPlaced'];$kills = $participant['stats']['kills'];$deaths = $participant['stats']['deaths'];$assists = $participant['stats']['assists'];$gold = $participant['stats']['goldEarned'];$cs = $participant['stats']['minionsKilled'] + $participant['stats']['neutralMinionsKilled'];$doubleKills = $participant['stats']['doubleKills'];$tripleKills = $participant['stats']['tripleKills'];$quadraKills = $participant['stats']['quadraKills'];$pentaKills = $participant['stats']['pentaKills'];$largestKillingSpree = $participant['stats']['largestKillingSpree'];$dragons = $match['teams'][$team==100?0:1]['dragonKills'];$barons = $match['teams'][$team==100?0:1]['baronKills'];$score = calcScore($kills, $deaths, $assists, $cs, $dragons, $barons, $largestKillingSpree);query("REPLACE INTO `".$settings['DefaultTable']."` (MatchId, Region, Gamemode, League, UserId, Score, Ban1, Ban2, Ban3, Pick, Spell1, Spell2, Item0, Item1, Item2, Item3, Item4, Item5, Kills, Deaths, Assists, Wards, Gold, CS, Doubles, Triples, Quadras, Pentas, LargestSpree, Drakes, Barons)"." VALUES ('".$game['gameId']."', '".$ps_region."', '".$ps_gameMode."', '".$league."', '".$sumID."', '".$score."', '".$ban1."', '".$ban2."', '".$ban3."', '".$champ."', '".$sumSpell1."', '".$sumSpell2."', '".$item0."', '".$item1."', '".$item2."', '".$item3."', '".$item4."', '".$item5."', '".$kills."', '".$deaths."', '".$assists."', '".$wards."', '".$gold."', '".$cs."', '".$doubleKills."', '".$tripleKills."', '".$quadraKills."', '".$pentaKills."', '".$largestKillingSpree."', '".$dragons."', '".$barons."');");*/
						saveGame($participant, $match, $sumID, $ps_r);
						break;
					}
				}
			}
		}
	}else{
		$verifiedUser = false;

	}
	//If the user is valid after all those massive checks, fetch the stats for the player.
	if ($verifiedUser) {
		//Get all DB-Entries corresponding to the chosen region and gamemode for player X.
		$playerStats = statquery($ps_region, $ps_gameMode, $summoner[strtolower($ps_Username)]['id'], -1);
		$playerStats['Summoner'] = $summoner[strtolower($ps_Username)];

		//Return as JSON? Used for AJAX
		if (isset($_GET['return']) && $_GET['return'] == "json") echo json_encode($playerStats);
	}
}

?>