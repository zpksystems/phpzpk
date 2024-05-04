<?php

require __DIR__.'/examples_autoloader.php';


use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkImageGenerator;


$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

$application = new zpkApplication($application_id,$api_key);
$generator = new zpkImageGenerator($application);

// Create 3 images
$generator->setPrompt("A futuristic cyberpunk world skyscraper with a neon logo with the letters \"ZPK\"");
$generator->generate(3);

// Wait for generation
echo "Waiting for generation...";
$generator->waitForGeneration();
echo "[DONE]".PHP_EOL;

// Show generated image
foreach( $generator->imagesList as $image ){
    if( $image->isGenerated() ){
        echo "Generated image, download url: ".$image->getDownloadUrl(). PHP_EOL;
    }else{
        echo "Image cannot be generated. Error: ".$image->getErrorCode().PHP_EOL;
    }
}

