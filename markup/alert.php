<?php

/**
 * Markup: alert
 */

namespace ProcessWire;

$class = "";

$text   = !empty($text) ? $text : '';
$style  = !empty($style) ? $style : 'primary';
$class  = !empty($class) ? " $class" : '';
$icon   = !empty($icon) ? $icon : false;

$link = !empty($link) ? $link : false;
$blank = isset($link['blank']) && $link['blank'] ? true : false;
?>

<div class="uk-alert-<?= $style ?> uk-flex uk-flex-between uk-flex-middle<?= $class ?>" uk-alert>

  <p class="uk-margin-remove">
    <?php if ($icon) : ?>
      <i class="fa fa-<?= $icon ?> fa-fw"></i>
    <?php endif; ?>
    <?= $text ?>
  </p>

  <?php if ($link) : ?>
    <a class="uk-button uk-button-small" href="<?= $link['url'] ?>" <?php if ($blank) echo "target='_blank'" ?>>
      <?= $link['title'] ?>
    </a>
  <?php else : ?>
    <a href class="uk-alert-close" uk-close></a>
  <?php endif; ?>
</div>