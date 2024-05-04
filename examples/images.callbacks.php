<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkImageGenerator;

$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

$application = new zpkApplication($application_id,$api_key);

$generator = new zpkImageGenerator($application);
$generator->setPrompt("Un bosque de fantasia con super-ordanadores y servidores en un claro del bosque.");

$code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 32);
$callback_url = 'https://callbacktest.zpk.systems/callback/send/'.$code;
$callback_viewer = 'https://callbacktest.zpk.systems/callback/view/'.$code;

echo "Sending image generation request, check results at: ".$callback_viewer;
$generator->setCallbackURL($callback_url);
$generator->generate(1);

