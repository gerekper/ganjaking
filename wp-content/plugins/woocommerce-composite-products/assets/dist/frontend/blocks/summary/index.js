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
/******/ 	return __webpack_require__(__webpack_require__.s = 31);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ 1:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ 17:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ 18:
/***/ (function(module) {

module.exports = JSON.parse("{\"$schema\":\"https://schemas.wp.org/trunk/block.json\",\"version\":\"x.x.x\",\"apiVersion\":3,\"name\":\"woocommerce-composite-products/configuration-summary\",\"title\":\"Composite Product Summary\",\"description\":\"The Composite Product Summary block is provided to enhance the experience of building and shopping personalized Composite Products. It provides an overview of all selections, including subtotals, and quick access individual Components and the add-to-cart button.\",\"textdomain\":\"woocommerce-composite-products\",\"category\":\"woocommerce\",\"attributes\":{\"display\":{\"type\":\"string\",\"default\":\"default\"},\"title\":{\"type\":\"string\",\"default\":\"Your Selections\"},\"maxColumns\":{\"type\":\"integer\",\"default\":3}},\"editorScript\":\"file:./index.js\",\"editorStyle\":\"file:./index.css\"}");

/***/ }),

/***/ 19:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ 26:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ 31:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__(26);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */


/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */
function Icon({
  icon,
  size = 24,
  ...props
}) {
  return Object(external_wp_element_["cloneElement"])(icon, {
    width: size,
    height: size,
    ...props
  });
}
/* harmony default export */ var build_module_icon = (Icon);
//# sourceMappingURL=index.js.map
// EXTERNAL MODULE: external ["wp","primitives"]
var external_wp_primitives_ = __webpack_require__(19);

// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/receipt.js

/**
 * WordPress dependencies
 */

const receipt = Object(external_wp_element_["createElement"])(external_wp_primitives_["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(external_wp_element_["createElement"])(external_wp_primitives_["Path"], {
  fillRule: "evenodd",
  d: "M16.83 6.342l.602.3.625-.25.443-.176v12.569l-.443-.178-.625-.25-.603.301-1.444.723-2.41-.804-.475-.158-.474.158-2.41.803-1.445-.722-.603-.3-.625.25-.443.177V6.215l.443.178.625.25.603-.301 1.444-.722 2.41.803.475.158.474-.158 2.41-.803 1.445.722zM20 4l-1.5.6-1 .4-2-1-3 1-3-1-2 1-1-.4L5 4v17l1.5-.6 1-.4 2 1 3-1 3 1 2-1 1 .4 1.5.6V4zm-3.5 6.25v-1.5h-8v1.5h8zm0 3v-1.5h-8v1.5h8zm-8 3v-1.5h8v1.5h-8z",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_receipt = (receipt);
//# sourceMappingURL=receipt.js.map
// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__(17);

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__(7);

// CONCATENATED MODULE: ./resources/js/frontend/blocks/summary/editor.scss
// extracted by mini-css-extract-plugin

// CONCATENATED MODULE: ./resources/js/frontend/blocks/summary/edit.js

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
const Skeleton = _ref => {
  let {
    numberOfLines = 1
  } = _ref;
  const skeletonLines = Array.from({
    length: numberOfLines
  }, (_, index) => Object(external_wp_element_["createElement"])("span", {
    className: "wc-cp-block-components__skeleton__text-line",
    "aria-hidden": "true",
    key: index
  }));
  return Object(external_wp_element_["createElement"])("div", {
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
  const blockProps = Object(external_wp_blockEditor_["useBlockProps"])();
  return Object(external_wp_element_["createElement"])("div", blockProps, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["InspectorControls"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: Object(external_wp_i18n_["__"])('Settings')
  }, Object(external_wp_element_["createElement"])(external_wp_components_["SelectControl"], {
    label: Object(external_wp_i18n_["__"])('Display'),
    help: isFixed ? Object(external_wp_i18n_["__"])("Selection details will be rendered in a carousel. The carousel will be fixed at the bottom of your shoppers' browser window, and will remain hidden on mobile screens.", 'woocommerce-composite-products') : Object(external_wp_i18n_["__"])('Selection details will be summarized in a list. Recommended when adding the Summary in a Column ancestor block.', 'woocommerce-composite-products'),
    value: display,
    options: displayOptions.map(option => ({
      label: option.label,
      value: option.value
    })),
    onChange: newDisplay => setAttributes({
      display: newDisplay
    })
  }), !isFixed && Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
    label: Object(external_wp_i18n_["__"])('Title'),
    value: title,
    help: Object(external_wp_i18n_["__"])('Title to display before the summary.', 'woocommerce-composite-products'),
    onChange: newTitle => setAttributes({
      title: newTitle
    })
  }), isFixed && Object(external_wp_element_["createElement"])(external_wp_components_["Notice"], {
    className: "wc-block-components-composite-summary-fixed-notice",
    isDismissible: false,
    status: "info"
  }, Object(external_wp_i18n_["__"])('Note: The block position in the editor is not indicative of the position on the front end. The block will always be positioned at the bottom of the page.', 'woocommerce-composite-products')), isFixed && Object(external_wp_element_["createElement"])(external_wp_components_["RangeControl"], {
    label: Object(external_wp_i18n_["__"])('Columns'),
    help: "Maximum number of columns to display in the carousel.",
    value: maxColumns,
    step: 1,
    min: 2,
    max: 5,
    onChange: newColumns => setAttributes({
      maxColumns: parseInt(newColumns, 10)
    })
  }))), Object(external_wp_element_["createElement"])(external_wp_components_["Tooltip"], {
    text: isFixed ? Object(external_wp_i18n_["__"])('Current selections will be rendered in a carousel.', 'woocommerce-composite-products') : Object(external_wp_i18n_["__"])('Current selections will be summarized in a list.', 'woocommerce-composite-products'),
    position: "bottom right"
  }, Object(external_wp_element_["createElement"])("div", {
    className: className
  }, !isFixed && Object(external_wp_element_["createElement"])("h3", {
    className: "cp_summary__title"
  }, title), Object(external_wp_element_["createElement"])("div", {
    className: "cp_summary__options"
  }, isFixed && Object(external_wp_element_["createElement"])("span", {
    className: "cp_summary__options__arrow cp_summary__options__arrow--left wc-cp-block-components__skeleton__text-line",
    "aria-hidden": "true"
  }), Object(external_wp_element_["createElement"])(Skeleton, {
    numberOfLines: isFixed ? maxColumns : 3
  }), isFixed && Object(external_wp_element_["createElement"])("span", {
    className: "cp_summary__options__arrow cp_summary__options__arrow--right wc-cp-block-components__skeleton__text-line",
    "aria-hidden": "true"
  })), Object(external_wp_element_["createElement"])("div", {
    className: "cp_summary__add-to-cart"
  }, Object(external_wp_element_["createElement"])(Skeleton, {
    numberOfLines: 1
  }), Object(external_wp_element_["createElement"])(external_wp_components_["Disabled"], null, Object(external_wp_element_["createElement"])("div", {
    className: "cp_summary__add-to-cart__button"
  }, Object(external_wp_element_["createElement"])("input", {
    type: 'number',
    value: '1',
    className: 'wc-block-editor-add-to-cart-form__quantity',
    readOnly: true
  }), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: 'primary',
    className: 'wc-block-editor-add-to-cart-form__button'
  }, Object(external_wp_i18n_["__"])('Add to cart', 'woocommerce-composite-products'))))))));
}
// EXTERNAL MODULE: ./resources/js/frontend/blocks/summary/block.json
var block = __webpack_require__(18);

// CONCATENATED MODULE: ./resources/js/frontend/blocks/summary/index.js

/**
 * External dependencies
 */



/**
 * Internal dependencies
 */



/**
 * Register block.
 */
Object(external_wp_blocks_["registerBlockType"])(block.name, {
  ...block,
  icon: {
    src: Object(external_wp_element_["createElement"])(build_module_icon, {
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

/***/ }),

/***/ 7:
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ })

/******/ });