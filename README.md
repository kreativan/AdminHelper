# AdminHelper
Processwire admin helper module that provides some useful functions and helpers mainly related to the custom admin UI and Process modules.

## Utility
`$util = $adminHelper->utility();`

```php

//
// Valitron
//

$v = $util->valitron($_POST, 'en');
$v->labels(['name' => 'Your Name', 'email' => 'Your Email']);
$v->rule('required', ['name', 'email']);

if (!$v->validate()) {
  $errors = $v->errors();
} else {
  // Success
}

// Validate POST request
$errors = $util->validatePOST([
  'labels' => ['name' => 'Your Name', 'email' => 'Your Email'],
  'required' => ['name', 'email'],
  'email' => ['email' ],
  'integer' => ['age', 'days'],
], 'en');

if (!$errors) {
  // Success
}

//
//  Helpers
//

// Format Page String
// Convert page.title to $page->title
$string = "{select_page.url}";
$util->formatPageString($string, $page);

//
// JSON
//

// echo json response header and json_encode
$util->jsonResponse($response);
// decode json file from AdminHelper json library (/lib/json/file_name.json)
$util->json_decode($file_name);
// encode array to json to use it inline in html attributes
$util->json_encode($data_array);

```

## HTMX

`$htmx = $adminHelper->htmx();`

```php
// Page Edit Modal
$htmx->pageEditModal($page_id, $data = []);
// Page Create Modal
$htmx->pageCreateModal($parent_id, $template_id, $vals = []);
// New Page Modal
$htmx->newPageModal($parent_id, $template_id, $vals = []);


/**
 * HTMX Request using only array as params
 * @param string $url - url or file path, if file path then $htmx_get = true
 * @param array $data
 * @param bool $htmx_get - if true then $url will be ?htmx=$url, if false then $url will be $url
 * @return string - HTMX attributes
 */
$htmx->req($url, $data = [], $htmx_get = true);

/**
 * Search
 * Type to search
 * @param string $url - url or file path, if file path then $htmx_get = true
 * @param array $data
 * @param bool $htmx_get - if true then $url will be ?htmx=$url, if false then $url will be $url
 */
$htmx->search($url, $data = [], $htmx_get = true);

/**
 * Modal
 * Modal markup needs to have #htmx-modal css ID
 * @param string $file_path - file path
 * @param array $data
 * @return string - html attributes
 */
$htmx->modal($file_path, $data = []);

/**
 * Offcanvas
 * Offcanvas markup needs to have #htmx-modal css ID
 * @param string $file_path - file path
 * @param array $data
 * @return string - html attributes
 */
$htmx->offcanvas($file_path, $data = []);
```