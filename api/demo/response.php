<?php

/**
 * Ajax Response for AdminHelper.js
 * @author Ivan Milincic <hello@kreativan.dev>
 * @link http://www.kraetivan.dev
 */

$response = [

  /**
   * Used also for notification color
   * string: success, warning, danger, error
   */
  "status" => "pending",

  /**
   * Clear-reset form input values
   */
  "reset_form" => false,

  /**
   * Response message
   */
  "message" => "Some response message",

  /**
   * Notification
   * Will trigger uikit notification
   */
  "notification" => "Notification: Ajax form submit was ok!",

  /**
   * Will trigger uikit modal on response,
   * has priority over the notification
   */
  "modal" => "<h3>Title</h3><p>text</p>",

  /**
   * Same as 'modal'...
   * Will trigger alert response,
   */
  "alert" => "<h3>Title</h3> p>text</p>",

  /**
   * Will trigger dialog on response
   * It is a good way to display iframe content in a modal
   */
  "dialog" => "<iframe src=''></iframe>",

  /**
   * Set modal dialog width in px
   */
  "modal_width" => "1200px",

  /**
   * Modal css ID that to remove on response (without #)
   * this is usually htmx-modal, as its mainly used to remove htmx triggered modals
   */
  "close_modal_id" => "htmx-modal",

  /**
   * Redirect after response.
   * If used with modal, will redirect after modal confirm...
   */
  "redirect" => "/",

  /**
   * Open new browser tab after response
   * @param string url
   */
  "open_new_tab" => "example.com",

  /**
   * Array of errors (strings), will trigger notification for each
   * @example ['error one', 'email two']
   */
  "errors" => [],

  /**
   * Array of invalid form field names. Will add .error class
   * @example ['name', 'email']
   */
  "error_fields" => [],

  /**
   * Valitron
   * Pass the valitron errors directly to the response
   */
  "valitron" => $valitron->errors(),

  /**
   * Run htmx sync on response
   * Sync-update all DOM elements with the data-htmx attribute
   */
  "htmxSync" => 1,

  /**
   * Will trigger htmx request on response
   */
  "htmx" => [
    "type" => "GET",
    "url" => "/", // or file
    "target" => "#target-element",
    "swap" => "innerHTML",
    "indicator" => "#htmx-indicator",
    "push_url" => "./",
  ],

  /**
   * Update DOM element
   * @param string $selector - css selector of the target element
   * @param string $html - innerHTML to replace the target element with
   */
  "update_DOM" => [
    "selector" => ".cart",
    "html" => "1",
  ],
];

header('Content-type: application/json');
echo json_encode($response);
exit();
