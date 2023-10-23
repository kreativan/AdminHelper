<?php

/**
 * examples: input-type-confirm.php
 */
?>
<span class="uk-position-relative">
  <input class="uk-input on-type-confirm" type="text" name="cp" value="<?= $input->get->cp ?>"
    placeholder="<?= __('CP') ?>..." onkeyup="adminHelper.inputTypeConfirm()" />
  <button type="button" class="<?= $input->get->cp ? 'active' : 'uk-hidden' ?>">
    <i class="fa fa-check"></i>
  </button>
</span>