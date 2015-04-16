<?php

//"Private" data about the database.
$DBServer = 'localhost';
$DBUser   = 'root';
$DBPass   = '';
$DBName   = 'URFistics';

//Open connection to database, also check for errors. If error -> Website useless, so just exit.
$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
if ($conn->connect_error) {
	trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	echo "Error";
	exit("");
}

//Simple select query with simple error handling.
function query($sql){
	global $conn;
	$rs = $conn->query($sql);
	if(!$rs || $rs === false){
		echo 'Wrong SQL: ' . $sql . ' Error: ' . $conn->error;
		return;
	}
	return $rs->fetch_all(MYSQLI_ASSOC);
}

?>