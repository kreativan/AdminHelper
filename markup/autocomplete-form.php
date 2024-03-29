<?php

/** 
 * Markup: autocomplete-form.php 
 * 
 */

namespace ProcessWire;

$action_url = !empty($action_url) ? $action_url : "./";
$form_id = !empty($form_id) ? $form_id : "autocomplete-form";
$field_name = !empty($field_name) ? $field_name : "autocomplete";
$field_label = !empty($field_label) ? $field_label : "Autocomplete Search";

// Data to search results
$template = $template ? $template : "";
$template_id = $template != "" ? $this->templates->get("name=$template")->id : "";
$selector = !empty($selector) ? $selector : "";
$search_fields = !empty($search_fields) ? $search_fields : "name title"; // fields to search
$search_label = !empty($search_label) ? $search_label : "{title}"; // search result item label
$search_value = !empty($value) ? $value : ""; // current value

$notes = !empty($notes) ? $notes : "";
$description = !empty($description) ? $description : "";

/**
 * Form
 */

$form = $this->modules->get("InputfieldForm");
$form->action = $action_url;
$form->method = "GET";
$form->attr("id+name", $form_id);

// PageAutocomplete (get users)
$f = $this->modules->get("InputfieldPageAutocomplete");
$f->label = $field_label;
$f->name = $field_name;
$f->required = true;
$f->maxSelectedItems = 1;
$f->template_id = $template_id;
if ($selector != "") {
  $f->findPagesSelector = $selector;
}
$f->searchFields = $search_fields;
$f->labelFieldFormat = $search_label;
$f->columnWidth = "100%";
$f->value = $search_value;
if ($notes) $f->notes = $notes;
if ($description) $f->description = $description;
// Add field to the form (do this for each field)
$form->append($f);

echo $form->render();

?>

<script>
  const autoCompleteSubmit = () => {

    let form = document.querySelector("#<?= $form_id ?>");
    let input = document.querySelector("#Inputfield_<?= $field_name ?>_input");

    // set input name user_id instead of user_id[]
    let _input = document.querySelector("#Inputfield_<?= $field_name ?>");
    _input.setAttribute("name", "<?= $field_name ?>");

    input.addEventListener("change", () => {
      form.submit();
    });

    document.querySelector(".InputfieldPageAutocompleteRemove").addEventListener("click", () => {
      _input.value = "";
      form.submit();
    });

  };
  autoCompleteSubmit()
</script>