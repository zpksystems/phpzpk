<?php

require __DIR__.'/examples_autoloader.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkHtmlToImageCapturer;

// About
echo "==========================================================================
	This example captures three elements from zpk website examples page.
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

$url = 'https://zpk.systems/html-to-image/test';

echo "Capturing elements on $url... \n";

// Initialize capturer
$capturer = new zpkHtmlToImageCapturer($application,$url);
$capturer->addElement('#example_1');

$ex_2 = $capturer->addElement('#example_2');
$ex_2->setOutputFormat('webp');
$ex_2->enableForceTransparency();

$ex_3 = $capturer->addElement('#example_3');

// Capture
$response = $capturer->capture();

print_r($response);

