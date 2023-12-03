<?php

namespace ProcessWire;

$response = [];
$ApiEndPoint = $AdminHelper->ApiEndPoint();

$segment = str_replace(".php", '', $ApiEndPoint->api_file());


if (!empty($segment)) $segment .= "/";
$base = $config->paths->templates . "api/$segment";
$base_module = $AdminHelper->path() . "api/$segment";

// Get files
$routes = is_dir($base_module) ? scandir($base_module) : [];
$routes_tmpl = is_dir($base) ? scandir($base) : [];
$routes = array_merge($routes, $routes_tmpl);

if (empty($routes) || count($routes) < 1) {
  $response = [
    'status' => 'error',
    'code' => '404',
    'message' => 'Route not found',
  ];
  $AdminHelper->json_response($response);
} else {
  $routes = array_reverse($routes);
}

foreach ($routes as $item) {
  if ($item != "." && $item != ".." && $item != "readme.php" && $item != "README.md") {
    if (substr($item, -4) != ".php") {
      $folder = $item;
      $response["/$folder/"] = $api->httpUrl() . $folder . "/";
    } else {
      $file = str_replace(".php", "", $item);
      $response[$file] = $api->httpUrl() . $segment . $file . "/";
    }
  }
}

$AdminHelper->json_response($response);
