<?php
session_start();
require 'vendor/autoload.php';

$client_id = '0oail44lnqcBE9uMB5d7';
$client_secret = '2Yr3A7FeadkfZbwjT1cEGvBLzqcpAhE88fNb6CI3F23hrtuvDNLEa0xxhR1p_D-p';
$redirect_uri = 'http://34.136.141.61:80/callback.php';
$okta_domain = 'https://dev-94271413.okta.com';

if (!isset($_GET['code'])) {
    exit('Authorization code not received');
}

$token_url = $okta_domain . '/oauth2/default/v1/token';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'authorization_code',
    'code' => $_GET['code'],
    'redirect_uri' => $redirect_uri,
    'client_id' => $client_id,
    'client_secret' => $client_secret
]));

$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response, true);

if (isset($token['access_token'])) {
    $_SESSION['user'] = $token;
    header('Location: index.php');
    exit();
} else {
    exit('Error fetching access token');
}
?>
