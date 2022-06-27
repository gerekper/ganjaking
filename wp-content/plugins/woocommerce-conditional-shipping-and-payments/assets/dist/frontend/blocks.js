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
/******/ 	return __webpack_require__(__webpack_require__.s = 7);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),
/* 1 */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["plugins"]; }());

/***/ }),
/* 2 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["wcBlocksRegistry"]; }());

/***/ }),
/* 3 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["blocksCheckout"]; }());

/***/ }),
/* 4 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["wcSettings"]; }());

/***/ }),
/* 5 */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),
/* 6 */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external ["wp","plugins"]
var external_wp_plugins_ = __webpack_require__(1);

// EXTERNAL MODULE: external ["wc","wcBlocksRegistry"]
var external_wc_wcBlocksRegistry_ = __webpack_require__(2);

// EXTERNAL MODULE: external ["wc","blocksCheckout"]
var external_wc_blocksCheckout_ = __webpack_require__(3);

// EXTERNAL MODULE: external ["wc","wcSettings"]
var external_wc_wcSettings_ = __webpack_require__(4);

// CONCATENATED MODULE: ./resources/js/frontend/blocks/utils/get-plugin-settings.js
/**
 * External dependencies
 */

/**
 * Helper for fetching plugin settings.
 */

const getPluginSettings = () => {
  const {
    gateways: allGateways,
    is_cart: isCart,
    is_debugger_enabled: isDebuggerEnabled
  } = Object(external_wc_wcSettings_["getSetting"])('woocommerce-conditional-shipping-and-payments_data');
  return {
    isCart,
    allGateways,
    isDebuggerEnabled
  };
};

/* harmony default export */ var get_plugin_settings = (getPluginSettings);
// CONCATENATED MODULE: ./resources/js/frontend/blocks/get-payment-method-callbacks.js
/**
 * Internal dependencies
 */

/**
 * Handle first load payment restrictions. Register a callback that runs on every payment method.
 *
 * @return {Array} Callback function for each payment method.
 */

const getPaymentMethodCallbacks = () => {
  const {
    allGateways
  } = get_plugin_settings();
  const callBacksConfig = {};

  if (!allGateways.length) {
    return;
  }

  for (const [key, paymentMethodName] of Object.entries(allGateways)) {
    callBacksConfig[paymentMethodName] = args => {
      const restrictionData = args.cart.extensions['woocommerce-conditional-shipping-and-payments'].restrictions.gateways;

      if (paymentMethodName in restrictionData) {
        const {
          is_hidden: isHidden
        } = restrictionData[paymentMethodName];
        return !isHidden;
      }

      return true;
    };
  }

  return callBacksConfig;
};

/* harmony default export */ var get_payment_method_callbacks = (getPaymentMethodCallbacks);
// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(5);

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__(6);

// CONCATENATED MODULE: ./resources/js/frontend/blocks/debugger-controller.js
/**
 * External dependencies
 */



/**
 * A dummy component acting as a JS controller for payment-related notices.
 *
 * @param {Object}   props
 * @param {Object}   props.extensions   The extension-added data on the WC Cart.
 * @param {Function} props.createNotice The function createNotice from Gutenberg.
 * @param {Function} props.removeNotice The function removeNotice from Gutenberg.
 * @return {null} Dummy Components return null.
 */

const DebuggerController = _ref => {
  let {
    extensions,
    createNotice,
    removeNotice
  } = _ref;
  const {
    gateways: gatewaysDebugNotice,
    shipping_countries: shippingDestinationsDebugNotice,
    shipping_methods: shippingMethodsDebugNotice
  } = extensions['woocommerce-conditional-shipping-and-payments'].debugger;
  /**
   * Displays debug message for payment gateways.
   *
   * @return {void}
   */

  const handlePaymentDebugMessage = Object(external_wp_element_["useCallback"])(() => {
    // Clear notice first.
    removeNotice('woocommerce-conditional-shipping-and-payments-debug-payment-method', 'wc/checkout');

    if (gatewaysDebugNotice.length) {
      createNotice('info', gatewaysDebugNotice, {
        context: 'wc/checkout',
        __unstableHTML: true,
        id: 'woocommerce-conditional-shipping-and-payments-debug-payment-method'
      });
    }
  }, [gatewaysDebugNotice, createNotice, removeNotice]);
  /**
   * Displays debug message for shipping destinations.
   *
   * @return {void}
   */

  const handleShippingDestinationsDebugMessage = Object(external_wp_element_["useCallback"])(() => {
    // Clear notice first.
    removeNotice('woocommerce-conditional-shipping-and-payments-debug-shipping-destinations', 'wc/checkout');

    if (shippingDestinationsDebugNotice.length) {
      createNotice('info', shippingDestinationsDebugNotice, {
        context: 'wc/checkout',
        __unstableHTML: true,
        id: 'woocommerce-conditional-shipping-and-payments-debug-shipping-destinations'
      });
    }
  }, [shippingDestinationsDebugNotice, createNotice, removeNotice]);
  /**
   * Displays debug message for shipping methods.
   *
   * @return {void}
   */

  const handleShippingMethodsDebugMessage = Object(external_wp_element_["useCallback"])(() => {
    // Clear notice first.
    removeNotice('woocommerce-conditional-shipping-and-payments-debug-shipping-methods', 'wc/checkout');

    if (shippingMethodsDebugNotice.length) {
      createNotice('info', shippingMethodsDebugNotice, {
        context: 'wc/checkout',
        __unstableHTML: true,
        id: 'woocommerce-conditional-shipping-and-payments-debug-shipping-methods'
      });
    }
  }, [shippingMethodsDebugNotice, createNotice, removeNotice]);
  /**
   * Handle notices.
   */

  Object(external_wp_element_["useEffect"])(() => {
    handlePaymentDebugMessage();
  }, [handlePaymentDebugMessage]);
  Object(external_wp_element_["useEffect"])(() => {
    handleShippingDestinationsDebugMessage();
  }, [handleShippingDestinationsDebugMessage]);
  Object(external_wp_element_["useEffect"])(() => {
    handleShippingMethodsDebugMessage();
  }, [handleShippingMethodsDebugMessage]);
  return null;
};
/**
 * Decorate the controller.
 */


/* harmony default export */ var debugger_controller = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    createNotice,
    removeNotice
  } = dispatch('core/notices');
  return {
    createNotice,
    removeNotice
  };
}))(DebuggerController));
// CONCATENATED MODULE: ./resources/js/frontend/blocks/index.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



 // Handle first-load payment restrictions.

Object(external_wc_wcBlocksRegistry_["registerPaymentMethodExtensionCallbacks"])('woocommerce-conditional-shipping-and-payments', get_payment_method_callbacks());

const render = () => {
  const {
    isCart,
    isDebuggerEnabled
  } = get_plugin_settings();

  if (!isDebuggerEnabled || isCart) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wc_blocksCheckout_["ExperimentalOrderMeta"], null, Object(external_wp_element_["createElement"])(debugger_controller, null));
};

Object(external_wp_plugins_["registerPlugin"])('woocommerce-conditional-shipping-and-payments', {
  render,
  scope: 'woocommerce-checkout'
});

/***/ })
/******/ ]);