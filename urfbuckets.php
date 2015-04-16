<?php
include_once 'VarInclude.php';
$st = '1427915100';
$b = array();

foreach ($regions as $r) {
set_time_limit(600);
while($st < (1428926400-30*3000)){
	$sql = "https://" . $r['Region'] . ".api.pvp.net/api/lol/" . strtolower($r['Region']) . "/v4.1/game/ids?api_key=" . $apiKey . "&beginDate=".$st;
	$matchCurl = cURLRequest($sql);
	$matchBucket = json_decode($matchCurl);
	foreach($matchBucket as $mid){
		array_push($b, $mid);
	}
	echo $matchCurl;
	$st += 300;
}
}
echo "<br /><br /><br /><br /><br />..........................................<br />";
print_r($b);
?>