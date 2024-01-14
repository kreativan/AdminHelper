<?php

/** 
 * Admin: Email modal 
 * 
 */

namespace ProcessWire;

$form_action = $input->get->form_action ? $input->get->form_action : "./?admin_action=send-email-ajax";
$form_ajax = $input->get->form_ajax && $input->get->form_ajax == "false"  ? false : true;

/**
 * Basic Email Data
 * @var string $to
 * @var string $from
 * @var string $fromName
 * @var string $replyTo
 * @var string $subject
 */
$to = $input->get->to ? $input->get->to : "";
$from = $input->get->from ? $input->get->from : "";
$fromName = $input->get->fromName ? $input->get->fromName : "";
$replyTo = $input->get->replyTo ? $input->get->replyTo : "";
$subject = $input->get->subject ? $input->get->subject : "";

/**
 * From Options
 * @var array $from_options
 * Use it instead of passing "from", "fromName" and "replyTo"
 * @example ["email_1" => "From name 1", "email_2" => "From name 2"]
 */
$from_options = $input->get->from_options ? $input->get->from_options : "";
if ($from_options != "") {
  $from = json_decode($from_options, true);
  $fromName = array_values($from)[0];
  $replyTo = array_keys($from)[0];
}

/**
 * Page Reference
 * Reference page to be used when applying string replacements
 * @var int $page_ref
 */
$page_ref = $input->get->page_ref ? $sanitizer->int($input->get->page_ref) : ''; // page id

/**
 * Attachments
 * @var string $attachment one file path
 * @var array $attachments array of file paths
 */
$attachment = $input->get->attachment ? $input->get->attachment : ""; // file path
$attachments = $input->get->attachments ? $input->get->attachments : []; // array of file paths

/**
 * Email Template
 * Path to the email template file to be used instead of body, this can be html or php file,
 * as system is using file_get_contents() to get the file content
 * @var string $email_template file path
 */
$email_template = $input->get->email_template ? $input->get->email_template : ""; // file path


/**
 * Email Templates Option
 * selector used to find email templates, to use them as a dropdown option
 * @var string $email_templates_selector
 */
$email_templates_selector = $input->get->email_templates_selector ? "{$input->get->email_templates_selector}, include=all, status!=trash" : "";
$email_templates = ($email_templates_selector != "") ? $pages->find($email_templates_selector) : "";
?>

<div id="htmx-modal" class="page-edit-modal uk-flex-top" uk-modal="bg-close: false">
  <div class="uk-modal-dialog uk-overflow-hidden uk-margin-auto-vertical uk-width-2xlarge">

    <div class="uk-modal-header uk-position-relative uk-visible@l uk-light">
      <h3 class="uk-margin-remove"><?= __('Send Email') ?></h3>
      <button class="uk-modal-close uk-position-center-right uk-margin-right" uk-close></button>
    </div>

    <div class="uk-modal-body Inputfields">

      <?php if (!is_array($from)) : ?>
        <div class="uk-margin">
          <?php if ($fromName != "") : ?>
            <label class="uk-label tm-bg-white uk-text-emphasis tm-border" uk-tooltip title="fromName">
              <i class="fa fa-user"></i>
              <?= $fromName; ?>
            </label>
          <?php endif; ?>

          <label class="uk-label <?= !empty($from) ? "tm-bg-white uk-text-emphasis tm-border" : 'uk-label-danger' ?>" uk-tooltip title="from">
            <i class="fa fa-envelope-o"></i>
            <?= !empty($from) ? $from : "email from is missing!" ?>
          </label>
        </div>
      <?php endif; ?>

      <form id="email-form" action="<?= $form_action ?>" method="POST" class="uk-form-stacked">

        <?php if (!is_array($from)) : ?>
          <input type="hidden" name="from" value="<?= $from ?>" />
        <?php endif; ?>
        <input type="hidden" name="fromName" value="<?= $fromName ?>" />
        <input type="hidden" name="replyTo" value="<?= $replyTo ?>" />
        <input type="hidden" name="attachment" value="<?= $attachment ?>" />
        <input type="hidden" name="page_ref" value="<?= $page_ref ?>" />

        <?php if (count($attachments) > 0) : ?>
          <?php foreach ($attachments as $attachment) : ?>
            <input type="hidden" name="attachments[]" value="<?= $attachment ?>" />
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($email_template) && $email_templates == "") : ?>
          <input type="hidden" name="email_template" value="<?= $email_template ?>" />
        <?php endif; ?>

        <?php if ($email_templates != "" && $email_templates->count) : ?>
          <div class="uk-margin-bottom">
            <label class="uk-form-label uk-text-bold uk-text-uppercase">
              <i class="fa fa-html5"></i>
              Email Template:
            </label>
            <select class="uk-select" name="email_template_page" onchange="inputToggle()">
              <option value="">- None -</option>
              <?php foreach ($email_templates as $item) : ?>
                <option value="<?= $item->id ?>" data-subject="<?= !empty($item->subject) ? $item->subject : $subject ?>">
                  <?= $item->title ?>
                </option>
              <?php endforeach; ?>
            </select>
            <script>
              function inputToggle() {
                let target = event.target;
                let index = target.selectedIndex;
                let subject_value = target.options[index].dataset.subject;
                let val = target.value;
                let items = document.querySelectorAll('.input-toggle');
                let subject = document.querySelector('input[name="subject"]');
                if (val != '') {
                  items.forEach(target => {
                    target.classList.add('uk-hidden');
                    subject.value = subject_value;
                  });
                } else {
                  items.forEach(target => {
                    target.classList.remove('uk-hidden');
                    subject.value = '';
                  });
                }
              }
            </script>
          </div>
        <?php endif; ?>

        <?php if (is_array($from)) : ?>
          <div class="uk-margin">
            <label class="uk-form-label uk-text-bold uk-text-uppercase">
              <i class="fa fa-at"></i>
              <?= __('Mail From:') ?>
            </label>
            <select class="uk-select" name="from" onchange="fromNameToggle()">
              <?php foreach ($from as $val => $label) : ?>
                <option value="<?= $val ?>" data-from="<?= $label ?>">
                  <?= $label ?> (<?= $val ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <script>
              function fromNameToggle() {
                let target = event.target;
                let index = target.selectedIndex;
                let from_name_value = target.options[index].dataset.from;
                let from_name_og = target.value;
                let val = target.value;
                let from_name_input = document.querySelector('input[name="fromName"]');
                let reply_to_input = document.querySelector('input[name="replyTo"]');
                if (val != '') {
                  from_name_input.value = from_name_value;
                  reply_to_input.value = from_name_og;
                } else {
                  from_name_input.value = '';
                  reply_to_input.value = '';
                }
              }
            </script>
          </div>
        <?php endif; ?>

        <div class="uk-margin-bottom">
          <label class="uk-form-label uk-text-bold uk-text-uppercase">
            <i class="fa fa-at"></i>
            <?= __('Mail To:') ?>
          </label>
          <input class="uk-input" type="email" name="to" placeholder="Email address" value="<?= $to ?>" required />
        </div>

        <div class="uk-margin">
          <label class="uk-form-label uk-text-bold uk-text-uppercase">
            <i class="fa fa-pencil"></i>
            <?= __('Subject:') ?>
          </label>
          <input class="uk-input" type="text" name="subject" placeholder="Subject" value="<?= $subject ?>" required />
        </div>

        <?php if ($email_template == "") : ?>
          <div class="uk-margin-small input-toggle">
            <?php
            $AdminHelper->render('markup/trix-editor', ['load_assets' => true]);
            ?>
            <?php if ($page_ref != "") : ?>
              <em class="uk-text-muted uk-text-small uk-margin-remove">
                Reference page has been detected. You can use any field value from the referenced page with <code>{field_name}</code> or subfield <code>{field_name.title}</code> shortcode.
              </em>
            <?php endif; ?>
          </div>
        <?php else : ?>
          <label class="uk-label uk-label-warning" uk-tooltip title="Email Template">
            <i class="fa fa-html5"></i>
            <?= basename($email_template); ?>
          </label>
        <?php endif; ?>

        <?php if ($attachment != "") : ?>
          <label class="uk-label" uk-tooltip title="Attachment">
            <i class="fa fa-paperclip"></i>
            <?= basename($attachment); ?>
          </label>
        <?php endif; ?>

        <?php if (count($attachments)) : ?>
          <?php foreach ($attachments as $attachment_file) : ?>
            <label class="uk-label" uk-tooltip title="Attachment">
              <i class="fa fa-paperclip"></i>
              <?= basename($attachment_file); ?>
            </label>
          <?php endforeach; ?>
        <?php endif; ?>

      </form>
    </div>

    <div class="uk-modal-footer uk-flex uk-flex-between">
      <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
      <span id="ajax-indicator" class="ajax-indicator uk-hidden" uk-spinner></span>
      <?php if ($form_ajax) : ?>
        <button class="uk-button uk-button-primary" type="button" onclick="adminHelper.formSubmit('email-form')">Send</button>
      <?php else : ?>
        <button type="submit" form="email-form" class="uk-button uk-button-primary" type="button">Send</button>
      <?php endif; ?>
    </div>

  </div>
</div>