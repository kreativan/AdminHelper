<?php
$page_id = $input->get->page_id ? $sanitizer->int($input->get->page_id) : $page->id;
$page = $pages->get($page_id);
$admin_url = $config->urls->admin;
$src = $admin_url . "page/edit/?id={$page_id}&modal=1&front_end_modal=1&remove_tabs=1";

$no_container = $input->get->no_container;
$no_container = !empty($no_container) && $no_container ? true : false;

$title = $input->get->title ? $sanitizer->text($input->get->title) : "";

if ($title == "") {
  $title = !empty($page->template->label) ? $page->template->label : $page->template->name;
  $title = ucwords(str_replace("-", " ", $title));
  $title .= $page->pb_options ? " ({$page->pb_options->pb_layout})" : "";
}

?>

<div id="htmx-modal" class="<?= !$no_container ? 'uk-modal-container' : '' ?>" uk-modal="bg-close: false;">
  <div class="uk-modal-dialog uk-width-2xlarge" style="height: 95%" ;>

    <div class="uk-light" style="position: relative; padding: 7px 20px;background: #1C2836;">
      <p class="uk-margin-remove uk-text-emphasis">
        <?php
        echo !empty($title) ? $title : "Edit";
        ?>
      </p>
      <button class="uk-modal-close-default uk-position-center-right uk-position-small" type="button" uk-close="ratio: 1.2"></button>
    </div>

    <hr class=" uk-margin-remove" />

    <div class="page-edit-modal-indicator uk-position-cover" style="background: rgba(255, 255, 255, 0.5)">
      <span class="uk-position-center" uk-spinner></span>
    </div>

    <iframe id=" page-edit-iframe-font-end" src="<?= $src ?>" width="100%" height="100%" class="uk-width-1-1 uk-height-1-1"></iframe>

  </div>
</div>