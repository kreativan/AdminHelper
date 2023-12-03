<?php

/** 
 * Classes: Emails 
 * 
 */

namespace AdminHelper;

use \ProcessWire\WireData;

class Emails extends WireData {

  /**
   * Send email
   * @param array $params
   * @param string $params['to'] - email address to send to (required)
   * @param string $params['from'] - email address to send from (required)
   * @param string $params['fromName'] - email name to send from (optional)
   * @param string $params['replyTo'] - email address to reply to (optional)
   * @param string $params['subject'] - email subject (required)
   * @param string $params['body'] - email body (required)
   * @param string $params['attachment'] - path to attachment file (optional)
   * @param array $params['attachments'] - array of paths to attachment files (optional)
   * @param string $params['email_template'] - path to email template file, will be used instead of body (optional)
   * @param string $params['email_template_page'] - page id to get email body from (optional)
   * @param array $params['data'] - data array to replace {text} in a provided string (optional)
   * @param int $params['related_page'] - page id to replace page fields (optional)
   * @return void
   */
  public function sendEmail($params = []) {

    $util = $this->AdminHelper->Utility();

    $to = $params['to'] ?? "";
    $from = $params['from'] ?? "";
    $fromName = $params['fromName'] ?? "";
    $replyTo = $params['replyTo'] ?? "";
    $subject = $params['subject'] ?? "";
    $body = $params['body'] ?? "";
    $attachment = $params['attachment'] ?? "";
    $attachments = $params['attachments'] ?? [];
    $email_template = $params['email_template'] ?? "";

    $email_template_page = $params['email_template_page'] ?? "";
    $email_template_page = $email_template_page != "" ? $this->pages->get($email_template_page) : "";

    $data_array = $params['data'] ?? [];
    $related_page = $params['related_page'] ?? "";
    $related_page = $related_page != "" ? $this->pages->get("id=$related_page") : "";

    if ($to == "") {
      $this->error("Email To is required");
      return false;
    }
    if ($from == "") {
      $this->error("Email From is required");
      return false;
    }
    if ($subject == "") {
      $this->error("Email Subject is required");
      return false;
    }

    /**
     * If email template is provided
     * get the contents and use it as email body
     */
    if (file_exists($email_template)) {
      $body = file_get_contents($email_template);
    }

    /**
     * If email template page id is provided
     * get the page body field and use it as email body
     */
    if ($email_template_page != "") {
      $body = $email_template_page->body;
    }

    /**
     * If related page is provided
     * format the body string and replace {page.field} with the actual value
     */
    if ($related_page != "") {
      $subject = $util->formatPageString($subject, $related_page);
      $body = $util->formatPageString($body, $related_page);
    }

    /**
     * If data array is provided
     * replace {text} in a provided string with the key from a $data array
     */
    if (count($data_array) > 0) {
      $subject = $util->strReplace($subject, $data_array);
      $body = $util->strReplace($body, $data_array);
    }

    /**
     * Send email
     */
    $mail = \ProcessWire\wireMail();
    $mail->to($to);
    $mail->from($from);
    if ($fromName != "") $mail->fromName($fromName);
    if ($replyTo != "") $mail->replyTo($replyTo);
    $mail->subject($subject);
    $mail->bodyHTML($body);

    // single attachment
    if ($attachment != "" && file_exists($attachment)) {
      $mail->attachment($attachment);
    }

    // multiple attachments
    if (count($attachments) > 0) {
      foreach ($attachments as $attachment) {
        if (file_exists($attachment)) $mail->attachment($attachment);
      }
    }

    $mail->send();
  }
}
