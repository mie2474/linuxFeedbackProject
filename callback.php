<?php
session_start();
if (!isset($_GET['state']) || ($_GET['state'] !== $_SESSION['state'])) {
    die('State mismatch or missing');
}

$code = $_GET['code'];
$token_url = 'https://dev-94271413.okta.com/oauth2/default/v1/token'; #change this and add your url token here
$client_id = ''; #Add client ID here
$client_secret = ''; #Add client secret here
$redirect_uri = 'http://localhost:80/callback.php';

$post_data = [
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirect_uri,
    'client_id' => $client_id,
    'client_secret' => $client_secret
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $token_url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded'
]);

$response = curl_exec($curl);
curl_close($curl);

$token_response = json_decode($response, true);

if (isset($token_response['access_token'])) {
    $_SESSION['access_token'] = $token_response['access_token'];
    header('Location: index.html');
    exit;
} else {
    echo 'Error retrieving access token';
}

