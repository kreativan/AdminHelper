<?php

/**
 * Modal Edit
 */

namespace ProcessWire;

/**
 * On front-end edit modal
 * Remove delete and settings tab from FieldsetPage
 */
if ($this->input->get->modal) {

  $class_name = $this->pages->get($this->input->get->id)->className();

  if ($class_name == "FieldsetPage" || $this->input->get->remove_tabs) {
    $this->addHookAfter('ProcessPageEdit::buildFormDelete', function ($event) {
      $wrapper = $event->return;
      $wrapper->collapsed = Inputfield::collapsedHidden;
      $process = $event->object;
      $process->removeTab('ProcessPageEditDelete');
    });

    $this->addHookAfter('ProcessPageEdit::buildFormSettings', function ($event) {
      $wrapper = $event->return;
      $wrapper->collapsed = Inputfield::collapsedHidden;
      $process = $event->object;
      $process->removeTab('ProcessPageEditSettings');
    });
  }
}