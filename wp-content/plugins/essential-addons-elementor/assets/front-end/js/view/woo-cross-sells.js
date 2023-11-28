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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/woo-cross-sells.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/woo-cross-sells.js":
/*!****************************************!*\
  !*** ./src/js/view/woo-cross-sells.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("jQuery(window).on(\"elementor/frontend/init\", function () {\n  var wooCrossSells = function wooCrossSells($scope, $) {\n    if ($scope.find('.eael-cs-products-container.style-1').length) {\n      $(document).ajaxComplete(function (event, xhr, settings) {\n        if (settings.url === '/?wc-ajax=add_to_cart') {\n          var add_to_cart_btn = $scope.find('.ajax_add_to_cart.added');\n          if (add_to_cart_btn.length) {\n            add_to_cart_btn.each(function () {\n              if ($(this).next().length < 1) {\n                $(this).closest('.eael-cs-purchasable').removeClass('eael-cs-purchasable');\n              }\n            });\n          }\n        }\n      });\n    } else if ($scope.find('.eael-cs-products-container.style-2.eael-custom-image-area').length) {\n      var productInfoHeight = 0,\n        wrapperHeight = 0;\n      $('.eael-cs-product-info', $scope).each(function () {\n        var localHeight = parseInt($(this).css('height'));\n        productInfoHeight = productInfoHeight < localHeight ? localHeight : productInfoHeight;\n      });\n      $('.eael-cs-single-product', $scope).each(function () {\n        var localHeight = parseInt($(this).css('height'));\n        wrapperHeight = wrapperHeight < localHeight ? localHeight : wrapperHeight;\n      });\n      $('.eael-cs-products-container.style-2 .eael-cs-product-image', $scope).css('max-height', \"calc(100% - \".concat(productInfoHeight, \"px)\"));\n      $('.eael-cs-products-container.style-2 .eael-cs-single-product', $scope).css('height', \"\".concat(wrapperHeight, \"px\"));\n    }\n  };\n  if (ea.elementStatusCheck('eaelWooCrossSells')) {\n    return false;\n  }\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-woo-cross-sells.default\", wooCrossSells);\n});\n\n//# sourceURL=webpack:///./src/js/view/woo-cross-sells.js?");

/***/ })

/******/ });