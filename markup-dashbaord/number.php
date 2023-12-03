<?php

/**
 * html: number
 */

namespace ProcessWire;

$number = !empty($number) ? $number : 0;
$text = !empty($text) ? $text : false;
$trend = !empty($trend) ? $trend : false;

?>

<div class="uk-text-center uk-padding-small uk-padding-remove-horizontal">

  <div class="uk-flex uk-flex-center uk-flex-bottom">
    <span class="uk-h1 uk-margin-remove uk-inline uk-text-bold" style="line-height: 0.8">
      <?= $number ?>
    </span>
    <?php if ($trend == 'up') : ?>
    <i class="fa fa-arrow-up uk-text-success uk-text-bold" style="margin-left:5px;font-size:1.1rem"></i>
    <?php elseif ($trend == 'down') : ?>
    <i class="fa fa-arrow-down uk-text-danger uk-text-bold" style="margin-left:5px;"></i>
    <?php endif; ?>
  </div>

  <?php if ($text) : ?>
  <div class="uk-text-small uk-text-muted uk-margin-small">
    <?= $text ?>
  </div>
  <?php endif; ?>

</div>