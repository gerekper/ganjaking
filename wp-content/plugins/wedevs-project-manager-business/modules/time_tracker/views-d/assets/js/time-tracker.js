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
/******/ 	return __webpack_require__(__webpack_require__.s = 55);
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
/* 2 */
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

var listToStyles = __webpack_require__(60)

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
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    data: function data() {
        return {
            'content': true
        };
    },

    computed: {
        isArchivedTaskListComputed: function isArchivedTaskListComputed() {
            return this.isArchivedTaskList(this.actionData.task);
        }
    },
    created: function created() {
        var self = this;
        window.stop_watch = function (param) {
            var args = JSON.parse(jQuery(param).attr('time'));
            jQuery('#timer_id_' + args.task_id).trigger('click');
            setTimeout(function () {
                self.timeStart(window.currentRunningTask);
            }, 1000);
        };
    },


    methods: {
        showCustomLogForm: function showCustomLogForm(list_id, task_id, actionData, status) {
            status = status || 'toggle';

            var form = actionData.task.custom_time_form;

            if (status == 'toggle') {
                actionData.task.custom_time_form = form ? false : true;
            } else {
                actionData.task.custom_time_form = status;
            }
        },
        isArchivedTaskList: function isArchivedTaskList(task) {
            if (typeof task.task_list !== 'undefined') {
                if (task.task_list.data.status === 'archived') {
                    return true;
                }
            }
            return false;
        },
        getListsTask: function getListsTask(actionData) {
            if (typeof actionData.list === 'undefined') {
                return;
            }
            var lists = this.$store.state.projectTaskLists.lists;

            if (lists.length) {
                var list_index = this.getIndex(lists, actionData.list.id, 'id');
                var list = lists[list_index];

                if (actionData.task.status) {
                    if (typeof lists[list_index].complete_tasks == 'undefined') {
                        return [];
                    }
                    var task_index = this.getIndex(lists[list_index].complete_tasks.data, actionData.task.id, 'id');
                    var task = lists[list_index].complete_tasks.data[task_index];
                } else {
                    if (typeof lists[list_index].incomplete_tasks == 'undefined') {
                        return [];
                    }
                    var task_index = this.getIndex(lists[list_index].incomplete_tasks.data, actionData.task.id, 'id');
                    var task = lists[list_index].incomplete_tasks.data[task_index];
                }

                return task;
            }

            return actionData.task;
        },
        timeStart: function timeStart(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + 'pm-pro/v2/time/',
                type: 'POST',
                data: args.data,
                success: function success(res) {

                    // Display a success toast, with a title
                    //pm.Toastr.success(res.message);

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                },
                error: function error(res) {

                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.options = {
                            "closeButton": true,
                            "timeOut": 0,
                            "tapToDismiss": false
                        };
                        pm.Toastr.warning(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        timeStop: function timeStop(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + 'pm-pro/v2/time/update',
                type: 'POST',
                data: args.data,

                success: function success(res) {

                    // Display a success toast, with a title
                    //pm.Toastr.success(res.message);


                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }

                    pmBus.$emit('pm_after_stop_time');
                },
                error: function error(res) {

                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        deleteTimeLog: function deleteTimeLog(args) {
            if (args.run_status == 1) {
                // Display a success toast, with a title
                pm.Toastr.error('Please stop your time tracker');
                return;
            }
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + 'pm-pro/v2/time/' + args.data.time_id + '/delete',
                type: 'POST',
                data: args.data,

                success: function success(res) {

                    // self.$store.commit('timeTracker/afterDeletedTime',
                    //     {
                    //         time_id: args.data.time_id,
                    //         task_id: args.data.task_id,
                    //         list_id: args.data.list_id,
                    //         res: res.data
                    //     }
                    // );

                    // Display a success toast, with a title
                    pm.Toastr.success(res.message);

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                },
                error: function error(res) {

                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        newCustomTime: function newCustomTime(args) {
            if (args.run_status == 1) {
                // Display a success toast, with a title
                pm.Toastr.error(this.__('Please stop your time tracker', 'pm-pro'));
                return;
            }

            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + 'pm-pro/v2/custom-time/',
                type: 'POST',
                data: args.data,

                success: function success(res) {

                    // self.$store.commit('timeTracker/afterStartTime',
                    //     {
                    //         time_id: args.data.time_id,
                    //         task_id: args.data.task_id,
                    //         list_id: args.data.list_id,
                    //         res: res.data
                    //     }
                    // );

                    // Display a success toast, with a title
                    pm.Toastr.success(res.message);

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }

                    pmBus.$emit('pm_after_add_custom_time', res);
                },
                error: function error(res) {

                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        getOthersTimeLog: function getOthersTimeLog(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + 'pm-pro/v2/others-time/',
                type: 'GET',
                data: args.data,

                success: function success(res) {

                    self.$store.commit('timeTracker/setOthersLog', res);

                    // Display a success toast, with a title
                    //pm.Toastr.success(res.message);

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                },
                error: function error(res) {

                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        deleteOthersTimeLog: function deleteOthersTimeLog(args) {

            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + 'pm-pro/v2/time/' + args.data.time_id + '/delete',
                type: 'POST',
                data: args.data,

                success: function success(res) {

                    self.$store.commit('timeTracker/afterDeletedOtherTime', {
                        time_id: args.data.time_id,
                        user_id: args.user_id,
                        res: res
                    });

                    // Display a success toast, with a title
                    pm.Toastr.success(res.message);

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                },
                error: function error(res) {

                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        canAddTime: function canAddTime(actionData) {
            if (actionData.task.status == '1') {
                return false;
            }
            var user_id = this.current_user.ID;
            var users_id = actionData.task.assignees.data.map(function (user) {
                return user.id;
            });
            return users_id.indexOf(user_id) !== -1;
        },
        canDeleteTime: function canDeleteTime(time) {
            if (this.isArchivedTaskListComputed) {
                return false;
            }
            if (this.actionData.task.status == '1') {
                return false;
            }
            return parseInt(time.created_by) == this.current_user.ID;
        },
        getUsers: function getUsers(callback) {
            var hasUsers = this.$store.state.timeTracker.users;

            if (hasUsers.length) {
                return;
            }

            var self = this;
            self.fetchReport = false;
            var args = {
                url: self.base_url + 'pm/v2/user-all-projects',
                success: function success(res) {
                    self.$store.commit('timeTracker/setUsers', res);
                    if (typeof callback != 'undefined') {
                        callback(res);
                    }
                }
            };
            self.httpRequest(args);
        },
        getReportResults: function getReportResults(args) {
            var self = this,
                pre_define = {
                conditions: {},
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var conditions = this.generateConditions(args.conditions);

            var request = {
                type: 'GET',
                url: args.url + conditions,
                success: function success(res) {
                    self.$store.commit('timeTracker/setReports', res);

                    if (typeof args.callback != 'undefined') {
                        args.callback(res);
                    }
                    pm.NProgress.done();
                }
            };

            this.httpRequest(request);
        },
        getTimeNumericValue: function getTimeNumericValue(time) {
            var minute = 0.5 * time.minute / 30;
            var count = time.hour + minute;

            return count.toFixed(2);
        }
    }
};

/***/ }),
/* 4 */,
/* 5 */
/***/ (function(module, exports) {

// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
var global = module.exports = typeof window != 'undefined' && window.Math == Math
  ? window : typeof self != 'undefined' && self.Math == Math ? self
  // eslint-disable-next-line no-new-func
  : Function('return this')();
if (typeof __g == 'number') __g = global; // eslint-disable-line no-undef


/***/ }),
/* 6 */
/***/ (function(module, exports) {

var core = module.exports = { version: '2.6.11' };
if (typeof __e == 'number') __e = core; // eslint-disable-line no-undef


/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

var anObject = __webpack_require__(10);
var IE8_DOM_DEFINE = __webpack_require__(99);
var toPrimitive = __webpack_require__(100);
var dP = Object.defineProperty;

exports.f = __webpack_require__(8) ? Object.defineProperty : function defineProperty(O, P, Attributes) {
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
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

// Thank's IE8 for his funny defineProperty
module.exports = !__webpack_require__(32)(function () {
  return Object.defineProperty({}, 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

var dP = __webpack_require__(7);
var createDesc = __webpack_require__(15);
module.exports = __webpack_require__(8) ? function (object, key, value) {
  return dP.f(object, key, createDesc(1, value));
} : function (object, key, value) {
  object[key] = value;
  return object;
};


/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(14);
module.exports = function (it) {
  if (!isObject(it)) throw TypeError(it + ' is not an object!');
  return it;
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
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Required jQuery methods
 *
 * @type Object
 */
var PM_Projects = {
    chart: function chart(el, binding, vnode) {
        var activity = vnode.context.__('Activity', 'wedevs-project-manager'),
            Task = vnode.context.__('Task', 'wedevs-project-manager');
        var data = {
            labels: ["Oct 05", "Oct 09", "Oct 15"],
            datasets: [{
                label: activity,
                fillColor: "rgba(120,200, 223, 0.4)",
                strokeColor: "#79C7DF",
                pointColor: "#79C7DF",
                pointStrokeColor: "#79C7DF",
                pointHighlightFill: "#79C7DF",
                pointHighlightStroke: "#79C7DF",
                data: PM_Projects.getActivities(vnode.context),
                backgroundColor: "rgba(120,200, 223, 0.4)"
            }, {
                label: Task,
                fillColor: "rgba(185, 114, 182,0.5)",
                strokeColor: "#B972B6",
                pointColor: "#B972B6",
                pointStrokeColor: "#B972B6",
                pointHighlightFill: "#B972B6",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: PM_Projects.getTasks(vnode.context),
                backgroundColor: "rgba(185, 114, 182,0.5)"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'line',
            data: data,
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [],
            graph_data = self.graph;

        graph_data.map(function (graph) {
            var date = PM_Projects.labelDateFormat(graph.date_time.date);
            labels.push(date);
        });

        return labels;
    },

    labelDateFormat: function labelDateFormat(date) {
        date = new Date(date);
        return pm.Moment(date).format('MMM DD');
    },

    getActivities: function getActivities(self) {
        // var activities = self.graph;
        // var set_activities = [];

        // activities.map(function(activity) {
        //     set_activities.push(activity.activities);
        // });

        return [12, 15];
    },

    getTasks: function getTasks(self) {
        // var tasks = self.graph;
        // var set_tasks = [];

        // tasks.map(function(task) {
        //     set_tasks.push(task.tasks);
        // });

        return [4, 5];
    }

    // Register a global custom directive called v-pm-sortable
};pm.Vue.directive('pm-projects-chart', {
    inserted: function inserted(el, binding, vnode) {
        PM_Projects.chart(el, binding, vnode);
    },
    update: function update(el, binding, vnode) {
        PM_Projects.chart(el, binding, vnode);
    }
});

/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

var global = __webpack_require__(5);
var core = __webpack_require__(6);
var ctx = __webpack_require__(31);
var hide = __webpack_require__(9);
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
/* 14 */
/***/ (function(module, exports) {

module.exports = function (it) {
  return typeof it === 'object' ? it !== null : typeof it === 'function';
};


/***/ }),
/* 15 */
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
/* 16 */,
/* 17 */,
/* 18 */,
/* 19 */,
/* 20 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__report_form_vue__ = __webpack_require__(61);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__projects_task_estimation_vue__ = __webpack_require__(65);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__projects_subtask_estimation_vue__ = __webpack_require__(69);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__user_projects_task_estimation_vue__ = __webpack_require__(71);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__user_projects_subtask_estimation_vue__ = __webpack_require__(75);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__lists_task_estimation_vue__ = __webpack_require__(77);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__lists_subtask_estimation_vue__ = __webpack_require__(81);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__user_reports_task_estimation_vue__ = __webpack_require__(83);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__project_by_user_graph_vue__ = __webpack_require__(87);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__user_by_project_graph_vue__ = __webpack_require__(91);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_10__sub_task_type_summary_vue__ = __webpack_require__(166);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_11__task_type_summary_vue__ = __webpack_require__(170);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__helpers_mixin_mixin_js__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__helpers_mixin_mixin_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_12__helpers_mixin_mixin_js__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    mixins: [__WEBPACK_IMPORTED_MODULE_12__helpers_mixin_mixin_js___default.a],
    data: function data() {
        return {
            form: {
                status: false
            },
            results: {
                type: ''
            },
            totalWorkingHours: 0,
            totalEstimationHours: 0,
            dateStart: '',
            dateEnd: '',
            showDropDownMenu: false,
            hasLoadingEffect: false
        };
    },

    computed: {
        getReports: function getReports() {
            var reports = this.$store.state.timeTracker.reports;

            // if(typeof reports.projects != 'undefined') {
            //     var workingHours = 0;
            //     var estimationHours = 0;

            //     jQuery.each(reports.projects, function(key, val) {

            //         workingHours = workingHours+parseInt(val.working_time);
            //         estimationHours = estimationHours + parseInt(val.task_estimation);
            //     });

            //     this.totalWorkingHours = workingHours;
            //     this.totalEstimationHours = estimationHours;
            //     this.hasDateBetween();
            // }

            return reports;
        }
    },
    components: {
        'report-form': __WEBPACK_IMPORTED_MODULE_0__report_form_vue__["a" /* default */],
        'projects-task-estimation': __WEBPACK_IMPORTED_MODULE_1__projects_task_estimation_vue__["a" /* default */],
        'projects-subtask-estimation': __WEBPACK_IMPORTED_MODULE_2__projects_subtask_estimation_vue__["a" /* default */],
        'user-projects-task-estimation': __WEBPACK_IMPORTED_MODULE_3__user_projects_task_estimation_vue__["a" /* default */],
        'user-projects-subtask-estimation': __WEBPACK_IMPORTED_MODULE_4__user_projects_subtask_estimation_vue__["a" /* default */],
        'lists-task-estimation': __WEBPACK_IMPORTED_MODULE_5__lists_task_estimation_vue__["a" /* default */],
        'lists-subtask-estimation': __WEBPACK_IMPORTED_MODULE_6__lists_subtask_estimation_vue__["a" /* default */],
        'user-reports-task-estimation': __WEBPACK_IMPORTED_MODULE_7__user_reports_task_estimation_vue__["a" /* default */],
        'project-report-by-users': __WEBPACK_IMPORTED_MODULE_8__project_by_user_graph_vue__["a" /* default */],
        'user-report-by-projects': __WEBPACK_IMPORTED_MODULE_9__user_by_project_graph_vue__["a" /* default */],
        'sub-task-type-summary': __WEBPACK_IMPORTED_MODULE_10__sub_task_type_summary_vue__["a" /* default */],
        'task-type-summary': __WEBPACK_IMPORTED_MODULE_11__task_type_summary_vue__["a" /* default */]
    },

    created: function created() {
        this.defaultReports();
        this.openFilterForm();
    },


    methods: {
        openFilterForm: function openFilterForm() {
            if (this.$route.query.report_query != 'yes') {
                this.form.status = true;
            }
        },
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        },
        hasDateBetween: function hasDateBetween() {
            var query = this.$route.query;
            var startDate = false;
            var endDate = false;

            if (typeof query.startDate != 'undefined') {
                this.dateStart = query.startDate;

                startDate = true;
            }

            if (typeof query.startDate != 'undefined') {
                this.dateEnd = query.endDate;
                endDate = true;
            }

            if (startDate && endDate) {
                return true;
            }

            return false;
        },
        defaultReports: function defaultReports() {
            if (this.$route.query.report_query == 'undefined') {
                return;
            }

            if (this.$route.query.report_query != 'yes') {
                return;
            }

            var self = this;
            self.fetchReport = false;
            var args = {
                url: self.base_url + 'pm-pro/v2/report-summary?',
                conditions: this.$route.query,
                callback: function callback(res) {
                    self.hasLoadingEffect = false;
                }
            };
            this.hasLoadingEffect = true;
            self.getReportResults(args);
        },
        getResults: function getResults(args) {
            var self = this,
                pre_define = {
                conditions: {},
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var conditions = this.generateConditions(args.conditions);

            var request = {
                type: 'GET',
                url: args.url + conditions,
                success: function success(res) {
                    self.$store.commit('timeTracker/setReports', res);
                    pm.NProgress.done();
                    self.hasLoadingEffect = false;
                }
            };
            this.hasLoadingEffect = true;
            this.httpRequest(request);
        },

        // dropdown trigger
        dropdownTrigger: function dropdownTrigger() {
            this.showDropDownMenu = this.showDropDownMenu ? false : true;
        },


        // dropdown class toggler
        dropdownToggleClass: function dropdownToggleClass() {
            if (this.showDropDownMenu) {
                return "pm-dropdown-menu pm-dropdown-open mt-10";
            } else {
                return "pm-settings pm-dropdown-menu";
            }
        },
        exportCSV: function exportCSV() {
            var args = {
                url: this.base_url + 'pm-pro/v2/report-summary/csv?',
                conditions: this.$route.query,
                callback: function callback(res) {}
            };

            this.downloadCSV(args);
        },
        downloadCSV: function downloadCSV(args) {
            var self = this,
                pre_define = {
                data: {
                    currentUser: PM_Vars.current_user.ID
                },
                callbakc: false
            };
            args.conditions = jQuery.extend(true, pre_define.data, args.conditions);

            var conditions = this.generateConditions(args.conditions);

            window.location.href = args.url + conditions;
        }
    },

    destroyed: function destroyed() {
        this.$store.commit('timeTracker/setReports', {
            data: {
                type: ''
            }
        });
    }
});

/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin_js__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin_js__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin_js___default.a],
    props: {
        formVisibility: {
            type: [Object],
            default: function _default() {
                return {};
            }
        },
        results: {
            type: [Object],
            default: function _default() {
                return {};
            }
        }
    },
    data: function data() {
        return {
            form: {
                summaryType: 'all_project',
                typeUsers: '',
                estimated_time: 'task_estimation',
                projects: [],
                type: 'summary',
                users: '',
                startDate: this.startMonth(),
                endDate: this.endMonth()
            },
            isActive: false,
            processSubmit: false
        };
    },

    watch: {
        '$route': function $route(route) {
            if (route.query.report_query == 'yes') {
                //this.getReports();
            }
        }
    },
    created: function created() {
        var self = this;
        this.isActive = this.formVisibility.status;

        this.getUsers(function (res) {
            self.setDefaultFormValu();
        });

        this.setDefaultFormValu();
    },

    computed: {
        project_users: function project_users() {
            return this.$store.state.timeTracker.users;
        }
    },
    components: {
        'multiselect': pm.Multiselect.Multiselect
    },
    methods: {
        changeProject: function changeProject(projects) {
            this.form.projects = projects.map(function (project) {
                return project.id;
            });
        },
        setDefaultFormValu: function setDefaultFormValu() {
            if (this.$route.query.report_query != 'yes') {
                return;
            }

            this.form = this.$route.query;

            var user = Array.isArray(this.$route.query.users) ? this.$route.query.users[0] : this.$route.query.users;
            var users = this.$store.state.timeTracker.users;

            var index = this.getIndex(users, user, 'user_id');

            if (index !== false) {
                this.form.users = users[index];
            }
        },
        buttonClass: function buttonClass() {
            return this.processSubmit ? 'process-data button button-primary pm-doc-btn' : 'button button-primary pm-doc-btn';
        },
        startMonth: function startMonth() {
            return pm.Moment().subtract(1, 'months').format('YYYY-MM-01');
        },
        endMonth: function endMonth() {
            return pm.Moment().date(0).format('YYYY-MM-DD');
        },
        getReports: function getReports() {
            var self = this;
            self.fetchReport = false;
            var args = {
                url: self.base_url + 'pm-pro/v2/report-summary?',
                conditions: this.$route.query,
                callback: function callback(res) {
                    self.formVisibility.status = false;
                }
            };
            this.processSubmit = true;
            self.getReportResults(args);
        },
        closeModal: function closeModal() {
            this.isActive = this.formVisibility.status = false;
        },
        submit: function submit() {
            var query = {};

            jQuery.each(this.form, function (key, val) {
                query[key] = val;
            });
            query['users'] = this.getQueryUsers();
            query['report_query'] = 'yes';

            this.$router.push({
                name: 'report_summary',
                query: {}
            });
            this.$router.push({
                name: 'report_summary',
                query: query
            });

            this.getReports();
        },
        getQueryUsers: function getQueryUsers() {
            if (typeof this.form.users == 'undefined') {
                return '';
            }

            if (!this.form.users) {
                return '';
            }
            return [this.form.users.user_id];
        }
    }
});

/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/**
 * Required jQuery methods
 *
 * @type Object
 */
var projectTaskEstimation = {
    chart: function chart(el, binding, vnode) {
        var data = {
            labels: projectTaskEstimation.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: projectTaskEstimation.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: vnode.context.reports.report_for == 'sub_task' ? __('Subtask Count', 'pm-pro') : __('Task Count', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: projectTaskEstimation.getCount(vnode.context),
                backgroundColor: "#4bc0c0"
            }, {
                label: __('Users', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#36a2ec",
                strokeColor: "#36a2ec",
                pointColor: "#36a2ec",
                pointStrokeColor: "#36a2ec",
                pointHighlightFill: "#36a2ec",
                pointHighlightStroke: "#36a2ec",
                data: projectTaskEstimation.getUserCount(vnode.context),
                backgroundColor: "#36a2ec"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            min: 0
                            //stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: __('Hours', 'pm-pro')
                        }
                    }]
                },
                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];

        jQuery.each(self.reports.projects.data, function (userId, project) {
            labels.push(project.title);
        });

        return labels;
    },
    getCount: function getCount(self) {
        var count = [];

        jQuery.each(self.reports.projects.data, function (userId, project) {
            if (self.reports.report_for == 'sub_task') {
                count.push(project.total_incomplete_sub_tasks);
            } else {
                count.push(project.total_incomplete_tasks);
            }
        });

        return count;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];

        jQuery.each(self.reports.projects.data, function (userId, project) {
            if (self.reports.report_for == 'sub_task') {
                estimatedHours.push(project.sub_task_estimation_hours);
            } else {
                estimatedHours.push(project.task_estimation_hours);
            }
        });

        return estimatedHours;
    },
    getUserCount: function getUserCount(self) {
        var userCount = [];

        jQuery.each(self.reports.projects.data, function (userId, project) {
            userCount.push(project.total_users);
        });

        return userCount;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('project-task-estimation', {
    inserted: function inserted(el, binding, vnode) {
        projectTaskEstimation.chart(el, binding, vnode);
    }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            grapVisibility: true
        };
    },


    watch: {
        reports: {
            handler: function handler(recent, old) {
                var _this = this;

                this.grapVisibility = false;

                setTimeout(function () {
                    _this.grapVisibility = true;
                }, 100);
            },


            deep: true
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 23 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        reports: {
            type: [Array],
            default: function _default() {
                return {};
            }
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 24 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/**
 * Required jQuery methods
 *
 * @type Object
 */
var userProjectsTaskEstimation = {
    chart: function chart(el, binding, vnode) {

        var data = {
            labels: userProjectsTaskEstimation.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: userProjectsTaskEstimation.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: vnode.context.reports.report_for == 'sub_task' ? __('Completed Subtask', 'pm-pro') : __('Completed Task', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: userProjectsTaskEstimation.getcompletedTasks(vnode.context),
                backgroundColor: "#4bc0c0"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            min: 0
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Hours'
                        }
                    }]
                },

                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];
        var reports = self.reports.report_for == 'sub_task' ? self.reports.sub_task : self.reports.task;

        jQuery.each(reports.data, function (userId, report) {
            labels.push(report.user.display_name);
        });

        return labels;
    },
    getcompletedTasks: function getcompletedTasks(self) {
        var completedTasks = [];
        var reports = self.reports.report_for == 'sub_task' ? self.reports.sub_task : self.reports.task;

        jQuery.each(reports.data, function (userId, report) {
            completedTasks.push(report.in_task_count);
        });

        return completedTasks;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];
        var reports = self.reports.report_for == 'sub_task' ? self.reports.sub_task : self.reports.task;

        jQuery.each(reports.data, function (key, report) {
            estimatedHours.push(report.estimation_hours);
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('user-projects-estimation', {

    inserted: function inserted(el, binding, vnode) {
        userProjectsTaskEstimation.chart(el, binding, vnode);
    }
    // update: function (el, binding, vnode) {
    //     userProjectsTaskEstimation.chart(el, binding, vnode);
    // }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            grapVisibility: true
        };
    },


    watch: {
        reports: {
            handler: function handler(recent, old) {
                var _this = this;

                this.grapVisibility = false;

                setTimeout(function () {
                    _this.grapVisibility = true;
                }, 100);
            },


            deep: true
        }
    },

    created: function created() {},


    methods: {
        getReports: function getReports(reports) {
            if (reports.report_for == 'sub_task') {
                return reports.sub_task.data;
            }

            return reports.task.data;
        },
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 25 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        reports: {
            type: [Array],
            default: function _default() {
                return {};
            }
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/**
 * Required jQuery methods
 *
 * @type Object
 */
var projectListTaskEstimation = {
    chart: function chart(el, binding, vnode) {
        var activity = vnode.context.__('Activity', 'wedevs-project-manager'),
            Task = vnode.context.__('Task', 'wedevs-project-manager');
        var data = {
            labels: projectListTaskEstimation.getLabels(vnode.context),
            datasets: [{
                label: __('Work Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "rgba(120,200, 223, 0.4)",
                strokeColor: "#79C7DF",
                pointColor: "#79C7DF",
                pointStrokeColor: "#79C7DF",
                pointHighlightFill: "#79C7DF",
                pointHighlightStroke: "#79C7DF",
                data: projectListTaskEstimation.getWorkHours(vnode.context),
                backgroundColor: "rgba(120,200, 223, 0.4)"
            }, {
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "rgba(185, 114, 182,0.5)",
                strokeColor: "#B972B6",
                pointColor: "#B972B6",
                pointStrokeColor: "#B972B6",
                pointHighlightFill: "#B972B6",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: projectListTaskEstimation.getEstimatedHours(vnode.context),
                backgroundColor: "rgba(185, 114, 182,0.5)"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            min: 0,
                            stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Hours'
                        }
                    }]
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];
        self.reports.forEach(function (report) {
            labels.push(report.list_title);
        });

        return labels;
    },
    getWorkHours: function getWorkHours(self) {
        var workHours = [];
        self.reports.forEach(function (report) {
            var time = self.secondsToHms(report.working_time);
            workHours.push(self.getTimeNumericValue(time));
        });

        return workHours;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];
        self.reports.forEach(function (report) {
            var time = self.secondsToHms(parseInt(report.task_estimation) * 60);

            estimatedHours.push(self.getTimeNumericValue(time));
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('project-list-task-estimation', {
    inserted: function inserted(el, binding, vnode) {
        projectListTaskEstimation.chart(el, binding, vnode);
    },
    update: function update(el, binding, vnode) {
        projectListTaskEstimation.chart(el, binding, vnode);
    }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 27 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        reports: {
            type: [Array],
            default: function _default() {
                return {};
            }
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty__ = __webpack_require__(94);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__);

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/**
 * Required jQuery methods
 *
 * @type Object
 */
var userReportsProjectsEstimation = {
    chart: function chart(el, binding, vnode) {
        var data = {
            labels: userReportsProjectsEstimation.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: userReportsProjectsEstimation.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: __('Completed Tasks', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: userReportsProjectsEstimation.getCompletedTasks(vnode.context),
                backgroundColor: "#4bc0c0"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    yAxes: [{

                        ticks: {
                            min: 0
                            //stepSize: userReportsProjectsEstimation.getStepSize(vnode.context)
                        },
                        scaleLabel: {
                            display: true,
                            labelString: ''
                        }
                    }],

                    xAxes: [{
                        // Change here
                        barPercentage: 0.5
                    }]
                },

                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];
        jQuery.each(self.reports.projects.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                labels.push(data.project.title);
            });
        });

        return labels;
    },
    getCompletedTasks: function getCompletedTasks(self) {
        var completedTasks = [];

        jQuery.each(self.reports.projects.data, function (key, report) {

            jQuery.each(report.data, function (key2, data) {

                if (report.meta.report_for == 'tasks') {
                    completedTasks.push(data.completed_tasks);
                } else {
                    completedTasks.push(data.completed_sub_tasks);
                }
            });
        });

        return completedTasks;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];

        jQuery.each(self.reports.projects.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                if (report.meta.report_for == 'tasks') {
                    estimatedHours.push(data.estimated_task_hours);
                } else {
                    estimatedHours.push(data.estimated_sub_task_hours);
                }
            });
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('user-report-projects-estimation', {
    inserted: function inserted(el, binding, vnode) {
        userReportsProjectsEstimation.chart(el, binding, vnode);
    }

    // update: function (el, binding, vnode) {
    //     userReportsProjectsEstimation.chart(el, binding, vnode);
    // }
});

/**
* Required jQuery methods
*
* @type Object
*/
var userReportTaskTypeEstimation = {
    chart: function chart(el, binding, vnode) {
        var data = {
            labels: userReportTaskTypeEstimation.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: userReportTaskTypeEstimation.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: __('Completed Tasks', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: userReportTaskTypeEstimation.getCompletedTasks(vnode.context),
                backgroundColor: "#4bc0c0"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            min: 0
                            //stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: ''
                        }
                    }],

                    xAxes: [{
                        // Change here
                        barPercentage: 0.5
                    }]
                },

                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];

        jQuery.each(self.reports.task_types.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                labels.push(data.type.title);
            });
        });

        return labels;
    },
    getCompletedTasks: function getCompletedTasks(self) {
        var completedTasks = [];

        jQuery.each(self.reports.task_types.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                completedTasks.push(data.completed);
            });
        });

        return completedTasks;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];

        jQuery.each(self.reports.task_types.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                estimatedHours.push(data.estimated_hours);
            });
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('user-report-task-type-estimation', {
    inserted: function inserted(el, binding, vnode) {
        userReportTaskTypeEstimation.chart(el, binding, vnode);
    }

    // update: function (el, binding, vnode) {
    //     userReportTaskTypeEstimation.chart(el, binding, vnode);
    // }
});

/**
 * Required jQuery methods
 *
 * @type Object
 */
var userReportSubTaskTypeEstimation = {
    chart: function chart(el, binding, vnode) {
        var data = {
            labels: userReportSubTaskTypeEstimation.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: userReportSubTaskTypeEstimation.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: __('Completed Tasks', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: userReportSubTaskTypeEstimation.getCompletedTasks(vnode.context),
                backgroundColor: "#4bc0c0"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                scales: __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty___default()({
                    xAxes: [{
                        ticks: {
                            min: 0
                            //stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: ''
                        }
                    }]

                }, 'xAxes', [{
                    // Change here
                    barPercentage: 0.5
                }]),
                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];

        jQuery.each(self.reports.sub_task_types.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                labels.push(data.type.title);
            });
        });

        return labels;
    },
    getCompletedTasks: function getCompletedTasks(self) {
        var completedTasks = [];

        jQuery.each(self.reports.sub_task_types.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                completedTasks.push(data.completed);
            });
        });

        return completedTasks;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];

        jQuery.each(self.reports.sub_task_types.data, function (key, report) {
            jQuery.each(report.data, function (key2, data) {
                estimatedHours.push(data.estimated_hours);
            });
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('user-report-sub-task-type-estimation', {
    inserted: function inserted(el, binding, vnode) {
        userReportSubTaskTypeEstimation.chart(el, binding, vnode);
    }

    // update: function (el, binding, vnode) {
    //     userReportSubTaskTypeEstimation.chart(el, binding, vnode);
    // }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            grapVisibility: true
        };
    },


    watch: {
        reports: {
            handler: function handler(recent, old) {
                var _this = this;

                this.grapVisibility = false;

                setTimeout(function () {
                    _this.grapVisibility = true;
                }, 100);
            },


            deep: true
        }
    },

    created: function created() {
        this.getUsers();
    },


    computed: {
        users: function users() {
            return this.$store.state.timeTracker.users;
        }
    },

    methods: {
        hasResults: function hasResults() {
            return false;
        },
        hasLength: function hasLength(obj) {
            if (jQuery.isEmptyObject(obj)) {
                return false;
            }
            return true;
        },
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        },
        timeGenerate: function timeGenerate(h, m) {
            var seconds = parseInt(h) * 3600 + parseInt(m) * 60;
            var time = this.secondsToHms(seconds);

            return time.hour + ':' + time.minute + ':' + time.second;
        },
        getUserName: function getUserName(user_id) {
            user_id = String(user_id);
            var index = this.getIndex(this.users, user_id, 'user_id');

            if (index === false) {
                return '';
            }
            return this.users[index].display_name;
        }
    }
});

/***/ }),
/* 29 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__directive_js__ = __webpack_require__(12);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__directive_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__directive_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




var projectByUsersDemo = {
    chart: function chart(el, binding, vnode) {

        var data = {
            labels: ['Admin', 'Kiron kiron', 'nayem nayem'],
            datasets: [{
                label: 'Nadim',
                backgroundColor: 'red',
                data: [30, 20, 10]
            }, {
                label: 'Sourov',
                backgroundColor: 'green',
                data: [10, 20, 0]
            }, {
                label: 'Mishu',
                backgroundColor: 'blue',
                data: [16, 20, 90]
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");

        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                title: {
                    display: true,
                    text: 'Chart.js Bar Chart - Stacked'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    }

    // Register a global custom directive called v-pm-sortable
    // pm.Vue.directive('project-by-users-demo', {
    //     inserted: function (el, binding, vnode) {
    //         projectByUsersDemo.chart(el, binding, vnode);
    //     },
    //     update: function (el, binding, vnode) {
    //         projectByUsersDemo.chart(el, binding, vnode);
    //     }
    // });


    /**
     * Required jQuery methods
     *
     * @type Object
     */
};var projectByUsers = {
    preSetLabels: [],
    chart: function chart(el, binding, vnode) {
        var data = {
            labels: projectByUsers.getLabels(vnode.context),
            datasets: projectByUsers.getUserTimes(vnode.context)
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                title: {
                    display: true,
                    text: 'Projects'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true,
                        scaleLabel: {
                            fontStyle: "bold",
                            display: true,
                            labelString: __('Hours', 'pm-pro')
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        scaleLabel: {
                            fontStyle: "bold",
                            display: true,
                            labelString: __('Users', 'pm-pro')
                        }
                    }]
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];
        var allUsers = {};

        self.reports.forEach(function (report) {

            jQuery.each(report.users, function (key, user) {
                allUsers[user.ID] = user;
            });
        });

        jQuery.each(allUsers, function (userId, user) {
            labels.push(user.display_name);

            projectByUsers.preSetLabels.push({
                'user_id': user.ID,
                'display_name': user.display_name
            });
        });
        return labels;
    },

    //let time = self.secondsToHms(report.working_time);
    getUserTimes: function getUserTimes(self) {

        var graphDataset = [];

        self.reports.forEach(function (report) {
            var dataSetObj = {
                label: report.project_title,
                backgroundColor: self.getRandomColor(),
                data: []
            };

            projectByUsers.preSetLabels.forEach(function (user) {
                if (typeof report.users[user.user_id] == 'undefined') {
                    dataSetObj.data.push(0);
                } else {
                    var time = self.secondsToHms(report.users[user.user_id].w_seconds);
                    dataSetObj.data.push(self.getTimeNumericValue(time));
                }
            });

            graphDataset.push(dataSetObj);
        });

        return graphDataset;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('project-by-users', {
    inserted: function inserted(el, binding, vnode) {
        projectByUsers.chart(el, binding, vnode);
    },
    update: function update(el, binding, vnode) {
        projectByUsers.chart(el, binding, vnode);
    }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    created: function created() {},


    methods: {
        getRandomColor: function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        },
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 30 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty__ = __webpack_require__(94);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__directive_js__ = __webpack_require__(12);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__directive_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__directive_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin__ = __webpack_require__(3);
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




var projectByUsersDemo = {
    chart: function chart(el, binding, vnode) {

        var data = {
            labels: ['Admin', 'Kiron kiron', 'nayem nayem'],
            datasets: [{
                label: 'Nadim',
                backgroundColor: 'red',
                data: [30, 20, 10]
            }, {
                label: 'Sourov',
                backgroundColor: 'green',
                data: [10, 20, 0]
            }, {
                label: 'Mishu',
                backgroundColor: 'blue',
                data: [16, 20, 90]
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");

        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                title: {
                    display: true,
                    text: 'Chart.js Bar Chart - Stacked'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    }

    // Register a global custom directive called v-pm-sortable
};pm.Vue.directive('project-by-users-demo', {
    inserted: function inserted(el, binding, vnode) {
        projectByUsersDemo.chart(el, binding, vnode);
    },
    update: function update(el, binding, vnode) {
        projectByUsersDemo.chart(el, binding, vnode);
    }
});

/**
 * Required jQuery methods
 *
 * @type Object
 */
var userByProjects = {
    preSetLabels: [],
    chart: function chart(el, binding, vnode) {
        var _ref;

        var data = {
            labels: userByProjects.getLabels(vnode.context),
            datasets: userByProjects.getUserTimes(vnode.context)
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, (_ref = {
            type: 'horizontalBar',
            data: data,
            tooltipTemplate: "<%= value %>",
            options: {
                title: {
                    display: true,
                    text: 'Users'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true,
                        scaleLabel: {
                            fontStyle: "bold",
                            display: true,
                            labelString: __('Hours', 'pm-pro')
                        }
                    }],
                    yAxes: [{
                        stacked: true,
                        scaleLabel: {
                            fontStyle: "bold",
                            display: true,
                            labelString: __('Projects', 'pm-pro')
                        }
                    }]
                }
            },
            pointDotRadius: 8,
            animationSteps: 60
        }, __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty___default()(_ref, 'tooltipTemplate', "<%= labels + sss %>%"), __WEBPACK_IMPORTED_MODULE_0_babel_runtime_helpers_defineProperty___default()(_ref, 'animationEasing', "easeOutQuart"), _ref));
    },

    getLabels: function getLabels(self) {
        var labels = [];
        var allProjects = {};

        self.reports.forEach(function (report) {

            jQuery.each(report.projects, function (key, project) {
                allProjects[project.id] = project;
            });
        });

        jQuery.each(allProjects, function (userId, project) {
            labels.push(project.title);

            userByProjects.preSetLabels.push({
                'project_id': project.id,
                'title': project.title
            });
        });
        return labels;
    },

    //let time = self.secondsToHms(report.working_time);
    getUserTimes: function getUserTimes(self) {

        var graphDataset = [];

        self.reports.forEach(function (report) {
            var dataSetObj = {
                label: report.display_name,
                backgroundColor: self.getRandomColor(),
                data: []
            };

            userByProjects.preSetLabels.forEach(function (project) {
                if (typeof report.projects[project.project_id] == 'undefined') {
                    dataSetObj.data.push(0);
                } else {
                    var time = self.secondsToHms(report.projects[project.project_id].w_seconds);
                    dataSetObj.data.push(self.getTimeNumericValue(time));
                }
            });

            graphDataset.push(dataSetObj);
        });

        return graphDataset;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('user-by-projects', {
    inserted: function inserted(el, binding, vnode) {
        userByProjects.chart(el, binding, vnode);
    },
    update: function update(el, binding, vnode) {
        userByProjects.chart(el, binding, vnode);
    }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    created: function created() {},


    methods: {
        getRandomColor: function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        },
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

// optional / simple context binding
var aFunction = __webpack_require__(98);
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
/* 32 */
/***/ (function(module, exports) {

module.exports = function (exec) {
  try {
    return !!exec();
  } catch (e) {
    return true;
  }
};


/***/ }),
/* 33 */
/***/ (function(module, exports, __webpack_require__) {

var isObject = __webpack_require__(14);
var document = __webpack_require__(5).document;
// typeof document.createElement is 'object' in old IE
var is = isObject(document) && isObject(document.createElement);
module.exports = function (it) {
  return is ? document.createElement(it) : {};
};


/***/ }),
/* 34 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__custom_time_log_form_vue__ = __webpack_require__(35);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
																return {};
												}
								}
				},

				data: function data() {
								return {
												interval: '',
												hour: '00',
												minute: '00',
												second: '00',
												isRunningStopWatch: false,
												action: {
																showMenu: false,
																customTimeForm: false
												},
												timeActivityRunning: false,
												RequestIsRunning: false
								};
				},
				created: function created() {
								this.defaultRuningTask(this.actionData);
								window.addEventListener('click', this.windowActivity);
								pmBus.$on('pm_after_task_doneUndone', this.doneUndoneTask);
				},


				components: {
								'custom-time-form': __WEBPACK_IMPORTED_MODULE_1__custom_time_log_form_vue__["a" /* default */]
				},

				watch: {
								// '$route' (to, from) {
								//     this.defaultRuningTask(this.actionData);
								// }
								actionData: {
												handler: function handler() {

																if (this.actionData.task.status === true) {
																				//if(this.actionData.task.time.meta.running) {
																				//this.selfTimeStop(this.actionData);
																				//}
																} else {
																				this.defaultRuningTask(this.actionData);
																}
												},


												deep: true
								}
				},

				mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],

				computed: {
								stopWatch: function stopWatch() {

												var task = this.actionData.task;
												if (typeof task == 'undefined') {
																return false;
												}

												return task.is_stop_watch_visible;
								},
								trHour: function trHour() {
												var task = this.actionData.task;
												if (typeof task == 'undefined') {
																return 0;
												}

												return task.time.meta.totalTime.hour;
								},
								trMinute: function trMinute() {
												var task = this.actionData.task;
												if (typeof task == 'undefined') {
																return 0;
												}
												return task.time.meta.totalTime.minute;
								},
								trSecond: function trSecond() {
												var task = this.actionData.task;
												if (typeof task == 'undefined') {
																return 0;
												}
												return task.time.meta.totalTime.second;
								},
								timeRunning: function timeRunning() {
												var task = this.actionData.task;
												if (typeof task == 'undefined') {
																return false;
												}
												return task.time.meta.running;
								},
								can_start_time: function can_start_time() {
												var user_id = this.actionData.task.assignees.data.map(function (user) {
																return user.id;
												});
												return user_id.indexOf(PM_Vars.current_user.ID) !== -1;
								},
								isArchivedTaskListComputed: function isArchivedTaskListComputed() {
												return this.isArchivedTaskList(this.actionData.task);
								}
				},
				methods: {
								customTimePopUpClass: function customTimePopUpClass(timeActivityRunning, timeRunning) {
												return timeActivityRunning || timeRunning ? 'custom-time-popup-menu' : 'watch-time-popup';
								},
								runningTimeClass: function runningTimeClass(timeActivityRunning, timeRunning) {
												return timeActivityRunning || timeRunning ? 'pm-popup-menu-running' : '';
								},
								showHideTimeForm: function showHideTimeForm(status) {
												if (status) {
																this.action.showMenu = false;
																this.action.customTimeForm = true;
												}
								},
								windowActivity: function windowActivity(el) {
												var menuPopup = jQuery(el.target).closest('.pm-pro-task-inline'),
												    datePicker = jQuery(el.target).closest('#ui-datepicker-div'),
												    datePickerBtn = jQuery(el.target).closest('.ui-datepicker-buttonpane');

												if (datePicker.length || datePickerBtn.length) {
																return;
												}

												if (!menuPopup.length) {
																this.action.showMenu = false;
												}
								},
								showTimeTrackMenu: function showTimeTrackMenu() {
												if (this.action.customTimeForm) {
																this.action.showMenu = false;
												} else {
																this.action.showMenu = this.action.showMenu ? false : true;
												}

												this.action.customTimeForm = false;
								},
								doneUndoneTask: function doneUndoneTask(data) {
												var self = this;
												pm.Vue.nextTick(function () {
																clearTimeout(self.interval);

																var task = self.actionData.task;
																if (typeof task != 'undefined') {
																				self.actionData.task.time.meta.running = false;
																}
												});
								},


								runStopwatch: function runStopwatch() {
												var self = this;

												self.interval = setTimeout(function () {
																pm.Vue.nextTick(function () {
																				self.timeOut();
																				clearTimeout(self.interval);
																				self.runStopwatch();
																});
												}, 1000);
								},

								timeOut: function timeOut() {
												var self = this;

												var hour = parseInt(this.actionData.task.time.meta.totalTime.hour);
												var minute = parseInt(this.actionData.task.time.meta.totalTime.minute);
												var second = parseInt(this.actionData.task.time.meta.totalTime.second);

												second++;

												if (second > 59) {
																second = 0;
																minute = minute + 1;
												}

												if (minute > 59) {
																minute = 0;
																hour = hour + 1;
												}

												this.actionData.task.time.meta.totalTime.hour = self.pad2(hour);
												this.actionData.task.time.meta.totalTime.minute = self.pad2(minute);
												this.actionData.task.time.meta.totalTime.second = self.pad2(second);

												this.hour = self.pad2(hour);
												this.minute = self.pad2(minute);
												this.second = self.pad2(second);
								},
								selfTimeStart: function selfTimeStart(actionData) {
												if (this.RequestIsRunning) {
																return;
												}
												var self = this;

												var task = actionData.task;
												var args = {
																data: {
																				project_id: actionData.task.project_id,
																				list_id: actionData.task.task_list_id,
																				task_id: actionData.task.id,
																				hour: self.hour,
																				minute: self.minute,
																				second: self.second,
																				start: true,
																				title: ''
																},

																callback: function callback(res) {
																				self.RequestIsRunning = false;
																				actionData.task.time = res.data.time;
																				self.runStopwatch();
																				self.timeActivityRunning = true;
																				//self.action.showMenu = false;
																}
												};
												window.currentRunningTask = args;
												this.RequestIsRunning = true;
												self.timeStart(args);
								},
								showStopWatch: function showStopWatch(actionData) {
												actionData.task.is_stop_watch_visible = actionData.task.is_stop_watch_visible ? false : true;
								},
								defaultRuningTask: function defaultRuningTask(actionData) {

												var task = actionData.task;

												if (typeof task === 'undefined') {
																return;
												}

												if (actionData.task.time.meta.running) {
																clearTimeout(this.interval);
																//this.isRunningStopWatch = true;
																this.runStopwatch();
												} else {
																var hour = parseInt(this.actionData.task.time.meta.totalTime.hour);
																var minute = parseInt(this.actionData.task.time.meta.totalTime.minute);
																var second = parseInt(this.actionData.task.time.meta.totalTime.second);

																this.hour = this.pad2(hour);
																this.minute = this.pad2(minute);
																this.second = this.pad2(second);
												}
								},
								selfTimeStop: function selfTimeStop(actionData) {
												if (this.RequestIsRunning) {
																return;
												}
												var self = this;
												var task = actionData.task;
												var args = {
																data: {
																				project_id: actionData.task.project_id,
																				list_id: actionData.task.task_list_id,
																				task_id: actionData.task.id
																},

																callback: function callback(res) {
																				self.RequestIsRunning = false;
																				if (!res) {
																								return false;
																				}
																				self.isRunningStopWatch = false;
																				clearTimeout(self.interval);
																				var time_index = self.getIndex(actionData.task.time.data, res.data.id, 'id');

																				actionData.task.time.data[time_index] = res.data;
																				actionData.task.time.meta.totalTime = res.meta.total_time;
																				actionData.task.time.meta.running = false;
																				self.timeActivityRunning = true;
																}
												};
												this.RequestIsRunning = true;
												self.timeStop(args);
								},
								afterStopTime: function afterStopTime(actionData) {},
								isActiveIcon: function isActiveIcon(actionData) {
												if (typeof actionData.list === 'undefined') {
																return false;
												}

												if (typeof actionData.task === 'undefined') {
																return false;
												}

												var users = actionData.task.assignees.data;
												if (users.length) {
																return true;
												} else {
																return false;
												}

												// var listId = actionData.list.id;
												// var taskId = actionData.task.id;
												// var lists = this.$store.state.projectTaskLists.lists;
												// var list_index = this.getIndex(lists, listId, 'id');

												//       if(typeof lists[list_index].incomplete_tasks !== 'undefined' ){
												//           var task_index = this.getIndex(lists[list_index].incomplete_tasks.data, taskId, 'id');

												//           if(task_index !== false) {
												//           	let users = lists[list_index].incomplete_tasks.data[task_index].assignees.data;
												//           	if(users.length) {
												//           		return true;
												//           	} else {
												//           		return false;
												//           	}
												//           }
												//       }

												return false;
								}
				},

				beforeDestroy: function beforeDestroy() {
								clearTimeout(this.interval);
				}
});

/***/ }),
/* 35 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_custom_time_log_form_vue__ = __webpack_require__(36);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_39d5d5b7_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_custom_time_log_form_vue__ = __webpack_require__(108);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(106)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_custom_time_log_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_39d5d5b7_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_custom_time_log_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/custom-time-log-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-39d5d5b7", Component.options)
  } else {
    hotAPI.reload("data-v-39d5d5b7", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 36 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
				return {};
			}
		},
		meta: {
			type: [Object],
			default: function _default() {
				return {};
			}
		}
	},
	mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
	data: function data() {
		return {
			start: pm.Moment().format('YYYY-MM-DD'),
			stop: '',
			add_entry: __('Add Entry', 'pm-pro')
		};
	},


	methods: {
		validation: function validation($ele) {
			var value = $ele.target.value;
			var hasColon = value.indexOf(":");

			if (hasColon == -1) {
				value = value.replace(/[^0-9]/g, '');
				this.stop = value;

				return;
			}

			value = value.replace(/[^:0-9]/g, '');
			this.stop = value;

			var replacePart = ':';
			var lastPart = value.substr(value.indexOf(replacePart) + replacePart.length);

			if (lastPart != '' && lastPart > 59) {
				this.stop = value.slice(0, -1);
			}
		},
		showCustomTimeForm: function showCustomTimeForm() {
			this.meta.customTimeForm = false;
			this.meta.showMenu = true;
		},
		selfNewCustomTime: function selfNewCustomTime(actionData) {
			var self = this;
			var task = actionData.task;

			var args = {
				data: {
					start: this.start,
					stop: this.stop,
					project_id: task.project_id,
					list_id: task.task_list_id,
					task_id: task.id
				},
				run_status: task.time.run_status,
				callback: function callback(res) {
					self.start = '';
					self.stop = '';
					actionData.task.time = res.data.time;
					//self.showCustomLogForm(actionData.list.id, actionData.task.id, actionData, false);
				}
			};

			this.newCustomTime(args);
		}
	}
});

/***/ }),
/* 37 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__custom_time_log_form_vue__ = __webpack_require__(35);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


//import OthersTimeLog from './others-time-log.vue';


/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        actionData: {
            type: Object,
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            timeLogs: [],
            openPrents: [],
            netTime: 0
        };
    },


    mixins: [__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default.a],

    created: function created() {
        this.getSelfOthersTimeLog();
        pmBus.$on('pm_after_stop_time', this.afterStopTime);
        pmBus.$on('pm_after_add_custom_time', this.afterStopTime);
    },


    computed: {
        customTimeForm: function customTimeForm() {
            var task = this.actionData.task;

            if (typeof task == 'undefined') {
                return false;
            }

            return task.custom_time_form;
        }
    },

    components: {
        'custom-time-form': __WEBPACK_IMPORTED_MODULE_0__custom_time_log_form_vue__["a" /* default */]
        //'others-co-worker-time-log': OthersTimeLog
    },

    methods: {
        netTimeGenerator: function netTimeGenerator(timeLogs) {
            var totalSecond = 0;
            timeLogs.forEach(function (timeLog) {
                totalSecond = parseInt(totalSecond) + parseInt(timeLog.total.total_second);
            });

            var timeObj = this.stringToTime(totalSecond);
            return timeObj.hours + ':' + timeObj.minutes + ':' + timeObj.seconds;
        },
        afterStopTime: function afterStopTime() {
            this.afterFetchOthersTime();
        },
        timeLogTd: function timeLogTd(time) {
            return time.class == '' ? 'time-log-child-td' : 'time-log-first-td';
        },
        getHourMinuteSecond: function getHourMinuteSecond(time) {
            var timeObj = this.stringToTime(time.userTotalSecond);
            return timeObj.hours + ':' + timeObj.minutes + ':' + timeObj.seconds;
        },
        addCustomField: function addCustomField(timeLog, index, parentOpenStatus) {
            if (index == '0') {
                //let childVisibility = typeof timeLog.childVisibility == 'undefined' ? false : timeLog.childVisibility;
                pm.Vue.set(timeLog, 'childVisibility', parentOpenStatus);

                pm.Vue.set(timeLog, 'showStatus', true);
                timeLog.class = parentOpenStatus ? 'icon-pm-up-arrow' : 'icon-pm-down-arrow';
            } else {
                //let showStatus = typeof timeLog.showStatus == 'undefined' ? false : timeLog.showStatus;
                pm.Vue.set(timeLog, 'showStatus', parentOpenStatus);
                pm.Vue.set(timeLog, 'isChildren', true);
                timeLog.class = '';
            }
        },
        getParentOpenStatus: function getParentOpenStatus(timeLogs) {

            if (timeLogs.length) {

                var id = timeLogs[0].id;
                var index = this.getIndex(this.openPrents, id, 'id');

                if (index === false) {
                    return false;
                }

                return this.openPrents[index].openStatus;
            }

            return false;
        },
        afterFetchOthersTime: function afterFetchOthersTime() {
            var self = this;

            if (typeof this.actionData.task == 'undefined') {
                return [];
            }
            var totalSecond = 0;
            var lastEle = self.actionData.task.time.data.length - 1;

            var parentOpenStatus = self.getParentOpenStatus(self.actionData.task.time.data);

            self.actionData.task.time.data.forEach(function (timeLog, index) {
                totalSecond = totalSecond + parseInt(timeLog.total.total_second);
                self.addCustomField(timeLog, index, parentOpenStatus);
            });

            if (parseInt(lastEle) > 0) {
                pm.Vue.set(self.actionData.task.time.data[lastEle], 'lastElement', true);
                pm.Vue.set(self.actionData.task.time.data[lastEle], 'userTotalSecond', totalSecond);
            }

            var othersLog = this.$store.state.timeTracker.othersLog;

            othersLog.forEach(function (logs, logsIndex) {
                var totalSecond = 0;
                var lastEle = logs.data.length - 1;
                var parentOpenStatus = self.getParentOpenStatus(logs.data);

                logs.data.forEach(function (log, logIndex) {
                    totalSecond = totalSecond + parseInt(log.total.total_second);

                    self.addCustomField(log, logIndex, parentOpenStatus);

                    self.actionData.task.time.data.push(log);
                });

                if (parseInt(lastEle) > 0) {
                    pm.Vue.set(logs.data[lastEle], 'lastElement', true);
                    pm.Vue.set(logs.data[lastEle], 'userTotalSecond', totalSecond);
                }
            });

            this.timeLogs = this.actionData.task.time.data;
        },
        expandLogs: function expandLogs(time) {
            var newLogs = [];
            var user_id = time.user_id;
            var childVisibility;

            var parent = this.getIndex(this.openPrents, time.id, 'id');

            if (parent === false) {
                this.openPrents.push({
                    id: time.id,
                    openStatus: true
                });
                childVisibility = true;
            } else {
                childVisibility = this.openPrents[parent].openStatus = this.openPrents[parent].openStatus ? false : true;
            }

            time.class = childVisibility ? "icon-pm-up-arrow" : "icon-pm-down-arrow";

            this.timeLogs.forEach(function (log) {

                if (user_id == log.user_id && time.id != log.id) {
                    log.showStatus = childVisibility;
                }

                newLogs.push(log);
            });

            this.timeLogs = newLogs;
        },
        getSelfOthersTimeLog: function getSelfOthersTimeLog() {
            var self = this;
            var task = this.actionData.task;

            if (typeof task === 'undefined') {
                return false;
            }
            if (typeof task.assignees === 'undefined') {
                return false;
            }
            var users_id = task.assignees.data.map(function (user) {
                return user.id;
            });

            var args = {
                data: {
                    users_id: users_id,
                    project_id: task.project_id,
                    task_id: task.id,
                    list_id: task.task_list.data.id
                },

                callback: function callback(res) {
                    self.afterFetchOthersTime();
                }
            };

            this.getOthersTimeLog(args);
        },
        selfTimeLogeDelete: function selfTimeLogeDelete(time) {
            if (!confirm(__('Are you sure!', 'pm-pro'))) {
                return false;
            }
            var self = this;
            var task = this.actionData.task;

            var args = {
                data: {
                    time_id: time.id,
                    task_id: task.id,
                    list_id: task.task_list.data.id
                },

                run_status: time.run_status,

                callback: function callback(res) {
                    var parentIndex = self.getIndex(self.timeLogs, time.id, 'id');
                    var firstChildrenIndex = parseInt(parentIndex + 1);
                    var firstChildren = self.timeLogs[firstChildrenIndex];

                    if (firstChildren && typeof firstChildren.isChildren != 'undefined' && firstChildren.isChildren) {
                        var openStatusLog = self.getIndex(self.openPrents, time.id, 'id');
                        if (openStatusLog !== false) {
                            self.openPrents[openStatusLog].id = firstChildren.id;
                        }
                    }

                    self.actionData.task.time = res.data.time;

                    self.afterFetchOthersTime();
                }
            };

            this.deleteTimeLog(args);
        }
    }
});

/***/ }),
/* 38 */,
/* 39 */,
/* 40 */,
/* 41 */,
/* 42 */,
/* 43 */,
/* 44 */,
/* 45 */,
/* 46 */,
/* 47 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        actionData: {
            type: Object,
            default: function _default() {
                return {};
            }
        }
    },
    data: function data() {
        return {
            showPopUp: false
        };
    },
    created: function created() {
        this.setDefaultData();
    },

    methods: {
        validation: function validation($ele) {
            var value = $ele.target.value;
            var hasColon = value.indexOf(":");

            if (hasColon == -1) {
                value = value.replace(/[^0-9]/g, '');
                this.time = value;
                this.actionData.estimation = value * 60;
                //console.log( this.actionData.estimation  );
                return;
            }

            value = value.replace(/[^:0-9]/g, '');

            if (value.split(":").length - 1 > 1) {
                this.time = value.slice(0, -1);
                this.actionData.estimation = this.timeTominute(this.time);
                return;
            }

            this.time = value;

            var replacePart = ':';
            var firstPart = value.substr(0, value.indexOf(replacePart));
            var lastPart = value.substr(value.indexOf(replacePart) + replacePart.length);

            if (lastPart != '' && lastPart > 59) {
                this.time = value.slice(0, -1);
                this.actionData.estimation = this.timeTominute(this.time);
                return;
            }

            this.actionData.estimation = this.timeTominute(this.time);
        },
        timeTominute: function timeTominute(time) {
            var replacePart = ':';
            var firstPart = time.substr(0, time.indexOf(replacePart));
            var lastPart = time.substr(time.indexOf(replacePart) + replacePart.length);

            lastPart = lastPart ? parseInt(lastPart) : 0;
            firstPart = firstPart ? parseInt(firstPart) : 0;

            return parseInt(firstPart) * 60 + lastPart;
        },
        setDefaultData: function setDefaultData() {
            if (typeof this.actionData.estimation == 'undefined') {
                return;
            }

            var minuteToTime = this.stringToTime(this.actionData.estimation * 60);

            this.time = minuteToTime.hours + ':' + minuteToTime.minutes;
        },
        closePopUp: function closePopUp() {
            this.showPopUp = false;
        }
    }
});

/***/ }),
/* 48 */,
/* 49 */,
/* 50 */,
/* 51 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        actionData: {
            type: Object,
            default: function _default() {
                return {};
            }
        }
    },
    data: function data() {
        return {
            showPopUp: false,
            time: "00:00"
        };
    },
    created: function created() {
        this.setDefaultData();
    },


    methods: {
        validation: function validation($ele) {
            var value = $ele.target.value;
            var hasColon = value.indexOf(":");

            if (hasColon == -1) {
                value = value.replace(/[^0-9]/g, '');
                this.time = value;
                this.actionData.estimation = value * 60;
                //console.log( this.actionData.estimation  );
                return;
            }

            value = value.replace(/[^:0-9]/g, '');

            if (value.split(":").length - 1 > 1) {
                this.time = value.slice(0, -1);
                this.actionData.estimation = this.timeTominute(this.time);
                return;
            }

            this.time = value;

            var replacePart = ':';
            var firstPart = value.substr(0, value.indexOf(replacePart));
            var lastPart = value.substr(value.indexOf(replacePart) + replacePart.length);

            if (lastPart != '' && lastPart > 59) {
                this.time = value.slice(0, -1);
                this.actionData.estimation = this.timeTominute(this.time);
                return;
            }

            this.actionData.estimation = this.timeTominute(this.time);
        },
        timeTominute: function timeTominute(time) {
            var replacePart = ':';
            var firstPart = time.substr(0, time.indexOf(replacePart));
            var lastPart = time.substr(time.indexOf(replacePart) + replacePart.length);

            lastPart = lastPart ? parseInt(lastPart) : 0;
            firstPart = firstPart ? parseInt(firstPart) : 0;

            return parseInt(firstPart) * 60 + lastPart;
        },
        setDefaultData: function setDefaultData() {
            if (typeof this.actionData.estimation == 'undefined') {
                return;
            }

            var minuteToTime = this.stringToTime(this.actionData.estimation * 60);

            this.time = minuteToTime.hours + ":" + minuteToTime.minutes;
        },
        closePopUp: function closePopUp() {
            this.showPopUp = false;
        }
    }
});

/***/ }),
/* 52 */,
/* 53 */
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

/* harmony default export */ __webpack_exports__["a"] = ({});

/***/ }),
/* 54 */
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

/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        actionData: {
            type: Object,
            default: function _default() {
                return {};
            }
        }
    },
    data: function data() {
        return {
            showPopUp: false,
            isLoading: false,
            time: "00:00"
        };
    },
    created: function created() {
        this.setDefaultData();
    },

    methods: {
        saveEstimation: function saveEstimation() {
            if (this.actionData.task.estimation == '') {
                pm.Toastr.error(__('Please insert task estimation time', 'pm-pro'));
                return;
            }
            if (this.isLoading) {
                return;
            }

            this.updateTaskElement(this.actionData.task);
        },
        setMinuteToTime: function setMinuteToTime(minute) {
            minute = minute ? parseInt(minute) : 0;
            var time = this.stringToTime(minute * 60);

            return time.hours + ':' + time.minutes;
        },
        updateTaskElement: function updateTaskElement(task) {
            var self = this;

            var update_data = {
                'title': task.title,
                'estimation': self.setMinuteToTime(task.estimation)
            },
                self = this,
                url = this.base_url + 'pm/v2/projects/' + task.project_id + '/tasks/' + task.id + '/update';

            var request_data = {
                url: url,
                data: update_data,
                type: 'POST',
                success: function success(res) {
                    self.showPopUp = false;
                    self.isLoading = false;
                    pm.Toastr.success(__('Task estimation updated!', 'pm-pro'));
                },
                error: function error(res) {
                    res.responseJSON.message.map(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };
            self.isLoading = true;
            this.httpRequest(request_data);
        },
        isActiveTaskEstimatedTime: function isActiveTaskEstimatedTime() {

            var subTask = this.actionData.task.sub_tasks;
            var users = this.actionData.task.assignees;
            var canRead = true;

            // Check subtask conditon
            if (typeof subTask != 'undefined') {
                if (subTask.length) {
                    canRead = false;
                }
            }

            // Check multiuser conditon
            if (typeof users != 'undefined') {
                if (users.data.length != 1) {
                    canRead = false;
                }
            }

            if (!canRead) {
                this.actionData.task.estimation = '';
            }

            return canRead;
        },
        validation: function validation($ele) {
            var value = $ele.target.value;
            var hasColon = value.indexOf(":");

            if (hasColon == -1) {
                value = value.replace(/[^0-9]/g, '');
                this.time = value;
                this.actionData.task.estimation = value * 60;
                //console.log( this.actionData.task.estimation  );
                return;
            }

            value = value.replace(/[^:0-9]/g, '');

            if (value.split(":").length - 1 > 1) {
                this.time = value.slice(0, -1);
                this.actionData.task.estimation = this.timeTominute(this.time);
                return;
            }

            this.time = value;

            var replacePart = ':';
            var firstPart = value.substr(0, value.indexOf(replacePart));
            var lastPart = value.substr(value.indexOf(replacePart) + replacePart.length);

            if (lastPart != '' && lastPart > 59) {
                this.time = value.slice(0, -1);
                this.actionData.task.estimation = this.timeTominute(this.time);
                return;
            }

            this.actionData.task.estimation = this.timeTominute(this.time);
        },
        timeTominute: function timeTominute(time) {
            var replacePart = ':';
            var firstPart = time.substr(0, time.indexOf(replacePart));
            var lastPart = time.substr(time.indexOf(replacePart) + replacePart.length);

            lastPart = lastPart ? parseInt(lastPart) : 0;
            firstPart = firstPart ? parseInt(firstPart) : 0;

            return parseInt(firstPart) * 60 + lastPart;
        },
        setDefaultData: function setDefaultData() {
            if (typeof this.actionData.task.estimation == 'undefined') {
                return;
            }

            var minuteToTime = this.stringToTime(this.actionData.task.estimation * 60);

            this.time = minuteToTime.hours + ':' + minuteToTime.minutes;
        },
        closePopUp: function closePopUp() {
            this.showPopUp = false;
        }
    }
});

/***/ }),
/* 55 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(56);

var _router2 = _interopRequireDefault(_router);

var _watch = __webpack_require__(103);

var _watch2 = _interopRequireDefault(_watch);

var _timeLog = __webpack_require__(110);

var _timeLog2 = _interopRequireDefault(_timeLog);

var _estimatedTimeField = __webpack_require__(138);

var _estimatedTimeField2 = _interopRequireDefault(_estimatedTimeField);

var _subtaskEstimationTimeField = __webpack_require__(149);

var _subtaskEstimationTimeField2 = _interopRequireDefault(_subtaskEstimationTimeField);

var _reportSummery = __webpack_require__(157);

var _reportSummery2 = _interopRequireDefault(_reportSummery);

var _singleTaskEstimationForm = __webpack_require__(160);

var _singleTaskEstimationForm2 = _interopRequireDefault(_singleTaskEstimationForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/time_tracker/views/assets/js/';

// const watch = resolve => {
//     require.ensure(['./components/watch.vue'], () => {
//         resolve(require('./components/watch.vue'));
//     });
// }

// const timeLog = resolve => {
//     require.ensure(['./components/time-log.vue'], () => {
//         resolve(require('./components/time-log.vue'));
//     });
// }

weDevs_PM_Components.push({
    hook: 'task_inline',
    component: 'pm-pro-watch',
    property: _watch2.default
});

weDevs_PM_Components.push({
    hook: 'single_task_inline',
    component: 'pm-pro-single-task-watch',
    property: _watch2.default
});

weDevs_PM_Components.push({
    hook: 'aftre_single_task_content',
    component: 'pm-pro-time-log',
    property: _timeLog2.default
});

weDevs_PM_Components.push({
    hook: 'pm_task_form',
    component: 'pm_estimated_time_field',
    property: _estimatedTimeField2.default
});

weDevs_PM_Components.push({
    hook: 'pm_pro_subtask_form',
    component: 'pm-pro-subtask-form-field',
    property: _subtaskEstimationTimeField2.default
});

weDevs_PM_Components.push({
    hook: 'after-task-report-contenet',
    component: 'pm-pro-after-task-report-contenet',
    property: _reportSummery2.default
});

//import EstimationField from './components/single-task-estimation-for.vue'

weDevs_PM_Components.push({
    hook: 'single_task_inline',
    component: 'pm-pro-single-task-estimation-field',
    property: _singleTaskEstimationForm2.default
});

/***/ }),
/* 56 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _summarySearchResults = __webpack_require__(57);

var _summarySearchResults2 = _interopRequireDefault(_summarySearchResults);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevsPmProAddonRegisterModule('timeTracker', 'time_tracker');

weDevsPMRegisterChildrenRoute('reports_component', [{
    path: 'report-summary',
    component: _summarySearchResults2.default,
    name: 'report_summary',
    children: [{
        path: 'pages/:current_page_number',
        component: _summarySearchResults2.default,
        name: 'report_summary_pagination'
    }]
}]);

/***/ }),
/* 57 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_summary_search_results_vue__ = __webpack_require__(20);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_f55dbd84_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_summary_search_results_vue__ = __webpack_require__(102);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(58)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_summary_search_results_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_f55dbd84_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_summary_search_results_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/summary-search-results.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-f55dbd84", Component.options)
  } else {
    hotAPI.reload("data-v-f55dbd84", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 58 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(59);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("337a0e49", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-f55dbd84\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./summary-search-results.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-f55dbd84\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./summary-search-results.vue");
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

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-report-summary .summay-total-table {\n  font-size: 12px;\n  font-weight: 500;\n}\n.pm-pro-report-summary .pm-dropdown-menu {\n  margin-top: 10px !important;\n}\n", ""]);

// exports


/***/ }),
/* 60 */
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
/* 61 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_report_form_vue__ = __webpack_require__(21);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_108d7c72_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_report_form_vue__ = __webpack_require__(64);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(62)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_report_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_108d7c72_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_report_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/report-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-108d7c72", Component.options)
  } else {
    hotAPI.reload("data-v-108d7c72", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 62 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(63);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("9ebd1e4a", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-108d7c72\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./report-form.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-108d7c72\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./report-form.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 63 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-filter-report-modal .form .type-field-wrap {\n  margin-left: 30px;\n  margin-top: 5px;\n  font-weight: 300;\n}\n.pm-filter-report-modal .form .help-text {\n  font-size: 11px;\n  font-weight: 500;\n  font-style: italic;\n}\n.pm-filter-report-modal .form .filed-wrap {\n  margin-top: 12px;\n}\n.pm-filter-report-modal .form .filed-wrap .label {\n  float: left;\n  font-size: 13px;\n  font-weight: 600;\n}\n.pm-filter-report-modal .form .filed-wrap .label-field {\n  margin-left: 132px;\n  font-size: 13px;\n}\n.pm-filter-report-modal .form .filed-wrap .pm-datepickter-from,\n.pm-filter-report-modal .form .filed-wrap .pm-datepickter-to {\n  margin-bottom: auto !important;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap {\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__select {\n  display: none;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__element .multiselect__option .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-filter-report-modal .form .filed-wrap .multiselect-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-filter-report-modal .form .pm-modal-form-buttons {\n  margin-top: 25px;\n  position: relative;\n}\n.pm-filter-report-modal .form .pm-modal-form-buttons .process-data {\n  color: #0085ba !important;\n  text-shadow: none;\n}\n.pm-filter-report-modal .form .pm-modal-form-buttons .pm-circle-spinner {\n  position: absolute;\n  right: 37px;\n  top: 2px;\n}\n.pm-filter-report-modal .form .pm-modal-form-buttons .pm-circle-spinner:after {\n  border-color: #fff #fff #fff transparent;\n}\n", ""]);

// exports


/***/ }),
/* 64 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "pm-modal",
    {
      attrs: { isActive: _vm.isActive, width: 400 },
      on: {
        close: function($event) {
          return _vm.closeModal()
        }
      }
    },
    [
      _c("div", { staticClass: "pm-filter-report-modal" }, [
        _c(
          "div",
          {
            staticClass: "pm-filter-modal-header",
            attrs: { slot: "header" },
            slot: "header"
          },
          [
            _c("h3", { staticClass: "pm-mb-0" }, [
              _vm._v(_vm._s(_vm.__("Report", "pm-pro")))
            ])
          ]
        ),
        _vm._v(" "),
        _c("div", { staticClass: "pm-filter-modal-body" }, [
          _c(
            "form",
            {
              staticClass: "form",
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.submit()
                }
              }
            },
            [
              _c("div", { staticClass: "filed-wrap" }, [
                _c("label", { staticClass: "label" }, [
                  _vm._v(_vm._s(_vm.__("Choose report type", "pm-pro")))
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "type-field-wrap label-field" }, [
                  _c("div", { staticClass: "types" }, [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.type,
                          expression: "form.type"
                        }
                      ],
                      attrs: {
                        id: "summary-type",
                        type: "radio",
                        value: "summary",
                        name: "type"
                      },
                      domProps: { checked: _vm._q(_vm.form.type, "summary") },
                      on: {
                        change: function($event) {
                          return _vm.$set(_vm.form, "type", "summary")
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "summary-type" } }, [
                      _vm._v(_vm._s(_vm.__("Summary", "pm-pro")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "types" }, [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.form.type,
                          expression: "form.type"
                        }
                      ],
                      attrs: {
                        id: "user-type",
                        type: "radio",
                        value: "user",
                        name: "type"
                      },
                      domProps: { checked: _vm._q(_vm.form.type, "user") },
                      on: {
                        change: function($event) {
                          return _vm.$set(_vm.form, "type", "user")
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "user-type" } }, [
                      _vm._v(_vm._s(_vm.__("User", "pm-pro")))
                    ])
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "pm-clearfix" })
              ]),
              _vm._v(" "),
              _vm.form.type == "summary"
                ? _c("div", { staticClass: "filed-wrap" }, [
                    _c("div", { staticClass: "label-field" }, [
                      _c(
                        "select",
                        {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.form.summaryType,
                              expression: "form.summaryType"
                            }
                          ],
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
                                _vm.form,
                                "summaryType",
                                $event.target.multiple
                                  ? $$selectedVal
                                  : $$selectedVal[0]
                              )
                            }
                          }
                        },
                        [
                          _c("option", { attrs: { value: "all_project" } }, [
                            _vm._v(_vm._s(_vm.__("All Project", "pm-pro")))
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "task_type" } }, [
                            _vm._v(_vm._s(_vm.__("Task Type", "pm-pro")))
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "sub_task_type" } }, [
                            _vm._v(_vm._s(_vm.__("Subtask Type", "pm-pro")))
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "all_user" } }, [
                            _vm._v(_vm._s(_vm.__("All User", "pm-pro")))
                          ])
                        ]
                      )
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "pm-clearfix" })
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.form.type == "user"
                ? _c("div", { staticClass: "filed-wrap" }, [
                    _c("label", { staticClass: "label" }, [
                      _vm._v(_vm._s(_vm.__("According Users", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "label-field multiselect-wrap" },
                      [
                        _c("multiselect", {
                          ref: "assingTask",
                          attrs: {
                            id: "assingTask",
                            options: _vm.project_users,
                            multiple: false,
                            "close-on-select": true,
                            "clear-on-select": true,
                            "show-labels": true,
                            searchable: true,
                            placeholder: _vm.__(
                              "Search User",
                              "wedevs-project-manager"
                            ),
                            "select-label": "",
                            "selected-label": "selected",
                            "deselect-label": "",
                            label: "display_name",
                            "track-by": "user_id",
                            "allow-empty": true
                          },
                          scopedSlots: _vm._u(
                            [
                              {
                                key: "option",
                                fn: function(props) {
                                  return [
                                    _c("img", {
                                      staticClass: "option__image",
                                      attrs: { src: props.option.avatar_url }
                                    }),
                                    _vm._v(" "),
                                    _c("div", { staticClass: "option__desc" }, [
                                      _c(
                                        "span",
                                        { staticClass: "option__title" },
                                        [
                                          _vm._v(
                                            _vm._s(props.option.display_name)
                                          )
                                        ]
                                      )
                                    ])
                                  ]
                                }
                              }
                            ],
                            null,
                            false,
                            1410818572
                          ),
                          model: {
                            value: _vm.form.users,
                            callback: function($$v) {
                              _vm.$set(_vm.form, "users", $$v)
                            },
                            expression: "form.users"
                          }
                        })
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "pm-clearfix" })
                  ])
                : _vm._e(),
              _vm._v(" "),
              (_vm.form.summaryType == "all_user" ||
                _vm.form.summaryType == "task_type" ||
                _vm.form.summaryType == "sub_task_type") &&
              _vm.form.type == "summary"
                ? _c("div", { staticClass: "filed-wrap" }, [
                    _c("label", { staticClass: "label" }, [
                      _vm._v(_vm._s(_vm.__("Projects", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "label-field multiselect-wrap" },
                      [
                        _c("pm-project-drop-down", {
                          attrs: { multiple: true, allowEmpty: true },
                          on: { onChange: _vm.changeProject }
                        })
                      ],
                      1
                    )
                  ])
                : _vm._e(),
              _vm._v(" "),
              _c("div", { staticClass: "filed-wrap" }, [
                _c("label", { staticClass: "label" }, [
                  _vm._v(_vm._s(_vm.__("Start date", "pm-pro")))
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "label-field" },
                  [
                    _c("pm-date-picker", {
                      staticClass: "pm-datepickter-from",
                      attrs: {
                        dependency: "pm-datepickter-to",
                        placeholder: _vm.__("Select start date", "pm-pro")
                      },
                      model: {
                        value: _vm.form.startDate,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "startDate", $$v)
                        },
                        expression: "form.startDate"
                      }
                    }),
                    _vm._v(" "),
                    _c("div", { staticClass: "help-text" }, [
                      _vm._v(_vm._s(_vm.__("Time log start date", "pm-pro")))
                    ])
                  ],
                  1
                ),
                _vm._v(" "),
                _c("div", { staticClass: "pm-clearfix" })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "filed-wrap" }, [
                _c("label", { staticClass: "label" }, [
                  _vm._v(_vm._s(_vm.__("End date", "pm-pro")))
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "label-field" },
                  [
                    _c("pm-date-picker", {
                      staticClass: "pm-datepickter-to",
                      attrs: {
                        dependency: "pm-datepickter-from",
                        placeholder: _vm.__("Select due date", "pm-pro")
                      },
                      model: {
                        value: _vm.form.endDate,
                        callback: function($$v) {
                          _vm.$set(_vm.form, "endDate", $$v)
                        },
                        expression: "form.endDate"
                      }
                    }),
                    _vm._v(" "),
                    _c("div", { staticClass: "help-text" }, [
                      _vm._v(_vm._s(_vm.__("Time log start date", "pm-pro")))
                    ])
                  ],
                  1
                ),
                _vm._v(" "),
                _c("div", { staticClass: "pm-clearfix" })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "pm-modal-form-buttons" }, [
                _c("input", {
                  class: _vm.buttonClass(),
                  attrs: {
                    slot: "footer",
                    value: "Run Report",
                    type: "submit"
                  },
                  slot: "footer"
                }),
                _vm._v(" "),
                _vm.processSubmit
                  ? _c("span", { staticClass: "pm-circle-spinner" })
                  : _vm._e()
              ])
            ]
          )
        ])
      ])
    ]
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-108d7c72", esExports)
  }
}

/***/ }),
/* 65 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_projects_task_estimation_vue__ = __webpack_require__(22);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_eabe2434_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_projects_task_estimation_vue__ = __webpack_require__(68);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(66)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_projects_task_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_eabe2434_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_projects_task_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/projects-task-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-eabe2434", Component.options)
  } else {
    hotAPI.reload("data-v-eabe2434", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 66 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(67);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("c29c1582", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-eabe2434\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./projects-task-estimation.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-eabe2434\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./projects-task-estimation.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 67 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.project-task-estimation .total-item-wrap {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  margin-bottom: 1em;\n}\n.project-task-estimation .total-item-wrap .total-items {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n}\n.project-task-estimation .total-item-wrap .total-items .total-item {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  padding: 10px 10px 10px 0;\n}\n.project-task-estimation .total-item-wrap .total-items .total-item:last-child {\n  padding-right: 0;\n}\n.project-task-estimation .total-item-wrap .total-items .total-item .label {\n  background: #3797a4;\n  padding: 0 5px;\n  color: #f9f9f9;\n  font-size: 12px;\n}\n.project-task-estimation .total-item-wrap .total-items .total-item .value {\n  background: #8ccbbe;\n  color: #555;\n  padding: 0 10px;\n  font-size: 12px;\n}\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 68 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "project-task-estimation" }, [
    _c(
      "div",
      {
        staticClass:
          "pm-card pm-card-default total-item-wrap pm-report-meta-panel pm-report-worker-panel"
      },
      [
        _vm._m(0),
        _vm._v(" "),
        _c("div", { staticClass: "total-items" }, [
          _c("div", { staticClass: "total-item" }, [
            _c("div", { staticClass: "label" }, [
              _vm._v(_vm._s(_vm.__("Total Estimation Hours")))
            ]),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("div", { staticClass: "value" }, [
                  _vm._v(
                    _vm._s(
                      _vm.reports.projects.meta.total_task_estimation_hours
                    )
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "sub_task"
              ? _c("div", { staticClass: "value" }, [
                  _vm._v(
                    _vm._s(
                      _vm.reports.projects.meta.total_sub_task_estimation_hours
                    )
                  )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "total-item" }, [
            _vm.reports.report_for == "sub_task"
              ? _c("div", { staticClass: "label" }, [
                  _vm._v(_vm._s(_vm.__("Total Subtasks", "pm-pro")))
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("div", { staticClass: "label" }, [
                  _vm._v(_vm._s(_vm.__("Total Tasks", "pm-pro")))
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("div", { staticClass: "value" }, [
                  _vm._v(
                    _vm._s(_vm.reports.projects.meta.total_incomplete_tasks)
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "sub_task"
              ? _c("div", { staticClass: "value" }, [
                  _vm._v(
                    _vm._s(_vm.reports.projects.meta.total_incomplete_sub_tasks)
                  )
                ])
              : _vm._e()
          ])
        ])
      ]
    ),
    _vm._v(" "),
    _vm.grapVisibility
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [
              {
                name: "project-task-estimation",
                rawName: "v-project-task-estimation"
              }
            ],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    _c("table", { staticClass: "wp-list-table widefat fixed striped" }, [
      _c("thead", [
        _c("th", [_vm._v(_vm._s(_vm.__("Project Name", "pm-pro")))]),
        _vm._v(" "),
        _c("th", [_vm._v(_vm._s(_vm.__("Users", "pm-pro")))]),
        _vm._v(" "),
        _vm.reports.report_for == "task"
          ? _c("th", [_vm._v(_vm._s(_vm.__("Task", "pm-pro")))])
          : _vm._e(),
        _vm._v(" "),
        _vm.reports.report_for == "sub_task"
          ? _c("th", [_vm._v(_vm._s(_vm.__("Subtask", "pm-pro")))])
          : _vm._e(),
        _vm._v(" "),
        _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
      ]),
      _vm._v(" "),
      _c(
        "tbody",
        [
          _vm._l(_vm.reports.projects.data, function(project, index) {
            return _c("tr", [
              _c("td", [_vm._v(_vm._s(project.title))]),
              _vm._v(" "),
              _c(
                "td",
                _vm._l(project.users.data, function(user, index) {
                  return _c("span", [
                    _vm._v(
                      "\n                        " +
                        _vm._s(user.display_name) +
                        ",\n                    "
                    )
                  ])
                }),
                0
              ),
              _vm._v(" "),
              _vm.reports.report_for == "task"
                ? _c("td", [
                    _vm._v(
                      "\n                    " +
                        _vm._s(project.total_incomplete_tasks) +
                        "\n                "
                    )
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "sub_task"
                ? _c("td", [
                    _vm._v(
                      "\n                    " +
                        _vm._s(project.total_incomplete_sub_tasks) +
                        "\n                "
                    )
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "task"
                ? _c("td", [
                    _vm._v(
                      "\n                    " +
                        _vm._s(project.task_estimation_hours) +
                        "\n                "
                    )
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "sub_task"
                ? _c("td", [
                    _vm._v(
                      "\n                    " +
                        _vm._s(project.sub_task_estimation_hours) +
                        "\n                "
                    )
                  ])
                : _vm._e()
            ])
          }),
          _vm._v(" "),
          _c("tr", [
            _c("td", { attrs: { colspan: "2" } }, [
              _vm._v(_vm._s(_vm.__("Total", "pm-pro")))
            ]),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("td", [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.reports.projects.meta.total_incomplete_tasks) +
                      "\n                "
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "sub_task"
              ? _c("td", [
                  _vm._v(
                    "\n                    " +
                      _vm._s(
                        _vm.reports.projects.meta.total_incomplete_sub_tasks
                      ) +
                      "\n                "
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("td", [
                  _vm._v(
                    "\n                    " +
                      _vm._s(
                        _vm.reports.projects.meta.total_task_estimation_hours
                      ) +
                      "\n                "
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "sub_task"
              ? _c("td", [
                  _vm._v(
                    "\n                    " +
                      _vm._s(
                        _vm.reports.projects.meta
                          .total_sub_task_estimation_hours
                      ) +
                      "\n                "
                  )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          false
            ? _c("tr", [
                _c("td", { attrs: { colspan: "3" } }, [
                  _vm._v(_vm._s(_vm.__("No results found!", "pm-pro")))
                ])
              ])
            : _vm._e()
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "pm-report-panel-icon" }, [
      _c("i", { staticClass: "flaticon-resume" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-eabe2434", esExports)
  }
}

/***/ }),
/* 69 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_projects_subtask_estimation_vue__ = __webpack_require__(23);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e007ea60_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_projects_subtask_estimation_vue__ = __webpack_require__(70);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_projects_subtask_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e007ea60_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_projects_subtask_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/projects-subtask-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e007ea60", Component.options)
  } else {
    hotAPI.reload("data-v-e007ea60", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 70 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("h1", [_vm._v(_vm._s(_vm.__("All Projects", "pm-pro")))]),
    _vm._v(" "),
    _c(
      "table",
      { staticClass: "widefat" },
      [
        _c("thead", [
          _c("th", [_vm._v(_vm._s(_vm.__("Project Name", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Working Hour", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
        ]),
        _vm._v(" "),
        _vm._l(_vm.reports, function(project) {
          return _c("tr", [
            _c("td", [_vm._v(_vm._s(project.project_title))]),
            _vm._v(" "),
            _c("td", [_vm._v(_vm._s(_vm.secondToTime(project.working_time)))]),
            _vm._v(" "),
            _c("td", [
              _vm._v(
                _vm._s(
                  _vm.secondToTime(parseInt(project.subtask_estimation) * 60)
                )
              )
            ])
          ])
        })
      ],
      2
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
    require("vue-hot-reload-api")      .rerender("data-v-e007ea60", esExports)
  }
}

/***/ }),
/* 71 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_projects_task_estimation_vue__ = __webpack_require__(24);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_3ae5cacc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_projects_task_estimation_vue__ = __webpack_require__(74);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(72)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_projects_task_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_3ae5cacc_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_projects_task_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/user-projects-task-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3ae5cacc", Component.options)
  } else {
    hotAPI.reload("data-v-3ae5cacc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 72 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(73);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("7e4376f6", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3ae5cacc\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./user-projects-task-estimation.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-3ae5cacc\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./user-projects-task-estimation.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 73 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.user-project-task-estimation .total-item-wrap {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  margin-bottom: 1em;\n}\n.user-project-task-estimation .total-item-wrap .summery {\n  flex: 1;\n}\n.user-project-task-estimation .total-item-wrap .total-items {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n}\n.user-project-task-estimation .total-item-wrap .total-items .total-item {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  padding: 10px 10px 10px 0;\n}\n.user-project-task-estimation .total-item-wrap .total-items .total-item:last-child {\n  padding-right: 0;\n}\n.user-project-task-estimation .total-item-wrap .total-items .total-item .label {\n  background: #3797a4;\n  padding: 0 5px;\n  color: #f9f9f9;\n  font-size: 12px;\n}\n.user-project-task-estimation .total-item-wrap .total-items .total-item .value {\n  background: #8ccbbe;\n  color: #555;\n  padding: 0 10px;\n  font-size: 12px;\n}\n.user-project-task-estimation .total-item-wrap .total-items .total-item .project-title:after {\n  content: \", \";\n}\n.user-project-task-estimation .total-item-wrap .total-items .total-item .project-title:last-child:after {\n  content: \"\";\n}\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 74 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "user-project-task-estimation" }, [
    _c(
      "div",
      {
        staticClass:
          "pm-card pm-card-default total-item-wrap pm-report-meta-panel pm-report-worker-panel"
      },
      [
        _vm._m(0),
        _vm._v(" "),
        _c("div", { staticClass: "summery" }, [
          _c("div", { staticClass: "total-items" }, [
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Estimation Hours")))
              ]),
              _vm._v(" "),
              _vm.reports.report_for == "task"
                ? _c("div", { staticClass: "value" }, [
                    _vm._v(_vm._s(_vm.reports.meta.total_task_estimation_hours))
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "sub_task"
                ? _c("div", { staticClass: "value" }, [
                    _vm._v(
                      _vm._s(_vm.reports.meta.total_sub_task_estimation_hours)
                    )
                  ])
                : _vm._e()
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "total-item" }, [
              _vm.reports.report_for == "sub_task"
                ? _c("div", { staticClass: "label" }, [
                    _vm._v(_vm._s(_vm.__("Total Subtasks", "pm-pro")))
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "task"
                ? _c("div", { staticClass: "label" }, [
                    _vm._v(_vm._s(_vm.__("Total Tasks", "pm-pro")))
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "task"
                ? _c("div", { staticClass: "value" }, [
                    _vm._v(_vm._s(_vm.reports.meta.total_tasks))
                  ])
                : _vm._e(),
              _vm._v(" "),
              _vm.reports.report_for == "sub_task"
                ? _c("div", { staticClass: "value" }, [
                    _vm._v(_vm._s(_vm.reports.meta.total_sub_tasks))
                  ])
                : _vm._e()
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Projects")))
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "value" },
                [
                  _vm._l(_vm.reports.projects.data, function(project) {
                    return [
                      _c("span", { staticClass: "project-title" }, [
                        _vm._v(_vm._s(project.title))
                      ])
                    ]
                  })
                ],
                2
              )
            ])
          ])
        ])
      ]
    ),
    _vm._v(" "),
    _vm.grapVisibility
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [
              {
                name: "user-projects-estimation",
                rawName: "v-user-projects-estimation"
              }
            ],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    _c("table", { staticClass: "wp-list-table widefat fixed striped" }, [
      _c("thead", [
        _c("th", [_vm._v(_vm._s(_vm.__("User Name", "pm-pro")))]),
        _vm._v(" "),
        _vm.reports.report_for == "sub_task"
          ? _c("th", [_vm._v(_vm._s(_vm.__("Subtask", "pm-pro")))])
          : _vm._e(),
        _vm._v(" "),
        _vm.reports.report_for == "task"
          ? _c("th", [_vm._v(_vm._s(_vm.__("Task", "pm-pro")))])
          : _vm._e(),
        _vm._v(" "),
        _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
      ]),
      _vm._v(" "),
      _c(
        "tbody",
        [
          _vm._l(_vm.getReports(_vm.reports), function(item, userId) {
            return _c("tr", [
              _c("td", [_vm._v(_vm._s(item.user.display_name))]),
              _vm._v(" "),
              _c("td", [_vm._v(_vm._s(item.in_task_count))]),
              _vm._v(" "),
              _c("td", [_vm._v(_vm._s(item.estimation_hours))])
            ])
          }),
          _vm._v(" "),
          _c("tr", [
            _c("td", [_vm._v(_vm._s(_vm.__("Total", "pm-pro")))]),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("td", [_vm._v(_vm._s(_vm.reports.meta.total_tasks))])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "sub_task"
              ? _c("td", [_vm._v(_vm._s(_vm.reports.meta.total_sub_tasks))])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "task"
              ? _c("td", [
                  _vm._v(_vm._s(_vm.reports.meta.total_task_estimation_hours))
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.reports.report_for == "sub_task"
              ? _c("td", [
                  _vm._v(
                    _vm._s(_vm.reports.meta.total_sub_task_estimation_hours)
                  )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          false
            ? _c("tr", [
                _c("td", [
                  _vm._v(_vm._s(_vm.__("No results found!", "pm-pro")))
                ])
              ])
            : _vm._e()
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "pm-report-panel-icon" }, [
      _c("i", { staticClass: "flaticon-resume" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-3ae5cacc", esExports)
  }
}

/***/ }),
/* 75 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_projects_subtask_estimation_vue__ = __webpack_require__(25);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2da23c2a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_projects_subtask_estimation_vue__ = __webpack_require__(76);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_projects_subtask_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2da23c2a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_projects_subtask_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/user-projects-subtask-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2da23c2a", Component.options)
  } else {
    hotAPI.reload("data-v-2da23c2a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 76 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("h1", [_vm._v(_vm._s(_vm.__("User All Projects", "pm-pro")))]),
    _vm._v(" "),
    _c(
      "table",
      { staticClass: "widefat" },
      [
        _c("thead", [
          _c("th", [_vm._v(_vm._s(_vm.__("User Name", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Working Hour", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
        ]),
        _vm._v(" "),
        _vm._l(_vm.reports, function(user) {
          return _c("tr", [
            _c("td", [_vm._v(_vm._s(user.display_name))]),
            _vm._v(" "),
            _c("td", [_vm._v(_vm._s(_vm.secondToTime(user.working_time)))]),
            _vm._v(" "),
            _c("td", [
              _vm._v(_vm._s(_vm.secondToTime(user.subtask_estimation)))
            ])
          ])
        })
      ],
      2
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
    require("vue-hot-reload-api")      .rerender("data-v-2da23c2a", esExports)
  }
}

/***/ }),
/* 77 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_lists_task_estimation_vue__ = __webpack_require__(26);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_264454b6_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_lists_task_estimation_vue__ = __webpack_require__(80);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(78)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_lists_task_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_264454b6_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_lists_task_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/lists-task-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-264454b6", Component.options)
  } else {
    hotAPI.reload("data-v-264454b6", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 78 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(79);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("1ec4bea0", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-264454b6\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./lists-task-estimation.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-264454b6\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./lists-task-estimation.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 79 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 80 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("h1", [_vm._v(_vm._s(_vm.__("All Lists", "pm-pro")))]),
    _vm._v(" "),
    _vm.reports.length
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [
              {
                name: "project-list-task-estimation",
                rawName: "v-project-list-task-estimation"
              }
            ],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    _c(
      "table",
      { staticClass: "widefat" },
      [
        _c("thead", [
          _c("th", [_vm._v(_vm._s(_vm.__("Project Name", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Working Hour", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
        ]),
        _vm._v(" "),
        _vm._l(_vm.reports, function(list) {
          return _c("tr", [
            _c("td", [_vm._v(_vm._s(list.list_title))]),
            _vm._v(" "),
            _c("td", [_vm._v(_vm._s(_vm.secondToTime(list.working_time)))]),
            _vm._v(" "),
            _c("td", [
              _vm._v(
                _vm._s(_vm.secondToTime(parseInt(list.task_estimation) * 60))
              )
            ])
          ])
        }),
        _vm._v(" "),
        !_vm.reports.length
          ? _c("tr", [
              _c("td", [_vm._v(_vm._s(_vm.__("No results found!", "pm-pro")))])
            ])
          : _vm._e()
      ],
      2
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
    require("vue-hot-reload-api")      .rerender("data-v-264454b6", esExports)
  }
}

/***/ }),
/* 81 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_lists_subtask_estimation_vue__ = __webpack_require__(27);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_c4ced29e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_lists_subtask_estimation_vue__ = __webpack_require__(82);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_lists_subtask_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_c4ced29e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_lists_subtask_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/lists-subtask-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-c4ced29e", Component.options)
  } else {
    hotAPI.reload("data-v-c4ced29e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 82 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("h1", [_vm._v(_vm._s(_vm.__("All Lists", "pm-pro")))]),
    _vm._v(" "),
    _c(
      "table",
      { staticClass: "widefat" },
      [
        _c("thead", [
          _c("th", [_vm._v(_vm._s(_vm.__("Project Name", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Working Hour", "pm-pro")))]),
          _vm._v(" "),
          _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
        ]),
        _vm._v(" "),
        _vm._l(_vm.reports, function(list) {
          return _c("tr", [
            _c("td", [_vm._v(_vm._s(list.list_title))]),
            _vm._v(" "),
            _c("td", [_vm._v(_vm._s(_vm.secondToTime(list.working_time)))]),
            _vm._v(" "),
            _c("td", [
              _vm._v(_vm._s(_vm.secondToTime(list.subtask_estimation)))
            ])
          ])
        })
      ],
      2
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
    require("vue-hot-reload-api")      .rerender("data-v-c4ced29e", esExports)
  }
}

/***/ }),
/* 83 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_reports_task_estimation_vue__ = __webpack_require__(28);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e00136ae_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_reports_task_estimation_vue__ = __webpack_require__(86);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(84)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_reports_task_estimation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_e00136ae_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_reports_task_estimation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/user-reports-task-estimation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-e00136ae", Component.options)
  } else {
    hotAPI.reload("data-v-e00136ae", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 84 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(85);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("c90ac614", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e00136ae\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./user-reports-task-estimation.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-e00136ae\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./user-reports-task-estimation.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 85 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-graph-wrap {\n  background: #fff;\n  margin-right: 10px;\n  padding: 0 10px 10px 10px;\n  margin-bottom: 1em;\n  border: 1px solid #e5e5e5;\n}\n.pm-pro-graph-wrap:last-child {\n  margin-right: 0;\n}\n.user-reports-wrap .table-wrap {\n  margin-bottom: 1em;\n}\n.user-reports-wrap .pm-report-worker-panel {\n  margin-bottom: 1em;\n}\n.user-reports-wrap .graph-wrap {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  width: 100%;\n  margin-bottom: 0.5em;\n}\n.user-reports-wrap .graph-wrap .canvas {\n  width: 33.33%;\n  height: 400px;\n}\n.user-reports-wrap .user-meta-data-wrap {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n}\n.user-reports-wrap .user-meta-data-wrap .meta-item {\n  display: flex;\n  align-items: center;\n  padding: 10px 10px 10px 0;\n}\n.user-reports-wrap .user-meta-data-wrap .meta-item:last-child {\n  padding-right: 0;\n}\n.user-reports-wrap .user-meta-data-wrap .meta-item .label {\n  background: #3797a4;\n  padding: 0 5px;\n  color: #f9f9f9;\n  font-size: 12px;\n}\n.user-reports-wrap .user-meta-data-wrap .meta-item .value {\n  background: #8ccbbe;\n  color: #555;\n  padding: 0 10px;\n  font-size: 12px;\n}\n.user-reports-wrap .user-wrap {\n  display: flex;\n  align-items: center;\n}\n.user-reports-wrap .user-wrap .user-name {\n  margin-left: 12px;\n  background: #fff;\n  border: 1px solid #E5E5E5;\n  padding: 2px 10px;\n}\n.user-reports-wrap .title {\n  width: 50%;\n}\n.user-reports-wrap .working-hour,\n.user-reports-wrap .estimated-hour {\n  width: 25%;\n}\n", ""]);

// exports


/***/ }),
/* 86 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "user-reports-wrap" },
    [
      _vm._l(_vm.reports.users, function(user, id) {
        return _c("div", { staticClass: "user-wrap" }, [
          _c("h3", [_vm._v(_vm._s(_vm.__("User Name", "pm-pro")))]),
          _vm._v(" "),
          _c("span", { staticClass: "user-name" }, [
            _vm._v(_vm._s(_vm.ucfirst(user.display_name)))
          ])
        ])
      }),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass:
            "pm-card pm-card-default pm-report-meta-panel pm-report-worker-panel"
        },
        [
          _vm._m(0),
          _vm._v(" "),
          _vm._l(_vm.reports.meta, function(meta, user_id) {
            return _c("div", { staticClass: "user-meta-data-wrap" }, [
              _c("div", { staticClass: "meta-item" }, [
                _c("div", { staticClass: "label" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Total Estimation Hours")) +
                      "\n                "
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "value" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(meta.total_estimation) +
                      "\n                "
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "meta-item" }, [
                _c("div", { staticClass: "label" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Completed Task Count")) +
                      "\n                "
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "value" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(meta.completed_tasks) +
                      "\n                "
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "meta-item" }, [
                _c("div", { staticClass: "label" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Avg. Hour Per-task")) +
                      "\n                "
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "value" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(meta.avg_hour_task) +
                      "\n                "
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "meta-item" }, [
                _c("div", { staticClass: "label" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Avg. Work Hour Per-day")) +
                      "\n                "
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "value" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(meta.avg_work_hour) +
                      "\n                "
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "meta-item" }, [
                _c("div", { staticClass: "label" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(_vm.__("Avg. Task Per-day")) +
                      "\n                "
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "value" }, [
                  _vm._v(
                    "\n                    " +
                      _vm._s(meta.avg_task_day) +
                      "\n                "
                  )
                ])
              ])
            ])
          })
        ],
        2
      ),
      _vm._v(" "),
      _vm.grapVisibility
        ? _c("div", { staticClass: "graph-wrap" }, [
            _c("div", { staticClass: "pm-pro-graph-wrap" }, [
              _c("h3", [_vm._v(_vm._s(_vm.__("All Projects", "pm-pro")))]),
              _vm._v(" "),
              _c("canvas", {
                directives: [
                  {
                    name: "user-report-projects-estimation",
                    rawName: "v-user-report-projects-estimation"
                  }
                ],
                staticClass: "canvas",
                attrs: { width: "280", height: "280" }
              })
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "pm-pro-graph-wrap" }, [
              _c("h3", [_vm._v(_vm._s(_vm.__("Task Types", "pm-pro")))]),
              _vm._v(" "),
              _c("canvas", {
                directives: [
                  {
                    name: "user-report-task-type-estimation",
                    rawName: "v-user-report-task-type-estimation"
                  }
                ],
                staticClass: "canvas",
                attrs: { width: "280", height: "280" }
              })
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "pm-pro-graph-wrap" }, [
              _c("h3", [_vm._v(_vm._s(_vm.__("Subtask Types", "pm-pro")))]),
              _vm._v(" "),
              _c("canvas", {
                directives: [
                  {
                    name: "user-report-sub-task-type-estimation",
                    rawName: "v-user-report-sub-task-type-estimation"
                  }
                ],
                staticClass: "canvas",
                attrs: { width: "280", height: "280" }
              })
            ])
          ])
        : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "table-wrap" },
        [
          _vm.reports.report_for == "task"
            ? _c("h3", { staticStyle: { "margin-top": "0" } }, [
                _vm._v(_vm._s(_vm.__("Tasks", "pm-pro")))
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.reports.report_for == "sub_task"
            ? _c("h3", { staticStyle: { "margin-top": "0" } }, [
                _vm._v(_vm._s(_vm.__("Subtasks", "pm-pro")))
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm._l(_vm.reports.tasks.data, function(taskItems, user_id) {
            return [
              _c(
                "table",
                { staticClass: "wp-list-table widefat fixed striped" },
                [
                  _c("thead", [
                    _c("th", { staticClass: "completed-at" }, [
                      _vm._v(_vm._s(_vm.__("Completed At", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "task-title" }, [
                      _vm._v(_vm._s(_vm.__("Title", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "project" }, [
                      _vm._v(_vm._s(_vm.__("Project", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "type" }, [
                      _vm._v(_vm._s(_vm.__("Type", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "hour" }, [
                      _vm._v(_vm._s(_vm.__("Hour", "pm-pro")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "tbody",
                    [
                      _vm._l(taskItems.data, function(task, user_id) {
                        return [
                          _c("tr", [
                            _c("td", [_vm._v(_vm._s(task.completed_at.date))]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(task.title))]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(task.project_title))]),
                            _vm._v(" "),
                            _c("td", [
                              _vm._v(
                                _vm._s(task.type.title ? task.type.title : "--")
                              )
                            ]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(task.estimation))])
                          ])
                        ]
                      }),
                      _vm._v(" "),
                      _c("tr", [
                        _c("td", { attrs: { colspan: "4" } }, [
                          _vm._v(_vm._s(_vm.__("Total")))
                        ]),
                        _vm._v(" "),
                        _c("td", [
                          _vm._v(_vm._s(_vm.reports.tasks.meta.total_hours))
                        ])
                      ])
                    ],
                    2
                  )
                ]
              )
            ]
          }),
          _vm._v(" "),
          _vm.hasResults()
            ? _c("h1", [_vm._v(_vm._s(_vm.__("No result found!", "pm-pro")))])
            : _vm._e()
        ],
        2
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "table-wrap" },
        [
          _c("h3", [_vm._v(_vm._s(_vm.__("Projects", "pm-pro")))]),
          _vm._v(" "),
          _vm._l(_vm.reports.projects.data, function(projectItems, user_id) {
            return [
              _c(
                "table",
                { staticClass: "wp-list-table widefat fixed striped" },
                [
                  _c("thead", [
                    _c("th", { staticClass: "completed-at" }, [
                      _vm._v(_vm._s(_vm.__("Project Name", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _vm.reports.report_for == "task"
                      ? _c("th", { staticClass: "task-title" }, [
                          _vm._v(
                            "\n                        " +
                              _vm._s(_vm.__("Task", "pm-pro")) +
                              "\n                    "
                          )
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.reports.report_for == "sub_task"
                      ? _c("th", { staticClass: "task-title" }, [
                          _vm._v(
                            "\n                        " +
                              _vm._s(_vm.__("Subtask", "pm-pro")) +
                              "\n                    "
                          )
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    _c("th", { staticClass: "project" }, [
                      _vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "tbody",
                    [
                      _vm._l(projectItems.data, function(project, user_id) {
                        return [
                          _c("tr", [
                            _c("td", [_vm._v(_vm._s(project.project.title))]),
                            _vm._v(" "),
                            _vm.reports.report_for == "sub_task"
                              ? _c("td", [
                                  _vm._v(_vm._s(project.completed_sub_tasks))
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.reports.report_for == "task"
                              ? _c("td", [
                                  _vm._v(_vm._s(project.completed_tasks))
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.reports.report_for == "task"
                              ? _c("td", [
                                  _vm._v(_vm._s(project.estimated_task_hours))
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.reports.report_for == "sub_task"
                              ? _c("td", [
                                  _vm._v(
                                    _vm._s(project.estimated_sub_task_hours)
                                  )
                                ])
                              : _vm._e()
                          ])
                        ]
                      }),
                      _vm._v(" "),
                      _c("tr", [
                        _c("td", [_vm._v(_vm._s(_vm.__("Total")))]),
                        _vm._v(" "),
                        _vm.reports.report_for == "sub_task"
                          ? _c("td", [
                              _vm._v(
                                _vm._s(
                                  _vm.reports.projects.meta.total_sub_tasks
                                )
                              )
                            ])
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.reports.report_for == "task"
                          ? _c("td", [
                              _vm._v(
                                _vm._s(_vm.reports.projects.meta.total_tasks)
                              )
                            ])
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.reports.report_for == "task"
                          ? _c("td", [
                              _vm._v(
                                _vm._s(
                                  _vm.reports.projects.meta
                                    .total_task_estimation
                                )
                              )
                            ])
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.reports.report_for == "sub_task"
                          ? _c("td", [
                              _vm._v(
                                _vm._s(
                                  _vm.reports.projects.meta
                                    .total_sub_task_estimation
                                )
                              )
                            ])
                          : _vm._e()
                      ])
                    ],
                    2
                  )
                ]
              )
            ]
          }),
          _vm._v(" "),
          _vm.hasResults()
            ? _c("h1", [_vm._v(_vm._s(_vm.__("No result found!", "pm-pro")))])
            : _vm._e()
        ],
        2
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "table-wrap" },
        [
          _c("h3", [_vm._v(_vm._s(_vm.__("Task type", "pm-pro")))]),
          _vm._v(" "),
          _vm._l(_vm.reports.task_types.data, function(items, user_id) {
            return [
              _c(
                "table",
                { staticClass: "wp-list-table widefat fixed striped" },
                [
                  _c("thead", [
                    _c("th", { staticClass: "completed-at" }, [
                      _vm._v(_vm._s(_vm.__("Task Type", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "task-title" }, [
                      _vm._v(_vm._s(_vm.__("Task", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "project" }, [
                      _vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "tbody",
                    [
                      _vm._l(items.data, function(type, index) {
                        return [
                          _c("tr", [
                            _c("td", [_vm._v(_vm._s(type.type.title))]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(type.completed))]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(type.estimated_hours))])
                          ])
                        ]
                      }),
                      _vm._v(" "),
                      _c("tr", [
                        _c("td", [_vm._v(_vm._s(_vm.__("Total")))]),
                        _vm._v(" "),
                        _c("td", [
                          _vm._v(
                            _vm._s(_vm.reports.task_types.meta.total_tasks)
                          )
                        ]),
                        _vm._v(" "),
                        _c("td", [
                          _vm._v(
                            _vm._s(_vm.reports.task_types.meta.total_estimation)
                          )
                        ])
                      ])
                    ],
                    2
                  )
                ]
              )
            ]
          }),
          _vm._v(" "),
          _vm.hasResults()
            ? _c("h1", [_vm._v(_vm._s(_vm.__("No result found!", "pm-pro")))])
            : _vm._e()
        ],
        2
      ),
      _vm._v(" "),
      _c(
        "div",
        [
          _c("h3", [_vm._v(_vm._s(_vm.__("Subtask type", "pm-pro")))]),
          _vm._v(" "),
          _vm._l(_vm.reports.sub_task_types.data, function(items, user_id) {
            return _c("div", [
              _c(
                "table",
                { staticClass: "wp-list-table widefat fixed striped" },
                [
                  _c("thead", [
                    _c("th", { staticClass: "completed-at" }, [
                      _vm._v(_vm._s(_vm.__("Subtask Type", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "task-title" }, [
                      _vm._v(_vm._s(_vm.__("Subtask", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("th", { staticClass: "project" }, [
                      _vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))
                    ])
                  ]),
                  _vm._v(" "),
                  _c(
                    "tbody",
                    [
                      _vm._l(items.data, function(type, user_id) {
                        return [
                          _c("tr", [
                            _c("td", [_vm._v(_vm._s(type.type.title))]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(type.completed))]),
                            _vm._v(" "),
                            _c("td", [_vm._v(_vm._s(type.estimated_hours))])
                          ])
                        ]
                      }),
                      _vm._v(" "),
                      _c("tr", [
                        _c("td", [_vm._v(_vm._s(_vm.__("Total")))]),
                        _vm._v(" "),
                        _c("td", [
                          _vm._v(
                            _vm._s(
                              _vm.reports.sub_task_types.meta.total_sub_tasks
                            )
                          )
                        ]),
                        _vm._v(" "),
                        _c("td", [
                          _vm._v(
                            _vm._s(
                              _vm.reports.sub_task_types.meta.total_estimation
                            )
                          )
                        ])
                      ])
                    ],
                    2
                  )
                ]
              )
            ])
          }),
          _vm._v(" "),
          _vm.hasResults()
            ? _c("h1", [_vm._v(_vm._s(_vm.__("No result found!", "pm-pro")))])
            : _vm._e()
        ],
        2
      )
    ],
    2
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "pm-report-panel-icon" }, [
      _c("i", { staticClass: "flaticon-resume" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-e00136ae", esExports)
  }
}

/***/ }),
/* 87 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_project_by_user_graph_vue__ = __webpack_require__(29);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_fea78550_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_project_by_user_graph_vue__ = __webpack_require__(90);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(88)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_project_by_user_graph_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_fea78550_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_project_by_user_graph_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/project-by-user-graph.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-fea78550", Component.options)
  } else {
    hotAPI.reload("data-v-fea78550", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 88 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(89);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("157021d4", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-fea78550\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./project-by-user-graph.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-fea78550\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./project-by-user-graph.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 89 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 90 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _vm.reports.length
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [
              { name: "project-by-users", rawName: "v-project-by-users" }
            ],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    !_vm.reports.length
      ? _c("div", [
          _vm._v(
            "\n            " +
              _vm._s(_vm.__("No results found!", "pm-pro")) +
              "\n    "
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
    require("vue-hot-reload-api")      .rerender("data-v-fea78550", esExports)
  }
}

/***/ }),
/* 91 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_by_project_graph_vue__ = __webpack_require__(30);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7eac1170_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_by_project_graph_vue__ = __webpack_require__(101);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(92)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_user_by_project_graph_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7eac1170_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_user_by_project_graph_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/user-by-project-graph.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7eac1170", Component.options)
  } else {
    hotAPI.reload("data-v-7eac1170", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 92 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(93);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("557401f2", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7eac1170\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./user-by-project-graph.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7eac1170\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./user-by-project-graph.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 93 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 94 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


exports.__esModule = true;

var _defineProperty = __webpack_require__(95);

var _defineProperty2 = _interopRequireDefault(_defineProperty);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = function (obj, key, value) {
  if (key in obj) {
    (0, _defineProperty2.default)(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
};

/***/ }),
/* 95 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = { "default": __webpack_require__(96), __esModule: true };

/***/ }),
/* 96 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(97);
var $Object = __webpack_require__(6).Object;
module.exports = function defineProperty(it, key, desc) {
  return $Object.defineProperty(it, key, desc);
};


/***/ }),
/* 97 */
/***/ (function(module, exports, __webpack_require__) {

var $export = __webpack_require__(13);
// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
$export($export.S + $export.F * !__webpack_require__(8), 'Object', { defineProperty: __webpack_require__(7).f });


/***/ }),
/* 98 */
/***/ (function(module, exports) {

module.exports = function (it) {
  if (typeof it != 'function') throw TypeError(it + ' is not a function!');
  return it;
};


/***/ }),
/* 99 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = !__webpack_require__(8) && !__webpack_require__(32)(function () {
  return Object.defineProperty(__webpack_require__(33)('div'), 'a', { get: function () { return 7; } }).a != 7;
});


/***/ }),
/* 100 */
/***/ (function(module, exports, __webpack_require__) {

// 7.1.1 ToPrimitive(input [, PreferredType])
var isObject = __webpack_require__(14);
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
/* 101 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _vm.reports.length
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [
              { name: "user-by-projects", rawName: "v-user-by-projects" }
            ],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    !_vm.reports.length
      ? _c("div", [
          _vm._v(
            "\n            " +
              _vm._s(_vm.__("No results found!", "pm-pro")) +
              "\n    "
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
    require("vue-hot-reload-api")      .rerender("data-v-7eac1170", esExports)
  }
}

/***/ }),
/* 102 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-pro-report-summary" },
    [
      _c("div", { staticClass: "pm-page-header" }, [
        _c("div", { staticClass: "pm-page-title-container" }, [
          _vm.getReports.type == "get_users_by_task_estimated_time"
            ? _c("h1", [
                _c("span", [
                  _vm._v(_vm._s(_vm.__("Report For Individual User", "pm-pro")))
                ])
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.getReports.type ==
          "get_summary_all_projects_by_task_estimated_time"
            ? _c("h1", [
                _c("span", [_vm._v(_vm._s(_vm.__("All Projects", "pm-pro")))])
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.getReports.type == "task_type_summary"
            ? _c("h1", [
                _c("span", [_vm._v(_vm._s(_vm.__("Task Types", "pm-pro")))])
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.getReports.type == "sub_task_type_summary"
            ? _c("h1", [
                _c("span", [_vm._v(_vm._s(_vm.__("Subtask Types", "pm-pro")))])
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.getReports.type == "get_user_all_projects_by_task_estimated_time"
            ? _c("h1", [
                _c("span", [_vm._v(_vm._s(_vm.__("All Users", "pm-pro")))])
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("h1", [
            _c("span", [_vm._v(_vm._s(_vm.__("Date between", "pm-pro")))])
          ]),
          _vm._v(" "),
          _vm.hasDateBetween()
            ? _c("div", { staticClass: "pm-report-date-range" }, [
                _c(
                  "span",
                  {
                    staticClass: "pm-text-primary",
                    attrs: { title: "Friday, March 1 2019, 0:00:00" }
                  },
                  [
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.dateFormat(_vm.dateStart, "MMM D, YYYY")) +
                        "\n\n                "
                    )
                  ]
                ),
                _vm._v(" "),
                _c("span", [
                  _vm._v("\n                    to\n                ")
                ]),
                _vm._v(" "),
                _c(
                  "span",
                  {
                    staticClass: "pm-text-primary",
                    attrs: { title: "Wednesday, April 10 2019, 0:00:00" }
                  },
                  [
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.dateFormat(_vm.dateEnd, "MMM D, YYYY")) +
                        "\n                "
                    )
                  ]
                )
              ])
            : _vm._e()
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "pm-display-flex pm-filter-wrapper" }, [
          _c(
            "a",
            {
              staticClass: "pm--btn pm--btn-default pm-mr-10",
              attrs: { href: "#" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  _vm.form.status = true
                }
              }
            },
            [
              _c("i", { staticClass: "flaticon-filter-tool-black-shape mr-5" }),
              _vm._v(
                "\n                " +
                  _vm._s(_vm.__("Filter", "pm-pro")) +
                  "\n            "
              )
            ]
          ),
          _vm._v(" "),
          _c("div", { staticClass: "pm-has-dropdown pm-report-export-block" }, [
            _c(
              "a",
              {
                staticClass: "pm--btn pm--btn-default pm-dropdown-trigger",
                attrs: { href: "#" },
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.dropdownTrigger()
                  }
                }
              },
              [
                _c("i", { staticClass: "flaticon-export mr-5" }),
                _vm._v(
                  "\n                    " +
                    _vm._s(_vm.__("Export", "pm-pro")) +
                    "\n                    "
                ),
                _c("i", {
                  staticClass:
                    "flaticon-arrow-down-sign-to-navigate pm-mr-0 pm-ml-10"
                })
              ]
            ),
            _vm._v(" "),
            _c("ul", { class: _vm.dropdownToggleClass() }, [
              _c("li", [
                _c(
                  "a",
                  {
                    attrs: { href: "#" },
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.exportCSV()
                      }
                    }
                  },
                  [
                    _c("span", {
                      staticClass:
                        "flaticon-data-export-symbol-of-a-window-with-an-arrow"
                    }),
                    _vm._v(" "),
                    _c("span", [
                      _vm._v(_vm._s(_vm.__("Export to CSV", "pm-pro")))
                    ])
                  ]
                )
              ])
            ])
          ])
        ])
      ]),
      _vm._v(" "),
      _vm.hasLoadingEffect
        ? _c("div", { staticClass: "pm-data-load-before" }, [_vm._m(0)])
        : _vm._e(),
      _vm._v(" "),
      _vm.form.status
        ? _c("report-form", {
            attrs: { results: _vm.results, formVisibility: _vm.form }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_summary_all_projects_by_task_estimated_time"
        ? _c("projects-task-estimation", { attrs: { reports: _vm.getReports } })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type ==
      "get_summary_all_projects_by_subtask_estimated_time"
        ? _c("projects-subtask-estimation", {
            attrs: { reports: _vm.getReports.projects }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_user_all_projects_by_task_estimated_time"
        ? _c("user-projects-task-estimation", {
            attrs: { reports: _vm.getReports }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_user_all_projects_by_subtask_estimated_time"
        ? _c("user-projects-subtask-estimation", {
            attrs: { reports: _vm.getReports.users }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_summary_list_type_by_task_estimated_time"
        ? _c("lists-task-estimation", {
            attrs: { reports: _vm.getReports.lists }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_summary_list_type_by_subtask_estimated_time"
        ? _c("lists-subtask-estimation", {
            attrs: { reports: _vm.getReports.lists }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_users_by_task_estimated_time"
        ? _c("user-reports-task-estimation", {
            attrs: { reports: _vm.getReports }
          })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_summary_project_by_users"
        ? _c("project-report-by-users", { attrs: { reports: _vm.getReports } })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "get_summary_user_by_projects"
        ? _c("user-report-by-projects", { attrs: { reports: _vm.getReports } })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "task_type_summary"
        ? _c("task-type-summary", { attrs: { reports: _vm.getReports } })
        : _vm._e(),
      _vm._v(" "),
      _vm.getReports.type == "sub_task_type_summary"
        ? _c("sub-task-type-summary", { attrs: { reports: _vm.getReports } })
        : _vm._e()
    ],
    1
  )
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
    require("vue-hot-reload-api")      .rerender("data-v-f55dbd84", esExports)
  }
}

/***/ }),
/* 103 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_watch_vue__ = __webpack_require__(34);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_8704dd4e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_watch_vue__ = __webpack_require__(109);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(104)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_watch_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_8704dd4e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_watch_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/watch.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-8704dd4e", Component.options)
  } else {
    hotAPI.reload("data-v-8704dd4e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 104 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(105);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("ef6d3546", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8704dd4e\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./watch.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-8704dd4e\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./watch.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 105 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.toast-warning {\n  background-color: #019dd6;\n}\n.pm-pro-task-inline .pm-popup-menu {\n  width: 118px !important;\n  left: -91px;\n  top: 30px;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul:before {\n  left: 90px !important;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul:after {\n  left: 90px !important;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul .icon-pm-play {\n  font-size: 11px;\n  cursor: pointer;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul .icon-pm-watch {\n  font-size: 13px;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul .icon-pm-stop-custom {\n  margin-right: 7px;\n  color: #d4d6d6;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li {\n  margin: 0 !important;\n  display: flex;\n  align-items: center;\n  border: none !important;\n  padding: 0 !important;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li:hover .icon-pm-play:before,\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li:hover .icon-pm-watch:before,\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li:hover .icon-pm-stop-custom:before {\n  color: #444;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li .icon-pm-play {\n  margin-right: 11px;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li .icon-pm-watch {\n  margin-right: 4px;\n}\n.pm-pro-task-inline .pm-popup-menu .time-popup-menu-ul li a {\n  font-size: 11px;\n  color: #000;\n  line-height: 0;\n  display: flex;\n  align-items: center;\n  padding: 10px 15px 10px 10px;\n  width: 100%;\n}\n.pm-pro-task-inline .pm-popup-menu-running {\n  width: 118px !important;\n  left: -19px;\n  top: 30px;\n}\n.pm-pro-task-inline .pm-popup-menu-running .time-popup-menu-ul:before {\n  left: 90px !important;\n}\n.pm-pro-task-inline .pm-popup-menu-running .time-popup-menu-ul:after {\n  left: 90px !important;\n}\n.pm-pro-task-inline .time-tracker-menu {\n  position: relative;\n}\n.pm-pro-task-inline .time-tracker-menu .icon-pm-watch {\n  cursor: pointer;\n}\n.pm-pro-task-inline .time-tracker-menu .icon-pm-watch:hover:before {\n  color: #444;\n}\n.pm-pro-task-inline .time-tracker-menu .custom-time-popup-menu {\n  position: absolute;\n  left: -139px;\n  top: 38px;\n  z-index: 9999;\n  background: #fff;\n  box-shadow: 0px 2px 40px 0px rgba(214, 214, 214, 0.6);\n  text-align: left;\n  margin: 0;\n  border-radius: 3px;\n  border: 1px solid #ddd;\n  padding: 10px;\n}\n.pm-pro-task-inline .time-tracker-menu .watch-time-popup {\n  position: absolute;\n  left: -219px;\n  top: 30px;\n  z-index: 9999;\n  background: #fff;\n  box-shadow: 0px 2px 40px 0px rgba(214, 214, 214, 0.6);\n  text-align: left;\n  margin: 0;\n  border-radius: 3px;\n  border: 1px solid #ddd;\n  padding: 10px;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info {\n  display: flex;\n  align-items: center;\n  border: 1px solid #DEDEDE;\n  padding: 4px 6px 4px 6px;\n  border-radius: 3px;\n  font-size: 11px;\n  line-height: 0;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .the-time {\n  display: flex;\n  width: 47px;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .icon-pm-play {\n  margin-right: 5px;\n  font-size: 8px;\n  cursor: pointer;\n  padding: 1px 1px 1px 3px;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .icon-pm-play:hover:before {\n  color: #444;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .icon-pm-stop {\n  margin-right: 5px;\n  font-size: 10px;\n  cursor: pointer;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .icon-pm-stop:hover:before {\n  color: #444;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .icon-pm-down-arrow {\n  margin-left: 5px;\n  font-size: 6px;\n  cursor: pointer;\n}\n.pm-pro-task-inline .pm-pro-tr-task-info .icon-pm-down-arrow:hover:before {\n  color: #444;\n}\n", ""]);

// exports


/***/ }),
/* 106 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(107);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("0c025cfc", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-39d5d5b7\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./custom-time-log-form.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-39d5d5b7\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./custom-time-log-form.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 107 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.custom-time-form * {\n  color: #000;\n  font-size: 11px;\n}\n.custom-time-form .custom-time-action {\n  margin-top: 10px;\n}\n.pm-task-start-field {\n  margin-bottom: 10px;\n}\n.pm-task-start-field,\n.pm-task-due-field {\n  display: flex;\n  align-items: center;\n}\n.pm-task-start-field label,\n.pm-task-due-field label {\n  width: 70px;\n}\n", ""]);

// exports


/***/ }),
/* 108 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "custom-time-form" }, [
    _c(
      "form",
      {
        staticClass: "pm-form",
        attrs: { action: "" },
        on: {
          submit: function($event) {
            $event.preventDefault()
            return _vm.selfNewCustomTime(_vm.actionData)
          }
        }
      },
      [
        _c(
          "div",
          { staticClass: "pm-task-start-field" },
          [
            _c("label", [_vm._v(_vm._s(_vm.__("Date", "pm-pro")))]),
            _vm._v(" "),
            _c("pm-date-time-picker", {
              staticClass: "pm-datepickter-from",
              attrs: {
                field_type: "date",
                dependency: "pm-datepickter-to",
                required: ""
              },
              model: {
                value: _vm.start,
                callback: function($$v) {
                  _vm.start = $$v
                },
                expression: "start"
              }
            })
          ],
          1
        ),
        _vm._v(" "),
        _c("div", { staticClass: "pm-task-due-field" }, [
          _c("label", [_vm._v(_vm._s(_vm.__("Total Time", "pm-pro")))]),
          _vm._v(" "),
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.stop,
                expression: "stop"
              }
            ],
            attrs: { type: "text" },
            domProps: { value: _vm.stop },
            on: {
              input: [
                function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.stop = $event.target.value
                },
                _vm.validation
              ]
            }
          })
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "custom-time-action" }, [
          _c("input", {
            staticClass: "cpmtt-custom-time-add button button-primary",
            attrs: { type: "submit", disabled: !_vm.start || !_vm.stop },
            domProps: { value: _vm.add_entry }
          }),
          _vm._v(" "),
          _c(
            "a",
            {
              staticClass: "cpmtt-custon-time-cancle button",
              attrs: { href: "#" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.showCustomTimeForm()
                }
              }
            },
            [_c("span", [_vm._v(_vm._s(_vm.__("Cancel", "pm-pro")))])]
          ),
          _vm._v(" "),
          _c("span", { staticClass: "cpmtt-spinner" })
        ])
      ]
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
    require("vue-hot-reload-api")      .rerender("data-v-39d5d5b7", esExports)
  }
}

/***/ }),
/* 109 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.can_start_time
    ? _c("div", { staticClass: "group-icon pm-pro-task-inline" }, [
        _c("div", { staticClass: "time-tracker-menu" }, [
          !_vm.timeActivityRunning && !_vm.timeRunning
            ? _c("div", {
                directives: [{ name: "pm-tooltip", rawName: "v-pm-tooltip" }],
                class:
                  "icon-pm-watch pm-font-size-16 click_clock_class_" +
                  _vm.actionData.task.id,
                attrs: { title: _vm.__("Time Log", "pm-pro"), href: "#" },
                on: {
                  click: function($event) {
                    if ($event.target !== $event.currentTarget) {
                      return null
                    }
                    $event.preventDefault()
                    return _vm.showTimeTrackMenu()
                  }
                }
              })
            : _vm._e(),
          _vm._v(" "),
          _vm.action.showMenu && !_vm.isArchivedTaskListComputed
            ? _c(
                "div",
                {
                  class:
                    _vm.runningTimeClass(
                      _vm.timeActivityRunning,
                      _vm.timeRunning
                    ) + " pm-popup-menu time-tracker-watch"
                },
                [
                  _c(
                    "ul",
                    { staticClass: "time-popup-menu-ul pm-popup-menu-ul" },
                    [
                      _c("li", [
                        !_vm.timeRunning
                          ? _c(
                              "a",
                              {
                                class:
                                  "click_start_clock_class_" +
                                  _vm.actionData.task.id,
                                attrs: { href: "#" },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    return _vm.selfTimeStart(_vm.actionData)
                                  }
                                }
                              },
                              [
                                _c("span", { staticClass: "icon-pm-play" }),
                                _vm._v(" "),
                                _c("span", [
                                  _vm._v(
                                    _vm._s(
                                      _vm.__(
                                        "Start Time",
                                        "wedevs-project-manager"
                                      )
                                    )
                                  )
                                ])
                              ]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.timeRunning
                          ? _c(
                              "a",
                              {
                                staticClass: "pm-pro-tr-button",
                                attrs: { href: "#" },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    return _vm.selfTimeStop(_vm.actionData)
                                  }
                                }
                              },
                              [
                                _c("span", {
                                  staticClass:
                                    "icon-pm-stop icon-pm-stop-custom"
                                }),
                                _vm._v(" "),
                                _c("span", [
                                  _vm._v(_vm._s(_vm.__("Stop", "pm-pro")))
                                ])
                              ]
                            )
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("li", [
                        _c(
                          "a",
                          {
                            attrs: { href: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.showHideTimeForm(true)
                              }
                            }
                          },
                          [
                            _c("span", { staticClass: "icon-pm-watch" }),
                            _vm._v(" "),
                            _c("span", [
                              _vm._v(
                                _vm._s(
                                  _vm.__(
                                    "Custom Time",
                                    "wedevs-project-manager"
                                  )
                                )
                              )
                            ])
                          ]
                        )
                      ])
                    ]
                  )
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.canAddTime(_vm.actionData) && _vm.action.customTimeForm
            ? _c(
                "div",
                {
                  class:
                    _vm.customTimePopUpClass(
                      _vm.timeActivityRunning,
                      _vm.timeRunning
                    ) + " time-tracker-watch popup-menu"
                },
                [
                  _c("custom-time-form", {
                    attrs: { meta: _vm.action, actionData: _vm.actionData }
                  })
                ],
                1
              )
            : _vm._e()
        ]),
        _vm._v(" "),
        _c(
          "div",
          {
            staticStyle: { display: "none" },
            attrs: { id: "timer_start_id_" + _vm.actionData.task.id },
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.selfTimeStart(_vm.actionData)
              }
            }
          },
          [_vm._v("Click")]
        ),
        _vm._v(" "),
        _vm.timeActivityRunning || _vm.timeRunning
          ? _c("div", { staticClass: "pm-pro-tr-task-info" }, [
              !_vm.timeRunning
                ? _c("span", {
                    staticClass: "icon-pm-play",
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.selfTimeStart(_vm.actionData)
                      }
                    }
                  })
                : _vm._e(),
              _vm._v(" "),
              _vm.timeRunning
                ? _c("span", {
                    staticClass: "icon-pm-stop",
                    attrs: { id: "timer_id_" + _vm.actionData.task.id },
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.selfTimeStop(_vm.actionData)
                      }
                    }
                  })
                : _vm._e(),
              _vm._v(" "),
              _c("div", { staticClass: "the-time" }, [
                _c("span", { staticClass: "hr" }, [
                  _vm._v(_vm._s(_vm.hour) + ":")
                ]),
                _vm._v(" "),
                _c("span", { staticClass: "min" }, [
                  _vm._v(_vm._s(_vm.minute) + ":")
                ]),
                _vm._v(" "),
                _c("span", { staticClass: "sec" }, [_vm._v(_vm._s(_vm.second))])
              ]),
              _vm._v(" "),
              _c("span", {
                staticClass: "icon-pm-down-arrow",
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.showTimeTrackMenu()
                  }
                }
              })
            ])
          : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-8704dd4e", esExports)
  }
}

/***/ }),
/* 110 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_time_log_vue__ = __webpack_require__(37);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_95c973ac_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_time_log_vue__ = __webpack_require__(137);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(111)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_time_log_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_95c973ac_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_time_log_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/time-log.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-95c973ac", Component.options)
  } else {
    hotAPI.reload("data-v-95c973ac", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 111 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(112);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("f307dbd6", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-95c973ac\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./time-log.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-95c973ac\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./time-log.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 112 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.time-logs img {\n  height: 24px;\n  width: 24px;\n  border-radius: 12px;\n}\n.time-logs .time-log-table {\n  border: 1px solid #ECECEC;\n  border-left: none;\n  background: #FAFAFA;\n  font-size: 13px;\n}\n.time-logs .time-log-table .net-total {\n  margin-left: 64%;\n  font-size: 12px;\n  color: #000;\n}\n.time-logs .time-log-table .net-total .net-total-time {\n  margin-left: 30%;\n}\n.time-logs .time-log-table .total-text-row {\n  font-size: 13px;\n  color: #000;\n  margin-top: 16px;\n}\n.time-logs .time-log-table .table-row-collups {\n  font-size: 7px;\n  cursor: pointer;\n}\n.time-logs .time-log-table .time-log-first-td .icon-pm-delete {\n  cursor: pointer;\n}\n.time-logs .time-log-table .user-info-td {\n  display: flex;\n  align-items: center;\n}\n.time-logs .time-log-table .user-info-td .icon-pm-down-arrow:before {\n  cursor: pointer;\n}\n.time-logs .time-log-table .user-info-td .icon-pm-down-arrow:hover:before {\n  color: #444;\n}\n.time-logs .time-log-table .user-info-td .icon-pm-up-arrow:before {\n  cursor: pointer;\n}\n.time-logs .time-log-table .user-info-td .icon-pm-up-arrow:hover:before {\n  color: #444;\n}\n.time-logs .time-log-table .time-log-th {\n  border-left: 1px solid #ECECEC;\n}\n.time-logs .time-log-table .user-image {\n  margin: 0 5px;\n}\n.time-logs .time-log-table .time-log-child-td {\n  border-top: none;\n  border-right: none;\n  border-bottom: none;\n  border-left: 1px solid #ECECEC;\n}\n.time-logs .time-log-table .time-log-child-td .user-image {\n  margin-left: 16px;\n}\n.time-logs .time-log-table th {\n  border: none;\n  font-weight: normal;\n  color: #000;\n}\n.time-logs .time-log-table tbody tr td {\n  font-size: 12px;\n  font-weight: normal;\n  color: #788383;\n  border-top: 1px solid #ECECEC;\n  border-left: 1px solid #ECECEC;\n  border-collapse: collapse;\n}\n", ""]);

// exports


/***/ }),
/* 113 */,
/* 114 */,
/* 115 */,
/* 116 */,
/* 117 */,
/* 118 */,
/* 119 */,
/* 120 */,
/* 121 */,
/* 122 */,
/* 123 */,
/* 124 */,
/* 125 */,
/* 126 */,
/* 127 */,
/* 128 */,
/* 129 */,
/* 130 */,
/* 131 */,
/* 132 */,
/* 133 */,
/* 134 */,
/* 135 */,
/* 136 */,
/* 137 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.timeLogs.length
    ? _c("div", { staticClass: "time-logs" }, [
        _c("table", { staticClass: "table time-log-table table-bordered" }, [
          _c("thead", [
            _c("tr", [
              _c("th", { staticClass: "time-log-th" }, [
                _vm._v(_vm._s(_vm.__("User", "pm-pro")))
              ]),
              _vm._v(" "),
              _c("th", [_vm._v(_vm._s(_vm.__("Start", "pm-pro")))]),
              _vm._v(" "),
              _c("th", [_vm._v(_vm._s(_vm.__("End", "pm-pro")))]),
              _vm._v(" "),
              _c("th", [_vm._v(_vm._s(_vm.__("Total", "pm-pro")))])
            ])
          ]),
          _vm._v(" "),
          _c(
            "tbody",
            [
              _vm._l(_vm.timeLogs, function(time, index) {
                return time.showStatus
                  ? _c("tr", { key: index }, [
                      _c("td", { class: _vm.timeLogTd(time) }, [
                        _c("div", { staticClass: "user-info-td" }, [
                          _c("span", {
                            class: time.class + " table-row-collups",
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.expandLogs(time)
                              }
                            }
                          }),
                          _vm._v(" "),
                          _c("img", {
                            staticClass: "user-image",
                            attrs: { src: time.user.data.avatar_url }
                          }),
                          _vm._v(" "),
                          _c("span", [
                            _vm._v(_vm._s(time.user.data.display_name))
                          ])
                        ])
                      ]),
                      _vm._v(" "),
                      _c("td", { class: _vm.timeLogTd(time) }, [
                        _c(
                          "span",
                          {
                            attrs: {
                              title: _vm.getFullDate(
                                time.start.date + " " + time.start.time
                              )
                            }
                          },
                          [
                            _vm._v(
                              _vm._s(_vm.getFullDateCustom(time.start.date))
                            )
                          ]
                        )
                      ]),
                      _vm._v(" "),
                      _c("td", { class: _vm.timeLogTd(time) }, [
                        _c(
                          "span",
                          {
                            attrs: {
                              title: _vm.getFullDate(
                                time.stop.date + " " + time.stop.time
                              )
                            }
                          },
                          [
                            _vm._v(
                              _vm._s(_vm.getFullDateCustom(time.stop.date))
                            )
                          ]
                        ),
                        _vm._v(" "),
                        time.lastElement
                          ? _c("div", { staticClass: "total-text-row" }, [
                              _vm._v(_vm._s(_vm.__("Total", "pm-pro")))
                            ])
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("td", { class: _vm.timeLogTd(time) }, [
                        _c("div", { staticClass: "pm-flex" }, [
                          _c("span", { staticClass: "delete-span" }, [
                            _vm._v(
                              _vm._s(time.total.hour) +
                                ":" +
                                _vm._s(time.total.minute) +
                                ":" +
                                _vm._s(time.total.second)
                            )
                          ]),
                          _vm._v(" "),
                          _vm.canDeleteTime(time)
                            ? _c("span", {
                                staticClass: "icon-pm-delete",
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    return _vm.selfTimeLogeDelete(time)
                                  }
                                }
                              })
                            : _vm._e()
                        ]),
                        _vm._v(" "),
                        time.lastElement
                          ? _c("div", { staticClass: "total-text-row" }, [
                              _vm._v(_vm._s(_vm.getHourMinuteSecond(time)))
                            ])
                          : _vm._e()
                      ])
                    ])
                  : _vm._e()
              }),
              _vm._v(" "),
              _c("tr", [
                _c("td", { attrs: { colspan: "4" } }, [
                  _c("div", { staticClass: "net-total" }, [
                    _c("span", [_vm._v(_vm._s(_vm.__("Net Total", "pm-pro")))]),
                    _vm._v(" "),
                    _c("span", { staticClass: "net-total-time" }, [
                      _vm._v(_vm._s(_vm.netTimeGenerator(_vm.timeLogs)))
                    ])
                  ])
                ])
              ])
            ],
            2
          )
        ]),
        _vm._v(" "),
        _vm.canAddTime(_vm.actionData)
          ? _c(
              "div",
              [
                _vm.customTimeForm
                  ? _c("custom-time-form", {
                      attrs: { actionData: _vm.actionData }
                    })
                  : _vm._e()
              ],
              1
            )
          : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-95c973ac", esExports)
  }
}

/***/ }),
/* 138 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_estimated_time_field_vue__ = __webpack_require__(47);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_31efe191_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_estimated_time_field_vue__ = __webpack_require__(141);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(139)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_estimated_time_field_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_31efe191_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_estimated_time_field_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/estimated-time-field.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-31efe191", Component.options)
  } else {
    hotAPI.reload("data-v-31efe191", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 139 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(140);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("7e0bae54", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-31efe191\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./estimated-time-field.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-31efe191\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./estimated-time-field.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 140 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.task-estimation-arae {\n  position: relative;\n}\n.task-estimation-arae .est-btn {\n  color: #d4d6d6;\n}\n.task-estimation-arae .est-field-wrap {\n  top: 31px !important;\n  padding: 8px !important;\n  width: auto !important;\n}\n.task-estimation-arae .est-field-wrap .label {\n  line-height: 1;\n  display: block;\n  font-size: 11px;\n  margin-bottom: 6px;\n  white-space: nowrap;\n}\n.task-estimation-arae .est-field-wrap .field {\n  display: block;\n  height: 24px;\n  width: 87px;\n}\n.task-estimation-arae .est-field-wrap .time-wrap {\n  display: flex;\n  align-items: center;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .minute {\n  margin-left: 5px;\n}\n", ""]);

// exports


/***/ }),
/* 141 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.isActiveTaskEstimatedTime()
    ? _c(
        "div",
        {
          directives: [
            {
              name: "pm-click-outside",
              rawName: "v-pm-click-outside",
              value: _vm.closePopUp,
              expression: "closePopUp"
            }
          ],
          staticClass: "task-estimation-arae"
        },
        [
          _c(
            "a",
            {
              staticClass: "est-btn",
              attrs: { href: "" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  _vm.showPopUp = true
                }
              }
            },
            [
              _c("span", {
                staticClass: "pm-icon flaticon-clock pm-estimate-icon"
              })
            ]
          ),
          _vm._v(" "),
          _vm.showPopUp
            ? _c("div", { staticClass: "pm-triangle-top est-field-wrap" }, [
                _c(
                  "label",
                  { staticClass: "label", attrs: { for: "task-est" } },
                  [_vm._v(_vm._s(_vm.__("Estimated Time", "pm-pro")) + " ")]
                ),
                _vm._v(" "),
                _c("div", { staticClass: "time-wrap" }, [
                  _c("div", { staticClass: "hour" }, [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.actionData.estimation,
                          expression: "actionData.estimation"
                        }
                      ],
                      staticClass: "field",
                      attrs: {
                        type: "text",
                        placeholder: _vm.__("hh:mm", "pm-pro"),
                        id: "task-est"
                      },
                      domProps: { value: _vm.actionData.estimation },
                      on: {
                        input: [
                          function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.$set(
                              _vm.actionData,
                              "estimation",
                              $event.target.value
                            )
                          },
                          _vm.validation
                        ]
                      }
                    })
                  ])
                ])
              ])
            : _vm._e()
        ]
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-31efe191", esExports)
  }
}

/***/ }),
/* 142 */,
/* 143 */,
/* 144 */,
/* 145 */,
/* 146 */,
/* 147 */,
/* 148 */,
/* 149 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_subtask_estimation_time_field_vue__ = __webpack_require__(51);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7a929604_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_subtask_estimation_time_field_vue__ = __webpack_require__(156);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(150)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_subtask_estimation_time_field_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7a929604_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_subtask_estimation_time_field_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/subtask-estimation-time-field.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7a929604", Component.options)
  } else {
    hotAPI.reload("data-v-7a929604", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 150 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(151);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("7ad4130d", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7a929604\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./subtask-estimation-time-field.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7a929604\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./subtask-estimation-time-field.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 151 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.task-estimation-arae {\n  position: relative;\n}\n.task-estimation-arae .est-btn {\n  color: #d4d6d6;\n}\n.task-estimation-arae .est-btn .pm-estimate-icon:before {\n  font-size: 13px !important;\n}\n.task-estimation-arae .est-field-wrap {\n  top: 31px !important;\n  padding: 8px !important;\n  width: auto !important;\n}\n.task-estimation-arae .est-field-wrap .label {\n  line-height: 1;\n  display: block;\n  font-size: 12px;\n  margin-bottom: 6px;\n}\n.task-estimation-arae .est-field-wrap .field {\n  display: block;\n  height: 24px;\n  width: 82px;\n}\n.task-estimation-arae .est-field-wrap .time-wrap {\n  display: flex;\n  align-items: center;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .minute {\n  margin-left: 5px;\n}\n", ""]);

// exports


/***/ }),
/* 152 */,
/* 153 */,
/* 154 */,
/* 155 */,
/* 156 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      directives: [
        {
          name: "pm-click-outside",
          rawName: "v-pm-click-outside",
          value: _vm.closePopUp,
          expression: "closePopUp"
        }
      ],
      staticClass: "task-estimation-arae"
    },
    [
      _c(
        "a",
        {
          directives: [{ name: "pm-tooltip", rawName: "v-pm-tooltip" }],
          staticClass: "est-btn",
          attrs: { href: "", title: _vm.__("Estimated Time", "pm-pro") },
          on: {
            click: function($event) {
              $event.preventDefault()
              _vm.showPopUp = true
            }
          }
        },
        [_c("span", { staticClass: "pm-icon flaticon-clock pm-estimate-icon" })]
      ),
      _vm._v(" "),
      _vm.showPopUp
        ? _c("div", { staticClass: "pm-triangle-top est-field-wrap" }, [
            _c("label", { staticClass: "label", attrs: { for: "task-est" } }, [
              _vm._v(_vm._s(_vm.__("Estimated Time", "pm-pro")) + " ")
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "time-wrap" }, [
              _c("div", { staticClass: "hour" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.time,
                      expression: "time"
                    }
                  ],
                  staticClass: "field",
                  attrs: {
                    type: "text",
                    placeholder: _vm.__("hh:mm", "pm-pro"),
                    id: "task-est"
                  },
                  domProps: { value: _vm.time },
                  on: {
                    input: [
                      function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.time = $event.target.value
                      },
                      _vm.validation
                    ]
                  }
                })
              ])
            ])
          ])
        : _vm._e()
    ]
  )
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-7a929604", esExports)
  }
}

/***/ }),
/* 157 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_report_summery_vue__ = __webpack_require__(53);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_cb5cb812_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_report_summery_vue__ = __webpack_require__(158);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_report_summery_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_cb5cb812_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_report_summery_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/report-summery.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-cb5cb812", Component.options)
  } else {
    hotAPI.reload("data-v-cb5cb812", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 158 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-card pm-card-default card-milestone-tasks" },
    [
      _c("img", {
        attrs: {
          src: __webpack_require__(159),
          height: "50"
        }
      }),
      _vm._v(" "),
      _c("br"),
      _vm._v(" "),
      _c("h3", [_vm._v(" " + _vm._s(_vm.__("Summary", "pm-pro")) + " ")]),
      _vm._v(" "),
      _c("div", {
        staticClass: "project-meta-text",
        domProps: {
          innerHTML: _vm._s(
            _vm.__(
              "Browse   <strong>tasks</strong> reports according to <strong>Milestones</strong> (CSV exportable).",
              "pm-pro"
            )
          )
        }
      }),
      _vm._v(" "),
      _c(
        "router-link",
        {
          staticClass: "pm--btn pm--btn-default",
          attrs: { to: { name: "report_summary" }, tag: "a" }
        },
        [
          _c("i", { staticClass: "flaticon-eye" }),
          _vm._v(
            "\n        " +
              _vm._s(_vm.__("View Full Report", "pm-pro")) +
              "\n    "
          )
        ]
      )
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
    require("vue-hot-reload-api")      .rerender("data-v-cb5cb812", esExports)
  }
}

/***/ }),
/* 159 */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__.p + "taskby_milestone.svg?40505893bf2b43973c0759910313222b";

/***/ }),
/* 160 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_task_estimation_form_vue__ = __webpack_require__(54);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_17f4f26d_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_task_estimation_form_vue__ = __webpack_require__(163);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(161)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_task_estimation_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_17f4f26d_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_task_estimation_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/single-task-estimation-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-17f4f26d", Component.options)
  } else {
    hotAPI.reload("data-v-17f4f26d", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 161 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(162);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("2d88fed7", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-17f4f26d\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-task-estimation-form.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-17f4f26d\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-task-estimation-form.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 162 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-task-estimation-field-wrap {\n  margin: 0 10px;\n}\n.pm-pro-task-estimation-field-wrap .pm-estimate-icon {\n  cursor: pointer;\n}\n.pm-pro-task-estimation-field-wrap .pm-estimate-icon:before {\n  font-size: 14px !important;\n  color: #d4d6d6;\n}\n.pm-pro-task-estimation-field-wrap .pm-estimate-icon:hover:before {\n  color: #444;\n}\n.task-estimation-arae {\n  position: relative;\n}\n.task-estimation-arae .est-btn {\n  color: #d4d6d6;\n}\n.task-estimation-arae .est-field-wrap {\n  top: 21px !important;\n  padding: 8px !important;\n  width: auto !important;\n}\n.task-estimation-arae .est-field-wrap .label {\n  line-height: 1;\n  display: block;\n  font-size: 11px;\n  margin-bottom: 6px;\n  white-space: nowrap;\n}\n.task-estimation-arae .est-field-wrap .field {\n  display: block;\n  height: 24px;\n  width: 112px;\n  border-radius: 2px 0 0 2px;\n  min-height: 30px;\n}\n.task-estimation-arae .est-field-wrap .time-wrap {\n  display: flex;\n  align-items: center;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .hour {\n  position: relative;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .minute {\n  margin-left: 5px;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .btn-wrap {\n  text-decoration: none;\n  position: absolute;\n  right: 0;\n  top: 0;\n  display: flex;\n  padding: 14px 8px;\n  background: #d5d7d7;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .btn-wrap:hover {\n  background: #bdbdbd;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .btn-wrap:hover .flaticon-pm-enter:before {\n  background: #bdbdbd;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .btn-wrap .flaticon-pm-enter:before {\n  background: #d5d7d7;\n  color: #fff;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .btn-wrap .pm-circle-spinner {\n  background: #d5d7d7;\n  position: absolute;\n  left: 66px;\n  top: 0px;\n  padding: 6px 3px 5px 0px;\n}\n.task-estimation-arae .est-field-wrap .time-wrap .btn-wrap .pm-circle-spinner:after {\n  height: 0.6em;\n  width: 0.6em;\n  border-color: #fff #fff #fff transparent;\n}\n", ""]);

// exports


/***/ }),
/* 163 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.isActiveTaskEstimatedTime()
    ? _c(
        "div",
        {
          directives: [
            { name: "pm-tooltip", rawName: "v-pm-tooltip" },
            {
              name: "pm-click-outside",
              rawName: "v-pm-click-outside",
              value: _vm.closePopUp,
              expression: "closePopUp"
            }
          ],
          staticClass:
            "group-icon pm-pro-task-inline pm-pro-task-estimation-field-wrap task-estimation-arae",
          attrs: {
            title: !_vm.showPopUp ? _vm.__("Estimated Time", "pm-pro") : ""
          }
        },
        [
          _c("span", {
            staticClass: "pm-icon flaticon-clock pm-estimate-icon",
            on: {
              click: function($event) {
                $event.preventDefault()
                _vm.showPopUp = true
              }
            }
          }),
          _vm._v(" "),
          _vm.showPopUp
            ? _c("div", { staticClass: "pm-triangle-top est-field-wrap" }, [
                _c(
                  "label",
                  { staticClass: "label", attrs: { for: "task-est" } },
                  [_vm._v(_vm._s(_vm.__("Estimated Time", "pm-pro")) + " ")]
                ),
                _vm._v(" "),
                _c("div", { staticClass: "time-wrap" }, [
                  _c("div", { staticClass: "hour" }, [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.time,
                          expression: "time"
                        }
                      ],
                      staticClass: "field",
                      attrs: {
                        type: "text",
                        placeholder: _vm.__("hh:mm", "pm-pro"),
                        id: "task-est"
                      },
                      domProps: { value: _vm.time },
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
                          return _vm.saveEstimation()
                        },
                        input: [
                          function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.time = $event.target.value
                          },
                          _vm.validation
                        ]
                      }
                    }),
                    _vm._v(" "),
                    _c(
                      "a",
                      {
                        staticClass: "btn-wrap",
                        attrs: { href: "#" },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.saveEstimation()
                          }
                        }
                      },
                      [
                        _c("span", { staticClass: "flaticon-pm-enter" }),
                        _vm._v(" "),
                        _vm.isLoading
                          ? _c("span", { staticClass: "pm-circle-spinner" })
                          : _vm._e()
                      ]
                    )
                  ])
                ])
              ])
            : _vm._e()
        ]
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-17f4f26d", esExports)
  }
}

/***/ }),
/* 164 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/**
 * Required jQuery methods
 *
 * @type Object
 */
var subTaskType = {
    chart: function chart(el, binding, vnode) {
        var activity = vnode.context.__('Activity', 'wedevs-project-manager'),
            Task = vnode.context.__('Task', 'wedevs-project-manager');
        var data = {
            labels: subTaskType.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: subTaskType.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: __('Subtask Count', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: subTaskType.getCount(vnode.context),
                backgroundColor: "#4bc0c0"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            min: 0
                            //stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: __('Hours', 'pm-pro')
                        }
                    }]
                },

                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];

        jQuery.each(self.reports.data, function (userId, report) {
            labels.push(report.type.title);
        });

        return labels;
    },
    getCount: function getCount(self) {
        var count = [];

        jQuery.each(self.reports.data, function (typeId, report) {
            count.push(report.total_incomplete_sub_tasks);
        });

        return count;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];

        jQuery.each(self.reports.data, function (typeId, report) {
            estimatedHours.push(report.total_estimation_hours);
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('sub_task_type', {
    inserted: function inserted(el, binding, vnode) {
        subTaskType.chart(el, binding, vnode);
    }

    // update: function (el, binding, vnode) {
    //     subTaskType.chart(el, binding, vnode);
    // }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            grapVisibility: true
        };
    },


    watch: {
        reports: {
            handler: function handler(recent, old) {
                var _this = this;

                this.grapVisibility = false;

                setTimeout(function () {
                    _this.grapVisibility = true;
                }, 100);
            },


            deep: true
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 165 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/**
 * Required jQuery methods
 *
 * @type Object
 */
var taskType = {
    chart: function chart(el, binding, vnode) {
        var activity = vnode.context.__('Activity', 'wedevs-project-manager'),
            Task = vnode.context.__('Task', 'wedevs-project-manager');
        var data = {
            labels: taskType.getLabels(vnode.context),
            datasets: [{
                label: __('Est. Hours', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#f77726",
                strokeColor: "#f77726",
                pointColor: "#f77726",
                pointStrokeColor: "#f77726",
                pointHighlightFill: "#f77726",
                pointHighlightStroke: "#f77726",
                data: taskType.getEstimatedHours(vnode.context),
                backgroundColor: "#f77726"
            }, {
                label: __('Task Count', 'pm-pro'),
                borderWidth: 1,
                fillColor: "#4bc0c0",
                strokeColor: "#4bc0c0",
                pointColor: "#4bc0c0",
                pointStrokeColor: "#4bc0c0",
                pointHighlightFill: "#4bc0c0",
                pointHighlightStroke: "#4bc0c0",
                data: taskType.getCount(vnode.context),
                backgroundColor: "#4bc0c0"
            }]
        };

        Chart.defaults.global.responsive = true;
        var ctx = el.getContext("2d");
        // This will get the first returned node in the jQuery collection.
        var pmChart = new pm.Chart(ctx, {
            type: 'horizontalBar',
            data: data,
            options: {
                scales: {
                    xAxes: [{
                        ticks: {
                            min: 0
                            //stepSize: 1
                        },
                        scaleLabel: {
                            display: true,
                            labelString: __('Hours', 'pm-pro')
                        }
                    }]
                },

                plugins: {
                    datalabels: {

                        anchor: function anchor(context) {
                            return 'end';
                        },
                        align: function align(context) {
                            return 'center';
                        },
                        backgroundColor: function backgroundColor(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: function color(context) {
                            return 'white';
                        },
                        font: {
                            weight: 'normal'
                        },
                        formatter: Math.round
                    }
                }
            },
            pointDotRadius: 8,
            animationSteps: 60,
            tooltipTemplate: "<%= labels + sss %>%",
            animationEasing: "easeOutQuart"
        });
    },

    getLabels: function getLabels(self) {
        var labels = [];

        jQuery.each(self.reports.data, function (userId, report) {
            labels.push(report.type.title);
        });

        return labels;
    },
    getCount: function getCount(self) {
        var count = [];

        jQuery.each(self.reports.data, function (typeId, report) {
            count.push(report.total_incomplete_tasks);
        });

        return count;
    },
    getEstimatedHours: function getEstimatedHours(self) {
        var estimatedHours = [];

        jQuery.each(self.reports.data, function (typeId, report) {
            estimatedHours.push(report.total_estimation_hours);
        });

        return estimatedHours;
    }
};

// Register a global custom directive called v-pm-sortable
pm.Vue.directive('task_type', {
    inserted: function inserted(el, binding, vnode) {
        taskType.chart(el, binding, vnode);
    }
});

/* harmony default export */ __webpack_exports__["a"] = ({
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    props: {
        reports: {
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            grapVisibility: true
        };
    },


    watch: {
        reports: {
            handler: function handler(recent, old) {
                var _this = this;

                this.grapVisibility = false;

                setTimeout(function () {
                    _this.grapVisibility = true;
                }, 100);
            },


            deep: true
        }
    },

    created: function created() {},


    methods: {
        secondToTime: function secondToTime(second) {
            var time = this.secondsToHms(second);

            return time.hour + ':' + time.minute;
        }
    }
});

/***/ }),
/* 166 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_task_type_summary_vue__ = __webpack_require__(164);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_191aee18_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_task_type_summary_vue__ = __webpack_require__(169);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(167)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_task_type_summary_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_191aee18_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_task_type_summary_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/sub-task-type-summary.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-191aee18", Component.options)
  } else {
    hotAPI.reload("data-v-191aee18", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 167 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(168);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("2f48eb88", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-191aee18\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./sub-task-type-summary.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-191aee18\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./sub-task-type-summary.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 168 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.sub-task-type-summery .total-item-wrap {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  margin-bottom: 1em;\n}\n.sub-task-type-summery .total-item-wrap .summery {\n  flex: 1;\n}\n.sub-task-type-summery .total-item-wrap .total-items {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n}\n.sub-task-type-summery .total-item-wrap .total-items .total-item {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  padding: 10px 10px 10px 0;\n}\n.sub-task-type-summery .total-item-wrap .total-items .total-item:last-child {\n  padding-right: 0;\n}\n.sub-task-type-summery .total-item-wrap .total-items .total-item .label {\n  background: #3797a4;\n  padding: 0 5px;\n  color: #f9f9f9;\n  font-size: 12px;\n}\n.sub-task-type-summery .total-item-wrap .total-items .total-item .value {\n  background: #8ccbbe;\n  color: #555;\n  padding: 0 10px;\n  font-size: 12px;\n}\n.sub-task-type-summery .total-item-wrap .total-items .total-item .project-title:after {\n  content: \", \";\n}\n.sub-task-type-summery .total-item-wrap .total-items .total-item .project-title:last-child:after {\n  content: \"\";\n}\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 169 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "sub-task-type-summery" }, [
    _c(
      "div",
      {
        staticClass:
          "pm-card pm-card-default total-item-wrap pm-report-meta-panel pm-report-worker-panel"
      },
      [
        _vm._m(0),
        _vm._v(" "),
        _c("div", { staticClass: "summery" }, [
          _c("div", { staticClass: "total-items" }, [
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Total Estimation Hours")))
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "value" }, [
                _vm._v(_vm._s(_vm.reports.meta.total_estimation_hours))
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Total Subtasks", "pm-pro")))
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "value" }, [
                _vm._v(_vm._s(_vm.reports.meta.total_incomplete_sub_tasks))
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Projects", "pm-pro")))
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "value" },
                [
                  _vm._l(_vm.reports.projects.data, function(project) {
                    return [
                      _c("span", { staticClass: "project-title" }, [
                        _vm._v(_vm._s(project.title))
                      ])
                    ]
                  })
                ],
                2
              )
            ])
          ])
        ])
      ]
    ),
    _vm._v(" "),
    _vm.grapVisibility
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [{ name: "sub_task_type", rawName: "v-sub_task_type" }],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    _c("table", { staticClass: "wp-list-table widefat fixed striped" }, [
      _c("thead", [
        _c("th", [_vm._v(_vm._s(_vm.__("Subtask Type Name", "pm-pro")))]),
        _vm._v(" "),
        _c("th", [
          _vm._v(_vm._s(_vm.__("Incomplete Subtask Count", "pm-pro")))
        ]),
        _vm._v(" "),
        _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
      ]),
      _vm._v(" "),
      _c(
        "tbody",
        [
          _vm._l(_vm.reports.data, function(report, index) {
            return _c("tr", [
              _c("td", [_vm._v(_vm._s(report.type.title))]),
              _vm._v(" "),
              _c("td", [
                _vm._v(
                  "\n                    " +
                    _vm._s(report.total_incomplete_sub_tasks) +
                    "\n                "
                )
              ]),
              _vm._v(" "),
              _c("td", [
                _vm._v(
                  "\n                    " +
                    _vm._s(report.total_estimation_hours) +
                    "\n                "
                )
              ])
            ])
          }),
          _vm._v(" "),
          _c("tr", [
            _c("td", [_vm._v(_vm._s(_vm.__("Total", "pm-pro")))]),
            _vm._v(" "),
            _c("td", [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.reports.meta.total_incomplete_sub_tasks) +
                  "\n                "
              )
            ]),
            _vm._v(" "),
            _c("td", [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.reports.meta.total_estimation_hours) +
                  "\n                "
              )
            ])
          ]),
          _vm._v(" "),
          false
            ? _c("tr", [
                _c("td", { attrs: { colspan: "3" } }, [
                  _vm._v(_vm._s(_vm.__("No results found!", "pm-pro")))
                ])
              ])
            : _vm._e()
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "pm-report-panel-icon" }, [
      _c("i", { staticClass: "flaticon-resume" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-191aee18", esExports)
  }
}

/***/ }),
/* 170 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_type_summary_vue__ = __webpack_require__(165);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0a5c1ae5_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_type_summary_vue__ = __webpack_require__(173);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(171)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_type_summary_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_0a5c1ae5_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_type_summary_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/time_tracker/views/assets/src/components/task-type-summary.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-0a5c1ae5", Component.options)
  } else {
    hotAPI.reload("data-v-0a5c1ae5", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 171 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(172);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("4322b968", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0a5c1ae5\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-type-summary.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-0a5c1ae5\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-type-summary.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 172 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.task-type-summery .total-item-wrap {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  margin-bottom: 1em;\n}\n.task-type-summery .total-item-wrap .summery {\n  flex: 1;\n}\n.task-type-summery .total-item-wrap .total-items {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n}\n.task-type-summery .total-item-wrap .total-items .total-item {\n  display: flex;\n  align-items: center;\n  flex-wrap: wrap;\n  padding: 10px 10px 10px 0;\n}\n.task-type-summery .total-item-wrap .total-items .total-item:last-child {\n  padding-right: 0;\n}\n.task-type-summery .total-item-wrap .total-items .total-item .label {\n  background: #3797a4;\n  padding: 0 5px;\n  color: #f9f9f9;\n  font-size: 12px;\n}\n.task-type-summery .total-item-wrap .total-items .total-item .value {\n  background: #8ccbbe;\n  color: #555;\n  padding: 0 10px;\n  font-size: 12px;\n}\n.task-type-summery .total-item-wrap .total-items .total-item .project-title:after {\n  content: \", \";\n}\n.task-type-summery .total-item-wrap .total-items .total-item .project-title:last-child:after {\n  content: \"\";\n}\n.pm-pro-graph-wrap {\n  background: #fff;\n  padding: 10px;\n  margin-bottom: 20px;\n  border: 1px solid #e5e5e5;\n}\n", ""]);

// exports


/***/ }),
/* 173 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "task-type-summery" }, [
    _c(
      "div",
      {
        staticClass:
          "pm-card total-item-wrap pm-card-default pm-report-meta-panel pm-report-worker-panel"
      },
      [
        _vm._m(0),
        _vm._v(" "),
        _c("div", { staticClass: "summery" }, [
          _c("div", { staticClass: "total-items" }, [
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Total Estimation Hours")))
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "value" }, [
                _vm._v(_vm._s(_vm.reports.meta.total_estimation_hours))
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Total Tasks", "pm-pro")))
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "value" }, [
                _vm._v(_vm._s(_vm.reports.meta.total_incomplete_tasks))
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "total-item" }, [
              _c("div", { staticClass: "label" }, [
                _vm._v(_vm._s(_vm.__("Projects", "pm-pro")))
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "value" },
                [
                  _vm._l(_vm.reports.projects.data, function(project) {
                    return [
                      _c("span", { staticClass: "project-title" }, [
                        _vm._v(_vm._s(project.title))
                      ])
                    ]
                  })
                ],
                2
              )
            ])
          ])
        ])
      ]
    ),
    _vm._v(" "),
    _vm.grapVisibility
      ? _c("div", { staticClass: "pm-pro-graph-wrap" }, [
          _c("canvas", {
            directives: [{ name: "task_type", rawName: "v-task_type" }],
            staticStyle: { width: "819px", height: "328px" },
            attrs: { width: "1638", height: "656" }
          })
        ])
      : _vm._e(),
    _vm._v(" "),
    _c("table", { staticClass: "wp-list-table widefat fixed striped" }, [
      _c("thead", [
        _c("th", [_vm._v(_vm._s(_vm.__("Task Type Name", "pm-pro")))]),
        _vm._v(" "),
        _c("th", [_vm._v(_vm._s(_vm.__("Incomplete Task Count", "pm-pro")))]),
        _vm._v(" "),
        _c("th", [_vm._v(_vm._s(_vm.__("Est. Hour", "pm-pro")))])
      ]),
      _vm._v(" "),
      _c(
        "tbody",
        [
          _vm._l(_vm.reports.data, function(report, index) {
            return _c("tr", [
              _c("td", [_vm._v(_vm._s(report.type.title))]),
              _vm._v(" "),
              _c("td", [
                _vm._v(
                  "\n                    " +
                    _vm._s(report.total_incomplete_tasks) +
                    "\n                "
                )
              ]),
              _vm._v(" "),
              _c("td", [
                _vm._v(
                  "\n                    " +
                    _vm._s(report.total_estimation_hours) +
                    "\n                "
                )
              ])
            ])
          }),
          _vm._v(" "),
          _c("tr", [
            _c("td", [_vm._v(_vm._s(_vm.__("Total", "pm-pro")))]),
            _vm._v(" "),
            _c("td", [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.reports.meta.total_incomplete_tasks) +
                  "\n                "
              )
            ]),
            _vm._v(" "),
            _c("td", [
              _vm._v(
                "\n                    " +
                  _vm._s(_vm.reports.meta.total_estimation_hours) +
                  "\n                "
              )
            ])
          ]),
          _vm._v(" "),
          false
            ? _c("tr", [
                _c("td", { attrs: { colspan: "3" } }, [
                  _vm._v(_vm._s(_vm.__("No results found!", "pm-pro")))
                ])
              ])
            : _vm._e()
        ],
        2
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "pm-report-panel-icon" }, [
      _c("i", { staticClass: "flaticon-resume" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-0a5c1ae5", esExports)
  }
}

/***/ })
/******/ ]);