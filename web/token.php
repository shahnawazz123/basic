<?php
require(__DIR__ . '/../vendor/autoload.php');


use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VideoGrant;

// An identifier for your app - can be anything you'd like
$appName = 'TwilioVideoDemo';

// choose a random username for the connecting user
$twilioAccountSid = "AC9e4942d195c3a3a90a3ee809db045772";
$twilioApiKey = "SKb20b4cf1ee0135a3a71afd94e00e66a9";
$twilioApiSecret = "nNDafbCSUovDqncWDIwuoxKOUcPbePfx";

$identity = $_GET['identity'];
$roomName = $_GET['room_name'];
// Create access token, which we will serialize and send to the client
$token = new AccessToken(
    $twilioAccountSid,
    $twilioApiKey,
    $twilioApiSecret,
    3600, 
    $identity
);

// Grant access to Video
$grant = new VideoGrant();
$grant->setRoom($roomName);
//$grant->setConfigurationProfileSid($TWILIO_CONFIGURATION_SID);
$token->addGrant($grant);

// return serialized token and the user's randomly generated ID
echo  $token->toJWT();

