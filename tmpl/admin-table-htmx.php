<?php

/**
 * Admin Table HTMX
 * @var string $selector - selector string to find the pages to display eg: "template=my-template"
 * @var string $table_fields - array of fields to display in the table eg: ["Template" => "template.name", "ID" => "id"]
 * @var string $close_modal - close modal after page edit
 * @var string $table_actions - show table actions publish-unpublish, trash
 * @var string $htmx_data - htmx data to pass to the table.php when loaded via htmx
 */

namespace ProcessWire;

// Selector to find the pages
$selector = !empty($selector) ? $selector : "";
$selector = $input->get->selector ? $sanitizer->text($input->get->selector) : $selector;

// Find items
$items = $pages->find($selector);

// Table fields to display
$table_fields = !empty($table_fields) ? $table_fields : [];
$table_fields = $input->get->table_fields ? $sanitizer->text($input->get->table_fields) : $table_fields;

// Page References table field
$references = !empty($references) ? $references : "";
$references = $input->get->references ? $sanitizer->text($input->get->references) : $references;
$references = $references == "true" ? true : false;

// Close modal after page edit
$close_modal = !empty($close_modal) ? $close_modal : "";
$close_modal = $input->get->close_modal ? $sanitizer->text($input->get->close_modal) : $close_modal;
$close_modal = $close_modal == "true" ? "true" : "false";

// Hide tabs on modal page edit
$remove_tabs = !empty($remove_tabs) ? $remove_tabs : "";
$remove_tabs = $input->get->remove_tabs ? $sanitizer->text($input->get->remove_tabs) : $remove_tabs;
$remove_tabs = $remove_tabs == "true" ? "true" : "false";

// remove delete tab on modal page edit
$delete_tab = !empty($delete_tab) ? $delete_tab : "";
$delete_tab = $input->get->delete_tab ? $sanitizer->text($input->get->delete_tab) : $delete_tab;
$delete_tab = $delete_tab == "true" ? "true" : "false";

// Show table actions publish-unpublish, trash
$table_actions = !empty($table_actions) ? $table_actions : "true";
$table_actions = $input->get->table_actions ? $sanitizer->text($input->get->table_actions) : $table_actions;
$table_actions = $table_actions == "true" ? true : false;

// Multi-language
// Show multilang td with name of the page in other languages
$multilang = !empty($multilang) ? $multilang : "true";
$multilang = $input->get->multilang ? $sanitizer->text($input->get->multilang) : $multilang;
$multilang = $multilang == "true" ? true : false;

// Display icon in the first column
$icon = !empty($icon) ? $icon : "";
$icon = $input->get->icon ? $sanitizer->text($input->get->icon) : $icon;

// Dropdown file path
$dropdown_file = !empty($dropdown_file) ? $dropdown_file : "";
$dropdown_file = $input->get->dropdown_file ? $sanitizer->text($input->get->dropdown_file) : $dropdown_file;

// Label
$label = !empty($label) ? $label : "";
$label = $input->get->label ? $sanitizer->text($input->get->label) : $label;

// Htmx data to pass to the table.php when loaded via htmx
$htmx_data = [
  'selector' => $selector,
  'table_fields' => $table_fields,
  'references' => $references,
  'close_modal' => $close_modal,
  'remove_tabs' => $remove_tabs,
  'delete_tab' => $delete_tab,
  'table_actions' => $table_actions,
  'icon' => $icon,
  'dropdown_file' => $dropdown_file,
  'label' => $label,
];

// Convert table_fields to array if it is json
$table_fields = is_array($table_fields) ? $table_fields : json_decode($table_fields, true);
?>

<div id="admin-table-htmx" data-htmx="<?= __DIR__ . "/admin-table-htmx.php" ?>" data-vals='<?= $AdminHelper->json_encode($htmx_data) ?>' data-close-modal="<?= $close_modal ?>">

  <table class="AdminDataTableSortable uk-table uk-table-striped uk-table-middle uk-table-small uk-margin-remove">

    <thead>
      <tr>
        <?php if (!empty($dropdown_file)) : ?>
          <th class="uk-table-shrink"></th>
        <?php endif; ?>

        <?php if (!empty($icon)) : ?>
          <th class="uk-table-shrink"></th>
        <?php endif; ?>

        <th><?= __('Title') ?></th>

        <?php if (!empty($label)) : ?>
          <th></th>
        <?php endif; ?>

        <?php if ($multilang && !empty($languages) && count($languages) > 0) : ?>
          <th><?= __('Multi-language') ?></th>
        <?php endif; ?>

        <?php foreach ($table_fields as $key => $value) : ?>
          <th><?= $key ?></th>
        <?php endforeach; ?>

        <?php if ($references) : ?>
          <th class="uk-table-shrink">
            <?= __('Ref.') ?>
          </th>
        <?php endif; ?>

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

          // remove modal page edit tabs
          $remove_modal_tabs = $remove_tabs == "true" ? true : false;
          // if remove_delete_tab and item has references remove delete tab
          $remove_delete_tab = $delete_tab == "true" ? true : false;
          $remove_delete_tab = $remove_delete_tab && $item->references()->count > 0 ? true : $remove_delete_tab;
          // set modal options
          $modal_options = ['container' => 1, 'height' => '100%', 'remove_tabs' => $remove_modal_tabs, 'remove_delete_tab' => $remove_delete_tab];

          // Page reference link
          $pageRefLink = $wirekit->pageRefLink($item);
        ?>
          <tr class="<?= $class ?>">

            <?php if (!empty($dropdown_file)) : ?>
              <td class="uk-table-shrink">
                <a href="#" class="uk-icon-button" uk-icon="more-vertical" hx-get="./?htmx=<?= $dropdown_file ?>&id=<?= $item->id ?>" hx-target=" #dropdown-<?= $item->id ?>" hx-swap="innerHTML">
                </a>
                <div id="dropdown-<?= $item->id ?>" class="uk-dropdown" uk-dropdown="mode: click;">
                  <span uk-spinner></span>
                </div>
              </td>
            <?php endif; ?>

            <?php if (!empty($icon)) : ?>
              <td class="uk-table-shrink uk-text-center">
                <i class="<?= $icon ?>"></i>
              </td>
            <?php endif; ?>

            <td>
              <a href="#" <?= $AdminHelper->htmx()->pageEditModal($item->id, $modal_options) ?>>
                <?= $item->title ?>
              </a>
            </td>

            <?php if (!empty($label)) : ?>
              <td>
                <label class="uk-label">
                  <?= $item->{$label} ?>
                </label>
              </td>
            <?php endif; ?>

            <?php if ($multilang && !empty($languages) && count($languages) > 1) : ?>
              <td class="uk-text-small">
                <?php
                foreach ($languages as $lang) {
                  if ($lang->name != "default") {
                    echo $item->getLanguageValue($lang, 'title') . " ({$lang->name})<br />";
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

            <?php if ($references) : ?>
              <td class="uk-table-shrink">
                <?php if (!empty($pageRefLink)) : ?>
                  <a href="<?= $pageRefLink ?>">
                    <?= $item->references()->count ?>
                  </a>
                <?php else : ?>
                  <?= $item->references()->count ?>
                <?php endif; ?>
              </td>
            <?php endif; ?>

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