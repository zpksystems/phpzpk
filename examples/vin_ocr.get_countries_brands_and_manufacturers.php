<?php

require __DIR__.'/examples_autoloader.php';


use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkVinOcr;

// About
echo "==========================================================================
This example asks the API to return all the manufacturers, countries, and
vehicle brands that the VINS API handles.
===========================================================================\n\n";

// Read application credentials
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpkApplicationa and vinAnalyzer
$application = new zpkApplication($application_id,$api_key);
$ocr = new zpkVinOcr($application);

// Make 3 requests
$data = [
	'manufacturers'=>$ocr->getManufacturers(),
	'countries'=>$ocr->getCountries(),
	'brands'=>$ocr->getBrands()
];

echo json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
 
