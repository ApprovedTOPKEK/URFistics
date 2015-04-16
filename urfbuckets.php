<?php

//SCRIPT USED TO GET THAT URF DATA... Unfortunately

//Include those important thigns again
include_once 'VarInclude.php';

//Fetch the setting we need
$st = $settings['urfbucketstime'];

//While we are below the last URF-Data, continue fetching
while($st < (1428926400)){

	//Loop through each region
	foreach($regions as $r){

		//Query the bucket for given time and region
		$sql = "https://" . $r['Region'] . ".api.pvp.net/api/lol/" . strtolower($r['Region']) . "/v4.1/game/ids?api_key=" . $apiKey . "&beginDate=".$st;
		$matchCurl = cURLRequest($sql);
		$matchBucket = json_decode($matchCurl);

		//For each ID in the bucket, save it to the queue
		foreach($matchBucket as $mid){
			$conn->query("REPLACE INTO `matchidqueue` (MatchId, Region) VALUES ('".$mid."', ".$r['ID'].");");
		}

		//Increment bucket
		$st += 300;
	}

	//After having looped through all regions, update the time we are at... Since freaking PHP has that freaking limited script runtime.
	$conn->query("UPDATE `Settings` SET `Value` = '" . $st . "' WHERE `Setting`='urfbucketstime';");
}
?>