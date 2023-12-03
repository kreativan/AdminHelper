<?php

/** 
 * Actions: publish-ajax
 * @var object $module - module used to handle this action
 * @var string $action - action name
 * @var int $_GET['id'] - page id
 */

namespace ProcessWire;

$response = [];

$id = $this->sanitizer->selectorValue($this->input->get->id);
$p = $this->pages->get("id=$id, include=all");

if ($p == '') {
  $response['status'] = 'error';
  $response['error'] = "Page not found";
}

if ($p->isUnpublished()) {

  $p->of(false);
  $p->removeStatus('unpublished');
  $p->save();

  $message = "{$p->title} has been published";
  $response['status'] = 'success';
  $response['message'] = $message;
  $response['notification'] = $message;
} else {

  $p->of(false);
  $p->status('unpublished');
  $p->save();

  $message = "{$p->title} has been unpublished";
  $response['status'] = 'warning';
  $response['message'] = $message;
  $response['notification'] = $message;
}

header('Content-type: application/json');
echo json_encode($response);
exit();
