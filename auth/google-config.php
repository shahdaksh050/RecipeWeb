<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('');
$client->setClientSecret('');
$client->setRedirectUri('http://localhost/recipe/auth/google-callback.php');
$client->addScope('email');
$client->addScope('profile');
?>
