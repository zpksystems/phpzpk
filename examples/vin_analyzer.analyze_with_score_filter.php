<?php

require __DIR__.'/examples_autoloader.php';


use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkVinAnalyzer;

// About
echo "==========================================================================

This example will analyze the VIN VS5ZZZ5F1P6510989 belonging to a
Renault brand vehicle.

The API will return the correct brand: Renault.

In a second iteration, a filter is sent that penalizes 'Renault' brand
vehicles. The API will interpret that there is a reading error, and will
return a VIN that begins with 'VSS' because it is optically similar
to 'VS5', the API will return a Seat brand vehicle because we have
excluded its first option.

===========================================================================\n\n";


// Read application credentials
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Scan without filters
$vinAnalyzer = new zpkVinAnalyzer($application);
$vinAnalyzer->addVin('VS5ZZZ5F1P6510989');
$data = $vinAnalyzer->analyze();

echo "Analyze without score filters:\n";
echo "       Returned VIN: ".$data['results'][0]['vin']['vin_number']."\n";
echo "     Returned Brand: ".implode(',',$data['results'][0]['vin']['brands'])."\n";

echo "\n\n";

// Scan with filters
$vinAnalyzer = new zpkVinAnalyzer($application);
$vinAnalyzer->addVin('VS5ZZZ5F1P6510989');
$vinAnalyzer->excludeBrand('renault');
$data = $vinAnalyzer->analyze();

echo "Analyze with a score filter to exclude 'renault' brand:\n";
echo "       Returned VIN: ".$data['results'][0]['vin']['vin_number']."\n";
echo "     Returned Brand: ".implode(',',$data['results'][0]['vin']['brands'])."\n";

echo "\nEND\n";


