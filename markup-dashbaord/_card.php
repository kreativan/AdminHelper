<?php

/**
 * layout: card
 */

namespace ProcessWire;

$card_class = '';
$body_id = !empty($body_id) ? $body_id : '';
$body_class = isset($body) && !$body ? ''  : 'uk-card-body';
$body_attr = "";
$body_style = "";

// file that will be rendered in the card
$file = !empty($file) ? $file : '';
// data that will be passed to the file
$data = count($data) ? $data : [];

// card headline
$headline = !empty($headline) ? $headline : '';

// card icon
$icon = !empty($icon) ? $icon : '';

// wrapper div class
$wrap = !empty($wrap) ? $wrap : '';

// $class - card additional class
$card_class .= !empty($class) ? " $class" : '';

// main card class
$card_class = !empty($style) ? " uk-card-$style" : ' uk-card-default';
$card_class .= !empty($size) ? " uk-card-$size" : ' uk-card-small';
$card_class .= !empty($hover) && $hover ? " uk-card-hover" : '';

// use flex to align card body to the middle
$flex = !empty($flex) ? $flex : false;
$body_class .= $flex ? ' uk-flex uk-flex-middle uk-flex-center' : '';

// set height
$height = !empty($height) ? $height : '';
$body_style .= $height ? "min-height: {$height};" : '';

// htmx sync
$htmx_sync = isset($htmx_sync) && $htmx_sync ? true : false;
if ($htmx_sync) {
  $htmx_vals = isset($data['htmx_vals']) ? $data['htmx_vals'] : [];
  $body_attr .= " data-htmx={$file}";
  $body_attr .= ' data-vals="' . $wireApp->utility()->json_encode($htmx_vals) . '"';
}

?>

<div class="<?= $wrap ?>">

  <div class="uk-card uk-border-rounded<?= $card_class ?>">

    <?php if ($headline) : ?>
      <div class="uk-card-header">
        <h3 class="uk-card-title uk-margin-remove-bottom uk-text-bold" style="font-size: 1rem;">
          <?php if ($icon) : ?>
            <i class="fa fa-<?= $icon ?> fa-fw uk-text-muted"></i>
          <?php endif; ?>
          <?= $headline ?>
        </h3>
      </div>
    <?php endif; ?>

    <div id="<?= $body_id ?>" class="<?= $body_class ?>" <?= $body_attr ?> style="<?= $body_style ?>">
      <?php
      $files->include($file, $data);
      ?>
    </div>

  </div>

</div>