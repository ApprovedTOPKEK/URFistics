<?php
/**
 * Script fetching all the league statistics for given arguments. Either gives them back as JSON or doesnt do anything (Variables used in other scripts)
 */

//Include necessary scripts
include_once "VarInclude.php";

//Include utility method to build query. In another PHP File for clarity (MANAPROBLEMS INCOMING) n stuff
include_once 'StatQuery.php';

//Range of "league"-queries
$ls_leagues = query("SELECT * FROM `LEAGUES`");

//Selected region
$ls_region = isset($_GET['region']) ? $_GET['region'] : $settings['defaultRegion'];

//Selected gamemode
$ls_gameMode = isset($_GET['gamemode']) ? $_GET['gamemode'] : $settings['defaultGamemode'];

//UNUSED
/*//Get structure of data-table to help build the query statement
$vanilla_select = query("SELECT * FROM (SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$dbName."' AND `TABLE_NAME`='".$dataTableName."') AS t1 WHERE COLUMN_NAME LIKE 'v_%';");
$avg_select = query("SELECT * FROM (SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$dbName."' AND `TABLE_NAME`='".$dataTableName."') AS t1 WHERE COLUMN_NAME LIKE 'v_%';");*/

//Table array used by view
$data = array();

//Loop through leagues, and save results
foreach($ls_leagues as $index => $league){

	//Get all DB-Entries corresponding to the chosen region and gamemode and league.
	$data[$ls_leagues[$index]['ID']] = statquery($ls_region, $ls_gameMode, -1, $ls_leagues[$index]['ID']);
}

//Return as JSON?
if(isset($_GET['return']) && $_GET['return'] == "json") echo json_encode($data);


?>