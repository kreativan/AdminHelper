<?php

/**
 *  Admin Helper
 *  @author Ivan Milincic <kreativan.dev@gmail.com>
 *  @link https://www.kraetivan.dev
 */

class AdminHelper extends WireData implements Module {

  public static function getModuleInfo() {
    return array(
      'title' => 'Admin Helper',
      'version' => 100,
      'summary' => 'Helper to handle custom processwire admin ui',
      'icon' => 'code-fork',
      'author' => "Ivan Milincic",
      "href" => "https://kreativan.dev",
      'singular' => true,
      'autoload' => true
    );
  }

  public function __construct() {
    // ...
  }

  // ========================================================= 
  // Init 
  // ========================================================= 

  public function init() {

    /** Include hooks from folder */
    $this->autoloadFolder(__DIR__ . '/hooks/');

    // assets suffix
    $suffix = $this->config->debug ? '?v=' . time() : "?v=" . $this->js_files_suffix;

    /**
     * admin helper global variable
     * @var $AdminHelper
     * @var $adminHelper
     */
    $this->wire("AdminHelper", $this, true);
    $this->wire("adminHelper", $this, true);
    $this->wire("helper", $this, true);

    /**
     * system page global variable
     * @var $system
     */
    $system_page = wire('pages')->get('template=system');
    if ($system_page != "") $this->wire("system", $system_page, true);

    /**
     * Runs only in Admin
     */
    if ($this->isAdminPage()) {

      /**
       * AdminHelper JS Config
       * @example console.log(ProcessWire.config.AdminHelper);
       */
      $this->config->js('AdminHelper', [
        'debug' => $this->config->debug,
      ]);

      /**
       * run hide pages hook
       * hide pages from page tree
       */
      $this->addHookAfter('ProcessPageList::execute', $this, 'hidePages');

      // Always set system page to the bottom of the page tree
      $system_page = $this->pages->get("template=system");
      if ($system_page != "" && $system_page->sort < 49) {
        $system_page->setAndSave("sort", 49);
      }

      // Always set search page to the bottom of the page tree
      $search_page = $this->pages->get("template=search");
      if ($search_page != "" && $search_page->sort < 48) {
        $search_page->setAndSave("sort", 48);
      }

      // load assets
      $this->config->scripts->append($this->url() . "assets/js/drag-drop-sort.js{$suffix}");
      $this->config->scripts->append($this->url() . "assets/js/AdminHelper.js{$suffix}");
      if ($this->load_htmx) {
        $this->config->scripts->append($this->url() . "assets/js/htmx.js");
      }

      // Load js files specified in module settings
      if (!empty($this->js_files)) {
        $js_files = explode("\n", $this->js_files);
        foreach ($js_files as $js) {
          $this->config->scripts->append($js . $suffix);
        }
      }

      /**
       * Watch and handle drag and drop request
       */
      if ($this->input->post->action == "drag_drop_sort") {
        $id = $this->sanitizer->int($this->input->post->id);
        $p = $this->pages->get($id);
        $next_id = $this->sanitizer->int($this->input->post->next_id);
        $next_page = (!empty($next_id)) ? $this->pages->get($next_id) : "";
        $this->dragDropSort($p, $next_page);
      }
    }
  }

  // ========================================================= 
  // Ready 
  // ========================================================= 

  public function ready() {

    // assets suffix
    $suffix = $this->config->debug ? '?v=' . time() : "?v=" . $this->js_files_suffix;

    /**
     * AdminTheme Config
     */
    $this->config->AdminThemeUikit = [
      'style' => 'reno',
      'compress' => $this->debug ? false : true,
      'recompile' => false,
      'customLessFiles' => [
        $this->path() . "assets/less/admin-vars.less",
        $this->path() . "assets/less/admin-utility.less",
        $this->path() . "assets/less/admin.less",
        $this->config->paths->templates . "admin.less",
      ],
    ];

    /**
     * Add custom page events
     * @method onPageEdit - runs on page edit screen
     * @method onPageVisit - runs on page visit - on front-end
     */
    if ($this->isAdminPage()) {
      if (($this->page->name === "edit" || $this->page->urlSegment === "edit") && $this->input->get->id) {
        $p = $this->pages->get($this->input->get->id);
        if (method_exists($p, 'onPageEdit')) $p->onPageEdit();
      }
    } else {
      // Run page init method if exists on page visit
      if (method_exists($this->page, 'onPageVisit')) $this->page->onPageVisit();
    }

    /**
     * Runs only in admin
     * Stuff inside will run only in admin area
     */
    if ($this->isAdminPage()) {

      /**
       * Watch for actions requests
       * include action file based on the action $_GET variable
       */
      $this->autoloadActions('action', $this);

      /**
       * Watch for HTMX request 
       * @see HTMX::watch()
       */
      if ($this->load_htmx) {
        $this->htmx()->watch();
      }

      // Load assets for pageEditModal
      if ($this->input->get->modal && $this->page->name == "edit" && $this->input->get->context != 'PageTable') {
        $this->config->tracyDisabled = true;
        $this->config->styles->append($this->url() . "assets/css/page-edit-modal.css{$suffix}");
        $this->config->scripts->append($this->url() . "assets/js/page-edit-modal.js{$suffix}");
      } elseif ($this->input->get->modal && $this->page->name == "edit" && $this->input->get->context == 'PageTable') {
        $this->config->tracyDisabled = true;
        $this->config->styles->append($this->url() . "assets/css/page-edit-modal-page-table.css{$suffix}");
      }
    }
  }

  // ========================================================= 
  // Helpers 
  // ========================================================= 

  public function path() {
    return $this->config->paths->siteModules . $this->className() . "/";
  }

  public function url() {
    return $this->config->urls->siteModules . $this->className() . "/";
  }

  // Check if current page is admin page
  public function isAdminPage() {
    if (strpos($_SERVER['REQUEST_URI'], $this->wire('config')->urls->admin) === 0) {
      return true;
    } else {
      return false;
    }
  }

  /** Get current language code */
  public function lang() {
    $lng = ($this->user->language && $this->user->language->name != "default") ? $this->user->language->name : setting('default_lang');
    return $lng;
  }

  /**
   * Encode json
   * ready to be used in HTML attributes
   * @param array $data
   */
  public function json_encode($data) {
    return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
  }

  /**
   * Include files based on a $_GET variable
   * from module /actions/ folder
   * To reference module in included file use $module instead of $this
   * @param string $GET - name of the $_GET variable
   */
  public function autoloadActions($GET, $module) {
    $action = $this->sanitizer->text($this->input->get->{$GET});
    $file = $this->config->paths->siteModules . $module->className() . "/actions/{$action}.php";
    if (file_exists($file)) {
      include_once($file);
    }
  }

  /**
   * Include all files from a specified folder
   * @param string $folder - folder name
   */
  public function autoloadFolder($folder) {
    $path = str_replace('//', '/', $folder);
    $files = glob($path . "[!_]*.php");
    foreach ($files as $file) include($file);
  }

  // ========================================================= 
  // Classes 
  // ========================================================= 

  public function utility() {
    require_once(__DIR__ . '/classes/Utility.php');
    $utility = new AdminHelper_Utility();
    return $utility;
  }

  public function htmx() {
    require_once(__DIR__ . '/classes/HTMX.php');
    $htmx = new AdminHelper_HTMX();
    return $htmx;
  }

  // ========================================================= 
  // Admin UI - Process Modules
  // ========================================================= 

  /**
   *  Page Edit Link
   *  Use this method to generate page edit link.
   *  @param integer $id  Page ID
   *  @example href='{$this->pageEditLink($item->id)}';
   */
  public function pageEditLink($id, $back_url = "") {
    if ($this->input->get->htmx) return "./edit/?id=$id&back_url={$back_url}";
    $currentURL = $_SERVER['REQUEST_URI'];
    $url_segment = explode('/', $currentURL);
    $url_segment = $url_segment[sizeof($url_segment) - 1];
    // encode & to ~
    $url_segment = str_replace("&", "~", $url_segment);
    $segment1 = $this->input->urlSegment1 ? $this->input->urlSegment1 . "/" : "";
    if ($back_url != "") return $this->page->url . "edit/?id=$id&back_url={$back_url}";
    return $this->page->url . "edit/?id=$id&back_url={$segment1}{$url_segment}";
  }

  /**
   * Create new page link
   * @example $adminHelper->newPageLink($parent_id, $template_id, $back_url = '')
   */
  public function newPageLink($parent_id, $template_id, $back_url = '') {
    return $this->config->urls->admin . "page/add/?parent_id=$parent_id&template_id=$template_id&back_url={$back_url}";
  }

  /**
   *  Redirect helper
   *  this should always redirect us where we left off after page save,
   *  back to paginated page, or witg get variables... based on back_url
   *  Run this in a process ___execute() method
   *  @example 
   *  add this at the begining of the ___execute() method:
   *  $this->modules->get('AdminHelper')->redirectHelper();
   *
   */
  public function redirectHelper() {
    $back_url = $this->session->get("back_url");
    if (!$this->input->get->id) {
      if (!empty($back_url)) {
        // decode back_url:  ~ to &  - see @method pageEditLink()
        $this->session->remove("back_url");
        $back_url = str_replace("~", "&", $back_url);
        $goto = $this->page->url . $back_url;
        $this->session->redirect($goto);
      }
    }
  }

  /**
   *  Admin Page Edit
   *  @example
   *  Only this is needed:
   *  public function executeEdit() {
   *    return $this->modules->get('AdminHelper')->adminPageEdit();
   *  }
   */
  public function adminPageEdit() {

    /**
     *  Set @var back_url session var
     *  So we can redirect back where we left
     */
    if ($this->input->get->back_url) {
      // decode back_url:  ~ to &  - see @method pageEditLink()
      $back_url_decoded = str_replace("~", "&", $this->input->get->back_url);
      $this->session->set("back_url", $back_url_decoded);
    }

    /**
     *  Set the breadcrumbs
     *  add $_SESSION["back_url"] to the breacrumb link
     */
    $this->fuel->breadcrumbs->add(new Breadcrumb($this->page->url . $this->session->get("back_url"), $this->page->title));

    // Execute Page Edit
    $processEdit = $this->modules->get('ProcessPageEdit');
    return $processEdit->execute();
  }

  //-------------------------------------------------------- 
  //  Admin Actions
  //-------------------------------------------------------- 

  /**
   *  Drag & Drop Sort
   *  @param Page $p
   *  @param Page $next_page
   */
  public function dragDropSort($p, $next_page) {
    // if no next move to the end
    if (empty($next_page) || $next_page == "") {
      $lastSibling = $p->siblings('include=all, status!=trash')->last();
      $this->pages->insertAfter($p, $lastSibling);
    } else {
      $this->pages->insertBefore($p, $next_page);
    }
  }

  /**
   *  Intercept page tree json and remove page from it
   *  We will remove page by its template
   */
  public function hidePages(HookEvent $event) {

    if ($this->user->isSuperuser() && $this->hide_for == "2") return;

    // get system pages
    $sysPagesArr = $this->sys_pages;

    // aditional pages to hide by ID
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
  }

  // ========================================================= 
  // Admin UI - Admin Table
  // ========================================================= 

  /**
   * Render Admin Table
   * @param array $params
   * @param $params['selector'] - selector string to find the pages to display eg: "templat=my-template"
   * @param $params['table_fields'] - array of fields to display in the table eg: ["Template" => "template.name", "ID" => "id"]
   * @param $params['table_actions'] - show table actions
   * @example $AdminHelper->adminTable($params);
   */
  public function adminTable($params = []) {
    $selector = $params['selector'] ?? "";
    $table_fields = $params['table_fields'] ?? [];
    $table_actions = $params['table_actions'] ?? true;
    $this->files->include(__DIR__ . "/tmpl/admin-table.php", [
      "selector" => $selector,
      "table_fields" => $table_fields,
      "table_actions" => $table_actions,
    ]);
  }

  /**
   * Render Admin Table
   * powered by htmx
   * @param array $params
   * @param $params['selector'] - selector string to find the pages to display eg: "templat=my-template"
   * @param $params['table_fields'] - array of fields to display in the table eg: ["Template" => "template.name", "ID" => "id"]
   * @param $params['close_modal'] - close modal after page edit
   * @param $params['table_actions'] - show table actions
   * @example $AdminHelper->adminTableHtmx($params);
   */
  public function adminTableHtmx($params = []) {
    $selector = $params['selector'] ?? "";
    $table_fields = $params['table_fields'] ?? [];
    $close_modal = $params['close_modal'] ?? "false";
    $table_actions = $params['table_actions'] ?? true;
    $this->files->include(__DIR__ . "/tmpl/admin-table-htmx.php", [
      "selector" => $selector,
      "table_fields" => $table_fields,
      "close_modal" => $close_modal,
      "table_actions" => $table_actions,
    ]);
  }
}
