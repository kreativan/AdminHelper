<?php

/**
 * classes: HtmxEndPoint
 * Requires api template and a page.
 * It will execute api files based on the urlSegment
 * from AdminHelper/api/ and /templates/api/ folders
 * @example
 * $HtmxEndPoint = $AdminHelper->HtmxEndPoint(); 
 * $HtmxEndPoint->render();
 */

namespace AdminHelper;

use \ProcessWire\WireData;

class HtmxEndPoint extends WireData {

  public function render() {
    $file = $this->AdminHelper->path() . "htmx/" . $this->htmx_file();
    $file_custom = $this->config->paths->templates . $this->htmx_file();
    $file = file_exists($file_custom) ? $file_custom : $file;
    // Render File
    if ($this->htmx_file() && file_exists($file)) {
      $this->files->include($file);
      exit();
    } else {
      echo "<code style='color: red;'>File not found</code>";
      if ($this->config->debug) {
        echo "<pre>{$file}</pre>";
      }
    }
  }

  /**
   *  Define file that will be rendered
   *  based on urlSegments
   *  @return string
   */
  public function htmx_file() {

    $input = $this->input;

    $file_path = "";
    $i = 0;

    foreach ($input->urlSegments as $segment) {
      $file_path .= ($i++ != 0) ? "/{$segment}" : $segment;
    }

    $file_path = str_replace(".php", "", $file_path);
    $file_path = $file_path != "" ? "{$file_path}.php" : "";

    return $file_path;
  }
}
