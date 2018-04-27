<?php
// Update the path below to your autoload.php,
// see https://getcomposer.org/doc/01-basic-usage.md
require_once "./vendor/autoload.php";
require_once "./MyRequestClass.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use Twilio\Rest\Client;

// Your Account Sid and Auth Token from twilio.com/console
$sid    = getenv('ACCOUNT_SID');
$token  = getenv('AUTH_TOKEN');
$proxy  = getenv('PROXY');

$httpClient = new MyRequestClass($proxy);
$twilio = new Client($sid, $token, null, null, $httpClient);

$message = $twilio->messages
                  ->create("+15558675310",
                           array(
                               'body' => "Hey there!",
                               'from' => "+15017122661"
                           )
                  );

print("Message SID: {$message->sid}");
