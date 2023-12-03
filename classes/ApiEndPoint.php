<?php

/**
 * classes: ApiEndPoint
 * Requires api template and a page.
 * It will execute api files based on the urlSegment
 * from AdminHelper/api/ and /templates/api/ folders
 * @example
 * $ApiEndPoint = $AdminHelper->ApiEndPoint();
 * $ApiEndPoint->render(['auth' => true, 'debug' => false]);
 */

namespace AdminHelper;

use \ProcessWire\WireData;

class ApiEndPoint extends WireData {

  public function execute($params = []) {
    $this->render($params);
  }

  public function render($params = []) {

    $use_auth = !empty($params['auth']) && $params['auth'] ? true : false;
    $debug = !empty($params['debug']) && $params['debug'] ? true : false;

    if ($use_auth && !$this->user->isSuperuser()) {
      $auth = $this->modules->get('AdminHelper')->Auth();

      // Allow CORS requests
      // Access-Control-Allow-Origin
      $auth->CORS();

      // Authenticate
      $auth->auth($debug);
    }

    // Render readme file by default
    if (!$this->input->urlSegment1) {
      $this->files->include($this->readme());
      exit();
    }

    // Get api file
    // from admin helper or templates, templates has priority
    $file = $this->AdminHelper->path() . "api/" . $this->api_file();
    $file_custom = $this->config->paths->templates . "api/" . $this->api_file();
    $file = file_exists($file_custom) ? $file_custom : $file;

    // Render File
    if (file_exists($file)) {
      $this->files->include($file);
      exit();
    } else {
      $this->files->include($this->readme());
      exit();
      $this->AdminHelper->json_response([
        'status' => 'error',
        'file' => $file,
        'message' => 'End-point not found',
      ]);
      // exit();
      // throw new Wire404Exception();
    }
  }

  /**
   * Get readme file
   * @return string
   */
  public function readme() {
    $readme = $this->AdminHelper->path() . "api/readme.php";
    $readme_tmpl = $this->config->paths->templates . "api/readme.php";
    $readme = file_exists($readme_tmpl) ? $readme_tmpl : $readme;
    return $readme;
  }

  /**
   * Get file based on url segments
   * @return string
   */
  public function api_file() {
    $input = $this->input;
    $file_path = "";
    $i = 0;
    foreach ($input->urlSegments as $segment) {
      $file_path .= ($i++ != 0) ? "/{$segment}" : $segment;
    }
    $file_path = $file_path != "" ? "{$file_path}.php" : "";
    return $file_path;
  }
}
