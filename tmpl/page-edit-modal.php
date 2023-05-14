<?php

namespace ProcessWire;

$src = $sanitizer->text($input->get->src);
$id = $sanitizer->int($input->get->page_id);
$p = $pages->get($id);

$container = !empty($input->get->container) && $input->get->container = 1 ? true : false;
$height = !empty($input->get->height) ? $input->get->height : '95%';
$width = !empty($input->get->width) ? $input->get->width : 'uk-width-2xlarge';
$full = !empty($input->get->full) && $input->get->full == 'true' ? ' uk-modal-full' : '';
$header = !empty($input->get->header) && $input->get->header == 'false' ? false : true;
?>

<div id="htmx-modal" class="page-edit-modal<?= $full ?> uk-flex-top <?= $container ? 'uk-modal-container' : '' ?>" uk-modal="bg-close: true">
  <div class="uk-modal-dialog uk-overflow-hidden uk-margin-auto-vertical <?= $width ?>" style="height:<?= $height ?>">

    <?php if ($header) : ?>
      <div class="uk-modal-header uk-position-relative uk-visible@l">
        <h3 class="uk-margin-remove">
          <?= $page->title ?> - <?= $p->title ?>
        </h3>
        <button class="uk-modal-close uk-position-center-right uk-margin-right" uk-close></button>
      </div>
    <?php else : ?>
      <a class="uk-modal-close uk-link-normal uk-text-muted uk-position-top-right uk-position-medium uk-margin-medium-right">
        <i uk-icon="icon: close; ratio:1.3"></i>
      </a>
    <?php endif; ?>

    <span class="uk-position-center" uk-spinner></span>

    <iframe id="page-edit-iframe" src="<?= $src ?>" width="100%" height="100%" class="uk-width-1-1 uk-height-1-1"></iframe>

    <div class="page-edit-modal-indicator uk-position-cover uk-hidden" style="background: rgba(255, 255, 255, 0.5)">
      <span class="uk-position-center" uk-spinner></span>
    </div>

  </div>
</div>