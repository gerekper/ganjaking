/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","plugins"]
const external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: external ["wc","wcBlocksRegistry"]
const external_wc_wcBlocksRegistry_namespaceObject = window["wc"]["wcBlocksRegistry"];
;// CONCATENATED MODULE: external ["wc","blocksCheckout"]
const external_wc_blocksCheckout_namespaceObject = window["wc"]["blocksCheckout"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wc","wcSettings"]
const external_wc_wcSettings_namespaceObject = window["wc"]["wcSettings"];
;// CONCATENATED MODULE: external ["wc","priceFormat"]
const external_wc_priceFormat_namespaceObject = window["wc"]["priceFormat"];
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/utils/index.js
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
    return sprintf(
    /* translators: %1$s is the item's price, %2$s is the full amount to be paid, %3$s is the period of the payment plan . */
    __('%1$s - Payable in total %2$s over %3$s', 'woocommerce-deposits'), pricePlaceholder, formatPrice(fullAmount), // Already multiplied by the quantity.
    duration.join(', '));
  }

  return sprintf(
  /* translators: %1$s is the full amount to be paid over the %2$s period of the payment plan. */
  __('%1$s payable over %2$s', 'woocommerce-deposits'), pricePlaceholder, duration.join(', '));
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
        aggregatedIntervals[key]['label'] = sprintf(_nx('%s day', '%s days', interval.amount + aggregatedIntervals.week.amount * 7, 'Used in payment plans price description. 2+ will need plural, 1 will need singular.', 'woocommerce-deposits'), interval.amount + aggregatedIntervals.week.amount * 7);
        break;

      case 'month':
        aggregatedIntervals[key]['label'] = sprintf(_nx('%s month', '%s months', interval.amount, 'Used in payment plans price description. 2+ will need plural, 1 will need singular.', 'woocommerce-deposits'), interval.amount);
        break;

      case 'year':
        aggregatedIntervals[key]['label'] = sprintf(_nx('%s year', '%s years', interval.amount, 'Used in payment plans price description. 2+ will need plural, 1 will need singular.', 'woocommerce-deposits'), interval.amount);
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
  } = (0,external_wc_wcSettings_namespaceObject.getSetting)('woocommerce-deposits_data');
  return {
    disabledGateways
  };
};
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/get-payment-method-callbacks.js
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

/* harmony default export */ const get_payment_method_callbacks = (getPaymentMethodCallbacks);
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/future-payments/index.js


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

  const SHOW_TAXES = (0,external_wc_wcSettings_namespaceObject.getSetting)('taxesEnabled', true) && (0,external_wc_wcSettings_namespaceObject.getSetting)('displayCartPricesIncludingTax', false);
  const currency = (0,external_wc_priceFormat_namespaceObject.getCurrency)();
  return (0,external_wp_element_namespaceObject.createElement)(external_wc_blocksCheckout_namespaceObject.TotalsWrapper, null, (0,external_wp_element_namespaceObject.createElement)(external_wc_blocksCheckout_namespaceObject.TotalsItem, {
    className: "wc-block-components-totals-item wc-block-components-totals-footer-item",
    currency: currency,
    value: futurePaymentAmount,
    label: (0,external_wp_i18n_namespaceObject.__)('Future payments', 'woocommerce-deposits'),
    description: SHOW_TAXES ? (0,external_wp_i18n_namespaceObject.__)('Including taxes', 'woocommerce-deposits') : null
  }));
};
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/discount-applied/index.js


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

  const currency = (0,external_wc_priceFormat_namespaceObject.getCurrency)(); // deferredDiscountAmount is positive, but we need to display it as negative.

  return (0,external_wp_element_namespaceObject.createElement)(external_wc_blocksCheckout_namespaceObject.TotalsWrapper, null, (0,external_wp_element_namespaceObject.createElement)(external_wc_blocksCheckout_namespaceObject.TotalsItem, {
    className: "wc-block-components-totals-item wc-block-components-totals-discount wc-block-components-totals-footer-item",
    currency: currency,
    value: -deferredDiscountAmount - deferredDiscountTax,
    label: (0,external_wp_i18n_namespaceObject.__)('Discount', 'woocommerce-deposits'),
    description: (0,external_wp_i18n_namespaceObject.__)('Applied toward future payments', 'woocommerce-deposits')
  }));
};
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/filters/index.js
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
  (0,external_wc_blocksCheckout_namespaceObject.__experimentalRegisterCheckoutFilters)('woocommerce-deposits', {
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
        return (0,external_wp_i18n_namespaceObject.__)('Due today', 'woocommerce-deposits');
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
        (0,external_wp_i18n_namespaceObject.__)('Due today %s', 'woocommerce-deposits'), pricePlaceholder) : sprintf(
        /* translators: %s is the deposit amount to pay immediately (ie: $10). */
        (0,external_wp_i18n_namespaceObject.__)('%s due today', 'woocommerce-deposits'), pricePlaceholder);
      }

      return pricePlaceholder;
    }
  });
};
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/index.js


/**
 * External dependencies
 */



/**
 * Internal dependencies
 */




 // Handle first-load payment restrictions.

(0,external_wc_wcBlocksRegistry_namespaceObject.registerPaymentMethodExtensionCallbacks)('woocommerce-deposits', get_payment_method_callbacks());

const render = () => {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wc_blocksCheckout_namespaceObject.ExperimentalOrderMeta, null, (0,external_wp_element_namespaceObject.createElement)(DepositsDiscountApplied, null), (0,external_wp_element_namespaceObject.createElement)(DepositsFuturePayments, null)));
};

(0,external_wp_plugins_namespaceObject.registerPlugin)('woocommerce-deposits', {
  render,
  scope: 'woocommerce-checkout'
});
registerFilters();
/******/ })()
;