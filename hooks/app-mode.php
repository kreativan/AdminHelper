<?php

/** 
 * Hooks: app-mode 
 * 
 */

namespace ProcessWire;

if ($this->app_mode || $this->app_mode == "1") {

  /**
   * Don't let admin user name to change
   */
  $this->addHookBefore("Pages::saveReady(template=user)", function ($event) {
    $user = $event->arguments(0);
    if ($user->id == 41 && $user->name != "admin") {
      $this->wireApp->error('User <b>admin</b> can not be changed.', Notice::allowMarkup);
      $user->name = "admin";
    }
  });

  // If user has no page-tree permission, redirect to dashboard
  // on ProcessHome "/admin/" url
  $this->addHookBefore('ProcessHome::execute', function (HookEvent $event) {
    if (!$this->hasPageTree() || $this->dashboard_redirect) {
      $url = $this->dashboardURL();
      $this->session->redirect($url);
    }
  });

  // If user has no page-tree permission, redirect to dashboard
  // on ProcessPageList "/admin/pages/" url
  // not on page edit...
  $this->addHookBefore('ProcessPageList::execute', function (HookEvent $event) {
    if (!$this->hasPageTree() && !$this->input->get->modal && !$this->input->get->id) {
      $url = $this->dashboardURL();
      $this->session->redirect($url);
    }
  });

  // Restrict / remove Page Tree for users without page-tree permission
  // If page-tree permission does not exists, create it manually
  $this->addHookAfter('Page::viewable', function (HookEvent $event) {
    $page = $event->object;
    $user = $this->user;
    if ($page->id == 3 && !$this->hasPageTree()) {
      $event->return = false;
    }
  });

  /**
   * Admin Theme Framework: User Nav Array
   * - Add custom links to admin user nav (only for admin user)
   */
  $this->addHookAfter('AdminThemeFramework::getUserNavArray', function (HookEvent $event) {

    if (!$this->user->isSuperuser()) return;
    if (!$this->hasPageTree()) return;
    if (!$this->custom_admin_menu) return;

    $items = $event->return;

    $page_tree = $this->pages->get(3);
    $setup = $this->pages->get(22);
    $modules = $this->pages->get(21);
    $access = $this->pages->get(28);

    $new = [];

    $new[] = [
      "url" => $page_tree->url,
      "title" => $page_tree->title,
      "icon" => $page_tree->getIcon(),
    ];

    $new[] = [
      "url" => $setup->url,
      "title" => $setup->title,
      "icon" => 'cog',
    ];

    $new[] = [
      "url" => $modules->url,
      "title" => $modules->title,
      "icon" => 'plug',
    ];

    $new[] = [
      "url" => $access->url,
      "title" => $access->title,
      "icon" => 'user-circle',
    ];

    $new = array_merge($new, $items);

    $event->return = $new;
  });
}
