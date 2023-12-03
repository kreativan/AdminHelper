<?php

/**
 * Api: json/countries
 */

$json = file_get_contents($AdminHelper->path() . "lib/json/countries.json");

// json header
header('Content-Type: application/json');
echo $json;
