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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/team-member-carousel.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/team-member-carousel.js":
/*!*********************************************!*\
  !*** ./src/js/view/team-member-carousel.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("/**\n * getControlValue\n *\n * Return Elementor control value in frontend,\n * But before uses this method you have to ensure that,\n * \"frontend_available = true\" in elementor control\n *\n * @since 5.0.1\n * @param name\n * @param $scope\n * @returns {*}\n */\nvar getControlValue = function getControlValue(name, $scope) {\n  if (ea.isEditMode) {\n    var _elementorFrontend$co, _$scope$;\n    return (_elementorFrontend$co = elementorFrontend.config.elements) === null || _elementorFrontend$co === void 0 || (_elementorFrontend$co = _elementorFrontend$co.data[(_$scope$ = $scope[0]) === null || _$scope$ === void 0 ? void 0 : _$scope$.dataset.modelCid]) === null || _elementorFrontend$co === void 0 || (_elementorFrontend$co = _elementorFrontend$co.attributes[name]) === null || _elementorFrontend$co === void 0 ? void 0 : _elementorFrontend$co.size;\n  } else {\n    var _$scope$data;\n    return $scope === null || $scope === void 0 || (_$scope$data = $scope.data('settings')) === null || _$scope$data === void 0 || (_$scope$data = _$scope$data[name]) === null || _$scope$data === void 0 ? void 0 : _$scope$data.size;\n  }\n};\nvar TeamMemberCarouselHandler = function TeamMemberCarouselHandler($scope, $) {\n  var $carousel = $scope.find(\".eael-tm-carousel\").eq(0),\n    $pagination = $carousel.data(\"pagination\") !== undefined ? $carousel.data(\"pagination\") : \".swiper-pagination\",\n    $arrow_next = $carousel.data(\"arrow-next\") !== undefined ? $carousel.data(\"arrow-next\") : \".swiper-button-next\",\n    $arrow_prev = $carousel.data(\"arrow-prev\") !== undefined ? $carousel.data(\"arrow-prev\") : \".swiper-button-prev\",\n    $defaultItems = $carousel.data(\"items\") !== undefined ? $carousel.data(\"items\") : 3,\n    $defaultMargin = $carousel.data(\"margin\") !== undefined ? $carousel.data(\"margin\") : 10,\n    $speed = $carousel.data(\"speed\") !== undefined ? $carousel.data(\"speed\") : 400,\n    $autoplay = $carousel.data(\"autoplay\") !== undefined ? $carousel.data(\"autoplay\") : 999999,\n    $loop = $carousel.data(\"loop\") !== undefined ? $carousel.data(\"loop\") : 0,\n    $grab_cursor = $carousel.data(\"grab-cursor\") !== undefined ? $carousel.data(\"grab-cursor\") : 0,\n    $data_id = $carousel.data(\"id\") !== undefined ? $carousel.data(\"id\") : \"\",\n    $pause_on_hover = $carousel.data(\"pause-on-hover\") !== undefined ? $carousel.data(\"pause-on-hover\") : \"\";\n  var el_breakpoints = {},\n    breakpoints = {},\n    bp_index = 0,\n    desktopBreakPoint = localize.el_breakpoints.widescreen.is_enabled ? localize.el_breakpoints.widescreen.value - 1 : 4800;\n  el_breakpoints[bp_index] = {\n    breakpoint: 0,\n    slidesPerView: 0,\n    spaceBetween: 0\n  };\n  bp_index++;\n  localize.el_breakpoints.desktop = {\n    is_enabled: true,\n    value: desktopBreakPoint\n  };\n  $.each(['mobile', 'mobile_extra', 'tablet', 'tablet_extra', 'laptop', 'desktop', 'widescreen'], function (index, device) {\n    var breakpoint = localize.el_breakpoints[device];\n    if (breakpoint.is_enabled) {\n      var _items = $carousel.data('items-' + device),\n        _margin = $carousel.data('margin-' + device);\n      $margin = _margin !== undefined ? _margin : device === 'desktop' ? $defaultMargin : 10;\n      $items = _items !== undefined && _items !== \"\" ? _items : device === 'desktop' ? $defaultItems : 3;\n      el_breakpoints[bp_index] = {\n        breakpoint: breakpoint.value,\n        slidesPerView: $items,\n        spaceBetween: $margin\n      };\n      bp_index++;\n    }\n  });\n  $.each(el_breakpoints, function (index, breakpoint) {\n    var _index = parseInt(index);\n    if (typeof el_breakpoints[_index + 1] !== 'undefined') {\n      breakpoints[breakpoint.breakpoint] = {\n        slidesPerView: el_breakpoints[_index + 1].slidesPerView,\n        spaceBetween: el_breakpoints[_index + 1].spaceBetween\n      };\n    }\n  });\n  var $slider_options = {\n    direction: \"horizontal\",\n    speed: $speed,\n    grabCursor: $grab_cursor,\n    loop: $loop,\n    observer: true,\n    observeParents: true,\n    autoplay: {\n      delay: $autoplay,\n      disableOnInteraction: false\n    },\n    pagination: {\n      el: $pagination,\n      clickable: true\n    },\n    navigation: {\n      nextEl: $arrow_next,\n      prevEl: $arrow_prev\n    },\n    breakpoints: breakpoints\n  };\n  swiperLoader($carousel, $slider_options).then(function (TeamSlider) {\n    if (0 == $autoplay) {\n      TeamSlider.autoplay.stop();\n    }\n    if ($pause_on_hover && $autoplay !== 0) {\n      $carousel.on(\"mouseenter\", function () {\n        TeamSlider.autoplay.stop();\n      });\n      $carousel.on(\"mouseleave\", function () {\n        TeamSlider.autoplay.start();\n      });\n    }\n    TeamSlider.update();\n  });\n  var $tabContainer = $('.eael-advance-tabs'),\n    nav = $tabContainer.find('.eael-tabs-nav li'),\n    tabContent = $tabContainer.find('.eael-tabs-content > div');\n  nav.on('click', function () {\n    var currentContent = tabContent.eq($(this).index()),\n      sliderExist = $(currentContent).find('.swiper-container-wrap.eael-team-member-carousel-wrap');\n    if (sliderExist.length) {\n      swiperLoader($carousel, $slider_options);\n    }\n  });\n};\nvar swiperLoader = function swiperLoader(swiperElement, swiperConfig) {\n  if ('undefined' === typeof Swiper || 'function' === typeof Swiper) {\n    var asyncSwiper = elementorFrontend.utils.swiper;\n    return new asyncSwiper(swiperElement, swiperConfig).then(function (newSwiperInstance) {\n      return newSwiperInstance;\n    });\n  } else {\n    return swiperPromise(swiperElement, swiperConfig);\n  }\n};\nvar swiperPromise = function swiperPromise(swiperElement, swiperConfig) {\n  return new Promise(function (resolve, reject) {\n    var swiperInstance = new Swiper(swiperElement, swiperConfig);\n    resolve(swiperInstance);\n  });\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  if (ea.elementStatusCheck('teamMemberSliderLoad')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-team-member-carousel.default\", TeamMemberCarouselHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/team-member-carousel.js?");

/***/ })

/******/ });