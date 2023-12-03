<?php

/** 
 * Hooks: modules 
 * 
 */

namespace ProcessWire;

/**
 * Include Page Builder Hooks 
 */
if ($this->modules->isInstalled('PageBuilder')) {
  $page_builder = $this->modules->get('PageBuilder');
  include $page_builder->path() . "includes/hooks.php";
}
