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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/learn-dash-course-list.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/learn-dash-course-list.js":
/*!***********************************************!*\
  !*** ./src/js/view/learn-dash-course-list.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var LearnDash = function LearnDash($scope, $) {\n  var card = $scope.find('.eael-3d-hover .eael-learn-dash-course'),\n    wrap = $scope.find('.eael-learndash-wrapper'),\n    $layout_mode = wrap.data('layout-mode');\n  hover3D = wrap.data('3d-hover') !== undefined ? 'true' : 'false';\n  var $nomore_item_text = wrap.data(\"nomore-item-text\");\n  var $next_page = wrap.data(\"next-page\");\n  if ($layout_mode === 'masonry' && !isEditMode) {\n    var $settings = {\n      itemSelector: \".eael-learn-dash-course\",\n      percentPosition: true,\n      masonry: {\n        columnWidth: \".eael-learn-dash-course\"\n      }\n    };\n\n    // init isotope\n    $ld_gallery = $(\".eael-learndash-wrapper\", $scope).isotope($settings);\n\n    // layout gal, while images are loading\n    $ld_gallery.imagesLoaded().progress(function () {\n      $ld_gallery.isotope(\"layout\");\n    });\n  }\n  if (hover3D) {\n    card.map(function (index, item) {\n      $(item).on(\"mousemove\", function (e) {\n        var mX = e.clientX,\n          mY = e.clientY,\n          winHalfWidth = window.innerWidth / 2,\n          winHalfHeight = window.innerHeight / 2,\n          xdeg = (mX - winHalfWidth) / winHalfWidth,\n          ydeg = (mY - winHalfHeight) / winHalfHeight;\n        $(this).css({\n          transition: '0ms',\n          transform: 'rotateX(' + ydeg * 10 + 'deg) rotateY(' + xdeg * 10 + 'deg)'\n        });\n      });\n      window.ondevicemotion = function (event) {\n        var acX = event.accelerationIncludingGravity.x,\n          acY = event.accelerationIncludingGravity.y,\n          acZ = event.accelerationIncludingGravity.z,\n          xdeg = acX / 5,\n          ydeg = acY / 5;\n        $(this).css({\n          transform: 'rotateX(' + ydeg * 10 + 'deg) rotateY(' + xdeg * 10 + 'deg)'\n        });\n      };\n      $(item).on('mouseout', function () {\n        $(this).css({\n          transition: 'transform 300ms linear 0s',\n          transform: 'rotateX(0deg) rotateY(0deg)'\n        });\n      });\n    });\n  }\n  $scope.on(\"click\", \".eael-ld-course-list-load-more\", function (e) {\n    e.preventDefault();\n    $('.eael-learn-dash-course.page-' + $next_page, $scope).removeClass('eael-d-none-lite').addClass('eael-d-block-lite');\n    wrap.attr(\"data-next-page\", $next_page + 1);\n    $(\".eael-learndash-wrapper\", $scope).isotope(\"layout\");\n    if ($('.eael-learn-dash-course.page-' + $next_page, $scope).hasClass('eael-last-ld-course-list-item')) {\n      $(\".eael-ld-course-list-load-more\", $scope).html($nomore_item_text).fadeOut('1500');\n    }\n    $next_page++;\n  });\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-learn-dash-course-list.default\", LearnDash);\n});\n\n//# sourceURL=webpack:///./src/js/view/learn-dash-course-list.js?");

/***/ })

/******/ });