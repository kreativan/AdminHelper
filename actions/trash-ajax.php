<?php

/** 
 * Actions: trash-ajax
 * @var $module
 */

namespace ProcessWire;

$response = [];

$id = $this->sanitizer->selectorValue($this->input->get->id);
$p = $this->pages->get("id=$id, include=all");

if ($p == '') {
  $response['status'] = 'error';
  $response['error'] = "Page not found";
}

$p->trash();

$message = "{$p->title} has been trashed";
$response['status'] = 'warning';
$response['message'] = $message;
$response['notification'] = $message;

header('Content-type: application/json');
echo json_encode($response);
exit();
