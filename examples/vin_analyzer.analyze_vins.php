<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkVinAnalyzer;

// About
echo "==========================================================================
This example shows how to send multiple VIN codes to the VinAnalyzer API
to display data for different vehicles.

Some incorrect VIN numbers are sent, to be autocorrected by the API.
===========================================================================\n\n";


$vins = [];

$vins[] = [
	'number'=>'L6TCX2E70ME005154',
	'description'=>'Correct VIN for a Geely car manufactured on China'
];

$vins[] = [
	'number'=>'6TCX2E70ME005154',
	'description'=>'Previous Geely vehicle with missing first digit.'
];

$vins[] = [
	'number'=>'1T9CX2E70ME005154',
	'description'=>'An incorrect VIN number for a Tomcar car, invalid check digit.'
];

$vins[] = [
	'number'=>'VWZZZ6RZAY133568',
	'description'=>'Germany manufactured wolkswagen with missing first digit.'
];

$vins[] = [
	'number'=>'NOVALIDVIN',
	'description'=>'Just an invalid VIN'
];


// Read application credentials
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);


// Iterate on all VIN numbers
foreach( $vins as $vin ){
	echo "==============================================================================\n";
	echo "         Sending: ".$vin['number']."\n";
	echo "     Description: ".$vin['description']."\n\n";

	$vinAnalyzer = new zpkVinAnalyzer($application);
	$vinAnalyzer->addVin($vin['number']);
	$data = $vinAnalyzer->analyze();

	if( isset($data['results']) && count($data['results'])>0 ){

		echo "       Found VIN: ".$data['results'][0]['vin']['vin_number']."\n";
		echo "   Valid Quality: ".$data['results'][0]['vin']['quality']."\n";

		if( $data['results'][0]['vin']['fixed'] ){
			echo " (VIN has been corrected)\n";
		}else{
			echo "\n";
		}

		echo "    Manufacturer:".$data['results'][0]['vin']['manufacturer_name']."\n";
		echo "          Brands:".implode(',',$data['results'][0]['vin']['brands'])."\n";
		echo "         Country:".$data['results'][0]['vin']['country_name']."\n";

		if( isset($data['results'][0]['vin']['invalid_reasons']) && count($data['results'][0]['vin']['invalid_reasons'])>0 ){
			echo " Invalid Reasons:".json_encode($data['results'][0]['vin']['invalid_reasons'])."\n";
		}
	
	}else{
		echo "     *No vins found*\n"; 
	}

	//print_r($data);
	
	echo "\n\n\n";
}
