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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/content-timeline.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/content-timeline.js":
/*!*****************************************!*\
  !*** ./src/js/view/content-timeline.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var contentTimelineHandler = function contentTimelineHandler($scope, $) {\n  var $timeline = $scope.find('.eael-timeline'),\n    $horizontalTimelineWrap = $scope.find('.horizontal-timeline-wrapper'),\n    $timelineTrack = $scope.find('.eael-horizontal-timeline-track'),\n    $nextArrow = $scope.find('.eael-next-arrow'),\n    $prevArrow = $scope.find('.eael-prev-arrow'),\n    $arrows = $scope.find('.eael-arrow'),\n    itemsCount = $scope.find('.eael-horizontal-timeline-list--middle .eael-horizontal-timeline-item').length,\n    currentTransform = 0,\n    currentPosition = 0,\n    transform = {\n      desktop: 100 / 3,\n      tablet: 100 / 3,\n      mobile: 100 / 3\n    },\n    maxPosition = {\n      desktop: Math.max(0, itemsCount - 3),\n      tablet: Math.max(0, itemsCount - 3),\n      mobile: Math.max(0, itemsCount - 3)\n    },\n    currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),\n    slidesToScroll = $horizontalTimelineWrap.data('slide_to_scroll'),\n    columns = {\n      desktop: 3,\n      tablet: 3,\n      mobile: 3\n    };\n  var contentBlock = $(\".eael-content-timeline-block\");\n  var horizontalTimeline = $scope.find('.eael-horizontal-timeline-track').length;\n  var containerWidth = $('.eael-content-timeline-container', $scope).width();\n  var containerLeft = $('.eael-content-timeline-container', $scope).offset().left;\n  if (horizontalTimeline) {\n    $('.eael-horizontal-timeline-track', $scope).on('scroll', function (e) {\n      var scrollLeftPosition = e.currentTarget.scrollLeft;\n      $(\".eael-horizontal-timeline-item\", $scope).each(function () {\n        var itemLeft = $(this).offset().left;\n        if (itemLeft > -50 && itemLeft < containerLeft + containerWidth + scrollLeftPosition - 100) {\n          $(this).addClass('is-active');\n        } else {\n          if ($(this).hasClass('is-active')) {\n            $(this).removeClass('is-active');\n          }\n        }\n      });\n    });\n  }\n  $(window).on(\"scroll\", function () {\n    contentBlock.each(function () {\n      if ($(this).find(\".eael-highlight\")) {\n        // Calculate screen middle position, top offset and line height and\n        // change line height dynamically\n\n        var lineEnd = contentBlock.height() * 0.15 + window.innerHeight / 2;\n        var topOffset = $(this).offset().top;\n        var lineHeight = window.scrollY + lineEnd * 1.3 - topOffset;\n        $(this).find(\".eael-content-timeline-inner\").css(\"height\", lineHeight + \"px\");\n      }\n    });\n    if (this.oldScroll > this.scrollY == false) {\n      this.oldScroll = this.scrollY;\n      // Scroll Down\n      $(\".eael-content-timeline-block.eael-highlight\").prev().find(\".eael-content-timeline-inner\").removeClass(\"eael-muted\").addClass(\"eael-highlighted\");\n    } else if (this.oldScroll > this.scrollY == true) {\n      this.oldScroll = this.scrollY;\n      // Scroll Up\n      $(\".eael-content-timeline-block.eael-highlight\").find(\".eael-content-timeline-inner\").addClass(\"eael-prev-highlighted\");\n      $(\".eael-content-timeline-block.eael-highlight\").next().find(\".eael-content-timeline-inner\").removeClass(\"eael-highlighted\").removeClass(\"eael-prev-highlighted\").addClass(\"eael-muted\");\n    }\n  });\n  setLinePosition();\n  function setLinePosition() {\n    var _$firstPoint$position, _$lastPoint$position;\n    var $line = $scope.find('.eael-horizontal-timeline__line'),\n      $firstPoint = $scope.find('.eael-horizontal-timeline-item__point-content:first'),\n      $lastPoint = $scope.find('.eael-horizontal-timeline-item__point-content:last'),\n      firstPointLeftPos = ((_$firstPoint$position = $firstPoint.position()) === null || _$firstPoint$position === void 0 ? void 0 : _$firstPoint$position.left) + parseInt($firstPoint.css('marginLeft')),\n      lastPointLeftPos = ((_$lastPoint$position = $lastPoint.position()) === null || _$lastPoint$position === void 0 ? void 0 : _$lastPoint$position.left) + parseInt($lastPoint.css('marginLeft')),\n      pointWidth = $firstPoint.outerWidth();\n    if (firstPointLeftPos && lastPointLeftPos && pointWidth) {\n      $line.css({\n        'left': '45px',\n        'width': Math.abs(lastPointLeftPos - firstPointLeftPos)\n      });\n    }\n  }\n\n  // Arrows\n  if ($nextArrow[0] && maxPosition[currentDeviceMode] === 0) {\n    $nextArrow.addClass('eael-arrow-disabled');\n  }\n  if ($arrows[0]) {\n    var slidesScroll = typeof slidesToScroll[currentDeviceMode] !== 'undefined' ? parseInt(slidesToScroll[currentDeviceMode]) : 1,\n      xPos = 0,\n      yPos = 0,\n      diffpos;\n    $arrows.on('click', function (event) {\n      var $this = $(this),\n        currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),\n        direction = $this.hasClass('eael-next-arrow') ? 'next' : 'prev',\n        dirMultiplier = -1;\n      if (slidesScroll > columns[currentDeviceMode]) {\n        slidesScroll = columns[currentDeviceMode];\n      }\n      if ('next' === direction && currentPosition < maxPosition[currentDeviceMode]) {\n        currentPosition += slidesScroll;\n        if (currentPosition > maxPosition[currentDeviceMode]) {\n          currentPosition = maxPosition[currentDeviceMode];\n        }\n      }\n      if ('prev' === direction && currentPosition > 0) {\n        currentPosition -= slidesScroll;\n        if (currentPosition < 0) {\n          currentPosition = 0;\n        }\n      }\n      if (currentPosition > 0) {\n        $prevArrow.removeClass('eael-arrow-disabled');\n      } else {\n        $prevArrow.addClass('eael-arrow-disabled');\n      }\n      if (currentPosition === maxPosition[currentDeviceMode]) {\n        $nextArrow.addClass('eaek-arrow-disabled');\n      } else {\n        $nextArrow.removeClass('eaek-arrow-disabled');\n      }\n      if (currentPosition === 0) {\n        currentTransform = 0;\n      } else {\n        currentTransform = currentPosition * transform[currentDeviceMode];\n      }\n      $timelineTrack.css({\n        'transform': 'translateX(' + dirMultiplier * currentTransform + '%)'\n      });\n      $(\".eael-horizontal-timeline-item\", $scope).each(function () {\n        var itemLeft = $(this).offset().left;\n        if (itemLeft < containerLeft + containerWidth) {\n          $(this).addClass('is-active');\n        } else {\n          if ($(this).hasClass('is-active')) {\n            $(this).removeClass('is-active');\n          }\n        }\n      });\n    });\n  }\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-content-timeline.default\", contentTimelineHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/content-timeline.js?");

/***/ })

/******/ });