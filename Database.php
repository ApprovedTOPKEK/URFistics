<?php

$DBServer = 'localhost';
$DBUser   = 'root';
$DBPass   = '';
$DBName   = 'URFistics';

$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
if ($conn->connect_error) {
	trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}

function query($sql){
	global $conn;
	$rs = $conn->query($sql);
	//if($ret == true)
	if($rs === false)
		trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
	return $rs->fetch_all(MYSQLI_ASSOC);
}

?>