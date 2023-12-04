/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external ["wc","blocksCheckout"]
var external_wc_blocksCheckout_namespaceObject = window["wc"]["blocksCheckout"];
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/checkout/index.js
/**
 * External dependencies
 */

(0,external_wc_blocksCheckout_namespaceObject.__experimentalRegisterCheckoutFilters)('product-bundles', {
  cartItemClass: (classlist, {
    bundles
  }, {
    context,
    cartItem
  }) => {
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
/******/ })()
;