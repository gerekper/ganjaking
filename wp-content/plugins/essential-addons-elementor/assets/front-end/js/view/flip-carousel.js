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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/flip-carousel.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/flip-carousel.js":
/*!**************************************!*\
  !*** ./src/js/view/flip-carousel.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var FlipCarousel = function FlipCarousel($scope, $) {\n  var $this = $(\".eael-flip-carousel\", $scope);\n  var style = $this.data(\"style\"),\n    start = $this.data(\"start\"),\n    fadeIn = $this.data(\"fadein\"),\n    loop = $this.data(\"loop\"),\n    autoplay = $this.data(\"autoplay\"),\n    pauseOnHover = $this.data(\"pauseonhover\"),\n    spacing = $this.data(\"spacing\"),\n    click = $this.data(\"click\"),\n    scrollwheel = $this.data(\"scrollwheel\"),\n    touch = $this.data(\"touch\"),\n    buttons = $this.data(\"buttons\");\n  var buttonPrev = $this.data(\"buttonprev\");\n  var buttonNext = $this.data(\"buttonnext\");\n  var buttonPrevIcon = $this.data(\"icon\");\n  var buttonNextIcon = $this.data(\"nexticon\");\n  var options = {\n    style: style,\n    start: start,\n    fadeIn: fadeIn,\n    loop: loop,\n    autoplay: autoplay,\n    pauseOnHover: pauseOnHover,\n    spacing: spacing,\n    click: click,\n    scrollwheel: scrollwheel,\n    tocuh: touch,\n    buttons: buttons,\n    buttonPrev: '',\n    buttonNext: ''\n  };\n  if (buttonPrevIcon == 'svg') {\n    options.buttonPrev = '<span class=\"flip-custom-nav\">' + buttonPrev + '</span>';\n  } else {\n    options.buttonPrev = '<i class=\"flip-custom-nav ' + buttonPrev + '\"></i>';\n  }\n  if (buttonNextIcon == 'svg') {\n    options.buttonNext = '<span class=\"flip-custom-nav\">' + buttonNext + '</div>';\n  } else {\n    options.buttonNext = '<i class=\"flip-custom-nav ' + buttonNext + '\"></i>';\n  }\n  $this.flipster(options);\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  if (ea.elementStatusCheck('eaelFlipLoad')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-flip-carousel.default\", FlipCarousel);\n});\n\n//# sourceURL=webpack:///./src/js/view/flip-carousel.js?");

/***/ })

/******/ });