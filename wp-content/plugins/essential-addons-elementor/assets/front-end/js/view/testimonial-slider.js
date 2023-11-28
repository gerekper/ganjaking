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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/testimonial-slider.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/testimonial-slider.js":
/*!*******************************************!*\
  !*** ./src/js/view/testimonial-slider.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var TestimonialSliderHandler = function TestimonialSliderHandler($scope, $) {\n  var items_tablet = getControlValue('items_tablet', $scope);\n  var items_mobile = getControlValue('items_mobile', $scope);\n  var $testimonialSlider = $scope.find('.eael-testimonial-slider-main').eq(0),\n    $pagination = $testimonialSlider.data('pagination') !== undefined ? $testimonialSlider.data('pagination') : '.swiper-pagination',\n    $arrow_next = $testimonialSlider.data('arrow-next') !== undefined ? $testimonialSlider.data('arrow-next') : '.swiper-button-next',\n    $arrow_prev = $testimonialSlider.data('arrow-prev') !== undefined ? $testimonialSlider.data('arrow-prev') : '.swiper-button-prev',\n    $items = $testimonialSlider.data('items') !== undefined ? $testimonialSlider.data('items') : 3,\n    $items_tablet = items_tablet !== undefined ? items_tablet : 3,\n    $items_mobile = items_mobile !== undefined ? items_mobile : 3,\n    $slideItems = $testimonialSlider.data('slide-items') !== undefined ? $testimonialSlider.data('slide-items') : 1,\n    $slideItems_tablet = $testimonialSlider.data('slide-items-tablet') !== undefined ? $testimonialSlider.data('slide-items-tablet') : 1,\n    $slideItems_mobile = $testimonialSlider.data('slide-items-mobile') !== undefined ? $testimonialSlider.data('slide-items-mobile') : 1,\n    $margin = $testimonialSlider.data('margin') !== undefined ? $testimonialSlider.data('margin') : 10,\n    $margin_tablet = $testimonialSlider.data('margin-tablet') !== undefined ? $testimonialSlider.data('margin-tablet') : 10,\n    $margin_mobile = $testimonialSlider.data('margin-mobile') !== undefined ? $testimonialSlider.data('margin-mobile') : 10,\n    $effect = $testimonialSlider.data('effect') !== undefined ? $testimonialSlider.data('effect') : 'slide',\n    $speed = $testimonialSlider.data('speed') !== undefined ? $testimonialSlider.data('speed') : 400,\n    $autoplay = $testimonialSlider.data('autoplay_speed') !== undefined ? $testimonialSlider.data('autoplay_speed') : 999999,\n    $loop = $testimonialSlider.data('loop') !== undefined ? $testimonialSlider.data('loop') : 0,\n    $grab_cursor = $testimonialSlider.data('grab-cursor') !== undefined ? $testimonialSlider.data('grab-cursor') : 0,\n    $centeredSlides = $effect == 'coverflow' ? true : false,\n    $pause_on_hover = $testimonialSlider.data('pause-on-hover') !== undefined ? $testimonialSlider.data('pause-on-hover') : '';\n  var $testimonialSliderOptions = {\n    direction: 'horizontal',\n    speed: $speed,\n    effect: $effect,\n    centeredSlides: $centeredSlides,\n    grabCursor: $grab_cursor,\n    autoHeight: true,\n    loop: $loop,\n    autoplay: {\n      delay: $autoplay,\n      disableOnInteraction: false\n    },\n    pagination: {\n      el: $pagination,\n      clickable: true\n    },\n    navigation: {\n      nextEl: $arrow_next,\n      prevEl: $arrow_prev\n    }\n  };\n  if ($effect === 'slide' || $effect === 'coverflow') {\n    $testimonialSliderOptions.breakpoints = {\n      1024: {\n        slidesPerView: $items,\n        spaceBetween: $margin,\n        slidesPerGroup: $slideItems\n      },\n      768: {\n        slidesPerView: $items_tablet,\n        spaceBetween: $margin_tablet,\n        slidesPerGroup: $slideItems_tablet\n      },\n      320: {\n        slidesPerView: $items_mobile,\n        spaceBetween: $margin_mobile,\n        slidesPerGroup: $slideItems_mobile\n      }\n    };\n  } else {\n    $testimonialSliderOptions.items = 1;\n  }\n  if ($effect === 'fade') {\n    $testimonialSliderOptions.fadeEffect = {\n      crossFade: true\n    };\n  }\n  var $testimonialSliderObj = swiperLoader($testimonialSlider, $testimonialSliderOptions);\n  $testimonialSliderObj.then(function ($testimonialSliderObj) {\n    if ($autoplay === 0) {\n      $testimonialSliderObj.autoplay.stop();\n    }\n    if ($pause_on_hover && $autoplay !== 0) {\n      $testimonialSlider.on('mouseenter', function () {\n        $testimonialSliderObj.autoplay.stop();\n      });\n      $testimonialSlider.on('mouseleave', function () {\n        $testimonialSliderObj.autoplay.start();\n      });\n    }\n    $testimonialSliderObj.update();\n\n    //gallery pagination\n    var $paginationGallerySelector = $scope.find('.eael-testimonial-slider .eael-testimonial-gallary-pagination').eq(0);\n    if ($paginationGallerySelector.length > 0) {\n      swiperLoader($paginationGallerySelector, {\n        spaceBetween: 20,\n        centeredSlides: true,\n        touchRatio: 0.2,\n        slideToClickedSlide: true,\n        loop: true,\n        slidesPerGroup: 1,\n        loopedSlides: $items,\n        slidesPerView: 3\n      }).then(function ($paginationGallerySlider) {\n        $testimonialSliderObj.controller.control = $paginationGallerySlider;\n        $paginationGallerySlider.controller.control = $testimonialSliderObj;\n      });\n    }\n  });\n  var $tabContainer = $('.eael-advance-tabs'),\n    nav = $tabContainer.find('.eael-tabs-nav li'),\n    tabContent = $tabContainer.find('.eael-tabs-content > div');\n  nav.on('click', function () {\n    var currentContent = tabContent.eq($(this).index()),\n      sliderExist = $(currentContent).find('.swiper-container-wrap.eael-testimonial-slider');\n    if (sliderExist.length) {\n      swiperLoader($testimonialSlider, $testimonialSliderOptions);\n    }\n  });\n  $(document).on('click', '.eael-testimonial-read-more-btn', function (e) {\n    e.preventDefault();\n    var parent = $(this).closest('.eael-testimonial-item-inner');\n    $('.eael-testimonial-text-excerpt', parent).toggle();\n    $('.eael-testimonial-text-full-text', parent).toggle();\n    window.dispatchEvent(new Event('resize'));\n  });\n};\n\n/**\n * getControlValue\n *\n * Return Elementor control value in frontend,\n * But before uses this method you have to ensure that,\n * \"frontend_available = true\" in elementor control\n *\n * @since 5.0.1\n * @param name\n * @param $scope\n * @returns {*}\n */\nvar getControlValue = function getControlValue(name, $scope) {\n  if (ea.isEditMode) {\n    var _elementorFrontend$co, _$scope$;\n    return (_elementorFrontend$co = elementorFrontend.config.elements) === null || _elementorFrontend$co === void 0 || (_elementorFrontend$co = _elementorFrontend$co.data[(_$scope$ = $scope[0]) === null || _$scope$ === void 0 ? void 0 : _$scope$.dataset.modelCid]) === null || _elementorFrontend$co === void 0 || (_elementorFrontend$co = _elementorFrontend$co.attributes[name]) === null || _elementorFrontend$co === void 0 ? void 0 : _elementorFrontend$co.size;\n  } else {\n    var _$scope$data;\n    return $scope === null || $scope === void 0 || (_$scope$data = $scope.data('settings')) === null || _$scope$data === void 0 || (_$scope$data = _$scope$data[name]) === null || _$scope$data === void 0 ? void 0 : _$scope$data.size;\n  }\n};\nvar swiperLoader = function swiperLoader(swiperElement, swiperConfig) {\n  if ('undefined' === typeof Swiper || 'function' === typeof Swiper) {\n    var asyncSwiper = elementorFrontend.utils.swiper;\n    return new asyncSwiper(swiperElement, swiperConfig).then(function (newSwiperInstance) {\n      return newSwiperInstance;\n    });\n  } else {\n    return swiperPromise(swiperElement, swiperConfig);\n  }\n};\nvar swiperPromise = function swiperPromise(swiperElement, swiperConfig) {\n  return new Promise(function (resolve, reject) {\n    var swiperInstance = new Swiper(swiperElement, swiperConfig);\n    resolve(swiperInstance);\n  });\n};\njQuery(window).on('elementor/frontend/init', function () {\n  if (ea.elementStatusCheck('testimonialLoad')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction('frontend/element_ready/eael-testimonial-slider.default', TestimonialSliderHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/testimonial-slider.js?");

/***/ })

/******/ });