<?php

require __DIR__.'/examples_autoloader.php';


use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkTranslator;

// About
echo "==========================================================================
Retrive all languages supported by ZPK Translate
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);
$zTranslate = new zpkTranslator($application);
$response = $zTranslate->getLanguages();

foreach( $response['languages'] as $language ){
	echo "===========================================================================================\n";
	echo "Language: ".$language['name'].' ('.$language['code'].").\n\n";
	echo "Name in multiple languages:\n".json_encode($language['localized'],JSON_UNESCAPED_UNICODE)."\n\n";
	echo "Accepts translations from:\n".json_encode($language['from'],JSON_UNESCAPED_UNICODE)."\n\n";
	echo "Accepts translations to:\n".json_encode($language['to'],JSON_UNESCAPED_UNICODE)."\n\n";
}

echo "ZPK Translate languages: ".count($response['languages']).PHP_EOL;

echo "\nEND\n";

