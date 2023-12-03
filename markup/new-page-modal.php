<?php

namespace ProcessWire;

$title = !empty($input->get->title) ? $input->get->title : $page->title;
$parent_id = $input->get->parent_id;
$template_id = $input->get->template_id;
?>

<div id="htmx-modal" class="page-create-modal uk-flex-top uk-modal=" bg-close: true">
  <div class="uk-modal-dialog uk-margin-auto-vertical uk-overflow-hidden">

    <form id="new-page-submit" action="./?action=new-page-submit" method="POST" class="uk-form-stacked">

      <input type="hidden" name="parent_id" value="<?= $parent_id ?>">
      <input type="hidden" name="template_id" value="<?= $template_id ?>">

      <div class="uk-modal-header uk-position-relative">
        <h3 class="uk-margin-remove">
          <?= $title ?>
        </h3>
        <button class="uk-modal-close uk-position-center-right uk-margin-right" uk-close></button>
      </div>

      <div class="Inputfields uk-modal-body">
        <div class="uk-margin">
          <label class="uk-form-label uk-text-bold">
            Title
            <span class="uk-text-danger">*</span>
          </label>
          <input class="uk-input" type="text" name="title" placeholder="Title" required>
        </div>
      </div>

      <div class="uk-modal-footer uk-background-muted uk-flex uk-flex-between">
        <span class="ajax-indicator uk-hidden" uk-spinner></span>
        <span></span>
        <button class="uk-button uk-button-primary" onclick="adminHelper.formSubmit('new-page-submit')">
          Submit
        </button>
      </div>

    </form>

  </div>
</div>