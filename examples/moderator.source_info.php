<?php

require __DIR__.'/../vendor/autoload.php';

use zpksystems\phpzpk\zpkApplication;
use zpksystems\phpzpk\zpkModerator;

// About
echo "==========================================================================
Get stats from specific source,
Run 'moderator.scan_messages.php' first to fill source 'User-A' with
some statistics.
===========================================================================\n\n";

// Read application id and api key
$application_id = trim(file_get_contents(__DIR__.'/application_id.txt'));
$api_key = trim(file_get_contents(__DIR__.'/api_key.txt'));

// Initialize zpk application
$application = new zpkApplication($application_id,$api_key);

// Initialize a moderator
$moderator = new zpkModerator($application);

// Get stats
$stats = $moderator->getSourceStats('User-A');

echo json_encode($stats,
 JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);

echo "END\n";

