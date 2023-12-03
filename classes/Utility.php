<?php

namespace AdminHelper;

use ProcessWire\WireData;

class Utility extends WireData {

  public function __construct() {
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
   * Replace {text} in a provided string 
   * with the key from a $data array
   * @param string $string eg: "Welcome to {title}"
   * @param array $data eg: ['title' => 'My Website']
   * @return string
   */
  public function strReplace($string, $data) {
    $regex = preg_match_all('#\{(.*?)\}#', $string, $matches);
    $arr = $matches[0];
    foreach ($arr as $item) {
      $key = str_replace("{", "", $item);
      $key = str_replace("}", "", $key);
      $replace = !empty($data[$key]) ? $data[$key] : "";
      $string = str_replace($item, $replace, $string);
    }
    return $string;
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
      $replace = !empty($selector) ? $selector : "";
      $string = str_replace($item, $replace, $string);
    }
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

  //-------------------------------------------------
  // Numbers
  //-------------------------------------------------

  /**
   * Check if number is even or odd
   */
  public function even_odd($number) {
    if ($number % 2 == 0) {
      return "even";
    } else {
      return "odd";
    }
  }

  /**
   * Format price
   * @param float $price
   * @param string $currency - defines decimal and thousands separator
   * @return float
   */
  public function format_price(float $price, string $currency = "EUR") {
    switch ($currency) {
      case "EUR":
        $decimal_separator = ".";
        $thousands_separator = ",";
        break;
      default:
        $decimal_separator = ",";
        $thousands_separator = ".";
    }
    return number_format((float) $price, 2, $thousands_separator, $decimal_separator);
  }
}
