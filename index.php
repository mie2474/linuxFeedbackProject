<?php
session_start();
require 'vendor/autoload.php';

$client_id = '0oail44lnqcBE9uMB5d7';
$redirect_uri = 'http://34.136.141.61:80/callback.php';
$okta_domain = 'https://dev-94271413.okta.com';

if (!isset($_SESSION['user'])) {
    $auth_url = $okta_domain . '/oauth2/default/v1/authorize?' . http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => 'openid profile email',
        'state' => bin2hex(random_bytes(5))
    ]);
    header('Location: ' . $auth_url);
    exit();
}

// Serve the index.html content after authentication
readfile('index.html');
?>

