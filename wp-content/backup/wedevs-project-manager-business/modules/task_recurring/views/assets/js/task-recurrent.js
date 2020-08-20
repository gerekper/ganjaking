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
/******/ 	return __webpack_require__(__webpack_require__.s = 34);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

var store = __webpack_require__(29)('wks');
var uid = __webpack_require__(31);
var Symbol = __webpack_require__(1).Symbol;
var USE_SYMBOL = typeof Symbol == 'function';

var $exports = module.exports = function (name) {
  return store[name] || (store[name] =
    USE_SYMBOL && Symbol[name] || (USE_SYMBOL ? Symbol : uid)('Symbol.' + name));
};

$exports.store = store;


/***/ }),
/* 1 */
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),
/* 2 */
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.11' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(10)(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__(5);
var createDesc = __webpack_require__(11);
module.exports = __webpack_require__(3) ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(6);
var IE8_DOM_DEFINE = __webpack_require__(47);
var toPrimitive = __webpack_require__(48);
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
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(9);
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
};


/***/ }),
/* 7 */
/***/ (function(module, exports) {

var hasOwnProperty = {}.hasOwnProperty;
module.exports = function (it, key) {
  return hasOwnProperty.call(it, key);
};


/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(1);
var core = __webpack_require__(2);
var ctx = __webpack_require__(22);
var hide = __webpack_require__(4);
var has = __webpack_require__(7);
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
/* 9 */
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


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

module.exports = function (bitmap, value) {
  return {
    enumerable: !(bitmap & 1),
    configurable: !(bitmap & 2),
    writable: !(bitmap & 4),
    value: value
  };
};


/***/ }),
/* 12 */
/***/ (function(module, exports) {

// 7.2.1 RequireObjectCoercible(argument)
module.exports = function (it) {
  if (it == undefined) throw TypeError("Can't call method on  " + it);
  return it;
};


/***/ }),
/* 13 */
/***/ (function(module, exports) {

// 7.1.4 ToInteger
var ceil = Math.ceil;
var floor = Math.floor;
module.exports = function (it) {
  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
};


/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

var shared = __webpack_require__(29)('keys');
var uid = __webpack_require__(31);
module.exports = function (key) {
  return shared[key] || (shared[key] = uid(key));
};


/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.13 ToObject(argument)
var defined = __webpack_require__(12);
module.exports = function (it) {
  return Object(defined(it));
};


/***/ }),
/* 16 */
/***/ (function(module, exports) {

module.exports = {};


/***/ }),
/* 17 */
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
/* 18 */
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

var listToStyles = __webpack_require__(38)

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
/* 19 */
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
/* 20 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__task_recurrent_vue__ = __webpack_require__(39);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    components: {
        taskRecurrent: __WEBPACK_IMPORTED_MODULE_0__task_recurrent_vue__["a" /* default */]
    },

    props: {
        actionData: {
            type: [Object],
            default: function _default() {
                return {
                    recurrent: false
                };
            }
        }
    },

    data: function data() {
        return {
            show_modal: false,
            requestProcessng: false
        };
    },


    computed: {
        is_single: function is_single() {
            return this.$store.state.is_single_task;
        },
        task: function task() {
            if (typeof this.actionData.task !== 'undefined') {
                return this.actionData.task;
            } else {
                return this.actionData;
            }
        },
        recurrentClass: function recurrentClass() {
            return {
                'has-recurrent': this.task.recurrent !== '9' && this.task.recurrent !== '0'
            };
        }
    },
    created: function created() {},


    methods: {
        ordinal_suffix_of: function ordinal_suffix_of(i) {
            var j = i % 10,
                k = i % 100;
            if (j == 1 && k != 11) {
                return i + "st";
            }
            if (j == 2 && k != 12) {
                return i + "nd";
            }
            if (j == 3 && k != 13) {
                return i + "rd";
            }
            return i + "th";
        },
        getFullDayName: function getFullDayName(recurrence) {
            var _this = this;

            var text = [];

            recurrence.weekdays.forEach(function (week) {
                if (week.checked === false || week.checked == 'false') {
                    var name = _this.dayName(week.name);
                    text.push(name);
                    text.push(', ');
                }
            });

            if (text.length > 1) {
                text.pop();
            }

            return text.join('');
        },
        dayName: function dayName(name) {
            name = name.toLowerCase();

            var days = {
                'sun': __('Sunday', 'pm-pro'),
                'mon': __('Monday', 'pm-pro'),
                'tue': __('Tuesday', 'pm-pro'),
                'wed': __('Wednesday', 'pm-pro'),
                'thu': __('Thursday', 'pm-pro'),
                'fri': __('Friday', 'pm-pro'),
                'sat': __('Saturday', 'pm-pro')
            };

            return days[name];
        },
        has_task_permission: function has_task_permission() {
            var permission = this.can_edit_task(this.actionData.task);
            return permission;
        },
        loading: function loading(_loading) {
            this.requestProcessng = _loading;
        },
        hasDay: function hasDay(weekes) {
            var day = false;
            weekes.forEach(function (week) {
                if (week.checked == 'false') {
                    day = true;
                }
            });

            return day;
        },
        hasRecurrence: function hasRecurrence() {
            if (!this.actionData.task.recurrent) {
                return false;
            }

            if (parseInt(this.actionData.task.recurrent) == 9) {
                return false;
            }

            if (parseInt(this.actionData.task.recurrent) > 0) {
                if (this.actionData.task.meta.recurrence) {
                    return true;
                }
            }

            return false;
        },
        closeModal: function closeModal() {
            jQuery(this.$refs.recurringWrapper).trigger('click');
        },

        // popper options
        popperOptions: function popperOptions() {
            return {
                placement: 'bottom-end',
                modifiers: { offset: { offset: '0, 3px' } }
            };
        },
        modalShowHide: function modalShowHide() {
            this.show_modal = this.show_modal ? false : true;
        },
        hideModel: function hideModel() {
            //this.show_modal = false;
        }
    }
});

/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends__ = __webpack_require__(42);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray__ = __webpack_require__(55);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray__);


//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    name: 'TaskRecurrent',

    props: {
        task: {
            type: [Object],
            default: function _default() {
                return {
                    recurrent: 0
                };
            }
        }
    },
    watch: {
        task_recurrent_type: function task_recurrent_type(newVal) {
            if (newVal == '1') {
                if (parseInt(this.repeat_term) > 3) {
                    this.repeat_term = 1;
                }
            }

            if (newVal == '2' || newVal == '3') {
                if (this.expire_type == 'date') {
                    this.expire_type = 'n';
                }
            }
        },


        weekdays: {
            handler: function handler(newVal) {
                if (!this.hasWeekDays(newVal)) {
                    this.setWeekDays(newVal);
                }
            },


            deep: true
        }
    },
    data: function data() {
        return {
            isLoading: false,
            task_recurrent_type: this.task.recurrent || 0,
            task_recurrent_data: this.getRecurrentData(),
            recurrentTitle: [{
                title: __('Set Recurrence', 'pm-pro'),
                termTitle: ''
            }, {
                title: __('Weekly Recurrence', 'pm-pro'),
                termTitle: __('Weeks', 'pm-pro')
            }, {
                title: __('Monthly Recurrence', 'pm-pro'),
                termTitle: __('of a Month', 'pm-pro')
            }, {
                title: __('Yearly Recurrence', 'pm-pro'),
                termTitle: __('of a Year', 'pm-pro')
            }],

            weekdays: [{
                name: 'SUN',
                value: '0',
                checked: true
            }, {
                name: 'MON',
                value: '1',
                checked: true
            }, {
                name: 'TUE',
                value: '2',
                checked: true
            }, {
                name: 'WED',
                value: '3',
                checked: true
            }, {
                name: 'THU',
                value: '4',
                checked: true
            }, {
                name: 'FRI',
                value: '5',
                checked: true
            }, {
                name: 'SAT',
                value: '6',
                checked: true
            }],
            expire_type: 'n',
            expire_after_date: new Date().toISOString().slice(0, 10),
            expire_after_occurrence: '0',
            repeat_term: 1,
            repeat_year: new Date().toISOString().slice(0, 10),
            init_once: false,
            duration: 1
        };
    },

    mixins: [PmMixin.projectTaskLists],
    computed: {
        repeatLeaps: function repeatLeaps() {
            var rl = [];
            var i = 0;

            if (this.task_recurrent_type == 2) {
                for (i = 1; i < 32; i++) {
                    rl.push({
                        value: i, text: this.getOrdinal(i)
                    });
                }

                rl.push({
                    value: 'end',
                    text: 'End'
                });
            }

            if (this.task_recurrent_type == 1) {
                for (i = 1; i < 4; i++) {
                    rl.push({
                        value: i,
                        text: i
                    });
                }
            }

            return rl;
        },
        disableSelect: function disableSelect() {

            if (this.task_recurrent_type == 2 || this.task_recurrent_type == 3) {
                return true;
            } else {
                return false;
            }
        },
        is_single: function is_single() {
            return this.$store.state.is_single_task;
        }
    },

    created: function created() {

        this.setWeekDays();
        this.openPopup();
        var self = this;

        if (!this.is_single) {
            pm_add_filter('before_task_save', [this, 'append_recurrence'], 2);
        }

        if (this.is_single) {
            pm_remove_filter('before_task_save', [this, 'append_recurrence']);
        }
    },

    // mounted () {
    //     console.log(this.task);
    // },

    // destroyed () {
    //     if(!this.is_single) {
    //         pm_remove_filter('before_task_save', '');
    //     }
    // },
    methods: {
        setWeekDays: function setWeekDays() {
            if (this.hasDbRecurrence()) {

                if (!this.hasWeekDays(this.task.meta.recurrence.weekdays)) {
                    this.setWeekDayValue(this.task.meta.recurrence.weekdays);
                }

                this.weekdays = [].concat(__WEBPACK_IMPORTED_MODULE_1_babel_runtime_helpers_toConsumableArray___default()(this.task.meta.recurrence.weekdays));
            } else {
                this.setWeekDayValue(this.weekdays);
            }

            this.weekdays.forEach(function (week) {
                if (week.checked == 'false') {
                    week.checked = false;
                }

                if (week.checked == 'true') {
                    week.checked = true;
                }
            });
        },
        hasDbRecurrence: function hasDbRecurrence() {
            var recurrence = false;

            if (this.task.meta.recurrence) {
                if (this.task.meta.recurrence.weekdays) {
                    recurrence = true;
                }
            }

            return recurrence;
        },
        setWeekDayValue: function setWeekDayValue(weekDays) {
            var today = pm.Moment(new Date()).format('ddd');

            weekDays.forEach(function (weekDay) {
                if (weekDay.name.toLowerCase() == today.toLowerCase()) {
                    weekDay.checked = false;
                }
            });
        },
        hasWeekDays: function hasWeekDays(weekDays) {
            var hasWeekDay = false;

            weekDays.forEach(function (weekDay) {
                if (weekDay.checked === false || weekDay.checked == 'false') {
                    hasWeekDay = true;
                }
            });

            return hasWeekDay;
        },
        updateRepeat: function updateRepeat(date) {
            this.repeat_year = pm.Moment(date.startDate).format('YYYY-MM-DD');
        },
        repeatEvery: function repeatEvery() {
            return this.repeat_year;
        },
        updateExpireDate: function updateExpireDate(date) {
            this.expire_after_date = pm.Moment(date.startDate).format('YYYY-MM-DD');
        },
        expireDate: function expireDate() {
            return this.expire_after_date;
        },
        getRecurrentData: function getRecurrentData() {

            if ('meta' in this.task) {
                if ('recurrence' in this.task.meta) {
                    return this.task.meta.recurrence;
                }
            } else {
                return 'undefined';
            }
        },
        cancelPopup: function cancelPopup() {

            if ('meta' in this.task) {
                if ('recurrence' in this.task.meta) {
                    // this.expire_type            = this.task_recurrent_data.expire_type;
                    // this.expire_after_date      = this.task_recurrent_data.expire_after_date;
                    // this.expire_after_occurrence = this.task_recurrent_data.expire_after_occurrence;
                    // this.repeat_term            = this.task_recurrent_data.repeat;
                    // this.repeat_year            = this.task_recurrent_data.repeat_year;
                    // this.weekdays               = this.createWeekdays(this.task_recurrent_data.weekdays);
                    // this.duration            = this.task_recurrent_data.duration;
                }
            } else {
                    // this.task_recurrent_type = this.recurrentStatus(this.task)
                }

            this.closePopup();
        },
        closePopup: function closePopup() {
            // this.updateTaskElement (this.task);

            this.$emit('closeModal');
        },
        openPopup: function openPopup() {
            if (!this.init_once) {
                if (typeof this.task.meta !== 'undefined') {
                    if (typeof this.task.meta.recurrence !== 'undefined') {
                        this.expire_type = this.task_recurrent_data.expire_type;
                        this.expire_after_date = this.task_recurrent_data.expire_after_date;
                        this.expire_after_occurrence = this.task_recurrent_data.expire_after_occurrence;
                        this.repeat_term = this.task_recurrent_data.repeat;
                        this.repeat_year = this.task_recurrent_data.repeat_year;
                        //this.weekdays = this.createWeekdays(this.task_recurrent_data.weekdays);
                        this.duration = this.task_recurrent_data.duration;
                    }
                }
                this.init_once = true;
            }
        },
        getOrdinal: function getOrdinal(n) {
            var s = ["th", "st", "nd", "rd"],
                v = n % 100;
            return n + (s[(v - 20) % 10] || s[v] || s[0]);
        },
        append_recurrence: function append_recurrence(value) {
            if (this.is_single) {
                return value;
            }

            if (value.id && value.id !== this.task.id) {
                return value;
            }

            value.recurrent = this.task.recurrent;

            if (value.recurrent && this.task_recurrent_type != 0) {
                value.recurrence_data = {
                    repeat: this.repeat_term,
                    repeat_year: this.repeat_year,
                    weekdays: this.weekdays,
                    expire_type: this.expire_type,
                    expire_after_date: this.expire_after_date,
                    expire_after_occurrence: this.expire_after_occurrence,
                    duration: this.duration,
                    occurrence_attempted: '0',
                    formatted: '0'
                };
            }
            this.task_recurrent_type = 0;
            return value;
        },
        createWeekdays: function createWeekdays(weekdays) {
            var self = this;
            var $ = jQuery;
            var wd = [];

            if (this.task_recurrent_data.hasOwnProperty('weekdays')) {
                $.each(weekdays, function (index, val) {
                    wd.push({
                        name: val.name,
                        value: val.value,
                        checked: self.convertStr(val.checked) ? true : false
                    });
                });
            }

            return wd;
        },
        convertStr: function convertStr(str) {
            if (str == "false") {
                return false;
            } else {
                return true;
            }
        },
        valid: function valid() {},
        saveRecurrence: function saveRecurrence() {
            var self = this;

            if (this.isLoading) {
                return;
            }

            // if(!this.valid()) {
            //     return;
            // }

            var args = {
                data: {
                    title: this.task.title,
                    task_id: this.task.id,
                    recurrent: this.task.recurrent,
                    recurrence_data: {
                        repeat: this.repeat_term,
                        repeat_year: this.repeat_year,
                        weekdays: this.weekdays,
                        expire_type: this.expire_type,
                        expire_after_date: this.expire_after_date,
                        expire_after_occurrence: this.expire_after_occurrence,
                        duration: this.duration,
                        occurrence_attempted: '0',
                        formatted: '0'
                    }
                },
                callback: function callback(instance, res) {
                    self.isLoading = false;
                    self.$emit('loading', self.isLoading);

                    self.task.meta = __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_extends___default()({}, res.data.meta);
                    self.task.recurrent = res.data.recurrent;

                    if (typeof self.task.activities !== 'undefined') {
                        self.task.activities.data.push(res.activity.data);
                    }

                    self.cancelPopup();
                }
            };

            this.isLoading = true;
            this.$emit('loading', this.isLoading);
            this.updateTask(args);
        },
        changeTaskRecurrent: function changeTaskRecurrent(event) {
            this.task.recurrent = event.target.value;
        }
    }
});

/***/ }),
/* 22 */
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(46);
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
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(9);
var document = __webpack_require__(1).document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.14 / 15.2.3.14 Object.keys(O)
var $keys = __webpack_require__(50);
var enumBugKeys = __webpack_require__(32);

module.exports = Object.keys || function keys(O) {
  return $keys(O, enumBugKeys);
};


/***/ }),
/* 25 */
/***/ (function(module, exports, __webpack_require__) {

// to indexed object, toObject with fallback for non-array-like ES3 strings
var IObject = __webpack_require__(26);
var defined = __webpack_require__(12);
module.exports = function (it) {
  return IObject(defined(it));
};


/***/ }),
/* 26 */
/***/ (function(module, exports, __webpack_require__) {

// fallback for non-array-like ES3 and non-enumerable old V8 strings
var cof = __webpack_require__(27);
// eslint-disable-next-line no-prototype-builtins
module.exports = Object('z').propertyIsEnumerable(0) ? Object : function (it) {
  return cof(it) == 'String' ? it.split('') : Object(it);
};


/***/ }),
/* 27 */
/***/ (function(module, exports) {

var toString = {}.toString;

module.exports = function (it) {
  return toString.call(it).slice(8, -1);
};


/***/ }),
/* 28 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.15 ToLength
var toInteger = __webpack_require__(13);
var min = Math.min;
module.exports = function (it) {
  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
};


/***/ }),
/* 29 */
/***/ (function(module, exports, __webpack_require__) {

var core = __webpack_require__(2);
var global = __webpack_require__(1);
var SHARED = '__core-js_shared__';
var store = global[SHARED] || (global[SHARED] = {});

(module.exports = function (key, value) {
  return store[key] || (store[key] = value !== undefined ? value : {});
})('versions', []).push({
  version: core.version,
  mode: __webpack_require__(30) ? 'pure' : 'global',
  copyright: '© 2019 Denis Pushkarev (zloirock.ru)'
});


/***/ }),
/* 30 */
/***/ (function(module, exports) {

module.exports = true;


/***/ }),
/* 31 */
/***/ (function(module, exports) {

var id = 0;
var px = Math.random();
module.exports = function (key) {
  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
};


/***/ }),
/* 32 */
/***/ (function(module, exports) {

// IE 8- don't enum bug keys
module.exports = (
  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
).split(',');


/***/ }),
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

var def = __webpack_require__(5).f;
var has = __webpack_require__(7);
var TAG = __webpack_require__(0)('toStringTag');

module.exports = function (it, tag, stat) {
  if (it && !has(it = stat ? it : it.prototype, TAG)) def(it, TAG, { configurable: true, value: tag });
};


/***/ }),
/* 34 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _recurrentButton = __webpack_require__(35);

var _recurrentButton2 = _interopRequireDefault(_recurrentButton);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/task-recurrent/views/assets/js/';

// import TaskRecurrent from './components/task-recurrent.vue';

//import RecurrentIcon from './components/recurrent-icon.vue';

// weDevs_PM_Components.push({
//     hook: 'pm_task_form',
//     component: 'pm_task_recurrent_popup',
//     property: TaskRecurrent
// });


weDevs_PM_Components.push({
    hook: 'single_task_tools',
    component: 'pm_task_recurrent_popup',
    property: _recurrentButton2.default
});

// weDevs_PM_Components.push({
//     hook: 'task_inline',
//     component: 'pm_task_recurrent_icon',
//     property: RecurrentIcon
// });

/***/ }),
/* 35 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_recurrent_button_vue__ = __webpack_require__(20);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4d79b4a2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_recurrent_button_vue__ = __webpack_require__(75);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(36)
}
var normalizeComponent = __webpack_require__(19)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_recurrent_button_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4d79b4a2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_recurrent_button_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/task_recurring/views/assets/src/components/recurrent-button.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4d79b4a2", Component.options)
  } else {
    hotAPI.reload("data-v-4d79b4a2", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 36 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(37);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(18)("8f2c45f2", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4d79b4a2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./recurrent-button.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4d79b4a2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./recurrent-button.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 37 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(17)(false);
// imports


// module
exports.push([module.i, "\n.pm-task-recurrent {\n  order: 10;\n  width: 100%;\n  position: relative;\n}\n.pm-task-recurrent .process-text-wrap {\n  position: relative;\n  align-items: baseline !important;\n}\n.pm-task-recurrent .process-text-wrap .process-btn {\n  padding: 5px;\n}\n.pm-task-recurrent .data-active {\n  align-items: flex-start !important;\n}\n.pm-task-recurrent .task-recurrent-view {\n  margin-left: 5px;\n  background-color: rgba(9, 30, 66, 0.04);\n  border-radius: 2px;\n  padding: 0px 5px;\n  font-size: 12px;\n  cursor: pointer;\n}\n.pm-task-recurrent .task-recurrent-view .weekely {\n  display: flex;\n  align-items: center;\n  justify-content: flex-start;\n  flex-wrap: wrap;\n}\n.pm-task-recurrent .task-recurrent-view .weekely .section {\n  margin-right: 10px;\n  display: flex;\n  align-items: center;\n  justify-content: flex-start;\n  flex-wrap: wrap;\n}\n.pm-task-recurrent .task-recurrent-view .weekely .section .label {\n  margin-right: 10px;\n}\n#wedevs-project-manager #pm-single-task-wrap .option-icon-groups .pm-action-wrap .pm-task-recurrent.has-recurrent .icon-pm-loop {\n  color: #61bd4f;\n  font-size: 12px;\n}\n", ""]);

// exports


/***/ }),
/* 38 */
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
/* 39 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_recurrent_vue__ = __webpack_require__(21);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e02605f2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_recurrent_vue__ = __webpack_require__(74);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(40)
}
var normalizeComponent = __webpack_require__(19)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_recurrent_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e02605f2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_recurrent_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/task_recurring/views/assets/src/components/task-recurrent.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e02605f2", Component.options)
  } else {
    hotAPI.reload("data-v-e02605f2", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 40 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(41);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(18)("1512857c", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e02605f2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-recurrent.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e02605f2\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-recurrent.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 41 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(17)(false);
// imports


// module
exports.push([module.i, "\n.pm-task-recurrent {\n  position: relative;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap {\n  white-space: nowrap;\n  top: 34px;\n  left: auto;\n  right: auto;\n  z-index: 9999;\n  font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif;\n  border: 1px solid #DDDDDD;\n  background: #fff;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .recurent-type,\n.pm-task-recurrent .pm-recurrent-task-wrap .repeat-dropdown {\n  width: 100px;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .title {\n  display: block;\n  margin-top: 10px;\n  margin-bottom: 10px;\n  margin-top: 6px;\n  font-size: 14px;\n  font-weight: 600;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .btn-box {\n  margin-top: 10px;\n  margin-bottom: 10px;\n  display: flex;\n  align-items: center;\n  justify-content: flex-end;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .btn-box .cancel {\n  padding: 0 15px;\n  cursor: pointer;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .btn-box .cancel svg {\n  height: 10px;\n  width: 10px;\n  fill: #000;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .title,\n.pm-task-recurrent .pm-recurrent-task-wrap .recurent-type,\n.pm-task-recurrent .pm-recurrent-task-wrap .field-wrap,\n.pm-task-recurrent .pm-recurrent-task-wrap .btn-box {\n  margin-left: 10px;\n  margin-right: 10px;\n}\n@media screen and (max-width: 480px) {\n.pm-task-recurrent .pm-recurrent-task-wrap {\n    white-space: normal;\n}\n}\n.pm-task-recurrent .pm-recurrent-task-wrap {\n  padding: 8px 8px 10px 8px;\n  min-width: 260px;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .pm-recurrent-task-wrap .title {\n  font-size: 14px;\n  font-weight: 600;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .field {\n  margin: 8px auto;\n  display: block;\n  line-height: 1;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .label,\n.pm-task-recurrent .pm-recurrent-task-wrap .input {\n  display: inline-block;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .label {\n  min-width: 100px;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .recurent-type {\n  margin: 0;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .weekDays-selector input {\n  display: none !important;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .weekDays-selector input[type=checkbox] + label {\n  display: inline-block;\n  border-radius: 50%;\n  background: #dddddd;\n  font-size: 10px;\n  font-weight: 600;\n  height: 20px;\n  width: 20px;\n  margin-right: 3px;\n  line-height: 20px;\n  text-align: center;\n  cursor: pointer;\n}\n.pm-task-recurrent .pm-recurrent-task-wrap .weekDays-selector input[type=checkbox]:checked + label {\n  background: #77cc77;\n  color: #ffffff;\n}\n", ""]);

// exports


/***/ }),
/* 42 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _assign = __webpack_require__(43);

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
/* 43 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(44), __esModule: true };

/***/ }),
/* 44 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(45);
module.exports = __webpack_require__(2).Object.assign;


/***/ }),
/* 45 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.3.1 Object.assign(target, source)
var $export = __webpack_require__(8);

$export($export.S + $export.F, 'Object', { assign: __webpack_require__(49) });


/***/ }),
/* 46 */
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(3) && !__webpack_require__(10)(function () {
  return Object.defineProperty(__webpack_require__(23)('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(9);
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
/* 49 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

// 19.1.2.1 Object.assign(target, source, ...)
var DESCRIPTORS = __webpack_require__(3);
var getKeys = __webpack_require__(24);
var gOPS = __webpack_require__(53);
var pIE = __webpack_require__(54);
var toObject = __webpack_require__(15);
var IObject = __webpack_require__(26);
var $assign = Object.assign;

// should work with symbols and should have deterministic property order (V8 bug)
module.exports = !$assign || __webpack_require__(10)(function () {
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
/* 50 */
/***/ (function(module, exports, __webpack_require__) {

var has = __webpack_require__(7);
var toIObject = __webpack_require__(25);
var arrayIndexOf = __webpack_require__(51)(false);
var IE_PROTO = __webpack_require__(14)('IE_PROTO');

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
/* 51 */
/***/ (function(module, exports, __webpack_require__) {

// false -> Array#indexOf
// true  -> Array#includes
var toIObject = __webpack_require__(25);
var toLength = __webpack_require__(28);
var toAbsoluteIndex = __webpack_require__(52);
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
/* 52 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(13);
var max = Math.max;
var min = Math.min;
module.exports = function (index, length) {
  index = toInteger(index);
  return index < 0 ? max(index + length, 0) : min(index, length);
};


/***/ }),
/* 53 */
/***/ (function(module, exports) {

exports.f = Object.getOwnPropertySymbols;


/***/ }),
/* 54 */
/***/ (function(module, exports) {

exports.f = {}.propertyIsEnumerable;


/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _from = __webpack_require__(56);

var _from2 = _interopRequireDefault(_from);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (arr) {
  if (Array.isArray(arr)) {
    for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) {
      arr2[i] = arr[i];
    }

    return arr2;
  } else {
    return (0, _from2.default)(arr);
  }
};

/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(57), __esModule: true };

/***/ }),
/* 57 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(58);
__webpack_require__(67);
module.exports = __webpack_require__(2).Array.from;


/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $at = __webpack_require__(59)(true);

// 21.1.3.27 String.prototype[@@iterator]()
__webpack_require__(60)(String, 'String', function (iterated) {
  this._t = String(iterated); // target
  this._i = 0;                // next index
// 21.1.5.2.1 %StringIteratorPrototype%.next()
}, function () {
  var O = this._t;
  var index = this._i;
  var point;
  if (index >= O.length) return { value: undefined, done: true };
  point = $at(O, index);
  this._i += point.length;
  return { value: point, done: false };
});


/***/ }),
/* 59 */
/***/ (function(module, exports, __webpack_require__) {

var toInteger = __webpack_require__(13);
var defined = __webpack_require__(12);
// true  -> String#at
// false -> String#codePointAt
module.exports = function (TO_STRING) {
  return function (that, pos) {
    var s = String(defined(that));
    var i = toInteger(pos);
    var l = s.length;
    var a, b;
    if (i < 0 || i >= l) return TO_STRING ? '' : undefined;
    a = s.charCodeAt(i);
    return a < 0xd800 || a > 0xdbff || i + 1 === l || (b = s.charCodeAt(i + 1)) < 0xdc00 || b > 0xdfff
      ? TO_STRING ? s.charAt(i) : a
      : TO_STRING ? s.slice(i, i + 2) : (a - 0xd800 << 10) + (b - 0xdc00) + 0x10000;
  };
};


/***/ }),
/* 60 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var LIBRARY = __webpack_require__(30);
var $export = __webpack_require__(8);
var redefine = __webpack_require__(61);
var hide = __webpack_require__(4);
var Iterators = __webpack_require__(16);
var $iterCreate = __webpack_require__(62);
var setToStringTag = __webpack_require__(33);
var getPrototypeOf = __webpack_require__(66);
var ITERATOR = __webpack_require__(0)('iterator');
var BUGGY = !([].keys && 'next' in [].keys()); // Safari has buggy iterators w/o `next`
var FF_ITERATOR = '@@iterator';
var KEYS = 'keys';
var VALUES = 'values';

var returnThis = function () { return this; };

module.exports = function (Base, NAME, Constructor, next, DEFAULT, IS_SET, FORCED) {
  $iterCreate(Constructor, NAME, next);
  var getMethod = function (kind) {
    if (!BUGGY && kind in proto) return proto[kind];
    switch (kind) {
      case KEYS: return function keys() { return new Constructor(this, kind); };
      case VALUES: return function values() { return new Constructor(this, kind); };
    } return function entries() { return new Constructor(this, kind); };
  };
  var TAG = NAME + ' Iterator';
  var DEF_VALUES = DEFAULT == VALUES;
  var VALUES_BUG = false;
  var proto = Base.prototype;
  var $native = proto[ITERATOR] || proto[FF_ITERATOR] || DEFAULT && proto[DEFAULT];
  var $default = $native || getMethod(DEFAULT);
  var $entries = DEFAULT ? !DEF_VALUES ? $default : getMethod('entries') : undefined;
  var $anyNative = NAME == 'Array' ? proto.entries || $native : $native;
  var methods, key, IteratorPrototype;
  // Fix native
  if ($anyNative) {
    IteratorPrototype = getPrototypeOf($anyNative.call(new Base()));
    if (IteratorPrototype !== Object.prototype && IteratorPrototype.next) {
      // Set @@toStringTag to native iterators
      setToStringTag(IteratorPrototype, TAG, true);
      // fix for some old engines
      if (!LIBRARY && typeof IteratorPrototype[ITERATOR] != 'function') hide(IteratorPrototype, ITERATOR, returnThis);
    }
  }
  // fix Array#{values, @@iterator}.name in V8 / FF
  if (DEF_VALUES && $native && $native.name !== VALUES) {
    VALUES_BUG = true;
    $default = function values() { return $native.call(this); };
  }
  // Define iterator
  if ((!LIBRARY || FORCED) && (BUGGY || VALUES_BUG || !proto[ITERATOR])) {
    hide(proto, ITERATOR, $default);
  }
  // Plug for library
  Iterators[NAME] = $default;
  Iterators[TAG] = returnThis;
  if (DEFAULT) {
    methods = {
      values: DEF_VALUES ? $default : getMethod(VALUES),
      keys: IS_SET ? $default : getMethod(KEYS),
      entries: $entries
    };
    if (FORCED) for (key in methods) {
      if (!(key in proto)) redefine(proto, key, methods[key]);
    } else $export($export.P + $export.F * (BUGGY || VALUES_BUG), NAME, methods);
  }
  return methods;
};


/***/ }),
/* 61 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(4);


/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var create = __webpack_require__(63);
var descriptor = __webpack_require__(11);
var setToStringTag = __webpack_require__(33);
var IteratorPrototype = {};

// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
__webpack_require__(4)(IteratorPrototype, __webpack_require__(0)('iterator'), function () { return this; });

module.exports = function (Constructor, NAME, next) {
  Constructor.prototype = create(IteratorPrototype, { next: descriptor(1, next) });
  setToStringTag(Constructor, NAME + ' Iterator');
};


/***/ }),
/* 63 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.2 / 15.2.3.5 Object.create(O [, Properties])
var anObject = __webpack_require__(6);
var dPs = __webpack_require__(64);
var enumBugKeys = __webpack_require__(32);
var IE_PROTO = __webpack_require__(14)('IE_PROTO');
var Empty = function () { /* empty */ };
var PROTOTYPE = 'prototype';

// Create object with fake `null` prototype: use iframe Object with cleared prototype
var createDict = function () {
  // Thrash, waste and sodomy: IE GC bug
  var iframe = __webpack_require__(23)('iframe');
  var i = enumBugKeys.length;
  var lt = '<';
  var gt = '>';
  var iframeDocument;
  iframe.style.display = 'none';
  __webpack_require__(65).appendChild(iframe);
  iframe.src = 'javascript:'; // eslint-disable-line no-script-url
  // createDict = iframe.contentWindow.Object;
  // html.removeChild(iframe);
  iframeDocument = iframe.contentWindow.document;
  iframeDocument.open();
  iframeDocument.write(lt + 'script' + gt + 'document.F=Object' + lt + '/script' + gt);
  iframeDocument.close();
  createDict = iframeDocument.F;
  while (i--) delete createDict[PROTOTYPE][enumBugKeys[i]];
  return createDict();
};

module.exports = Object.create || function create(O, Properties) {
  var result;
  if (O !== null) {
    Empty[PROTOTYPE] = anObject(O);
    result = new Empty();
    Empty[PROTOTYPE] = null;
    // add "__proto__" for Object.getPrototypeOf polyfill
    result[IE_PROTO] = O;
  } else result = createDict();
  return Properties === undefined ? result : dPs(result, Properties);
};


/***/ }),
/* 64 */
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__(5);
var anObject = __webpack_require__(6);
var getKeys = __webpack_require__(24);

module.exports = __webpack_require__(3) ? Object.defineProperties : function defineProperties(O, Properties) {
  anObject(O);
  var keys = getKeys(Properties);
  var length = keys.length;
  var i = 0;
  var P;
  while (length > i) dP.f(O, P = keys[i++], Properties[P]);
  return O;
};


/***/ }),
/* 65 */
/***/ (function(module, exports, __webpack_require__) {

var document = __webpack_require__(1).document;
module.exports = document && document.documentElement;


/***/ }),
/* 66 */
/***/ (function(module, exports, __webpack_require__) {

// 19.1.2.9 / 15.2.3.2 Object.getPrototypeOf(O)
var has = __webpack_require__(7);
var toObject = __webpack_require__(15);
var IE_PROTO = __webpack_require__(14)('IE_PROTO');
var ObjectProto = Object.prototype;

module.exports = Object.getPrototypeOf || function (O) {
  O = toObject(O);
  if (has(O, IE_PROTO)) return O[IE_PROTO];
  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
    return O.constructor.prototype;
  } return O instanceof Object ? ObjectProto : null;
};


/***/ }),
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var ctx = __webpack_require__(22);
var $export = __webpack_require__(8);
var toObject = __webpack_require__(15);
var call = __webpack_require__(68);
var isArrayIter = __webpack_require__(69);
var toLength = __webpack_require__(28);
var createProperty = __webpack_require__(70);
var getIterFn = __webpack_require__(71);

$export($export.S + $export.F * !__webpack_require__(73)(function (iter) { Array.from(iter); }), 'Array', {
  // 22.1.2.1 Array.from(arrayLike, mapfn = undefined, thisArg = undefined)
  from: function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
    var O = toObject(arrayLike);
    var C = typeof this == 'function' ? this : Array;
    var aLen = arguments.length;
    var mapfn = aLen > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    var index = 0;
    var iterFn = getIterFn(O);
    var length, result, step, iterator;
    if (mapping) mapfn = ctx(mapfn, aLen > 2 ? arguments[2] : undefined, 2);
    // if object isn't iterable or it's array with default iterator - use simple case
    if (iterFn != undefined && !(C == Array && isArrayIter(iterFn))) {
      for (iterator = iterFn.call(O), result = new C(); !(step = iterator.next()).done; index++) {
        createProperty(result, index, mapping ? call(iterator, mapfn, [step.value, index], true) : step.value);
      }
    } else {
      length = toLength(O.length);
      for (result = new C(length); length > index; index++) {
        createProperty(result, index, mapping ? mapfn(O[index], index) : O[index]);
      }
    }
    result.length = index;
    return result;
  }
});


/***/ }),
/* 68 */
/***/ (function(module, exports, __webpack_require__) {

// call something on iterator step with safe closing on error
var anObject = __webpack_require__(6);
module.exports = function (iterator, fn, value, entries) {
  try {
    return entries ? fn(anObject(value)[0], value[1]) : fn(value);
  // 7.4.6 IteratorClose(iterator, completion)
  } catch (e) {
    var ret = iterator['return'];
    if (ret !== undefined) anObject(ret.call(iterator));
    throw e;
  }
};


/***/ }),
/* 69 */
/***/ (function(module, exports, __webpack_require__) {

// check on default Array iterator
var Iterators = __webpack_require__(16);
var ITERATOR = __webpack_require__(0)('iterator');
var ArrayProto = Array.prototype;

module.exports = function (it) {
  return it !== undefined && (Iterators.Array === it || ArrayProto[ITERATOR] === it);
};


/***/ }),
/* 70 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $defineProperty = __webpack_require__(5);
var createDesc = __webpack_require__(11);

module.exports = function (object, index, value) {
  if (index in object) $defineProperty.f(object, index, createDesc(0, value));
  else object[index] = value;
};


/***/ }),
/* 71 */
/***/ (function(module, exports, __webpack_require__) {

var classof = __webpack_require__(72);
var ITERATOR = __webpack_require__(0)('iterator');
var Iterators = __webpack_require__(16);
module.exports = __webpack_require__(2).getIteratorMethod = function (it) {
  if (it != undefined) return it[ITERATOR]
    || it['@@iterator']
    || Iterators[classof(it)];
};


/***/ }),
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

// getting tag from 19.1.3.6 Object.prototype.toString()
var cof = __webpack_require__(27);
var TAG = __webpack_require__(0)('toStringTag');
// ES3 wrong here
var ARG = cof(function () { return arguments; }()) == 'Arguments';

// fallback for IE11 Script Access Denied error
var tryGet = function (it, key) {
  try {
    return it[key];
  } catch (e) { /* empty */ }
};

module.exports = function (it) {
  var O, T, B;
  return it === undefined ? 'Undefined' : it === null ? 'Null'
    // @@toStringTag case
    : typeof (T = tryGet(O = Object(it), TAG)) == 'string' ? T
    // builtinTag case
    : ARG ? cof(O)
    // ES3 arguments fallback
    : (B = cof(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : B;
};


/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

var ITERATOR = __webpack_require__(0)('iterator');
var SAFE_CLOSING = false;

try {
  var riter = [7][ITERATOR]();
  riter['return'] = function () { SAFE_CLOSING = true; };
  // eslint-disable-next-line no-throw-literal
  Array.from(riter, function () { throw 2; });
} catch (e) { /* empty */ }

module.exports = function (exec, skipClosing) {
  if (!skipClosing && !SAFE_CLOSING) return false;
  var safe = false;
  try {
    var arr = [7];
    var iter = arr[ITERATOR]();
    iter.next = function () { return { done: safe = true }; };
    arr[ITERATOR] = function () { return iter; };
    exec(arr);
  } catch (e) { /* empty */ }
  return safe;
};


/***/ }),
/* 74 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "pm-recurrent-task-wrap" }, [
    _c("span", { staticClass: "title" }, [
      _vm._v(_vm._s(_vm.recurrentTitle[_vm.task_recurrent_type].title))
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "field-wrap" }, [
      _c("div", { staticClass: "label" }, [
        _c("label", [_vm._v(_vm._s(_vm.__("Repeat:", "pm-pro")))])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "input" }, [
        _c(
          "select",
          {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.task_recurrent_type,
                expression: "task_recurrent_type"
              }
            ],
            staticClass: "recurent-type",
            attrs: { disabled: !_vm.can_edit_task(_vm.task) },
            on: {
              change: [
                function($event) {
                  var $$selectedVal = Array.prototype.filter
                    .call($event.target.options, function(o) {
                      return o.selected
                    })
                    .map(function(o) {
                      var val = "_value" in o ? o._value : o.value
                      return val
                    })
                  _vm.task_recurrent_type = $event.target.multiple
                    ? $$selectedVal
                    : $$selectedVal[0]
                },
                _vm.changeTaskRecurrent
              ]
            }
          },
          [
            _c("option", { domProps: { value: 0 } }, [
              _vm._v(_vm._s(_vm.__("No", "pm-pro")))
            ]),
            _vm._v(" "),
            _c("option", { domProps: { value: 1 } }, [
              _vm._v(_vm._s(_vm.__("Weekly", "pm-pro")))
            ]),
            _vm._v(" "),
            _c("option", { domProps: { value: 2 } }, [
              _vm._v(_vm._s(_vm.__("Monthly", "pm-pro")))
            ]),
            _vm._v(" "),
            _c("option", { domProps: { value: 3 } }, [
              _vm._v(_vm._s(_vm.__("Annualy", "pm-pro")))
            ])
          ]
        )
      ])
    ]),
    _vm._v(" "),
    _vm.task_recurrent_type != 0
      ? _c("div", { staticClass: "field-wrap weekly" }, [
          _c("div", { staticClass: "field repeat-term" }, [
            _c("div", { staticClass: "label" }, [
              _c("label", [_vm._v(_vm._s(_vm.__("Repeat Every:", "pm-pro")))])
            ]),
            _vm._v(" "),
            _vm.can_edit_task(_vm.task)
              ? _c(
                  "div",
                  { staticClass: "input" },
                  [
                    _vm.task_recurrent_type != 3
                      ? _c(
                          "select",
                          {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.repeat_term,
                                expression: "repeat_term"
                              }
                            ],
                            staticClass: "repeat-dropdown",
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
                                _vm.repeat_term = $event.target.multiple
                                  ? $$selectedVal
                                  : $$selectedVal[0]
                              }
                            }
                          },
                          _vm._l(_vm.repeatLeaps, function(rptLeap) {
                            return _c(
                              "option",
                              {
                                key: rptLeap.value,
                                domProps: { value: rptLeap.value }
                              },
                              [
                                _vm._v(
                                  " " +
                                    _vm._s(
                                      rptLeap.text +
                                        " " +
                                        _vm.recurrentTitle[
                                          _vm.task_recurrent_type
                                        ].termTitle
                                    ) +
                                    " "
                                )
                              ]
                            )
                          }),
                          0
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.task_recurrent_type == 3
                      ? _c("pm-vue2-daterange-picker", {
                          attrs: {
                            opens: "right",
                            singleDatePicker: true,
                            startDate: _vm.repeatEvery(),
                            endDate: _vm.repeatEvery(),
                            showDropdowns: true,
                            autoApply: true,
                            disabledCancelBtn: true
                          },
                          on: { update: _vm.updateRepeat }
                        })
                      : _vm._e()
                  ],
                  1
                )
              : _c("div", { staticClass: "input" }, [
                  _vm.task_recurrent_type != 3
                    ? _c("span", [
                        _vm._v(
                          " " +
                            _vm._s(_vm.repeatLeaps[_vm.repeat_term].text) +
                            " "
                        )
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.task_recurrent_type == 3
                    ? _c("span", [_vm._v(" " + _vm._s(_vm.repeat_year) + " ")])
                    : _vm._e()
                ])
          ]),
          _vm._v(" "),
          _vm.task_recurrent_type == 1
            ? _c("div", { staticClass: "field weekdays" }, [
                _c(
                  "div",
                  { staticClass: "weekDays-selector" },
                  [
                    _vm._l(_vm.weekdays, function(weekday) {
                      return [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: weekday.checked,
                              expression: "weekday.checked"
                            }
                          ],
                          key: weekday.name,
                          staticClass: "weekday",
                          attrs: {
                            disabled: !_vm.can_edit_task(_vm.task),
                            type: "checkbox",
                            id: "weekday-" + weekday.name
                          },
                          domProps: {
                            value: weekday.value,
                            checked: Array.isArray(weekday.checked)
                              ? _vm._i(weekday.checked, weekday.value) > -1
                              : weekday.checked
                          },
                          on: {
                            change: function($event) {
                              var $$a = weekday.checked,
                                $$el = $event.target,
                                $$c = $$el.checked ? true : false
                              if (Array.isArray($$a)) {
                                var $$v = weekday.value,
                                  $$i = _vm._i($$a, $$v)
                                if ($$el.checked) {
                                  $$i < 0 &&
                                    _vm.$set(
                                      weekday,
                                      "checked",
                                      $$a.concat([$$v])
                                    )
                                } else {
                                  $$i > -1 &&
                                    _vm.$set(
                                      weekday,
                                      "checked",
                                      $$a
                                        .slice(0, $$i)
                                        .concat($$a.slice($$i + 1))
                                    )
                                }
                              } else {
                                _vm.$set(weekday, "checked", $$c)
                              }
                            }
                          }
                        }),
                        _vm._v(" "),
                        _c(
                          "label",
                          {
                            key: weekday.name,
                            attrs: {
                              disabled: !_vm.can_edit_task(_vm.task),
                              for: "weekday-" + weekday.name
                            }
                          },
                          [_vm._v(_vm._s(weekday.name.charAt(0)))]
                        )
                      ]
                    })
                  ],
                  2
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("div", { staticClass: "field expire-type" }, [
            _vm._m(0),
            _vm._v(" "),
            _c("div", { staticClass: "input" }, [
              _c(
                "select",
                {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.expire_type,
                      expression: "expire_type"
                    }
                  ],
                  attrs: { disabled: !_vm.can_edit_task(_vm.task) },
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
                      _vm.expire_type = $event.target.multiple
                        ? $$selectedVal
                        : $$selectedVal[0]
                    }
                  }
                },
                [
                  _c("option", { attrs: { value: "n" } }, [
                    _vm._v(_vm._s(_vm.__("Never", "pm-pro")))
                  ]),
                  _vm._v(" "),
                  _c(
                    "option",
                    { attrs: { disabled: _vm.disableSelect, value: "date" } },
                    [_vm._v(_vm._s(_vm.__("On Date", "pm-pro")))]
                  ),
                  _vm._v(" "),
                  _c("option", { attrs: { value: "occurrence" } }, [
                    _vm._v(_vm._s(_vm.__("After Occurrence", "pm-pro")))
                  ])
                ]
              )
            ])
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "field" }, [
            _vm.expire_type == "occurrence"
              ? _c("div", { staticClass: "occurrence" }, [
                  _c("div", { staticClass: "label" }, [
                    _c("label", [
                      _vm._v(_vm._s(_vm.__(" Occurrcence:", "pm-pro")))
                    ])
                  ]),
                  _vm._v(" "),
                  _vm.can_edit_task(_vm.task)
                    ? _c("div", { staticClass: "input" }, [
                        _c("input", {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.expire_after_occurrence,
                              expression: "expire_after_occurrence"
                            }
                          ],
                          attrs: {
                            type: "number",
                            min: "0",
                            max: "1200",
                            step: "1"
                          },
                          domProps: { value: _vm.expire_after_occurrence },
                          on: {
                            input: function($event) {
                              if ($event.target.composing) {
                                return
                              }
                              _vm.expire_after_occurrence = $event.target.value
                            }
                          }
                        })
                      ])
                    : _c("div", { staticClass: "input" }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(_vm.expire_after_occurrence) +
                            "\n                "
                        )
                      ])
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.expire_type == "date"
              ? _c("div", { staticClass: "date" }, [
                  _vm._m(1),
                  _vm._v(" "),
                  _vm.can_edit_task(_vm.task)
                    ? _c(
                        "div",
                        { staticClass: "input" },
                        [
                          _c("pm-vue2-daterange-picker", {
                            attrs: {
                              opens: "right",
                              singleDatePicker: true,
                              startDate: _vm.expireDate(),
                              endDate: _vm.expireDate(),
                              showDropdowns: true,
                              autoApply: true,
                              disabledCancelBtn: true
                            },
                            on: { update: _vm.updateExpireDate }
                          })
                        ],
                        1
                      )
                    : _c("div", { staticClass: "input" }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(_vm.expire_after_date) +
                            "\n                "
                        )
                      ])
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "field duration" }, [
            _vm._m(2),
            _vm._v(" "),
            _vm.can_edit_task(_vm.task)
              ? _c("div", { staticClass: "input" }, [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.duration,
                        expression: "duration"
                      }
                    ],
                    attrs: { type: "number", min: "1", step: "1", max: "300" },
                    domProps: { value: _vm.duration },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.duration = $event.target.value
                      }
                    }
                  })
                ])
              : _c("div", { staticClass: "input" }, [
                  _vm._v(
                    "\n                " +
                      _vm._s(_vm.duration) +
                      "\n            "
                  )
                ])
          ])
        ])
      : _vm._e(),
    _vm._v(" "),
    _vm.is_single && _vm.can_edit_task(_vm.task)
      ? _c("div", [
          _c("hr"),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "btn-box" },
            [
              _c(
                "a",
                {
                  staticClass: "cancel",
                  on: {
                    click: function($event) {
                      $event.preventDefault()
                      return _vm.cancelPopup()
                    }
                  }
                },
                [
                  _c(
                    "svg",
                    {
                      attrs: {
                        version: "1.1",
                        id: "Capa_1",
                        xmlns: "http://www.w3.org/2000/svg",
                        "xmlns:xlink": "http://www.w3.org/1999/xlink",
                        x: "0px",
                        y: "0px",
                        viewBox: "0 0 241.171 241.171",
                        "xml:space": "preserve"
                      }
                    },
                    [
                      _c("path", {
                        attrs: {
                          id: "Close",
                          d:
                            "M138.138,120.754l99.118-98.576c4.752-4.704,4.752-12.319,0-17.011c-4.74-4.704-12.439-4.704-17.179,0 l-99.033,98.492L21.095,3.699c-4.74-4.752-12.439-4.752-17.179,0c-4.74,4.764-4.74,12.475,0,17.227l99.876,99.888L3.555,220.497 c-4.74,4.704-4.74,12.319,0,17.011c4.74,4.704,12.439,4.704,17.179,0l100.152-99.599l99.551,99.563 c4.74,4.752,12.439,4.752,17.179,0c4.74-4.764,4.74-12.475,0-17.227L138.138,120.754z"
                        }
                      })
                    ]
                  )
                ]
              ),
              _vm._v(" "),
              _c("pm-button", {
                attrs: {
                  label: _vm.__("Done", "pm-pro"),
                  isPrimary: "",
                  spinner: _vm.isLoading,
                  type: "button"
                },
                on: {
                  onClick: function($event) {
                    return _vm.saveRecurrence()
                  }
                }
              })
            ],
            1
          )
        ])
      : _vm._e()
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "label" }, [
      _c("label", [_vm._v("Expires :")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "label" }, [
      _c("label", [_vm._v("Expire Date:")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "label" }, [
      _c("label", [_vm._v("Duration (day):")])
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-e02605f2", esExports)
  }
}

/***/ }),
/* 75 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _obj
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      ref: "recurringWrapper",
      staticClass: "pm-task-recurrent nonSortableTag context"
    },
    [
      _c("h3", { staticClass: "label" }, [
        _vm._v(_vm._s(_vm.__("Recurring", "pm-pro")))
      ]),
      _vm._v(" "),
      _c(
        "div",
        {
          class: _vm.classnames(
            ((_obj = {}),
            (_obj["process-1"] = _vm.hasRecurrence() ? false : true),
            (_obj["data-active"] = _vm.hasRecurrence() ? true : false),
            _obj)
          )
        },
        [
          _c(
            "div",
            {},
            [
              _vm.has_task_permission()
                ? _c(
                    "pm-popper",
                    {
                      attrs: { trigger: "click", options: _vm.popperOptions() }
                    },
                    [
                      _c(
                        "div",
                        { staticClass: "pm-popper popper" },
                        [
                          _c("task-recurrent", {
                            attrs: { task: _vm.task },
                            on: {
                              closeModal: _vm.closeModal,
                              loading: _vm.loading
                            }
                          })
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        {
                          staticClass: "process-text-wrap",
                          attrs: { slot: "reference" },
                          slot: "reference"
                        },
                        [
                          _c(
                            "a",
                            {
                              staticClass: "display-flex process-btn",
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                }
                              }
                            },
                            [
                              _c("i", [
                                _c(
                                  "svg",
                                  {
                                    staticStyle: {
                                      "enable-background":
                                        "new 0 0 344.37 344.37"
                                    },
                                    attrs: {
                                      version: "1.1",
                                      id: "Capa_1",
                                      xmlns: "http://www.w3.org/2000/svg",
                                      "xmlns:xlink":
                                        "http://www.w3.org/1999/xlink",
                                      x: "0px",
                                      y: "0px",
                                      width: "344.37px",
                                      height: "344.37px",
                                      viewBox: "0 0 344.37 344.37",
                                      "xml:space": "preserve"
                                    }
                                  },
                                  [
                                    _c("g", [
                                      _c("g", [
                                        _c("path", {
                                          attrs: {
                                            d:
                                              "M334.485,37.463c-6.753-1.449-13.396,2.853-14.842,9.603l-9.084,42.391C281.637,40.117,228.551,9.155,170.368,9.155 c-89.603,0-162.5,72.896-162.5,162.5c0,6.903,5.596,12.5,12.5,12.5c6.903,0,12.5-5.597,12.5-12.5 c0-75.818,61.682-137.5,137.5-137.5c49.429,0,94.515,26.403,118.925,68.443l-41.674-8.931c-6.752-1.447-13.396,2.854-14.841,9.604 c-1.446,6.75,2.854,13.396,9.604,14.842l71.536,15.33c1.215,0.261,2.449,0.336,3.666,0.234c2.027-0.171,4.003-0.836,5.743-1.962 c2.784-1.801,4.738-4.634,5.433-7.875l15.331-71.536C345.535,45.555,341.235,38.911,334.485,37.463z"
                                          }
                                        }),
                                        _vm._v(" "),
                                        _c("path", {
                                          attrs: {
                                            d:
                                              "M321.907,155.271c-6.899,0.228-12.309,6.006-12.081,12.905c1.212,36.708-11.942,71.689-37.042,98.504 c-25.099,26.812-59.137,42.248-95.844,43.46c-1.53,0.05-3.052,0.075-4.576,0.075c-47.896-0.002-92.018-24.877-116.936-65.18 l43.447,11.65c6.668,1.787,13.523-2.168,15.311-8.837c1.788-6.668-2.168-13.522-8.836-15.312l-70.664-18.946 c-3.202-0.857-6.615-0.409-9.485,1.247c-2.872,1.656-4.967,4.387-5.826,7.589L0.43,293.092 c-1.788,6.668,2.168,13.522,8.836,15.311c1.085,0.291,2.173,0.431,3.245,0.431c5.518,0,10.569-3.684,12.066-9.267l10.649-39.717 c29.624,46.647,81.189,75.367,137.132,75.365c1.797,0,3.604-0.029,5.408-0.089c43.381-1.434,83.608-19.674,113.271-51.362 s45.209-73.031,43.776-116.413C334.586,160.453,328.805,155.026,321.907,155.271z"
                                          }
                                        })
                                      ])
                                    ]),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g"),
                                    _vm._v(" "),
                                    _c("g")
                                  ]
                                )
                              ])
                            ]
                          ),
                          _vm._v(" "),
                          _vm.hasRecurrence()
                            ? _c(
                                "div",
                                {
                                  staticClass:
                                    "process-results task-recurrent-view"
                                },
                                [
                                  _vm.actionData.task.recurrent == "1" &&
                                  _vm.actionData.task.meta.recurrence
                                    ? _c("div", { staticClass: "weekely" }, [
                                        _c("div", { staticClass: "text" }, [
                                          _vm.actionData.task.meta.recurrence
                                            .repeat == "1"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    _vm._s(
                                                      "Weekly on",
                                                      "pm-pro"
                                                    )
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .repeat == "2"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    _vm._s(
                                                      "Every 2 weeks on",
                                                      "pm-pro"
                                                    )
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .repeat == "3"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    _vm._s(
                                                      "Every 3 Weeks on",
                                                      "pm-pro"
                                                    )
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _c("span", { staticClass: "value" }, [
                                            _vm._v(
                                              _vm._s(
                                                _vm.getFullDayName(
                                                  _vm.actionData.task.meta
                                                    .recurrence
                                                )
                                              )
                                            )
                                          ]),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "date"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    _vm._s(
                                                      _vm.__("until ", "pm-pro")
                                                    ) +
                                                      _vm._s(
                                                        _vm.pmDateFormat(
                                                          _vm.actionData.task
                                                            .meta.recurrence
                                                            .expire_after_date,
                                                          "MMM DD, YYYY"
                                                        )
                                                      )
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "occurrence" &&
                                          _vm.actionData.task.meta.recurrence
                                            .expire_after_occurrence == "1"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "repeat once",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "occurrence" &&
                                          parseInt(
                                            _vm.actionData.task.meta.recurrence
                                              .expire_after_occurrence
                                          ) > 1
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.actionData.task.meta
                                                          .recurrence
                                                          .expire_after_occurrence
                                                      ) +
                                                      " " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "times",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e()
                                        ])
                                      ])
                                    : _vm._e(),
                                  _vm._v(" "),
                                  _vm.actionData.task.recurrent == "2" &&
                                  _vm.actionData.task.meta.recurrence
                                    ? _c("div", { staticClass: "monthly" }, [
                                        _c("div", { staticClass: "text" }, [
                                          _vm.actionData.task.meta.recurrence
                                            .repeat == "1"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "Monthly",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          parseInt(
                                            _vm.actionData.task.meta.recurrence
                                              .repeat
                                          ) > 1
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "Month on day ",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      " " +
                                                      _vm._s(
                                                        _vm.ordinal_suffix_of(
                                                          _vm.actionData.task
                                                            .meta.recurrence
                                                            .repeat
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "occurrence" &&
                                          _vm.actionData.task.meta.recurrence
                                            .expire_after_occurrence == "1"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "repeat once",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "occurrence" &&
                                          parseInt(
                                            _vm.actionData.task.meta.recurrence
                                              .expire_after_occurrence
                                          ) > 1
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.actionData.task.meta
                                                          .recurrence
                                                          .expire_after_occurrence
                                                      ) +
                                                      " " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "times",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e()
                                        ])
                                      ])
                                    : _vm._e(),
                                  _vm._v(" "),
                                  _vm.actionData.task.recurrent == "3" &&
                                  _vm.actionData.task.meta.recurrence
                                    ? _c("div", { staticClass: "yearly" }, [
                                        _c("div", { staticClass: "text" }, [
                                          _c("span", { staticClass: "value" }, [
                                            _vm._v(
                                              "\n                                    " +
                                                _vm._s(
                                                  _vm.__(
                                                    "Annualy on ",
                                                    "pm-pro"
                                                  )
                                                ) +
                                                " " +
                                                _vm._s(
                                                  _vm.pmDateFormat(
                                                    _vm.actionData.task.meta
                                                      .recurrence.repeat_year,
                                                    "DD MMMM"
                                                  )
                                                ) +
                                                "\n                                "
                                            )
                                          ]),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "occurrence" &&
                                          _vm.actionData.task.meta.recurrence
                                            .expire_after_occurrence == "1"
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "repeat once",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _vm.actionData.task.meta.recurrence
                                            .expire_type == "occurrence" &&
                                          parseInt(
                                            _vm.actionData.task.meta.recurrence
                                              .expire_after_occurrence
                                          ) > 1
                                            ? _c(
                                                "span",
                                                { staticClass: "value" },
                                                [
                                                  _vm._v(
                                                    "\n                                    " +
                                                      _vm._s(
                                                        _vm.actionData.task.meta
                                                          .recurrence
                                                          .expire_after_occurrence
                                                      ) +
                                                      " " +
                                                      _vm._s(
                                                        _vm.__(
                                                          "times",
                                                          "pm-pro"
                                                        )
                                                      ) +
                                                      "\n                                "
                                                  )
                                                ]
                                              )
                                            : _vm._e()
                                        ])
                                      ])
                                    : _vm._e()
                                ]
                              )
                            : _vm._e(),
                          _vm._v(" "),
                          !_vm.hasRecurrence()
                            ? _c("div", { staticClass: "helper-text" }, [
                                _vm._v(
                                  _vm._s(_vm.__("Add Recurring", "pm-pro"))
                                )
                              ])
                            : _vm._e()
                        ]
                      )
                    ]
                  )
                : _vm._e(),
              _vm._v(" "),
              !_vm.has_task_permission() && !_vm.hasRecurrence()
                ? _c("div", [
                    _c("span", [
                      _vm._v(_vm._s("This task is not recurrence!", "pm-pro"))
                    ])
                  ])
                : _vm._e()
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _vm.requestProcessng
        ? _c("div", { staticClass: "spinner-wrap" }, [_vm._m(0)])
        : _vm._e()
    ]
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "task-tool-spinner" }, [
      _c("div", { staticClass: "bounce1" }),
      _vm._v(" "),
      _c("div", { staticClass: "bounce2" }),
      _vm._v(" "),
      _c("div", { staticClass: "bounce3" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-4d79b4a2", esExports)
  }
}

/***/ })
/******/ ]);