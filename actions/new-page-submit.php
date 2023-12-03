<?php

/**
 * Actions: new-page-ajax
 * @var object $module - module used to handle this action
 * @var string $action - action name
 * @var int $_POST['parent_id'] - parent page id
 * @var int $_POST['template_id'] - template id
 */

namespace ProcessWire;

$response = [];
$errors = [];

$parent_id = $this->sanitizer->selectorValue($this->input->post->parent_id);
$template_id = $this->sanitizer->selectorValue($this->input->post->template_id);
$title = $this->sanitizer->text($this->input->post->title);

if ($parent_id == '') {
  $errors[] = "Parent ID not found";
}

if ($template_id == '') {
  $errors[] = "Template ID not found";
}

if ($title == '') {
  $errors[] = "Title not found";
}

if (count($errors) > 0) {
  $response['status'] = 'error';
  $response['errors'] = $errors;
  header('Content-type: application/json');
  echo json_encode($response);
  exit();
}

// Continue 
// ========================================================= 

$p = new Page();
$p->template = $template_id;
$p->parent = $parent_id;
$p->title = $title;
$p->save();

$response = [
  'status' => 'success',
  'message' => "{$p->title} has been created",
  'htmxSync' => 1,
  "close_modal_id" => "htmx-modal",
];

header('Content-type: application/json');
echo json_encode($response);
exit();
