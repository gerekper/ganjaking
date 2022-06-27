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

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ 22:
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["blocksCheckout"]; }());

/***/ }),

/***/ 25:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(2);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(22);
/* harmony import */ var _woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _woocommerce_price_format__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(26);
/* harmony import */ var _woocommerce_price_format__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_woocommerce_price_format__WEBPACK_IMPORTED_MODULE_2__);
/**
 * External dependencies
 */




Object(_woocommerce_blocks_checkout__WEBPACK_IMPORTED_MODULE_1__["__experimentalRegisterCheckoutFilters"])('composite-products', {
  cartItemClass: (classlist, _ref, _ref2) => {
    let {
      composites
    } = _ref;
    let {
      context,
      cartItem
    } = _ref2;

    if (composites) {
      let classes = [];

      if (composites.composite_parent) {
        classes.push('is-composited');
        classes.push('is-composited__cid_' + composites.composited_item_data.component_id);
        classes.push('is-composited__pid_' + composites.composited_item_data.composited_product_id);
        classes.push('is-composited__ctitle_' + composites.composited_item_data.component_title_sanitized);

        if (composites.composited_item_data.is_indented) {
          classes.push('is-composited__indented');
        }

        if (composites.composited_item_data.is_removable) {
          classes.push('is-composited__removable');
        }

        if (composites.composited_item_data.is_last) {
          classes.push('is-composited__last');
        }

        if (composites.composited_item_data.is_subtotal_aggregated) {
          classes.push('is-composited__subtotal_aggregated');
        }

        if (composites.composited_item_data.is_price_hidden) {
          classes.push('is-composited__price_hidden');
        }

        if (composites.composited_item_data.is_subtotal_hidden) {
          classes.push('is-composited__subtotal_hidden');
        }

        classes.push('is-composited__description_hidden');

        if (composites.composited_item_data.is_hidden_in_cart && 'cart' === context) {
          classes.push('is-composited__hidden');
        }

        if (composites.composited_item_data.is_hidden_in_summary && 'summary' === context) {
          classes.push('is-composited__hidden');
        }

        if (composites.composited_item_data.is_meta_hidden_in_cart && 'cart' === context) {
          classes.push('is-composited__meta_hidden');
        }

        if (composites.composited_item_data.is_meta_hidden_in_summary && 'summary' === context) {
          classes.push('is-composited__meta_hidden');
        }
      } else if (composites.composite_children) {
        classes.push('is-composite');
        classes.push('is-composite__cid_' + cartItem.id);

        if (composites.composite_data.is_editable) {
          classes.push('is-composite__editable');
        }

        if (composites.composite_data.is_meta_hidden_in_cart && 'cart' === context) {
          classes.push('is-composite__meta_hidden');
        }

        if (composites.composite_data.is_meta_hidden_in_summary && 'summary' === context) {
          classes.push('is-composite__meta_hidden');
        }

        if (composites.composite_data.is_price_hidden) {
          classes.push('is-composite__price_hidden');
        }

        if (composites.composite_data.is_subtotal_hidden) {
          classes.push('is-composite__subtotal_hidden');
        }
      }

      if (classes.length) {
        classlist += ' ' + classes.join(' ');
      }
    }

    return classlist;
  }
});

/***/ }),

/***/ 26:
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["priceFormat"]; }());

/***/ })

/******/ });