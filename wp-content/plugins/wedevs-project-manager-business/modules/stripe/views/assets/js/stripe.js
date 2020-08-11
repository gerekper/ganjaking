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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["a"] = ({
	props: {
		actionData: {
			type: Object,
			default: function _default() {

				return {
					stripe_instruction: 'Pay with your credit card'
				};
			}
		}
	},
	data: function data() {
		return {
			stripe_instruction: this.getSettings('stripe_instruction', 'Pay with your credit card', 'invoice'),
			stripe_test_secret: this.getSettings('stripe_test_secret', false, 'invoice'),
			secret_key: this.getSettings('secret_key', '', 'invoice'),
			secret_publishable_key: this.getSettings('secret_publishable_key', '', 'invoice'),
			live_secret_key: this.getSettings('live_secret_key', '', 'invoice'),
			live_publishable_key: this.getSettings('live_publishable_key', '', 'invoice')
		};
	},
	created: function created() {
		pm_add_filter('pm_invoice_settings', this.invoiceSettings);
	},

	methods: {
		invoiceSettings: function invoiceSettings(invoice) {
			invoice.stripe_instruction = this.stripe_instruction;
			invoice.stripe_test_secret = this.stripe_test_secret;
			invoice.secret_key = this.secret_key;
			invoice.secret_publishable_key = this.secret_publishable_key;
			invoice.live_secret_key = this.live_secret_key;
			invoice.stripe_instruction = this.stripe_instruction;
			invoice.live_publishable_key = this.live_publishable_key;
			return invoice;
		}
	}

});

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/invoice/views/assets/js/';

__webpack_require__(2);

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(3);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _settings = __webpack_require__(4);

var _settings2 = _interopRequireDefault(_settings);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevsPmProAddonRegisterModule('stripe', 'stripe');

pm_add_filter('pm_invoice_gateways', function (gateways) {
  gateways.push({
    'name': 'stripe',
    'label': 'Stripe',
    'active': false
  });
  return gateways;
}, 1);

weDevs_PM_Components.push({
  hook: 'pm_invoice_settings',
  component: 'pm_stripe_settings',
  property: _settings2.default
});

/***/ }),
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_settings_vue__ = __webpack_require__(0);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5dc4a13c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_settings_vue__ = __webpack_require__(6);
var disposed = false
var normalizeComponent = __webpack_require__(5)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_settings_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5dc4a13c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_settings_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/stripe/views/assets/src/components/settings.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5dc4a13c", Component.options)
  } else {
    hotAPI.reload("data-v-5dc4a13c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 5 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("table", { staticClass: "form-table" }, [
    _c("tbody", [
      _c("tr", [
        _c("th", { attrs: { scope: "row" } }, [
          _c("label", { attrs: { for: "stripe_instruction" } }, [
            _vm._v(_vm._s(_vm.__("Stripe Instruction", "pm-pro")))
          ])
        ]),
        _vm._v(" "),
        _c("td", [
          _c("textarea", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.stripe_instruction,
                expression: "stripe_instruction"
              }
            ],
            staticClass: "regular-text",
            attrs: { rows: "5", cols: "55", id: "stripe_instruction" },
            domProps: { value: _vm.stripe_instruction },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.stripe_instruction = $event.target.value
              }
            }
          })
        ])
      ]),
      _vm._v(" "),
      _c("tr", [
        _c("th", { attrs: { scope: "row" } }, [
          _c("label", { attrs: { for: "stripe_test_secret" } }, [
            _vm._v(_vm._s(_vm.__("Enable stripe test secret Key", "pm-pro")))
          ])
        ]),
        _vm._v(" "),
        _c("td", [
          _c("fieldset", [
            _c("label", { attrs: { for: "stripe_test_secret" } }, [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.stripe_test_secret,
                    expression: "stripe_test_secret"
                  }
                ],
                staticClass: "checkbox",
                attrs: { type: "checkbox", id: "stripe_test_secret" },
                domProps: {
                  checked: Array.isArray(_vm.stripe_test_secret)
                    ? _vm._i(_vm.stripe_test_secret, null) > -1
                    : _vm.stripe_test_secret
                },
                on: {
                  change: function($event) {
                    var $$a = _vm.stripe_test_secret,
                      $$el = $event.target,
                      $$c = $$el.checked ? true : false
                    if (Array.isArray($$a)) {
                      var $$v = null,
                        $$i = _vm._i($$a, $$v)
                      if ($$el.checked) {
                        $$i < 0 && (_vm.stripe_test_secret = $$a.concat([$$v]))
                      } else {
                        $$i > -1 &&
                          (_vm.stripe_test_secret = $$a
                            .slice(0, $$i)
                            .concat($$a.slice($$i + 1)))
                      }
                    } else {
                      _vm.stripe_test_secret = $$c
                    }
                  }
                }
              }),
              _vm._v(
                " " +
                  _vm._s(
                    _vm.__(
                      "When sandbox mode is active, all payment gateway will be used in demo mode",
                      "pm-pro"
                    )
                  ) +
                  "\n\t\t\t\t\t"
              )
            ])
          ])
        ])
      ]),
      _vm._v(" "),
      _c("tr", [
        _c("th", { attrs: { scope: "row" } }, [
          _c("label", { attrs: { for: "secret_key" } }, [
            _vm._v(_vm._s(_vm.__("Test Secret Key", "pm-pro")))
          ])
        ]),
        _vm._v(" "),
        _c("td", [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.secret_key,
                expression: "secret_key"
              }
            ],
            staticClass: "regular-text",
            attrs: { type: "text", id: "secret_key" },
            domProps: { value: _vm.secret_key },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.secret_key = $event.target.value
              }
            }
          })
        ])
      ]),
      _vm._v(" "),
      _c("tr", [
        _vm._m(0),
        _vm._v(" "),
        _c("td", [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.secret_publishable_key,
                expression: "secret_publishable_key"
              }
            ],
            staticClass: "regular-text",
            attrs: { type: "text", id: "secret_publishable_key" },
            domProps: { value: _vm.secret_publishable_key },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.secret_publishable_key = $event.target.value
              }
            }
          })
        ])
      ]),
      _vm._v(" "),
      _c("tr", [
        _c("th", { attrs: { scope: "row" } }, [
          _c("label", { attrs: { for: "live_secret_key" } }, [
            _vm._v(_vm._s(_vm.__("Live Secret Key", "pm-pro")))
          ])
        ]),
        _vm._v(" "),
        _c("td", [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.live_secret_key,
                expression: "live_secret_key"
              }
            ],
            staticClass: "regular-text",
            attrs: { type: "text", id: "live_secret_key", value: "" },
            domProps: { value: _vm.live_secret_key },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.live_secret_key = $event.target.value
              }
            }
          })
        ])
      ]),
      _vm._v(" "),
      _c("tr", [
        _c("th", { attrs: { scope: "row" } }, [
          _c("label", { attrs: { for: "live_publishable_key" } }, [
            _vm._v(_vm._s(_vm.__("Live Publishable Key", "pm-pro")))
          ])
        ]),
        _vm._v(" "),
        _c("td", [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.live_publishable_key,
                expression: "live_publishable_key"
              }
            ],
            staticClass: "regular-text",
            attrs: { type: "text", id: "live_publishable_key", value: "" },
            domProps: { value: _vm.live_publishable_key },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.live_publishable_key = $event.target.value
              }
            }
          })
        ])
      ])
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("th", { attrs: { scope: "row" } }, [
      _c("label", { attrs: { for: "secret_publishable_key" } }, [
        _vm._v("Test Publishable Key")
      ])
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-5dc4a13c", esExports)
  }
}

/***/ })
/******/ ]);