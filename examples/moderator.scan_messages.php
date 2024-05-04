<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkModerator;


// About
echo "==========================================================================
Scan some texts with ZPK moderation API
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);
$moderator = new zpkModerator($application);

$moderator->addText([
	'text'=>'I want to kill myself.',
	'source_id'=>'User-A',
	'message_id'=>'u.a.'.date('YmdHis')
]);

$moderator->addText([
	'text'=>'Good morning to everyone.',
	'source_id'=>'User-B',
	'message_id'=>'u.b.'.date('YmdHis')
]);

$moderator->addText([
	'text'=>"I'm ready to shoot everyone of that fucking bastards at my work.",
	'source_id'=>'User-C',
	'message_id'=>'u.c.'.date('YmdHis')
]);

echo json_encode($moderator->scan(),
 JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

echo "\nEND\n";


