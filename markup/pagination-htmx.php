<?php

/** 
 * Markup: pagination 
 * 
 */

namespace ProcessWire;

// items
$items = !empty($items) ? $items : "";

// HTMX file for the markup to swap
$htmx_file = !empty($htmx_file) ? $htmx_file : "";

// css id of the HTMX target element to swap
$htmx_target = !empty($htmx_target) ? $htmx_target : "";

// Variables to be included in the HTMX request and url segments
$vars = !empty($vars) ? $vars : [];

// URL
$url = "?modal=1&htmx=$htmx_file";

// Add variables to the URL
if (count($vars) > 0) {
  foreach ($vars as $key => $value) {
    $url .= "&$key=$value";
  }
}

$onclick = 'adminHelper.toggleNav(".pagination-htmx-item", "uk-active")';
$link_markup = "<a href='{url}' hx-get='{url}{$url}' hx-target='{$htmx_target}' hx-select='$htmx_target' hx-swap='outerHTML' onclick='{$onclick}'>{out}</a>";

$pagination = $items->renderPager([
  'lastItemClass'         => "pagination-last",
  'currentItemClass'      => "uk-active",
  'listMarkup'            => "<ul class='uk-pagination uk-margin-remove'>{out}</ul>",
  'itemMarkup'            => "<li class='pagination-htmx-item {class}'>{out}</li>",
  'linkMarkup'            => $link_markup,
  'currentLinkMarkup'     => $link_markup,
  'previousLinkMarkup'    => "",
  'nextLinkMarkup'        => "",
]);

echo $pagination;
