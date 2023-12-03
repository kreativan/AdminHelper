<?php

/** 
 * Markup: trix 
 * 
 */

namespace ProcessWire;

$id = !empty($id) ? $id : "trix";
$name = !empty($name) ? $name : "body";
$value = !empty($value) ? $value : "";
$placeholder = !empty($placeholder) ? $placeholder : "Your content here";
$load_assets = !empty($load_assets) && $load_assets ? true : false;

?>

<?php if ($load_assets) : ?>
  <link rel="stylesheet" type="text/css" href="<?= $AdminHelper->url() ?>lib/trix/trix.css" />
  <script type="text/javascript" src="<?= $AdminHelper->url() ?>lib/trix/trix.umd.min.js"></script>
<?php endif; ?>

<input id="<?= $id ?>" type="hidden" name="<?= $name ?>" value="<?= $value ?>">
<trix-editor class="trix-content" input="<?= $id  ?>" placeholder="<?= $placeholder ?>"></trix-editor>