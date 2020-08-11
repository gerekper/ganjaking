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
/******/ 	return __webpack_require__(__webpack_require__.s = 488);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
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

/***/ 18:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
exports.default = {
	methods: {}
};

/***/ }),

/***/ 19:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mixin__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__mixin__);
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
    mixins: [__WEBPACK_IMPORTED_MODULE_0__mixin___default.a],

    data: function data() {
        return {
            loading: false,
            license: '',
            email: '',
            status: {},
            message: '',
            error: ''
        };
    },

    created: function created() {
        this.fetchLicense();
    },

    computed: {

        isActivated: function isActivated() {
            return this.status.activated !== undefined ? this.status.activated : false;
        }
    },

    methods: {

        fetchLicense: function fetchLicense() {
            var self = this;

            //self.loading = true;

            var request = {
                data: {},
                url: self.base_url + '/pm-pro/v2/license/check',
                success: function success(response) {
                    response = response.data;
                    self.license = response.license.key;
                    self.email = response.license.email;
                    self.status = response.status || {};
                    self.message = response.message;
                    pm.NProgress.done();
                },

                complete: function complete() {
                    self.loading = true;
                }
            };

            self.httpRequest(request);
        },

        deleteLicense: function deleteLicense(target, nonce) {
            var self = this;

            if (!confirm(__('License will delete permanently', 'pm-pro'))) {
                return;
            }

            self.loading = true;
            jQuery(target).addClass('updating-message');

            var request = {
                data: {},
                type: 'POST',
                url: self.base_url + '/pm-pro/v2/license/delete',
                success: function success() {
                    self.license = '';
                    self.email = '';
                    //self.status  = {};
                    //self.message = '';
                },

                error: function error(_error) {
                    alert(_error);
                },

                complete: function complete() {
                    //self.loading = false;
                    //jQuery(target).removeClass('updating-message');
                    location.reload();
                }
            };

            self.httpRequest(request);
        },

        activate: function activate(target) {
            if ('' === this.license || '' === this.email) {
                alert(__('Please provide your email and license key', 'pm-pro'));
                return;
            }

            var self = this;
            self.error = '';

            jQuery(target).addClass('updating-message');

            var request = {
                data: {
                    email: self.email,
                    key: self.license
                },
                type: 'post',
                url: self.base_url + '/pm-pro/v2/license/activation',
                success: function success(response) {
                    response = response.data;
                    if (response.data.activated) {
                        //self.status  = response.data;
                        //self.message = response.message;
                        location.reload();
                    } else {
                        self.status = response.data;
                        self.error = response.data.error;
                    }
                },

                error: function error(_error2) {
                    alert(_error2);
                },

                complete: function complete() {
                    jQuery(target).removeClass('updating-message');
                }
            };

            self.httpRequest(request);
        }
    }
});

/***/ }),

/***/ 44:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _mixin = __webpack_require__(18);

var _mixin2 = _interopRequireDefault(_mixin);

var _update = __webpack_require__(45);

var _update2 = _interopRequireDefault(_update);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevsPmProRegisterModule('update', 'update');

PmProMixin.update = _mixin2.default;

weDevsPMRegisterChildrenRoute('project_root', [{
    path: '/license',
    component: _update2.default,
    name: 'license'
}]);

/***/ }),

/***/ 45:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_update_vue__ = __webpack_require__(19);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_82b498a4_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_update_vue__ = __webpack_require__(46);
var disposed = false
var normalizeComponent = __webpack_require__(0)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_update_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_82b498a4_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_update_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "views/assets/src/components/update/update.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-82b498a4", Component.options)
  } else {
    hotAPI.reload("data-v-82b498a4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),

/***/ 46:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.loading
    ? _c("div", [
        _vm.error
          ? _c("div", { staticClass: "updated error" }, [
              _c("p", [_vm._v(_vm._s(_vm.error))])
            ])
          : _vm._e(),
        _vm._v(" "),
        _c("h2", [_vm._v(_vm._s(_vm.__("License Activation", "pm-pro")))]),
        _vm._v(" "),
        !_vm.isActivated
          ? _c("table", { staticClass: "form-table" }, [
              _c("tr", [
                _c("th", [_vm._v(_vm._s(_vm.__("E-mail Address", "pm-pro")))]),
                _vm._v(" "),
                _c("td", [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.email,
                        expression: "email"
                      }
                    ],
                    staticClass: "regular-text",
                    attrs: { type: "email", name: "email", required: "" },
                    domProps: { value: _vm.email },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.email = $event.target.value
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("p", { staticClass: "description" }, [
                    _vm._v(
                      _vm._s(
                        _vm.__("Enter your purchase Email address", "pm-pro")
                      )
                    )
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("tr", [
                _c("th", [_vm._v(_vm._s(_vm.__("License Key", "pm-pro")))]),
                _vm._v(" "),
                _c("td", [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.license,
                        expression: "license"
                      }
                    ],
                    staticClass: "regular-text",
                    attrs: { type: "text", name: "license_key" },
                    domProps: { value: _vm.license },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.license = $event.target.value
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("p", { staticClass: "description" }, [
                    _vm._v(_vm._s(_vm.__("Enter your license key", "pm-pro")))
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("tr", [
                _c("th", [_vm._v(" ")]),
                _vm._v(" "),
                _c("td", [
                  _c(
                    "button",
                    {
                      staticClass: "button button-primary",
                      on: {
                        click: function($event) {
                          return _vm.activate($event.target)
                        }
                      }
                    },
                    [_vm._v(_vm._s(_vm.__("Save & Activate", "pm-pro")))]
                  )
                ])
              ])
            ])
          : _c("div", { staticClass: "weforms-license activated" }, [
              _c("div", { staticClass: "updated" }, [
                _c("p", [_vm._v(_vm._s(_vm.message))])
              ]),
              _vm._v(" "),
              _c(
                "button",
                {
                  staticClass: "button",
                  on: {
                    click: function($event) {
                      return _vm.deleteLicense($event.target)
                    }
                  }
                },
                [_vm._v(_vm._s(_vm.__("Deactive License", "pm-pro")))]
              )
            ])
      ])
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-82b498a4", esExports)
  }
}

/***/ }),

/***/ 488:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(44);

/***/ })

/******/ });