<?php
/**
 * Page composed of two main components:
 * 1. Table of all average statistics for one specific gamemode, sorted in leagues
 * 2. Player-Averages, compared to all-averages. f.e: "You belong in GOLD!"
 */

if(!isset($index)) exit();

//Set "return" variable in get to avoid problems with other scripts
$_GET['return'] = "false";

//Include logic
include "LeagueStats.php";
include "PlayerStats.php";

//include view
include "Templates/Pages/cptma.phtml";

?>