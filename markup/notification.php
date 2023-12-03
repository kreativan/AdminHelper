<?php

/** 
 * Markup: notification 
 * 
 */

namespace ProcessWire;

$icon = !empty($icon) ? $icon : '';
$message = !empty($message) ? $message : '';
$status = !empty($status) ? $status : 'primary';
$pos = !empty($pos) ? $pos : 'top-right';
$timeout = !empty($timeout) ? $timeout : 3000;
?>

<script>
  UIkit.notification({
    message: '<i class="fa fa-<?= $icon ?> fa-lg uk-margin-small-right"></i> <?= $message ?>',
    status: '<?= $status ?>',
    pos: '<?= $pos ?>',
    timeout: '<?= $timeout ?>'
  });
</script>