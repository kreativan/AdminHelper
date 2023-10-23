<?php

class AdminHelper_HTMX extends WireData {

  public function __construct() {
    $this->tmpl_folder = __DIR__ . '/../tmpl/';

    $this->page_edit_modal = $this->tmpl_folder . 'page-edit-modal.php';
    $this->page_create_modal = $this->tmpl_folder . 'page-create-modal.php';
    $this->new_page_modal = $this->tmpl_folder . 'new-page-modal.php';
    $this->admin_menu_offcanvas = $this->tmpl_folder . 'admin-menu-offcanvas.php';
  }

  /**
   * Watch for htmx requests
   * If there is htmx request, include file based on the passed htmx get variable
   * "?htmx=file_path"
   */
  public function watch() {
    if ($this->input->get->htmx) {

      $htmx_file = $this->input->get->htmx;
      $htmx_file = substr($htmx_file, -4) == ".php" ? $htmx_file : "{$htmx_file}.php";

      if (file_exists($htmx_file)) {

        $this->files->include($htmx_file, [
          "AdminHelper" => $this->modules->get("AdminHelper"),
          "_this" => $this->input->get->this ? $this->modules->get($this->input->get->this) : null,
        ]);

        exit();
      }
    }
  }

  /**
   * HTMX Request using only array as params
   * @param string $url - url or file path, if file path then $htmx_get = true
   * @param array $data
   * @param bool $htmx_get - if true then $url will be ?htmx=$url, if false then $url will be $url
   * @return string - HTMX attributes
   */
  public function req($url, $data = [], $htmx_get = true) {

    $url = $htmx_get ? "?htmx=$url" : $url;

    $method = !empty($data["method"]) ? $data["method"] : "get";
    $trigger = !empty($data["trigger"]) ? $data["trigger"] : "click";
    $target = !empty($data["target"]) ? $data["target"] : false;
    $select = !empty($data["select"]) ? $data["select"] : false;
    $swap = !empty($data["swap"]) ? $data["swap"] : false;
    $indicator = !empty($data["indicator"]) ? $data["indicator"] : "";
    $push_url = !empty($data["push_url"]) ? $data["push_url"] : false;

    $vals = !empty($data["vals"]) ? $data["vals"] : $data;
    if ($vals && is_array($vals)) $vals = json_encode($vals);

    if ($method == 'get') {
      $attr = "hx-get='{$url}'";
    } else {
      $attr = "hx-post='{$url}'";
    }

    $attr .= " hx-trigger='$trigger'";
    if ($target) $attr .= " hx-target='$target'";
    if ($select) $attr .= " hx-select='$select'";
    if ($swap) $attr .= " hx-swap='$swap'";
    if ($indicator) $attr .= " hx-indicator='$indicator'";
    if ($push_url) $attr .= " hx-push-url='$push_url'";
    if ($vals) $attr .= " hx-vals='$vals'";

    return $attr;
  }

  /**
   * Search
   * Type to search
   * @param string $url - url or file path, if file path then $htmx_get = true
   * @param array $data
   * @param bool $htmx_get - if true then $url will be ?htmx=$url, if false then $url will be $url
   */
  public function search($url, $data = [], $htmx_get = true) {

    $url = $htmx_get ? "?htmx=$url" : $url;

    $target = !empty($data["target"]) ? $data["target"] : "body";
    $swap = !empty($data["swap"]) ? $data["swap"] : "innerHTML";
    $push_url = !empty($data["push_url"]) ? $data["push_url"] : false;
    $indicator = !empty($data["indicator"]) ? $data["indicator"] : false;

    $vals = !empty($data["vals"]) ? $data["vals"] : $data;
    if ($vals && is_array($vals)) $vals = json_encode($vals);

    $attr = "hx-get='$url'";
    $attr .= " hx-trigger='keyup changed delay:500ms, search'";
    $attr .= " hx-target='$target'";
    $attr .= " hx-swap='$swap'";

    if ($push_url) $attr .= " hx-push-url='$push_url'";
    if ($indicator) $attr .= " hx-indicator='$indicator'";
    if ($vals) $attr .= " hx-vals='$vals'";

    return $attr;
  }

  //------------------------------------------------- 
  //  Modal and Offcanvas
  //-------------------------------------------------

  /**
   * Modal
   * Modal markup needs to have #htmx-modal css ID
   * @param string $file_path - file path
   * @param array $data
   * @return string - html attributes
   */
  public function modal($file_path, $data = []) {

    if (is_array($file_path)) {
      $data = $file_path;
      $file_path = $data["file"];
      unset($data['file']);
    }

    $indicator = !empty($data["indicator"]) ? $data["indicator"] : false;
    $vals = false;
    if (is_array($data) && count($data) > 0) $vals = json_encode($data);

    $onclick = "adminHelper.htmxModal()";

    $attr = "";
    $attr .= " hx-get='./?htmx={$file_path}'";
    $attr .= " hx-target='body'";
    $attr .= " hx-swap='beforeend'";
    $attr .= " onclick='$onclick'";
    if ($indicator != "") $attr .= " hx-indicator='$indicator'";
    if ($vals) $attr .= " hx-vals='$vals'";

    return $attr;
  }

  /**
   * Offcanvas
   * Offcanvas markup needs to have #htmx-offcanvas css ID
   * @param string $file_path - file path
   * @param array $data
   * @return string - html attributes
   */
  public function offcanvas($file_path, $data = []) {

    $indicator = !empty($data["indicator"]) ? $data["indicator"] : false;
    $vals = false;
    if (is_array($data) && count($data) > 0) $vals = json_encode($data);

    $onclick = "adminHelper.htmxOffcanvas()";

    $attr = "";
    $attr .= " hx-get='./?htmx={$file_path}'";
    $attr .= " hx-target='body'";
    $attr .= " hx-swap='beforeend'";
    $attr .= " onclick='$onclick'";
    if ($indicator != "") $attr .= " hx-indicator='$indicator'";
    if ($vals) $attr .= " hx-vals='$vals'";

    return $attr;
  }

  //------------------------------------------------- 
  //  Specific
  //-------------------------------------------------

  /**
   * Edit page in modal window
   * @param int $id - page id
   * @param array $data - additional data to pass to the template file
   * @return string - html attributes
   */
  public function pageEditModal($id = null, $data = []) {

    $src = $this->config->urls->admin . "page/edit/?id={$id}&modal=1";
    $onclick = "adminHelper.htmxModal()";
    $indicator = !empty($data['indicator']) ? $data['indicator'] : false;

    $vals = ['page_id' => $id, 'src' => $src];
    $vals = array_merge($vals, $data);
    $vals = json_encode($vals);

    $attr = "hx-get='?htmx={$this->page_edit_modal}'";
    $attr .= " hx-target='body'";
    $attr .= " hx-swap='beforeend'";
    $attr .= " hx-vals='$vals'";
    if ($indicator) $attr .= " hx-indicator='$indicator'";
    $attr .= " onclick='$onclick'";

    return $attr;
  }

  /**
   * Create page in modal window
   * @param int $parent_id - parent page id
   * @param int $template_id - template id
   * @param array $vals - additional data to pass to the template file
   * @return string - html attributes
   */
  public function pageCreateModal($parent_id, $template_id, $vals = []) {

    $onclick = "adminHelper.htmxModal()";

    // $vals = ['parent_id' => $parent_id, 'template_id' => $template_id];
    $vals['parent_id'] = $parent_id;
    if ($template_id) $vals['template_id'] = $template_id;
    $vals = json_encode($vals);

    $attr = "hx-get='?htmx={$this->page_create_modal}'";
    $attr .= " hx-target='body'";
    $attr .= " hx-swap='beforeend'";
    $attr .= " hx-vals='$vals'";
    $attr .= " onclick='$onclick'";

    return $attr;
  }

  /**
   * New Page Modal
   * @param int $parent_id - parent page id
   * @param int $template_id - template id
   */
  public function newPageModal($parent_id, $template_id, $vals = []) {
    $onclick = "adminHelper.htmxModal()";

    // $vals = ['parent_id' => $parent_id, 'template_id' => $template_id];
    $vals['parent_id'] = $parent_id;
    if ($template_id) $vals['template_id'] = $template_id;
    $vals = json_encode($vals);

    $attr = "hx-get='?htmx={$this->new_page_modal}'";
    $attr .= " hx-target='body'";
    $attr .= " hx-swap='beforeend'";
    $attr .= " hx-vals='$vals'";
    $attr .= " onclick='$onclick'";

    return $attr;
  }

  /**
   * Trigger admin menu offcanvas
   */
  public function adminMenuOffcanvas() {
    return $this->offcanvas($this->admin_menu_offcanvas);
  }
}
