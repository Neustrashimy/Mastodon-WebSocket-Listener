<?php
mb_internal_encoding("UTF-8");


// Load External Settings
$settings = json_decode(file_get_contents("access_token.json"), true);
if($settings === Null) { die("ERROR: Failed to parse access_token.json"); }


$url = $settings["url"]."/api/v1/streaming/user";

$headers = array(
	sprintf("User-Agent: %s", $settings["client_name"]),
	sprintf("Authorization: Bearer %s", $settings["access_token"]),
);



while(1) {
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url); 
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_BUFFERSIZE, (1*1024*1024));
	curl_setopt($curl, CURLOPT_WRITEFUNCTION, 'wrcallback');

	print "Connected, Waiting for stream...\n";
	$res = curl_exec($curl);
	if($res === FALSE) {
		print "ERROR: Loosing connection.";
		sleep(5);
	}


	curl_close($curl);
	print "Disconnected.\n";
}
// End of Main Routine



// Write Callback Function
function wrcallback($res, $response) {

	//global $settings;

	$raw = trim($response); // $responseは触らない！

	//print "Raw Data: $raw\n\n";

	// thump(keepalive?)はスルーする
	if($raw == ":thump" or substr_count($raw, "\n") != 1) { return strlen($response); }

	list($type, $body) = explode("\n", $raw, 2);

	$type = trim(mb_substr($type, mb_strpos($type, ":")+1)); // 最初の「:」までの文字を取る
	$body = trim(mb_substr($body, mb_strpos($body, ":")+1)); // 最初の「:」までの文字を取る

	//print "Type: $type\n";
	//print "Body: $body\n\n\n";
	
	switch($type) {
		case "follow":
		case "notification":
		case "update":
		case "delete":
		default:
	}


	return strlen($response); // 必ずresponseのlengthを返す
}


function toot($status, $visibility, $in_reply_to_id=Null) {

	global $settings, $headers;

	$url = $settings["url"] . "/api/v1/statuses";
	$posts = array(
		"status" => $status,
		"visibility" => $visibility,
		"in_reply_to_id" => $in_reply_to_id,
	);


	$_curl = curl_init();
	curl_setopt($_curl, CURLOPT_URL, $url); 
	curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($_curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($posts));

	$result = curl_exec($_curl);

	//print "curl error:" . curl_error($_curl) . "\n";

	curl_close($_curl);

	return json_decode($result, true);
}
