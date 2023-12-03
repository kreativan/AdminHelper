<?php

/**
 * Classes: Valitron
 * @method $this init_lib($array, $lang = '')
 * @method $this get_req_errors($params = [], $lang = '')
 */

namespace AdminHelper;

use ProcessWire\WireData;

class Valitron extends WireData {

  public function __construct() {
  }

  /**
   * Init valitron validation library
   * @see $this->get_req_errors() for usage
   */
  public function init_lib($array, $lang = '') {
    require($this->AdminHelper->path() . "lib/valitron/src/Valitron/Validator.php");
    if ($lang != '') {
      \Valitron\Validator::lang($lang);
    } else {
      \Valitron\Validator::lang($this->adminHelper->lang());
    }
    $v = new \Valitron\Validator($array);
    return $v;
  }

  /**
   * Validate POST data using $this->valitron() method
   * @param array $params
   * @param string $lang
   * @return array|bool false if POST is valid - array of errors if not
   * 
   * @example
   * $errors = $this->get_req_errors([
   *  'labels' => ['name' => 'Your Name', 'email' => 'Your Email'],
   *  'required' => ['name', 'email'],
   *  'email' => ['email' ],
   *  'integer' => ['age', 'days'],
   * ], 'en');
   * if (!$errors) // is valid
   */
  public function get_req_errors($params = [], $lang = '') {

    $labels = !empty($params['labels']) ? $params['labels'] : [];
    $required = !empty($params['required']) ? $params['required'] : []; // field names

    // exclude from dynamic rules
    $exc = ['labels', 'required'];

    $v = $this->init_lib($_REQUEST, $lang);
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
