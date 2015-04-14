<?php
/**
 * Page composed of two main components:
 * 1. Table of all average statistics for one specific gamemode, sorted in leagues
 * 2. Player-Averages, compared to all-averages. f.e: "You belong in GOLD!"
 */

/**  COMPONENT 1 **/

//Set "return" variable in get to avoid problems with other scripts
$_GET['return'] = "false";

include "../LeagueStats.php";
include "../PlayerStats.php";


?>