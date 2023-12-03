<?php

namespace AdminHelper;

use \ProcessWire\WireData;

class CSV extends WireData {

  /**
   * Check if csv file is valid
   * - delimiter is comma
   * - file is UTF-8
   */
  public function is_csv_valid($file_path) {

    // we will store errors here
    $errors = [];

    // get file delimiter
    $delimiter = $this->detectDelimiter($file_path);

    // check delimiter
    if ($delimiter != ',') {
      $errors[] = __('CSV file not valid. Please use a comma (,) as delimiter in your CSV file');
    }

    // check if file is UTF-8
    if (!mb_check_encoding(file_get_contents($file_path), 'UTF-8')) {
      $errors[] = __('CSV file not valid, not UTF-8');
    }

    // if there is an error, return false and set error notices
    if (count($errors) > 0) {
      foreach ($errors as $error) {
        $this->error($error, Notice::allowMarkup);
      }
      return false;
    }

    return true;
  }

  /**
   * Parse CSV file
   * as $key => $value array
   * @param string $csv_file - full file path
   * @param bool $sanitize - sanitize keys
   * @return array
   */
  public function parse_csv_file($csv_file, $sanitize = true) {

    $rows   = array_map('str_getcsv', file($csv_file));
    $header = array_shift($rows);
    $csv    = array();

    if ($sanitize) {
      foreach ($header as $key => $value) {
        $val = preg_replace('/\s+/', ' ', $value); // remove white space
        $val = $this->sanitizer->fieldName($val); // field name sanitizer
        $val = strtolower($val); // lowercase
        $header[$key] =  $val;
      }
    }

    foreach ($rows as $row) {
      if (count(array_filter($row)) != 0) {
        $csv[] = array_combine($header, $row);
      }
    }

    return $csv;
  }

  /**
   * @param string $csvFile Path to the CSV file
   * @return string Delimiter
   */
  public function detectDelimiter($csvFile) {
    $delimiters = [";" => 0, "," => 0, "\t" => 0, "|" => 0];

    $handle = fopen($csvFile, "r");
    $firstLine = fgets($handle);
    fclose($handle);
    foreach ($delimiters as $delimiter => &$count) {
      $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
  }
}
