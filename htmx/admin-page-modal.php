<?php
$src = !empty($input->get->src) ? $sanitizer->text($input->get->src) : "";
$src .= "?modal=1&front_end_modal=1";
$title = $input->get->title ? $sanitizer->text($input->get->title) : $page("title|headline");
?>

<div id="htmx-modal" class="uk-modal-container" uk-modal="bg-close: false;">
  <div class="uk-modal-dialog uk-width-2xlarge" style="height: 95%" ;>

    <div class="uk-light uk-position-relative uk-position-z-index" style="position: relative; padding: 7px 20px;background: #1C2836;">
      <p class="uk-margin-remove uk-text-emphasis">
        <?php
        echo !empty($title) ? $title : "Edit";
        ?>
      </p>
      <a href="./" class="uk-position-center-right uk-position-small" uk-close="ratio: 1.2"></a>
    </div>

    <hr class=" uk-margin-remove" />

    <div class="page-edit-modal-indicator uk-position-cover" style="background: rgba(255, 255, 255, 0.5)">
      <span class="uk-position-center" uk-spinner></span>
    </div>

    <iframe id="page-edit-iframe-font-end" src="<?= $src ?>" width="100%" height="100%" class="uk-width-1-1 uk-height-1-1 uk-position-relative"></iframe>

  </div>
</div>