<?php

/**
 * Api: json/months
 */

$json = file_get_contents($AdminHelper->path() . "lib/json/months.json");

// json header
header('Content-Type: application/json');
echo $json;
