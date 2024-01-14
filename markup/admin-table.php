<?php

/**
 * Admin Table HTMX
 * @var string $selector - selector string to find the pages to display eg: "template=my-template"
 */

namespace ProcessWire;

// Selector to find the pages
$selector = isset($selector) ? $selector : "";
$selector = $helper->prop('selector', $selector, "text");
// Get current page number
$pageNum = !empty($_REQUEST['pageNum']) ? (int) $_REQUEST['pageNum'] : $input->pageNum;
// Set Page Number for HTMX req
if (!empty($_REQUEST['pageNum']) && $pageNum > 1) $input->setPageNum($pageNum);
// Find items
$items = $pages->find($selector);

// Set properties and variables
$props = $helper->props([
  'selector' => $selector,
  'pageNum' => $pageNum,
  'table_fields' => !empty($table_fields) ? $table_fields : [],
  'title' => isset($title) ? $title : 1,
  'references' => isset($references) ? $references : "",
  'close_modal' => isset($close_modal) ? $close_modal : 1,
  'remove_tabs' => isset($remove_tabs) ? $remove_tabs : 1,
  'delete_tab' => isset($delete_tab) ? $delete_tab : 1,
  'delete_tab_ref' => isset($delete_tab_ref) ? $delete_tab_ref : 0,
  'settings_tab' => isset($settings_tab) ? $settings_tab : 1,
  'table_actions' => isset($table_actions) ? $table_actions : 0,
  'dropdown_file' => isset($dropdown_file) ? $dropdown_file : "",
  'table_count' => isset($table_count) ? $table_count : 0,
  'table_class' => isset($table_class) ? $table_class : "uk-table-striped",
]);

// Reassign props as variables so we can use them as: echo $key;
foreach ($props as $key => $val) $$key = $val;

// Convert table_fields to array if it is json
$table_fields = is_array($table_fields) ? $table_fields : json_decode($table_fields, true);

?>

<div id="admin-table-htmx" data-htmx="<?= __DIR__ . "/admin-table.php" ?>" data-vals='<?= $AdminHelper->json_encode($props) ?>' data-close-modal="<?= $close_modal ?>" class="uk-overflow-auto">

  <table class="AdminDataTableSortable uk-margin-remove uk-table uk-table-middle uk-table-small uk-margin-remove <?= $table_class ?>">

    <thead>
      <tr>
        <?php if (!empty($dropdown_file)) : ?>
          <th class="uk-table-shrink"></th>
        <?php endif; ?>

        <?php if ($table_count) : ?>
          <th class="uk-table-shrink"></th>
        <?php endif; ?>

        <?php if ($title) : ?>
          <th><?= __('Item') ?></th>
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

            <?php if ($table_count) : ?>
              <td class="uk-table-shrink uk-text-center uk-text-small">
                <?= $i++ ?>.
              </td>
            <?php endif; ?>

            <?php if ($title) : ?>
              <td class="uk-link-heading">
                <?php if (method_exists($item, 'admin_table_title')) : ?>
                  <a href="#" <?= $AdminHelper->htmx()->pageEditModal($item->id, $modal_options) ?>>
                    <?= $item->admin_table_title() ?>
                  </a>
                <?php else : ?>
                  <a href="#" <?= $AdminHelper->htmx()->pageEditModal($item->id, $modal_options) ?>>
                    <?= $item->title ?>
                  </a>
                <?php endif; ?>
              </td>
            <?php endif; ?>

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