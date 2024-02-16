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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/post-carousel.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/post-carousel.js":
/*!**************************************!*\
  !*** ./src/js/view/post-carousel.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function get_configurations($postCarousel, $scope) {\n  var items_tablet = getControlValue('items_tablet', $scope),\n    items_mobile = getControlValue('items_mobile', $scope);\n  var $autoplay = $postCarousel.data(\"autoplay\") !== undefined ? $postCarousel.data(\"autoplay\") : 999999,\n    $pagination = $postCarousel.data(\"pagination\") !== undefined ? $postCarousel.data(\"pagination\") : \".swiper-pagination\",\n    $arrow_next = $postCarousel.data(\"arrow-next\") !== undefined ? $postCarousel.data(\"arrow-next\") : \".swiper-button-next\",\n    $arrow_prev = $postCarousel.data(\"arrow-prev\") !== undefined ? $postCarousel.data(\"arrow-prev\") : \".swiper-button-prev\",\n    $defaultItems = $postCarousel.data(\"items\") !== undefined ? $postCarousel.data(\"items\") : 3,\n    $items_tablet = items_tablet !== undefined ? items_tablet : 3,\n    $items_mobile = items_mobile !== undefined ? items_mobile : 3,\n    $defaultMargin = $postCarousel.data(\"margin\") !== undefined ? $postCarousel.data(\"margin\") : 10,\n    $margin_tablet = $postCarousel.data(\"margin-tablet\") !== undefined ? $postCarousel.data(\"margin-tablet\") : 10,\n    $margin_mobile = $postCarousel.data(\"margin-mobile\") !== undefined ? $postCarousel.data(\"margin-mobile\") : 10,\n    $effect = $postCarousel.data(\"effect\") !== undefined ? $postCarousel.data(\"effect\") : \"slide\",\n    $speed = $postCarousel.data(\"speed\") !== undefined ? $postCarousel.data(\"speed\") : 400,\n    $loop = $postCarousel.data(\"loop\") !== undefined ? $postCarousel.data(\"loop\") : 0,\n    $grab_cursor = $postCarousel.data(\"grab-cursor\") !== undefined ? $postCarousel.data(\"grab-cursor\") : 0,\n    $pause_on_hover = $postCarousel.data(\"pause-on-hover\") !== undefined ? $postCarousel.data(\"pause-on-hover\") : \"\",\n    $centeredSlides = $effect === \"coverflow\" ? true : false;\n  var $carouselOptions = {\n    pause_on_hover: $pause_on_hover,\n    direction: \"horizontal\",\n    speed: $speed,\n    effect: $effect,\n    centeredSlides: $centeredSlides,\n    grabCursor: $grab_cursor,\n    autoHeight: true,\n    loop: $loop,\n    autoplay: {\n      delay: $autoplay\n    },\n    pagination: {\n      el: $pagination,\n      clickable: true\n    },\n    navigation: {\n      nextEl: $arrow_next,\n      prevEl: $arrow_prev\n    }\n  };\n  if ($autoplay === 0) {\n    $carouselOptions.autoplay = false;\n  }\n  if ($effect === 'slide' || $effect === 'coverflow') {\n    if (typeof localize.el_breakpoints === 'string') {\n      $carouselOptions.breakpoints = {\n        1024: {\n          slidesPerView: $defaultItems,\n          spaceBetween: $defaultMargin\n        },\n        768: {\n          slidesPerView: $items_tablet,\n          spaceBetween: $margin_tablet\n        },\n        320: {\n          slidesPerView: $items_mobile,\n          spaceBetween: $margin_mobile\n        }\n      };\n    } else {\n      var el_breakpoints = {},\n        breakpoints = {},\n        bp_index = 0,\n        desktopBreakPoint = localize.el_breakpoints.widescreen.is_enabled ? localize.el_breakpoints.widescreen.value - 1 : 4800;\n      el_breakpoints[bp_index] = {\n        breakpoint: 0,\n        slidesPerView: 0,\n        spaceBetween: 0\n      };\n      bp_index++;\n      localize.el_breakpoints.desktop = {\n        is_enabled: true,\n        value: desktopBreakPoint\n      };\n      jQuery.each(['mobile', 'mobile_extra', 'tablet', 'tablet_extra', 'laptop', 'desktop', 'widescreen'], function (index, device) {\n        var breakpoint = localize.el_breakpoints[device];\n        if (breakpoint.is_enabled) {\n          var _items = getControlValue('items_' + device, $scope),\n            _margin = $postCarousel.data('margin-' + device),\n            $margin = _margin !== undefined ? _margin : device === 'desktop' ? $defaultMargin : 10,\n            $items = _items !== undefined && _items !== \"\" ? _items : device === 'desktop' ? $defaultItems : 3;\n          el_breakpoints[bp_index] = {\n            breakpoint: breakpoint.value,\n            slidesPerView: $items,\n            spaceBetween: $margin\n          };\n          bp_index++;\n        }\n      });\n      jQuery.each(el_breakpoints, function (index, breakpoint) {\n        var _index = parseInt(index);\n        if (typeof el_breakpoints[_index + 1] !== 'undefined') {\n          breakpoints[breakpoint.breakpoint] = {\n            slidesPerView: el_breakpoints[_index + 1].slidesPerView,\n            spaceBetween: el_breakpoints[_index + 1].spaceBetween\n          };\n        }\n      });\n      $carouselOptions.breakpoints = breakpoints;\n    }\n  } else {\n    $carouselOptions.items = 1;\n  }\n  return $carouselOptions;\n}\nfunction autoPlayManager(element, options, event) {\n  if (options.autoplay.delay === 0) {\n    var _event$autoplay;\n    event === null || event === void 0 || (_event$autoplay = event.autoplay) === null || _event$autoplay === void 0 || _event$autoplay.stop();\n  }\n  if (options.pause_on_hover && options.autoplay.delay !== 0) {\n    element.on(\"mouseenter\", function () {\n      var _event$autoplay2;\n      event === null || event === void 0 || (_event$autoplay2 = event.autoplay) === null || _event$autoplay2 === void 0 || _event$autoplay2.pause();\n    });\n    element.on(\"mouseleave\", function () {\n      var _event$autoplay3;\n      event === null || event === void 0 || (_event$autoplay3 = event.autoplay) === null || _event$autoplay3 === void 0 || _event$autoplay3.run();\n    });\n  }\n}\nvar PostCarouselHandler = function PostCarouselHandler($scope, $) {\n  var $postCarousel = $scope.find(\".eael-post-carousel\").eq(0),\n    $carouselOptions = get_configurations($postCarousel, $scope);\n  swiperLoader($postCarousel, $carouselOptions).then(function (eaelPostCarousel) {\n    autoPlayManager($postCarousel, $carouselOptions, eaelPostCarousel);\n  });\n  var PostCarouselLoader = function PostCarouselLoader(element) {\n    var postCarousels = $(element).find('.eael-post-carousel');\n    if (postCarousels.length) {\n      postCarousels.each(function () {\n        if ($(this)[0].swiper) {\n          $(this)[0].swiper.destroy(true, true);\n          var options = get_configurations($(this), element);\n          var $this = $(this);\n          swiperLoader($(this)[0], options).then(function (event) {\n            autoPlayManager($this, options, event);\n          });\n        }\n      });\n    }\n  };\n  ea.hooks.addAction(\"ea-toggle-triggered\", \"ea\", PostCarouselLoader);\n  ea.hooks.addAction(\"ea-lightbox-triggered\", \"ea\", PostCarouselLoader);\n  ea.hooks.addAction(\"ea-advanced-tabs-triggered\", \"ea\", PostCarouselLoader);\n  ea.hooks.addAction(\"ea-advanced-accordion-triggered\", \"ea\", PostCarouselLoader);\n};\nvar swiperLoader = function swiperLoader(swiperElement, swiperConfig) {\n  if ('undefined' === typeof Swiper || 'function' === typeof Swiper) {\n    var asyncSwiper = elementorFrontend.utils.swiper;\n    return new asyncSwiper(swiperElement, swiperConfig).then(function (newSwiperInstance) {\n      return newSwiperInstance;\n    });\n  } else {\n    return swiperPromise(swiperElement, swiperConfig);\n  }\n};\nvar swiperPromise = function swiperPromise(swiperElement, swiperConfig) {\n  return new Promise(function (resolve, reject) {\n    var swiperInstance = new Swiper(swiperElement, swiperConfig);\n    resolve(swiperInstance);\n  });\n};\n\n/**\n * getControlValue\n *\n * Return Elementor control value in frontend,\n * But before uses this method you have to ensure that,\n * \"frontend_available = true\" in elementor control\n *\n * @since 5.0.1\n * @param name\n * @param $scope\n * @returns {*}\n */\nvar getControlValue = function getControlValue(name, $scope) {\n  if (ea.isEditMode) {\n    var _elementorFrontend$co, _$scope$;\n    return (_elementorFrontend$co = elementorFrontend.config.elements) === null || _elementorFrontend$co === void 0 || (_elementorFrontend$co = _elementorFrontend$co.data[(_$scope$ = $scope[0]) === null || _$scope$ === void 0 ? void 0 : _$scope$.dataset.modelCid]) === null || _elementorFrontend$co === void 0 || (_elementorFrontend$co = _elementorFrontend$co.attributes[name]) === null || _elementorFrontend$co === void 0 ? void 0 : _elementorFrontend$co.size;\n  } else {\n    var _$scope;\n    $scope = jQuery($scope);\n    return (_$scope = $scope) === null || _$scope === void 0 || (_$scope = _$scope.data('settings')) === null || _$scope === void 0 || (_$scope = _$scope[name]) === null || _$scope === void 0 ? void 0 : _$scope.size;\n  }\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  if (ea.elementStatusCheck('eaelPostSliderLoad')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-post-carousel.default\", PostCarouselHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/post-carousel.js?");

/***/ })

/******/ });