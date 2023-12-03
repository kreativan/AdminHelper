<?php

namespace ProcessWire;

$response = [
  "status" => "error", // used also for notification color
  "reset_form" => false, // clear-reset form input values
  "message" => "No form submission!", // if no modal, 
  "REQUEST" => $_REQUEST,
];

$validation = $AdminHelper->Validation();

//-------------------------------------------------------- 
//  Validation
//-------------------------------------------------------- 

$errors = $validation->get_req_errors([
  "required" => ["email", "your_name", "message"],
  "email" => ["email"],
  "labels" => [
    "email" => "Email Address",
    "your_name" => "Name",
    "message" => "Form Message",
  ],
]);

if ($errors) {

  $response['status'] = 'error';
  $response['notification'] = 'Form is invalid!';
  $response['valitron'] = $errors;

  header('Content-type: application/json');
  echo json_encode($response);
  exit();
}

//-------------------------------------------------------- 
//  Continue
//-------------------------------------------------------- 

if ($input->post->something) {

  // do your logic here

}

//-------------------------------------------------------- 
//  Reponse
//-------------------------------------------------------- 

header('Content-type: application/json');
echo json_encode($response);
exit();
