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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/dynamic-filter-gallery.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/dynamic-filter-gallery.js":
/*!***********************************************!*\
  !*** ./src/js/view/dynamic-filter-gallery.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var DynamicFilterableGallery = function DynamicFilterableGallery($scope, $) {\n  var $gallery = $(\".eael-filter-gallery-container\", $scope),\n    $settings = $gallery.data(\"settings\"),\n    $layout_mode = $settings.layout_mode === \"masonry\" ? \"masonry\" : \"fitRows\";\n  var $isotope_gallery = $gallery.isotope({\n    itemSelector: \".dynamic-gallery-item\",\n    layoutMode: $layout_mode,\n    percentPosition: true,\n    stagger: 30,\n    transitionDuration: $settings.duration + \"ms\"\n  });\n  $isotope_gallery.imagesLoaded().progress(function () {\n    $isotope_gallery.isotope(\"layout\");\n  });\n  $(\".dynamic-gallery-item\", $gallery).resize(function () {\n    $isotope_gallery.isotope(\"layout\");\n  });\n  $scope.on(\"click\", \".control\", function (e) {\n    e.preventDefault();\n    var $this = $(this),\n      filterValue = $this.data(\"filter\");\n    $this.siblings().removeClass(\"active\");\n    $this.addClass(\"active\");\n    if ($this.data('initial-load') === undefined && filterValue !== '*') {\n      $this.closest('.eael-filter-gallery-wrapper').find('button.eael-load-more-button').trigger('click');\n      $this.data('initial-load', 'loaded');\n    }\n    $isotope_gallery.isotope({\n      filter: filterValue\n    });\n    if ($this.hasClass('no-more-posts')) {\n      $this.closest('.eael-filter-gallery-wrapper').find('.eael-load-more-button').addClass('hide');\n    } else {\n      $this.closest('.eael-filter-gallery-wrapper').find('.eael-load-more-button').removeClass('hide');\n    }\n  });\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-dynamic-filterable-gallery.default\", DynamicFilterableGallery);\n});\n\n//# sourceURL=webpack:///./src/js/view/dynamic-filter-gallery.js?");

/***/ })

/******/ });