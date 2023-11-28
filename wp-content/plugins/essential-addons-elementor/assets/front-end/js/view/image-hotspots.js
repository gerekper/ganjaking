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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/image-hotspots.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/image-hotspots.js":
/*!***************************************!*\
  !*** ./src/js/view/image-hotspots.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var ImageHotspotHandler = function ImageHotspotHandler($scope, $) {\n  //fixed tooltip blink issue\n  // when body has position relative in wp login mode tipso top not working properly\n  var position = $(\"body.elementor-page.logged-in.admin-bar\");\n  if (position.css(\"position\") === \"relative\" && typeof $(\".eael-hot-spot-wrap\").data('tipso') !== 'undefined') {\n    position.css(\"position\", \"inherit\");\n  }\n  $('.eael-hot-spot-tooptip').each(function () {\n    var $position_local = $(this).data('tooltip-position-local'),\n      $position_global = $(this).data('tooltip-position-global'),\n      $width = $(this).data('tooltip-width'),\n      $size = $(this).data('tooltip-size'),\n      $animation_in = $(this).data('tooltip-animation-in'),\n      $animation_out = $(this).data('tooltip-animation-out'),\n      $animation_speed = $(this).data('tooltip-animation-speed'),\n      $animation_delay = $(this).data('tooltip-animation-delay'),\n      $background = $(this).data('tooltip-background'),\n      $text_color = $(this).data('tooltip-text-color'),\n      $arrow = $(this).data('eael-tooltip-arrow') === 'yes' ? true : false,\n      $position = $position_local;\n    if (typeof $position_local === 'undefined' || $position_local === 'global') {\n      $position = $position_global;\n    }\n    if (typeof $animation_out === 'undefined' || !$animation_out) {\n      $animation_out = $animation_in;\n    }\n    $(this).tipso({\n      speed: $animation_speed,\n      delay: $animation_delay,\n      width: $width,\n      background: $background,\n      color: $text_color,\n      size: $size,\n      position: $position,\n      animationIn: typeof $animation_in != 'undefined' ? 'animate__' + $animation_in : '',\n      animationOut: typeof $animation_out != 'undefined' ? 'animate__' + $animation_out : '',\n      showArrow: $arrow,\n      autoClose: true,\n      tooltipHover: true\n    });\n  });\n\n  // $('.eael-hot-spot-wrap').on('click', function (e) {\n  //     // e.preventDefault();\n  //     // e.stopImmediatePropagation();\n  //     $link = $(this).data('link')\n  //     $link_target = $(this).data('link-target')\n  //\n  //     if (typeof $link != 'undefined' && $link != '#') {\n  //\n  //         if ($link_target == '_blank') {\n  //\n  //             window.open($link)\n  //         } else {\n  //             alert('hash');\n  //             window.location.hash = $link\n  //         }\n  //     }\n  // })\n};\n\njQuery(window).on('elementor/frontend/init', function () {\n  elementorFrontend.hooks.addAction('frontend/element_ready/eael-image-hotspots.default', ImageHotspotHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/image-hotspots.js?");

/***/ })

/******/ });