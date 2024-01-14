<?php

/**
 * Modal Edit
 */

namespace ProcessWire;

/**
 * On front-end edit modal
 * Remove delete and settings tab from FieldsetPage
 */
if ($this->input->get->modal && $this->input->get->id) {

  $page = $this->pages->get($this->input->get->id);

  $this->addHookAfter('ProcessPageEdit::buildFormChildren', function ($event) {
    $wrapper = $event->return;
    $wrapper->collapsed = Inputfield::collapsedHidden;
    $process = $event->object;
    $process->removeTab('ProcessPageEditChildren');
  });

  if ($page->className() == "FieldsetPage" || $this->input->get->remove_tabs) {
    $this->addHookAfter('ProcessPageEdit::buildFormDelete', function ($event) {
      $wrapper = $event->return;
      $wrapper->collapsed = Inputfield::collapsedHidden;
      $process = $event->object;
      $process->removeTab('ProcessPageEditDelete');
    });

    $this->addHookAfter('ProcessPageEdit::buildForm', function ($event) {
      $form = $event->arguments(0);
      $form->attr('class', 'admin-helper-remove-tabs');
      $event->return = $form;
    });
  } else {

    if ($this->input->get->remove_delete_tab) {
      $this->addHookAfter('ProcessPageEdit::buildFormDelete', function ($event) {
        $wrapper = $event->return;
        $wrapper->collapsed = Inputfield::collapsedHidden;
        $process = $event->object;
        $process->removeTab('ProcessPageEditDelete');
      });
    }

    if ($this->input->get->remove_settings_tab) {
      $this->addHookAfter('ProcessPageEdit::buildForm', function ($event) {
        $form = $event->arguments(0);
        $form->attr('class', 'admin-helper-remove-tabs');
        $event->return = $form;
      });
    }
  }
}
