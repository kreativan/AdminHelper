<?php

/** 
 * Actions: trash-ajax
 * @var object $module - module used to handle this action
 * @var string $action - action name
 * @var int $_GET['id'] - page id
 */

namespace ProcessWire;

$response = [];

$id = $this->input->get->id ? $this->sanitizer->selectorValue($this->input->get->id) : "";
$p = $id != "" ? $this->pages->get("id=$id, include=all") : "";

if ($p == '') {
  $response['status'] = 'error';
  $response['error'] = "Page not found";
  header('Content-type: application/json');
  echo json_encode($response);
  exit();
}

$p->trash();

$message = "{$p->title} has been trashed";
$response['status'] = 'warning';
$response['message'] = $message;
$response['notification'] = $message;

header('Content-type: application/json');
echo json_encode($response);
exit();
