<?php

require __DIR__.'/../../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkSMS;
use zpksystems\phpzpk\zpkSmsSendRequest;

// About
echo "
==========================================================================
This example sends three SMS text mesages to a phone numbers
===========================================================================
\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/../application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/../api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Prepare request
$r = new zpkSmsSendRequest($application);

// Add first SMS
$sms = new zpkSMS("+34634568046");
$sms->setFrom("First");
$sms->setMessage("SMS test message, the first one :)");
$r->add($sms);

// Add another SMS, with reference
$sms = new zpkSMS("+34634568046");
$sms->setFrom("Another msg");
$sms->setMessage("Text on the seccond message :)");
$sms->setReference('myref');
$r->add($sms);

// trigger an error
// Add third SMS, with an invalid from
$sms = new zpkSMS("+34634568046");
$sms->setFrom("This from message is too long");
$sms->setMessage("This SMS is not valid :(");
$r->add($sms);

// SEND
$response = $r->send();

echo "RESPONSE FROM SERVER:\n";
print_r( $response );

echo "END OF EXAMPLE\n";

