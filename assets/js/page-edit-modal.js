// parent.location.reload();
window.addEventListener("DOMContentLoaded", function () {

  let htmxElements = parent.document.querySelectorAll("[data-htmx]");
  let htmxModal = parent.document.querySelector("#htmx-modal");

  // console.log(htmxElements);

  let spinner = parent.document.querySelector("#htmx-modal .uk-spinner");
  let pageEditModalIndicator = parent.document.querySelector('.page-edit-modal-indicator');

  if (spinner) spinner.classList.add('uk-hidden');
  if (pageEditModalIndicator) pageEditModalIndicator.classList.add('uk-hidden');

  // Reload parent document on any button clisk
  let buttons = document.querySelectorAll("button.ui-button");
  let delete_button = document.querySelector("#submit_delete");

  buttons.forEach(e => {

    e.addEventListener('click', function () {

      setTimeout(() => {
        if (htmxElements && htmxElements.length > 0) {
          adminHelper.htmxSync(htmxElements, true, false);
        } else {
          parent.location.reload();
        }
      }, 100);

      // if its' delete button, close modal anyways
      if (e == delete_button) UIkit.modal(htmxModal).hide();

    });
  });


});