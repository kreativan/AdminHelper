# AdminHelper
Processwire admin helper module that provides some useful functions and helpers mainly related to the custom admin UI and Process modules.

## Methods
```php
// module path
$helper->path();

// module url
$helper->url();

// Check if current page is admin page
$helper->isAdminPage();

// Get current user language
$helper->lang();

// Encode array to json data, ready to use in html attributes
$helper->json_encode($data);

/**
 * Auto-load Actions
 * Will include files from /actions/ folder based on a specified GET variable
 * @param array $GET - name of the get variable eg: 'my_action' eg: '?my_action=delete'
 * @param string $module - module name
 */
$helper->autoloadActions($GET, $module);

/**
 * Auto-load folder
 * Include all files from a specified folder 
 * @param string $folder - folder path and name eh: __DIR__ . '/hooks/'
 */ 
$helper->autoloadFolder($folder);
```

## Admin Table

Render dynamic data tables.

```php
$selector = "template=MY_TEMPLATE, limit=50, include=all, status!=trash, sort=-created";
$parent_id = $pages->get('template=MY_TEMPLATE')->id;
$template_id = $templates->get('MY_TEMPLATE')->id;

/** 
 * Tabs 
 */
$helper->render('markup/admin-tabs.php', [
  "tabs" => [
    "my_tab" => [
      'title' => 'My Tab',
      'url' => "./?tab=my_tab",
      'icon' => 'user',
      'visible' => true,
    ],
  ]
]);

/**
 * Add new page button
 */
$helper->render('markup/admin-table-new-button', [
  "parent_id" => $parent_id,
  "template_id" => $template_id,
  "text" => "Create New",
]);

/**
 * Admin Table
 */
$helper->render("markup/admin-table", [
  "selector" => $selector, // selector to find pages
  // "close_modal" => "true", // close modal after page save
  "table_actions" => 1, // display page publish-unpublish nd page trash
  // "remove_tabs" => "false", // remove tabs from modal edit
  //"settings_tab" => 1, // display settings tab on modal page edit
  //"delete_tab" => 1, // display delete tab on modal page edit
  //"delete_tab_ref" => "true", // remove delete tab if page has references
  //"references" => false, // link to page references ui
  "dropdown_file" => __DIR__ . "/MY_DROPDOWN_MENU_FILE.php", // dropdown menu file path
  "table_class" => "uk-table-striped uk-table-hover",
  // table fields [label => field_name]
  // you can use dot to get and subfield value eg: "template.name",
  // or you can use page method name to get the value eg: "test()"
  "table_fields" => [
		'thumbnail' => 'admin_table_thumbnail()', // PageClass method
    'ID' => 'id',
    'name' => 'name',
  ],
]);

```