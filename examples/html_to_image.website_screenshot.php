<?php

require __DIR__.'/examples_autoloader.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkHtmlToImageCapturer;

// About
echo "==========================================================================
	Page screenshot example
===========================================================================\n\n";


// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

$url = 'https://zpk.systems/en/docs/api/all-apis';

echo "Capturing screenshot for $url... \n";

// Initialize capturer
$capturer = new zpkHtmlToImageCapturer($application,$url);
$capturer->setResolution(3280,1120);
$capturer->addElement('html');

// Capture
$response = $capturer->capture();

print_r($response);







