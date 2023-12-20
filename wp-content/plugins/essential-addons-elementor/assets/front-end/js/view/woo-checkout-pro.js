/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/woo-checkout-pro.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/woo-checkout-pro.js":
/*!*****************************************!*\
  !*** ./src/js/view/woo-checkout-pro.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var WooCheckoutPro = function WooCheckoutPro($scope, $) {\n  var $ea_woo_checkout_pro = $scope.find(\".ea-woo-checkout\").eq(0);\n  var tabs_wrapper, tabs, tabs_content, tabFirstTab, tabs_panel, tabLastTab, tabs_common_class, first_step_tab, tabs_common_class_without_tag, first_prev, last_next, layout_type, coupon;\n\n  // Common variable\n  var button_prev = $('.ea-woo-checkout-btn-prev'),\n    button_next = $('.ea-woo-checkout-btn-next'),\n    first_step = 0,\n    last_step = 0,\n    active_step = 1;\n  if ($ea_woo_checkout_pro.hasClass(\"layout-split\")) {\n    tabs_panel = 'split-tab-panel';\n    tabs_wrapper = $('.layout-split-container');\n    coupon = tabs_wrapper.data('coupon');\n    tabs = $('.split-tabs');\n    tabs_content = $('.split-tabs-content');\n    tabFirstTab = 'li.split-tab.first';\n    tabLastTab = 'li.split-tab.last';\n    tabs_common_class = 'li.split-tab';\n    tabs_common_class_without_tag = 'split-tab';\n    layout_type = 'split';\n    first_prev = 'split-first-prev';\n    last_next = 'split-last-next';\n  } else if ($ea_woo_checkout_pro.hasClass(\"layout-multi-steps\")) {\n    tabs_panel = 'ms-tab-panel';\n    tabs_wrapper = $('.layout-multi-steps-container');\n    coupon = tabs_wrapper.data('coupon');\n    tabs = $('.ms-tabs');\n    tabs_content = $('.ms-tabs-content');\n    tabFirstTab = 'li.ms-tab.first';\n    tabLastTab = 'li.ms-tab.last';\n    tabs_common_class = 'li.ms-tab';\n    tabs_common_class_without_tag = 'ms-tab';\n    layout_type = 'multi';\n    first_prev = 'ms-first-prev';\n    last_next = 'ms-last-next';\n  }\n\n  // Common variable\n  var button_prev = $('.ea-woo-checkout-btn-prev');\n  var button_next = $('.ea-woo-checkout-btn-next');\n  var first_step = 0;\n  var last_step = 0;\n  var active_step = 1;\n  $(\".woo-checkout-login, .woo-checkout-coupon, #customer_details, .woo-checkout-payment\").addClass(tabs_panel);\n  $('.woo-checkout-login').addClass(tabs_panel + '-0');\n  if (coupon == 1) {\n    $('.woo-checkout-coupon').addClass(tabs_panel + '-1');\n    $('#customer_details').addClass(tabs_panel + '-2');\n    $('.woo-checkout-payment').addClass(tabs_panel + '-3');\n  } else {\n    $('#customer_details').addClass(tabs_panel + '-1');\n    $('.woo-checkout-payment').addClass(tabs_panel + '-2');\n  }\n\n  //Common function\n  function validate_email(email) {\n    var reg = /^([A-Za-z0-9_\\-\\.])+\\@([A-Za-z0-9_\\-\\.])+\\.([A-Za-z]{2,})$/;\n    if (reg.test(email) == false) {\n      return false;\n    }\n    return true;\n  }\n  function isEmpty(str) {\n    return !str || 0 === str.length;\n  }\n  function get_field_value(type, elm, name) {\n    var value = '';\n    switch (type) {\n      case 'radio':\n        value = $(\"input[type=radio][name='\" + name + \"']:checked\").val();\n        value = value ? value : '';\n        break;\n      case 'checkbox':\n        if (elm.data('multiple') == 1) {\n          var valueArr = [];\n          $(\"input[type=checkbox][name='\" + name + \"']:checked\").each(function () {\n            valueArr.push($(this).val());\n          });\n          value = valueArr;\n          if ($.isEmptyObject(value)) {\n            value = \"\";\n          }\n        } else {\n          value = $(\"input[type=checkbox][name='\" + name + \"']:checked\").val();\n          value = value ? value : '';\n        }\n        break;\n      case 'select':\n        value = elm.val();\n        break;\n      case 'multiselect':\n        value = elm.val();\n        break;\n      case 'hidden':\n        value = $(\"input[type=hidden][name='\" + name + \"']\").val();\n        break;\n      default:\n        value = elm.val();\n        break;\n    }\n    return value;\n  }\n  function initialize_split_multistep_tab() {\n    if (tabs_wrapper && tabs_wrapper.length) {\n      first_step_tab = tabs.find(tabFirstTab);\n      first_step = first_step_tab.data('step');\n      last_step = tabs.find(tabLastTab).data('step');\n      if (layout_type == \"split\") {\n        jump_to_step(first_step, first_step_tab);\n      } else if (layout_type == \"multi\") {\n        jump_to_step_multistep(first_step, first_step_tab);\n      }\n      tabs.find(tabs_common_class).click(function () {\n        var step_number = $(this).data('step');\n        if (step_number < active_step) {\n          if (layout_type == \"split\") {\n            jump_to_step(step_number, $(this));\n          } else if (layout_type == \"multi\") {\n            jump_to_step_multistep(step_number, $(this));\n          }\n        }\n      });\n      button_prev.click(function () {\n        $(window).scrollTop($ea_woo_checkout_pro.offset().top - 50);\n        var step_number = active_step - 1;\n        if (step_number >= first_step) {\n          if (layout_type == \"split\") {\n            jump_to_step(step_number, false);\n          } else if (layout_type == \"multi\") {\n            jump_to_step_multistep(step_number, false);\n          }\n        }\n      });\n      button_next.click(function () {\n        $(window).scrollTop($ea_woo_checkout_pro.offset().top - 50);\n        var step_number = active_step + 1;\n        if (step_number <= last_step) {\n          if (layout_type == \"split\") {\n            validate_checkout_step(active_step, step_number);\n          } else if (layout_type == \"multi\") {\n            validate_checkout_step_multi_step(active_step, step_number);\n          }\n        }\n      });\n    }\n  }\n  function validate_checkout_step(active_step, next_step) {\n    var valid = validate_step_fields(active_step);\n    if (valid) {\n      tabs.find('.step-' + active_step).addClass('split-finished-step');\n      jump_to_step(next_step, false);\n    } else {\n      scrol_to_error();\n    }\n  }\n  function scrol_to_error() {\n    var topPosition = $('.ea-woo-checkout');\n    if (topPosition) {\n      var top = topPosition.offset().top - 50;\n      document.body.scrollTop = top;\n      document.documentElement.scrollTop = top;\n    }\n  }\n  function display_error_message(msg) {\n    var error_div = $('.woocommerce-error');\n    var error = '<ul class=\"woocommerce-error\" role=\"alert\">' + msg + '</ul>';\n    tabs_content.prepend('<div class=\"woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout\">' + error + '</div>');\n  }\n  function clear_validation_error() {\n    $('.ea-wrapper .woocommerce-NoticeGroup-checkout, .ea-wrapper .woocommerce-error, .ea-wrapper .woocommerce-message, .woocommerce .woocommerce-error').remove();\n  }\n  function validate_step_fields(active_step) {\n    clear_validation_error();\n    var active_section = $('.' + tabs_panel + '-' + active_step);\n    if (active_section) {\n      var all_inputs = active_section.find(\":input\").not('.ea-disabled-field, .woocommerce-validated');\n      var ship_to_different_address = $('input[name=\"ship_to_different_address\"]');\n      var is_account_field = $('#createaccount');\n      var valid = true;\n      var msg = '<p>error msg</p>';\n      var errorMsgTxt = '';\n      var liTagStart = \"<li>\";\n      var liTagEnd = \"</li>\";\n      var dataSet = {};\n      $.each(all_inputs, function (field) {\n        var type = $(this).getType();\n        var name = $(this).attr('name');\n        if (type == 'checkbox' || type == 'select') {\n          var formated_name = name.replace('[]', '');\n          var parent = $('#' + formated_name + '_field');\n        } else {\n          var parent = $('#' + name + '_field');\n        }\n        var is_shipping_field = parent.parents('.shipping_address');\n\n        //Check - is shipping address enable from woo commerce setting that means this section exist or not and second condition is\n        // Is ship to different address checkbox is checked or not So if condition fullfilled ignore the validation that means return\n        //valid and othersie those have to validate\n        if (is_shipping_field.length > 0 && ship_to_different_address.prop('checked') != true) {\n          return valid;\n        }\n        var value = get_field_value(type, $(this), name);\n        dataSet[name] = value;\n        if (parent.hasClass('validate-required')) {\n          if (isEmpty(value)) {\n            var label = $(\"label[for=\" + name + \"]\").clone().children().remove().end().text();\n            var getBillingOrShippingText = name.split('_');\n            getBillingOrShippingText = getBillingOrShippingText[0];\n            if (getBillingOrShippingText == 'billing') {\n              getBillingOrShippingText = localize.eael_translate_text.billing_text;\n            }\n            if (getBillingOrShippingText == 'shipping') {\n              getBillingOrShippingText = localize.eael_translate_text.shipping_text;\n            }\n            var singleErrorMsgTxt = liTagStart + getBillingOrShippingText + ' ' + label + localize.eael_translate_text.required_text + liTagEnd;\n            errorMsgTxt += singleErrorMsgTxt;\n            valid = false;\n            scrol_to_error();\n          }\n        } else if (parent.hasClass('validate-email')) {\n          var valid_email = validate_email(value);\n          if (!valid_email) {\n            var label = $(\"label[for=\" + name + \"]\").clone().children().remove().end().text();\n            var getBillingOrShippingText = name.split('_');\n            getBillingOrShippingText = getBillingOrShippingText[0];\n            var singleErrorMsgTxt = liTagStart + localize.eael_translate_text.invalid_text + getBillingOrShippingText + ' ' + label + liTagEnd;\n            errorMsgTxt += singleErrorMsgTxt;\n            valid = false;\n            scrol_to_error();\n          }\n        } else if (parent.hasClass('validate-postcode')) {\n          var country;\n          if (name == 'billing_postcode') {\n            var _dataSet$billing_coun;\n            country = (_dataSet$billing_coun = dataSet === null || dataSet === void 0 ? void 0 : dataSet['billing_country']) !== null && _dataSet$billing_coun !== void 0 ? _dataSet$billing_coun : '';\n          } else if (name == 'shipping_postcode') {\n            var _dataSet$shipping_cou;\n            country = (_dataSet$shipping_cou = dataSet === null || dataSet === void 0 ? void 0 : dataSet['shipping_country']) !== null && _dataSet$shipping_cou !== void 0 ? _dataSet$shipping_cou : '';\n          }\n          $.ajax({\n            type: 'POST',\n            url: localize.ajaxurl,\n            async: false,\n            data: {\n              action: 'woo_checkout_post_code_validate',\n              data: {\n                country: country,\n                postcode: value\n              }\n            },\n            success: function success(data) {\n              if (!(data !== null && data !== void 0 && data.valid)) {\n                //$(\"#step-2\").trigger(\"click\");\n                var label = $(\"label[for=\" + name + \"]\").clone().children().remove().end().text();\n                var getBillingOrShippingText = name.split('_');\n                getBillingOrShippingText = getBillingOrShippingText[0];\n                var singleErrorMsgTxt = liTagStart + data.message + ' ' + liTagEnd;\n                errorMsgTxt += singleErrorMsgTxt;\n                valid = false;\n                scrol_to_error();\n              }\n            }\n          });\n        }\n      });\n      if (!valid) {\n        display_error_message(errorMsgTxt);\n      }\n    }\n    return valid;\n  }\n  $.fn.getType = function () {\n    try {\n      return this[0].tagName == \"INPUT\" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase();\n    } catch (err) {\n      return 'E001';\n    }\n  };\n  function jump_to_step(step_number, step) {\n    if (!step) {\n      step = tabs.find('#step-' + step_number);\n    }\n    var numberOfTabs = $('.' + tabs_common_class_without_tag).length;\n    for (var x = 1; x <= numberOfTabs; x++) {\n      var nextStepNumber = step_number + x;\n      if (nextStepNumber <= numberOfTabs) {\n        $(\"[data-step=\" + nextStepNumber + \"]\").removeClass('completed');\n      }\n    }\n    tabs.find('li').removeClass('active');\n    var active_tab_panel = tabs_wrapper.find('.split-tab-panel-' + step_number);\n    if (!step.hasClass(\"completed\")) {\n      step.addClass(\"completed\");\n    }\n    if (!step.hasClass(\"active\")) {\n      step.addClass(\"active\");\n    }\n    tabs_wrapper.find('div.' + tabs_panel).not('.' + tabs_panel + '-' + step_number).hide();\n    active_tab_panel.show();\n    active_step = step_number;\n    button_prev.prop('disabled', false);\n    button_next.prop('disabled', false);\n    button_prev.removeClass(first_prev);\n    button_next.removeClass(last_next);\n    button_next.data('next');\n    button_next.show();\n    $(\"#ea_place_order\").hide();\n    button_prev.show();\n    if (active_step == first_step) {\n      button_prev.prop('disabled', true);\n      button_prev.addClass('split-first-prev');\n      button_prev.hide();\n    }\n    if (active_step == last_step) {\n      button_next.prop('disabled', false);\n      button_next.addClass('split-last-next');\n      button_next.hide();\n      $(\"#ea_place_order\").show();\n    }\n  }\n  function validate_checkout_step_multi_step(active_step, next_step) {\n    var valid = validate_step_fields(active_step);\n    if (valid) {\n      tabs.find('.step-' + active_step).addClass('ms-finished-step');\n      jump_to_step_multistep(next_step, false);\n    } else {\n      // display_error_message(msg);\n      scrol_to_error();\n    }\n  }\n  function jump_to_step_multistep(step_number, step) {\n    if (!step) {\n      step = tabs.find('#step-' + step_number);\n    }\n    var numberOfTabs = $('.' + tabs_common_class_without_tag).length;\n    for (var x = 1; x <= numberOfTabs; x++) {\n      var nextStepNumber = step_number + x;\n      if (nextStepNumber <= numberOfTabs) {\n        $(\"[data-step=\" + nextStepNumber + \"]\").removeClass('completed');\n      }\n    }\n    tabs.find('li').removeClass('active');\n    var active_tab_panel = tabs_wrapper.find('.ms-tab-panel-' + step_number);\n    if (!step.hasClass(\"completed\")) {\n      step.addClass(\"completed\");\n    }\n    if (!step.hasClass(\"active\")) {\n      step.addClass(\"active\");\n    }\n    tabs_wrapper.find('div.ms-tab-panel').not('.ms-tab-panel-' + step_number).hide();\n    active_tab_panel.show();\n    active_step = step_number;\n    button_prev.prop('disabled', false);\n    button_next.prop('disabled', false);\n    button_prev.removeClass('ms-first-prev');\n    button_next.removeClass('ms-last-next');\n    button_next.data('next');\n    button_next.show();\n    $(\"#ea_place_order\").hide();\n    button_prev.show();\n    if (active_step == first_step) {\n      button_prev.prop('disabled', true);\n      button_prev.addClass('ms-first-prev');\n      button_prev.hide();\n    }\n    if (active_step == last_step) {\n      button_next.prop('disabled', false);\n      button_next.addClass('ms-last-next');\n      button_next.hide();\n      $(\"#ea_place_order\").show();\n    }\n  }\n  initialize_split_multistep_tab();\n  if (tabs_wrapper) {\n    tabs_wrapper.on('click', '#ea_place_order', function () {\n      $(\"#place_order\").trigger(\"click\");\n    });\n  }\n  if ($('.ea-woo-checkout', $scope).hasClass('layout-multi-steps') || $('.ea-woo-checkout', $scope).hasClass('layout-split')) {\n    $(document).ajaxComplete(function () {\n      var login_btn = $('.ea-woo-checkout .woocommerce-NoticeGroup-checkout a.showlogin', $scope);\n      login_btn.on('click', function (e) {\n        $('#step-0', $scope).trigger('click');\n      });\n    });\n  }\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-woo-checkout.default\", WooCheckoutPro);\n});\n\n//# sourceURL=webpack:///./src/js/view/woo-checkout-pro.js?");

/***/ })

/******/ });