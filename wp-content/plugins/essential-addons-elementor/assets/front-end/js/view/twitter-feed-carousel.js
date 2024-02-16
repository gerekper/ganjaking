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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/twitter-feed-carousel.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/twitter-feed-carousel.js":
/*!**********************************************!*\
  !*** ./src/js/view/twitter-feed-carousel.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function get_configurations($carousel) {\n  var $pagination = $carousel.data('pagination') !== undefined ? $carousel.data('pagination') : '.swiper-pagination',\n    $arrow_next = $carousel.data('arrow-next') !== undefined ? $carousel.data('arrow-next') : '.swiper-button-next',\n    $arrow_prev = $carousel.data('arrow-prev') !== undefined ? $carousel.data('arrow-prev') : '.swiper-button-prev',\n    $items = $carousel.data('items') !== undefined ? $carousel.data('items') : 3,\n    $items_tablet = $carousel.data('items-tablet') !== undefined ? $carousel.data('items-tablet') : 3,\n    $items_mobile = $carousel.data('items-mobile') !== undefined ? $carousel.data('items-mobile') : 3,\n    $margin = $carousel.data('margin') !== undefined ? $carousel.data('margin') : 10,\n    $margin_tablet = $carousel.data('margin-tablet') !== '' ? $carousel.data('margin-tablet') : 10,\n    $margin_mobile = $carousel.data('margin-mobile') !== '' ? $carousel.data('margin-mobile') : 10,\n    $effect = $carousel.data('effect') !== undefined ? $carousel.data('effect') : 'slide',\n    $speed = $carousel.data('speed') !== undefined ? $carousel.data('speed') : 400,\n    $autoplay = $carousel.data('autoplay') !== undefined ? $carousel.data('autoplay') : 0,\n    $loop = $carousel.data('loop') !== undefined ? $carousel.data('loop') : 0,\n    $grab_cursor = $carousel.data('grab-cursor') !== undefined ? $carousel.data('grab-cursor') : 0,\n    $centeredSlides = $effect === 'coverflow' ? true : false,\n    $pause_on_hover = $carousel.data('pause-on-hover') !== undefined ? $carousel.data('pause-on-hover') : '',\n    $twitterCarouselOptions = {\n      pause_on_hover: $pause_on_hover,\n      direction: 'horizontal',\n      speed: $speed,\n      effect: $effect,\n      fadeEffect: {\n        crossFade: true\n      },\n      centeredSlides: $centeredSlides,\n      grabCursor: $grab_cursor,\n      autoHeight: true,\n      loop: $loop,\n      autoplay: {\n        delay: $autoplay,\n        disableOnInteraction: false\n      },\n      pagination: {\n        el: $pagination,\n        clickable: true\n      },\n      navigation: {\n        nextEl: $arrow_next,\n        prevEl: $arrow_prev\n      }\n    };\n  if ($autoplay === 0) {\n    $twitterCarouselOptions.autoplay = false;\n  }\n  if ($effect === 'slide' || $effect === 'coverflow') {\n    $twitterCarouselOptions.breakpoints = {\n      1024: {\n        slidesPerView: $items,\n        spaceBetween: $margin\n      },\n      768: {\n        slidesPerView: $items_tablet,\n        spaceBetween: $margin_tablet\n      },\n      320: {\n        slidesPerView: $items_mobile,\n        spaceBetween: $margin_mobile\n      }\n    };\n  } else {\n    $twitterCarouselOptions.items = 1;\n  }\n  return $twitterCarouselOptions;\n}\nfunction autoPlayManager(element, options, event) {\n  if (options.autoplay.delay === 0) {\n    var _event$autoplay;\n    event === null || event === void 0 || (_event$autoplay = event.autoplay) === null || _event$autoplay === void 0 || _event$autoplay.stop();\n  }\n  if (options.pause_on_hover && options.autoplay.delay !== 0) {\n    element.on('mouseenter', function () {\n      var _event$autoplay2;\n      event === null || event === void 0 || (_event$autoplay2 = event.autoplay) === null || _event$autoplay2 === void 0 || _event$autoplay2.stop();\n    });\n    element.on('mouseleave', function () {\n      var _event$autoplay3;\n      event === null || event === void 0 || (_event$autoplay3 = event.autoplay) === null || _event$autoplay3 === void 0 || _event$autoplay3.start();\n    });\n  }\n}\nvar TwitterFeedCarouselHandler = function TwitterFeedCarouselHandler($scope, $) {\n  var $carousel = $('.eael-twitter-feed-carousel', $scope),\n    $twitterCarouselOptions = get_configurations($carousel);\n  swiperLoader($carousel, $twitterCarouselOptions).then(function (twitterCarousel) {\n    autoPlayManager($carousel, $twitterCarouselOptions, twitterCarousel);\n  });\n  var TwitterFeedCarouselLoader = function TwitterFeedCarouselLoader(element) {\n    var productSliders = $(element).find('.eael-twitter-feed-carousel');\n    if (productSliders.length) {\n      productSliders.each(function () {\n        var $this = $(this);\n        if ($this[0].swiper) {\n          $this[0].swiper.destroy(true, true);\n          var options = get_configurations($this);\n          swiperLoader($(this)[0], options).then(function (event) {\n            autoPlayManager($this, options, event);\n          });\n        }\n      });\n    }\n  };\n  ea.hooks.addAction(\"ea-toggle-triggered\", \"ea\", TwitterFeedCarouselLoader);\n  ea.hooks.addAction(\"ea-lightbox-triggered\", \"ea\", TwitterFeedCarouselLoader);\n  ea.hooks.addAction(\"ea-advanced-tabs-triggered\", \"ea\", TwitterFeedCarouselLoader);\n  ea.hooks.addAction(\"ea-advanced-accordion-triggered\", \"ea\", TwitterFeedCarouselLoader);\n  if (isEditMode) {\n    elementor.hooks.addAction(\"panel/open_editor/widget/eael-twitter-feed-carousel\", function (panel, model, view) {\n      panel.content.el.onclick = function (event) {\n        if (event.target.dataset.event == \"ea:cache:clear\") {\n          var button = event.target;\n          button.innerHTML = \"Clearing...\";\n          jQuery.ajax({\n            url: localize.ajaxurl,\n            type: \"post\",\n            data: {\n              action: \"eael_clear_widget_cache_data\",\n              security: localize.nonce,\n              ac_name: model.attributes.settings.attributes.eael_twitter_feed_ac_name,\n              hastag: model.attributes.settings.attributes.eael_twitter_feed_hashtag_name,\n              c_key: model.attributes.settings.attributes.eael_twitter_feed_consumer_key,\n              c_secret: model.attributes.settings.attributes.eael_twitter_feed_consumer_secret\n            },\n            success: function success(response) {\n              if (response.success) {\n                button.innerHTML = \"Clear\";\n              } else {\n                button.innerHTML = \"Failed\";\n              }\n            },\n            error: function error() {\n              button.innerHTML = \"Failed\";\n            }\n          });\n        }\n      };\n    });\n  }\n};\nvar swiperLoader = function swiperLoader(swiperElement, swiperConfig) {\n  if ('undefined' === typeof Swiper || 'function' === typeof Swiper) {\n    var asyncSwiper = elementorFrontend.utils.swiper;\n    return new asyncSwiper(swiperElement, swiperConfig).then(function (newSwiperInstance) {\n      return newSwiperInstance;\n    });\n  } else {\n    return swiperPromise(swiperElement, swiperConfig);\n  }\n};\nvar swiperPromise = function swiperPromise(swiperElement, swiperConfig) {\n  return new Promise(function (resolve, reject) {\n    var swiperInstance = new Swiper(swiperElement, swiperConfig);\n    resolve(swiperInstance);\n  });\n};\njQuery(window).on('elementor/frontend/init', function () {\n  if (ea.elementStatusCheck('twitterFeedLoad')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction('frontend/element_ready/eael-twitter-feed-carousel.default', TwitterFeedCarouselHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/twitter-feed-carousel.js?");

/***/ })

/******/ });