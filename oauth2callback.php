<?php
require_once 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://yourdomain.com/oauth2callback.php');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);

// If we have a code back from the OAuth flow, we need to exchange that with the OAuth token.
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    // Save the token to the session for later use.
    $_SESSION['access_token'] = $token;
    header('Location: ' . filter_var('index.php', FILTER_SANITIZE_URL));
    exit;
}
    
// If there is an access token in the session, set it.
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    // If no token, generate an auth URL and redirect to it.
    $authUrl = $client->createAuthUrl();
    header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
    exit;
}

// Redirect back to your main script after authentication.
