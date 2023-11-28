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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/lightbox.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/lightbox.js":
/*!*********************************!*\
  !*** ./src/js/view/lightbox.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var LightBox = function LightBox($scope, $) {\n  var $lightBox = $scope.find(\".eael-lightbox-wrapper\").eq(0),\n    $main_class = $lightBox.data(\"main-class\") !== undefined ? $lightBox.data(\"main-class\") : \"\",\n    $popup_layout = $lightBox.data(\"popup-layout\") !== undefined ? $lightBox.data(\"popup-layout\") : \"\",\n    $close_button = $lightBox.data(\"close_button\") === \"yes\" ? true : false,\n    $effect = $lightBox.data(\"effect\") !== undefined ? $lightBox.data(\"effect\") : \"\",\n    $type = $lightBox.data(\"type\") !== undefined ? $lightBox.data(\"type\") : \"\",\n    $iframe_class = $lightBox.data(\"iframe-class\") !== undefined ? $lightBox.data(\"iframe-class\") : \"\",\n    $src = $lightBox.data(\"src\") !== undefined ? $lightBox.data(\"src\") : \"\",\n    $trigger_element = $lightBox.data(\"trigger-element\") !== undefined ? $lightBox.data(\"trigger-element\") : \"\",\n    $delay = $lightBox.data(\"delay\") != \"\" ? $lightBox.data(\"delay\") : 0,\n    $trigger = $lightBox.data(\"trigger\") !== undefined ? $lightBox.data(\"trigger\") : \"\",\n    $popup_id = $lightBox.data(\"lightbox-id\") !== undefined ? $lightBox.data(\"lightbox-id\") : \"\",\n    $display_after = $lightBox.data(\"display-after\") !== undefined ? $lightBox.data(\"display-after\") : \"\",\n    $esc_exit = $lightBox.data(\"esc_exit\") === \"yes\" ? true : false,\n    $click_exit = $lightBox.data(\"click_exit\") === \"yes\" ? true : false;\n  $main_class += \" \" + $popup_layout + \" \" + $effect;\n  if (\"eael-lightbox-popup-fullscreen\" == $popup_layout) {\n    var win_height = $(window).height() - 20;\n    $(\".eael-lightbox-container.content-type-image-now\").css({\n      \"max-height\": win_height + \"px\",\n      \"margin-top\": \"10px\"\n    });\n  }\n  if (\"eael_lightbox_trigger_exit_intent\" == $trigger) {\n    var flag = true,\n      mouseY = 0,\n      topValue = 0;\n    if ($display_after === 0) {\n      $.removeCookie($popup_id, {\n        path: \"/\"\n      });\n    }\n    window.addEventListener(\"mouseout\", function (e) {\n      mouseY = e.clientY;\n      if (mouseY < topValue && !$.cookie($popup_id)) {\n        $.magnificPopup.open({\n          items: {\n            src: $src //ID of inline element\n          },\n\n          iframe: {\n            markup: '<div class=\"' + $iframe_class + '\">' + '<div class=\"modal-popup-window-inner\">' + '<div class=\"mfp-iframe-scaler\">' + '<div class=\"mfp-close\"></div>' + '<iframe class=\"mfp-iframe\" frameborder=\"0\" allowfullscreen></iframe>' + \"</div>\" + \"</div>\" + \"</div>\"\n          },\n          type: $type,\n          showCloseBtn: $close_button,\n          enableEscapeKey: $esc_exit,\n          closeOnBgClick: $click_exit,\n          removalDelay: 500,\n          //Delaying the removal in order to fit in the animation of the popup\n          mainClass: $main_class\n        });\n        ea.hooks.doAction(\"ea-lightbox-triggered\", $src);\n        $(document).trigger('eael-lightbox-open');\n        if ($display_after > 0) {\n          $.cookie($popup_id, $display_after, {\n            expires: $display_after,\n            path: \"/\"\n          });\n        } else {\n          $.removeCookie($popup_id);\n        }\n      }\n    }, false);\n  } else if (\"eael_lightbox_trigger_pageload\" == $trigger) {\n    if ($display_after === 0) {\n      $.removeCookie($popup_id, {\n        path: \"/\"\n      });\n    }\n    if (!$.cookie($popup_id)) {\n      setTimeout(function () {\n        $.magnificPopup.open({\n          items: {\n            src: $src\n          },\n          iframe: {\n            markup: '<div class=\"' + $iframe_class + '\">' + '<div class=\"modal-popup-window-inner\">' + '<div class=\"mfp-iframe-scaler\">' + '<div class=\"mfp-close\"></div>' + '<iframe class=\"mfp-iframe\" frameborder=\"0\" allowfullscreen></iframe>' + \"</div>\" + \"</div>\" + \"</div>\"\n          },\n          type: $type,\n          showCloseBtn: $close_button,\n          enableEscapeKey: $esc_exit,\n          closeOnBgClick: $click_exit,\n          mainClass: $main_class\n        });\n        ea.hooks.doAction(\"ea-lightbox-triggered\", $src);\n        $(document).trigger('eael-lightbox-open');\n        if ($display_after > 0) {\n          $.cookie($popup_id, $display_after, {\n            expires: $display_after,\n            path: \"/\"\n          });\n        } else {\n          $.removeCookie($popup_id);\n        }\n      }, $delay);\n    }\n  } else {\n    if (typeof $trigger_element === \"undefined\" || $trigger_element === \"\") {\n      $trigger_element = \".eael-modal-popup-link\";\n    }\n    $scope.on('keydown', $trigger_element, function (e) {\n      if (e.which === 13 || e.which === 32) {\n        $(this).trigger('click');\n      }\n    });\n    $($trigger_element).magnificPopup({\n      image: {\n        markup: '<div class=\"' + $iframe_class + '\">' + '<div class=\"modal-popup-window-inner\">' + '<div class=\"mfp-figure\">' + '<div class=\"mfp-close\"></div>' + '<div class=\"mfp-img\"></div>' + '<div class=\"mfp-bottom-bar\">' + '<div class=\"mfp-title\"></div>' + '<div class=\"mfp-counter\"></div>' + \"</div>\" + \"</div>\" + \"</div>\" + \"</div>\"\n      },\n      iframe: {\n        markup: '<div class=\"' + $iframe_class + '\">' + '<div class=\"modal-popup-window-inner\">' + '<div class=\"mfp-iframe-scaler\">' + '<div class=\"mfp-close\"></div>' + '<iframe class=\"mfp-iframe\" frameborder=\"0\" allowfullscreen></iframe>' + \"</div>\" + \"</div>\" + \"</div>\"\n      },\n      items: {\n        src: $src,\n        type: $type\n      },\n      removalDelay: 500,\n      showCloseBtn: $close_button,\n      enableEscapeKey: $esc_exit,\n      closeOnBgClick: $click_exit,\n      mainClass: $main_class,\n      callbacks: {\n        open: function open() {\n          ea.hooks.doAction(\"ea-lightbox-triggered\", $src);\n          $(document).trigger('eael-lightbox-open');\n        }\n      },\n      type: 'inline'\n    });\n  }\n  $.extend(true, $.magnificPopup.defaults, {\n    tClose: \"Close\"\n  });\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  if (ea.elementStatusCheck('eaelLightboxLoad')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-lightbox.default\", LightBox);\n});\n\n//# sourceURL=webpack:///./src/js/view/lightbox.js?");

/***/ })

/******/ });