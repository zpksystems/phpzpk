<?php

require __DIR__.'/examples_autoloader.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkHtmlToImageCapturer;

// About
echo "==========================================================================
	Page screenshot after injecting javascript to alter CSS
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

// Inject JS
$capturer->injectJavascript('

	// random numbers on interval
	function getRandomRotation(min, max) {
		return Math.random() * (max - min) + min;
	}

	// select all div elements
	const divs = document.querySelectorAll("*");

	// foreach div element, alter rotation
	divs.forEach(div => {
		const rotation = getRandomRotation(-6, 6);
		div.style.transform = `rotate3d(0, 0, 1, ${rotation}deg)`;
	});

	// Show a message
    let floatingDiv = document.createElement("div");

    floatingDiv.textContent = "Elementos rotados con javascript";

    // Aplicar estilos al div
    floatingDiv.style.position = "fixed";
    floatingDiv.style.top = "10px";
    floatingDiv.style.left = "10px";
    floatingDiv.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
    floatingDiv.style.color = "white";
    floatingDiv.style.padding = "10px";
    floatingDiv.style.zIndex = "1000"; 
    floatingDiv.style.borderRadius = "5px";
    floatingDiv.style.fontFamily = "Arial, sans-serif";

    // add message to body
	document.body.appendChild(floatingDiv);
	
');

$capturer->setResolution(3280,1120);
$capturer->addElement('html');

// Capture
$response = $capturer->capture();

print_r($response);








