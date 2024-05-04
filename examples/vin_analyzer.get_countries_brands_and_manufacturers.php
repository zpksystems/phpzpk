<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkVinAnalyzer;

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
$vinAnalyzer = new zpkVinAnalyzer($application);

// Make 3 requests
$data = [
	'manufacturers'=>$vinAnalyzer->getManufacturers(),
	'countries'=>$vinAnalyzer->getCountries(),
	'brands'=>$vinAnalyzer->getBrands()
];

echo json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
 
