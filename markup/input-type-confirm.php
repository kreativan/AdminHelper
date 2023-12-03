<?php

/** 
 * Markup: input-type-confirm.php 
 * 
 */

namespace ProcessWire;

$name = $name ?? "";
$placeholder = $placeholder ?? "";
$value = $value ?? "";
$class = !empty($class) ? " $class" : "";

?>

<span class="uk-position-relative<?= $class ?>">
  <input class="uk-input on-type-confirm" type="text" name="<?= $name ?>" value="<?= $value ?>" placeholder="<?= $placeholder ?>" onkeyup="adminHelper.inputTypeConfirm()" />
  <button type="button" class="<?= $value != "" ? 'active' : 'uk-hidden' ?>">
    <i class="fa fa-check"></i>
  </button>
</span>