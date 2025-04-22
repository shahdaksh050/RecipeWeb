<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('281943650357-3d6sjk0m9t0coq4t6njje40l14ttv2aa.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/recipe/auth/google-callback.php');
$client->addScope('email');
$client->addScope('profile');
?>
