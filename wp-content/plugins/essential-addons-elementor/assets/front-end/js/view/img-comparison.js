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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/img-comparison.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/img-comparison.js":
/*!***************************************!*\
  !*** ./src/js/view/img-comparison.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var ImageComparisonHandler = function ImageComparisonHandler($scope, $) {\n  var $img_comp = $(\".eael-img-comp-container\", $scope);\n  var $options = {\n    default_offset_pct: $img_comp.data(\"offset\") || 0.7,\n    orientation: $img_comp.data(\"orientation\") || \"horizontal\",\n    before_label: $img_comp.data(\"before_label\") || \"Before\",\n    after_label: $img_comp.data(\"after_label\") || \"After\",\n    no_overlay: $img_comp.data(\"overlay\") == \"yes\" ? false : true,\n    move_slider_on_hover: $img_comp.data(\"onhover\") == \"yes\" ? true : false,\n    move_with_handle_only: true,\n    click_to_move: $img_comp.data(\"onclick\") == \"yes\" ? true : false\n  };\n  var $tabContainer = $('.eael-advance-tabs'),\n    nav = $tabContainer.find('.eael-tabs-nav li'),\n    tabContent = $tabContainer.find('.eael-tabs-content > div');\n  nav.on('click', function () {\n    var currentContent = tabContent.eq($(this).index()),\n      $imagCompExist = $(currentContent).find('.eael-img-comp-container');\n    if ($imagCompExist.length) {\n      $img_comp.imagesLoaded().done(function () {\n        $img_comp.find('div').remove();\n        $img_comp.find('img').removeClass('twentytwenty-before twentytwenty-after').removeAttr('style');\n        $img_comp.closest('.elementor-widget-container').html($img_comp);\n        $img_comp.eatwentytwenty($options);\n      });\n    }\n  });\n  $img_comp.imagesLoaded().done(function () {\n    $img_comp.find('div').remove();\n    $img_comp.find('img').removeClass('twentytwenty-before twentytwenty-after').removeAttr('style');\n    $img_comp.closest('.elementor-widget-container').html($img_comp);\n    $img_comp.eatwentytwenty($options);\n  });\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-image-comparison.default\", ImageComparisonHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/img-comparison.js?");

/***/ })

/******/ });