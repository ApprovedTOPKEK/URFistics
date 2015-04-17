<?php

//Include necessary scripts
include_once "VarInclude.php";

//Include utility method to build query. In another PHP File for clarity (MANAPROBLEMS INCOMING) n stuff (outdated comment)
include_once 'StatQuery.php';

//Selected gamemode
$ps_gameMode = isset($_GET['gamemode']) ? $_GET['gamemode'] : $settings['defaultGamemode'];

$query = "
SELECT Top1, Top2, Top3, Top4, Top5, Top6, Top7, Top8, Top9, Top10 FROM
(
(SELECT Top1 FROM
	(

	) AS tab1
	GROUP BY Top1
	HAVING Top1>0
	ORDER BY COUNT(Top1) DESC
) AS tab2
LEFT JOIN



) AS tab99;


";