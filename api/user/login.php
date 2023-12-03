<?php

/**
 *  User Login
 *  @var string $_POST["user_name"]
 *  @var string $_POST["psw"]
 * 
 *  @author Ivan Milincic <kreativan.dev@gmail.com>
 *  @link http://www.kraetivan.dev
 */

namespace ProcessWire;

$errors = [];
$error_fields = [];

$response = [
  "status" => "pending",
  "reset_form" => false,
  "errors" => [],
  "error_fields" => [],
];

//  Required fields
// ===========================================================
if (!$input->post->user_name || !$input->post->psw) {

  if (!$input->post->user_name) {
    $errors[] = __("Please enter user name or email");
    $error_fields[] = "user_name";
  }
  if (!$input->post->psw) {
    $errors[] = __("Please enter your password");
    $error_fields[] = "psw";
  }

  $response["status"] = "error";
  $response["errors"] = $errors;
  $response["error_fields"] = $error_fields;
  $AdminHelper->jsonResponse($response);
}

//  Find User
// ===========================================================

$user_name = $sanitizer->text($input->post->user_name);
$psw = $sanitizer->text($input->post->psw);
$usr = $users->get("name|email=$user_name");

if ($usr == "") {

  $response["status"] = "error";
  $response["errors"] = [__("User not found")];
  $AdminHelper->jsonResponse($response);
} else {

  $u = null;

  try {
    $u = $session->login($usr->name, $psw);
    if ($u) {
      $response["redirect"] = $config->urls->admin;
    } else {
      $response["status"] = "error";
      $response["errors"] = [__("Password is incorrect")];
    }
  } catch (WireException $e) {
    $response["errors"][] = $e->getMessage();
    $AdminHelper->jsonResponse($response);
  }

  $AdminHelper->jsonResponse($response);
}
