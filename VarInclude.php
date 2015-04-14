<?php

//Include database if not already done so
include_once "Database.php";

//Include settings if not already done so
$settings = array();
updateSettings();

//API-KEY
$apiKey = "b7ca061e-63e0-49cb-bac1-a4886089badf";

//Check if it is time to fetch new games & fetch 'em
include 'DataProcessor.php';

//cURL
function cURLRequest($link){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	echo $info['http_code'];
	if($info['http_code'] == 429){
		sleep(1);
		return cURLRequest($link);
	}
	if($info['http_code'] != 200){
		return "false";
	}
	return $data;
}

function calcScore($k, $d, $a, $cs, $dragons, $barons, $largestKillingSpree){
	return 30*$k - 40*$d + 25*$a + $cs + 100*$dragons + 150*$barons + 5*$largestKillingSpree;
}

function updateSettings(){
	global $settings, $conn;
	$rs = $conn->query("SELECT Setting, `Value` FROM Settings");
	$arr = array();
	while($row = $rs->fetch_assoc()){
		$arr[$row['Setting']] = $row['Value'];
	}
	$settings = $arr;
}

function saveGame($participant, $match, $uid, $r){
	global $conn;
	$team = $participant['teamId'];
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
		.$match['matchId']."', '"
		.$r['ID']."', '"
		.$gm[0]['id']."', '"
		.$l[0]['id']."', '".$uid."', '".$score."', '".$ban1."', '".$ban2."', '".$ban3."', '".$champ."', '".$sumSpell1."', '".$sumSpell2."', '".$item0."', '".$item1."', '".$item2."', '".$item3."', '".$item4."', '".$item5."', '".$kills."', '".$deaths."', '".$assists."', '".$wards."', '".$gold."', '".$cs."', '".$doubleKills."', '".$tripleKills."', '".$quadraKills."', '".$pentaKills."', '".$largestKillingSpree."', '".$dragons."', '".$barons."');";
	$conn->query($iq);
}

function debug($msg, $var){
	echo "-- Message --";
	echo "<br />";
	echo $msg.": ";
	var_dump($var);
	echo "<br />";
}
?>