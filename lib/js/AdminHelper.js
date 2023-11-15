/**
 * AdminHelper.js
 * @author Ivan Milincic <hello@kreativan.dev>
 * @link http://www.kraetivan.dev
 */

const adminHelper = (function () {

  'use strict';

  // Create the methods object
  var methods = {};

  /* =========================================================== 
    Ajax
  =========================================================== */

  methods.strToJSON = function (str) {
    let json_data = {};
    str = str.split(';');
    str.forEach(e => {
      if (e) {
        let arr = e.split(':');
        json_data[arr[0]] = arr[1];
      }
    });
    return json_data;
  }

  methods.isJSON = function (str) {
    try {
      JSON.parse(str);
    } catch (e) {
      return false;
    }
    return true;
  }

  /**
   * Modal Confirm
   * @param {string} message 
   * @returns 
   */
  methods.modalConfirm = async function (message = '', meta = '') {
    message = message != '' ? message : "Are you sure?";
    meta = meta != '' ? `<p class="uk-text-muted uk-text-center uk-margin-small">${meta}</p>` : '';
    const confirm = await UIkit.modal.confirm(`<div class="uk-text-large uk-text-center uk-text-bold">${message}</div>${meta}`).then(function () {
      return true;
    }, function () {
      return false;
    });
    return confirm;
  }


  /**
   * Ajax Request on given URl
   * @param {string} url 
   * @param {object} data
   */
  methods.ajaxReq = async function (url, data = null, isConfirm = false) {

    event.preventDefault();

    // Json data from data-ajax attribute
    let data_ajax = event.target.getAttribute("data-ajax");

    // if data doesent exist, get it from data-ajax attribute
    data = data ? data : data_ajax;

    if (data) {
      if (this.isJSON(data)) {
        data = JSON.parse(data)
      } else {
        data = this.strToJSON(data);
      }
    }

    let method = data && data.method ? data.method : 'POST';
    let confirm = data && data.confirm && data.confirm == 'true' ? true : false;
    let confirm_message = data && data.confirm_message ? data.confirm_message : '';
    let confirm_meta = data && data.confirm_meta ? data.confirm_meta : '';

    //------------------------------------------------- 
    //  Ask for confirmation
    //-------------------------------------------------

    if (isConfirm || confirm) {
      let confirmed = await this.modalConfirm(confirm_message, confirm_meta);
      if (!confirmed) return;
    }

    //------------------------------------------------- 
    //  Confirmed, let's go!
    //-------------------------------------------------

    let indicator;
    const indicatorGlobal = document.getElementById("ajax-indicator");

    /** 
     * Get the indicator
     * from the data argument data.indicator
     */
    if (data != null && data.indicator) {
      indicator = document.querySelector(data.indicator);
    } else {
      indicator = event.target.querySelector(".ajax-indicator");
    }

    if (indicator) {
      indicator.classList.remove("uk-hidden");
    } else if (indicatorGlobal) {
      indicatorGlobal.classList.remove("uk-hidden");
    }

    //------------------------------------------------- 
    //  Fetch options
    //------------------------------------------------- 

    // Fetch options
    let fetchOptions = {
      method: 'POST',
      cache: 'no-cache',
    };

    // console.log(method);

    /**
     * Create a formData from data argument object
     * Set fetch options based on the method
     * If POST include the body else add the formData to the url
     */

    if (data) {

      let formData = new FormData();

      for (const item in data) {
        if (item != 'method') formData.append(item, data[item]);
      }

      if (method == 'GET') {
        url += '?' + new URLSearchParams(formData).toString();

      } else {
        fetchOptions.body = formData;
      }
    }

    // console.log(url);

    /**
     * Send the fetch request
     */
    let request = await fetch(url, fetchOptions);
    let response = await request.json();

    /** Run response method */
    this.ajaxResponse(response);

    // hide indicator
    if (indicator) {
      indicator.classList.add("uk-hidden");
    } else if (indicatorGlobal) {
      indicatorGlobal.classList.add("uk-hidden");
    }

  }

  /* =========================================================== 
    Ajax Response
    Used in  this.ajaxReq() and this.formSubmit()
    - errors
    - modal
    - notification
    - redirect
    - open_new_tab
    - dialog
    - modal_width
    - htmx
    - close_modal_id
    - update_DOM
    If you want to trigger anything else, do it here!
  =========================================================== */

  methods.ajaxResponse = function (response) {

    // Log the response
    if (ProcessWire.config.adminHelper && ProcessWire.config.adminHelper.debug) console.log(response);

    // catch php valitron lib errors
    if (response.valitron) {
      // if no errors array, create it
      if (!response.errors) response.errors = [];
      // add valitron errors to response errors
      for (const key in response.valitron) {
        let item = response.valitron[key];
        if (item) response.errors.push(item);
      }
    }

    // Error notification, for each item in response errors
    if (response.errors && response.errors.length > 0) {
      response.errors.forEach(error => {
        UIkit.notification({
          message: error,
          status: 'danger',
          pos: response.notification_pos ? response.notification_pos : 'top-right',
          timeout: 3000
        });
      });
    }

    // Modal or Alert
    // If redirect or open_new_tab, do it after modal confirm
    else if (response.modal || response.alert) {
      UIkit.modal.alert(response.modal).then(function () {
        if (response.open_new_tab) {
          window.open(response.open_new_tab, '_blank');
        } else if (response.redirect) {
          window.location.href = response.redirect;
        }
      });
    }

    // Just a notification
    // based on a response status
    else if (response.notification) {
      UIkit.notification({
        message: response.notification,
        status: response.status ? response.status : 'primary',
        pos: response.notification_pos ? response.notification_pos : 'top-right',
        timeout: 3000
      });
    }

    // Redirect to the specified URL
    else if (response.redirect) {
      window.location.href = response.redirect;
    }

    // Open new tab has to be an url
    else if (response.open_new_tab) {
      window.open(response.open_new_tab, '_blank');
    }

    // Trigger uikit dialog
    else if (response.dialog) {
      UIkit.modal.dialog(response.dialog);
    }

    // Set custom modal width
    if (response.modal_width) {
      document.querySelector(".uk-modal:last-child > .uk-modal-dialog").style.width = response.modal_width;
    }

    /**
     * Run htmx Req If any
     * @param {string} url
     * @param {string} type - GET / POST
     * @param {string} target - #target-element
     * @param {string} swap = innerHTML
     * @param {string} indicator - #htmx-indicator
     * @param {string} push_url - url
     */
    if (response.htmx) {
      let htmxOpt = {};
      let htmxUrl = response.htmx.url ? response.htmx.url : "";
      let htmxFile = response.htmx.file ? response.htmx.file : "";
      let htmxContent = htmxFile ? htmxFile : htmxUrl;
      let htmxType = response.htmx.type ? response.htmx.type : "GET";
      if (response.htmx.target) htmxOpt.target = response.htmx.target;
      if (response.htmx.swap) htmxOpt.swap = response.htmx.swap;
      if (response.htmx.indicator) htmxOpt.indicator = response.htmx.indicator;
      htmx.ajax(htmxType, htmxContent, htmxOpt);
      if (response.htmx.push_url) {
        window.history.pushState({}, '', response.htmx.push_url);
      }
    }

    /**
     * htmxSync
     * Sync all elements that have data-htmx attributes
     */
    if (response.htmxSync) {
      this.htmxSync();
    }

    /**
     * Close modal based on specified ID
     * if exists...
     */
    if (response.close_modal_id) {
      let modal = window.document.getElementById(response.close_modal_id);
      if (modal) {
        UIkit.modal(modal).hide();
        if (response.htmx) {
          UIkit.util.on('#htmx-modal', 'hidden', function () {
            modal.remove();
          });
        }
      }
    }

    /**
     * Update DOM elements
     * by passed selector and html
     */
    if (response.update_DOM) {
      let selector = response.update_DOM.selector;
      let html = response.update_DOM.html;
      // console.log(response.update_DOM);
      if (selector && selector.charAt(0) == '#') {
        let el = document.querySelector(selector);
        // console.log(el);
        if (html) el.innerHTML = html;
      } else {
        let domElements = document.querySelectorAll(selector);
        domElements.forEach(e => {
          if (html) e.innerHTML = html;
        });
      }
    }

    // If there is a page edit indicator, hide it
    let pageEditModalIndicator = document.querySelector('.page-edit-modal-indicator');
    let pageEditModalIndicatorParent = parent.document.querySelector('.page-edit-modal-indicator');
    if (pageEditModalIndicator) pageEditModalIndicator.classList.add('uk-hidden');
    if (pageEditModalIndicatorParent) pageEditModalIndicatorParent.classList.add('uk-hidden');

  }


  /* =========================================================== 
    Forms
  =========================================================== */

  /**
   * Form Submit (non ajax)
   * Submit the form based on a css selector.
   * Hidden input field "action_name" is added to indentify the request in php
   * eg: if($input->get->js_submit) {...}
   */
  methods.submit = function (css_selector, action_name = "js_submit") {
    event.preventDefault();
    const form = document.querySelector(css_selector);
    // add input field so we know what action to process
    let input = document.createElement("INPUT");
    input.setAttribute("type", "hidden");
    input.setAttribute("name", action_name);
    input.setAttribute("value", "1");
    form.appendChild(input);
    form.submit();
  }

  /**
   * Submit Form Data to the form action url
   * This should be like: /ajax/example/
   * @param {string} form_id 
   */
  methods.formSubmit = async function (form_id) {

    event.preventDefault();

    /**
     * Get the form.
     * If does not exist, try to find a element with data-form attr instead
     */
    let form = document.getElementById(form_id);
    if (!form) form = document.querySelector("[data-form='" + form_id + "']");

    /**
     * Use this.formFields() method
     * to get the fields, so we can manupulate them,
     * clear values, add error indicators etc...
     */
    const fields = this.formFields(form_id);

    /**
     * Find .ajax-indicator
     * or #ajax-indicator
     */
    const indicator = form.querySelector(".ajax-indicator");
    const indicatorGlobal = document.getElementById("ajax-indicator");

    if (indicator) {
      indicator.classList.remove("uk-hidden");
    } else if (indicatorGlobal) {
      indicatorGlobal.classList.remove("uk-hidden");
    }

    /**
     * Fetch Options
     * url - based on form action or data-action attribute
     * method -  based on form method or data-method attribute
     */
    let ajaxUrl = form.getAttribute("action");
    if (!ajaxUrl) ajaxUrl = form.getAttribute("data-action");
    let formMethod = form.getAttribute("method");
    if (!formMethod) formMethod = form.getAttribute("data-method");

    /**
     * Use this.formData() method
     * to collect all form data
     */
    let formData = this.formData(form_id);

    /**
     * Send fetch request
     */
    let request = await fetch(ajaxUrl, {
      method: formMethod,
      cache: 'no-cache',
      body: formData
    });

    // Get the response
    let response = await request.json();

    // if reset-form clear form fields
    if (response.reset_form) this.formClear(form_id);

    // Clear error marks
    fields.forEach(e => {
      e.classList.remove("error");
    });


    // Form: mark error fields
    // Catch php valitron lib errors and add them to response.error_fields
    if (response.valitron) {
      if (!response.error_fields) response.error_fields = [];
      for (const key in response.valitron) {
        response.error_fields.push(key);
      }
    }

    // Form: mark error fields
    if (response.error_fields && response.error_fields.length > 0) {
      response.error_fields.forEach(e => {
        let field = form.querySelector(`[name='${e}']`);
        if (field) field.classList.add("error");
      });
    }

    // Run the response
    this.ajaxResponse(response);

    // hide indicator
    if (indicator) {
      indicator.classList.add("uk-hidden");
    } else if (indicatorGlobal) {
      indicatorGlobal.classList.add("uk-hidden");
    }

  }

  /**
   * Get fields from specified form
   * @param {string} form_id 
   */
  methods.formFields = function (form_id) {
    let form = document.getElementById(form_id);
    if (!form) form = document.querySelector("[data-form='" + form_id + "']");
    const fields = form.querySelectorAll("input, select, textarea, file, hidden, date");
    return fields;
  }

  /**
   * Create FormData for use in fetch requests 
   * @param {string} form_id 
   * @param {object} data - {"name": "My Name", "email": "My Email"}
   * @returns {object}
   */
  methods.formData = function (form_id, data = null) {
    let fields = this.formFields(form_id);
    let formData = new FormData();
    if (data) {
      for (const item in data) formData.append(item, data[item]);
    }
    fields.forEach((e) => {
      let type = e.getAttribute('type');
      let name = e.getAttribute("name");
      if (type === "date") {
        formData.append(name, e.value);
        if (e.value) formData.append(`${name}_timestamp`, e.valueAsNumber);
      } else if (type === 'file') {
        formData.append(name, e.files[0]);
      } else {
        formData.append(name, e.value);
      }
    });
    return formData;
  }

  /**
   * Reset/clear all form fields values
   * @param {string} form_id css id 
   */
  methods.formClear = function (form_id) {
    let fields = this.formFields(form_id);
    fields.forEach((e) => {
      let type = e.getAttribute("type");
      if (type !== "submit" && type !== "hidden" && type !== "button") e.value = "";
    });
  }

  /**
   * Set form field values
   * @param {string} form_id 
   * @param {object} obj {id: '123', title: 'My Title'...} 
   */
  methods.formSetVals = function (form_id, obj) {
    const form = document.getElementById(form_id);
    for (const property in obj) {
      let name = property;
      let value = obj[property]
      let input = form.querySelector(`[name='${name}']`);
      input.value = value;
    }
  }

  /* =========================================================== 
    HTMX
  =========================================================== */

  methods.htmxModal = function (modalID = "htmx-modal") {
    let isHtmxElement = event.target.hasAttribute("hx-target") ? true : false;
    let htmxEl = isHtmxElement ? event.target : event.target.closest("[hx-target]");
    htmxEl.addEventListener("htmx:afterOnLoad", function () {
      let modal = window.document.getElementById(modalID);
      UIkit.modal(modal).show();
      UIkit.util.on(`#${modalID}`, 'hidden', function () {
        modal.remove();
      });
    });
  }

  methods.htmxOffcanvas = function (offcanvasID = "htmx-offcanvas") {
    let isHtmxElement = event.target.hasAttribute("hx-target") ? true : false;
    let htmxEl = isHtmxElement ? event.target : event.target.closest("[hx-target]");
    htmxEl.addEventListener("htmx:afterOnLoad", function () {
      let offcanvas = window.document.getElementById(offcanvasID);
      UIkit.offcanvas(offcanvas).show();
      UIkit.util.on(`#${offcanvasID}`, 'hidden', function () {
        offcanvas.remove();
      });
    })
  }

  /**
   * Sync / reload content of the elements using htmx.ajax
   * All elements that have data-htmx attribute.
   * Also works with iframes
   * @example <div id='my-content' data-htmx='path_to_my_file'></div>
   */
  methods.htmxSync = function (items = false, iframe = false, forceModalClose = false) {

    let htmxEl;

    if (items) {
      htmxEl = items;
    } else {
      let htmxElement = document.querySelectorAll(`[data-htmx]`);
      let htmxElementIframe = parent.document.querySelectorAll(`[data-htmx]`); // from within the iframe
      htmxEl = iframe ? htmxElementIframe : htmxElement;
    }

    if (htmxEl) {

      htmxEl.forEach(e => {

        let id = e.getAttribute('id');
        let closeModal = e.getAttribute('data-close-modal');
        let toCloseModal = forceModalClose || (closeModal && closeModal === 'true') ? true : false;

        if (!toCloseModal) {
          let pageEditModalIndicator = parent.document.querySelector('.page-edit-modal-indicator');
          if (pageEditModalIndicator) pageEditModalIndicator.classList.remove('uk-hidden');
        }

        if (id) {

          let path = e.getAttribute('data-htmx');
          let url = `./?htmx=${path}`;
          let dataVals = e.getAttribute('data-vals');
          let vals = JSON.parse(dataVals);

          let options = {
            target: `#${id}`,
            source: `#${id}`,
            swap: 'innerHTML',
            values: vals
          };

          if (iframe) {
            parent.htmx.ajax('GET', url, options);
          } else {
            htmx.ajax('GET', url, options);
          }

          let modals = iframe ? parent.document.querySelectorAll(".uk-modal:not(.htmx-sync-open)") : document.querySelectorAll(".uk-modal:not(.htmx-sync-open)");

          if (modals && toCloseModal) {
            modals.forEach(el => {
              UIkit.modal(el).hide();
            })
          }

        }
      });

    }

  }

  /* =========================================================== 
    UI
  =========================================================== */

  /**
   *  Display modal confirm
   *  this will redirect to the href attribute on confirm
   *  @example onclick="adminHelper.confirm('Are you sure?', 'More text...')"
   */
  methods.confirm = function (title = "Are you sure?", text = "") {
    event.preventDefault();
    let e = event.target;
    if (e.tagName != 'A' && e.tagName != 'BUTTON') {
      e = e.closest("a");
      if (!e) e = e.closest("button");
    }
    // Close drop menu if exists
    let drop = e.closest(".uk-drop");
    if (drop) UIkit.drop(drop).hide();
    // Set message
    let message = `<div class='uk-text-large uk-text-bold uk-text-center uk-margin-remove'>${title}</div>`;
    message += (text != "") ? `<p class='uk-text-center uk-margin-small'>${text}</p>` : "";
    // Show modal
    UIkit.modal.confirm(message).then(function () {
      e = e.hasAttribute('href') ? e : e.parentNode;
      let thisHref = e.getAttribute('href');
      window.location.replace(thisHref);
    }, function () {
      // console.log('Rejected.')
    });
  }

  /**
   * Toggle class on a target elements
   * Add class on the clicked element, remove from all other 
   * @param {string} target 
   * @param {string} cls 
   * @example <a href="#" onclick='toggleNav('.menu-item')'></a>
   */
  methods.toggleNav = function (target, cls = "uk-active") {
    let _this = event.target.closest(target);
    _this.classList.add(cls);
    let items = document.querySelectorAll(target);
    if (items) {
      items.forEach(e => {
        if (e != _this) e.classList.remove(cls);
      });
    }
  }

  /**
   * On type (input text) toggle active class and icon
   * on next sibling element (button)
   */
  methods.inputTypeConfirm = function (html = '<i class="fa fa-search"></i>') {

    let input = event.target;
    let target = input.nextElementSibling;
    let chars_typed = input.value.length;

    if (target) {

      target.classList.add('active');
      target.innerHTML = html;

      input.addEventListener('change', function () {
        target.innerHTML = '<i class="fa fa-check"></i>';
        if (!input.value || input.value == '') target.classList.remove('active');
      });

    } else {
      target.classList.remove('active');
    }

  }

  /* =========================================================== 
    Pages
  =========================================================== */

  /**
   * Publish/Unpublish Page
   * @param {int} id PageID
   */
  methods.togglePage = async function (id, reload = false) {
    event.preventDefault();
    let tr = event.target.closest("tr");
    let icon = event.target.closest("i");
    if (icon) icon.classList.add("cog-spin");
    const req = await fetch(`./?action=publish-ajax&id=${id}`);
    let response = await req.json();
    if (icon) icon.classList.remove("cog-spin");
    // if (response) this.ajaxResponse(response);
    if (reload) {
      window.location.href = reload;
    } else {
      if (response) this.ajaxResponse(response);
      this.htmxSync();
    }
  }

  /**
   * Move page to trash 
   * @param {int} id 
   */
  methods.trashPage = async function (id, reload = false) {
    event.preventDefault();
    let tr = event.target.closest("tr");
    const confirm = await UIkit.modal.confirm('<div class="uk-text-large uk-text-center">Are you sure?</div>').then(function () {
      return true;
    }, function () {
      return false;
    });
    if (confirm) {
      const req = await fetch(`./?action=trash-ajax&id=${id}`);
      let response = await req.json();
      if (response) this.ajaxResponse(response);
      //if (tr) tr.remove();
      if (reload) {
        window.location.href = reload;
      } else {
        this.htmxSync();
      }
    }
  }

  /**
   * Delete page
   * @param {int} id 
   */
  methods.deletePage = async function (id, reload = false) {
    event.preventDefault();
    let tr = event.target.closest("tr");
    const confirm = await UIkit.modal.confirm('<div class="uk-text-large uk-text-center">Are you sure?</div>').then(function () {
      return true;
    }, function () {
      return false;
    });
    if (confirm) {
      const req = await fetch(`./?ajax_action=trash&id=${id}`);
      //if (tr) tr.remove();
      if (reload) {
        window.location.href = reload;
      } else {
        this.htmxSync();
      }
    }
  }

  /* =========================================================== 
    Pages Bulk
  =========================================================== */

  /**
   * Get Checked Checkboxes
   * @returns array
   */
  methods.getCheckboxes = function () {
    let checkboxes = document.querySelectorAll("table input[name='admin_items[]']");
    let checked = [];
    checkboxes.forEach(e => {
      if (e.checked) checked.push(e.value);
    });
    return checked;
  }

  /**
   * Do a bulk action
   * @param {string} action_name 
   */
  methods.bulkAction = async function (action_name) {
    event.preventDefault();

    let items = getCheckboxes();
    if (items.length < 1) {
      UIkit.notification({
        message: 'No items selected',
        status: 'primary',
        pos: 'top-center',
        timeout: 500
      });
      return;
    }

    const confirm = await UIkit.modal.confirm('<div class="uk-text-large uk-text-center">Are you sure?</div>').then(function () {
      return true;
    }, function () {
      return false;
    });

    if (confirm && items.length > 0) {

      let strArray = JSON.stringify(items);
      // console.log(items);
      let formData = new FormData();
      formData.append("ajax_bulk_action", action_name);
      formData.append("admin_items", strArray);

      const response = await fetch("./?action=bulk-action-ajax", {
        method: "POST",
        body: formData
      })

      const data = await response.json();

      // console.log(data)
      // console.log(action_name)


      items.forEach(e => {

        let input = document.querySelector(`input[value='${e}']`);
        input.checked = false;
        let tr = input.closest("tr");

        if (tr && action_name == "publish") {
          tr.classList.toggle("is-hidden");
        }

        if (tr && (action_name === "delete" || action_name === "trash")) {
          tr.remove();
          if (data.count < 1) window.location.href = "./";
        }

      });

    }

  }

  // Expose the public methods
  return methods;

})();

const helper = adminHelper;