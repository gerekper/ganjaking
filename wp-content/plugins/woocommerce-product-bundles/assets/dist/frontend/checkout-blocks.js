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
/******/ 	return __webpack_require__(__webpack_require__.s = 25);
/******/ })
/************************************************************************/
/******/ ({

/***/ 22:
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["blocksCheckout"]; }());

/***/ }),

/***/ 25:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(22);
/* harmony import */ var _woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_0__);
/**
 * External dependencies
 */

Object(_woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_0__["__experimentalRegisterCheckoutFilters"])('product-bundles', {
  cartItemClass: (classlist, _ref, _ref2) => {
    let {
      bundles
    } = _ref;
    let {
      context,
      cartItem
    } = _ref2;
    if (bundles) {
      const classes = [];
      if (bundles.bundled_by) {
        classes.push('is-bundled');
        classes.push('is-bundled__cid_' + bundles.bundled_item_data.bundle_id);
        classes.push('is-bundled__iid_' + bundles.bundled_item_data.bundled_item_id);
        if (bundles.bundled_item_data.is_indented) {
          classes.push('is-bundled__indented');
        }
        if (bundles.bundled_item_data.is_last) {
          classes.push('is-bundled__last');
        }
        if (bundles.bundled_item_data.is_removable) {
          classes.push('is-bundled__removable');
        }
        if (bundles.bundled_item_data.is_subtotal_aggregated) {
          classes.push('is-bundled__subtotal_aggregated');
        }
        if (bundles.bundled_item_data.is_price_hidden) {
          classes.push('is-bundled__price_hidden');
        }
        if (bundles.bundled_item_data.is_subtotal_hidden) {
          classes.push('is-bundled__subtotal_hidden');
        }
        if (bundles.bundled_item_data.is_hidden_in_cart && context === 'cart') {
          classes.push('is-bundled__hidden');
        }
        if (bundles.bundled_item_data.is_hidden_in_summary && context === 'summary') {
          classes.push('is-bundled__hidden');
        }
        if (bundles.bundled_item_data.is_thumbnail_hidden) {
          classes.push('is-bundled__thumbnail_hidden');
        }
        if (bundles.bundled_item_data.is_parent_visible) {
          classes.push('is-bundled__description_hidden');
        }
        if (bundles.bundled_item_data.is_composited) {
          classes.push('is-bundled__composited');
        }
        if (bundles.bundled_item_data.is_ungrouped) {
          classes.push('is-bundled__ungrouped');
        }
      } else if (bundles.bundled_items) {
        classes.push('is-bundle');
        classes.push('is-bundle__cid_' + cartItem.id);
        if (bundles.bundle_data.is_editable) {
          classes.push('is-bundle__editable');
        }
        if (bundles.bundle_data.is_hidden) {
          classes.push('is-bundle__hidden');
        }
        if (bundles.bundle_data.is_title_hidden) {
          classes.push('is-bundle__title_hidden');
        }
        if (bundles.bundle_data.is_price_hidden) {
          classes.push('is-bundle__price_hidden');
        }
        if (bundles.bundle_data.is_subtotal_hidden) {
          classes.push('is-bundle__subtotal_hidden');
        }
        if (bundles.bundle_data.is_meta_hidden_in_cart && context === 'cart') {
          classes.push('is-bundle__meta_hidden');
        }
        if (bundles.bundle_data.is_meta_hidden_in_summary && context === 'summary') {
          classes.push('is-bundle__meta_hidden');
        }
      }
      if (classes.length) {
        classlist += ' ' + classes.join(' ');
      }
    }
    return classlist;
  }
});

/***/ })

/******/ });