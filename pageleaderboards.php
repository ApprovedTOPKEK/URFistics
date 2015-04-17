<?php

//Include necessary scripts
include_once "VarInclude.php";

//Include utility method to build query. In another PHP File for clarity (MANAPROBLEMS INCOMING) n stuff (outdated comment)
include_once 'StatQuery.php';

//Selected gamemode
$lb_gameMode = isset($_GET['gamemode']) ? $_GET['gamemode'] : $settings['defaultGamemode'];

//Best ten games. Maybe allow best ten players too? (Through averages)
$query = "SELECT UserId AS Top, Score, Region FROM statistics WHERE Gamemode=".$lb_gameMode." AND NOT UserId='-1' ORDER BY Score DESC LIMIT 0, 10";

$rs = query($query);
$lbdata = array();
foreach($rs as $v){
	$lb_r = query("SELECT `ID`, `Region` FROM regions WHERE `ID` = " . $v['Region'] . ";")[0];
	$summoner = json_decode(cURLRequest("https://" . strtolower($lb_r['Region']) . ".api.pvp.net/api/lol/" . strtolower($lb_r['Region']) . "/v1.4/summoner/".$v['Top']."?api_key=" . $apiKey), true);
	$lbplayerStats = statquery($v['Region'], $lb_gameMode, $v['Top'], -1);
	$lbplayerStats['Summoner'] = $summoner[$v['Top']];
	$lbplayerStats['RID'] = $v['Region'];
	$lbplayerStats['R'] = $lb_r['Region'];
	array_push($lbdata, $lbplayerStats);
}

include 'Templates/Pages/Leaderboards.phtml';