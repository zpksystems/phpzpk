<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkImageGenerator;
use zpksystems\phpzpk\zpkImageStatusRequest;

$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

$application = new zpkApplication($application_id,$api_key);

$generator = new zpkImageGenerator($application);
$generator->setPrompt("Ilustración realista de fantasía con un castillo de color purpura en el centro de un oceano de color verde con dragones dorados volando a su alrededor.");
$generator->generate(2);

$statusRequest = new zpkImageStatusRequest($application);

while(true){

	sleep(3);

	$images = $statusRequest->requestStatus( $generator->getUniqueRequestId() );

	foreach( $images as $key=>$image ){
		if( $image->isGenerated() ){
			echo "Image #$key | URL: ".$image->getDownloadUrl().PHP_EOL;
		}else{
			echo "Image #$key | status: ".$image->getStatus()." | ETA: ".$image->getETA().PHP_EOL;
		}
	}
}

