# AdminHelper
Processwire admin helper module that provides some useful functions and helpers mainly related to the custom admin UI and Process modules.

## Methods
```php
// module path
$adminHelper->path();

// module url
$adminHelper->url();

// Check if current page is admin page
$adminHelper->isAdminPage();

// Get current user language
$adminHelper->lang();

// Encode array to json data, ready to use in html attributes
$adminHelper->json_encode($data);

/**
 * Auto-load Actions
 * Will include files from /actions/ folder based on a specified GET variable
 * @param array $GET - name of the get variable eg: 'my_action' eg: '?my_action=delete'
 * @param string $module - module name
 */
$adminHelper->autoloadActions($GET, $module);

/**
 * Auto-load folder
 * Include all files from a specified folder 
 * @param string $folder - folder path and name eh: __DIR__ . '/hooks/'
 */ 
$adminHelper->autoloadFolder($folder);
```

## Utility

```php

$util = $adminHelper->utility();


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

```php
$htmx = $adminHelper->htmx();

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

## AdminHelper.js
Use AdminHelper.js to send ajax requests and submit forms and automatically trigger notifications, modals, reloads, htmx etc.. based on the response.

### `ajaxReq()`
```js
// request url
let url = './?id=123';

// json data to pass to the request
// can also send confirm message and description
let data = {
  'confirm_message': 'Are you Sure?', 
  'confirm_meta': 'Are you sure you ant to send this request?',
};

// Enable modal confirm
let confirm = true;

// run ajax request
adminHelper.ajaxReq(url, data, confirm);
```
Send ajax request to the `./?id=123` url, without any data and no confirm.
```html
<button onclick="adminHelper.ajaxReq('./?id=123', null, false)">
  Ajax Request
</button>
```
### `submit()`
Standard form submit (no-ajax) based on the css selector.
```js
// Form css selector
let css_selector = '#my_form';

// Action name so we can indentify the request
// if (isset($_POST['js_submit'])) { ... }
let action_name = "js_submit";

// Run form submit
adminHelper.adminFormSubmit(css_selector, action_name);
```
Example form submit with `#my_form` css selector and `js_submit` action name.
```php
<?php
if (isset($_POST['js_submit'])) {
  // Do something
}
?>

<!-- Form -->
<form id="my_form" action="./" method="POST">
  <label>Example</label>
  <input type="text" name="example" />
</form>

<!-- submit button outside the form -->
<button onclick="adminHelper.submit('#my_form', 'js_submit')">
  Submit Form
</button>
``` 

### `formSubmit()`
Submit form with the ajax request to url specified in the form action attribute. Automatically collect form data, trigger notifications, modals, reloads, htmx etc.. based on the response.
```js
adnimHelper.formSubmit('#my_form');
```
Example:
```php
<?php
/**
 * Handle ajax form submit
 * @see Ajax Response docs below for all supported response options
 */
if (isset($_POST['example'])) {

  // Set JSON response
  header('Content-Type: application/json');
  echo json_encode([
    'status' => 'success', // will also define notification style: success, warning, danger, error
    'notification' => 'Form has been submitted!', // trigger uikit notification
    'reset_form' => true,
  ]);

  exit();
}
?>

<form id="my_form" action="./" method="POST">
  <label>Example</label>
  <input type="text" name="example" />
</form>

<button onclick="adminHelper.formSubmit('#my_form')">
  Submit Form
</button>
``` 

### Ajax Response
When using `adminHelper` to send ajax request, you can automatically trigger notifications, modals, reloads, htmx etc.. based on the response.
```php
<?php
$response = [

  // Used also for notification color
  // string: success, warning, danger, error
  "status" => "pending", 

  // Clear-reset form input values
  "reset_form" => false,

  // Response message
  "message" => "Some response message",

  // Notification, will trigger uikit notification
  "notification" => "Notification: Ajax form submit was ok!",

  // Will trigger modal on response, has priority over notification
  "modal" => "<h3>Title</h3><p>text</p>",

  // Same as 'modal'. Will trigger modal response, has priority over notification
  "alert" => "<h3>Title</h3><p>text</p>",

  // Will trigger dialog on response
  // It is a nice wy to display iframe content in a modal
  "dialog" => "<iframe src=''></iframe>",

  // Set modal dialog width
  "modal_width" => "1200px",

  // Specify modal css ID that you want to remove on response
  // this is usually htmx-modal, as its mainly used to remove htmx triggered modals
  "close_modal_id" => "htmx-modal",

  // Redirect after response. 
  // If used with modal, will redirect after modal confirm... 
  "redirect" => "/",

  // Open new browser tab after response
  "open_new_tab" => "example.com",

  // Array of errors (strings), will trigger notification for each
  // Eg: ['error one', 'email two']
  "errors" => [],

  // Array of invalid field names eg: ['name', 'email']
  "error_fields" => [],

  // Valitron errors 
  // Pass the valitron errors directly to the response
  "valitron" => $valitron->errors(),

  // Run htmx sync on response
  // Sync-update all DOM elements with the data-htmx attribute
  "htmxSync" => 1,

  // Will trigger htmx request on response
  "htmx" => [
    "type" => "GET",
    "url" => "/", // or file
    "target" => "#target-element",
    "swap" => "innerHTML",
    "indicator" => "#htmx-indicator",
    "push_url" => "./",
  ],

  /**
   * Update DOM element
   * @param string $selector - css selector of the target element
   * @param string $html - innerHTML to replace the target element with
   */
  "update_DOM" => [
    "selector" => ".cart",
    "html" => "1",
  ],
];

header('Content-type: application/json');
echo json_encode($response);
exit();

```
