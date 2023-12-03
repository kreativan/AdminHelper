<?php

/** 
 * Action: send-email-ajax
 * @var object $module - module used to handle this action
 * @var string $action - action name
 * 
 */

namespace ProcessWire;

$response = [];

// Get validation library
$valitron = $AdminHelper->Valitron();

// Validate POST data
$errors = $valitron->get_req_errors([
  'labels' => ['to' => 'Email To', 'from' => 'Email From', 'subject' => 'Subject'],
  'required' => ['to', 'from', 'subject'],
  'email' => ['to', 'from'],
], 'en');

// If POST is not valid
if ($errors) {
  $this->AdminHelper->json_response([
    'valitron' => $errors,
  ]);
}

// continue ... 

$to = $sanitizer->email($input->post->to);
$from = $sanitizer->email($input->post->from);
$fromName = $sanitizer->text($input->post->fromName);
$replyTo = $sanitizer->email($input->post->replyTo);
$subject = $sanitizer->text($input->post->subject);
$body = $sanitizer->purify($input->post->body);
$attachment = $sanitizer->text($input->post->attachment);
$attachments = $input->post->attachments ?? [];
$email_template = $sanitizer->textarea($input->post->email_template);
$email_template_page = $sanitizer->int($input->post->email_template_page);
$related_page = $sanitizer->int($input->post->page_ref);
$data = [];

$params = [
  'to' => $to, // email to
  'from' => $from, // email from
  'fromName' => $fromName, // email from name
  'replyTo' => $replyTo, // email reply to
  'subject' => $subject, // email subject
  'body' => $body, // email body
  'attachment' => $attachment, // attachment file path
  'attachments' => $attachments, // array of attachment file paths
  'email_template' => $email_template, // path to email template file, will be used instead of body (optional)
  'email_template_page' => $email_template_page, // page id to get email body from (optional)
  'related_page' => $related_page, // page id to replace page fields {field_name} (optional)
  'data' => $data, // data array to replace {text} in a provided string (optional)
];

try {
  // Send email
  $AdminHelper->send_email($params);
  // Set response
  $message = "Email sent to <b>{$params['to']}</b>";
  $response = [
    'status' => 'success',
    'message' => $message,
    'notification' => $message,
    'reset_form' => true,
    'close_modal_id' => 'htmx-modal',
    'REQ_DATA' => $params,
    'body' => $body,
  ];
} catch (Exception $e) {
  $response = [
    'status' => 'error',
    'message' => $e->getMessage(),
    'notification' => $e->getMessage(),
    'REQ_DATA' => $params,
  ];
}

$this->AdminHelper->json_response($response);
