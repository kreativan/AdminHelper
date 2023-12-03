<?php

namespace ProcessWire;

$src = $config->urls->admin . "page/add/?modal=1";
if ($input->get->parent_id) $src .= "&parent_id={$input->get->parent_id}";
if ($input->get->template_id) $src .= "&template_id={$input->get->template_id}";
$height = !empty($input->get->height) ? $input->get->height : '95%';
$container = $input->get->container;
$container = empty($container) || !$container ? true : false;
?>

<div id="htmx-modal" class="page-create-modal uk-flex-top <?= $container ? 'uk-modal-container' : '' ?>" uk-modal="bg-close: true">
  <div class="uk-modal-dialog uk-margin-auto-vertical uk-overflow-hidden <?= !$container ? 'uk-width-2xlarge' : '' ?>" style="height:<?= $height ?>">

    <div class="uk-modal-header uk-position-relative">
      <h3 class="uk-margin-remove">
        <?= $page->title ?>
      </h3>
      <button class="uk-modal-close uk-position-center-right uk-margin-right" uk-close></button>
    </div>

    <iframe id="page-create-iframe" src="<?= $src ?>" width="100%" height="100%" class="uk-width-1-1 uk-height-1-1"></iframe>

  </div>
</div>