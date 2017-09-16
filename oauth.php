<?php

// *** Edit those settings
$url = "https://your.mastodon.tld";
$client_name = "your_bot_name";
$scopes = "read write";
$username = "your_mastodon_user_mail@domain.tld";
$password = "your_mastodon_password";
// *** End of settings



$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_URL, $url."/api/v1/apps");

// client id & client secret
$params = array(
	"client_name"	=> $client_name,
	"redirect_uris"	=> "urn:ietf:wg:oauth:2.0:oob",
	"scopes" => $scopes,
);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

$clients = json_decode(curl_exec($curl), true);

if($clients === null) { die("ERROR: Failed to get client ids\n"); }
//var_dump($clients);

// access token
curl_setopt($curl, CURLOPT_URL, $url."/oauth/token");
$params = array(
	"client_id"	=> $clients["client_id"],
	"client_secret"	=> $clients["client_secret"],
	"grant_type"	=> "password",
	"username"	=> $username,
	"password"	=> $password,
	"scope"		=> $scopes,
);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

$tokens = json_decode(curl_exec($curl), true);

if($tokens === null) { die("ERROR: Failed to get access tokens\n"); }
//var_dump($tokens);

curl_close($curl);

$output = array(
	"client_name" => $client_name,
	"url" => $url,
	"client_id" => $clients["client_id"],
	"client_secret" => $clients["client_secret"],
	"access_token" => $tokens["access_token"],
	"token_type" => $tokens["token_type"],
	"scope" => $tokens["scope"],
	"created_at" => $tokens["created_at"],
);

file_put_contents("access_token.json", json_encode($output));



