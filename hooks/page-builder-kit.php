<?php

namespace ProcessWire;

if ($this->modules->isInstalled("PageBuilderKit")) {

  /**
   * When delete page, delete related page builder page
   */
  $this->addHookBefore('Pages::trash', function (HookEvent $event) {
    $page = $event->arguments[0];
    $pb = $this->pages->get("/system/page-builder/{$page->id}/");
    if ($pb->id && !empty($pb->id) && $pb->id != "") $pb->trash();
  });

  /**
   * When remove page_builder field from the template, 
   * delete all related page-builder pages
   */
  $this->addHookBefore('Fieldtype::deleteTemplateField', function (HookEvent $event) {
    $template = $event->arguments(0);
    $field = $event->arguments(1);

    if ($field->name == "page_builder") {
      $pb_pages = $this->pages->find("template=$template->name, include=all, status!=trash");
      $this->warning("Deleted all related page builder pages.");
      if ($pb_pages->count) {
        foreach ($pb_pages as $pb_page) {
          $p = $this->pages->get("/system/page-builder/{$pb_page->id}/");
          if ($p != "") $p->trash();
        }
      }
    }
  });

  // end
}
