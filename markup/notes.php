<?php

/** 
 * actions: notes-list
 * 
 */

namespace ProcessWire;

$class = !empty($class) ? $class : "tm-border uk-padding-small";

$parent_page = !empty($parent_page) ? $parent_page : "";
$parent_page = $input->get->parent_page ? $input->get->parent_page : $parent_page;

$template = !empty($template) ? $template : "note";
$template = $input->get->template ? $input->get->template : $template;

$limit = !empty($limit) ? $limit : 3;
$limit = $input->get->limit ? $input->get->limit : $limit;

$sort = !empty($sort) ? $sort : "-created";
$sort = $input->get->sort ? $input->get->sort : $sort;

$selector = "parent=$parent_page, template=$template, limit=$limit, sort=$sort";
$selector .= $input->pageNum > 1 ? ", start=$input->pageNum" : "";
$items = $pages->find($selector);

?>

<div id="notes" class="<?= $class ?> uk-position-relative">

  <?php if ($items->count > 0) : ?>
    <ul class="uk-list uk-list-divider uk-margin-remove">
      <?php foreach ($items as $item) : ?>
        <li>
          <ul class="uk-subnav uk-subnav-divider uk-margin-remove-top uk-margin-remove-bottom uk-text-small uk-text-muted">
            <li><?= $item->date() ?></li>
            <li><?= $item->createdUser->name ?></li>
            <?php if ($user->isSuperuser()) : ?>
              <li>
                <a href="<?= $config->urls->admin ?>page/edit/?id=<?= $item->id ?>">
                  <i class="fa fa-edit"></i>
                </a>
              </li>
              <li>
                <button class="tm-reset-button uk-text-danger" onclick="adminHelper.trashPage(<?= $item->id ?>)">
                  <i class="fa fa-trash"></i>
                </button>
              </li>
            <?php endif; ?>
          </ul>
          <p class="uk-margin-small uk-text-small">
            <?= $item->text ?>
          </p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <p class="uk-margin-remove uk-text-muted">
      <?= __("No notes found") ?>
    </p>
  <?php endif; ?>

  <div class="uk-position-top-right uk-position-small">
    <?php
    $AdminHelper->render('markup/pagination-htmx-next-prev', [
      'items' => $items,
      'limit' => 3,
      'htmx_file' => __DIR__ . '/notes.php',
      'htmx_target' => '#notes',
      'vars' => [
        'parent_page' => $parent_page,
      ],
    ]);
    ?>
  </div>
</div>