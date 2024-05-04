<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkPlateScanner;

// About
echo "==========================================================================
Get all regions used by PlateScanner API
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

$plateScanner = new zpkPlateScanner( $application );

echo json_encode($plateScanner->getAllRegions(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

echo "END\n";


