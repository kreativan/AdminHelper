<?php

/**
 *  ProcessModuleExample
 *
 *  @author Ivan Milincic <kreativan.dev@gmail.com>
 *  @copyright 2023 kraetivan.dev
 *  @link http://kraetivan.dev
 */

class ProcessModuleExample extends Process implements WirePageEditor {

  // for WirePageEditor
  public function getPage() {
    return $this->page;
  }

  public function init() {
    parent::init(); // always remember to call the parent init

  }

  public function ___execute() {
    $this->adminHelper->redirectHelper();
    $this->headline('Headline');
    $this->breadcrumb('./', 'Headline');
    return [
      "_this" => $this,
    ];
  }

  public function executeEdit() {
    return $this->adminHelper->executeEdit();
  }
}
