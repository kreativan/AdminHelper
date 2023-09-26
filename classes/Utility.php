<?php

/**
 *  AdminHelper: Utility
 *  @author Ivan Milincic <hello@kreativan.dev>
 *  @link http://www.kraetivan.dev
 */

class AdminHelper_Utility extends WireData {

  public function __construct() {
    // ...
  }

  // Check if current page is admin page
  public function isAdminPage() {
    if (strpos($_SERVER['REQUEST_URI'], $this->wire('config')->urls->admin) === 0) {
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

  /**
   *  Format page strings
   *  extract page variables
   *  @param string $string  eg: {title} or {select_page.url}
   *  @example $this->format("{select_page.url}") will get $page->select_page->url
   *  @return string
   */
  public function formatPageString($string, $p = "") {
    $page = $p != "" ? $p : wire("page");
    $string = ltrim($string);
    $string = preg_replace('/\s\s+/', ' ', $string);
    $text = preg_match_all('#\{(.*?)\}#', $string, $matches);
    $arr = $matches[0];
    $i = 0;
    foreach ($arr as $item) {
      $n = $i++;
      $str = $matches[1][$n];
      $str = explode(".", $str);
      $sl1 = $str[0];
      $sl2 = isset($str[1]) ? $str[1] : "";
      $selector = !empty($sl2) ? $page->{$sl1}->{$sl2} : $page->{$sl1};
      $string = str_replace($item, $selector, $string);
    }
    $string = strip_tags($string);
    $string = wire("sanitizer")->removeNewlines($string);
    return $string;
  }

  // ========================================================= 
  // JSON 
  // ========================================================= 

  /**
   * JSON response
   * @param array $response
   */
  public function jsonResponse($response = []) {
    header('Content-type: application/json');
    if (count($response) > 0) echo json_encode($response);
    exit();
  }

  /**
   * Get json file from the lib and decode it
   * @param string $file_name - file name without extension
   * @return array
   */
  public function json_decode($file_name) {
    $json = file_get_contents($this->wireApp->lib_path("json/{$file_name}.json"));
    return json_decode($json, true);
  }

  /**
   * Encode json
   * ready to be used in HTML attributes
   * @param array $data
   */
  public function json_encode($data) {
    return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
  }

  // ========================================================= 
  // Validation 
  // ========================================================= 

  /**
   * Init valitron validation library
   * @see $this->validatePOST() for usage
   */
  public function valitron($array, $lang = '') {
    require($this->path() . "lib/valitron/src/Valitron/Validator.php");

    // Set global language
    if ($lang != '') {
      \Valitron\Validator::lang($lang);
    } else {
      \Valitron\Validator::lang($this->adminHelper->lang());
    }

    // create valitron instance
    $v = new \Valitron\Validator($array);

    // return valitron instance
    return $v;
  }

  /**
   * Validate POST data using $this->valitron() method
   * @param array $params
   * @param string $lang
   * @return array|bool false if POST is valid - array of errors if not
   * 
   * @example
   * $errors = $adminHelper->validatePOST([
   *  'labels' => ['name' => 'Your Name', 'email' => 'Your Email'],
   *  'required' => ['name', 'email'],
   *  'email' => ['email' ],
   *  'integer' => ['age', 'days'],
   * ], 'en');
   * 
   */
  public function validatePOST($params = [], $lang = '') {

    $labels = !empty($params['labels']) ? $params['labels'] : [];
    $required = !empty($params['required']) ? $params['required'] : []; // field names

    // exclude from rules
    $exc = ['labels', 'required'];

    $v = $this->valitron($_POST, $lang);
    $v->labels($labels);
    $v->rule('required', $required);

    // add all params except excluded as rules
    foreach ($params as $key => $array) {
      if (!in_array($key, $exc)) {
        $v->rule($key, $array);
      }
    }

    if (!$v->validate()) {
      return $v->errors();
    } else {
      return false;
    }
  }
}
