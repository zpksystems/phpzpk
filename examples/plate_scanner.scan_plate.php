<?php

require __DIR__.'/examples_autoloader.php';


use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkPlateScanner;

// About
echo "==========================================================================
This example sends all the images in /example_plates to the ZPK
PlateScanner API.

The detected license plate, the coordinates where it was found and the
region to which it belongs are displayed on the screen.
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);


// Foreach image in /test_images...
$image_files = glob(__DIR__.'/plate_scanner_test_images/*');
foreach( $image_files as $image_file ){

	// Initialize PlateScanner
	$plateScanner = new zpkPlateScanner( $application );

	// Set image
	$plateScanner->setImageFile($image_file);

	echo "============================================================\n";
	echo "Scan of: ".$image_file."\n";

	echo json_encode($plateScanner->scan(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

	echo "\n";

}


echo "END\n";

