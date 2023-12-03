<?php

/**
 * Markup: chart
 */

namespace ProcessWire;

$class = !empty($class) ? $class : "";
$chart = !empty($chart) ? $chart : [];

?>

<div class="<?= $class ?>">
  <canvas class="admin-helper-chart" data-chart="<?= $AdminHelper->json_encode($chart) ?>"></canvas>
</div>