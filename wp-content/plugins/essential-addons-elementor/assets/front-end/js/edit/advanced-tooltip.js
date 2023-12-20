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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/edit/advanced-tooltip.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/edit/advanced-tooltip.js":
/*!*****************************************!*\
  !*** ./src/js/edit/advanced-tooltip.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var EaelGlobalTooltip = function EaelGlobalTooltip($scope, $) {\n  var target = $scope,\n    sectionId = target.data(\"id\"),\n    editMode = elementorFrontend.isEditMode();\n  if (editMode) {\n    var editorElements = null,\n      sectionData = {},\n      settings = {};\n    if (!window.elementor.hasOwnProperty(\"elements\")) {\n      return false;\n    }\n    editorElements = window.elementor.elements;\n    if (!editorElements.models) {\n      return false;\n    }\n    var prepare_settings_data = function prepare_settings_data(sectionData) {\n      settings[\"switch\"] = sectionData[\"eael_tooltip_section_enable\"];\n      settings.content = sectionData[\"eael_tooltip_section_content\"];\n      settings.position = sectionData[\"eael_tooltip_section_position\"];\n      settings.animation = sectionData[\"eael_tooltip_section_animation\"];\n      settings.arrow = sectionData[\"eael_tooltip_section_arrow\"];\n      settings.arrowType = sectionData[\"eael_tooltip_section_arrow_type\"];\n      settings.duration = sectionData[\"eael_tooltip_section_duration\"];\n      settings.delay = sectionData[\"eael_tooltip_section_delay\"];\n      settings.size = sectionData[\"eael_tooltip_section_size\"];\n      settings.trigger = sectionData[\"eael_tooltip_section_trigger\"];\n      settings.flip = sectionData[\"eael_tooltip_auto_flip\"];\n      settings.distance = sectionData[\"eael_tooltip_section_distance\"];\n      settings.maxWidth = sectionData[\"eael_tooltip_section_width\"];\n      return settings;\n    };\n    $.each(editorElements.models, function (index, elem) {\n      if (elem.id === target.closest('.elementor-top-section').data('id')) {\n        $.each(elem.attributes.elements.models, function (index, col) {\n          $.each(col.attributes.elements.models, function (index, subSec) {\n            $.each(subSec.attributes.elements.models, function (index, subCol) {\n              $.each(subCol.attributes.elements.models, function (ind, subWidget) {\n                if (sectionId === subWidget.id) {\n                  sectionData = subWidget.attributes.settings.attributes;\n                  settings = prepare_settings_data(sectionData);\n                  if (settings[\"switch\"] === \"yes\") {\n                    target.addClass(\"eael-section-tooltip\");\n                    generateTooltip();\n                  } else {\n                    target.removeClass(\"eael-section-tooltip\");\n                  }\n                  if (0 !== settings.length) {\n                    return settings;\n                  }\n                }\n                if (!editMode || !settings) {\n                  return false;\n                }\n              });\n            });\n          });\n        });\n      }\n      if (elem.id === target.closest('.e-container').data('id') || sectionId === (target === null || target === void 0 ? void 0 : target[0].getAttribute('data-id'))) {\n        $.each(elem.attributes.elements.models, function (index, widget) {\n          if (sectionId === widget.id) {\n            sectionData = widget.attributes.settings.attributes;\n            settings = prepare_settings_data(sectionData);\n            if (settings[\"switch\"] === \"yes\") {\n              target.addClass(\"eael-section-tooltip\");\n              generateTooltip();\n            } else {\n              target.removeClass(\"eael-section-tooltip\");\n            }\n            if (0 !== settings.length) {\n              return settings;\n            }\n          }\n          if (!editMode || !settings) {\n            return false;\n          }\n        });\n      }\n      $.each(elem.attributes.elements.models, function (inde, column) {\n        $.each(column.attributes.elements.models, function (ind, widget) {\n          if (sectionId == widget.id) {\n            sectionData = widget.attributes.settings.attributes;\n            settings[\"switch\"] = sectionData[\"eael_tooltip_section_enable\"];\n            settings.content = sectionData[\"eael_tooltip_section_content\"];\n            settings.position = sectionData[\"eael_tooltip_section_position\"];\n            settings.animation = sectionData[\"eael_tooltip_section_animation\"];\n            settings.arrow = sectionData[\"eael_tooltip_section_arrow\"];\n            settings.arrowType = sectionData[\"eael_tooltip_section_arrow_type\"];\n            settings.duration = sectionData[\"eael_tooltip_section_duration\"];\n            settings.delay = sectionData[\"eael_tooltip_section_delay\"];\n            settings.size = sectionData[\"eael_tooltip_section_size\"];\n            settings.trigger = sectionData[\"eael_tooltip_section_trigger\"];\n            settings.flip = sectionData[\"eael_tooltip_auto_flip\"];\n            settings.distance = sectionData[\"eael_tooltip_section_distance\"];\n            settings.maxWidth = sectionData[\"eael_tooltip_section_width\"];\n            if (settings[\"switch\"] == \"yes\") {\n              target.addClass(\"eael-section-tooltip\");\n              generateTooltip();\n            } else {\n              target.removeClass(\"eael-section-tooltip\");\n            }\n            if (0 !== settings.length) {\n              return settings;\n            }\n          }\n          if (!editMode || !settings) {\n            return false;\n          }\n        });\n      });\n      function esc_HTML(raw) {\n        if (raw.search(/(<script>|<script type=\"text\\/javascript\">).*(<\\/script>)/g) > 0) {\n          return raw.replace(/[&<>\"']/g, function onReplace(match) {\n            return '&#' + match.charCodeAt(0) + ';';\n          });\n        } else {\n          return raw;\n        }\n      }\n      function generateTooltip() {\n        target.attr(\"id\", \"eael-section-tooltip-\" + sectionId);\n        var $currentTooltip = \"#\" + target.attr(\"id\");\n        tippy($currentTooltip, {\n          content: settings.content,\n          placement: settings.position,\n          animation: settings.animation,\n          arrow: settings.arrow,\n          arrowType: settings.arrowType,\n          duration: settings.duration,\n          distance: settings.distance,\n          delay: settings.content,\n          size: settings.size,\n          trigger: settings.trigger,\n          flip: settings.flip === 'yes',\n          flipBehavior: settings.flip === 'yes' ? 'flip' : [],\n          animateFill: false,\n          flipOnUpdate: true,\n          interactive: true,\n          maxWidth: settings.maxWidth,\n          zIndex: 99999,\n          onShow: function onShow(instance) {\n            settings.content = esc_HTML(sectionData[\"eael_tooltip_section_content\"]);\n            settings.position = sectionData[\"eael_tooltip_section_position\"];\n            settings.animation = sectionData[\"eael_tooltip_section_animation\"];\n            settings.arrow = sectionData[\"eael_tooltip_section_arrow\"];\n            settings.arrowType = sectionData[\"eael_tooltip_section_arrow_type\"];\n            settings.duration = sectionData[\"eael_tooltip_section_duration\"];\n            settings.delay = sectionData[\"eael_tooltip_section_delay\"];\n            settings.size = sectionData[\"eael_tooltip_section_size\"];\n            settings.trigger = sectionData[\"eael_tooltip_section_trigger\"];\n            settings.flip = sectionData[\"eael_tooltip_auto_flip\"];\n            settings.distance = sectionData[\"eael_tooltip_section_distance\"];\n            settings.maxWidth = sectionData[\"eael_tooltip_section_width\"];\n\n            // Get tooltip enable/disable status\n            settings[\"switch\"] = sectionData[\"eael_tooltip_section_enable\"];\n\n            // Disable tooltip\n            if (settings[\"switch\"] !== 'yes') {\n              instance.destroy();\n            } else {\n              instance.set({\n                content: settings.content,\n                placement: settings.position,\n                animation: settings.animation,\n                arrow: settings.arrow,\n                arrowType: settings.arrowType,\n                duration: settings.duration,\n                distance: settings.distance,\n                delay: settings.delay,\n                size: settings.size,\n                trigger: settings.trigger,\n                flip: settings.flip === 'yes',\n                flipBehavior: settings.flip === 'yes' ? 'flip' : [],\n                maxWidth: settings.maxWidth\n              });\n              var tippyPopper = instance.popper;\n              $(tippyPopper).attr('data-tippy-popper-id', sectionId);\n            }\n          }\n        });\n      }\n    });\n  }\n};\njQuery(window).on('elementor/frontend/init', function () {\n  elementorFrontend.hooks.addAction('frontend/element_ready/widget', EaelGlobalTooltip);\n});\n\n//# sourceURL=webpack:///./src/js/edit/advanced-tooltip.js?");

/***/ })

/******/ });