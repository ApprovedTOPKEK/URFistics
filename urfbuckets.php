<?php
include_once 'VarInclude.php';
$st = $settings['urfbucketstime'];
$b = array();

while($st < (1428926400-30*3000)){
	foreach($regions as $r){
		$sql = "https://" . $r['Region'] . ".api.pvp.net/api/lol/" . strtolower($r['Region']) . "/v4.1/game/ids?api_key=" . $apiKey . "&beginDate=".$st;
		$matchCurl = cURLRequest($sql);
		$matchBucket = json_decode($matchCurl);

		foreach($matchBucket as $mid){
			$conn->query("REPLACE INTO `matchidqueue` (MatchId, Region) VALUES ('".$mid."', ".$r['ID'].");");
		}
		$st += 300;
	}
	$conn->query("UPDATE `Settings` SET `Value` = '" . $st . "' WHERE `Setting`='urfbucketstime';");
}
?>