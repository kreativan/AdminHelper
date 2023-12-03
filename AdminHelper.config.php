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
    $page_tree_set = $this->wire('modules')->get("InputfieldFieldset");
    $page_tree_set->label = "Page Tree";
    $wrapper->add($page_tree_set);

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
    $page_tree_set->add($f);

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
    $page_tree_set->add($f);

    // sys_pages
    $f = $this->wire('modules')->get("InputfieldAsmSelect");
    $f->attr('name', 'sys_pages');
    $f->label = 'System Pages';
    $f->description = __("Additional pages that will be hidden from page tree");
    $f->options = $templatesArr;
    $page_tree_set->add($f);

    $inputfields->add($page_tree_set);

    //  Assets
    // ===========================================================
    $assets_set = $this->wire('modules')->get("InputfieldFieldset");
    $assets_set->label = "Assets";
    $wrapper->add($assets_set);

    // Load HTMX
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'load_htmx');
    $f->label = 'Load HTMX';
    $f->options = [1 => "Yes", 0 => "No"];
    $f->required = true;
    $f->defaultValue = 1;
    $f->optionColumns = 1;
    $f->columnWidth = "50%";
    $f->description = __("Load HTMX library in admin");
    $assets_set->add($f);

    // load_chartjs
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'load_chartjs');
    $f->label = 'Load Chart.js';
    $f->options = [1 => "Yes", 2 => "No"];
    $f->required = true;
    $f->defaultValue = 2;
    $f->optionColumns = 1;
    $f->columnWidth = "50%";
    $f->description = __("Load chart.js library in admin");
    $assets_set->add($f);

    $inputfields->add($assets_set);

    //  App Mode
    // ===========================================================

    $app_mode_set = $this->wire('modules')->get("InputfieldFieldset");
    $app_mode_set->label = "App Mode";
    $wrapper->add($app_mode_set);

    // app_mode
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'app_mode');
    $f->label = 'Enable App Mode';
    $f->options = [1 => "Yes", 0 => "No"];
    $f->required = true;
    $f->defaultValue = 0;
    $f->optionColumns = 1;
    $f->columnWidth = "50%";
    $f->description = __("When the App mode is enabled, front-end is restricted for non-logged-in users. Users without page-tree permission will not have access to the page tree, and will be redirected to the admin dashboard.");
    $app_mode_set->add($f);

    // force_login
    $f = $this->wire('modules')->get("InputfieldCheckbox");
    $f->attr('name', 'force_login');
    $f->label = 'Force Login';
    $f->checkboxLabel = 'Yes / No';
    $f->value = 1;
    $f->description = __("Force, redirect users to login page when not logged-in. Relative url `login/` or page with login template.");
    $f->showIf = "app_mode=1";
    $f->columnWidth = "50%";
    $app_mode_set->add($f);

    // public_templates
    $templates_array = [];
    foreach (wire('templates') as $t) $templates_array[$t->id] = $t->name;
    $f = $this->wire('modules')->get("InputfieldAsmSelect");
    $f->attr('name', 'public_templates');
    $f->label = 'Public Templates';
    $f->options = $templates_array;
    $f->description = __("Selected template with be public and will not require login.");
    $f->showIf = "app_mode=1, force_login=1";
    $f->columnWidth = "100%";
    $app_mode_set->add($f);

    // dashboard_redirect
    $f = $this->wire('modules')->get("InputfieldCheckbox");
    $f->attr('name', 'dashboard_redirect');
    $f->label = 'Redirect to dashboard';
    $f->checkboxLabel = 'Yes / No';
    $f->value = 1;
    $f->description = __("Redirect users in admin to the dashboard");
    $f->showIf = "app_mode=1";
    $f->columnWidth = "50%";
    $app_mode_set->add($f);

    // dashboard_url
    $f = $this->wire('modules')->get("InputfieldText");
    $f->attr('name', 'dashboard_url');
    $f->label = 'Dashboard URL';
    $f->description = __("Relative admin dashboard url `dashboard/`");
    $f->showIf = "app_mode=1";
    $f->placeholder = "dashboard/";
    $f->columnWidth = "50%";
    $app_mode_set->add($f);

    // custom_admin_menu
    $f = $this->wire('modules')->get("InputfieldCheckbox");
    $f->attr('name', 'custom_admin_menu');
    $f->label = 'Custom Admin Menu';
    $f->checkboxLabel = 'Yes / No';
    $f->value = 1;
    $f->description = __("Add system menu items to the user dropdown menu, for superusers.");
    $f->showIf = "app_mode=1";
    $app_mode_set->add($f);

    $inputfields->add($app_mode_set);

    //  System
    // ===========================================================
    $system_set = $this->wire('modules')->get("InputfieldFieldset");
    $system_set->label = "System";
    $wrapper->add($system_set);

    // hooks
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'hooks');
    $f->label = 'Hooks';
    $f->options = [1 => "Yes", 0 => "No"];
    $f->required = true;
    $f->defaultValue = 1;
    $f->optionColumns = 1;
    $f->columnWidth = "33%";
    $f->description = __("Auto-include hooks from the `/templates/hooks/` folder");
    $system_set->add($f);

    // functions
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'functions');
    $f->label = 'Functions';
    $f->options = [1 => "Yes", 0 => "No"];
    $f->required = true;
    $f->defaultValue = 1;
    $f->optionColumns = 1;
    $f->columnWidth = "33%";
    $f->description = __("Auto-include functions from the `/templates/functions/` folder");
    $system_set->add($f);

    // Controllers
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'controllers');
    $f->label = 'Controllers';
    $f->options = [1 => "Yes", 0 => "No"];
    $f->required = true;
    $f->defaultValue = 1;
    $f->optionColumns = 1;
    $f->columnWidth = "33%";
    $f->description = __("Auto-include controllers absed on a page tempalte name from the `/templates/controllers/` folder");
    $system_set->add($f);

    // sitemap_route
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'sitemap_route');
    $f->label = 'Sitemap Route';
    $f->options = [1 => "Yes", 2 => "No"];
    $f->required = true;
    $f->defaultValue = 1;
    $f->optionColumns = 1;
    $f->columnWidth = "33%";
    $f->description = __("Enable `/sitemap.xml` url hook");
    $system_set->add($f);

    // cron_route
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'cron_route');
    $f->label = 'Cron Route';
    $f->options = [1 => "Yes", 2 => "No"];
    $f->required = true;
    $f->defaultValue = 2;
    $f->optionColumns = 1;
    $f->columnWidth = "33%";
    $f->description = __("Enable `/cron/{file_name}/` url hook");
    $system_set->add($f);

    // json_route
    $f = $this->wire('modules')->get("InputfieldRadios");
    $f->attr('name', 'json_route');
    $f->label = 'Page JSON Route';
    $f->options = [1 => "Yes", 2 => "No"];
    $f->required = true;
    $f->defaultValue = 2;
    $f->optionColumns = 1;
    $f->columnWidth = "33%";
    $f->description = __('Enable `(/.*)/json/` url hook. 
    Get json response for any page by url eg: `/basic-page/json/`.    
    Page needs to have `$page->json()` defined in its PageClass');
    $system_set->add($f);

    $inputfields->add($system_set);

    // render fields
    return $inputfields;
  }
}
