<?php

/**
 * Actions: bulk-actions
 * handle bulk actions here
 * @var string $_POST['admin_action_bulk'] - action name publish|trash|delete
 */

// Get the action anme
$admin_action_bulk = isset($input->post->admin_action_bulk) ? $this->sanitizer->text($input->post->admin_action_bulk) : false;

// Set the ajax response
$response = [];

if ($admin_action_bulk == "publish") {

  // Do something

} elseif ($admin_action_bulk == "trash") {

  // Do something

} elseif ($admin_action_bulk == "delete") {

  // Do something

}

if ($admin_action_bulk) {
  header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}
