<?php

/**
 * Actions: bulk-actions
 * handle bulk actions here
 * @var string $_POST['ajax_bulk'] - action name
 */

// Get the action anme
$ajax_bulk_action = isset($input->post->ajax_bulk_action) ? $this->sanitizer->text($input->post->ajax_bulk_action) : false;

// Set the ajax response
$response = [];

if ($ajax_bulk_action == "publish") {

  // Do something

} elseif ($ajax_bulk_action == "trash") {

  // Do something

} elseif ($ajax_bulk_action == "delete") {

  // Do something

}

if ($ajax_bulk_action) {
  header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}
