<?php

/**
 * Admin Table HTMX
 * @var string $selector - selector string to find the pages to display eg: "template=my-template"
 */

namespace ProcessWire;

// Selector to find the pages
$selector = !empty($selector) ? $selector : "";
// get current paginated page
$selector .= $input->pageNum > 1 ? ", start=$input->pageNum" : "";
// find
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
$delete_tab = $delete_tab == "false" ? "false" : "true";

// remove delete tab if page has references
$delete_tab_ref = !empty($delete_tab_ref) ? $delete_tab_ref : "";
$delete_tab_ref = $input->get->delete_tab_ref ? $sanitizer->text($input->get->delete_tab_ref) : $delete_tab_ref;
$delete_tab_ref = $delete_tab_ref == "true" ? "true" : "false";

// remove settings tab on modal page edit
$settings_tab = !empty($settings_tab) ? $settings_tab : "";
$settings_tab = $input->get->settings_tab ? $sanitizer->text($input->get->settings_tab) : $settings_tab;
$settings_tab = $settings_tab == "false" ? "false" : "true";

// Show table actions publish-unpublish, trash
$table_actions = !empty($table_actions) ? $table_actions : "true";
$table_actions = $input->get->table_actions ? $sanitizer->text($input->get->table_actions) : $table_actions;
$table_actions = $table_actions == "true" ? true : false;

// Dropdown file path
$dropdown_file = !empty($dropdown_file) ? $dropdown_file : "";
$dropdown_file = $input->get->dropdown_file ? $sanitizer->text($input->get->dropdown_file) : $dropdown_file;

// Htmx data to pass to the table.php when loaded via htmx
$htmx_data = [
  'selector' => $selector,
  'table_fields' => $table_fields,
  'references' => $references,
  'close_modal' => $close_modal,
  'remove_tabs' => $remove_tabs,
  'delete_tab' => $delete_tab,
  'delete_tab_ref' => $delete_tab_ref,
  'settings_tab' => $settings_tab,
  'table_actions' => $table_actions,
  'dropdown_file' => $dropdown_file,
];

// Convert table_fields to array if it is json
$table_fields = is_array($table_fields) ? $table_fields : json_decode($table_fields, true);
?>

<div id="admin-table-htmx" data-htmx="<?= __DIR__ . "/admin-table.php" ?>" data-vals='<?= $AdminHelper->json_encode($htmx_data) ?>' data-close-modal="<?= $close_modal ?>" class="uk-overflow-auto">

  <table class="AdminDataTableSortable uk-table uk-table-striped uk-table-middle uk-table-small uk-margin-remove">

    <thead>
      <tr>
        <?php if (!empty($dropdown_file)) : ?>
          <th class="uk-table-shrink"></th>
        <?php endif; ?>

        <th><?= __('Title') ?></th>

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

          $delete_tab = $delete_tab == "false" ? true : false;
          $delete_tab = $delete_tab_ref == "true" && $item->references()->count > 0 ? true : $delete_tab;

          // set modal options
          $modal_options = [
            'container' => 1,
            'height' => '100%',
            'remove_tabs' => ($delete_tab == "true" && $item->references()->count > 0) ? true : false,
            'remove_delete_tab' => $delete_tab,
            'remove_settings_tab' => $settings_tab == "false" ? true : false,
          ];

          // Page reference link
          $pageRefLink = isset($wirekit) ? $wirekit->pageRefLink($item) : "";
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

            <td>
              <?php if (method_exists($item, 'admin_table_title')) : ?>
                <?= $item->admin_table_title() ?>
              <?php else : ?>
                <a href="#" <?= $AdminHelper->htmx()->pageEditModal($item->id, $modal_options) ?>>
                  <?= $item->title ?>
                </a>
              <?php endif; ?>
            </td>

            <?php if (!empty($label)) : ?>
              <td>
                <label class="uk-label uk-label-primary uk-text-center uk-label-<?= $item->{$label} ?>" style="width: 120px;">
                  <?= $item->{$label} ?>
                </label>
              </td>
            <?php endif; ?>

            <!-- additional fields -->
            <?php foreach ($table_fields as $key => $value) :
              $value = str_replace("()", "", $value);
              $val = method_exists($item, $value) ? $item->{$value}() : $item->{$value};
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
if (!$input->get->q && !$input->get->htmx) echo $items->renderPager();
?>