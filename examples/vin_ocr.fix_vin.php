<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkVinOcr;


// About
echo "==========================================================================
This example scans the image file '/vin_ocr_test_images/VSSZZZ5F1P6510989.jpg'
That image contains an incorrect VIN ending in '9B9', which will be
automatically corrected to '989'.

The VIN starts with 'VS5', which corresponds to a 'Renault' brand vehicle.
If we do not use filters, that Renault brand vehicle will be returned.

However, if we use a filter excluding 'Renault', the API will assume that
there is a read error and will return a VIN starting with 'VSS'
(the most optically similar to VS5), which corresponds to the
'Seat' brand.
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Initialize VinOcr
$vinOcr = new zpkVinOcr( $application );

// Scan with filter
echo "Running test with a filter excluding 'Renault' brand:\n";
$vinOcr->setImageFile(__DIR__.'/vin_ocr_test_images/VSSZZZ5F1P6510989.jpg');
$vinOcr->includeConsideredVins();
$vinOcr->excludeBrand("Renault");
$response = $vinOcr->scan();

echo json_encode([
	'returned_vin'=>$response['results'][0]['vin_number'],
],JSON_PRETTY_PRINT)."\n\n";


// Scan without filters
echo "Running test without filters:\n";

$vinOcr = new zpkVinOcr($application);
$vinOcr->setImageFile(__DIR__.'/vin_ocr_test_images/VSSZZZ5F1P6510989.jpg');
$vinOcr->includeConsideredVins();
$response = $vinOcr->scan();

echo json_encode([
	'returned_vin'=>$response['results'][0]['vin_number'],
],JSON_PRETTY_PRINT)."\n\n";


echo "END\n";
