<?php

/**
 *  Admin Helper Config
 *  @author Ivan Milincic <kreativan.dev@gmail.com>
 *  @link http://www.kraetivan.dev
 */

class AdminHelperConfig extends ModuleConfig {

  public function getInputfields() {
    $inputfields = parent::getInputfields();

    // create templates options array
    $templatesArr = array();
    foreach ($this->templates as $tmp) {
      $templatesArr["$tmp"] = $tmp->name;
    }

    $wrapper = new InputfieldWrapper();

    //  Options
    // ===========================================================

    $options_set = $this->wire('modules')->get("InputfieldFieldset");
    $options_set->label = "Options";
    $wrapper->add($options_set);

    // hide_system_pages
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'hide_system_pages');
    $f->label = 'Hide system pages from page tree';
    $f->options = array(
      '1' => "Yes",
      '2' => "No"
    );
    $f->required = true;
    $f->defaultValue = "2";
    $f->optionColumns = 1;
    $f->columnWidth = "50%";
    $options_set->add($f);

    // hide for
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'hide_for');
    $f->label = 'Hide pages for';
    $f->options = array(
      '1' => "All",
      '2' => "Non-Superusers"
    );
    $f->required = true;
    $f->defaultValue = "2";
    $f->optionColumns = 1;
    $f->columnWidth = "50%";
    $options_set->add($f);

    // sys_pages
    $f = $this->wire('modules')->get("InputfieldAsmSelect");
    $f->attr('name', 'sys_pages');
    $f->label = 'System Pages';
    $f->description = __("Additional pages that will be hidden from page tree");
    $f->options = $templatesArr;
    $options_set->add($f);

    $inputfields->add($options_set);

    // Load HTMX
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'load_htmx');
    $f->label = 'Load HTMX';
    $f->options = [1 => "Yes", 0 => "No"];
    $f->required = true;
    $f->defaultValue = 1;
    $f->optionColumns = 1;
    $f->columnWidth = "50%";
    $inputfields->add($f);


    $f = $this->wire('modules')->get("InputfieldTextarea");
    $f->attr('name', 'js_files');
    $f->label = 'JavaScript Files';
    $f->description = __("Load JavaScript files in admin. One file per line.");
    $f->rows = 5;
    $inputfields->add($f);

    // render fields
    return $inputfields;
  }
}
