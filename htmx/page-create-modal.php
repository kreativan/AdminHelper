<?php
$src = $config->urls->admin . "page/add/?modal=1";
if ($input->get->parent_id) $src .= "&parent_id={$input->get->parent_id}";
if ($input->get->template_id) $src .= "&template_id={$input->get->template_id}";
?>

<div id="htmx-modal" class="uk-modal-container" uk-modal="bg-close: false;">
  <div class="uk-modal-dialog uk-width-2xlarge" style="height: 95%" ;>

    <div class="uk-light uk-position-z-index" style="position: relative; padding: 7px 20px;background: #1C2836;">
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

    <iframe id="page-create-iframe-font-end" src="<?= $src ?>" width="100%" height="100%" class="uk-position-relative uk-width-1-1 uk-height-1-1"></iframe>

  </div>
</div>