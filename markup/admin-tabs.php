<?php

/** 
 * Admin Helper: admin-tabs 
 * 
 * Need to pass array of tabs ['tab_name' => []]
 * @var string $tab['title']
 * @var string $tab['url']
 * @var string $tab['icon']
 * @var bool $tab['visible']
 * 
 * You can also pass @var $active_var or @var $active_tab - "./?$active_var=my_tab" 
 * to change the active tab variable name (default is "tab" ?tab=my_tab)
 * @example $AdminHelper->render("markup/admin-tabs", ['tabs' => $tabs, 'active_var' => 'my_var']);
 */

namespace ProcessWire;

$i = 0;
$tabs = !empty($tabs) ? $tabs : [];
$active_tab = !empty($active_tab) ? $active_tab : "tab";
$active_var = !empty($active_var) ? $active_var : $active_tab;
?>

<?php if (count($tabs) > 0) : ?>
  <ul class="uk-tab uk-position-relative">

    <?php foreach ($tabs as $key => $tab) :
      $visible = !isset($tab['visible']) || $tab['visible'] == true ? true : false;
      $icon = isset($tab['icon']) && $tab['icon'] ? "<i class='fa fa-{$tab['icon']}'></i> " : "";
    ?>
      <?php if ($visible) :
        $name = $tab['name'] ?? $key;
        $GET = $input->get->{$active_var};
      ?>
        <li class="<?= ((!$GET && $i++ == 0) || $GET == $name) ? "uk-active" : "" ?>">
          <a href="<?= $tab['url'] ?>" hx-get="./?<?= $active_var ?>=<?= $name ?>" hx-select="#pw-content-body" hx-target="#pw-content-body" hx-swap="outerHTML" hx-indicator=".htmx-indicator" hx-push-url="./?<?= $active_var ?>=<?= $name ?>">
            <?= $icon ?>
            <?= $tab['title'] ?>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>

    <li>
      <a href="#" class="htmx-indicator">
        <i class="uk-text-emphasis" uk-spinner="ratio: 0.7"></i>
      </a>
    </li>

  </ul>
<?php endif; ?>