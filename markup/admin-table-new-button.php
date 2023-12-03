<?php

/** 
 * tmpl: admin-table-new-button 
 * 
 */

namespace ProcessWire;

$parent_id = !empty($parent_id) ? $parent_id : "";
$template_id = !empty($template_id) ? $template_id : "";
$text = !empty($text) ? $text : "Create New";

?>

<div class="uk-padding-small uk-background-muted uk-margin-small-top">
  <a href="#" <?= $AdminHelper->htmx()->pageCreateModal($parent_id, $template_id) ?> class="uk-button uk-button-primary uk-button-small">
    <i class="fa fa-plus-circle"></i>
    <?= $text ?>
  </a>
</div>