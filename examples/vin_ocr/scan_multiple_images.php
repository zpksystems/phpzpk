<?php

require __DIR__.'/../../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkVinOcr;

// About
echo "==========================================================================

This example scans all images in '/test_images/test_multiple_vin/'
using ZPK's VinOcr API.

Shows for each image all the VINs found or an alert if the image
does not contain any VIN.

===========================================================================\n\n";


// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/../application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/../api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Initialize VinOcr
$vinOcr = new zpkVinOcr( $application );
$file_list = glob(__DIR__.'/test_images/test_multiple/vin_*.jpeg');

// Scan all example images
foreach( $file_list as $example_file ){

	echo "Scan of file: ".basename($example_file)."\n";

	$vinOcr->setImageFile($example_file);
	$response = $vinOcr->scan();

	// Show found vins
	foreach( $response['results'] as $result ){
		echo "  - Found VIN ".$result['vin_number']."\n";
	}

	// Show warning if no vins
	if( count($response['results']) == 0 ){
		echo "  - No VINS found\n";
	}

	echo "\n";

}

