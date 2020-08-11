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
/******/ 	return __webpack_require__(__webpack_require__.s = 19);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
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
/* 1 */
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

var listToStyles = __webpack_require__(25)

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
/* 2 */
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
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(7)(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 4 */
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),
/* 5 */
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.11' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),
/* 6 */
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),
/* 7 */
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),
/* 8 */
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
            type: [Object]
        }
    },

    data: function data() {
        return {
            fields: []
        };
    },
    created: function created() {
        this.fetchField();
    },


    methods: {
        has_task_permission: function has_task_permission() {
            var permission = this.can_edit_task(this.actionData.task);
            return permission;
        },
        selected: function selected(field, option) {
            if (field.value.value == option.title && field.value.color == option.color) {
                return 'selected';
            }

            return false;
        },
        setValue: function setValue(field, event) {
            var dataSet = event.target.value;

            if (dataSet) {
                dataSet = dataSet.split('|');
                field.value.value = dataSet[0];
                field.value.color = dataSet[1];

                this.insertValue(field);
            }
        },
        insertValue: function insertValue(field) {
            var self = this;
            var task_id = self.actionData.task.id;
            var project_id = self.actionData.task.project_id;

            if (!this.validation(field)) {
                return false;
            }

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + project_id + '/custom-fields/' + field.id + '/tasks/' + task_id + '/update',
                data: {
                    value: field.value.value,
                    color: field.value.color
                },
                type: 'POST',
                success: function success(res) {
                    field.editMode = false;
                }
            };

            self.httpRequest(request_data);
        },
        validation: function validation(field) {

            if (field.type == 'number') {

                if (typeof parseFloat(field.value.value) != 'number') {
                    var message = '' + field.value.value + __(' is not number', 'pm-pro');
                    pm.Toastr.error(message);
                    return false;
                }
            }

            if (field.type == 'url') {

                var url = field.value.value.match(/(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g);
                if (url == null) {
                    var _message = '\'' + field.value.value + '\'' + __(' is not url', 'pm-pro');
                    pm.Toastr.error(_message);
                    return false;
                }
            }

            return true;
        },
        isEditMode: function isEditMode(field) {
            if (!this.has_task_permission()) {
                return;
            }

            field.editMode = true;
        },
        fetchField: function fetchField() {
            var self = this;
            var project_id = this.actionData.task.project_id;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + project_id + '/custom-fields/?with=value',
                data: {
                    task_id: self.actionData.task.id
                },
                type: 'GET',
                success: function success(res) {
                    res.data.forEach(function (field) {
                        field.editMode = false;
                    });

                    self.fields = res.data;
                },
                error: function error(res) {}
            };

            self.httpRequest(request_data);
        },
        checkTextColor: function checkTextColor(background) {

            if (typeof background == 'undefined') {
                return '#848484';
            }

            if (background == '') {
                return '#848484';
            }

            var textColor = this.getTextColor(background);

            if (textColor == '') {
                return '#848484';
            }

            return textColor;
        }
    }
});

/***/ }),
/* 9 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__ = __webpack_require__(10);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__custom_field_form_vue__ = __webpack_require__(57);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__field_lists_vue__ = __webpack_require__(66);

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
            type: [Object]
        }
    },

    data: function data() {
        return {
            fields: {
                title: '',
                type: 'dropdown',
                description: '',
                options: []
            },
            modal: {
                title: __('Custom Fields', 'pm-pro'),
                manageLoading: true,
                submitButtonDisabled: false
            },
            isActiveForm: false,
            fieldLoading: true
        };
    },
    created: function created() {
        //set setting custom field tab menu
        this.actionData.tabs.push({
            id: 'custom_field',
            label: __('Custom Field', 'pm-pro'),
            icon: 'flaticon-custom-field-icon',
            active: false
        });

        this.getFields();
    },


    components: {
        'custom-field-form': __WEBPACK_IMPORTED_MODULE_1__custom_field_form_vue__["a" /* default */],
        'field-lists': __WEBPACK_IMPORTED_MODULE_2__field_lists_vue__["a" /* default */]
    },

    computed: {
        fieldItems: function fieldItems() {
            return this.$store.state.customFields.fields;
        }
    },

    methods: {
        toggleForm: function toggleForm() {
            this.isActiveForm = this.isActiveForm ? false : true;
            this.fields = {
                title: '',
                type: 'dropdown',
                description: '',
                options: []
            };
        },
        submit: function submit(options) {

            if (!this.validCustomField()) {
                return false;
            }

            if (this.fields.id) {
                this.update();
            } else {
                this.insert();
            }
        },
        validCustomField: function validCustomField() {
            if (this.modal.loading) {
                return false;
            }

            if (this.fields.title == '') {
                pm.Toastr.warning(__('Field title required!', 'pm-pro'));
                return false;
            }

            if (this.fields.type == '') {
                pm.Toastr.warning(__('Field type required!', 'pm-pro'));
                return false;
            }

            if (this.fields.type == 'dropdown' && !this.fields.options.length) {
                pm.Toastr.warning(__('Field options required!', 'pm-pro'));
                return false;
            }

            return true;
        },
        insert: function insert() {
            var self = this;

            this.modal.loading = true;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/custom-fields',
                type: 'POST',
                data: self.fields,
                success: function success(res) {
                    self.$store.commit('customFields/setField', res.data);
                    self.modal.loading = false;
                    self.fields = {
                        title: '',
                        type: 'dropdown',
                        description: '',
                        options: []
                    };
                    pm.Toastr.success(__('Custom field created successfully!', 'pm-pro'));
                    self.toggleForm();
                },
                error: function error(res) {}
            };

            self.httpRequest(request_data);
        },
        update: function update() {
            var self = this;

            this.modal.loading = true;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/custom-fields/' + self.fields.id + '/update',
                type: 'POST',
                data: self.fields,
                success: function success(res) {
                    self.$store.commit('customFields/updateField', res.data);
                    self.modal.loading = false;
                    self.fields = {
                        title: '',
                        type: 'dropdown',
                        description: '',
                        options: []
                    };
                    pm.Toastr.success(__('Custom field updated successfully!', 'pm-pro'));
                    self.toggleForm();
                },
                error: function error(res) {}
            };

            self.httpRequest(request_data);
        },
        tabContent: function tabContent() {

            var index = this.getIndex(this.actionData.tabs, 'custom_field', 'id');

            return this.actionData.tabs[index].active;
        },
        setSettings: function setSettings(field) {
            this.settings = field;
        },
        getFields: function getFields() {
            var self = this;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/custom-fields/?with=field_value',
                type: 'GET',
                success: function success(res) {
                    self.$store.commit('customFields/setFields', res.data);
                    self.fieldLoading = false;
                },
                error: function error(res) {}
            };

            self.httpRequest(request_data);
        },
        editField: function editField(field) {
            if (field.type == 'dropdown') {
                field.options.forEach(function (field) {
                    field.update = false;
                    field.updateColorMode = false;
                });
            }

            __WEBPACK_IMPORTED_MODULE_0_babel_runtime_core_js_object_assign___default()(this.fields, field);
            this.isActiveForm = true;
            this.modal.submitButton = __('Update', 'pm-pro');
        }
    }

});

/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(30), __esModule: true };

/***/ }),
/* 11 */
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(13);
var defined = __webpack_require__(14);
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(45);
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),
/* 14 */
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),
/* 15 */
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),
/* 16 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__options_vue__ = __webpack_require__(60);
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



Vue.directive('description-textarea', {
    inserted: function inserted(element, text, vnode) {
        if (vnode.context.fields.description) {
            return;
        }
        element.focus();
    }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        fields: {
            type: [Object]
        },
        modal: {
            type: [Object]
        }
    },
    data: function data() {
        return {
            hasDescription: false
        };
    },

    computed: {
        isUpdateMode: function isUpdateMode() {
            return parseInt(this.fields.id) ? true : false;
        }
    },
    components: {
        'options-field': __WEBPACK_IMPORTED_MODULE_0__options_vue__["a" /* default */]
    },
    created: function created() {
        this.hasDescription = this.fields.description ? true : false;
    },

    methods: {
        descriptionStatus: function descriptionStatus() {
            this.hasDescription = this.hasDescription ? false : true;
        }
    }
});

/***/ }),
/* 17 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends__ = __webpack_require__(63);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends__);

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

Vue.directive('color-picker', {
    inserted: function inserted(element) {
        element.getElementsByClassName('color-picker-button')[0].click();
    }
});
/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        options: {
            type: [Array]
        },
        modal: {
            type: [Object]
        }
    },
    data: function data() {
        return {
            fields: {
                title: '',
                color: '',
                key: ''
            },
            optionField: false
        };
    },


    methods: {
        clickOutSideNewOptionColorField: function clickOutSideNewOptionColorField() {
            var colorButton = document.getElementsByClassName('child-field')[0].getElementsByClassName('color-picker-container')[0].getElementsByClassName('button-group');

            if (colorButton.length) {
                colorButton[0].getElementsByClassName('button-small')[1].click();
            }
        },
        modalSubmitButtonDisable: function modalSubmitButtonDisable() {
            this.modal.submitButtonDisabled = true;
        },
        modalSubmitButtonEnable: function modalSubmitButtonEnable() {
            this.modal.submitButtonDisabled = false;
        },
        toggleOptionField: function toggleOptionField() {
            this.optionField = this.optionField ? false : true;
        },
        characterLimit: function characterLimit(title, event) {
            if (title.length > 39) {
                pm.Toastr.warning(__('Maximum character limit 40', 'pm-pro'));
                event.preventDefault();
            }
        },
        clickOutSideOptionColor: function clickOutSideOptionColor(option) {
            if (option.update) {
                option.update = false;
            } else {
                Vue.set(option, 'updateColorMode', false);
            }
        },
        activeColoerUpdateMode: function activeColoerUpdateMode(option) {
            if (option.update) {
                option.update = true;
            } else {
                Vue.set(option, 'updateColorMode', true);
            }
        },
        clickOutSideOptionTitle: function clickOutSideOptionTitle(option) {
            if (option.update) {
                option.update = false;
            } else {
                Vue.set(option, 'update', false);
            }
        },
        clickInsideOptionTitle: function clickInsideOptionTitle(option) {
            if (option.update) {
                option.update = true;
            } else {
                Vue.set(option, 'update', true);
            }
        },
        addNewOption: function addNewOption() {
            if (this.fields.title == '') {
                pm.Toastr.warning(__('Option name required', 'pm-pro'));
                return;
            }

            this.fields.key = this.fields.title.toLowerCase();
            this.fields.key = this.fields.key.replace(' ', '_');

            this.options.push(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends___default()({}, this.fields));

            this.fields.title = '';
            this.fields.color = '';
            this.fields.key = '';

            this.clickOutSideNewOptionColorField();
        },
        deleteOption: function deleteOption(index) {
            this.options.splice(index, 1);
        },
        setColor: function setColor(color) {
            this.fields.color = color;
        },
        updateOptionColor: function updateOptionColor(color, option) {
            option.color = color;
        },
        checkTextColor: function checkTextColor(background) {

            if (typeof background == 'undefined') {
                return '#848484';
            }

            if (background == '') {
                return '#848484';
            }

            var textColor = this.getTextColor(background);

            if (textColor == '') {
                return '#848484';
            }

            return textColor;
        }
    }
});

/***/ }),
/* 18 */
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
        fields: {
            type: [Array]
        }
    },

    methods: {
        edit: function edit(field) {
            this.$emit('edit', field);
        },
        deleteItem: function deleteItem(field) {
            if (!confirm(__('Are you sure!', 'pm-pro'))) {
                return false;
            }
            var self = this;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/custom-fields/' + field.id + '/delete',
                type: 'POST',
                success: function success(res) {
                    self.$store.commit('customFields/deleteField', field.id);
                    pm.Toastr.success(__('Field deleted successfully!', 'pm-pro'));
                }
            };

            self.httpRequest(request_data);
        }
    }
});

/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/custom_field/views/assets/js/';

__webpack_require__(20);

/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(21);

var _router2 = _interopRequireDefault(_router);

var _taskContent = __webpack_require__(22);

var _taskContent2 = _interopRequireDefault(_taskContent);

var _customField = __webpack_require__(27);

var _customField2 = _interopRequireDefault(_customField);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevs_PM_Components.push({
    hook: 'after_single_task_tools',
    component: 'pm-pro-custom-field-single-task-content',
    property: _taskContent2.default
});

weDevs_PM_Components.push({
    hook: 'pm_pro_settings_content',
    component: 'pm-pro-settings-custom-field-content',
    property: _customField2.default
});

/***/ }),
/* 21 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


weDevsPmProAddonRegisterModule('customFields', 'custom_fields');

/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_content_vue__ = __webpack_require__(8);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1f517b1c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_content_vue__ = __webpack_require__(26);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(23)
}
var normalizeComponent = __webpack_require__(2)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_content_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1f517b1c_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_content_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/custom_fields/views/assets/src/components/task-content.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1f517b1c", Component.options)
  } else {
    hotAPI.reload("data-v-1f517b1c", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(24);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("63ac9ddc", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1f517b1c\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-content.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1f517b1c\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-content.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.pm-text-wrap-width {\n  width: 79%;\n}\n.custom-fields-content-wrapper {\n  margin-top: 20px;\n}\n.custom-fields-content-wrapper .label {\n  font-size: 13px;\n  font-weight: bold;\n  margin: 0;\n  padding: 0;\n  margin-bottom: 5px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field {\n  margin-bottom: 5px;\n  min-height: 25px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field:last-child {\n  margin-bottom: 0;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field:hover > .field-container .field-text-wrap {\n  background: #f7f7f7;\n  padding: 0 5px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field:hover > .field-container .field-text-wrap .icon-pm-pencil {\n  display: inline-block;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container {\n  display: flex;\n  align-items: baseline;\n  flex-wrap: wrap;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .label-wrap {\n  width: 20%;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .label-wrap .label {\n  font-size: 13px;\n  font-weight: 500;\n  color: #666;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-text-wrap {\n  padding: 0 5px;\n  width: 79%;\n  word-break: break-all;\n  display: flex;\n  align-items: baseline;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-text-wrap .field-text {\n  width: 94%;\n  font-size: 13px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-text-wrap .icon-pm-pencil {\n  display: none;\n  cursor: pointer;\n  margin-left: 8px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-text-wrap .icon-pm-pencil:before {\n  font-size: 11px;\n  color: #72777c;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-wrap {\n  width: 79%;\n  display: flex;\n  align-items: center;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-wrap .select,\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-wrap .text {\n  width: 100%;\n  border-top-right-radius: 0;\n  border-bottom-right-radius: 0;\n  min-height: 25px;\n  height: 25px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-wrap .field-contetn {\n  width: 75%;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-wrap .flaticon-check-mark-1 {\n  background: #33b5e5;\n  padding: 4px 8px;\n  color: #fff;\n  cursor: pointer;\n  display: flex;\n  height: 25px;\n  align-items: center;\n  margin-left: -1px;\n}\n.pm-pro-task-custom-field-wrap .task-custom-field .field-container .field-wrap .flaticon-cross {\n  background: #d8434c;\n  padding: 4px 8px;\n  color: #fff;\n  cursor: pointer;\n}\n", ""]);

// exports


/***/ }),
/* 25 */
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
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "custom-fields-content-wrapper" }, [
    _c("h3", { staticClass: "label" }, [
      _vm._v(_vm._s(_vm.__("Custom Fields", "pm-pro")))
    ]),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "pm-pro-task-custom-field-wrap" },
      _vm._l(_vm.fields, function(field) {
        return _c("div", { staticClass: "task-custom-field" }, [
          field.type == "dropdown"
            ? _c("div", { staticClass: "field-container" }, [
                _c("div", { staticClass: "label-wrap" }, [
                  _c("label", { staticClass: "label" }, [
                    _vm._v(_vm._s(field.title))
                  ])
                ]),
                _vm._v(" "),
                field.editMode
                  ? _c("div", { staticClass: "field-wrap" }, [
                      _c(
                        "div",
                        { staticClass: "field-contetn" },
                        [
                          _c(
                            "pm-click-wrap",
                            {
                              on: {
                                clickOutSide: function($event) {
                                  field.editMode = false
                                }
                              }
                            },
                            [
                              _c(
                                "select",
                                {
                                  staticClass: "select",
                                  on: {
                                    change: function($event) {
                                      return _vm.setValue(field, $event)
                                    }
                                  }
                                },
                                [
                                  _c("option", { attrs: { value: "" } }, [
                                    _vm._v(
                                      "\n                                    " +
                                        _vm._s(_vm.__("-Select-", "pm-pro")) +
                                        "\n                                "
                                    )
                                  ]),
                                  _vm._v(" "),
                                  _vm._l(field.options, function(option) {
                                    return _c(
                                      "option",
                                      {
                                        domProps: {
                                          value:
                                            option.title + "|" + option.color,
                                          selected: _vm.selected(field, option)
                                        }
                                      },
                                      [
                                        _vm._v(
                                          "\n                                    " +
                                            _vm._s(option.title) +
                                            "\n                                "
                                        )
                                      ]
                                    )
                                  })
                                ],
                                2
                              )
                            ]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("div", [
                        _c("a", {
                          staticClass: "flaticon-check-mark-1",
                          attrs: { href: "#" },
                          on: {
                            click: function($event) {
                              $event.preventDefault()
                              field.editMode = false
                            }
                          }
                        })
                      ])
                    ])
                  : _vm._e(),
                _vm._v(" "),
                !field.editMode && field.value.value
                  ? _c(
                      "div",
                      {
                        staticClass: "field-text-wrap",
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.isEditMode(field)
                          }
                        }
                      },
                      [
                        _c("div", { staticClass: "field-text" }, [
                          _c(
                            "span",
                            {
                              style: {
                                background: field.value.color,
                                color: _vm.checkTextColor(field.value.color),
                                padding: "1px 6px",
                                "border-radius": "3px"
                              }
                            },
                            [
                              _vm._v(
                                "\n                            " +
                                  _vm._s(field.value.value) +
                                  "\n                        "
                              )
                            ]
                          )
                        ]),
                        _vm._v(" "),
                        _vm.has_task_permission()
                          ? _c("span", {
                              staticClass: "icon-pm-pencil",
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.isEditMode(field)
                                }
                              }
                            })
                          : _vm._e()
                      ]
                    )
                  : _vm._e(),
                _vm._v(" "),
                !field.editMode && !field.value.value
                  ? _c(
                      "div",
                      {
                        staticClass: "field-text-wrap",
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.isEditMode(field)
                          }
                        }
                      },
                      [
                        _c("B", { staticClass: "field-text" }, [_vm._v("⋯")]),
                        _vm._v(" "),
                        _vm.has_task_permission()
                          ? _c("span", {
                              staticClass: "icon-pm-pencil",
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.isEditMode(field)
                                }
                              }
                            })
                          : _vm._e()
                      ],
                      1
                    )
                  : _vm._e()
              ])
            : _vm._e(),
          _vm._v(" "),
          field.type == "text" || field.type == "number" || field.type == "url"
            ? _c("div", { staticClass: "field-container" }, [
                _c("div", { staticClass: "label-wrap" }, [
                  _c("label", { staticClass: "label" }, [
                    _vm._v(_vm._s(field.title))
                  ])
                ]),
                _vm._v(" "),
                field.editMode
                  ? _c("div", { staticClass: "field-wrap" }, [
                      _c(
                        "div",
                        { staticClass: "field-contetn" },
                        [
                          _c(
                            "pm-click-wrap",
                            {
                              on: {
                                clickOutSide: function($event) {
                                  field.editMode = false
                                }
                              }
                            },
                            [
                              field.type === "checkbox"
                                ? _c("input", {
                                    directives: [
                                      {
                                        name: "model",
                                        rawName: "v-model",
                                        value: field.value.value,
                                        expression: "field.value.value"
                                      }
                                    ],
                                    staticClass: "text",
                                    attrs: {
                                      placeholder:
                                        _vm.__("Field type ", "pm-pro") +
                                        field.type,
                                      type: "checkbox"
                                    },
                                    domProps: {
                                      checked: Array.isArray(field.value.value)
                                        ? _vm._i(field.value.value, null) > -1
                                        : field.value.value
                                    },
                                    on: {
                                      keyup: function($event) {
                                        if (
                                          !$event.type.indexOf("key") &&
                                          _vm._k(
                                            $event.keyCode,
                                            "enter",
                                            13,
                                            $event.key,
                                            "Enter"
                                          )
                                        ) {
                                          return null
                                        }
                                        return _vm.insertValue(field)
                                      },
                                      change: function($event) {
                                        var $$a = field.value.value,
                                          $$el = $event.target,
                                          $$c = $$el.checked ? true : false
                                        if (Array.isArray($$a)) {
                                          var $$v = null,
                                            $$i = _vm._i($$a, $$v)
                                          if ($$el.checked) {
                                            $$i < 0 &&
                                              _vm.$set(
                                                field.value,
                                                "value",
                                                $$a.concat([$$v])
                                              )
                                          } else {
                                            $$i > -1 &&
                                              _vm.$set(
                                                field.value,
                                                "value",
                                                $$a
                                                  .slice(0, $$i)
                                                  .concat($$a.slice($$i + 1))
                                              )
                                          }
                                        } else {
                                          _vm.$set(field.value, "value", $$c)
                                        }
                                      }
                                    }
                                  })
                                : field.type === "radio"
                                ? _c("input", {
                                    directives: [
                                      {
                                        name: "model",
                                        rawName: "v-model",
                                        value: field.value.value,
                                        expression: "field.value.value"
                                      }
                                    ],
                                    staticClass: "text",
                                    attrs: {
                                      placeholder:
                                        _vm.__("Field type ", "pm-pro") +
                                        field.type,
                                      type: "radio"
                                    },
                                    domProps: {
                                      checked: _vm._q(field.value.value, null)
                                    },
                                    on: {
                                      keyup: function($event) {
                                        if (
                                          !$event.type.indexOf("key") &&
                                          _vm._k(
                                            $event.keyCode,
                                            "enter",
                                            13,
                                            $event.key,
                                            "Enter"
                                          )
                                        ) {
                                          return null
                                        }
                                        return _vm.insertValue(field)
                                      },
                                      change: function($event) {
                                        return _vm.$set(
                                          field.value,
                                          "value",
                                          null
                                        )
                                      }
                                    }
                                  })
                                : _c("input", {
                                    directives: [
                                      {
                                        name: "model",
                                        rawName: "v-model",
                                        value: field.value.value,
                                        expression: "field.value.value"
                                      }
                                    ],
                                    staticClass: "text",
                                    attrs: {
                                      placeholder:
                                        _vm.__("Field type ", "pm-pro") +
                                        field.type,
                                      type: field.type
                                    },
                                    domProps: { value: field.value.value },
                                    on: {
                                      keyup: function($event) {
                                        if (
                                          !$event.type.indexOf("key") &&
                                          _vm._k(
                                            $event.keyCode,
                                            "enter",
                                            13,
                                            $event.key,
                                            "Enter"
                                          )
                                        ) {
                                          return null
                                        }
                                        return _vm.insertValue(field)
                                      },
                                      input: function($event) {
                                        if ($event.target.composing) {
                                          return
                                        }
                                        _vm.$set(
                                          field.value,
                                          "value",
                                          $event.target.value
                                        )
                                      }
                                    }
                                  })
                            ]
                          )
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("div", [
                        _c("a", {
                          staticClass: "flaticon-check-mark-1",
                          attrs: { href: "#" },
                          on: {
                            click: function($event) {
                              $event.preventDefault()
                              return _vm.insertValue(field)
                            }
                          }
                        })
                      ])
                    ])
                  : _vm._e(),
                _vm._v(" "),
                !field.editMode && field.value.value && field.type == "text"
                  ? _c(
                      "div",
                      {
                        staticClass: "field-text-wrap",
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.isEditMode(field)
                          }
                        }
                      },
                      [
                        _c("span", { staticClass: "field-text" }, [
                          _vm._v(_vm._s(field.value.value))
                        ]),
                        _vm._v(" "),
                        _vm.has_task_permission()
                          ? _c("span", {
                              staticClass: "icon-pm-pencil",
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.isEditMode(field)
                                }
                              }
                            })
                          : _vm._e()
                      ]
                    )
                  : _vm._e(),
                _vm._v(" "),
                !field.editMode && field.value.value && field.type == "number"
                  ? _c(
                      "div",
                      {
                        staticClass: "field-text-wrap",
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.isEditMode(field)
                          }
                        }
                      },
                      [
                        _c("span", { staticClass: "field-text" }, [
                          _vm._v(_vm._s(field.value.value))
                        ]),
                        _vm._v(" "),
                        _vm.has_task_permission()
                          ? _c("span", {
                              staticClass: "icon-pm-pencil",
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.isEditMode(field)
                                }
                              }
                            })
                          : _vm._e()
                      ]
                    )
                  : _vm._e(),
                _vm._v(" "),
                !field.editMode && field.value.value && field.type == "url"
                  ? _c("div", { staticClass: "field-text-wrap" }, [
                      _c("span", { staticClass: "field-text" }, [
                        _c(
                          "a",
                          {
                            attrs: { target: "_blank", href: field.value.value }
                          },
                          [
                            _vm._v(
                              _vm._s(_vm.cutString(field.value.value, 50, true))
                            )
                          ]
                        )
                      ]),
                      _vm._v(" "),
                      _vm.has_task_permission()
                        ? _c("span", {
                            staticClass: "icon-pm-pencil",
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.isEditMode(field)
                              }
                            }
                          })
                        : _vm._e()
                    ])
                  : _vm._e(),
                _vm._v(" "),
                !field.editMode && !field.value.value
                  ? _c(
                      "div",
                      {
                        staticClass: "field-text-wrap",
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.isEditMode(field)
                          }
                        }
                      },
                      [
                        _c("B", { staticClass: "field-text" }, [_vm._v("⋯")]),
                        _vm._v(" "),
                        _vm.has_task_permission()
                          ? _c("span", {
                              staticClass: "icon-pm-pencil",
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.isEditMode(field)
                                }
                              }
                            })
                          : _vm._e()
                      ],
                      1
                    )
                  : _vm._e()
              ])
            : _vm._e()
        ])
      }),
      0
    )
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-1f517b1c", esExports)
  }
}

/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_custom_field_vue__ = __webpack_require__(9);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1217f35f_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_custom_field_vue__ = __webpack_require__(70);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(28)
}
var normalizeComponent = __webpack_require__(2)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_custom_field_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_1217f35f_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_custom_field_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/custom_fields/views/assets/src/components/custom-field.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-1217f35f", Component.options)
  } else {
    hotAPI.reload("data-v-1217f35f", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 28 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(29);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("7f87e8c5", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1217f35f\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./custom-field.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-1217f35f\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./custom-field.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.field-wrap {\n  display: flex;\n}\n.flaticon-custom-field-icon {\n  color: #f39b13;\n}\n", ""]);

// exports


/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(31);
module.exports = __webpack_require__(5).Object.assign;


/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(32);

$export($export.S + $export.F, 'Object', { assign: __webpack_require__(42) });


/***/ }),
/* 32 */
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(4);
var core = __webpack_require__(5);
var ctx = __webpack_require__(33);
var hide = __webpack_require__(35);
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
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(34);
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
/* 34 */
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),
/* 35 */
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__(36);
var createDesc = __webpack_require__(41);
module.exports = __webpack_require__(3) ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(37);
var IE8_DOM_DEFINE = __webpack_require__(38);
var toPrimitive = __webpack_require__(40);
var dP = Object.defineProperty;

exports.f = __webpack_require__(3) ? Object.defineProperty : function defineProperty(O, P, Attributes) {
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
/* 37 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(6);
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),
/* 38 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(3) && !__webpack_require__(7)(function () {
  return Object.defineProperty(__webpack_require__(39)('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 39 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(6);
var document = __webpack_require__(4).document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),
/* 40 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(6);
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
/* 41 */
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
/* 42 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var DESCRIPTORS = __webpack_require__(3);
var getKeys = __webpack_require__(43);
var gOPS = __webpack_require__(54);
var pIE = __webpack_require__(55);
var toObject = __webpack_require__(56);
var IObject = __webpack_require__(13);
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__(7)(function () {
  var A = {};
  var B = {};
  // eslint-disable-next-line no-undef
  var S = Symbol();
  var K = 'abcdefghijklmnopqrst';
  A[S] = 7;
  K.split('').forEach(function (k) { B[k] = k; });
  return $assign({}, A)[S] != 7 || Object.keys($assign({}, B)).join('') != K;
}) ? function assign(target, source) { // eslint-disable-line no-unused-vars
  var T = toObject(target);
  var aLen = arguments.length;
  var index = 1;
  var getSymbols = gOPS.f;
  var isEnum = pIE.f;
  while (aLen > index) {
    var S = IObject(arguments[index++]);
    var keys = getSymbols ? getKeys(S).concat(getSymbols(S)) : getKeys(S);
    var length = keys.length;
    var j = 0;
    var key;
    while (length > j) {
      key = keys[j++];
      if (!DESCRIPTORS || isEnum.call(S, key)) T[key] = S[key];
    }
  } return T;
} : $assign;


/***/ }),
/* 43 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __webpack_require__(44);
var enumBugKeys = __webpack_require__(53);

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),
/* 44 */
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__(11);
var toIObject = __webpack_require__(12);
var arrayIndexOf = __webpack_require__(46)(false);
var IE_PROTO = __webpack_require__(49)('IE_PROTO');

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
/* 45 */
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),
/* 46 */
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(12);
var toLength = __webpack_require__(47);
var toAbsoluteIndex = __webpack_require__(48);
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
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(15);
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(15);
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(50)('keys');
var uid = __webpack_require__(52);
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__(5);
var global = __webpack_require__(4);
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__(51) ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),
/* 51 */
/***/ (function(module, exports) {

module.exports = true;


/***/ }),
/* 52 */
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),
/* 53 */
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),
/* 54 */
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),
/* 55 */
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(14);
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),
/* 57 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_custom_field_form_vue__ = __webpack_require__(16);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_86e537bc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_custom_field_form_vue__ = __webpack_require__(65);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(58)
}
var normalizeComponent = __webpack_require__(2)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_custom_field_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_86e537bc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_custom_field_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/custom_fields/views/assets/src/components/custom-field-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-86e537bc", Component.options)
  } else {
    hotAPI.reload("data-v-86e537bc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(59);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("7b5d6e23", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-86e537bc\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./custom-field-form.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-86e537bc\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./custom-field-form.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 59 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.label-color {\n  color: #24292e;\n}\n.field-width {\n  width: 100%;\n}\n.field-focus:focus {\n  border-color: #007cba;\n  color: #016087;\n  box-shadow: 0 0 0 1px #007cba;\n}\n.pm-custom-fields .inline-two-field {\n  display: flex;\n}\n.pm-custom-fields .inline-two-field .first-field {\n  padding-right: 10px;\n}\n.pm-custom-fields .inline-two-field .field {\n  flex: 1;\n}\n.pm-custom-fields .inline-two-field .field .item #field-name {\n  color: #444;\n}\n.pm-custom-fields .inline-two-field .field:first-child {\n  margin-right: 20px;\n}\n.pm-custom-fields .inline-two-field .field .label {\n  display: block;\n  color: #24292e;\n  margin-bottom: 5px;\n}\n.pm-custom-fields .inline-two-field .field .text,\n.pm-custom-fields .inline-two-field .field .select {\n  width: 100%;\n}\n.pm-custom-fields .inline-two-field .field .text:focus,\n.pm-custom-fields .inline-two-field .field .select:focus {\n  border-color: #007cba;\n  color: #016087;\n  box-shadow: 0 0 0 1px #007cba;\n}\n.pm-custom-fields .options-title {\n  font-size: 14px;\n  font-weight: 600;\n  color: #24292e;\n}\n.pm-custom-fields .field-description {\n  margin-top: 15px;\n}\n.pm-custom-fields .field-description .des-label {\n  display: block;\n  width: 100%;\n}\n.pm-custom-fields .field-description .description-textarea {\n  width: 100%;\n}\n.pm-custom-fields .field-description .description-textarea:focus {\n  border-color: #007cba;\n  color: #016087;\n  box-shadow: 0 0 0 1px #007cba;\n}\n", ""]);

// exports


/***/ }),
/* 60 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_options_vue__ = __webpack_require__(17);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5ed12726_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_options_vue__ = __webpack_require__(64);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(61)
}
var normalizeComponent = __webpack_require__(2)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_options_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5ed12726_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_options_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/custom_fields/views/assets/src/components/options.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5ed12726", Component.options)
  } else {
    hotAPI.reload("data-v-5ed12726", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 61 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(62);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("aac4da74", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5ed12726\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./options.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5ed12726\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./options.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.pm-custom-field-options-wrap {\n  margin-top: 15px;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options {\n  padding: 5px 12px;\n  background: #f6f8fa;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .options-title {\n  margin: 0;\n  padding: 0;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap {\n  margin: 10px 0 0 0;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list {\n  padding-bottom: 5px;\n  display: flex;\n  align-items: baseline;\n  margin: 0;\n  line-height: initial;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list:last-child {\n  padding-bottom: 0;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .cross-wrap {\n  width: 16px;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .title-content {\n  width: 80%;\n  display: flex;\n  align-items: center;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .update-option-text-field-wrap {\n  width: 80%;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .update-option-text-field-wrap .update-option-text-field {\n  min-height: inherit !important;\n  height: 22px;\n  width: 100%;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .list-color-wrap {\n  position: relative;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .list-color-wrap .color-picker {\n  position: absolute;\n  z-index: 99;\n  margin-top: 6px;\n  margin-left: -12px;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .list-color-wrap .color-picker .hex-input {\n  display: none;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .list-color-wrap .button-group {\n  display: none;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .list-color-wrap .color-picker-button {\n  display: none;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .icon-wrap {\n  min-height: 22px;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .icon-wrap .check {\n  border-radius: 16px;\n  width: 16px;\n  height: 16px;\n  text-align: center;\n  display: flex;\n  align-items: center;\n  justify-content: center;\n  line-height: 1;\n  font-size: 10px;\n  margin-right: 6px;\n  cursor: pointer;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .title {\n  font-size: 12px;\n  color: #24292e;\n  font-weight: 500;\n  line-height: 1.8;\n  min-height: 22px;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list .cross {\n  font-size: 16px;\n  margin-left: 6px;\n  color: #943636;\n  cursor: pointer;\n  display: none;\n  line-height: 0;\n}\n.pm-custom-field-options-wrap .pm-custom-field-options .option-list-wrap .option-list:hover .cross {\n  display: block;\n}\n.pm-custom-field-options-wrap .fields-wrap .add-new-options {\n  font-size: 12px;\n}\n.pm-custom-field-options-wrap .fields-wrap .child-field {\n  display: flex;\n  position: relative;\n}\n.pm-custom-field-options-wrap .fields-wrap .child-field .option-name {\n  width: 62.5%;\n}\n.pm-custom-field-options-wrap .fields-wrap .child-field .color-picker-container {\n  position: absolute;\n  left: 67.5%;\n}\n.pm-custom-field-options-wrap .fields-wrap .child-field .text-color-field-warp {\n  position: relative;\n  flex: 1;\n}\n.pm-custom-field-options-wrap .fields-wrap .child-field .text-color-field-warp .color-picker-container .hex-input {\n  display: none;\n}\n.pm-custom-field-options-wrap .fields-wrap .child-field .text-color-field-warp .color-picker-container .button-group {\n  display: none;\n}\n", ""]);

// exports


/***/ }),
/* 63 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _assign = __webpack_require__(10);

var _assign2 = _interopRequireDefault(_assign);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = _assign2.default || function (target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i];

    for (var key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        target[key] = source[key];
      }
    }
  }

  return target;
};

/***/ }),
/* 64 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "pm-custom-field-options-wrap" }, [
    _vm.options.length
      ? _c("div", { staticClass: "pm-custom-field-options" }, [
          _vm.options.length
            ? _c("h2", { staticClass: "options-title" }, [
                _vm._v(_vm._s(_vm.__("Options", "pm-pro")))
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.options.length
            ? _c(
                "div",
                { staticClass: "option-list-wrap" },
                _vm._l(_vm.options, function(option, index) {
                  return _vm.options.length
                    ? _c(
                        "div",
                        { key: index, staticClass: "option-list" },
                        [
                          _c(
                            "div",
                            { staticClass: "icon-wrap list-color-wrap" },
                            [
                              _c("span", {
                                staticClass: "check",
                                style: {
                                  border: "1px solid " + option.color,
                                  background: option.color,
                                  color: _vm.checkTextColor(option.color)
                                },
                                domProps: { innerHTML: _vm._s("&#x02713;") },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    return _vm.activeColoerUpdateMode(option)
                                  }
                                }
                              }),
                              _vm._v(" "),
                              option.updateColorMode
                                ? _c(
                                    "pm-click-wrap",
                                    {
                                      on: {
                                        clickOutSide: function($event) {
                                          return _vm.clickOutSideOptionColor(
                                            option
                                          )
                                        }
                                      }
                                    },
                                    [
                                      _c(
                                        "div",
                                        { staticClass: "color-picker" },
                                        [
                                          _c("pm-color-picker", {
                                            directives: [
                                              {
                                                name: "color-picker",
                                                rawName: "v-color-picker"
                                              }
                                            ],
                                            attrs: { value: option.color },
                                            on: {
                                              input: function($event) {
                                                return _vm.updateOptionColor(
                                                  $event,
                                                  option
                                                )
                                              }
                                            }
                                          })
                                        ],
                                        1
                                      )
                                    ]
                                  )
                                : _vm._e()
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c(
                            "pm-click-wrap",
                            {
                              on: {
                                clickOutSide: function($event) {
                                  return _vm.clickOutSideOptionTitle(option)
                                },
                                clickInSide: function($event) {
                                  return _vm.clickInsideOptionTitle(option)
                                }
                              }
                            },
                            [
                              _c("div", { staticClass: "title-content" }, [
                                !option.update
                                  ? _c("span", { staticClass: "title" }, [
                                      _vm._v(_vm._s(option.title))
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                option.update
                                  ? _c(
                                      "span",
                                      {
                                        staticClass:
                                          "update-option-text-field-wrap"
                                      },
                                      [
                                        _c("input", {
                                          directives: [
                                            {
                                              name: "model",
                                              rawName: "v-model",
                                              value: option.title,
                                              expression: "option.title"
                                            }
                                          ],
                                          staticClass:
                                            "update-option-text-field",
                                          attrs: { type: "text" },
                                          domProps: { value: option.title },
                                          on: {
                                            keydown: function($event) {
                                              if (
                                                !$event.type.indexOf("key") &&
                                                _vm._k(
                                                  $event.keyCode,
                                                  "enter",
                                                  13,
                                                  $event.key,
                                                  "Enter"
                                                )
                                              ) {
                                                return null
                                              }
                                              $event.preventDefault()
                                            },
                                            keypress: function($event) {
                                              return _vm.characterLimit(
                                                option.title,
                                                $event
                                              )
                                            },
                                            keyup: function($event) {
                                              if (
                                                !$event.type.indexOf("key") &&
                                                _vm._k(
                                                  $event.keyCode,
                                                  "enter",
                                                  13,
                                                  $event.key,
                                                  "Enter"
                                                )
                                              ) {
                                                return null
                                              }
                                              $event.preventDefault()
                                              return _vm.clickOutSideOptionTitle(
                                                option
                                              )
                                            },
                                            input: function($event) {
                                              if ($event.target.composing) {
                                                return
                                              }
                                              _vm.$set(
                                                option,
                                                "title",
                                                $event.target.value
                                              )
                                            }
                                          }
                                        })
                                      ]
                                    )
                                  : _vm._e(),
                                _vm._v(" "),
                                _c("span", {
                                  staticClass: "cross",
                                  domProps: { innerHTML: _vm._s("&#x02717;") },
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      return _vm.deleteOption(index)
                                    }
                                  }
                                })
                              ])
                            ]
                          )
                        ],
                        1
                      )
                    : _vm._e()
                }),
                0
              )
            : _vm._e()
        ])
      : _vm._e(),
    _vm._v(" "),
    _vm.optionField
      ? _c(
          "div",
          {
            staticClass: "fields-wrap",
            style: {
              "margin-top": _vm.options.length ? "13px" : 0
            }
          },
          [
            _vm.optionField
              ? _c("div", { staticClass: "child-field" }, [
                  _c(
                    "div",
                    { staticClass: "text-color-field-warp" },
                    [
                      _c(
                        "pm-click-wrap",
                        {
                          on: {
                            clickOutSide: function($event) {
                              return _vm.clickOutSideNewOptionColorField()
                            }
                          }
                        },
                        [
                          _c("pm-color-picker", {
                            attrs: { value: _vm.fields.color },
                            on: { input: _vm.setColor }
                          })
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: _vm.fields.title,
                            expression: "fields.title"
                          }
                        ],
                        staticClass: "option-name",
                        attrs: {
                          type: "text",
                          placeholder: _vm.__("Type an option name", "pm-pro")
                        },
                        domProps: { value: _vm.fields.title },
                        on: {
                          keydown: function($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            $event.preventDefault()
                          },
                          keypress: function($event) {
                            return _vm.characterLimit(_vm.fields.title, $event)
                          },
                          keyup: function($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            return _vm.addNewOption()
                          },
                          input: function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(_vm.fields, "title", $event.target.value)
                          }
                        }
                      })
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("div", [
                    _c(
                      "a",
                      {
                        staticClass: "pm-button pm-primary",
                        attrs: { href: "#" },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.addNewOption()
                          }
                        }
                      },
                      [_vm._v(_vm._s(_vm.__("Add option", "pm-pro")))]
                    )
                  ])
                ])
              : _vm._e(),
            _vm._v(" "),
            _c(
              "a",
              {
                staticClass: "add-new-options",
                attrs: { href: "#" },
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.toggleOptionField()
                  }
                }
              },
              [
                !_vm.optionField
                  ? _c("span", { domProps: { innerHTML: _vm._s("&#43;") } })
                  : _vm._e(),
                _vm._v(" "),
                _vm.optionField
                  ? _c("span", { domProps: { innerHTML: _vm._s("&#8722;") } })
                  : _vm._e(),
                _vm._v(" "),
                _c("span", [_vm._v(_vm._s("Close", "pm-pro"))])
              ]
            )
          ]
        )
      : _vm._e(),
    _vm._v(" "),
    !_vm.optionField
      ? _c("div", [
          _c(
            "a",
            {
              staticClass: "add-new-options",
              attrs: { href: "#" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.toggleOptionField()
                }
              }
            },
            [
              !_vm.optionField
                ? _c("span", { domProps: { innerHTML: _vm._s("&#43;") } })
                : _vm._e(),
              _vm._v(" "),
              _vm.optionField
                ? _c("span", { domProps: { innerHTML: _vm._s("&#8722;") } })
                : _vm._e(),
              _vm._v(" "),
              _c("span", [_vm._v(_vm._s("Add an option", "pm-pro"))])
            ]
          )
        ])
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
    require("vue-hot-reload-api")      .rerender("data-v-5ed12726", esExports)
  }
}

/***/ }),
/* 65 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-custom-fields" },
    [
      _c("div", { staticClass: "inline-two-field" }, [
        _c(
          "div",
          {
            staticClass: "field",
            style: {
              "margin-right": _vm.isUpdateMode ? "0" : "20px"
            }
          },
          [
            _c(
              "label",
              { staticClass: "label", attrs: { for: "field-name" } },
              [
                _vm._v(
                  "\n                " +
                    _vm._s(_vm.__("Field Title", "pm-pro")) +
                    "\n            "
                )
              ]
            ),
            _vm._v(" "),
            _c("div", { staticClass: "item" }, [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.fields.title,
                    expression: "fields.title"
                  }
                ],
                staticClass: "text",
                attrs: {
                  id: "field-name",
                  type: "text",
                  placeholder: _vm.__("e.g Priority, Stage, Status", "pm-pro")
                },
                domProps: { value: _vm.fields.title },
                on: {
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.$set(_vm.fields, "title", $event.target.value)
                  }
                }
              })
            ])
          ]
        ),
        _vm._v(" "),
        !_vm.isUpdateMode
          ? _c("div", { staticClass: "field" }, [
              _c("label", { staticClass: "label", attrs: { for: "type" } }, [
                _vm._v(
                  "\n                    " +
                    _vm._s(_vm.__("Type", "pm-pro")) +
                    "\n                "
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "item" }, [
                _c(
                  "select",
                  {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.fields.type,
                        expression: "fields.type"
                      }
                    ],
                    staticClass: "select",
                    attrs: { id: "type" },
                    on: {
                      change: function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.$set(
                          _vm.fields,
                          "type",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      }
                    }
                  },
                  [
                    _c("option", { attrs: { value: "dropdown" } }, [
                      _vm._v(_vm._s(_vm.__("Drop-down", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "text" } }, [
                      _vm._v(_vm._s(_vm.__("Text", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "number" } }, [
                      _vm._v(_vm._s(_vm.__("Number", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "url" } }, [
                      _vm._v(_vm._s(_vm.__("URL", "pm-pro")))
                    ])
                  ]
                )
              ])
            ])
          : _vm._e()
      ]),
      _vm._v(" "),
      _vm.fields.type == "dropdown"
        ? _c("options-field", {
            attrs: { modal: _vm.modal, options: _vm.fields.options }
          })
        : _vm._e(),
      _vm._v(" "),
      _c("div", { staticClass: "field-description" }, [
        !_vm.hasDescription
          ? _c(
              "a",
              {
                staticClass: "des-label",
                attrs: { href: "#" },
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.descriptionStatus()
                  }
                }
              },
              [
                _c("span", [
                  _vm._v(_vm._s(_vm.__("Add description", "pm-pro")))
                ])
              ]
            )
          : _vm._e(),
        _vm._v(" "),
        _vm.hasDescription
          ? _c("textarea", {
              directives: [
                {
                  name: "description-textarea",
                  rawName: "v-description-textarea"
                },
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.fields.description,
                  expression: "fields.description"
                }
              ],
              staticClass: "description-textarea",
              attrs: { id: "description" },
              domProps: { value: _vm.fields.description },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.fields, "description", $event.target.value)
                }
              }
            })
          : _vm._e()
      ])
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-86e537bc", esExports)
  }
}

/***/ }),
/* 66 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_field_lists_vue__ = __webpack_require__(18);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_43a31551_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_field_lists_vue__ = __webpack_require__(69);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(67)
}
var normalizeComponent = __webpack_require__(2)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_field_lists_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_43a31551_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_field_lists_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/custom_fields/views/assets/src/components/field-lists.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-43a31551", Component.options)
  } else {
    hotAPI.reload("data-v-43a31551", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(68);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("7a9d4fe3", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-43a31551\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./field-lists.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-43a31551\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./field-lists.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 68 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.display-table-wrap {\n  margin-top: 10px;\n  font-size: 13px;\n  border: 1px solid #eee;\n  border-bottom: none;\n}\n.display-table-wrap .th-title {\n  font-weight: 500;\n}\n.display-table-wrap thead tr {\n  box-shadow: none !important;\n}\n.display-table-wrap tbody td {\n  padding: 10px 16px;\n}\n.display-table-wrap .tr-wrap .color-attribute {\n  display: inline-flex;\n  align-items: center;\n  justify-content: space-between;\n}\n.display-table-wrap .tr-wrap .color-attribute .color-box {\n  margin-right: 10px;\n  height: 12px;\n  width: 12px;\n}\n.display-table-wrap .tr-wrap:hover .action-td .action-wrap {\n  position: static;\n}\n.display-table-wrap .tr-wrap .action-td .action-wrap {\n  position: relative;\n  left: -9999em;\n  padding-top: 2px;\n  color: #37aedf;\n  font-size: 12px;\n  font-weight: 400;\n}\n.display-table-wrap .tr-wrap .action-td .action-wrap .pipe {\n  color: #ddd;\n}\n", ""]);

// exports


/***/ }),
/* 69 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "display-table-wrap" }, [
    _c("table", { staticClass: "pm-table table-striped table-justified" }, [
      _c("thead", [
        _c("tr", [
          _c("th", { staticClass: "th-title" }, [
            _vm._v(_vm._s(_vm.__("Title", "pm-pro")))
          ]),
          _vm._v(" "),
          _c("th", { staticClass: "th-title" }, [
            _vm._v(_vm._s(_vm.__("Type", "pm-pro")))
          ]),
          _vm._v(" "),
          _c("th", { staticClass: "th-title" }, [
            _vm._v(_vm._s(_vm.__("Description", "pm-pro")))
          ]),
          _vm._v(" "),
          _c("th", { staticClass: "th-title" }, [
            _vm._v(_vm._s(_vm.__("Action", "pm-pro")))
          ])
        ])
      ]),
      _vm._v(" "),
      _c(
        "tbody",
        [
          _vm._l(_vm.fields, function(field) {
            return _vm.fields.length
              ? _c("tr", { staticClass: "tr-wrap" }, [
                  _c("td", { staticClass: "action-td" }, [
                    _c("div", { staticClass: "title" }, [
                      _vm._v(_vm._s(field.title))
                    ])
                  ]),
                  _vm._v(" "),
                  _c("td", [_vm._v(_vm._s(field.type))]),
                  _vm._v(" "),
                  _c("td", [_vm._v(_vm._s(field.description))]),
                  _vm._v(" "),
                  _c("td", [
                    _c("div", { staticClass: "action-wrap" }, [
                      _c("span", [
                        _c(
                          "a",
                          {
                            attrs: { href: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.edit(field)
                              }
                            }
                          },
                          [_vm._v(_vm._s(_vm.__("Edit", "pm-pro")))]
                        )
                      ]),
                      _vm._v(" "),
                      _c("span", { staticClass: "pipe" }, [_vm._v("|")]),
                      _vm._v(" "),
                      _c("span", [
                        _c(
                          "a",
                          {
                            attrs: { href: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.deleteItem(field)
                              }
                            }
                          },
                          [_vm._v(_vm._s(_vm.__("Delete", "pm-pro")))]
                        )
                      ])
                    ])
                  ])
                ])
              : _vm._e()
          }),
          _vm._v(" "),
          !_vm.fields.length
            ? _c("tr", [
                _c("td", { attrs: { colspan: "4" } }, [
                  _vm._v(_vm._s(_vm.__("No field found!", "pm-pro")))
                ])
              ])
            : _vm._e()
        ],
        2
      )
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
    require("vue-hot-reload-api")      .rerender("data-v-43a31551", esExports)
  }
}

/***/ }),
/* 70 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.tabContent()
    ? _c("div", { staticClass: "group" }, [
        _c("div", [
          _vm.fieldLoading
            ? _c(
                "div",
                {
                  staticClass: "loadmoreanimation",
                  staticStyle: { display: "block" }
                },
                [_vm._m(0)]
              )
            : _vm._e(),
          _vm._v(" "),
          !_vm.fieldLoading
            ? _c("div", [
                _c(
                  "a",
                  {
                    staticClass: "pm-button pm-primary",
                    attrs: { href: "#" },
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.toggleForm()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Add New", "pm-pro")))]
                ),
                _vm._v(" "),
                _c(
                  "div",
                  [
                    _c("field-lists", {
                      attrs: { fields: _vm.fieldItems },
                      on: { edit: _vm.editField }
                    })
                  ],
                  1
                )
              ])
            : _vm._e()
        ]),
        _vm._v(" "),
        _vm.isActiveForm
          ? _c(
              "div",
              [
                _c(
                  "pm-popup-modal",
                  {
                    attrs: { options: _vm.modal },
                    on: { submit: _vm.submit, close: _vm.toggleForm }
                  },
                  [
                    _c("custom-field-form", {
                      attrs: { modal: _vm.modal, fields: _vm.fields }
                    })
                  ],
                  1
                )
              ],
              1
            )
          : _vm._e()
      ])
    : _vm._e()
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "load-spinner" }, [
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
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-1217f35f", esExports)
  }
}

/***/ })
/******/ ]);