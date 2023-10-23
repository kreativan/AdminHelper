<?php

/** 
 * Examples: Valitron 
 * 
 */

namespace ProcessWire;

$utility = $AdminHelper->utility();

// Init valitron validation lib
$v = $utility->valitron($_POST, 'fr');

// Custom labels array
$labels = [
  "field_name" => 'Field Label',
];

// set labels
$v->labels($labels);

// required
$req = ['one', 'two'];
$v->rule('required', $req);

// integer
$v->rule('integer', ['one']);

if (!$v->validate()) {
  d($v->errors());
}

// --------------------------------------------------------- 
// Use validatePOST() 
// --------------------------------------------------------- 

$errors = $utility->validatePOST([
  'labels' => [
    "field_name" => 'Field Label',
  ],
  'required' => ['one', 'two'],
  'integer' => ['one'],
]);

if ($errors) d($errors);