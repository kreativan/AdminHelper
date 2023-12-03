<?php

/** 
 * Markup: next-prev-page-htmx 
 * render it inside a htmx target element
 */

namespace ProcessWire;

$items = !empty($items) ? $items : "";

$limit = !empty($limit) ? $limit : "";

$vars = !empty($vars) ? $vars : [];

$htmx_file = !empty($htmx_file) ? $htmx_file : "";
$htmx_target = !empty($htmx_target) ? $htmx_target : "";

$total = $items->getTotal();
$pages_count = ceil($total / $limit);

$prev_page_numb = $input->pageNum > 1 ? $input->pageNum - 1 : 1;
$prev_page = $prev_page_numb > 1 ? "page{$prev_page_numb}" : "";

$next_page_numb = $input->pageNum < $pages_count ? $input->pageNum + 1 : $pages_count;
$next_page = "page{$next_page_numb}";

$base_url = !empty($base_url) ? $base_url : "./";
$prev_url = "{$base_url}{$prev_page}?modal=1&htmx=$htmx_file";
$next_url = "{$base_url}{$next_page}?modal=1&htmx=$htmx_file";

// Add variables to the URL
if (count($vars) > 0) {
  foreach ($vars as $key => $value) {
    $prev_url .= "&$key=$value";
    $next_url .= "&$key=$value";
  }
}

?>

<?php if ($total > $limit) : ?>
  <div class="uk-button-group tm-border">

    <?php if ($input->pageNum > 1) : ?>
      <button class="uk-button uk-background-muted uk-button-small" hx-get="<?= $prev_url ?>" hx-target="<?= $htmx_target ?>" hx-select="<?= $htmx_target ?>" hx-swap="outerHTML">
        <i uk-icon="arrow-left"></i>
      </button>
    <?php else : ?>
      <button class="uk-button uk-background-muted uk-button-small uk-text-muted">
        <i uk-icon="arrow-left"></i>
      </button>
    <?php endif; ?>

    <button class="uk-button uk-button-small tm-bg-white uk-text-small">
      <span><?= $input->pageNum ?> / <?= $pages_count ?></span>
    </button>

    <?php if ($input->pageNum < $pages_count) : ?>
      <button class="uk-button uk-background-muted uk-button-small" hx-get="<?= $next_url ?>" hx-target="<?= $htmx_target ?>" hx-select="<?= $htmx_target ?>" hx-swap="outerHTML">
        <i uk-icon="arrow-right"></i>
      </button>
    <?php else : ?>
      <button class="uk-button uk-background-muted uk-button-small uk-text-muted">
        <i uk-icon="arrow-right"></i>
      </button>
    <?php endif; ?>

  </div>
<?php endif; ?>