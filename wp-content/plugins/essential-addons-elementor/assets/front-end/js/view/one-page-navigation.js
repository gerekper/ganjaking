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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/one-page-navigation.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/one-page-navigation.js":
/*!********************************************!*\
  !*** ./src/js/view/one-page-navigation.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function ($) {\n  \"use strict\";\n\n  var OnePageNavHandler = function OnePageNavHandler($scope, $) {\n    var onepage_nav_elem = $scope.find(\".eael-one-page-nav\").eq(0);\n    var $section_id = \"#\" + onepage_nav_elem.data(\"section-id\"),\n      $section_ids = onepage_nav_elem.data(\"section-ids\"),\n      $top_offset = onepage_nav_elem.data(\"top-offset\"),\n      $scroll_speed = onepage_nav_elem.data(\"scroll-speed\"),\n      $scroll_wheel = onepage_nav_elem.data(\"scroll-wheel\"),\n      $scroll_touch = onepage_nav_elem.data(\"scroll-touch\"),\n      $scroll_keys = onepage_nav_elem.data(\"scroll-keys\"),\n      $target_dot = $section_id + \" .eael-one-page-nav-item a\",\n      $nav_item = $section_id + \" .eael-one-page-nav-item\",\n      $active_item = $section_id + \" .eael-one-page-nav-item.active\";\n    $($target_dot).on(\"click\", function (e) {\n      e.preventDefault();\n      e.stopPropagation();\n      if (0 === $(\"#\" + $(this).data(\"row-id\")).length) {\n        return;\n      }\n      if ($(\"html, body\").is(\":animated\")) {\n        return;\n      }\n      $(\"html, body\").animate({\n        scrollTop: $(\"#\" + $(this).data(\"row-id\")).offset().top - $top_offset\n      }, $scroll_speed);\n      $($section_id + \" .eael-one-page-nav-item\").removeClass(\"active\");\n      $(this).parent().addClass(\"active\");\n      return false;\n    });\n    updateDot();\n    $(window).on(\"scroll\", function () {\n      updateDot();\n    });\n    function updateDot() {\n      $.each($section_ids, function (index, item) {\n        var $this = $('#' + item);\n        if ($this.offset().top - $(window).height() / 2 < $(window).scrollTop() && ($this.offset().top >= $(window).scrollTop() || $this.offset().top + $this.height() - $(window).height() / 2 > $(window).scrollTop())) {\n          $($section_id + ' .eael-one-page-nav-item a[data-row-id=\"' + $this.attr(\"id\") + '\"]').parent().addClass(\"active\");\n        } else {\n          $($section_id + ' .eael-one-page-nav-item a[data-row-id=\"' + $this.attr(\"id\") + '\"]').parent().removeClass(\"active\");\n        }\n      });\n    }\n    if ($scroll_wheel == \"on\") {\n      var lastAnimation = 0,\n        quietPeriod = 500,\n        animationTime = 800,\n        startX,\n        startY,\n        timestamp;\n      $(document).on(\"mousewheel DOMMouseScroll\", function (e) {\n        var timeNow = new Date().getTime();\n        if (timeNow - lastAnimation < quietPeriod + animationTime) {\n          e.preventDefault();\n          return;\n        }\n        //wDelta = e.wheelDelta < 0 ? 'down' : 'up';\n        var delta = e.originalEvent.detail < 0 || e.originalEvent.wheelDelta > 0 ? 1 : -1;\n        if (!$(\"html,body\").is(\":animated\")) {\n          if (delta < 0) {\n            if ($($active_item).next().length > 0) {\n              $($active_item).next().find(\"a\").trigger(\"click\");\n            }\n          } else {\n            if ($($active_item).prev().length > 0) {\n              $($active_item).prev().find(\"a\").trigger(\"click\");\n            }\n          }\n        }\n        lastAnimation = timeNow;\n      });\n      if ($scroll_touch == \"on\") {\n        $(document).on(\"pointerdown touchstart\", function (e) {\n          var touches = e.originalEvent.touches;\n          if (touches && touches.length) {\n            startY = touches[0].screenY;\n            timestamp = e.originalEvent.timeStamp;\n          }\n        }).on(\"touchmove\", function (e) {\n          if ($(\"html,body\").is(\":animated\")) {\n            e.preventDefault();\n          }\n        }).on(\"pointerup touchend\", function (e) {\n          var touches = e.originalEvent;\n          if (touches.pointerType === \"touch\" || e.type === \"touchend\") {\n            var Y = touches.screenY || touches.changedTouches[0].screenY;\n            var deltaY = startY - Y;\n            var time = touches.timeStamp - timestamp;\n            // swipe up.\n            if (deltaY < 0) {\n              if ($($active_item).prev().length > 0) {\n                $($active_item).prev().find(\"a\").trigger(\"click\");\n              }\n            }\n            // swipe down.\n            if (deltaY > 0) {\n              if ($($active_item).next().length > 0) {\n                $($active_item).next().find(\"a\").trigger(\"click\");\n              }\n            }\n            if (Math.abs(deltaY) < 2) {\n              return;\n            }\n          }\n        });\n      }\n    }\n    if ($scroll_keys == \"on\") {\n      $(document).keydown(function (e) {\n        var tag = e.target.tagName.toLowerCase();\n        if (tag === \"input\" && tag === \"textarea\") {\n          return;\n        }\n        switch (e.which) {\n          case 38:\n            // up arrow key.\n            $($active_item).prev().find(\"a\").trigger(\"click\");\n            break;\n          case 40:\n            // down arrow key.\n            $($active_item).next().find(\"a\").trigger(\"click\");\n            break;\n          case 33:\n            // pageup key.\n            $($active_item).prev().find(\"a\").trigger(\"click\");\n            break;\n          case 36:\n            // pagedown key.\n            $($active_item).next().find(\"a\").trigger(\"click\");\n            break;\n          default:\n            return;\n        }\n      });\n    }\n  };\n  $(window).on(\"elementor/frontend/init\", function () {\n    elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-one-page-nav.default\", OnePageNavHandler);\n  });\n})(jQuery);\n\n//# sourceURL=webpack:///./src/js/view/one-page-navigation.js?");

/***/ })

/******/ });