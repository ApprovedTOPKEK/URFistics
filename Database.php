<?php

$DBServer = 'localhost';
$DBUser   = 'root';
$DBPass   = '';
$DBName   = 'URFistics';

$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
if ($conn->connect_error) {
	trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
	echo "Error";
}

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