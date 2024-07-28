<?php
session_start();

$authorize_url = 'https://dev-94271413.okta.com/oauth2/default/v1/authorize'; #change this and add your url token here
$client_id = ''; #Add your client ID here
$redirect_uri = 'http://localhost:80/callback.php';
$scope = 'openid profile email';
$state = bin2hex(random_bytes(5));
$_SESSION['state'] = $state;

$auth_url = $authorize_url . '?client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=' . urlencode($scope) . '&response_type=code&state=' . $state;

header('Location: ' . $auth_url);
exit;

