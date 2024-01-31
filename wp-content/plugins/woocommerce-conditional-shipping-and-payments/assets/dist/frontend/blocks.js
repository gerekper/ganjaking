/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","plugins"]
var external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: external ["wc","wcBlocksRegistry"]
var external_wc_wcBlocksRegistry_namespaceObject = window["wc"]["wcBlocksRegistry"];
;// CONCATENATED MODULE: external ["wc","blocksCheckout"]
var external_wc_blocksCheckout_namespaceObject = window["wc"]["blocksCheckout"];
;// CONCATENATED MODULE: external ["wc","wcSettings"]
var external_wc_wcSettings_namespaceObject = window["wc"]["wcSettings"];
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/utils/get-plugin-settings.js
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
  } = (0,external_wc_wcSettings_namespaceObject.getSetting)('woocommerce-conditional-shipping-and-payments_data');
  return {
    isCart,
    allGateways,
    isDebuggerEnabled
  };
};
/* harmony default export */ var get_plugin_settings = (getPluginSettings);
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
    allGateways
  } = get_plugin_settings();
  const callBacksConfig = {};
  if (!allGateways.length) {
    return;
  }

  // eslint-disable-next-line no-unused-vars, @typescript-eslint/no-unused-vars
  for (const [_, paymentMethodName] of Object.entries(allGateways)) {
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
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/debugger-controller.js
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
const DebuggerController = ({
  extensions,
  createNotice,
  removeNotice
}) => {
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
  const handlePaymentDebugMessage = (0,external_wp_element_namespaceObject.useCallback)(() => {
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
  const handleShippingDestinationsDebugMessage = (0,external_wp_element_namespaceObject.useCallback)(() => {
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
  const handleShippingMethodsDebugMessage = (0,external_wp_element_namespaceObject.useCallback)(() => {
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
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    handlePaymentDebugMessage();
  }, [handlePaymentDebugMessage]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    handleShippingDestinationsDebugMessage();
  }, [handleShippingDestinationsDebugMessage]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    handleShippingMethodsDebugMessage();
  }, [handleShippingMethodsDebugMessage]);
  return null;
};

/**
 * Decorate the controller.
 */
/* harmony default export */ var debugger_controller = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withDispatch)(dispatch => {
  const {
    createNotice,
    removeNotice
  } = dispatch('core/notices');
  return {
    createNotice,
    removeNotice
  };
}))(DebuggerController));
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/index.js

/**
 * External dependencies
 */




/**
 * Internal dependencies
 */




// Handle first-load payment restrictions.
(0,external_wc_wcBlocksRegistry_namespaceObject.registerPaymentMethodExtensionCallbacks)('woocommerce-conditional-shipping-and-payments', get_payment_method_callbacks());
const render = () => {
  const {
    isCart,
    isDebuggerEnabled
  } = get_plugin_settings();
  if (!isDebuggerEnabled || isCart) {
    return null;
  }
  return (0,external_React_namespaceObject.createElement)(external_wc_blocksCheckout_namespaceObject.ExperimentalOrderMeta, null, (0,external_React_namespaceObject.createElement)(debugger_controller, null));
};
(0,external_wp_plugins_namespaceObject.registerPlugin)('woocommerce-conditional-shipping-and-payments', {
  render,
  scope: 'woocommerce-checkout'
});
/******/ })()
;