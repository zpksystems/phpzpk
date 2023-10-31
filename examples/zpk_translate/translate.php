<?php

require __DIR__.'/../../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkTranslator;

// About
echo "==========================================================================

This example shows how to translate several sentences into multiple languages.

===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/../application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/../api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Sentences
$sentences = [
	"Una frase en español de un conocido poema: Nuestras vidas son los rios, que van a dar a la mar.",
	"Però ZPK també pot tradüir text correctament el idioma català.",
	"이 번역 API는 많은 언어를 지원합니다.",
];

$target_langs = ['en','it'];

$zTranslate = new zpkTranslator( $application );

foreach( $sentences as $sentence ){
	$zTranslate->addTranslation($sentence,$target_langs);
}

$data = $zTranslate->translate();

print_r($data);
