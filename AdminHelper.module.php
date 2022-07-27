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
      'icon' => 'codepen',
      'author' => "Ivan Milincic",
      "href" => "https://kreativan.dev",
      'singular' => true,
      'autoload' => true
    );
  }

  public function init() {

    if($this->isAdminPage()) {

      // console.log(ProcessWire.config.crm);
      $this->config->js('crm', [
        'GET' => $_GET,
      ]);

      // Always set system page to the bottom of the page tree
      $system_page = $this->pages->get("template=system");
      if($system_page != "" && $system_page->sort < 49) {
        $system_page->setAndSave("sort", 49);
      }

      $this->config->scripts->append($this->module_url."/assets/drag-drop-sort.js");

      if(!empty($this->js_files)) {
        $js_files = explode("\n", $this->js_files);
        foreach($js_files as $js) {
          $suffix = $this->config->debug ? '?v='.time() : '';
          $this->config->scripts->append($js.$suffix);
        }
      }

      // Drag & drop sort
      if($this->input->post->action == "drag_drop_sort") {
        $id = $this->sanitizer->int($this->input->post->id);
        $p = $this->pages->get($id);
        $next_id = $this->sanitizer->int($this->input->post->next_id);
        $next_page = (!empty($next_id)) ? $this->pages->get($next_id) : "";
        $this->dragDropSort($p, $next_page);
      }

      // run hide pages hook
      $this->addHookAfter('ProcessPageList::execute', $this, 'hidePages');

    }
    
  }

  /* ----------------------------------------------------------------
    Admin UI
  ------------------------------------------------------------------- */

 /**
  *  Page Edit Link
  *  Use this method to generate page edit link.
  *  @param integer $id  Page ID
  *  @example href='{$this->pageEditLink($item->id)}';
  */
  public function pageEditLink($id) {
    $currentURL = $_SERVER['REQUEST_URI'];
    $url_segment = explode('/', $currentURL);
    $url_segment = $url_segment[sizeof($url_segment)-1];
    // encode & to ~
    $url_segment = str_replace("&", "~", $url_segment);
    $segment1 = $this->input->urlSegment1 ? $this->input->urlSegment1."/" : "";
    return $this->page->url . "edit/?id=$id&back_url={$segment1}{$url_segment}";
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
    if(!$this->input->get->id) {
      if(!empty($back_url)) {
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
    if($this->input->get->back_url) {
      // decode back_url:  ~ to &  - see @method pageEditLink()
      $back_url_decoded = str_replace("~", "&", $this->input->get->back_url);
      $this->session->set("back_url", $back_url_decoded);
    }

    /**
     *  Set the breadcrumbs
     *  add $_SESSION["back_url"] to the breacrumb link
     */
    $this->fuel->breadcrumbs->add(new Breadcrumb($this->page->url.$this->session->get("back_url"), $this->page->title));

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
    if(empty($next_page) || $next_page == "") {
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

    if($this->user->isSuperuser() && $this->hide_for == "2") return;

    // get system pages
    $sysPagesArr = $this->sys_pages;

    // aditional pages to hide by ID
    $customArr = [];
    if($this->hide_system_pages == "1") {
      $customArr[] = "2"; // admin
      $customArr[] = $this->pages->get("template=system");
    }

    if($this->config->ajax) {

      // manipulate the json returned and remove any pages found from array
      $json = json_decode($event->return, true);
      if($json && isset($json['children'])) {
        foreach($json['children'] as $key => $child){
          $c = $this->pages->get($child['id']);
          $pagetemplate = $c->template;
          if(in_array($pagetemplate, $sysPagesArr) || in_array($c, $customArr)) {
            unset($json['children'][$key]);
          }
        }
        $json['children'] = array_values($json['children']);
        $event->return = json_encode($json);
      }

    }

  }

  //-------------------------------------------------------- 
  //  Helpers
  //-------------------------------------------------------- 

  // Check if current page is admin page
  public function isAdminPage() {
    if(strpos($_SERVER['REQUEST_URI'], $this->wire('config')->urls->admin) === 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   *  Save Module Settings
   *  @param string $module     module class name
   *  @param array $data        module settings
   */
  public function saveModule($module, $data = []) {
    $old_data = $this->modules->getModuleConfigData($module);
    $data = array_merge($old_data, $data);
    $this->modules->saveModuleConfigData($module, $data);
  }

}