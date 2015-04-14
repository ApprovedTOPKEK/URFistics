<?php
/**
 * IMPLEMENTATION READY FOR EVERY GAMEMODE, BUT NEED ANOTHER API BY RIOT TO USE IT.
 */

//Set variable used by other scripts to check if they were called by this script
$index = true;

//Include necessary scripts
include_once "VarInclude.php";

//Include the first part of the HTML Code: Header + Navigation Bar and Top of the site (Not body) CONTAINS PHP CODE
include 'Templates/Header.php';

//Include the HTML Code of the page the user wants to see (Body) CONTAINS PHP CODE
if(isset($_GET['page']) && file_exists("Scripts/Pages/".$_GET['page'].".php") && file_exists("Templates/Pages/".$_GET['page'].".php")){
	include 'Scripts/Pages/'.$_GET['page'].".php";
	include 'Templates/Pages/'.$_GET['page'].".php";
}else{
	include 'Scripts/Pages/'.$settings['defaultPage'].".php";
	include 'Templates/Pages/'.$settings['defaultPage'].".php";
}

//Include the last part of the HTML Code: Footer (After body) CONTAINS PHP CODE
include 'Templates/Footer.php';