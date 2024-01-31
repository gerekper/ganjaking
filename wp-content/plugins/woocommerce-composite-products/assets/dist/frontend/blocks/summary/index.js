/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps}                                 props icon is the SVG component to render
 *                                                          size is a number specifiying the icon size in pixels
 *                                                          Other props will be passed to wrapped SVG component
 * @param {import('react').ForwardedRef<HTMLElement>} ref   The forwarded ref to the SVG element.
 *
 * @return {JSX.Element}  Icon component
 */
function Icon({
  icon,
  size = 24,
  ...props
}, ref) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props,
    ref
  });
}
/* harmony default export */ var icon = ((0,external_wp_element_namespaceObject.forwardRef)(Icon));
//# sourceMappingURL=index.js.map
;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/receipt.js

/**
 * WordPress dependencies
 */

const receipt = (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_React_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M16.83 6.342l.602.3.625-.25.443-.176v12.569l-.443-.178-.625-.25-.603.301-1.444.723-2.41-.804-.475-.158-.474.158-2.41.803-1.445-.722-.603-.3-.625.25-.443.177V6.215l.443.178.625.25.603-.301 1.444-.722 2.41.803.475.158.474-.158 2.41-.803 1.445.722zM20 4l-1.5.6-1 .4-2-1-3 1-3-1-2 1-1-.4L5 4v17l1.5-.6 1-.4 2 1 3-1 3 1 2-1 1 .4 1.5.6V4zm-3.5 6.25v-1.5h-8v1.5h8zm0 3v-1.5h-8v1.5h8zm-8 3v-1.5h8v1.5h-8z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_receipt = (receipt);
//# sourceMappingURL=receipt.js.map
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/summary/edit.js

/**
 * External dependencies
 */





/**
 * Internal dependencies
 */



/**
 * Skeleton component
 *
 * @param {Object} props
 * @param {number} props.numberOfLines Number of lines to render.
 */
const Skeleton = ({
  numberOfLines = 1
}) => {
  const skeletonLines = Array.from({
    length: numberOfLines
  }, (_, index) => (0,external_React_namespaceObject.createElement)("span", {
    className: "wc-cp-block-components__skeleton__text-line",
    "aria-hidden": "true",
    key: index
  }));
  return (0,external_React_namespaceObject.createElement)("div", {
    className: "wc-cp-block-components__skeleton"
  }, skeletonLines);
};

/**
 * The Edit Component.
 *
 * @param {Object} props
 */
function Edit(props) {
  const {
    attributes,
    setAttributes
  } = props;
  const {
    display,
    title,
    maxColumns
  } = attributes;
  const isFixed = display === 'fixed';
  let className = 'cp_summary';
  if (isFixed) {
    className = className + ' cp_summary--grid cp_summary--column-' + parseInt(maxColumns, 10);
  }
  const displayOptions = [{
    label: 'List',
    value: 'default'
  }, {
    label: 'Carousel',
    value: 'fixed'
  }];
  const blockProps = (0,external_wp_blockEditor_namespaceObject.useBlockProps)();
  return (0,external_React_namespaceObject.createElement)("div", {
    ...blockProps
  }, (0,external_React_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.InspectorControls, null, (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Settings')
  }, (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.SelectControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Display'),
    help: isFixed ? (0,external_wp_i18n_namespaceObject.__)("Selection details will be rendered in a carousel. The carousel will be fixed at the bottom of your shoppers' browser window, and will remain hidden on mobile screens.", 'woocommerce-composite-products') : (0,external_wp_i18n_namespaceObject.__)('Selection details will be summarized in a list. Recommended when adding the Summary in a Column ancestor block.', 'woocommerce-composite-products'),
    value: display,
    options: displayOptions.map(option => ({
      label: option.label,
      value: option.value
    })),
    onChange: newDisplay => setAttributes({
      display: newDisplay
    })
  }), !isFixed && (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Title'),
    value: title,
    help: (0,external_wp_i18n_namespaceObject.__)('Title to display before the summary.', 'woocommerce-composite-products'),
    onChange: newTitle => setAttributes({
      title: newTitle
    })
  }), isFixed && (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Notice, {
    className: "wc-block-components-composite-summary-fixed-notice",
    isDismissible: false,
    status: "info"
  }, (0,external_wp_i18n_namespaceObject.__)('Note: The block position in the editor is not indicative of the position on the front end. The block will always be positioned at the bottom of the page.', 'woocommerce-composite-products')), isFixed && (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.RangeControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Columns'),
    help: "Maximum number of columns to display in the carousel.",
    value: maxColumns,
    step: 1,
    min: 2,
    max: 5,
    onChange: newColumns => setAttributes({
      maxColumns: parseInt(newColumns, 10)
    })
  }))), (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Tooltip, {
    text: isFixed ? (0,external_wp_i18n_namespaceObject.__)('Current selections will be rendered in a carousel.', 'woocommerce-composite-products') : (0,external_wp_i18n_namespaceObject.__)('Current selections will be summarized in a list.', 'woocommerce-composite-products'),
    position: "bottom right"
  }, (0,external_React_namespaceObject.createElement)("div", {
    className: className
  }, !isFixed && (0,external_React_namespaceObject.createElement)("h3", {
    className: "cp_summary__title"
  }, title), (0,external_React_namespaceObject.createElement)("div", {
    className: "cp_summary__options"
  }, isFixed && (0,external_React_namespaceObject.createElement)("span", {
    className: "cp_summary__options__arrow cp_summary__options__arrow--left wc-cp-block-components__skeleton__text-line",
    "aria-hidden": "true"
  }), (0,external_React_namespaceObject.createElement)(Skeleton, {
    numberOfLines: isFixed ? maxColumns : 3
  }), isFixed && (0,external_React_namespaceObject.createElement)("span", {
    className: "cp_summary__options__arrow cp_summary__options__arrow--right wc-cp-block-components__skeleton__text-line",
    "aria-hidden": "true"
  })), (0,external_React_namespaceObject.createElement)("div", {
    className: "cp_summary__add-to-cart"
  }, (0,external_React_namespaceObject.createElement)(Skeleton, {
    numberOfLines: 1
  }), (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Disabled, null, (0,external_React_namespaceObject.createElement)("div", {
    className: "cp_summary__add-to-cart__button"
  }, (0,external_React_namespaceObject.createElement)("input", {
    type: 'number',
    value: '1',
    className: 'wc-block-editor-add-to-cart-form__quantity',
    readOnly: true
  }), (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: 'primary',
    className: 'wc-block-editor-add-to-cart-form__button'
  }, (0,external_wp_i18n_namespaceObject.__)('Add to cart', 'woocommerce-composite-products'))))))));
}
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/summary/block.json
var block_namespaceObject = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","version":"8.10.5","apiVersion":3,"name":"woocommerce-composite-products/configuration-summary","title":"Composite Product Summary","description":"The Composite Product Summary block is provided to enhance the experience of building and shopping personalized Composite Products. It provides an overview of all selections, including subtotals, and quick access individual Components and the add-to-cart button.","textdomain":"woocommerce-composite-products","category":"woocommerce","attributes":{"display":{"type":"string","default":"default"},"title":{"type":"string","default":"Your Selections"},"maxColumns":{"type":"integer","default":3}},"editorScript":"file:./index.js","editorStyle":"file:./index.css"}');
;// CONCATENATED MODULE: ./resources/js/frontend/blocks/summary/index.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



/**
 * Register block.
 */
(0,external_wp_blocks_namespaceObject.registerBlockType)(block_namespaceObject.name, {
  ...block_namespaceObject,
  icon: {
    src: (0,external_React_namespaceObject.createElement)(icon, {
      icon: library_receipt,
      className: "wc-block-editor-components-block-icon wc-block-editor-components-block-icon--receipt"
    })
  },
  /**
   * @see ./edit.js
   */
  edit: Edit,
  save: () => {
    return null;
  }
});
/******/ })()
;