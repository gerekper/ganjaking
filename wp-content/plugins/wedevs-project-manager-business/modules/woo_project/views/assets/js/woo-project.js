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
/******/ 	return __webpack_require__(__webpack_require__.s = 16);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
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
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(10)(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 2 */
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),
/* 3 */
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.11' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),
/* 4 */
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(42);
var defined = __webpack_require__(44);
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__action_vue__ = __webpack_require__(19);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__woo_option_vue__ = __webpack_require__(26);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__user_search_vue__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__helpers_mixin_mixin__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3__helpers_mixin_mixin__);
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
    beforeRouteEnter: function beforeRouteEnter(to, from, next) {
        if (!pmHasManageCapability()) {
            next(false);
        } else {
            next(function (vm) {
                vm.getRoles();
                vm.getProducts();
                vm.getProjects();
                vm.getSelectedUser();
            });
        }
    },


    mixins: [__WEBPACK_IMPORTED_MODULE_3__helpers_mixin_mixin___default.a, PmMixin.settings],
    data: function data() {
        return {
            options: this.getSettings('woo_project', [{
                action: 'create',
                product_ids: '',
                project_id: '',
                assignees: []

            }]),
            projectLoaded: false,
            productLoaded: false,
            userLoaded: false
        };
    },

    components: {
        action: __WEBPACK_IMPORTED_MODULE_0__action_vue__["a" /* default */],
        wooOption: __WEBPACK_IMPORTED_MODULE_1__woo_option_vue__["a" /* default */],
        user: __WEBPACK_IMPORTED_MODULE_2__user_search_vue__["a" /* default */]
    },
    computed: {
        selectedUsers: function selectedUsers() {
            return this.$root.$store.state.assignees;
        },
        loaded: function loaded() {
            return this.projectLoaded && this.productLoaded && this.userLoaded;
        }
    },
    mounted: function mounted() {
        if (this.loaded) {
            pm.NProgress.done();
        }
    },

    methods: {
        addOption: function addOption() {
            this.options.push({
                action: 'create',
                product_ids: '',
                project_id: '',
                assignees: []

            });
        },
        removeOption: function removeOption(index) {
            this.options.splice(index, 1);
        },
        saveOption: function saveOption() {
            var data = {
                woo_project: this.options
            };
            this.saveSettings(data);
        }
    }
});

/***/ }),
/* 7 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
	props: {
		value: String
	},
	data: function data() {
		return {
			action: this.value
		};
	},

	watch: {
		value: function value(newVal) {
			this.action = newVal;
		}
	},
	methods: {
		onChange: function onChange() {
			this.$emit('input', this.action);
		}
	}
});

/***/ }),
/* 8 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_values__ = __webpack_require__(9);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_values___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_values__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__user_search_vue__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin__);

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
    mixins: [__WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin___default.a],
    props: {
        option: {
            required: true,
            default: function _default() {
                return {
                    action: 'create',
                    project_id: {},
                    product_ids: [],
                    assignees: []
                };
            }
        },
        index: Number
    },
    data: function data() {
        return {
            project: this.getProject(),
            product: this.getProduct(),
            wcloading: false,
            pmloading: false,
            timer: false,
            search_product: __('Search Product', 'pm-pro'),
            search_project: __('Search Project', 'pm-pro')
        };
    },

    watch: {
        'option.product_ids': function optionProduct_ids() {
            this.product = this.getProduct();
        },
        'option.project_id': function optionProject_id() {
            this.project = this.getProject();
        }
    },
    components: {
        multiselect: pm.Multiselect.Multiselect
    },
    computed: {
        products: function products() {
            return this.$store.state.wooProject.products;
        },
        projects: function projects() {
            return this.$store.state.wooProject.projects;
        }
    },
    methods: {
        getProduct: function getProduct() {
            if (typeof this.option.product_ids != 'undefined') {
                if (this.option.product_ids == '') {
                    this.option.product_ids = {};
                }
            }
            if (typeof this.option.product_ids == 'undefined') {
                this.option.product_ids = {};
            }

            var ids = __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_values___default()(this.option.product_ids);

            return this.$store.state.wooProject.products.filter(function (product) {
                return ids.indexOf(product.id) !== -1 || ids.indexOf(product.id.toString()) !== -1;
            });
        },
        getProject: function getProject() {
            var id = parseInt(this.option.project_id);
            var index = this.$store.state.wooProject.projects.findIndex(function (p) {
                return p.id == id;
            });
            if (index !== -1) {
                return this.$store.state.wooProject.projects[index];
            } else {
                return {};
            }
        },
        setProductIds: function setProductIds(value) {
            var product_ids = value.map(function (value) {
                return value.id;
            });
            this.option.product_ids = product_ids;
        },
        setProjectId: function setProjectId(value) {

            this.option.project_id = value.id;
        },
        searchProducts: function searchProducts(s, index) {

            var self = this,
                delay = 500,
                args = {
                conditions: {
                    s: s
                },
                callback: function callback(res) {
                    this.wcloading = false;
                }
            };

            if (s.length > 1) {

                this.wcloading = true;

                if (this.timer) {
                    clearTimeout(this.timer);
                }

                this.timer = setTimeout(function () {

                    self.getWoocommerceProducts(args);
                }, delay);
            }
        },
        searchProject: function searchProject(s, index) {
            var self = this,
                delay = 500,
                args = {
                conditions: {
                    s: s
                },
                callback: function callback(res) {
                    this.pmloading = false;
                }
            };

            if (s.length > 1) {

                this.pmloading = true;

                if (this.timer) {
                    clearTimeout(this.timer);
                }

                this.timer = setTimeout(function () {

                    self.searchProjectByKey(args);
                }, delay);
            }
        }
    }
});

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(27), __esModule: true };

/***/ }),
/* 10 */
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),
/* 11 */
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),
/* 12 */
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),
/* 13 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_search_vue__ = __webpack_require__(14);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_8794203a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_search_vue__ = __webpack_require__(55);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_search_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_8794203a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_search_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/woo_project/views/assets/src/components/user-search.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-8794203a", Component.options)
  } else {
    hotAPI.reload("data-v-8794203a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 14 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__directives_directive__ = __webpack_require__(54);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__directives_directive___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__directives_directive__);
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
        option: {
            required: true,
            default: function _default() {
                return {
                    action: 'create',
                    project_id: {},
                    product_ids: [],
                    assignees: []
                };
            }
        },
        index: Number
    },
    data: function data() {
        return {
            assign: {},
            project_user_text: __('Select User to project', 'pm-pro'),
            text_delete: __('Delete', 'pm-pro')
        };
    },

    computed: {
        roles: function roles() {
            return this.$root.$store.state.roles;
        },

        users: {
            get: function get() {
                var self = this;
                var users = [];
                if (this.option.assignees) {
                    this.option.assignees.forEach(function (item) {
                        var index = self.$store.state.wooProject.users.findIndex(function (user) {
                            return user.id == parseInt(item.user_id);
                        });

                        if (index !== -1) {
                            var user = self.$store.state.wooProject.users[index];
                            user.role_id = parseInt(item.role_id);
                            users.push(user);
                        }
                    });
                }
                return users;
            },
            set: function set(value) {
                var value = [];
                var self = this;

                value.map(function (user) {
                    if (self.option.assignees.findIndex(function (u) {
                        return u.user_id == user.id && u.role_id == user.role_id;
                    }) == -1) {
                        v.push({ user_id: user.id, role_id: user.role_id });
                    }
                });

                if (value.length) {
                    self.option.assignees = self.option.assignees.concat(v);
                }
            }
        }
    },
    methods: {
        changeRole: function changeRole(user, value) {
            var i = this.option.assignees.findIndex(function (u) {
                return parseInt(u.user_id) == user.id;
            });
            this.option.assignees[i].role_id = value;
            this.$forceUpdate();
        },
        removeUser: function removeUser(user) {
            var i = this.option.assignees.findIndex(function (u) {
                return u.user_id == user.id;
            });
            this.option.assignees.splice(i, 1);
            this.$forceUpdate();
        }
    }
});

/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

var _values = __webpack_require__(9);

var _values2 = _interopRequireDefault(_values);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
    data: function data() {
        return {};
    },

    methods: {
        getWoocommerceProducts: function getWoocommerceProducts(args) {
            var self = this,
                pre_define = {
                conditions: {},
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var conditions = self.generateConditions(args.conditions);

            var request = {
                type: 'GET',
                data: args.data,
                url: self.base_url + '/pm-pro/v2/woo-project/products?' + conditions,
                success: function success(res) {
                    self.$store.commit('wooProject/addProducts', res.data);
                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                    pm.NProgress.done();
                }
            };

            return this.httpRequest(request);
        },
        searchProjectByKey: function searchProjectByKey(args) {
            var self = this,
                pre_define = {
                conditions: {},
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var conditions = self.generateConditions(args.conditions);

            var request = {
                type: 'GET',
                data: args.data,
                url: self.base_url + '/pm-pro/v2/woo-project/project?' + conditions,
                success: function success(res) {
                    self.$store.commit('wooProject/addProject', res.data);
                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                    pm.NProgress.done();
                }
            };

            return this.httpRequest(request);
        },
        getProducts: function getProducts() {
            var ids = [];
            this.options.forEach(function (option) {

                if (typeof option.product_ids == 'undefined') {
                    option.product_ids = {};
                }
                if (option.product_ids == '') {
                    option.product_ids = {};
                }
                ids = ids.concat((0, _values2.default)(option.product_ids));
            });

            if (!ids.length) {
                this.productLoaded = true;
                return;
            }

            var args = {
                data: {
                    id: ids
                },
                callback: function callback(res) {
                    this.productLoaded = true;
                }
            };
            this.getWoocommerceProducts(args);
        },
        getProjects: function getProjects() {
            var ids = [];
            this.options.forEach(function (option) {
                if (option.project_id) {
                    ids.push(parseInt(option.project_id));
                }
            });
            if (!ids.length) {
                this.projectLoaded = true;
                return;
            }

            var args = {
                data: {
                    id: ids
                },
                callback: function callback(res) {
                    this.projectLoaded = true;
                }
            };

            this.searchProjectByKey(args);
        },
        getSelectedUser: function getSelectedUser() {
            var self = this;
            var user_id = [];
            this.options.forEach(function (option) {
                if (option.assignees) {
                    option.assignees.forEach(function (item) {
                        if (item.user_id) {
                            user_id.push(item.user_id);
                        }
                    });
                }
            });
            var args = {
                data: {
                    id: user_id
                },
                callback: function callback(res) {
                    this.$store.commit('wooProject/addUser', res.data);
                    this.userLoaded = true;
                }
            };
            self.getUsers(args);
        }
    }
};

/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(17);

var _router2 = _interopRequireDefault(_router);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/woo_project/views/assets/js/';

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _wooProject = __webpack_require__(18);

var _wooProject2 = _interopRequireDefault(_wooProject);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevsPmProAddonRegisterModule('wooProject', 'woo_project');

// const woo_project = resolve => {
//     require.ensure(['./../components/woo-project.vue'], () => {
//         resolve(require('./../components/woo-project.vue'));
//     });
// }

weDevsPMRegisterChildrenRoute('project_root', [{
    path: 'woo-project',
    component: _wooProject2.default,
    name: 'woo_project'
}]);

/***/ }),
/* 18 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_woo_project_vue__ = __webpack_require__(6);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_265ab548_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_woo_project_vue__ = __webpack_require__(57);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_woo_project_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_265ab548_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_woo_project_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/woo_project/views/assets/src/components/woo-project.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-265ab548", Component.options)
  } else {
    hotAPI.reload("data-v-265ab548", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 19 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_action_vue__ = __webpack_require__(7);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_584884cd_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_action_vue__ = __webpack_require__(25);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(20)
}
var normalizeComponent = __webpack_require__(0)
/* script */


/* template */

/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_action_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_584884cd_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_action_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/woo_project/views/assets/src/components/action.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-584884cd", Component.options)
  } else {
    hotAPI.reload("data-v-584884cd", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(21);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(23)("29174622", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-584884cd\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./action.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-584884cd\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./action.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 21 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(22)(false);
// imports


// module
exports.push([module.i, "\n.pm-wp-action label {\n    text-align: center;\n    vertical-align: bottom;\n}\n.pm-wp-action input {\n    margin-right: 5px !important;\n}\n", ""]);

// exports


/***/ }),
/* 22 */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(24)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}
var options = null
var ssrIdKey = 'data-vue-ssr-id'

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction, _options) {
  isProduction = _isProduction

  options = _options || {}

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[' + ssrIdKey + '~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }
  if (options.ssrId) {
    styleElement.setAttribute(ssrIdKey, obj.id)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),
/* 24 */
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),
/* 25 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "pm-wp-action pm-col-2" }, [
    _c("strong", [_vm._v(" " + _vm._s(_vm.__("Actions", "pm-pro")) + "  ")]),
    _vm._v(" "),
    _c("label", [
      _c("input", {
        directives: [
          {
            name: "model",
            rawName: "v-model",
            value: _vm.action,
            expression: "action"
          }
        ],
        attrs: { type: "radio", value: "create" },
        domProps: { checked: _vm._q(_vm.action, "create") },
        on: {
          change: [
            function($event) {
              _vm.action = "create"
            },
            _vm.onChange
          ]
        }
      }),
      _vm._v(" " + _vm._s(_vm.__("Create", "pm-pro")) + "\n    ")
    ]),
    _vm._v(" "),
    _c("label", [
      _c("input", {
        directives: [
          {
            name: "model",
            rawName: "v-model",
            value: _vm.action,
            expression: "action"
          }
        ],
        attrs: { type: "radio", value: "duplicate" },
        domProps: { checked: _vm._q(_vm.action, "duplicate") },
        on: {
          change: [
            function($event) {
              _vm.action = "duplicate"
            },
            _vm.onChange
          ]
        }
      }),
      _vm._v(_vm._s(_vm.__("Duplicate", "pm-pro")) + "\n    ")
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-584884cd", esExports)
  }
}

/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_woo_option_vue__ = __webpack_require__(8);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_b4d929fc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_woo_option_vue__ = __webpack_require__(56);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_woo_option_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_b4d929fc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_woo_option_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/woo_project/views/assets/src/components/woo-option.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-b4d929fc", Component.options)
  } else {
    hotAPI.reload("data-v-b4d929fc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 27 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(28);
module.exports = __webpack_require__(3).Object.values;


/***/ }),
/* 28 */
/***/ (function(module, exports, __webpack_require__) {

// https://github.com/tc39/proposal-object-values-entries
var $export = __webpack_require__(29);
var $values = __webpack_require__(39)(false);

$export($export.S, 'Object', {
  values: function values(it) {
    return $values(it);
  }
});


/***/ }),
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(2);
var core = __webpack_require__(3);
var ctx = __webpack_require__(30);
var hide = __webpack_require__(32);
var has = __webpack_require__(11);
var PROTOTYPE = 'prototype';

var $export = function (type, name, source) {
  var IS_FORCED = type & $export.F;
  var IS_GLOBAL = type & $export.G;
  var IS_STATIC = type & $export.S;
  var IS_PROTO = type & $export.P;
  var IS_BIND = type & $export.B;
  var IS_WRAP = type & $export.W;
  var exports = IS_GLOBAL ? core : core[name] || (core[name] = {});
  var expProto = exports[PROTOTYPE];
  var target = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE];
  var key, own, out;
  if (IS_GLOBAL) source = name;
  for (key in source) {
    // contains in native
    own = !IS_FORCED && target && target[key] !== undefined;
    if (own && has(exports, key)) continue;
    // export native or passed
    out = own ? target[key] : source[key];
    // prevent global pollution for namespaces
    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
    // bind timers to global for call from export context
    : IS_BIND && own ? ctx(out, global)
    // wrap global constructors for prevent change them in library
    : IS_WRAP && target[key] == out ? (function (C) {
      var F = function (a, b, c) {
        if (this instanceof C) {
          switch (arguments.length) {
            case 0: return new C();
            case 1: return new C(a);
            case 2: return new C(a, b);
          } return new C(a, b, c);
        } return C.apply(this, arguments);
      };
      F[PROTOTYPE] = C[PROTOTYPE];
      return F;
    // make static versions for prototype methods
    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
    if (IS_PROTO) {
      (exports.virtual || (exports.virtual = {}))[key] = out;
      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
      if (type & $export.R && expProto && !expProto[key]) hide(expProto, key, out);
    }
  }
};
// type bitmap
$export.F = 1;   // forced
$export.G = 2;   // global
$export.S = 4;   // static
$export.P = 8;   // proto
$export.B = 16;  // bind
$export.W = 32;  // wrap
$export.U = 64;  // safe
$export.R = 128; // real proto method for `library`
module.exports = $export;


/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(31);
module.exports = function (fn, that, length) {
  aFunction(fn);
  if (that === undefined) return fn;
  switch (length) {
    case 1: return function (a) {
      return fn.call(that, a);
    };
    case 2: return function (a, b) {
      return fn.call(that, a, b);
    };
    case 3: return function (a, b, c) {
      return fn.call(that, a, b, c);
    };
  }
  return function (/* ...args */) {
    return fn.apply(that, arguments);
  };
};


/***/ }),
/* 31 */
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__(33);
var createDesc = __webpack_require__(38);
module.exports = __webpack_require__(1) ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(34);
var IE8_DOM_DEFINE = __webpack_require__(35);
var toPrimitive = __webpack_require__(37);
var dP = Object.defineProperty;

exports.f = __webpack_require__(1) ? Object.defineProperty : function defineProperty(O, P, Attributes) {
  anObject(O);
  P = toPrimitive(P, true);
  anObject(Attributes);
  if (IE8_DOM_DEFINE) try {
    return dP(O, P, Attributes);
  } catch (e) { /* empty */ }
  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported!');
  if ('value' in Attributes) O[P] = Attributes.value;
  return O;
};


/***/ }),
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),
/* 35 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(1) && !__webpack_require__(10)(function () {
  return Object.defineProperty(__webpack_require__(36)('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(4);
var document = __webpack_require__(2).document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),
/* 37 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(4);
// instead of the ES6 spec version, we didn't implement @@toPrimitive case
// and the second argument - flag - preferred type is a string
module.exports = function (it, S) {
  if (!isObject(it)) return it;
  var fn, val;
  if (S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  if (typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it))) return val;
  if (!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it))) return val;
  throw TypeError("Can't convert object to primitive value");
};


/***/ }),
/* 38 */
/***/ (function(module, exports) {

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),
/* 39 */
/***/ (function(module, exports, __webpack_require__) {

var DESCRIPTORS = __webpack_require__(1);
var getKeys = __webpack_require__(40);
var toIObject = __webpack_require__(5);
var isEnum = __webpack_require__(53).f;
module.exports = function (isEntries) {
  return function (it) {
    var O = toIObject(it);
    var keys = getKeys(O);
    var length = keys.length;
    var i = 0;
    var result = [];
    var key;
    while (length > i) {
      key = keys[i++];
      if (!DESCRIPTORS || isEnum.call(O, key)) {
        result.push(isEntries ? [key, O[key]] : O[key]);
      }
    }
    return result;
  };
};


/***/ }),
/* 40 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __webpack_require__(41);
var enumBugKeys = __webpack_require__(52);

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),
/* 41 */
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__(11);
var toIObject = __webpack_require__(5);
var arrayIndexOf = __webpack_require__(45)(false);
var IE_PROTO = __webpack_require__(48)('IE_PROTO');

module.exports = function (object, names) {
  var O = toIObject(object);
  var i = 0;
  var result = [];
  var key;
  for (key in O) if (key != IE_PROTO) has(O, key) && result.push(key);
  // Don't enum bug & hidden keys
  while (names.length > i) if (has(O, key = names[i++])) {
    ~arrayIndexOf(result, key) || result.push(key);
  }
  return result;
};


/***/ }),
/* 42 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(43);
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),
/* 43 */
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),
/* 44 */
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(5);
var toLength = __webpack_require__(46);
var toAbsoluteIndex = __webpack_require__(47);
module.exports = function (IS_INCLUDES) {
  return function ($this, el, fromIndex) {
    var O = toIObject($this);
    var length = toLength(O.length);
    var index = toAbsoluteIndex(fromIndex, length);
    var value;
    // Array#includes uses SameValueZero equality algorithm
    // eslint-disable-next-line no-self-compare
    if (IS_INCLUDES && el != el) while (length > index) {
      value = O[index++];
      // eslint-disable-next-line no-self-compare
      if (value != value) return true;
    // Array#indexOf ignores holes, Array#includes - not
    } else for (;length > index; index++) if (IS_INCLUDES || index in O) {
      if (O[index] === el) return IS_INCLUDES || index || 0;
    } return !IS_INCLUDES && -1;
  };
};


/***/ }),
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(12);
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(12);
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(49)('keys');
var uid = __webpack_require__(51);
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__(3);
var global = __webpack_require__(2);
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__(50) ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),
/* 50 */
/***/ (function(module, exports) {

module.exports = true;


/***/ }),
/* 51 */
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),
/* 52 */
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),
/* 53 */
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),
/* 54 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var Project = {
    coWorkerSearch: function coWorkerSearch(el, binding, vnode) {

        var $ = jQuery;
        var pm_abort;
        var context = vnode.context;
        var element = jQuery(el);

        $(element).autocomplete({
            minLength: 1,

            source: function source(request, response) {
                var args = {
                    conditions: {
                        query: request.term
                    },
                    callback: function callback(res) {
                        if (res.data.length) {
                            response(res.data);
                        } else {
                            response({
                                value: '0'
                            });
                        }
                    }
                };

                if (pm_abort) {
                    pm_abort.abort();
                }
                pm_abort = context.get_search_user(args);
            },

            search: function search() {
                $(this).addClass('pm-spinner');
            },

            open: function open() {
                var self = $(this);
                self.autocomplete('widget').css('z-index', 999999);
                self.removeClass('pm-spinner');
                return false;
            },

            select: function select(event, ui) {
                element.val('');
                context.$store.commit('wooProject/addUser', [ui.item]);
                if (typeof context.option.assignees == 'undefined') {
                    context.option.assignees = [];
                }

                if (typeof context.option.assignees == '') {
                    context.option.assignees = [];
                }
                context.option.assignees.push({ user_id: ui.item.id, role_id: 2 });
                return false;
            }

        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            var no_user = context.text.no_user_found;
            if (item.email) {
                return $("<li>").append('<a>' + item.display_name + '</a>').appendTo(ul);
            } else {
                return $("<li>").append('<a><div class="no-user-wrap"><p>' + no_user + '</p></div></a>').appendTo(ul);
            }
        };
    }

    // Register a global custom directive called v-pm-popup-box
};pm.Vue.directive('pm-wcp-users', {
    inserted: function inserted(el, binding, vnode) {
        Project.coWorkerSearch(el, binding, vnode);
    }
});

/***/ }),
/* 55 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.option.action == "create"
    ? _c("div", { staticClass: "pm-wp-assignees pm-col-3" }, [
        _c("strong", [_vm._v(_vm._s(_vm.__("Assignees Users", "pm-pro")))]),
        _vm._v(" "),
        _c("div", { staticClass: "assains-user" }, [
          _vm.users.length
            ? _c("div", { staticClass: "pm-form-item pm-project-role" }, [
                _c(
                  "table",
                  _vm._l(_vm.users, function(user, index) {
                    return _c("tr", { key: "user.id" }, [
                      _c("td", [_vm._v(_vm._s(user.display_name))]),
                      _vm._v(" "),
                      _c("td", [
                        _c(
                          "select",
                          {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: user.role_id,
                                expression: "user.role_id"
                              }
                            ],
                            domProps: { value: 2 },
                            on: {
                              change: [
                                function($event) {
                                  var $$selectedVal = Array.prototype.filter
                                    .call($event.target.options, function(o) {
                                      return o.selected
                                    })
                                    .map(function(o) {
                                      var val =
                                        "_value" in o ? o._value : o.value
                                      return val
                                    })
                                  _vm.$set(
                                    user,
                                    "role_id",
                                    $event.target.multiple
                                      ? $$selectedVal
                                      : $$selectedVal[0]
                                  )
                                },
                                function($event) {
                                  return _vm.changeRole(
                                    user,
                                    $event.target.value
                                  )
                                }
                              ]
                            }
                          },
                          _vm._l(_vm.roles, function(role) {
                            return _c(
                              "option",
                              { domProps: { value: parseInt(role.id) } },
                              [_vm._v(_vm._s(role.title))]
                            )
                          }),
                          0
                        )
                      ]),
                      _vm._v(" "),
                      _c("td", [
                        _c(
                          "a",
                          {
                            staticClass: "pm-del-proj-role pm-assign-del-user",
                            attrs: { hraf: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.removeUser(user)
                              }
                            }
                          },
                          [
                            _c("span", {
                              staticClass:
                                "dashicons dashicons-trash remove-user",
                              attrs: { title: _vm.text_delete }
                            })
                          ]
                        )
                      ])
                    ])
                  }),
                  0
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("div", { staticClass: "pm-form-item project-users" }, [
            _c("input", {
              directives: [{ name: "pm-wcp-users", rawName: "v-pm-wcp-users" }],
              staticClass: "pm-project-coworker",
              staticStyle: { width: "100%" },
              attrs: {
                type: "text",
                name: "user",
                placeholder: _vm.project_user_text
              }
            })
          ])
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
    require("vue-hot-reload-api")      .rerender("data-v-8794203a", esExports)
  }
}

/***/ }),
/* 56 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "wcp-option-inner" }, [
    _c(
      "div",
      { staticClass: "pm-wp-product pm-col-3" },
      [
        _c("strong", [_vm._v(" " + _vm._s(_vm.__("Product", "pm-pro")) + " ")]),
        _vm._v(" "),
        _c(
          "multiselect",
          {
            attrs: {
              options: _vm.products,
              multiple: true,
              "close-on-select": false,
              "clear-on-select": true,
              "hide-selected": true,
              "show-labels": false,
              searchable: true,
              loading: _vm.wcloading,
              placeholder: _vm.search_product,
              label: "title",
              "track-by": "id"
            },
            on: {
              "search-change": _vm.searchProducts,
              input: _vm.setProductIds
            },
            model: {
              value: _vm.product,
              callback: function($$v) {
                _vm.product = $$v
              },
              expression: "product"
            }
          },
          [
            _c("span", { attrs: { slot: "noResult" }, slot: "noResult" }, [
              _vm._v(
                "\n                " +
                  _vm._s(
                    _vm.__(
                      "Oops! No product found. Consider changing the search query.",
                      "pm-pro"
                    )
                  ) +
                  "\n            "
              )
            ])
          ]
        )
      ],
      1
    ),
    _vm._v(" "),
    _vm.option.action == "duplicate"
      ? _c(
          "div",
          { staticClass: "pm-wp-project pm-col-3" },
          [
            _c("strong", [_vm._v(_vm._s(_vm.__("Project", "pm-pro")) + " ")]),
            _vm._v(" "),
            _c(
              "multiselect",
              {
                attrs: {
                  options: _vm.projects,
                  multiple: false,
                  "close-on-select": true,
                  "clear-on-select": true,
                  "hide-selected": true,
                  "show-labels": false,
                  searchable: true,
                  loading: _vm.pmloading,
                  placeholder: _vm.search_project,
                  label: "title",
                  "track-by": "id"
                },
                on: {
                  "search-change": _vm.searchProject,
                  input: _vm.setProjectId
                },
                model: {
                  value: _vm.project,
                  callback: function($$v) {
                    _vm.project = $$v
                  },
                  expression: "project"
                }
              },
              [
                _c("span", { attrs: { slot: "noResult" }, slot: "noResult" }, [
                  _vm._v(
                    "\n                " +
                      _vm._s(
                        _vm.__(
                          "Oops! No project found. Consider changing the search query.",
                          "pm-pro"
                        )
                      ) +
                      "\n            "
                  )
                ])
              ]
            )
          ],
          1
        )
      : _vm._e()
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-b4d929fc", esExports)
  }
}

/***/ }),
/* 57 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "pm-page woo-project pm-module" }, [
    !_vm.loaded
      ? _c("div", { staticClass: "pm-data-load-before" }, [_vm._m(0)])
      : _vm._e(),
    _vm._v(" "),
    _vm.loaded
      ? _c(
          "div",
          { staticClass: "woo-project-outer" },
          [
            _vm._l(_vm.options, function(option, index) {
              return _c(
                "div",
                { key: index, staticClass: "pm-wp-repeat pm-row" },
                [
                  _c(
                    "div",
                    { staticClass: "wcp-option-inner" },
                    [
                      _c("action", {
                        model: {
                          value: option.action,
                          callback: function($$v) {
                            _vm.$set(option, "action", $$v)
                          },
                          expression: "option.action"
                        }
                      }),
                      _vm._v(" "),
                      _c("woo-option", { attrs: { option: option } }),
                      _vm._v(" "),
                      option.action == "create"
                        ? _c("user", { attrs: { option: option } })
                        : _vm._e()
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _vm.options.length > 1
                    ? _c("span", {
                        staticClass: "dashicons dashicons-trash wcp-remove",
                        on: {
                          click: function($event) {
                            return _vm.removeOption(index)
                          }
                        }
                      })
                    : _vm._e()
                ]
              )
            }),
            _vm._v(" "),
            _c("div", { staticClass: "woo-footer" }, [
              _c(
                "a",
                {
                  staticClass: "pm-report-submit-button button button-primary",
                  on: {
                    click: function($event) {
                      return _vm.saveOption()
                    }
                  }
                },
                [_vm._v(" " + _vm._s(_vm.__("Save Change", "pm-pro")) + " ")]
              ),
              _vm._v(" "),
              _c(
                "a",
                {
                  staticClass: "button pm-right",
                  on: {
                    click: function($event) {
                      return _vm.addOption()
                    }
                  }
                },
                [_vm._v(" " + _vm._s(_vm.__("Add New", "pm-pro")) + " ")]
              )
            ])
          ],
          2
        )
      : _vm._e()
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "loadmoreanimation" }, [
      _c("div", { staticClass: "load-spinner" }, [
        _c("div", { staticClass: "rect1" }),
        _vm._v(" "),
        _c("div", { staticClass: "rect2" }),
        _vm._v(" "),
        _c("div", { staticClass: "rect3" }),
        _vm._v(" "),
        _c("div", { staticClass: "rect4" }),
        _vm._v(" "),
        _c("div", { staticClass: "rect5" })
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
    require("vue-hot-reload-api")      .rerender("data-v-265ab548", esExports)
  }
}

/***/ })
/******/ ]);