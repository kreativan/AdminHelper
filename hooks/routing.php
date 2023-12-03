<?php

/** 
 * Hooks: routing 
 * 
 */

namespace ProcessWire;

$this->addHook('/test/', function ($event) {
  $test_file = $this->config->paths->templates . "test.php";
  $this->files->include($test_file);
  return true;
});

/**
 * Create /get-htmx/ route
 * To render specific files from htmx folder from any where
 * We need this route because ./?htmx= is working only in admin.
 * For example we want ot use page-edit-modal on front-end ... 
 * So any admin markup you want to share with the front-end store it in /htmx/ folder
 * and access it using /get-htmx/{file_name}/ route
 */
$this->addHook('/get-htmx/{file_name}/', function ($event) {
  $file_name = $event->arguments(1);
  $file = $this->AdminHelper->path() . "htmx/$file_name.php";
  if (!file_exists($file)) return false;
  $this->files->include($file);
  return true;
});


/**
 * Create login route if login page does not exists
 * /login/
 */
if ($this->pages->get('template=login') == "") {
  $this->addHook('/login/', function ($event) {
    $login_file = $this->AdminHelper->path() . "html/login.php";
    if (file_exists($login_file)) {
      $this->files->include($login_file);
      return true;
    }
  });
}

/**
 * Create xml sitemap route
 * /example.com/sitemap.xml
 */
if ($this->sitemap_route == 1) {
  $this->addHook('/sitemap.xml', function ($event) {
    $helper_sitemap = $this->AdminHelper->path() . "html/sitemap.xml.php";
    $tmpl_sitemap = $this->config->paths->templates . "sitemap.xml.php";
    $sitemap = file_exists($tmpl_sitemap) ? $tmpl_sitemap : $helper_sitemap;
    $this->files->include($sitemap);
    return true;
  });
}


/**
 * Create cron route
 * Execute file based on the {file_name}
 */
if ($this->cron_route == 1) {
  $this->addHook('/cron/{file_name}/?', function ($event) {
    $file_name = $event->arguments(1);
    $file = $this->config->paths->templates . "cron/$file_name.php";
    if (!file_exists($file)) return false;
    $this->files->include($file);
    return true;
  });
}


/**
 * Get json response for any page by url 
 * @example /basic-page/json/
 * Page needs to have @method $page->json() defined in its PageClass
 */
if ($this->json_route == 1) {
  $this->addHook('(/.*)/json/?', function ($event) {
    $page = $event->pages->get($event->arguments(1));
    if ($page->viewable()) {
      if (method_exists($page, "json")) {
        $json = $page->json();
        if ($json === false) return false;
        header('Content-type: application/json');
        return is_array($json) ? json_encode($json) : $json;
      }
    }
  });
}
