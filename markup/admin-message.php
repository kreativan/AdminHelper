<?php

/** 
 * Markup: admin-message 
 * 
 */

namespace ProcessWire;

$style = !empty($style) ? $style : "message";

$text = !empty($text) ? $text : "";
$text = $input->get->text ? $input->get->text : $text;

$message = "";
$message .= $text ? "<span class='uk-text-small'>$text</span>" : "";

if (!empty($message)) {
  if ($style == "warning") {
    $page->warning($message, Notice::allowMarkup);
  } else if ($style == "error") {
    $page->error($message, Notice::allowMarkup);
  } else {
    $page->message($message, Notice::allowMarkup);
  }
}
