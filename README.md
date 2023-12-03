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