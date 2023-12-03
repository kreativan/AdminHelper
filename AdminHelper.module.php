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
      'summary' => 'Helper to handle custom processwire system and admin ui',
      'icon' => 'connectdevelop',
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

    /**
     * page-tree permission
     * If there is no page tree permission, add it
     */
    if (!$this->permissions->has('page-tree')) {
      $this->permissions->add('page-tree');
    }

    /** Include hooks from folder */
    $this->autoloadFolder(__DIR__ . '/hooks/');

    /** Include hooks from templates folder */
    if ($this->hooks) {
      $this->autoloadFolder($this->config->paths->templates . 'hooks/');
    }

    /**
     * admin helper global variable
     * @var $AdminHelper
     * @var $adminHelper
     */
    $this->wire("AdminHelper", $this, true);
    $this->wire("adminHelper", $this, true);
    $this->wire("helper", $this, true);

    /**
     * System page global variable
     * @var $system
     */
    $system_page = wire('pages')->get('template=system');
    if ($system_page != "") $this->wire("system", $system_page, true);

    /**
     * Api page global variable
     * @var $api
     */
    $api_page = wire('pages')->get('template=api');
    if ($api_page != "") $this->wire("api", $api_page, true);

    /**
     * HTMX page global variable
     * @var $htmx
     */
    $htmx_page = wire('pages')->get('template=htmx');
    if ($htmx_page != "") $this->wire("htmx", $htmx_page, true);

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

      /**
       * Load Assets
       * use $this->config->scripts->append() to add more scripts
       * use $suffix for cache busting
       */
      $suffix = $this->debug ? time() : '';
      $this->config->scripts->append($this->url() . "assets/js/drag-drop-sort.js");
      $this->config->scripts->append($this->url() . "assets/js/AdminHelper.js");
      if ($this->load_htmx) {
        $this->config->scripts->append($this->url() . "lib/htmx-1.9.7/htmx.min.js");
      }
      if ($this->load_chartjs == 1) {
        $this->config->scripts->append($this->url() . "lib/js/chart.js");
        $this->config->scripts->append($this->url() . "lib/js/charts-init.js{$suffix}");
      }

      /**
       * Watch for actions requests
       * include action file based on the action $_GET variable
       */
      $this->autoloadActions('admin_action', $this);

      /**
       * Watch and handle drag and drop request
       */
      if ($this->input->post->action == "drag_drop_sort") {
        $id = $this->sanitizer->int($this->input->post->drag_drop_page_id);
        $p = $this->pages->get($id);
        $next_id = $this->sanitizer->int($this->input->post->drag_drop_next_id);
        $next_page = (!empty($next_id)) ? $this->pages->get($next_id) : "";
        $this->dragDropSort($p, $next_page);
      }
    }
  }

  // ========================================================= 
  // Ready 
  // ========================================================= 

  public function ready() {

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
     * App mode
     */
    if ($this->app_mode) {

      // When users login redirect to it's admin dashboard
      if ($this->input->get->login == "1" && (!$this->hasPageTree() || $this->dashboard_redirect)) {
        $url = $this->dashboardURL();
        $this->session->redirect($url);
      }

      /**
       * Redirect to login in app mode
       */
      if ($this->forceLogin()) {
        $this->session->redirect($this->loginURL());
        exit();
      }
    }


    /** 
     * auto-include all functions from the templates/functions folder 
     */
    if ($this->functions) {
      $this->autoloadFolder($this->config->paths->templates . 'functions/');
    }

    /**
     * Include controllers based on a page template name
     * @example /site/templates/controllers/basic-page.php
     */
    if ($this->controllers) {
      $controller = $this->config->paths->templates . "controllers/{$this->page->template}.php";
      if (file_exists($controller)) {
        include_once($controller);
      }
    }

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
       * Watch for HTMX request 
       * @see HTMX::watch()
       */
      if ($this->load_htmx) {
        $this->htmx()->watch();
      }

      // assets suffix
      $suffix = $this->config->debug ? '?v=' . time() : "?v=" . $this->js_files_suffix;

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

  /** Get current language code */
  public function lang() {
    $lng = ($this->user->language && $this->user->language->name != "default") ? $this->user->language->name : setting('default_lang');
    return $lng;
  }

  /**
   * Include files based on a $_GET variable
   * from module /actions/ folder
   * To reference module in included file use $module instead of $this
   * @example $this->autoloadActions('my_module_action', 'MyModuleClassName');
   * @param string $GET - name of the $_GET variable
   */
  public function autoloadActions($GET, $module) {
    $action = $this->sanitizer->text($this->input->get->{$GET});
    $module = is_string($module) ? $this->modules->get($module) : $module;
    $file = $this->config->paths->siteModules . $module->className() . "/actions/{$action}.php";
    if (file_exists($file)) {
      $this->files->include($file, [
        'module' => $module,
        'action' => $action,
      ]);
    } else if ($action != "" && ($this->input->get->admin_helper_ajax || $this->input->post->admin_helper_ajax)) {
      $this->json_response([
        'status' => 'error',
        'notification' => "<i class='fa fa-exclamation-triangle uk-margin-small-right'></i> <b>$action</b> action file not found",
        'REQ' => $_REQUEST
      ]);
    } else if ($action != "") {
      $this->error("$action action file not found");
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

  // --------------------------------------------------------- 
  // ACL - Access Control - App Mode
  // --------------------------------------------------------- 

  // Check if current page is admin page
  public function isAdminPage() {
    if (strpos($_SERVER['REQUEST_URI'], $this->wire('config')->urls->admin) === 0) {
      return true;
    } else {
      return false;
    }
  }

  public function isLoginPage() {
    if ($this->input->urlSegment1 == "login") return true;
    // check if current url is login url
    if (strpos($_SERVER['REQUEST_URI'], $this->loginURL()) === 0) return true;
    return false;
  }

  public function dashboardURL() {
    $dashboard_url = !empty($this->dashboard_url) ? $this->dashboard_url : 'dashboard/';
    return $this->config->urls->admin . $dashboard_url;
  }

  public function loginURL() {
    $login = $this->pages->get("template=login");
    if ($login != "") return $login->url;
    return $this->pages->get('/')->url . "login/";
  }

  public function forceLogin() {
    // if force login is disabled, false
    if (!$this->force_login) return false;
    // if it is login page, false
    if ($this->isLoginPage()) return false;
    // if page template is public, false
    if ($this->page && in_array($this->page->template->id, $this->public_templates)) {
      return false;
    }
    // when user is not loggedin
    if (!$this->user->isLoggedin()) return true;
    // When logging out from admin
    if ($this->input->get->loggedout == "1") return true;
    return false;
  }

  public function hasPageTree() {

    // If app_mode is disabled, return true
    if (!$this->app_mode) return true;

    // Everyone
    if ($this->page_tree_access == "1") return true;

    // Superuser Admin
    if ($this->page_tree_access == "2") {
      return $this->user->name == "admin" ? true : false;
    }

    // All Superusers
    if ($this->page_tree_access == "3") {
      return $this->user->isSuperuser() ? true :  false;
    }

    // Page Tree Permission
    if ($this->page_tree_access == "4") {
      return $this->user->hasPermission('page-tree') ? true : false;
    }

    return true;
  }

  // --------------------------------------------------------- 
  // JSON 
  // --------------------------------------------------------- 

  /**
   * Encode json
   * ready to be used in HTML attributes
   * @param array $data
   */
  public function json_encode($data) {
    return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
  }

  /**
   * Set JSON header
   * and echo json data
   */
  public function jsonResponse($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit();
  }

  /**
   * Set JSON header
   * and echo json data
   * same as jsonResponse()
   */
  public function json_response($response) {
    header('Content-type: application/json');
    echo json_encode($response);
    exit();
  }

  /**
   * Get json file from the lib and decode it
   * @param string $file_name
   * @return array
   */
  public function get_json_lib($file_name) {
    $file = $this->lib_path() . "json/$file_name.json";
    if (file_exists($file)) {
      $json = file_get_contents($file);
      return json_decode($json, true);
    }
    return [];
  }

  // ========================================================= 
  // Classes 
  // ========================================================= 

  public function htmx() {
    require_once(__DIR__ . '/classes/HTMX.php');
    $obj = new \AdminHelper\HTMX();
    return $obj;
  }

  public function Utility() {
    require_once(__DIR__ . '/classes/Utility.php');
    $obj = new \AdminHelper\Utility();
    return $obj;
  }

  public function Markup() {
    require_once(__DIR__ . '/classes/Markup.php');
    $obj = new \AdminHelper\Markup();
    return $obj;
  }

  public function Request() {
    require_once(__DIR__ . '/classes/Request.php');
    $obj = new \AdminHelper\Request();
    return $obj;
  }

  public function Valitron() {
    require_once(__DIR__ . '/classes/Valitron.php');
    $obj = new \AdminHelper\Valitron();
    return $obj;
  }

  public function Auth() {
    require_once(__DIR__ . '/classes/Auth.php');
    $obj = new \AdminHelper\Auth();
    return $obj;
  }

  public function CSV() {
    require_once(__DIR__ . '/classes/CSV.php');
    $obj = new \AdminHelper\CSV();
    return $obj;
  }

  public function Fields() {
    require_once(__DIR__ . '/classes/Fields.php');
    $obj = new \AdminHelper\Fields();
    return $obj;
  }

  public function ApiEndPoint() {
    require_once(__DIR__ . '/classes/ApiEndPoint.php');
    $obj = new \AdminHelper\ApiEndPoint();
    return $obj;
  }

  public function HtmxEndPoint() {
    require_once(__DIR__ . '/classes/HtmxEndPoint.php');
    $obj = new \AdminHelper\HtmxEndPoint();
    return $obj;
  }

  public function Emails() {
    require_once(__DIR__ . '/classes/Emails.php');
    $obj = new \AdminHelper\Emails();
    return $obj;
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
  //  Admin Actions & UI
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

  public function render($file_path, $vars = []) {
    $file_path = str_replace(".php", "", $file_path);
    $file = $this->path() . $file_path . ".php";
    $this->files->include($file, $vars);
  }

  public function renderMarkup($file_path, $vars = []) {
    $file_path = str_replace(".php", "", $file_path);
    $file = $this->path() . "markup/" . $file_path . ".php";
    $file = $this->config->paths->templates . $file;
    $this->files->include($file, $vars);
  }

  /**
   * Run uikit notification
   * @param array $params
   * @example $AdminHelper->notification($params);
   * @example $AdminHelper->notification(['message' => "Success", 'status' => "success"]);
   */
  public function notification($params) {
    $icon = !empty($params['icon']) ? $params['icon'] : '';
    $message = !empty($params['message']) ? $params['message'] : '';
    $status = !empty($params['status']) ? $params['status'] : 'primary';
    $pos = !empty($params['pos']) ? $params['pos'] : 'top-center';
    $timeout = !empty($params['timeout']) ? $params['timeout'] : 3000;
    $html = "
      <script>
        UIkit.notification({
          message: '<i class=\"fa fa-{$icon} fa-lg uk-margin-small-right\"></i> {$message}',
          status: '{$status}',
          pos: '{$pos}',
          timeout: '{$timeout}'
        });
      </script>
    ";
    $this->adminTheme->addExtraMarkup("content", $html);
  }

  /**
   * Send email
   * @param array $params
   * @param string $params['to'] - email address to send to (required)
   * @param string $params['from'] - email address to send from (required)
   * @param string $params['fromName'] - email name to send from (optional)
   * @param string $params['replyTo'] - email address to reply to (optional)
   * @param string $params['subject'] - email subject (required)
   * @param string $params['body'] - email body (required)
   * @param string $params['attachment'] - path to attachment file (optional)
   * @param array $params['attachments'] - array of paths to attachment files (optional)
   * @param string $params['email_template'] - path to email template file, will be used instead of body (optional)
   * @param string $params['email_template_page'] - page id to get email body from (optional)
   * @param array $params['data'] - data array to replace {text} in a provided string (optional)
   * @param int $params['related_page'] - page id to get page fields (optional)
   * @return void
   */
  public function send_email($params) {
    $this->Emails()->sendEmail($params);
  }
}
