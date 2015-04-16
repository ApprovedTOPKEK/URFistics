<?php
/**
 * IMPLEMENTATION READY FOR EVERY GAMEMODE, BUT NEED ANOTHER API BY RIOT TO USE IT.
 */

//Set variable used by other scripts to check if they were called by this script
$index = true;

//Include necessary scripts, and some other nice thingies
include_once "VarInclude.php";

//Include the first part of the HTML Code: Header + Navigation Bar and Top of the site (Not body) CONTAINS PHP CODE
include 'Templates/Header.php';

//Include the HTML Code of the page the user wants to see (Body) CONTAINS PHP CODE; The page should contain logic + view.
if(isset($_GET['page']) && file_exists("page".$_GET['page'].".php")){
	include "page".$_GET['page'].".php";
}else{
	include 'page'.$settings['defaultPage'].".php";
}

//Include the last part of the HTML Code: Footer (After body) CONTAINS PHP CODE
include 'Templates/Footer.php';