<?php

require __DIR__.'/examples_autoloader.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkException;
use zpksystems\phpzpk\zpkModerator;

// Use invalid credentials
$app_id = 'invalid-app-id';
$api_key = 'invalid-key';

$app = new zpkApplication($app_id,$api_key);

$moderator = new zpkModerator($app);

$moderator->addText([
	'text'=>'Hello',
	'source_id'=>'User-A',
	'message_id'=>'u.a.'.date('YmdHis')
]);

try{
	echo json_encode($moderator->scan(),
	 JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
}catch (zpkException $e){

	if( $e->getCode() == EX_RESPONSE_ERRORS ){
		echo "Server response contains errors:\n";
		print_r( $e->getResponseErrors() );
	}else{
		echo "Technical problem, unnable to connect with API endpoint.\n";
		echo "Code: ".$e->getCode()."\n";
		echo "Message: ".$e->getMessage()."\n";
	}
}

echo "\nEND\n";


