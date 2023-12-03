<?php

/**
 * AdminHelper: admin-links-offcanvas
 */

namespace ProcessWire;

$items = !empty($items) ? $items : [];

$admin = [
  'Page Tree' => [
    'url' => $this->pages->get(3)->url,
    'icon' => 'sitemap',
  ],
  'Modules' => [
    'url' => $this->pages->get(21)->url,
    'icon' => 'plug',
  ],
];

$setup = [];

foreach ($this->pages->get(22)->children() as $p) {
  $setup[$p->title] = [
    'url' => $p->url,
    'icon' => $p->getIcon(),
  ];
}

$access = [];

foreach ($this->pages->get(28)->children() as $p) {
  $access[$p->title] = [
    'url' => $p->url,
    'icon' => $p->getIcon(),
  ];
}

?>

<div id="htmx-offcanvas" uk-offcanvas="overlay: true">
  <div class="uk-offcanvas-bar">

    <button class="uk-offcanvas-close" type="button" uk-close></button>

    <ul class="uk-list uk-list-divider">

      <li class="uk-nav-header uk-text-bold">Admin Menu</li>


      <?php foreach ($admin as $key => $val) : ?>
      <li>
        <a href="<?= $val['url'] ?>" class="uk-link-heading uk-text-bold">
          <?php if (!empty($val['icon'])) : ?>
          <i class="fa fa-<?= $val['icon'] ?> uk-margin-small-right uk-text-muted"></i>
          <?php endif; ?>
          <?= $key ?>
        </a>
      </li>
      <?php endforeach; ?>


      <li class="uk-nav-header uk-text-bold">Setup</li>

      <?php foreach ($setup as $key => $val) : ?>
      <li>
        <a href="<?= $val['url'] ?>" class="uk-link-heading uk-text-bold">
          <?php if (!empty($val['icon'])) : ?>
          <i class="fa fa-<?= $val['icon'] ?> uk-margin-small-right  uk-text-muted"></i>
          <?php endif; ?>
          <?= $key ?>
        </a>
      </li>
      <?php endforeach; ?>

      <li class="uk-nav-header uk-text-bold">Access</li>

      <?php foreach ($access as $key => $val) : ?>
      <li>
        <a href="<?= $val['url'] ?>" class="uk-link-heading uk-text-bold">
          <?php if (!empty($val['icon'])) : ?>
          <i class="fa fa-<?= $val['icon'] ?> uk-margin-small-right  uk-text-muted"></i>
          <?php endif; ?>
          <?= $key ?>
        </a>
      </li>
      <?php endforeach; ?>

    </ul>


  </div>
</div>