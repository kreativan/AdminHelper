<?php

namespace AdminHelper;

use ProcessWire\WireData;

class Markup extends WireData {

  public function __construct() {
  }

  /**
   * Render files from /markup/ folder
   */
  public function render($file_name, $vars = []) {
    $file_name = str_replace(".php", "", $file_name);
    $file = $this->AdminHelper->path() . "markup/{$file_name}.php";
    $this->files->include($file, $vars);
  }

  // ========================================================= 
  // Admin Table
  // ========================================================= 

  /**
   * Render Admin Table
   * powered by htmx
   * @param array $params
   * @example $AdminHelper->adminTableHtmx($params);
   */
  public function adminTable($params = []) {
    $this->files->include(__DIR__ . "/../markup/admin-table.php", $params);
  }

  public function adminTableHtmx($params = []) {
    $this->adminTable($params);
  }

  /**
   * Render Admin Tabs
   * @param array $tabs - ['tab_name_1' => [], 'tab_name_2' => []]
   * @param string $active_var - $_GET variable to set active tab
   * 
   * @example $AdminHelper->adminTabs($tabs, 'taxonomy');
   * In this example tab will be active if ($input->get->taxonomy == 'tab_name')
   * 
   * Tabs array items:
   * @var string $tab['title']
   * @var string $tab['url'] - "./?taxonomy=tab_name"
   * @var string $tab['icon']
   * @var bool $tab['visible']
   */
  public function adminTabs($tabs, $active_var = "tab") {
    $this->files->include(__DIR__ . "/../markup/admin-tabs.php", ['tabs' => $tabs, 'active_var' => $active_var]);
  }

  public function adminTableNewButton($vars = []) {
    $this->files->include(__DIR__ . "/../markup/admin-table-new-button.php", $vars);
  }

  // ========================================================= 
  // Dashboard
  // ========================================================= 

  /**
   * Load ChartJS
   */
  public function loadChartJS() {
    $suffix = $this->debug ? time() : '';
    $this->config->scripts->append($this->adminHelper->url() . "lib/js/chart.js");
    $this->config->scripts->append($this->adminHelper->url() . "lib/js/charts-init.js{$suffix}");
  }

  /**
   * Render file
   * @param string $file_path - file name from /markup-dashbaord/
   * @param array $vars
   */
  public function renderDashboard($file_path, $vars = []) {
    $path = $this->AdminHelper->path() . "markup-dashbaord/"  . $file_path;
    $this->files->include($path, $vars);
  }

  /**
   * Render card
   * @param string $file_path
   * @param array $vars
   * @see /markup-dashbaord/_card.php
   * $vars['data'] - data that will be passed to the file
   * $vars['vars'] - vars that will be passed to the file
   * $vars['chart'] - chart data
   */
  function card($file, $vars = []) {
    $file_path = $this->AdminHelper->path() . "markup-dashbaord/"  . $file;
    $card = $this->wireApp->path() . "markup-dashbaord/_card.php";
    if ($file != 'custom') $vars['file'] = $file_path;
    $vars['data'] = !empty($vars['data']) ? $vars['data'] : [];
    if (!empty($vars['chart'])) $vars['data']['chart'] = $vars['chart'];
    if (!empty($vars['vars'])) {
      $vars['data'] = array_merge($vars['data'], $vars['vars']);
    }
    $this->files->include($card, $vars);
  }
}
