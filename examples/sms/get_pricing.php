<?php

require __DIR__.'/../../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkSmsPriceRequest;


// About
echo "==========================================================================
This example fetches all sms prices.
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/../application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/../api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Price request
$pRequest = new zpkSmsPriceRequest( $application );

echo json_encode($pRequest->getPrices(),JSON_PRETTY_PRINT);
