/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wc","blocksCheckout"]
var external_wc_blocksCheckout_namespaceObject = window["wc"]["blocksCheckout"];
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/checkout/index.js
/**
 * External dependencies
 */

(0,external_wc_blocksCheckout_namespaceObject.__experimentalRegisterCheckoutFilters)('composite-products', {
  cartItemClass: (classlist, {
    composites
  }, {
    context,
    cartItem
  }) => {
    if (composites) {
      const classes = [];
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
        if (composites.composited_item_data.is_hidden_in_cart && context === 'cart') {
          classes.push('is-composited__hidden');
        }
        if (composites.composited_item_data.is_hidden_in_summary && context === 'summary') {
          classes.push('is-composited__hidden');
        }
        if (composites.composited_item_data.is_meta_hidden_in_cart && context === 'cart') {
          classes.push('is-composited__meta_hidden');
        }
        if (composites.composited_item_data.is_meta_hidden_in_summary && context === 'summary') {
          classes.push('is-composited__meta_hidden');
        }
      } else if (composites.composite_children) {
        classes.push('is-composite');
        classes.push('is-composite__cid_' + cartItem.id);
        if (composites.composite_data.is_editable) {
          classes.push('is-composite__editable');
        }
        if (composites.composite_data.is_meta_hidden_in_cart && context === 'cart') {
          classes.push('is-composite__meta_hidden');
        }
        if (composites.composite_data.is_meta_hidden_in_summary && context === 'summary') {
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
/******/ })()
;