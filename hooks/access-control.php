<?php

/**
 * Hooks: access-control.php
 */

namespace ProcessWire;

/**
 * Hide pages from page tree
 * based on AdminHelper config
 */
$this->addHookAfter('ProcessPageList::execute',  function (HookEvent $event) {

  if ($this->user->isSuperuser() && $this->hide_for == "2") return;

  // get system pages
  $sysPagesArr = $this->sys_pages;

  // additional pages to hide by ID
  $customArr = [];
  if ($this->hide_system_pages == "1") {
    $customArr[] = "2"; // admin
    $customArr[] = $this->pages->get("template=system");
  }

  if ($this->config->ajax) {

    // manipulate the json returned and remove any pages found from array
    $json = json_decode($event->return, true);
    if ($json && isset($json['children'])) {
      foreach ($json['children'] as $key => $child) {
        $c = $this->pages->get($child['id']);
        $pagetemplate = $c->template;
        if (in_array($pagetemplate, $sysPagesArr) || in_array($c, $customArr)) {
          unset($json['children'][$key]);
        }
      }
      $json['children'] = array_values($json['children']);
      $event->return = json_encode($json);
    }
  }
});
