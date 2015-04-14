<?php

function cURLRequest($link){
	$ch = curl_init();
	//$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $link);
	//curl_setopt ($ch, CURLOPT_PORT , 8089);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);
	if($info['http_code'] != 200){
		sleep(0.5);
		return cURLRequest($link);
	}
	return $data;
}

for($i = 0; $i < 20; $i++){
	echo "--<br />";
	echo cURLRequest("https://euw.api.pvp.net/api/lol/euw/v1.4/summoner/48828082/name?api_key=b7ca061e-63e0-49cb-bac1-a4886089badf");
	echo "<br />";
}

?>