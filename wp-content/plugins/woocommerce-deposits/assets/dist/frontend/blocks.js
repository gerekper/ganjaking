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

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),
/* 1 */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),
/* 2 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["blocksCheckout"]; }());

/***/ }),
/* 3 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["priceFormat"]; }());

/***/ }),
/* 4 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["wcSettings"]; }());

/***/ }),
/* 5 */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["plugins"]; }());

/***/ }),
/* 6 */
/***/ (function(module, exports) {

(function() { module.exports = window["wc"]["wcBlocksRegistry"]; }());

/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(1);

// EXTERNAL MODULE: external ["wp","plugins"]
var external_wp_plugins_ = __webpack_require__(5);

// EXTERNAL MODULE: external ["wc","wcBlocksRegistry"]
var external_wc_wcBlocksRegistry_ = __webpack_require__(6);

// EXTERNAL MODULE: external ["wc","blocksCheckout"]
var external_wc_blocksCheckout_ = __webpack_require__(2);

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(0);

// EXTERNAL MODULE: external ["wc","wcSettings"]
var external_wc_wcSettings_ = __webpack_require__(4);

// EXTERNAL MODULE: external ["wc","priceFormat"]
var external_wc_priceFormat_ = __webpack_require__(3);

// CONCATENATED MODULE: ./resources/js/frontend/blocks/utils/index.js
/**
 * External dependencies
 */



/**
 * Creates a duration string from a duration object that holds 4 plan intervals.
 *
 * @param {Array}   schedule           Payment plan schedule.
 * @param {string}  pricePlaceholder   <price/> string interpolation.
 * @param {integer} quantity           Line item quantity.
 * @param {integer} fullAmount         Full amount to be paid.
 * @return {string}                    The duration string.
 */

const getDuration = (schedule, pricePlaceholder, quantity, fullAmount) => {
  const intervals = getAggregatedIntervalResolution(schedule);
  const duration = [];

  if (intervals.year.amount > 0) {
    duration.push(intervals.year.label);
  }

  if (intervals.month.amount > 0) {
    duration.push(intervals.month.label);
  }

  if (intervals.day.amount > 0) {
    duration.push(intervals.day.label);
  }

  if (quantity > 1) {
    return Object(external_wp_i18n_["sprintf"])(
    /* translators: %1$s is the item's price, %2$s is the full amount to be paid, %3$s is the period of the payment plan . */
    Object(external_wp_i18n_["__"])('%1$s - Payable in total %2$s over %3$s', 'woocommerce-deposits'), pricePlaceholder, Object(external_wc_priceFormat_["formatPrice"])(fullAmount), // Already multiplied by the quantity.
    duration.join(', '));
  }

  return Object(external_wp_i18n_["sprintf"])(
  /* translators: %1$s is the full amount to be paid over the %2$s period of the payment plan. */
  Object(external_wp_i18n_["__"])('%1$s payable over %2$s', 'woocommerce-deposits'), pricePlaceholder, duration.join(', '));
};
/**
 * Constructs the duration string from the intervals object.
 *
 * @param {Array} intervals    Payment plan intervals.
 * @return {Object}            Amount of period, and human-readable intervals (maybe pluralized).
 */

const getAggregatedIntervalResolution = intervals => {
  const aggregatedIntervals = {
    day: {
      amount: 0,
      label: ''
    },
    week: {
      amount: 0,
      label: ''
    },
    month: {
      amount: 0,
      label: ''
    },
    year: {
      amount: 0,
      label: ''
    }
  };

  for (const [key, interval] of Object.entries(intervals)) {
    // If the schedule_index is 0, then it's the first part of the plan, which is always a deposit.
    if ('0' === interval.schedule_index) {
      continue;
    }

    aggregatedIntervals[interval.interval_unit]['amount'] += parseInt(interval.interval_amount, 10);
  }

  for (const [key, interval] of Object.entries(aggregatedIntervals)) {
    if (!interval.amount) {
      continue;
    } // We don't need to switch over "week" as we multiply by 7 and add it to the days.


    switch (key) {
      case 'day':
        aggregatedIntervals[key]['label'] = Object(external_wp_i18n_["sprintf"])(Object(external_wp_i18n_["_nx"])('%s day', '%s days', interval.amount + aggregatedIntervals.week.amount * 7, 'Used in payment plans price description. 2+ will need plural, 1 will need singular.', 'woocommerce-deposits'), interval.amount + aggregatedIntervals.week.amount * 7);
        break;

      case 'month':
        aggregatedIntervals[key]['label'] = Object(external_wp_i18n_["sprintf"])(Object(external_wp_i18n_["_nx"])('%s month', '%s months', interval.amount, 'Used in payment plans price description. 2+ will need plural, 1 will need singular.', 'woocommerce-deposits'), interval.amount);
        break;

      case 'year':
        aggregatedIntervals[key]['label'] = Object(external_wp_i18n_["sprintf"])(Object(external_wp_i18n_["_nx"])('%s year', '%s years', interval.amount, 'Used in payment plans price description. 2+ will need plural, 1 will need singular.', 'woocommerce-deposits'), interval.amount);
        break;
    }
  }

  return aggregatedIntervals;
};
/**
 * Helper for fetching plugin settings.
 */

const getPluginSettings = () => {
  const {
    disabled_gateways: disabledGateways
  } = Object(external_wc_wcSettings_["getSetting"])('woocommerce-deposits_data');
  return {
    disabledGateways
  };
};
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
    disabledGateways
  } = getPluginSettings();
  const callBacksConfig = {};

  if (!disabledGateways.length) {
    return callBacksConfig;
  }

  for (const [key, paymentMethodName] of Object.entries(disabledGateways)) {
    callBacksConfig[paymentMethodName] = args => {
      var _args$cart$extensions;

      const hasDeposit = (_args$cart$extensions = args.cart.extensions['woocommerce-deposits']) === null || _args$cart$extensions === void 0 ? void 0 : _args$cart$extensions.has_deposit;
      return !hasDeposit;
    };
  }

  return callBacksConfig;
};

/* harmony default export */ var get_payment_method_callbacks = (getPaymentMethodCallbacks);
// CONCATENATED MODULE: ./resources/js/frontend/blocks/future-payments/index.js


/**
 * External dependencies
 */




/**
 * Internal dependencies
 */

/**
 * This component is responsible for rending recurring totals.
 * It has to be the highest level item directly inside the SlotFill
 * to receive properties passed from Cart and Checkout.
 *
 * extensions is data registered into `/cart` endpoint.
 *
 * @param {Object} props            Passed props from SlotFill to this component.
 * @param {Object} props.extensions data registered into `/cart` endpoint.
 * @param {Object} props.cart       cart endpoint data in readonly mode.
 */

const DepositsFuturePayments = _ref => {
  let {
    extensions
  } = _ref;

  // Bail out early.
  if (undefined === extensions['woocommerce-deposits']) {
    return null;
  }

  const {
    has_deposit: hasDeposit,
    future_payment_amount: futurePaymentAmount
  } = extensions['woocommerce-deposits'];

  if (!hasDeposit || futurePaymentAmount <= 0) {
    return null;
  }

  const SHOW_TAXES = Object(external_wc_wcSettings_["getSetting"])('taxesEnabled', true) && Object(external_wc_wcSettings_["getSetting"])('displayCartPricesIncludingTax', false);
  const currency = Object(external_wc_priceFormat_["getCurrency"])();
  return Object(external_wp_element_["createElement"])(external_wc_blocksCheckout_["TotalsWrapper"], null, Object(external_wp_element_["createElement"])(external_wc_blocksCheckout_["TotalsItem"], {
    className: "wc-block-components-totals-item wc-block-components-totals-footer-item",
    currency: currency,
    value: futurePaymentAmount,
    label: Object(external_wp_i18n_["__"])('Future payments', 'woocommerce-deposits'),
    description: SHOW_TAXES ? Object(external_wp_i18n_["__"])('Including taxes', 'woocommerce-deposits') : null
  }));
};
// CONCATENATED MODULE: ./resources/js/frontend/blocks/discount-applied/index.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */

/**
 * This component is responsible for rending recurring totals.
 * It has to be the highest level item directly inside the SlotFill
 * to receive properties passed from Cart and Checkout.
 *
 * extensions is data registered into `/cart` endpoint.
 *
 * @param {Object} props            Passed props from SlotFill to this component.
 * @param {Object} props.extensions data registered into `/cart` endpoint.
 * @param {Object} props.cart       cart endpoint data in readonly mode.
 */

const DepositsDiscountApplied = _ref => {
  let {
    extensions
  } = _ref;

  // Bail out early.
  if (undefined === extensions['woocommerce-deposits']) {
    return null;
  }

  const {
    has_deposit: hasDeposit,
    deferred_discount_amount: deferredDiscountAmount,
    deferred_discount_tax: deferredDiscountTax
  } = extensions['woocommerce-deposits'];

  if (!hasDeposit || deferredDiscountAmount <= 0) {
    return null;
  }

  const currency = Object(external_wc_priceFormat_["getCurrency"])(); // deferredDiscountAmount is positive, but we need to display it as negative.

  return Object(external_wp_element_["createElement"])(external_wc_blocksCheckout_["TotalsWrapper"], null, Object(external_wp_element_["createElement"])(external_wc_blocksCheckout_["TotalsItem"], {
    className: "wc-block-components-totals-item wc-block-components-totals-discount wc-block-components-totals-footer-item",
    currency: currency,
    value: -deferredDiscountAmount - deferredDiscountTax,
    label: Object(external_wp_i18n_["__"])('Discount', 'woocommerce-deposits'),
    description: Object(external_wp_i18n_["__"])('Applied toward future payments', 'woocommerce-deposits')
  }));
};
// CONCATENATED MODULE: ./resources/js/frontend/blocks/filters/index.js
/**
 * External dependencies
 */


/**
 * This is the filter integration API, it uses registerCheckoutFilters
 * to register its filters, each filter is a key: function pair.
 * The key the filter name, and the function is the filter.
 *
 * Each filter function is passed the previous (or default) value in that filter
 * as the first parameter, the second parameter is an object of 3PD registered data.
 * For Deposits, we register our data with key `woocommerce-deposits`.
 * Filters must return the previous value or a new value with the same type.
 * If an error is thrown, it would be visible for store managers only.
 */

const registerFilters = () => {
  Object(external_wc_blocksCheckout_["__experimentalRegisterCheckoutFilters"])('woocommerce-deposits', {
    // deposits data here comes from register_endpoint_data /cart registration.
    totalLabel: (label, extensions) => {
      // Bail out early.
      if (undefined === extensions['woocommerce-deposits']) {
        return label;
      }

      const {
        has_deposit: hasDeposit
      } = extensions['woocommerce-deposits'];

      if (hasDeposit) {
        return Object(external_wp_i18n_["__"])('Due today', 'woocommerce-deposits');
      }

      return label;
    },
    cartItemClass: (classlist, extensions, args) => {
      // Bail out early.
      if (undefined === extensions['woocommerce-deposits']) {
        return classlist;
      }

      const {
        is_deposit: isDeposit
      } = extensions['woocommerce-deposits'];

      if (isDeposit) {
        classlist += ' is-deposit';
      }

      return classlist;
    },
    cartItemPrice: (pricePlaceholder, extensions, _ref) => {
      let {
        context
      } = _ref;

      // Bail out early.
      if (undefined === extensions['woocommerce-deposits']) {
        return pricePlaceholder;
      }

      const {
        is_deposit: isDeposit
      } = extensions['woocommerce-deposits'];

      if (isDeposit) {
        return 'cart' === context ? sprintf(
        /* translators: %s is the deposit amount to pay immediately (ie: $10). */
        Object(external_wp_i18n_["__"])('Due today %s', 'woocommerce-deposits'), pricePlaceholder) : sprintf(
        /* translators: %s is the deposit amount to pay immediately (ie: $10). */
        Object(external_wp_i18n_["__"])('%s due today', 'woocommerce-deposits'), pricePlaceholder);
      }

      return pricePlaceholder;
    }
  });
};
// CONCATENATED MODULE: ./resources/js/frontend/blocks/index.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */




 // Handle first-load payment restrictions.

Object(external_wc_wcBlocksRegistry_["registerPaymentMethodExtensionCallbacks"])('woocommerce-deposits', get_payment_method_callbacks());

const render = () => {
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wc_blocksCheckout_["ExperimentalOrderMeta"], null, Object(external_wp_element_["createElement"])(DepositsDiscountApplied, null), Object(external_wp_element_["createElement"])(DepositsFuturePayments, null)));
};

Object(external_wp_plugins_["registerPlugin"])('woocommerce-deposits', {
  render,
  scope: 'woocommerce-checkout'
});
registerFilters();

/***/ })
/******/ ]);