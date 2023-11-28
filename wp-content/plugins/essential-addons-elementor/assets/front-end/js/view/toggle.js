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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/toggle.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/toggle.js":
/*!*******************************!*\
  !*** ./src/js/view/toggle.js ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("ea.hooks.addAction(\"init\", \"ea\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/eael-toggle.default\", function ($scope, $) {\n    var context = $scope[0];\n\n    // make primary active on init\n    context.querySelector(\".eael-primary-toggle-label\").classList.add(\"active\");\n    context.querySelector(\".eael-toggle-switch\").onclick = function (e) {\n      e.preventDefault();\n      var current = context.querySelector(\".eael-toggle-content-wrap\").classList.contains(\"primary\") ? \"primary\" : \"secondary\";\n      if (current == \"primary\") {\n        context.querySelector(\".eael-toggle-content-wrap\").classList.remove(\"primary\");\n        context.querySelector(\".eael-toggle-content-wrap\").classList.add(\"secondary\");\n        context.querySelector(\".eael-toggle-switch-container\").classList.add(\"eael-toggle-switch-on\");\n        context.querySelector(\".eael-primary-toggle-label\").classList.remove(\"active\");\n        context.querySelector(\".eael-secondary-toggle-label\").classList.add(\"active\");\n      } else {\n        context.querySelector(\".eael-toggle-content-wrap\").classList.add(\"primary\");\n        context.querySelector(\".eael-toggle-content-wrap\").classList.remove(\"secondary\");\n        context.querySelector(\".eael-toggle-switch-container\").classList.remove(\"eael-toggle-switch-on\");\n        context.querySelector(\".eael-primary-toggle-label\").classList.add(\"active\");\n        context.querySelector(\".eael-secondary-toggle-label\").classList.remove(\"active\");\n      }\n      ea.hooks.doAction(\"ea-toogle-triggered\", context);\n    };\n  });\n});\n\n//# sourceURL=webpack:///./src/js/view/toggle.js?");

/***/ })

/******/ });