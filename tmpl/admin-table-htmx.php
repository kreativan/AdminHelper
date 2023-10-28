<?php

/**
 * Admin Table HTMX
 * @var string $selector - selector string to find the pages to display eg: "templat=my-template"
 * @var string $table_fields - array of fields to display in the table eg: ["Template" => "template.name", "ID" => "id"]
 * @var string $close_modal - close modal after page edit
 * @var string $table_actions - show table actions publish-unpublish, trash
 * @var string $htmx_data - htmx data to pass to the table.php when loaded via htmx
 */

namespace ProcessWire;

// Selector to find the pages
$selector = !empty($selector) ? $selector : "";
$selector = $input->get->selector ? $sanitizer->text($input->get->selector) : $selector;

// Table fields to display
$table_fields = !empty($table_fields) ? $table_fields : [];
$table_fields = $input->get->table_fields ? $sanitizer->text($input->get->table_fields) : $table_fields;

// Find items
$items = $pages->find($selector);

// Close modal after page edit
$close_modal = !empty($close_modal) ? $close_modal : "";
$close_modal = $input->get->close_modal ? $sanitizer->text($input->get->close_modal) : $close_modal;
$close_modal = $close_modal == "true" ? "true" : "false";

// Show table actions publish-unpublish, trash
$table_actions = !empty($table_actions) ? $table_actions : "true";
$table_actions = $input->get->table_actions ? $sanitizer->text($input->get->table_actions) : $table_actions;
$table_actions = $table_actions == "true" ? true : false;

// Multi-language
$multilang = !empty($multilang) ? $multilang : "true";
$multilang = $input->get->multilang ? $sanitizer->text($input->get->multilang) : $multilang;
$multilang = $multilang == "true" ? true : false;

$icon = !empty($icon) ? $icon : "";
$icon = $input->get->icon ? $sanitizer->text($input->get->icon) : $icon;

// Htmx data to pass to the table.php when loaded via htmx
$htmx_data = [
  'selector' => $selector,
  'table_fields' => $table_fields,
  'close_modal' => $close_modal,
  'table_actions' => $table_actions,
  'icon' => $icon,
];

// Convert table_fields to array if it is json
$table_fields = is_array($table_fields) ? $table_fields : json_decode($table_fields, true);
?>

<div id="admin-table-htmx" data-htmx="<?= __DIR__ . "/admin-table-htmx.php" ?>" data-vals='<?= $AdminHelper->json_encode($htmx_data) ?>' data-close-modal="<?= $close_modal ?>">

  <table class="AdminDataTableSortable uk-table uk-table-striped uk-table-middle uk-table-small uk-margin-remove">

    <thead>
      <tr>
        <?php if (!empty($icon)) : ?>
          <th class="uk-table-shrink"></th>
        <?php endif; ?>

        <th><?= __('Title') ?></th>

        <?php if ($multilang && $languages && count($languages) > 0) : ?>
          <th><?= __('Multi-language') ?></th>
        <?php endif; ?>

        <?php foreach ($table_fields as $key => $value) : ?>
          <th><?= $key ?></th>
        <?php endforeach; ?>

        <?php if ($table_actions) : ?>
          <th></th>
        <?php endif; ?>
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

            <?php if (!empty($icon)) : ?>
              <td class="uk-table-shrink uk-text-center">
                <i class="<?= $icon ?>"></i>
              </td>
            <?php endif; ?>

            <td>
              <a href="#" <?= $AdminHelper->htmx()->pageEditModal($item->id, $modal_options) ?>>
                <?= $item->title ?>
                <?= $item->get("title|bg"); ?>
              </a>
            </td>

            <?php if ($multilang && $languages && count($languages) > 1) : ?>
              <td class="uk-text-small">
                <?php
                foreach ($languages as $lang) {
                  if ($lang->name != "default") {
                    echo $item->get("title|{$lang->name}") . " ({$lang->name})<br />";
                  }
                }
                ?>
              </td>
            <?php endif; ?>

            <!-- additional fields -->
            <?php foreach ($table_fields as $key => $value) :
              $val = $item->{$value};
            ?>
              <td class="admin-table-<?= $sanitizer->fieldName($value) ?> uk-text-small">
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

</div>

<?php
// Render Pagination
if (!$input->get->q) echo $items->renderPager();
?>