<?php

/**
 * html: alert
 */

namespace ProcessWire;

$class = "";

$text = !empty($text) ? $text : '';
$style = !empty($style) ? $style : 'primary';
$shadow = !isset($shadow) || $shadow ? true : false;
$class .= $shadow ? " uk-box-shadow-medium" : '';
$fa = !empty($fa) ? $fa : false;


$link = !empty($link) ? $link : false;
$blank = isset($link['blank']) && $link['blank'] ? true : false;
?>

<div class="uk-alert-<?= $style ?> uk-flex uk-flex-between uk-flex-middle <?= $class ?>" uk-alert>

  <p class="uk-margin-remove">
    <?php if ($fa) : ?>
    <i class="fa fa-<?= $fa ?> fa-fw"></i>
    <?php endif; ?>
    <?= $text ?>
  </p>

  <?php if ($link) : ?>
  <a class="uk-button uk-button-small" href="<?= $link['url'] ?>" <?php if ($blank) echo "target='_blank'" ?>>
    <?= $link['title'] ?>
  </a>
  <?php endif; ?>
</div>