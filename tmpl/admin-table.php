<?php

/**
 * Admin Table
 * @var string $selector - selector string to find the pages to display eg: "templat=my-template"
 * @var string $table_fields - array of fields to display in the table eg: ["Template" => "template.name", "ID" => "id"]
 * @var string $close_modal - close modal after page edit
 * @var string $table_actions - show table actions publish-unpublish, trash
 */

namespace ProcessWire;

// Selector to find the pages
$selector = !empty($selector) ? $selector : "";

// Table fields to display
$table_fields = !empty($table_fields) ? $table_fields : [];

// Find items
$items = $pages->find($selector);

// Show table actions publish-unpublish, trash
$table_actions = !empty($table_actions) && $table_actions ? true : false;

?>

<table class="AdminDataTableSortable uk-table uk-table-striped uk-table-middle uk-table-small uk-margin-remove">

  <thead>
    <tr>
      <th><?= __('Title') ?></th>
      <?php foreach ($table_fields as $key => $value) : ?>
        <th><?= $key ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>

  <tbody>
    <?php if ($items && $items->count) : ?>
      <?php foreach ($items as $item) :
        // row (tr) css class
        // add is-hidden class if page is hidden or unpublished
        $class = $item->isHidden() || $item->isUnpublished() ? "is-hidden" : "";

        // Page edit modal options
        $modal_options = ['container' => 1, 'height' => '100%'];
      ?>
        <tr class="<?= $class ?>">

          <td>
            <a href="#" <?= $AdminHelper->htmx()->pageEditModal($item->id, $modal_options) ?>>
              <?= $item->title ?>
            </a>
          </td>

          <!-- additional fields -->
          <?php foreach ($table_fields as $key => $value) :
            $val = $item->{$value};
          ?>
            <td>
              <?= !empty($val) ? $val : "-" ?>
            </td>
          <?php endforeach; ?>

          <!-- table actions -->
          <?php if ($table_actions) : ?>
            <td class="uk-text-right uk-text-small" style="width: 120px;padding-right: 20px;">
              <button class="tm-reset-button uk-margin-small-left" onclick="adminHelper.togglePage(<?= $item->id ?>)" title="Show / Hide" uk-tooltip>
                <i class="fa fa-toggle-<?= $item->isUnpublished() ? "off" : "on" ?> fa-lg"></i>
              </button>
              <button class="tm-reset-button uk-text-danger uk-margin-small-left" onclick="adminHelper.trashPage(<?= $item->id ?>)" title="Trash" uk-tooltip>
                <i class="fa fa-times-circle fa-lg"></i>
              </button>
            </td>
          <?php endif; ?>

        </tr>
      <?php endforeach; ?>
    <?php else :
      // if there is no items display a message 
    ?>
      <tr>
        <td colspan="100%">
          <?= __('No items to display') ?>
        </td>
      </tr>
    <?php endif; ?>
  </tbody>

</table>

<?php
// Render Pagination
if (!$input->get->q) echo $items->renderPager();
?>