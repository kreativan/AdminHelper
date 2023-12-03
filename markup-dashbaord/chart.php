<?php

/**
 * html: chart-line
 */

namespace ProcessWire;

$utility = $this->wireApp->utility();

$chart = !empty($chart) ? $chart : [];
?>

<div>
  <canvas class="wireapp-chart" data-chart="<?= $wireApp->json_encode($chart) ?>"></canvas>
</div>