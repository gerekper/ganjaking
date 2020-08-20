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
/******/ 	return __webpack_require__(__webpack_require__.s = 17);
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

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    data: function data() {
        return {};
    },
    created: function created() {},

    computed: {
        canCraeteTask: function canCraeteTask() {
            return this.user_can('create_task');
        }
    },
    methods: {
        canCraeteSubTask: function canCraeteSubTask(task) {
            if (this.isArchivedTaskList(task)) {
                return false;
            }
            var user = PM_Vars.current_user;
            if (this.is_manager()) {
                return true;
            }

            if (this.user_can('create_task')) {
                if (task.creator.data.id == user.ID) {
                    return true;
                }
                if (this.is_user_assigneed(task, user.ID)) {
                    return true;
                }
            }

            return false;
        },
        canEditSubTask: function canEditSubTask(subtask) {
            if (this.isArchivedTaskList(this.actionData.task)) {
                return false;
            }
            var user = PM_Vars.current_user;
            if (this.is_manager()) {
                return true;
            }

            if (subtask.creator.data.id == user.ID) {
                return true;
            }

            return false;
        },
        isArchivedTaskList: function isArchivedTaskList(task) {
            if (typeof task.task_list !== 'undefined') {
                if (task.task_list.data.status === 'archived') {
                    return true;
                }
            }
            return false;
        },
        is_user_assigneed: function is_user_assigneed(task, user_id) {
            return task.assignees.data.findIndex(function (user) {
                return user.id == user_id;
            }) !== -1;
        },
        showHideSubTaskForm: function showHideSubTaskForm(task, status, subTasks) {
            status = status || 'toggle';
            subTasks = subTasks || {};
            if (typeof subTasks.edit_mode != 'undefined') {
                subTasks.edit_mode = subTasks.edit_mode ? false : true;
            }

            var isListSingleTask = this.$store.state.projectTaskLists.is_single_task;

            if (!isListSingleTask && (this.$route.name == 'task_lists' || this.$route.name == 'single_list')) {
                this.$store.commit('subTasks/formVisibility', { task: task, status: status });
            }
        },
        selfShowHideSubTaskForm: function selfShowHideSubTaskForm(task, status, subTasks) {

            var taskId = this.$route.params.task_id ? true : false;
            status = status || 'toggle';
            subTasks = subTasks || {};

            this.localSubTaskFrom.status = this.localSubTaskFrom.status ? false : true;
            this.showHideSubTaskForm(task, status, subTasks);
        },
        hasPermission: function hasPermission(task) {
            return true;
            var get_current_user_id = PM_Vars.current_user.ID,
                in_task = task.assignees.data.indexOf(get_current_user_id);

            if (in_task != '-1') {
                return true;
            }

            return false;
        },
        getListsTask: function getListsTask(actionData) {
            if (typeof actionData.list == 'undefined') {
                return false;
            }
            if (typeof actionData.task == 'undefined') {
                return false;
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


        /**
         * Retrive All task list
         * @param  {[object]}   args SSR url condition
         * @param  {Function} callback  [description]
         * @return {[void]}             [description]
         */
        getSubTasks: function getSubTasks(args) {
            var self = this,
                pre_define = {
                data: {
                    project_id: this.project_id
                },
                condition: {
                    per_page: 1000,
                    page: 1
                },
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var condition = this.generateConditions(args.condition);
            var request = {
                url: self.base_url + '/pm-pro/v2/tasks/' + args.task.id + '/sub-tasks',
                data: args.data,
                success: function success(res) {
                    res.data.map(function (list, index) {
                        self.addSubTaskMeta(list);
                    });

                    //self.$store.commit('subTasks/setSubTasks', {task: args.task, subTasks: res.data });

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                }
            };
            self.httpRequest(request);
        },


        /**
         * Insert  task
         *
         * @return void
         */
        addSubTask: function addSubTask(args) {
            var self = this,
                pre_define = {
                data: {
                    project_id: self.project_id
                },
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/tasks/' + args.data.task_id + '/sub-tasks/',
                type: 'POST',
                data: args.data,
                success: function success(res) {
                    self.addSubTaskMeta(res.data);
                    // self.$store.commit( 'subTasks/afterNewSubTask',
                    //     {
                    //         task: self.task,
                    //         subTask: res.data
                    //     }
                    // );

                    // Display a success toast, with a title
                    pm.Toastr.success(res.message);
                    self.selfShowHideSubTaskForm(self.task, false);
                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                },
                error: function error(res) {
                    // Showing error
                    if (res.status == 500) {
                        res.responseJSON.message.map(function (value, index) {
                            pm.Toastr.error(value);
                        });
                    }
                    if (res.status == 400) {
                        var params = res.responseJSON.data.params;
                        for (var obj in params) {
                            pm.Toastr.error(params[obj][0]);
                        }
                    }

                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                }
            };

            self.httpRequest(request_data);
        },


        /**
         * Update Task using Task object
         * @param  {Object} task Task Object
         * @return {void}      Update a task
         */
        updateSubTask: function updateSubTask(args) {
            var self = this,
                pre_define = {
                data: {
                    project_id: self.project_id
                },
                callback: false
            };
            var args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/tasks/' + args.data.task_id + '/sub-tasks/' + args.data.sub_task_id + '/update',
                type: 'POST',
                data: args.data,
                success: function success(res) {
                    self.addSubTaskMeta(res.data);
                    pm.Toastr.success(res.message);
                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                },
                error: function error(res) {
                    // Showing error
                    if (res.status == 500) {
                        res.responseJSON.message.map(function (value, index) {
                            pm.Toastr.error(value);
                        });
                    }
                    if (res.status == 400) {
                        var params = res.responseJSON.data.params;
                        for (var obj in params) {
                            pm.Toastr.error(params[obj][0]);
                        }
                    }

                    if (typeof args.callback === 'function') {
                        args.callback.call(self, res);
                    }
                }
            };

            self.httpRequest(request_data);
        },
        addSubTaskMeta: function addSubTaskMeta(subTask) {
            subTask.edit_mode = false;
            subTask.show_spinner = false;
        },


        /**
        * Mark task done and undone
        *
        * @param  int  task_id
        * @param  Boolean is_checked
        * @param  int  task_index
        *
        * @return void
        */
        subTaskDoneUndone: function subTaskDoneUndone(args) {
            var self = this,
                pre_define = {
                data: {
                    project_id: self.project_id
                },
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/tasks/' + args.data.parent_id + '/sub-tasks/' + args.data.sub_task_id + '/update',
                type: 'POST',
                data: args.data,
                success: function success(res) {
                    self.addSubTaskMeta(res.data);
                    //self.$store.commit( 'subTasks/afterDoneUndoneSubTask', res.data);
                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                }
            };
            self.httpRequest(request_data);
        },
        deleteSubTask: function deleteSubTask(args) {
            if (!confirm(this.text.are_you_sure)) {
                return;
            }

            var self = this,
                pre_define = {
                data: {
                    project_id: this.project_id
                },
                callback: false
            };
            var args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/tasks/' + args.task_id + '/sub-tasks/' + args.sub_task_id + '/delete',
                data: args.data,
                type: 'POST',
                success: function success(res) {
                    self.$store.commit('subTasks/afterDeleteSubTask', {
                        sub_task_id: args.sub_task_id,
                        task_id: args.task_id,
                        list_id: args.list_id
                    });

                    if (typeof args.callback === 'function') {
                        args.callback(res);
                    }
                }
            };

            this.httpRequest(request_data);
        },
        subtaskOrder: function subtaskOrder(receive, callback) {
            var self = this;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/sub-tasks/sorting',
                type: 'POST',
                data: receive,
                success: function success(res) {
                    pm.Toastr.success(res.message);
                    if (typeof callback === 'function') {
                        callback.call(self, res);
                    }
                },
                error: function error(res) {}
            };
            self.httpRequest(request_data);
        }
    }
};

/***/ }),
/* 2 */
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
/* 3 */
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

var listToStyles = __webpack_require__(32)

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
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_new_subtask_form_vue__ = __webpack_require__(11);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2a620912_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_new_subtask_form_vue__ = __webpack_require__(33);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_new_subtask_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_2a620912_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_new_subtask_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/new-subtask-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-2a620912", Component.options)
  } else {
    hotAPI.reload("data-v-2a620912", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__new_subtask_button_vue__ = __webpack_require__(21);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__show_subtask_button_vue__ = __webpack_require__(23);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin__ = __webpack_require__(1);
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





/* harmony default export */ __webpack_exports__["a"] = ({
	props: {
		actionData: {
			type: Object,
			default: function _default() {
				return {};
			}
		}
	},
	mixins: [__WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin___default.a],
	components: {
		'new-subtask-button': __WEBPACK_IMPORTED_MODULE_0__new_subtask_button_vue__["a" /* default */],
		'show-subtask-button': __WEBPACK_IMPORTED_MODULE_1__show_subtask_button_vue__["a" /* default */]
	},
	created: function created() {
		this.actionData.task.subTaskForm = false;
	},


	methods: {}
});

/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["a"] = ({
	props: {
		task: {
			type: Object,
			default: function _default() {
				return {};
			}
		}
	},
	data: function data() {
		return {
			new_subtask: __('New sub task', 'pm-pro')
		};
	},

	mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
	computed: {
		is_single_task: function is_single_task() {
			return this.$store.state.is_single_task;
		}
	},

	methods: {
		selfShowHideSubTaskForm: function selfShowHideSubTaskForm(task) {
			this.showHideSubTaskForm(task);
		}
	}
});

/***/ }),
/* 7 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__);
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["a"] = ({
	props: {
		task: {
			type: Object,
			default: function _default() {
				return {};
			}
		}
	},
	data: function data() {
		return {
			show_sub_task: __('Show Subtask', 'pm-pro')
		};
	},

	mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
	computed: {
		is_single_task: function is_single_task() {
			return this.$store.state.is_single_task;
		}
	},
	methods: {
		getSelfSubtask: function getSelfSubtask(task) {
			var self = this;
			var args = {
				task: task,
				data: {
					project_id: task.project_id
				},
				callback: function callback(res) {
					self.task.sub_tasks = res.data;
					self.task.sub_task_content = self.task.sub_task_content ? false : true;
					//self.$store.commit('subTasks/showSubTaskContent', {task: task});
				}
			};

			this.getSubTasks(args);
		}
	}
});

/***/ }),
/* 8 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
//
//

/* harmony default export */ __webpack_exports__["a"] = ({
    created: function created() {
        pmBus.$on('pm_after_update_task', this.afterUpdateSingelTaskUser);
    },


    methods: {
        afterUpdateSingelTaskUser: function afterUpdateSingelTaskUser(before, after) {}
    }
});

/***/ }),
/* 9 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__sub_task_lists_vue__ = __webpack_require__(29);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__new_subtask_form_vue__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin__ = __webpack_require__(1);
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





/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        actionData: {
            type: Object,
            default: function _default() {
                return {};
            }
        }
    },
    mixins: [__WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin___default.a],
    computed: {
        isSingleTask: function isSingleTask() {
            return this.$store.state.is_single_task;
        },
        subTaskForm: function subTaskForm() {
            var task = this.getListsTask(this.actionData);
            if (typeof task == 'undefined') {
                return false;
            }
            return task.new_sub_task_form;
        },
        subTaskContent: function subTaskContent() {
            var task = this.getListsTask(this.actionData);
            if (typeof task == 'undefined') {
                return false;
            }
            return task.sub_task_content;
        }
    },

    methods: {},
    components: {
        'sub-task-lists': __WEBPACK_IMPORTED_MODULE_0__sub_task_lists_vue__["a" /* default */],
        'new-subtask-form': __WEBPACK_IMPORTED_MODULE_1__new_subtask_form_vue__["a" /* default */]
    }
});

/***/ }),
/* 10 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__new_subtask_form_vue__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__ = __webpack_require__(1);
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




/* harmony default export */ __webpack_exports__["a"] = ({
				props: {
								actionData: {
												type: [Object, Array],
												default: function _default() {
																return {};
												}
								}
				},
				data: function data() {
								return {
												show_spinner: false
								};
				},

				mixins: [__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default.a],
				components: {
								'new-subtask-form': __WEBPACK_IMPORTED_MODULE_0__new_subtask_form_vue__["a" /* default */]
				},
				computed: {
								task_start_field: function task_start_field() {
												return this.$store.state.projectTaskLists.permissions.task_start_field;
								},
								isTaskActive: function isTaskActive() {
												return this.actionData.task.status ? false : true;
								},
								subTasks: function subTasks() {
												var task = this.actionData.task;
												if (!task) {
																return [];
												}
												return task.sub_tasks;
								},
								is_single_task_open: function is_single_task_open() {
												if (typeof this.actionData.is_single_task_open !== 'undefined') {
																return this.actionData.is_single_task_open;
												}
								}
				},

				methods: {
								/**
         * addTaskMeta for task edit mode
         * @param {[Object]} task [Task Object]
         */
								addTaskMeta: function addTaskMeta(task) {
												task.edit_mode = false;
								},
								makeAsTask: function makeAsTask(subtask, actionData) {
												var self = this;
												var request = {
																type: 'post',
																url: self.base_url + '/pm-pro/v2/tasks/' + subtask.parent_id + '/sub-tasks/' + subtask.id + '/make-task',
																data: {
																				list_id: subtask.task_list.data.id
																},
																success: function success(res) {

																				self.addTaskMeta(res.data);

																				self.$store.commit('subTasks/afterDeleteSubTask', {
																								sub_task_id: subtask.id,
																								task_id: subtask.parent_id,
																								list_id: subtask.task_list.data.id
																				});

																				self.$store.commit('projectTaskLists/afterNewTask', {
																								list_id: subtask.task_list.data.id,
																								task: res.data,
																								list: subtask.task_list.data
																				});

																				self.$store.commit('updateProjectMeta', 'total_activities');
																				// Display a success toast, with a title
																				//self.showHideTaskFrom(false, subtask.task_list.data, res.data );

																				//pmBus.$emit('pm_after_create_task', res);
																}
												};
												self.httpRequest(request);
								},
								isChecked: function isChecked(task) {
												return task.status ? 'checked' : '';
								},

								/**
            * WP settings date format convert to pm.Moment date format with time zone
            *
            * @param  string date
            *
            * @return string
            */
								dateFormat: function dateFormat(date) {
												if (!date) {
																return;
												}

												date = new Date(date);
												date = pm.Moment(date).format('YYYY-MM-DD');

												var format = 'MMMM DD YYYY';

												if (PM_Vars.wp_date_format == 'Y-m-d') {
																format = 'YYYY-MM-DD';
												} else if (PM_Vars.wp_date_format == 'm/d/Y') {
																format = 'MM/DD/YYYY';
												} else if (PM_Vars.wp_date_format == 'd/m/Y') {
																format = 'DD/MM/YYYY';
												}

												return pm.Moment(date).format(format);
								},

								/**
            * Showing (-) between task start date and due date
            *
            * @param  string  task_start_field
            * @param  string  start_date
            * @param  string  due_date
            *
            * @return Boolean
            */
								isBetweenDate: function isBetweenDate(task_start_field, start_date, due_date) {
												if (task_start_field && !start_date && !due_date) {
																return true;
												}
												return false;
								},

								/**
            * CSS class for task date
            *
            * @param  string start_date
            * @param  string due_date
            *
            * @return string
            */
								subTaskDateWrap: function subTaskDateWrap(due_date) {
												if (!due_date) {
																return false;
												}

												due_date = new Date(due_date);
												due_date = pm.Moment(due_date).format('YYYY-MM-DD');

												if (!pm.Moment(due_date).isValid()) {
																return false;
												}

												var today = pm.Moment().format('YYYY-MM-DD'),
												    due_day = pm.Moment(due_date).format('YYYY-MM-DD');
												return pm.Moment(today).isSameOrBefore(due_day) ? 'pm-current-date' : 'pm-due-date';
								},
								getUser: function getUser(userId) {
												userId = userId || this.$store.state.get_current_user_id;

												var usr = this.$store.state.project_users.find(function (user) {
																return user.id == userId;
												});

												return usr.name;
								},
								is_assigned: function is_assigned(subtask) {

												var get_current_user_id = this.$store.state.get_current_user_id,
												    in_task = subtask.assignees.data.indexOf(get_current_user_id);

												if (subtask.can_del_edit || in_task != '-1') {
																return true;
												}

												return false;
								},
								doneUndone: function doneUndone(subTask) {
												var self = this,
												    status = subTask.status == 'incomplete' ? 1 : 0;
												subTask.show_spinner = true;
												var args = {
																data: {
																				sub_task_id: subTask.id,
																				parent_id: subTask.parent_id,
																				status: status
																},
																callback: function callback(res) {
																				var subTaskIndex = self.getIndex(self.actionData.task.sub_tasks, subTask.id, 'id');
																				self.actionData.task.sub_tasks.splice(subTaskIndex, 1, res.data);

																				// self.$store.commit( 'projectTaskLists/afterTaskDoneUndone', {
																				//     status: status,
																				//     task: res.data,
																				//     list_id: self.list.id,
																				//     task_id: self.task.id
																				// });
																				subTask.show_spinner = false;
																}
												};

												this.subTaskDoneUndone(args);
								},


								/**
         * Delete task
         *
         * @return void
         */
								deleteSelfSubTask: function deleteSelfSubTask(subtask, task) {
												var self = this;
												var form_data = {
																sub_task_id: subtask.id,
																task_id: task.id,
																list_id: task.task_list.data.id,

																callback: function callback(res) {
																				var subTaskIndex = self.getIndex(self.actionData.task.sub_tasks, subtask.id, 'id');
																				self.actionData.task.sub_tasks.splice(subTaskIndex, 1);
																}
												};

												this.deleteSubTask(form_data);
								},
								editSubTask: function editSubTask(subtask) {
												subtask.edit_mode = true;
								}
				}
});

/***/ }),
/* 11 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(1);
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




/* harmony default export */ __webpack_exports__["a"] = ({
    // Get passing data for this component. Remember only array and objects are
    props: {
        task: {
            type: Object,
            default: function _default() {
                return {
                    assignees: {}
                };
            }
        },

        subTask: {
            type: Object,
            default: function _default() {
                return {
                    description: {},
                    start_at: { date: '' },
                    due_date: { date: '' },
                    assignees: { data: [] }
                };
            }
        },

        localSubTaskFrom: {
            type: Object,
            default: function _default() {
                return {
                    status: false
                };
            }
        }
    },
    beforeMount: function beforeMount() {
        this.setDefaultValue();
    },
    created: function created() {},


    /**
     * Initial data for this component
     *
     * @return obj
     */
    data: function data() {
        return {
            task_privacy: this.subTask.task_privacy == 'yes' ? true : false,
            submit_disabled: false,
            before_edit: jQuery.extend(true, {}, this.subTask),
            show_spinner: false,
            assigned_to: [],
            description: this.subTask.description.content,
            task_users: this.task.assignees.data,
            select_user_text: __('Select User', 'pm-pro'),
            update_sub_task: __('Update Sub Task', 'pm-pro'),
            new_sub_task: __('New sub task', 'pm-pro'),
            subtask_title: __('Sub task title', 'pm-pro'),
            task_start_date: __('Start Date', 'pm-pro'),
            task_due_date: __('Due Date', 'pm-pro')
        };
    },
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],

    components: {
        'multiselect': pm.Multiselect.Multiselect
    },

    computed: {
        project_users: function project_users() {
            return this.$root.$store.state.project_users;
        },
        subTaskStartField: function subTaskStartField() {
            return this.getSettings('task_start_field', false);
        },


        /**
         * Get and Set task users
         */
        sub_task_assign: {
            /**
             * Filter only current task assgin user from vuex state project_users
             *
             * @return array
             */
            get: function get() {
                return typeof this.subTask.assignees === 'undefined' ? [] : this.subTask.assignees.data;
            },

            /**
             * Set selected users at task insert or edit time
             *
             * @param array selected_users
             */
            set: function set(selected_users) {
                this.subTask.assignees.data = selected_users;

                this.assigned_to = selected_users.map(function (user) {
                    return user.id;
                });
            }
        }
    },

    methods: {
        setDefaultValue: function setDefaultValue() {
            if (typeof this.subTask.assignees !== 'undefined') {
                var self = this;
                this.subTask.assignees.data.map(function (user) {
                    self.assigned_to.push(user.id);
                });
            }
        },
        selfSubTaskFormAction: function selfSubTaskFormAction() {

            // Exit from this function, If submit button disabled
            if (this.submit_disabled) {
                return;
            }

            if (!this.subTask.title) {
                pm.Toastr.error('Sub task title required!');
                return false;
            }

            var self = this;
            this.submit_disabled = true;
            // Showing loading option
            this.show_spinner = true;
            var args = {
                data: {
                    sub_task_id: this.subTask.id,
                    board_id: this.task.task_list.data.id,
                    assignees: this.assigned_to,
                    title: this.subTask.title,
                    description: this.description,
                    start_at: this.subTask.start_at.date,
                    due_date: this.subTask.due_date.date,
                    task_privacy: this.subTask.task_privacy,
                    parent_id: this.task.id,
                    task_id: this.task.id,
                    project_id: this.task.project_id
                },
                callback: function callback(res) {
                    if (self.subTask.id) {
                        var subTaskIndex = self.getIndex(self.task.sub_tasks, self.subTask.id, 'id');
                        self.task.sub_tasks.splice(subTaskIndex, 1, res.data);
                    } else {
                        self.task.sub_tasks.push(res.data);
                    }
                    self.description = res.data.description.content;

                    self.show_spinner = false;
                    self.submit_disabled = false;
                }
            };

            if (typeof this.subTask.id !== 'undefined') {
                self.updateSubTask(args);
            } else {
                self.addSubTask(args);
            }
        }
    }
});

/***/ }),
/* 12 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__new_subtask_form_vue__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__single_subtask_lists_vue__ = __webpack_require__(39);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

//import NewSubTaskForm from './new-subtask-form.vue';
//import SubTaskLists from './sub-task-lists.vue';




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
            //task_users: this.actionData.task.assignees.data,
            task_users: this.actionData.assignees,
            createForm: false,
            formStatus: false,
            showSubtasksInSingleTask: false,
            localSubTaskFrom: {
                status: false
            },
            new_sub_task: __('New sub task', 'pm-pro'),
            subTask: {
                assignees: [],
                title: '',
                start_at: '',
                due_date: '',
                description: '',
                meta: []
            },
            assigned_to: [],
            subTaskUserAssign: false,
            isActiveCalendar: false,
            isActiveDescription: false,
            show_spinner: false
        };
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default.a],
    created: function created() {
        var self = this;
        this.getSelfSubtask();
        window.addEventListener('click', this.windowActivity);
        this.$root.$on('pm_date_picker', this.getDatePicker);

        jQuery(document).keyup(function (e) {
            if (e.keyCode === 27) {
                var subtaskInput = jQuery(e.target).closest('.new-subtask-form').find('.input-area');

                if (subtaskInput.length) {
                    self.createForm = false;
                }
            }
        });
    },

    computed: {
        lists: function lists() {

            return {
                list: this.actionData.list,
                task: this.actionData.task
            };
        },

        /**
         * Get and Set task users
         */
        sub_task_assign: {
            /**
             * Filter only current task assgin user from vuex state project_users
             *
             * @return array
             */
            get: function get() {
                return typeof this.subTask.assignees === 'undefined' ? [] : this.subTask.assignees;
            },

            /**
             * Set selected users at task insert or edit time
             *
             * @param array selected_users
             */
            set: function set(selected_users) {

                if (selected_users) {
                    this.subTask.assignees = [selected_users];
                } else {
                    this.subTask.assignees = [0];
                }

                this.assigned_to = this.subTask.assignees.map(function (user) {
                    return user.id;
                });
            }
        },
        isTaskActive: function isTaskActive() {
            return this.actionData.task.status ? false : true;
        },
        subTaskForm: function subTaskForm() {
            if (typeof this.actionData.list == 'undefined') {
                return false;
            }
            if (typeof this.actionData.task == 'undefined') {
                return false;
            }
            if (this.actionData.task.status) {
                return false;
            }
            var lists = this.$store.state.projectTaskLists.lists;
            var list_index = this.getIndex(lists, this.actionData.list.id, 'id');
            var list = lists[list_index];

            if (this.actionData.task.status) {
                var task_index = this.getIndex(lists[list_index].complete_tasks.data, this.actionData.task.id, 'id');
                var task = lists[list_index].complete_tasks.data[task_index];
            } else {
                var task_index = this.getIndex(lists[list_index].incomplete_tasks.data, this.actionData.task.id, 'id');
                var task = lists[list_index].incomplete_tasks.data[task_index];
            }

            return typeof task == 'undefined' ? false : task.new_sub_task_form;
        }
    },
    components: {
        //'new-subtask-form': NewSubTaskForm,
        'subtask-lists': __WEBPACK_IMPORTED_MODULE_2__single_subtask_lists_vue__["a" /* default */],
        'new-subtask-form': __WEBPACK_IMPORTED_MODULE_0__new_subtask_form_vue__["a" /* default */],
        'multiselect': pm.Multiselect.Multiselect,
        'single-subtask-lists': __WEBPACK_IMPORTED_MODULE_2__single_subtask_lists_vue__["a" /* default */]
    },
    methods: {
        windowActivity: function windowActivity(el) {
            var mainField = jQuery(el.target).closest('.new-subtask-form'),
                subTaskUserAssign = jQuery(el.target).closest('.icon-pm-single-user'),
                subTaskCalendar = jQuery(el.target).hasClass('icon-pm-calendar'),
                subTaskCalendarWrap = jQuery(el.target).closest('.subtask-date'),
                description = jQuery(el.target).closest('.description-wrap'),
                descriptionBtn = jQuery(el.target).closest('.icon-pm-pencil'),
                hasCalendarArrowBtn = jQuery(el.target).hasClass('ui-icon'),
                hasCalendarCornerAll = jQuery(el.target).hasClass('ui-corner-all');

            if (hasCalendarArrowBtn || hasCalendarCornerAll) {
                return;
            }

            if (!mainField.length) {
                this.createForm = false;
            }

            if (!subTaskUserAssign.length) {
                this.subTaskUserAssign = false;
            }

            if (!subTaskCalendar && !subTaskCalendarWrap.length && !hasCalendarArrowBtn) {
                this.isActiveCalendar = false;
            }

            if (!description.length && !descriptionBtn.length) {
                this.isActiveDescription = false;
            }
        },
        getDatePicker: function getDatePicker(date) {
            if (date.id == 'subTaskForm' && date.field == 'datepicker_from') {
                this.subTask.start_at = date.date;
            }

            if (date.id == 'subTaskForm' && date.field == 'datepicker_to') {
                this.subTask.due_date = date.date;
            }
        },
        showHideDescription: function showHideDescription() {
            this.isActiveDescription = this.isActiveDescription ? false : true;

            if (this.isActiveDescription) {
                pm.Vue.nextTick(function () {
                    jQuery('.description-wrap').find('.description-field').focus();
                });
            }
        },
        showHideCalendar: function showHideCalendar() {
            this.isActiveCalendar = this.isActiveCalendar ? false : true;
        },
        showHideSubtaskUserAssign: function showHideSubtaskUserAssign() {
            this.subTaskUserAssign = this.subTaskUserAssign ? false : true;
        },
        showHideCreateForm: function showHideCreateForm() {
            this.createForm = this.createForm ? false : true;

            if (this.createForm) {
                pm.Vue.nextTick(function () {
                    jQuery('.subtask-title-field').focus();
                });
            }
        },
        showSubTaskForm: function showSubTaskForm(status) {
            status = status || 'toggle';

            if (status == 'toggle') {
                this.formStatus = this.formStatus ? false : true;
            } else {
                this.formStatus = status;
            }
        },
        getSelfSubtask: function getSelfSubtask() {

            if (typeof this.actionData.task.task_list === 'undefined') {
                return;
            }

            var self = this;
            var args = {
                task: self.actionData.task,
                data: {
                    project_id: self.actionData.task.project_id
                },
                callback: function callback(res) {
                    self.showSubtasksInSingleTask = self.showSubtasksInSingleTask ? false : true;
                    self.actionData.task.sub_tasks = res.data;
                    self.actionData.task.sub_task_content = self.actionData.task.sub_task_content ? false : true;
                    //console.log(self.actionData.task.sub_task_content);
                    //self.$store.commit('subTasks/showSubTaskContent', {task: self.actionData.task});
                }
            };

            this.getSubTasks(args);
        },
        selfSubTaskFormAction: function selfSubTaskFormAction() {

            if (!this.subTask.title) {
                pm.Toastr.error('Sub task title required!');
                return false;
            }
            this.show_spinner = true;

            var self = this;

            var args = {
                data: {
                    sub_task_id: this.subTask.id,
                    board_id: this.actionData.task.task_list.data.id,
                    assignees: this.assigned_to,
                    title: this.subTask.title,
                    description: this.subTask.description,
                    start_at: this.subTask.start_at,
                    due_date: this.subTask.due_date,
                    task_privacy: this.subTask.task_privacy,
                    estimation: this.setMinuteToTime(this.subTask.estimation) || '',
                    parent_id: this.actionData.task.id,
                    task_id: this.actionData.task.id,
                    project_id: this.actionData.task.project_id
                },
                callback: function callback(res) {
                    if (self.subTask.id) {
                        var subTaskIndex = self.getIndex(self.actionData.task.sub_tasks, self.subTask.id, 'id');
                        self.actionData.task.sub_tasks.splice(subTaskIndex, 1, res.data);
                    } else {
                        self.actionData.task.sub_tasks.push(res.data);
                    }
                    self.subTask.description = res.data.description.content;

                    self.subTask = {
                        assignees: [],
                        title: '',
                        start_at: '',
                        due_date: '',
                        description: '',
                        meta: []
                    };

                    self.show_spinner = false;
                    self.setSubtaskUserInTask(self.actionData.task, res.data);
                }
            };

            if (typeof this.subTask.id !== 'undefined') {
                self.updateSubTask(args);
            } else {
                self.addSubTask(args);
            }
        },
        setMinuteToTime: function setMinuteToTime(minute) {
            minute = minute ? parseInt(minute) : 0;
            var time = this.stringToTime(minute * 60);

            return time.hours + ':' + time.minutes;
        },
        setSubtaskUserInTask: function setSubtaskUserInTask(task, subTask) {
            var self = this;
            var taskUsers = task.assignees.data;
            var subTaskUsers = subTask.assignees.data;
            var newUsers = [];

            subTask.assignees.data.forEach(function (subTaskUser) {
                var hasId = self.getIndex(task.assignees.data, subTaskUser.id, 'id');

                if (hasId === false) {
                    task.assignees.data.push(subTaskUser);
                }
            });
        }
    }
});

/***/ }),
/* 13 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__new_subtask_form_vue__ = __webpack_require__(4);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__single_subtask_edit_vue__ = __webpack_require__(42);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__subtask_to_task_modal_vue__ = __webpack_require__(46);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__helpers_mixin_mixin__ = __webpack_require__(1);
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
            type: [Object, Array],
            default: function _default() {
                return {};
            }
        }
    },
    data: function data() {
        return {
            show_spinner: false,
            isSubtaskToTaskModalActive: false,
            modalSubtask: {}
        };
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_3__helpers_mixin_mixin___default.a],
    components: {
        'new-subtask-form': __WEBPACK_IMPORTED_MODULE_0__new_subtask_form_vue__["a" /* default */],
        'subtask-eidt': __WEBPACK_IMPORTED_MODULE_1__single_subtask_edit_vue__["a" /* default */],
        'subtask-to-task-modal': __WEBPACK_IMPORTED_MODULE_2__subtask_to_task_modal_vue__["a" /* default */]
    },

    computed: {
        task_start_field: function task_start_field() {
            var isActiveStartField = this.getSettings('task_start_field', false);
            return isActiveStartField;
        },
        isTaskActive: function isTaskActive() {
            return this.actionData.task.status ? false : true;
        },
        subTasks: function subTasks() {
            var task = this.actionData.task;
            if (!task) {
                return [];
            }
            return task.sub_tasks;
        },
        is_single_task_open: function is_single_task_open() {
            if (typeof this.actionData.is_single_task_open !== 'undefined') {
                return this.actionData.is_single_task_open;
            }
        }
    },

    created: function created() {
        pmBus.$on('after_update_single_task_user', this.afterUpdateSingelTaskUser);
    },


    methods: {
        afterUpdateSingelTaskUser: function afterUpdateSingelTaskUser(data) {
            if (data.beforeUpdate.assignees.data.length != data.afterUpdate.assignees.data.length) {
                pm.Toastr.error(__('User already exist in subtask', 'pm'));
            }
        },

        /**
         * addTaskMeta for task edit mode
         * @param {[Object]} task [Task Object]
         */
        addTaskMeta: function addTaskMeta(task) {
            task.edit_mode = false;
        },
        makeAsTask: function makeAsTask(subtask, actionData) {
            var self = this;
            var request = {
                type: 'post',
                url: self.base_url + '/pm-pro/v2/tasks/' + subtask.parent_id + '/sub-tasks/' + subtask.id + '/make-task',
                data: {
                    list_id: subtask.task_list.data.id
                },
                success: function success(res) {

                    self.addTaskMeta(res.data);

                    self.$store.commit('subTasks/afterDeleteSubTask', {
                        sub_task_id: subtask.id,
                        task_id: subtask.parent_id,
                        list_id: subtask.task_list.data.id
                    });

                    self.$store.commit('projectTaskLists/afterNewTask', {
                        list_id: subtask.task_list.data.id,
                        task: res.data,
                        list: subtask.task_list.data
                    });

                    self.$store.commit('updateProjectMeta', 'total_activities');
                    // Display a success toast, with a title
                    //self.showHideTaskFrom(false, subtask.task_list.data, res.data );

                    //pmBus.$emit('pm_after_create_task', res);
                }
            };
            self.httpRequest(request);
        },
        isChecked: function isChecked(task) {
            return task.status ? 'checked' : '';
        },

        /**
         * WP settings date format convert to pm.Moment date format with time zone
         *
         * @param  string date
         *
         * @return string
         */
        dateFormat: function dateFormat(date) {
            if (!date) {
                return;
            }

            date = new Date(date);
            date = pm.Moment(date).format('YYYY-MM-DD');

            var format = 'MMMM DD YYYY';

            if (PM_Vars.wp_date_format == 'Y-m-d') {
                format = 'YYYY-MM-DD';
            } else if (PM_Vars.wp_date_format == 'm/d/Y') {
                format = 'MM/DD/YYYY';
            } else if (PM_Vars.wp_date_format == 'd/m/Y') {
                format = 'DD/MM/YYYY';
            }

            return pm.Moment(date).format(format);
        },

        /**
         * Showing (-) between task start date and due date
         *
         * @param  string  task_start_field
         * @param  string  start_date
         * @param  string  due_date
         *
         * @return Boolean
         */
        isBetweenDate: function isBetweenDate(task_start_field, start_date, due_date) {

            if (!task_start_field) {
                return false;
            }

            if (!start_date) {
                return false;
            }
            if (!due_date) {
                return false;
            }

            return true;
        },

        /**
         * CSS class for task date
         *
         * @param  string start_date
         * @param  string due_date
         *
         * @return string
         */
        subTaskDateWrap: function subTaskDateWrap(due_date, start_date, isEnableStart) {
            if (!due_date) {
                if (isEnableStart && start_date) {
                    return 'pm-current-date';
                }

                return '';
            }

            due_date = new Date(due_date);
            due_date = pm.Moment(due_date).format('YYYY-MM-DD');

            if (!pm.Moment(due_date).isValid()) {
                return false;
            }

            var today = pm.Moment().format('YYYY-MM-DD'),
                due_day = pm.Moment(due_date).format('YYYY-MM-DD');
            return pm.Moment(today).isSameOrBefore(due_day) ? 'pm-current-date' : 'pm-due-date';
        },
        getUser: function getUser(userId) {
            userId = userId || this.$store.state.get_current_user_id;

            var usr = this.$store.state.project_users.find(function (user) {
                return user.id == userId;
            });

            return usr.name;
        },
        is_assigned: function is_assigned(subtask) {

            var get_current_user_id = this.$store.state.get_current_user_id,
                in_task = subtask.assignees.data.indexOf(get_current_user_id);

            if (subtask.can_del_edit || in_task != '-1') {
                return true;
            }

            return false;
        },
        doneUndone: function doneUndone(subTask) {
            var self = this,
                status = subTask.status == 'incomplete' ? 1 : 0;
            subTask.show_spinner = true;
            var args = {
                data: {
                    sub_task_id: subTask.id,
                    parent_id: subTask.parent_id,
                    status: status,
                    project_id: subTask.project_id
                },
                callback: function callback(res) {
                    var subTaskIndex = self.getIndex(self.actionData.task.sub_tasks, subTask.id, 'id');
                    self.actionData.task.sub_tasks.splice(subTaskIndex, 1, res.data);
                    subTask.show_spinner = false;
                    // self.$store.commit( 'projectTaskLists/afterTaskDoneUndone', {
                    //     status: status,
                    //     task: res.data,
                    //     list_id: self.list.id,
                    //     task_id: self.task.id
                    // });
                }
            };

            this.subTaskDoneUndone(args);
        },


        /**
         * Delete task
         *
         * @return void
         */
        deleteSelfSubTask: function deleteSelfSubTask(subtask, task) {

            var self = this;
            var form_data = {
                sub_task_id: subtask.id,
                task_id: task.id,
                list_id: task.task_list.data.id,

                callback: function callback(res) {
                    var subTaskIndex = self.getIndex(self.actionData.task.sub_tasks, subtask.id, 'id');
                    self.actionData.task.sub_tasks.splice(subTaskIndex, 1);
                }
            };

            this.deleteSubTask(form_data);
        },
        editSubTask: function editSubTask(subtask) {
            pm.Vue.nextTick(function () {
                pm.Vue.set(subtask, 'edit_mode', true);
            });
        },
        activeSubtaskToTaskModal: function activeSubtaskToTaskModal(subtask) {
            this.modalSubtask = subtask;
            this.isSubtaskToTaskModalActive = true;
        },
        afterMoveSubtaskToTask: function afterMoveSubtaskToTask(data) {
            var subTaskIndex = this.getIndex(this.actionData.task.sub_tasks, parseInt(data.subtaskId), 'id');
            this.actionData.task.sub_tasks.splice(subTaskIndex, 1);
        }
    }
});

/***/ }),
/* 14 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin__ = __webpack_require__(1);
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
        task: {
            type: Object,
            default: function _default() {
                return {
                    assignees: {}

                };
            }
        },

        subTask: {
            type: Object,
            default: function _default() {
                return {
                    description: {},
                    start_at: { date: '' },
                    due_date: { date: '' },
                    assignees: { data: [] }
                };
            }
        },

        actionData: {
            type: Object,
            default: function _default() {}
        }
    },
    data: function data() {
        return {
            //task_users: this.task.assignees.data,
            task_users: this.actionData.assignees,
            createForm: false,
            formStatus: false,
            showSubtasksInSingleTask: false,
            localSubTaskFrom: {
                status: false
            },
            new_sub_task: __('New sub task', 'pm-pro'),
            assigned_to: [],
            subTaskUserAssign: false,
            isActiveCalendar: false,
            isActiveDescription: false,
            isLoaded: false
        };
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    beforeMount: function beforeMount() {},
    created: function created() {
        var self = this;
        this.setEditData();
        window.addEventListener('click', this.windowActivity);
        this.$root.$on('pm_date_picker', this.getDatePicker);

        jQuery(document).keyup(function (e) {
            if (e.keyCode === 27) {
                var subtaskInput = jQuery(e.target).closest('.new-subtask-form').find('.input-area');

                if (subtaskInput.length) {
                    self.subTask.edit_mode = false;
                }
            }
        });

        pm.Vue.nextTick(function () {
            jQuery('.subtask-updpate-title-field').focus();
        });
    },

    computed: {
        lists: function lists() {

            return {
                list: this.actionData.list,
                task: this.actionData.task
            };
        },

        /**
         * Get and Set task users
         */
        sub_task_assign: {
            /**
             * Filter only current task assgin user from vuex state project_users
             *
             * @return array
             */
            get: function get() {
                return this.subTask.editData.users;
            },

            /**
             * Set selected users at task insert or edit time
             *
             * @param array selected_users
             */
            set: function set(selected_users) {
                if (selected_users) {
                    this.subTask.editData.users = [selected_users];
                } else {
                    this.subTask.editData.users = [];
                }
            }
        },
        isTaskActive: function isTaskActive() {
            return this.actionData.task.status ? false : true;
        }
    },
    components: {
        'multiselect': pm.Multiselect.Multiselect
    },
    methods: {
        windowActivity: function windowActivity(el) {
            var subTaskEditForm = jQuery(el.target).closest('.subtask-edit-wrap'),
                hasCalendarArrowBtn = jQuery(el.target).hasClass('ui-icon'),
                hasCalendarCornerAll = jQuery(el.target).hasClass('ui-corner-all');

            if (hasCalendarArrowBtn || hasCalendarCornerAll) {
                return;
            }

            if (!subTaskEditForm.length) {
                pm.Vue.set(this.subTask, 'edit_mode', false);
            }
        },
        setEditData: function setEditData() {
            if (typeof this.subTask.editData == 'undefined') {

                var editData = {
                    title: this.subTask.title,
                    start: this.subTask.start_at.date,
                    due: this.subTask.due_date.date,
                    users: typeof this.subTask.assignees === 'undefined' ? [] : this.subTask.assignees.data
                };

                pm.Vue.set(this.subTask, 'editData', editData);
            }

            this.isLoaded = true;
        },
        getDatePicker: function getDatePicker(date) {
            // if ( date.id == 'subTaskForm' && date.field == 'datepicker_from' ) {
            //     this.subTask.start_at.date = date.date;
            // }

            // if ( date.id == 'subTaskForm' &&  date.field == 'datepicker_to' ) {
            //     this.subTask.due_date.date = date.date;
            // }
        },
        showHideDescription: function showHideDescription() {
            this.isActiveDescription = this.isActiveDescription ? false : true;

            if (this.isActiveDescription) {
                pm.Vue.nextTick(function () {
                    jQuery('.description-wrap').find('.description-field').focus();
                });
            }
        },
        showHideCalendar: function showHideCalendar() {
            this.isActiveCalendar = this.isActiveCalendar ? false : true;
        },
        showHideSubtaskUserAssign: function showHideSubtaskUserAssign() {
            this.subTaskUserAssign = this.subTaskUserAssign ? false : true;
        },
        showHideCreateForm: function showHideCreateForm() {
            this.createForm = this.createForm ? false : true;
        },
        showSubTaskForm: function showSubTaskForm(status) {
            status = status || 'toggle';

            if (status == 'toggle') {
                this.formStatus = this.formStatus ? false : true;
            } else {
                this.formStatus = status;
            }
        },
        selfSubTaskFormAction: function selfSubTaskFormAction() {

            if (!this.subTask.title) {
                pm.Toastr.error('Sub task title required!');
                return false;
            }

            var start = new Date(this.subTask.editData.start);
            var end = new Date(this.subTask.editData.due);
            var compare = pm.Moment(end).isBefore(start);

            if (this.subTask.editData.start && this.subTask.editData.due && compare) {
                pm.Toastr.error(__('Invalid date range!', 'wedevs-project-manager'));
                return;
            }

            var self = this;
            //this.submit_disabled = true;
            // Showing loading option
            //this.show_spinner = true;
            var args = {
                data: {
                    sub_task_id: this.subTask.id,
                    board_id: this.task.task_list.data.id,
                    assignees: this.filterUserId(this.subTask.editData.users),
                    title: this.subTask.editData.title,
                    description: this.subTask.description.content,
                    start_at: this.subTask.editData.start,
                    due_date: this.subTask.editData.due,
                    task_privacy: this.subTask.task_privacy,
                    estimation: this.setMinuteToTime(this.subTask.estimation) || '',
                    parent_id: this.task.id,
                    task_id: this.task.id,
                    project_id: this.task.project_id
                },
                callback: function callback(res) {
                    if (self.subTask.id) {
                        var subTaskIndex = self.getIndex(self.task.sub_tasks, self.subTask.id, 'id');
                        self.task.sub_tasks.splice(subTaskIndex, 1, res.data);
                    } else {
                        self.task.sub_tasks.push(res.data);
                    }
                    self.subTask.description = res.data.description.content;

                    // self.subTask = {
                    //     assignees: [],
                    //     title: '',
                    //     start_at: '',
                    //     due_date: '',
                    //     description: ''
                    // }

                    //self.show_spinner = false;
                    //self.submit_disabled = false;
                }
            };

            self.updateSubTask(args);
        },
        setMinuteToTime: function setMinuteToTime(minute) {
            minute = minute ? parseInt(minute) : 0;
            var time = this.stringToTime(minute * 60);

            return time.hours + ':' + time.minutes;
        },
        filterUserId: function filterUserId(users) {
            var cuser = [];
            cuser = users.map(function (user) {
                return user.id;
            });

            if (!cuser.length) {
                cuser = [0];
            }

            return cuser;
        }
    }
});

/***/ }),
/* 15 */
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
        subtask: {
            type: [Object],
            default: function _default() {
                return {};
            }
        }
    },
    data: function data() {
        return {
            submitProcessing: false,
            isListLoaded: false,
            listDropDownOptions: {
                placeholder: __('Select Task List', 'pm-pro'),
                projectId: false
            },
            list: {}
        };
    },
    created: function created() {
        this.listDropDownOptions.projectId = this.subtask.project_id;
    },


    methods: {
        close: function close() {
            this.$emit('closeSubtaskToTaskModal');
        },
        afterFetchList: function afterFetchList(list) {
            this.isListLoaded = true;
        },
        submitBtnClass: function submitBtnClass() {
            return this.submitProcessing ? 'submit-btn-text update pm-button pm-primary' : 'update pm-button pm-primary';
        },
        setList: function setList(list) {
            this.list = list;
        },
        subtaskToTask: function subtaskToTask() {
            if (jQuery.isEmptyObject(this.list)) {
                pm.Toastr.error(__('Pelase select task list', 'pm-pro'));
                return;
            }
            if (this.submitProcessing) {
                return;
            }

            this.submitProcessing = true;

            var self = this;
            var request = {
                type: 'post',
                url: self.base_url + '/pm-pro/v2/tasks/' + self.subtask.parent_id + '/sub-tasks/' + self.subtask.id + '/make-task',
                data: {
                    list_id: self.list.id
                },
                success: function success(res) {
                    self.addTaskMeta(res.data);

                    self.$store.commit('subTasks/afterDeleteSubTask', {
                        sub_task_id: self.subtask.id,
                        task_id: self.subtask.parent_id,
                        list_id: self.subtask.task_list_id
                    });

                    self.$store.commit('projectTaskLists/afterNewTask', {
                        list_id: res.data.task_list_id,
                        task: res.data,
                        list: res.data.task_list.data
                    });

                    self.close();

                    self.$emit('afterMoveSubtaskToTask', {
                        subtaskId: self.subtask.id
                    });
                }
            };
            self.httpRequest(request);
        },
        addTaskMeta: function addTaskMeta(task) {
            task.edit_mode = false;
        }
    }
});

/***/ }),
/* 16 */
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

/* harmony default export */ __webpack_exports__["a"] = ({

    props: {
        actionData: {
            type: [Object]
        }
    },

    created: function created() {
        this.sanitizeOptions(this.actionData.options);
    },
    data: function data() {
        return {
            isActiveMoreOption: false,
            showContent: true,
            isFiltered: false
        };
    },


    methods: {
        isCollapsed: function isCollapsed() {
            return this.showContent ? '' : 'collapsed';
        },
        showHideContent: function showHideContent() {
            this.showContent = this.showContent ? false : true;
        },
        changeStatus: function changeStatus() {
            this.slack.status = this.slack.status == 'enable' ? 'disable' : 'enable';
        },
        isChecked: function isChecked() {
            return this.slack.status == 'enable' ? 'checked' : '';
        },
        sanitizeOptions: function sanitizeOptions(options) {
            var self = this;

            if (!options.subTasks) {
                pm.Vue.set(options, 'subTasks', {
                    create: true,
                    update: true,
                    complete: true,
                    incomplete: true
                });
            }
        },
        slackIcon: function slackIcon() {
            return PM_Pro_Vars.dir_url + '/views/assets/images/icon-slack.png';
        },
        showHideMoreOption: function showHideMoreOption() {
            this.isActiveMoreOption = this.isActiveMoreOption ? false : true;
        }
    }
});

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(18);

var _router2 = _interopRequireDefault(_router);

__webpack_require__(19);

var _subTasks = __webpack_require__(20);

var _subTasks2 = _interopRequireDefault(_subTasks);

var _afterTaskContent = __webpack_require__(26);

var _afterTaskContent2 = _interopRequireDefault(_afterTaskContent);

var _subTaskContent = __webpack_require__(28);

var _subTaskContent2 = _interopRequireDefault(_subTaskContent);

var _singleSubtaskCreate = __webpack_require__(36);

var _singleSubtaskCreate2 = _interopRequireDefault(_singleSubtaskCreate);

var _slack = __webpack_require__(52);

var _slack2 = _interopRequireDefault(_slack);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/sub_tasks/views/assets/js/';

// const SubTask = resolve => {
//     require.ensure(['./components/sub-tasks.vue'], () => {
//         resolve(require('./components/sub-tasks.vue'));
//     });
// }

// weDevs_PM_Components.push({
// 	hook: 'task_inline',
// 	component: 'pm-sub-tasks',
// 	property: SubTask
// });

weDevs_PM_Components.push({
  hook: 'pm_after_task_content',
  component: 'sub-task-after-task-content',
  property: _afterTaskContent2.default
});

weDevs_PM_Components.push({
  hook: 'after_task_content',
  component: 'sub-task-lists',
  property: _subTaskContent2.default
});

weDevs_PM_Components.push({
  hook: 'aftre_single_task_content',
  component: 'after-single-task',
  property: _singleSubtaskCreate2.default
});

weDevs_PM_Components.push({
  hook: 'pm-pro-after-slack-more-options',
  component: 'pm-pro-subtask-slack-options',
  property: _slack2.default
});

/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


weDevsPmProAddonRegisterModule('subTasks', 'sub_tasks');

/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


pm.Vue.directive('pm-slide-up-down', {
    inserted: function inserted(el) {
        var node = jQuery(el);

        if (node.is(':visible')) {
            node.slideUp(400);
        } else {
            node.slideDown(400);
        }
    }
});

var PM_Subtask = {
    sortable: function sortable(el, binding, vnode) {
        var $ = jQuery;
        var component = vnode.context;

        $(el).sortable({
            cancel: '.subtsk-nonsortable,form',
            connectWith: '.pm-subtsk-sortable',
            placeholder: "pm-ui-state-highlight",
            handle: '.pm-sub-task-handaler',
            beforeStop: function beforeStop(event, ui) {
                if (!component.canCraeteSubTask(component.actionData.task)) {
                    $(el).sortable("cancel");
                }
            },
            update: function update(event, ui) {
                var item = $(ui.item).data('id');
                var listId = $(ui.item).closest('ul.pm-pro-subtask-todos, ul.pm-subtsk-sortable').data('list');
                var taskId = $(ui.item).closest('ul.pm-pro-subtask-todos, ul.pm-subtsk-sortable').data('task');
                var todos = $(ui.item).closest('ul.pm-pro-subtask-todos, ul.pm-subtsk-sortable').find('li.pm-pro-subtask-content, li.subtask-li');
                var orders = PM_Subtask.sorting(todos);

                if (ui.sender) {

                    component.subtaskOrder({
                        item: item,
                        receive: taskId,
                        list_id: listId,
                        orders: orders
                    });
                } else {
                    component.subtaskOrder({
                        list_id: listId,
                        orders: orders
                    });
                }
            }
        });
    },

    sorting: function sorting(todos) {
        todos = todos || [];
        var $ = jQuery,
            orders = [];

        // finding new order sequence and old orders
        todos.each(function (index, e) {
            var task_id = $(e).data('id');

            orders.push({
                index: index,
                id: task_id
            });
        });

        return orders;
    }
};

pm.Vue.directive('pm-subtask-sortable', {
    inserted: function inserted(el, binding, vnode) {
        PM_Subtask.sortable(el, binding, vnode);
    }
});

/***/ }),
/* 20 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_tasks_vue__ = __webpack_require__(5);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_200b5cd8_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_tasks_vue__ = __webpack_require__(25);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_tasks_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_200b5cd8_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_tasks_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/sub-tasks.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-200b5cd8", Component.options)
  } else {
    hotAPI.reload("data-v-200b5cd8", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_new_subtask_button_vue__ = __webpack_require__(6);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_72e12700_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_new_subtask_button_vue__ = __webpack_require__(22);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_new_subtask_button_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_72e12700_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_new_subtask_button_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/new-subtask-button.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-72e12700", Component.options)
  } else {
    hotAPI.reload("data-v-72e12700", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    !_vm.is_single_task && _vm.hasPermission(_vm.task)
      ? _c(
          "a",
          {
            staticClass: "cpmst-btn-column-add",
            attrs: { href: "#", title: _vm.new_subtask },
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.selfShowHideSubTaskForm(_vm.task)
              }
            }
          },
          [_vm._v(" ")]
        )
      : _vm._e(),
    _vm._v(" "),
    _vm.is_single_task && _vm.hasPermission(_vm.task)
      ? _c(
          "a",
          {
            staticClass: "button button-primary",
            attrs: { href: "#", title: _vm.new_subtask },
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.selfShowHideSubTaskForm(_vm.task)
              }
            }
          },
          [_vm._v(_vm._s(_vm.new_subtask))]
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
    require("vue-hot-reload-api")      .rerender("data-v-72e12700", esExports)
  }
}

/***/ }),
/* 23 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_show_subtask_button_vue__ = __webpack_require__(7);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_bb66823e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_show_subtask_button_vue__ = __webpack_require__(24);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_show_subtask_button_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_bb66823e_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_show_subtask_button_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/show-subtask-button.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-bb66823e", Component.options)
  } else {
    hotAPI.reload("data-v-bb66823e", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 24 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return !_vm.is_single_task
    ? _c("div", [
        _c(
          "a",
          {
            staticClass: "dashicons dashicons-editor-ul",
            attrs: { href: "#" },
            on: {
              click: function($event) {
                $event.preventDefault()
                return _vm.getSelfSubtask(_vm.task)
              }
            }
          },
          [_vm._v(" ")]
        )
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
    require("vue-hot-reload-api")      .rerender("data-v-bb66823e", esExports)
  }
}

/***/ }),
/* 25 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-pro-subtask-action pm-pro-task-inline" },
    [
      _vm.canCraeteSubTask(_vm.actionData.task)
        ? _c(
            "div",
            { staticClass: "pm-pro-subtask-child-action" },
            [
              _c("new-subtask-button", { attrs: { task: _vm.actionData.task } })
            ],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "pm-pro-subtask-child-action" },
        [_c("show-subtask-button", { attrs: { task: _vm.actionData.task } })],
        1
      ),
      _vm._v(" "),
      _c("div", { staticClass: "pm-clearfix" })
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
    require("vue-hot-reload-api")      .rerender("data-v-200b5cd8", esExports)
  }
}

/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_after_task_content_vue__ = __webpack_require__(8);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_3535100a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_after_task_content_vue__ = __webpack_require__(27);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_after_task_content_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_3535100a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_after_task_content_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/after-task-content.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-3535100a", Component.options)
  } else {
    hotAPI.reload("data-v-3535100a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div")
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-3535100a", esExports)
  }
}

/***/ }),
/* 28 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_task_content_vue__ = __webpack_require__(9);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_13cd1857_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_task_content_vue__ = __webpack_require__(35);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_task_content_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_13cd1857_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_task_content_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/sub-task-content.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-13cd1857", Component.options)
  } else {
    hotAPI.reload("data-v-13cd1857", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 29 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_task_lists_vue__ = __webpack_require__(10);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_298b12f3_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_task_lists_vue__ = __webpack_require__(34);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(30)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_sub_task_lists_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_298b12f3_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_sub_task_lists_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/sub-task-lists.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-298b12f3", Component.options)
  } else {
    hotAPI.reload("data-v-298b12f3", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 30 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(31);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("61decd9f", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-298b12f3\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./sub-task-lists.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-298b12f3\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./sub-task-lists.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 31 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-subtask-list {\n\tmargin-top: 10px;\n}\n.pm-pro-subtask-content {\n\tborder: none !important;\n\tpadding: 0 0 8px 23px !important;\n}\n.pm-pro-subtask-action-warp, .pm-pro-complete-subtask-action-wrap {\n\theight: 16px;\n\tmargin-left: 24px;\n}\n.pm-pro-subtask-todo-action, .pm-pro-subtask-todo-action {\n\tdisplay: none;\n\tfont-size: 12px;\n}\n.pm-pro-subtask-content:hover .pm-pro-subtask-todo-action,\n.pm-pro-subtask-action-warp:hover .pm-pro-subtask-todo-action,\n.pm-pro-complete-subtask-action-wrap:hover .pm-pro-subtask-todo-action {\n\tdisplay: block;\n}\n.pm-pro-subtask-todo-completed .pm-pro-subtask-todo-content {\n\ttext-decoration: line-through;\n}\n.sub-task-details {\n\tmargin-top: 10px;\n}\n", ""]);

// exports


/***/ }),
/* 32 */
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
/* 33 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "pm-todo-form" }, [
    _c(
      "form",
      {
        staticClass: "pm-task-form pm-form pm-subtask-form",
        attrs: { action: "" },
        on: {
          submit: function($event) {
            $event.preventDefault()
            return _vm.selfSubTaskFormAction()
          }
        }
      },
      [
        _c("div", { staticClass: "item task-title" }, [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.subTask.title,
                expression: "subTask.title"
              }
            ],
            staticClass: "task_title",
            attrs: {
              type: "text",
              name: "task_title",
              required: "required",
              placeholder: _vm.subtask_title
            },
            domProps: { value: _vm.subTask.title },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.$set(_vm.subTask, "title", $event.target.value)
              }
            }
          })
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "item content" }, [
          _c("textarea", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.description,
                expression: "description"
              }
            ],
            staticClass: "todo_content",
            attrs: { name: "task_text", placeholder: "Sub task details" },
            domProps: { value: _vm.description },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.description = $event.target.value
              }
            }
          })
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "item date" }, [
          _vm.subTaskStartField
            ? _c(
                "div",
                { staticClass: "pm-task-start-field" },
                [
                  _c("pm-date-picker", {
                    staticClass: "pm-datepickter-from",
                    attrs: {
                      dependency: "pm-datepickter-to",
                      placeholder: _vm.task_start_date
                    },
                    model: {
                      value: _vm.subTask.start_at.date,
                      callback: function($$v) {
                        _vm.$set(_vm.subTask.start_at, "date", $$v)
                      },
                      expression: "subTask.start_at.date"
                    }
                  })
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "pm-task-due-field" },
            [
              _c("pm-date-picker", {
                staticClass: "pm-datepickter-to",
                attrs: {
                  dependency: "pm-datepickter-from",
                  placeholder: _vm.task_due_date
                },
                model: {
                  value: _vm.subTask.due_date.date,
                  callback: function($$v) {
                    _vm.$set(_vm.subTask.due_date, "date", $$v)
                  },
                  expression: "subTask.due_date.date"
                }
              })
            ],
            1
          )
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "item user" }, [
          _c(
            "div",
            [
              _c("multiselect", {
                attrs: {
                  options: _vm.task_users,
                  multiple: true,
                  "close-on-select": false,
                  "clear-on-select": false,
                  "hide-selected": true,
                  "show-labels": false,
                  placeholder: _vm.select_user_text,
                  label: "display_name",
                  "track-by": "id"
                },
                model: {
                  value: _vm.sub_task_assign,
                  callback: function($$v) {
                    _vm.sub_task_assign = $$v
                  },
                  expression: "sub_task_assign"
                }
              })
            ],
            1
          )
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "item submit" }, [
          _c("span", { staticClass: "pm-new-task-spinner" }),
          _vm._v(" "),
          _vm.subTask.edit_mode
            ? _c("span", [
                _c("input", {
                  staticClass: "button-primary",
                  attrs: {
                    disabled: _vm.submit_disabled,
                    type: "submit",
                    name: "submit_todo"
                  },
                  domProps: { value: _vm.update_sub_task }
                })
              ])
            : _vm._e(),
          _vm._v(" "),
          !_vm.subTask.edit_mode
            ? _c("span", [
                _c("input", {
                  staticClass: "button-primary",
                  attrs: {
                    disabled: _vm.submit_disabled || !_vm.subTask.title,
                    type: "submit",
                    name: "submit_todo"
                  },
                  domProps: { value: _vm.new_sub_task }
                })
              ])
            : _vm._e(),
          _vm._v(" "),
          _c(
            "a",
            {
              staticClass: "button todo-cancel",
              attrs: { href: "#" },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.selfShowHideSubTaskForm(
                    _vm.task,
                    false,
                    _vm.subTask
                  )
                }
              }
            },
            [_vm._v(_vm._s(_vm.__("Cancel", "pm-pro")))]
          ),
          _vm._v(" "),
          _c("span", {
            directives: [
              {
                name: "show",
                rawName: "v-show",
                value: _vm.show_spinner,
                expression: "show_spinner"
              }
            ],
            staticClass: "pm-spinner"
          })
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
    require("vue-hot-reload-api")      .rerender("data-v-2a620912", esExports)
  }
}

/***/ }),
/* 34 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "pm-pro-subtask-list" }, [
    _c(
      "ul",
      {
        directives: [
          { name: "pm-subtask-sortable", rawName: "v-pm-subtask-sortable" }
        ],
        staticClass:
          "pm-pro-subtask-todos pm-pro-subtask-uncomplete-status pm-pro-subtask-sub-task pm-pro-subtask-front-ul-wrap pm-subtsk-sortable",
        attrs: {
          "data-list": _vm.actionData.task.task_list.data.id,
          "data-task": _vm.actionData.task.id
        }
      },
      _vm._l(_vm.subTasks, function(subtask) {
        return subtask.status == "incomplete"
          ? _c(
              "li",
              {
                key: subtask.id,
                staticClass: "pm-pro-subtask-content",
                attrs: { "data-id": subtask.id, "data-order": subtask.order }
              },
              [
                _c(
                  "div",
                  {
                    staticClass:
                      "pm-pro-subtask-todo-wrap pm-pro-subtask-task-uncomplete"
                  },
                  [
                    _c("span", { staticClass: "move" }),
                    _vm._v(" "),
                    _c(
                      "span",
                      { staticClass: "pm-pro-subtask-todo-content" },
                      [
                        subtask.show_spinner
                          ? _c("span", { staticClass: "pm-spinner" })
                          : _vm._e(),
                        _vm._v(" "),
                        !subtask.show_spinner
                          ? _c("input", {
                              staticClass: "pm-pro-subtask-uncomplete",
                              attrs: {
                                disabled:
                                  !subtask.meta.can_complete_task ||
                                  _vm.isArchivedTaskList(_vm.actionData.task),
                                type: "checkbox"
                              },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.doneUndone(subtask)
                                }
                              }
                            })
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "span",
                          { staticClass: "pm-pro-subtask-todo-text" },
                          [_vm._v(" " + _vm._s(subtask.title) + " ")]
                        ),
                        _vm._v(" "),
                        _vm._l(subtask.assignees.data, function(user) {
                          return _c(
                            "span",
                            { key: user.ID, staticClass: "pm-assigned-user" },
                            [
                              _c(
                                "a",
                                {
                                  attrs: { href: "#", title: user.display_name }
                                },
                                [
                                  _c("img", {
                                    attrs: {
                                      src: user.avatar_url,
                                      alt: user.display_name,
                                      height: "48",
                                      width: "48"
                                    }
                                  })
                                ]
                              )
                            ]
                          )
                        }),
                        _vm._v(" "),
                        _c(
                          "span",
                          { class: _vm.subTaskDateWrap(subtask.due_date.date) },
                          [
                            _vm.task_start_field
                              ? _c("span", [
                                  _vm._v(
                                    _vm._s(
                                      _vm.dateFormat(subtask.start_at.date)
                                    )
                                  )
                                ])
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.isBetweenDate(
                              _vm.task_start_field,
                              subtask.start_at.date,
                              subtask.due_date.date
                            )
                              ? _c("span", [_vm._v("–")])
                              : _vm._e(),
                            _vm._v(" "),
                            _c("span", [
                              _vm._v(
                                _vm._s(_vm.dateFormat(subtask.due_date.date))
                              )
                            ])
                          ]
                        )
                      ],
                      2
                    ),
                    _vm._v(" "),
                    _vm.is_single_task_open
                      ? _c("div", {
                          staticClass: "sub-task-details",
                          domProps: {
                            innerHTML: _vm._s(subtask.description.html)
                          }
                        })
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.canEditSubTask(subtask)
                      ? _c("transition", { attrs: { name: "slide" } }, [
                          subtask.edit_mode
                            ? _c(
                                "div",
                                {
                                  staticClass: "pm-pro-subtask-task-edit-form"
                                },
                                [
                                  _c("new-subtask-form", {
                                    attrs: {
                                      subTask: subtask,
                                      task: _vm.actionData.task
                                    }
                                  })
                                ],
                                1
                              )
                            : _vm._e()
                        ])
                      : _vm._e()
                  ],
                  1
                ),
                _vm._v(" "),
                _vm.canEditSubTask(subtask)
                  ? _c("div", { staticClass: "pm-pro-subtask-action-warp" }, [
                      _c(
                        "span",
                        { staticClass: "pm-pro-subtask-todo-action" },
                        [
                          _c(
                            "a",
                            {
                              staticClass: "pm-pro-subtask-todo-edit",
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.editSubTask(subtask)
                                }
                              }
                            },
                            [
                              _c("span", [
                                _vm._v(_vm._s(_vm.__("Edit", "pm-pro")) + " |")
                              ])
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              staticClass: "pm-pro-subtask-todo-delete",
                              attrs: { href: "#" },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.deleteSelfSubTask(
                                    subtask,
                                    _vm.actionData.task
                                  )
                                }
                              }
                            },
                            [
                              _c("span", [
                                _vm._v(
                                  _vm._s(_vm.__("Delete", "pm-pro")) + " |"
                                )
                              ])
                            ]
                          )
                        ]
                      )
                    ])
                  : _vm._e()
              ]
            )
          : _vm._e()
      }),
      0
    ),
    _vm._v(" "),
    _vm.actionData.task
      ? _c(
          "ul",
          {
            staticClass: "pm-pro-subtask-todo-completed pm-pro-subtask-sub-task"
          },
          _vm._l(_vm.actionData.task.sub_tasks, function(subtask) {
            return subtask.status == "complete"
              ? _c(
                  "li",
                  { key: subtask.ID, staticClass: "pm-pro-subtask-content" },
                  [
                    _c(
                      "div",
                      {
                        staticClass:
                          "pm-pro-subtask-todo-wrap pm-pro-subtask-task-complete"
                      },
                      [
                        _c(
                          "span",
                          { staticClass: "pm-pro-subtask-todo-content" },
                          [
                            subtask.show_spinner
                              ? _c("span", { staticClass: "pm-spinner" })
                              : _vm._e(),
                            _vm._v(" "),
                            !subtask.show_spinner
                              ? _c("input", {
                                  staticClass: "pm-pro-subtask-complete",
                                  attrs: {
                                    disabled:
                                      !subtask.meta.can_complete_task ||
                                      _vm.isArchivedTaskList(
                                        _vm.actionData.task
                                      ),
                                    type: "checkbox",
                                    checked: "checked"
                                  },
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      return _vm.doneUndone(subtask)
                                    }
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            _c(
                              "span",
                              { staticClass: "pm-pro-subtask-todo-text" },
                              [_vm._v(" " + _vm._s(subtask.title) + " ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "span",
                              { staticClass: "pm-pro-subtask-completed-by" },
                              [
                                _vm._v(
                                  "\n\t                        ( " +
                                    _vm._s(_vm.__("Completed by", "pm-pro")) +
                                    " " +
                                    _vm._s(subtask.updater.data.display_name) +
                                    " " +
                                    _vm._s(_vm.__("on", "pm-pro")) +
                                    " "
                                ),
                                _c(
                                  "time",
                                  {
                                    attrs: {
                                      datetime: subtask.updated_at.date,
                                      title: subtask.updated_at.date
                                    }
                                  },
                                  [
                                    _vm._v(
                                      _vm._s(
                                        _vm.dateFormat(subtask.updated_at.date)
                                      )
                                    )
                                  ]
                                ),
                                _vm._v(")\n\t                    ")
                              ]
                            )
                          ]
                        )
                      ]
                    ),
                    _vm._v(" "),
                    _vm.canCraeteSubTask &&
                    !_vm.isArchivedTaskList(_vm.actionData.task)
                      ? _c(
                          "div",
                          {
                            staticClass: "pm-pro-complete-subtask-action-wrap"
                          },
                          [
                            _c(
                              "span",
                              { staticClass: "pm-pro-subtask-todo-action" },
                              [
                                _c(
                                  "a",
                                  {
                                    staticClass: "pm-pro-subtask-todo-delete",
                                    attrs: { href: "#" },
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        return _vm.deleteSelfSubTask(
                                          subtask,
                                          _vm.actionData.task
                                        )
                                      }
                                    }
                                  },
                                  [
                                    _c("span", [
                                      _vm._v(_vm._s(_vm.__("Delete", "pm-pro")))
                                    ])
                                  ]
                                )
                              ]
                            )
                          ]
                        )
                      : _vm._e()
                  ]
                )
              : _vm._e()
          }),
          0
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
    require("vue-hot-reload-api")      .rerender("data-v-298b12f3", esExports)
  }
}

/***/ }),
/* 35 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      !_vm.isSingleTask && _vm.subTaskContent
        ? _c("sub-task-lists", { attrs: { actionData: _vm.actionData } })
        : _vm._e(),
      _vm._v(" "),
      _vm.canCraeteSubTask(_vm.actionData.task)
        ? _c(
            "transition",
            { attrs: { name: "slide" } },
            [
              _vm.subTaskForm && !_vm.isSingleTask
                ? _c("new-subtask-form", {
                    attrs: { task: _vm.actionData.task }
                  })
                : _vm._e()
            ],
            1
          )
        : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-13cd1857", esExports)
  }
}

/***/ }),
/* 36 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_subtask_create_vue__ = __webpack_require__(12);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_46719680_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_subtask_create_vue__ = __webpack_require__(51);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(37)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_subtask_create_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_46719680_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_subtask_create_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/single-subtask-create.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-46719680", Component.options)
  } else {
    hotAPI.reload("data-v-46719680", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 37 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(38);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("55b4970e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-46719680\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-subtask-create.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-46719680\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-subtask-create.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 38 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.singleSubtask {\n  margin-top: 20px;\n}\n.singleSubtask .new-subtask-form {\n  margin-top: 13px;\n}\n.singleSubtask .new-subtask-form .create-area {\n  border: 1px solid #ECECEC;\n  width: 100%;\n  padding: 5px 10px;\n  color: #B0BABC;\n}\n.singleSubtask .new-subtask-form .create-area:hover .icon-plus {\n  color: #444;\n}\n.singleSubtask .new-subtask-form .create-area .icon-plus {\n  line-height: 0;\n  margin-right: 10px;\n  font-size: 25px;\n  color: #D7DEE2;\n}\n.singleSubtask .new-subtask-form .input-area .input-action-wrap {\n  position: relative;\n}\n.singleSubtask .new-subtask-form .input-area .input-action-wrap .plus-text {\n  position: absolute;\n  top: 4px;\n  margin-left: 9px;\n  font-size: 25px;\n  color: #D7DEE2;\n  font-weight: 300;\n}\n.singleSubtask .new-subtask-form .input-area .input-action-wrap .pm-spinner {\n  position: absolute;\n  top: 7px;\n  left: 7px;\n}\n.singleSubtask .new-subtask-form .input-area .input-action-wrap .subtask-date {\n  position: absolute;\n  top: 32px;\n  right: 0px;\n  display: flex;\n  border: 1px solid #DDDDDD;\n  box-shadow: 0px 6px 20px 0px rgba(214, 214, 214, 0.6);\n}\n.singleSubtask .new-subtask-form .input-area .input-action-wrap .subtask-date .pm-date-picker-from .ui-datepicker {\n  border: none;\n}\n.singleSubtask .new-subtask-form .input-area .input-action-wrap .subtask-date .pm-date-picker-to .ui-datepicker {\n  border: none;\n}\n.singleSubtask .new-subtask-form .input-area .description-field {\n  border: none;\n  border-bottom: 1px solid #1A9ED4;\n  height: 30px;\n  padding: 10px;\n  width: 100%;\n  margin-bottom: 15px;\n  box-shadow: none;\n  line-height: 1.5;\n}\n.singleSubtask .new-subtask-form .input-area .icon-pm-single-user,\n.singleSubtask .new-subtask-form .input-area .icon-pm-calendar {\n  position: relative;\n  top: 4px;\n}\n.singleSubtask .new-subtask-form .input-area .input-field {\n  width: 100%;\n  height: 33px;\n  padding-left: 28px;\n  padding-right: 72px;\n}\n.singleSubtask .new-subtask-form .input-area .action-icons {\n  position: absolute;\n  right: 0;\n  top: 6px;\n  margin-right: 11px;\n  display: flex;\n}\n.singleSubtask .new-subtask-form .input-area .action-icons span {\n  margin-right: 10px;\n}\n.singleSubtask .new-subtask-form .input-area .action-icons .pm-action-wrap {\n  margin-right: 0;\n}\n.singleSubtask .subtask-title {\n  font-size: 14px;\n  font-weight: normal;\n  color: #000;\n}\n", ""]);

// exports


/***/ }),
/* 39 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_subtask_lists_vue__ = __webpack_require__(13);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a80421fe_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_subtask_lists_vue__ = __webpack_require__(50);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(40)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_subtask_lists_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_a80421fe_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_subtask_lists_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/single-subtask-lists.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-a80421fe", Component.options)
  } else {
    hotAPI.reload("data-v-a80421fe", Component.options)
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
var update = __webpack_require__(3)("991a2cae", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a80421fe\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-subtask-lists.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-a80421fe\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-subtask-lists.vue");
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

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-single-subtask-lists .pm-ui-state-highlight {\n  background: none !important;\n  border: 1px dashed #d7dee2 !important;\n  min-height: 30px !important;\n  margin: 0 21px 10px 3px !important;\n}\n.pm-pro-single-subtask-lists .subtask-li {\n  padding: 5px 0px !important;\n  border: none !important;\n  margin-bottom: 6px !important;\n}\n.pm-pro-single-subtask-lists .subtask-li:hover .icon-pm-pencil:before {\n  color: #136e94 !important;\n}\n.pm-pro-single-subtask-lists .subtask-li:hover .icon-pm-delete:before {\n  color: #e9485e !important;\n}\n.pm-pro-single-subtask-lists .subtask-li:hover .flaticon-pm-copy-files:before {\n  color: #136e94 !important;\n}\n.pm-pro-single-subtask-lists .subtask-li:hover .sub-task-content .subtask-move .icon-pm-drag-drop:before {\n  color: #b4b9be !important;\n}\n.pm-pro-single-subtask-lists .subtask-title {\n  font-size: 14px;\n  color: #000;\n  margin: 0 0 5px 0;\n  font-weight: bold;\n}\n.pm-pro-single-subtask-lists .sub-task-content {\n  display: flex;\n  position: relative;\n}\n.pm-pro-single-subtask-lists .sub-task-content .subtask-move {\n  position: absolute;\n  left: -22px;\n  top: -8px;\n  padding: 10px;\n  cursor: grab;\n}\n.pm-pro-single-subtask-lists .sub-task-content .subtask-move .icon-pm-drag-drop:before {\n  color: #fff;\n}\n.pm-pro-single-subtask-lists .sub-task-content .body {\n  margin-left: 10px;\n}\n.pm-pro-single-subtask-lists .sub-task-content .body .copy-task {\n  margin-right: 1px !important;\n}\n.pm-pro-single-subtask-lists .sub-task-content .body .copy-task .flaticon-pm-copy-files:before {\n  font-size: 14px !important;\n  font-weight: 600 !important;\n}\n.pm-pro-single-subtask-lists .sub-task-content .checkbox {\n  border-radius: 3px;\n  height: 18px;\n  width: 18px;\n  box-shadow: none;\n}\n.pm-pro-single-subtask-lists .sub-task-content .complete-title {\n  text-decoration: line-through;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title {\n  font-size: 13px;\n  color: #525252;\n  margin-right: 10px;\n  word-wrap: break-word;\n  word-break: break-all;\n  hyphens: auto;\n}\n.pm-pro-single-subtask-lists .sub-task-content .description {\n  font-size: 13px;\n  color: #959595;\n}\n.pm-pro-single-subtask-lists .sub-task-content .header-wrap {\n  display: flex;\n  align-items: baseline;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta {\n  display: flex;\n  align-items: baseline;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .subtask-date-wrap {\n  white-space: nowrap;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .icon-pm-delete,\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .icon-pm-pencil,\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .flaticon-pm-copy-files {\n  cursor: pointer;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .icon-pm-delete:before,\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .icon-pm-pencil:before,\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .flaticon-pm-copy-files:before {\n  color: #fff;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .meta-item {\n  margin-right: 10px;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .assigned-user-wrap {\n  display: flex;\n  align-items: center;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .assigned-user-wrap .assigned-user {\n  margin-right: 5px;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .assigned-user-wrap .assigned-user:last-child {\n  margin-right: 0;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .assigned-user-wrap .assigned-user .user-anchor {\n  display: block;\n}\n.pm-pro-single-subtask-lists .sub-task-content .title-meta .assigned-user-wrap .assigned-user img {\n  height: 16px;\n  width: 16px;\n  border-radius: 12px;\n  vertical-align: middle;\n}\n", ""]);

// exports


/***/ }),
/* 42 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_subtask_edit_vue__ = __webpack_require__(14);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5ab4b464_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_subtask_edit_vue__ = __webpack_require__(45);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(43)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_single_subtask_edit_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_5ab4b464_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_single_subtask_edit_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/single-subtask-edit.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-5ab4b464", Component.options)
  } else {
    hotAPI.reload("data-v-5ab4b464", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 43 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(44);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("1070ca38", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5ab4b464\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-subtask-edit.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-5ab4b464\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./single-subtask-edit.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 44 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.single-subtask-edit .new-subtask-form {\n  margin-top: 0;\n}\n.single-subtask-edit .new-subtask-form .create-area {\n  border: 1px solid #ECECEC;\n  width: 100%;\n  padding: 5px 10px;\n  color: #B0BABC;\n}\n.single-subtask-edit .new-subtask-form .create-area:hover .icon-plus {\n  color: #444;\n}\n.single-subtask-edit .new-subtask-form .create-area .icon-plus {\n  line-height: 0;\n  margin-right: 10px;\n  font-size: 25px;\n  color: #D7DEE2;\n}\n.single-subtask-edit .new-subtask-form .input-area .input-action-wrap {\n  position: relative;\n}\n.single-subtask-edit .new-subtask-form .input-area .input-action-wrap .update-button {\n  position: absolute;\n  right: 0;\n  top: 0px;\n  background: #019dd6;\n  color: #fff;\n  font-size: 12px;\n  padding: 6px 8px;\n}\n.single-subtask-edit .new-subtask-form .input-area .input-action-wrap .update-button:hover {\n  background: #008ec2;\n}\n.single-subtask-edit .new-subtask-form .input-area .input-action-wrap .update-button .icon-pm-check-circle {\n  font-weight: bold;\n}\n.single-subtask-edit .new-subtask-form .input-area .description-field {\n  border: none;\n  border-bottom: 1px solid #1A9ED4;\n  height: 30px;\n  padding: 10px;\n  width: 100%;\n  margin-bottom: 15px;\n  box-shadow: none;\n  line-height: 1.5;\n}\n.single-subtask-edit .new-subtask-form .input-area .icon-pm-single-user {\n  position: relative;\n}\n.single-subtask-edit .new-subtask-form .input-area .input-field {\n  width: 100%;\n  padding: 15px 11px;\n  padding-right: 97px;\n}\n.single-subtask-edit .new-subtask-form .input-area .action-icons {\n  position: absolute;\n  right: 0;\n  top: 6px;\n  margin-right: 30px;\n}\n.single-subtask-edit .new-subtask-form .input-area .action-icons .icon-pm-calendar {\n  position: relative;\n}\n.single-subtask-edit .new-subtask-form .input-area .action-icons .icon-pm-calendar .subtask-date {\n  position: absolute;\n  top: 23px;\n  right: -2px;\n  display: flex;\n  border: 1px solid #DDDDDD;\n  box-shadow: 0px 6px 20px 0px rgba(214, 214, 214, 0.6);\n}\n.single-subtask-edit .new-subtask-form .input-area .action-icons .icon-pm-calendar .subtask-date .pm-datepicker-from .ui-datepicker {\n  border: none !important;\n}\n.single-subtask-edit .new-subtask-form .input-area .action-icons .icon-pm-calendar .subtask-date .pm-datepicker-to .ui-datepicker {\n  border: none !important;\n}\n.single-subtask-edit .new-subtask-form .input-area .action-icons span {\n  margin-right: 10px;\n}\n.single-subtask-edit .subtask-title {\n  font-size: 14px;\n  font-weight: normal;\n  color: #000;\n}\n", ""]);

// exports


/***/ }),
/* 45 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.isLoaded
    ? _c("div", { staticClass: "single-subtask-edit subtask-nonsortable" }, [
        _c("div", { staticClass: "new-subtask-form" }, [
          _c("div", { staticClass: "input-area" }, [
            _c("div", { staticClass: "input-action-wrap subtask-edit-wrap" }, [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.subTask.editData.title,
                    expression: "subTask.editData.title"
                  }
                ],
                staticClass: "subtask-updpate-title-field input-field",
                attrs: { type: "text" },
                domProps: { value: _vm.subTask.editData.title },
                on: {
                  keyup: function($event) {
                    if (
                      !$event.type.indexOf("key") &&
                      _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
                    ) {
                      return null
                    }
                    return _vm.selfSubTaskFormAction()
                  },
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.$set(_vm.subTask.editData, "title", $event.target.value)
                  }
                }
              }),
              _vm._v(" "),
              _c("div", { staticClass: "action-icons" }, [
                _c(
                  "span",
                  { staticClass: "pm-dark-hover" },
                  [
                    _c("pm-do-action", {
                      attrs: {
                        hook: "pm_pro_subtask_form",
                        actionData: _vm.subTask
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "span",
                  {
                    staticClass: "icon-pm-single-user pm-dark-hover",
                    on: {
                      click: function($event) {
                        if ($event.target !== $event.currentTarget) {
                          return null
                        }
                        $event.preventDefault()
                        return _vm.showHideSubtaskUserAssign()
                      }
                    }
                  },
                  [
                    _vm.subTaskUserAssign
                      ? _c(
                          "div",
                          {
                            staticClass:
                              "pm-multiselect-top pm-multiselect-subtask-task"
                          },
                          [
                            _c(
                              "div",
                              { staticClass: "pm-multiselect-content" },
                              [
                                _c("div", { staticClass: "assign-to" }, [
                                  _vm._v(
                                    _vm._s(
                                      _vm.__(
                                        "Assign to",
                                        "wedevs-project-manager"
                                      )
                                    )
                                  )
                                ]),
                                _vm._v(" "),
                                _c("multiselect", {
                                  attrs: {
                                    options: _vm.task_users,
                                    multiple: false,
                                    "close-on-select": false,
                                    "clear-on-select": true,
                                    "show-labels": true,
                                    searchable: true,
                                    placeholder: "Select User",
                                    "select-label": "",
                                    "selected-label": "selected",
                                    "deselect-label": "",
                                    label: "display_name",
                                    "track-by": "id",
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
                                              attrs: {
                                                src: props.option.avatar_url,
                                                alt: "No Man’s Sky"
                                              }
                                            }),
                                            _vm._v(" "),
                                            _c(
                                              "div",
                                              { staticClass: "option__desc" },
                                              [
                                                _c(
                                                  "span",
                                                  {
                                                    staticClass: "option__title"
                                                  },
                                                  [
                                                    _vm._v(
                                                      _vm._s(
                                                        props.option
                                                          .display_name
                                                      )
                                                    )
                                                  ]
                                                )
                                              ]
                                            )
                                          ]
                                        }
                                      }
                                    ],
                                    null,
                                    false,
                                    487504267
                                  ),
                                  model: {
                                    value: _vm.sub_task_assign,
                                    callback: function($$v) {
                                      _vm.sub_task_assign = $$v
                                    },
                                    expression: "sub_task_assign"
                                  }
                                })
                              ],
                              1
                            )
                          ]
                        )
                      : _vm._e()
                  ]
                ),
                _vm._v(" "),
                _c(
                  "span",
                  {
                    staticClass: "icon-pm-calendar pm-dark-hover",
                    on: {
                      click: function($event) {
                        if ($event.target !== $event.currentTarget) {
                          return null
                        }
                        $event.preventDefault()
                        return _vm.showHideCalendar()
                      }
                    }
                  },
                  [
                    _vm.isActiveCalendar
                      ? _c(
                          "div",
                          { staticClass: "subtask-date" },
                          [
                            _c("pm-content-datepicker", {
                              staticClass:
                                "pm-datepicker-from pm-inline-date-picker-from",
                              attrs: { dependency: "pm-datepickter-to" },
                              model: {
                                value: _vm.subTask.editData.start,
                                callback: function($$v) {
                                  _vm.$set(_vm.subTask.editData, "start", $$v)
                                },
                                expression: "subTask.editData.start"
                              }
                            }),
                            _vm._v(" "),
                            _c("pm-content-datepicker", {
                              staticClass:
                                "pm-datepicker-to pm-inline-date-picker-to",
                              attrs: { dependency: "pm-datepickter-from" },
                              model: {
                                value: _vm.subTask.editData.due,
                                callback: function($$v) {
                                  _vm.$set(_vm.subTask.editData, "due", $$v)
                                },
                                expression: "subTask.editData.due"
                              }
                            })
                          ],
                          1
                        )
                      : _vm._e()
                  ]
                )
              ]),
              _vm._v(" "),
              _c(
                "a",
                {
                  staticClass: "update-button",
                  attrs: { href: "#" },
                  on: {
                    click: function($event) {
                      $event.preventDefault()
                      return _vm.selfSubTaskFormAction()
                    }
                  }
                },
                [_c("span", { staticClass: "icon-pm-check-circle" })]
              )
            ])
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
    require("vue-hot-reload-api")      .rerender("data-v-5ab4b464", esExports)
  }
}

/***/ }),
/* 46 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_subtask_to_task_modal_vue__ = __webpack_require__(15);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4aa5cef4_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_subtask_to_task_modal_vue__ = __webpack_require__(49);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(47)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_subtask_to_task_modal_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_4aa5cef4_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_subtask_to_task_modal_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/subtask-to-task-modal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4aa5cef4", Component.options)
  } else {
    hotAPI.reload("data-v-4aa5cef4", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 47 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(48);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("37d773e5", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4aa5cef4\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./subtask-to-task-modal.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-4aa5cef4\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./subtask-to-task-modal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 48 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.pm-subtask-to-task-wrap {\n  background: rgba(0, 0, 0, 0.59) !important;\n}\n.pm-pro-import-task-modal .pm-pro-form-wrap {\n  background: #eee;\n  opacity: 0.3;\n  display: none;\n}\n.pm-pro-import-task-modal .loading-animation {\n  display: flex;\n  align-items: center;\n  margin-left: 33%;\n  color: #000;\n}\n.pm-pro-import-task-modal .loading-animation .load-spinner {\n  margin: 0;\n}\n.pm-pro-automation .head {\n  width: 500px !important;\n  background-color: #f6f8fa;\n  border-bottom: 1px solid #eee;\n  padding: 16px;\n  font-size: 14px;\n  font-weight: 600;\n  color: #24292e;\n  position: fixed;\n  top: 45px;\n}\n.pm-pro-automation .automation-popup-body .content {\n  padding: 16px;\n  height: 100%;\n}\n.pm-pro-automation .automation-popup-body .content .list-drop-down {\n  z-index: 999999;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__single {\n  width: auto;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__spinner {\n  top: 16px !important;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__select {\n  display: block;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__select:before {\n  position: relative;\n  right: 0;\n  top: 17px;\n  color: #999;\n  margin-top: 4px;\n  border-style: solid;\n  border-width: 5px 5px 0;\n  border-color: #999 transparent transparent;\n  content: \"\";\n  z-index: 999;\n}\n.pm-pro-automation .automation-popup-body .content .tab-link {\n  border-radius: 3px 0px 0px 3px !important;\n}\n.pm-pro-automation .automation-popup-body .content .tasks-wrap {\n  border: 1px solid #f1f1f1;\n  height: 200px;\n  overflow: auto;\n  padding: 10px 10px;\n  color: #555;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .tasks-wrap .incomplete {\n  background: #aa4100;\n  padding: 1px 3px;\n  color: #fff;\n  font-size: 10px;\n  margin-left: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .tasks-wrap .complete {\n  background: #0073aa;\n  padding: 1px 3px;\n  color: #fff;\n  font-size: 10px;\n  margin-left: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .all-select-wrap {\n  border: 1px solid #f1f1f1;\n  padding: 10px;\n  color: #555;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab {\n  display: flex;\n  justify-content: center;\n  margin: 15px 0 !important;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab .first {\n  border-radius: 3px 0px 0px 3px !important;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab .second {\n  border-radius: 0px !important;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab .third {\n  border-radius: 0px 3px 3px 0px !important;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset {\n  color: #24292e;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .select-type {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .select-type select {\n  min-width: 135px;\n  color: #24292e;\n  border-color: #ccc;\n  margin-left: 7px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .type-header {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap {\n  margin-top: 5px;\n  margin-bottom: 10px;\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option .option-image-wrap .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap {\n  margin: 15px 0 8px 15px;\n  padding-left: 14px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .event-checkbox {\n  float: left;\n  margin: 5px 0 0 -22px;\n  vertical-align: middle;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .label-title {\n  color: #24292e;\n  font-weight: 600;\n  font-size: 14px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .note {\n  color: #586069;\n  display: block;\n  font-size: 12px;\n  font-weight: 400;\n  margin: 0;\n}\n.pm-pro-automation .automation-popup-body .content .first-event {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .none {\n  margin-top: 16px;\n  color: #24292e;\n  font-weight: 400;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .type-header {\n  font-weight: 600;\n  font-size: 14px;\n  padding-bottom: 4px;\n  border-bottom: 1px solid #e1e4e8;\n  color: #24292e;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap {\n  margin-top: 20px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-assign-note {\n  margin: 5px 0 0px 7px;\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap {\n  margin-left: 7px;\n  margin-top: 5px;\n  margin-bottom: 10px;\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option .option-image-wrap .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-wrap .event-wrap .event-checkbox {\n  border-radius: 100%;\n}\n.pm-pro-automation .automation-popup-body .button-group {\n  position: fixed;\n  display: block;\n  background: #f6f8fa;\n  width: 500px !important;\n  border-top: 1px solid #eee;\n  padding: 12px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside {\n  display: flex;\n  float: right;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .cancel-btn-wrap {\n  margin-right: 10px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .submit-btn-text {\n  color: #199ed4 !important;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap {\n  position: relative;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap .pm-circle-spinner {\n  position: absolute;\n  left: 50%;\n  top: 50%;\n  margin-left: -16px;\n  margin-top: -11px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap .pm-circle-spinner:after {\n  height: 10px;\n  width: 10px;\n  border-color: #fff #fff #fff transparent;\n}\n.automation-popup-container {\n  width: 500px !important;\n  top: 99px !important;\n  height: 76px !important;\n  border-radius: 0 !important;\n}\n.automation-popup-container .automation-popup-body {\n  height: 100%;\n  width: auto;\n}\n", ""]);

// exports


/***/ }),
/* 49 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass:
        "popup-mask pm-subtask-to-task-wrap pm-pro-automation pm-pro-import-task-modal"
    },
    [
      _c("div", { staticClass: "popup-container automation-popup-container" }, [
        _c("div", { staticClass: "automation-popup-body" }, [
          _c("div", { staticClass: "head" }, [
            _c("span", [_vm._v(_vm._s(_vm.__("Subtask To Task", "pm-pro")))])
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "content" }, [
            !_vm.isListLoaded
              ? _c("div", { staticClass: "loading-animation" }, [
                  _c("div", { staticClass: "loading-projects-title" }, [
                    _vm._v(_vm._s(_vm.__("Loading Task Lists", "pm-pro")))
                  ]),
                  _vm._v(" "),
                  _vm._m(0)
                ])
              : _vm._e(),
            _vm._v(" "),
            _c(
              "div",
              {
                class: !_vm.isListLoaded
                  ? "pm-pro-form-wrap list-drop-down"
                  : "list-drop-down"
              },
              [
                _c("pm-list-drop-down", {
                  attrs: { options: _vm.listDropDownOptions },
                  on: {
                    onChange: _vm.setList,
                    afterGetLists: _vm.afterFetchList
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "button-group" }, [
            _c("div", { staticClass: "button-group-inside" }, [
              _c("div", { staticClass: "cancel-btn-wrap" }, [
                _c(
                  "a",
                  {
                    staticClass: "pm-button pm-secondary",
                    attrs: { href: "#" },
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.close()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Cancel", "pm-pro")))]
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "update-btn-wrap" }, [
                _c(
                  "a",
                  {
                    class: _vm.submitBtnClass(),
                    attrs: { href: "#" },
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.subtaskToTask()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Subtask To Task", "pm-pro")))]
                ),
                _vm._v(" "),
                _vm.submitProcessing
                  ? _c("div", { staticClass: "pm-circle-spinner" })
                  : _vm._e()
              ])
            ])
          ])
        ])
      ])
    ]
  )
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
      _c("div", { staticClass: "rect4" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-4aa5cef4", esExports)
  }
}

/***/ }),
/* 50 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-pro-single-subtask-lists" },
    [
      _c("div", { staticClass: "subtask-title pm-h2" }, [
        _vm._v(_vm._s(_vm.__("Subtasks", "pm-pro")))
      ]),
      _vm._v(" "),
      _c(
        "ul",
        {
          directives: [
            { name: "pm-subtask-sortable", rawName: "v-pm-subtask-sortable" }
          ],
          staticClass: "pm-subtsk-sortable",
          attrs: {
            "data-list": _vm.actionData.task.task_list.data.id,
            "data-task": _vm.actionData.task.id
          }
        },
        _vm._l(_vm.subTasks, function(subtask) {
          return subtask.status == "incomplete"
            ? _c(
                "li",
                {
                  key: subtask.id,
                  staticClass: "subtask-li",
                  attrs: { "data-id": subtask.id, "data-order": subtask.order }
                },
                [
                  subtask.edit_mode && _vm.canEditSubTask(subtask)
                    ? _c(
                        "div",
                        { staticClass: "pm-pro-subtask-task-edit-form" },
                        [
                          _c("subtask-eidt", {
                            attrs: {
                              subTask: subtask,
                              task: _vm.actionData.task,
                              actionData: _vm.actionData
                            }
                          })
                        ],
                        1
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  !subtask.edit_mode
                    ? _c("div", { staticClass: "sub-task-content" }, [
                        _vm._m(0, true),
                        _vm._v(" "),
                        _c("div", [
                          subtask.show_spinner
                            ? _c("span", { staticClass: "pm-spinner" })
                            : _vm._e(),
                          _vm._v(" "),
                          !subtask.show_spinner
                            ? _c("input", {
                                staticClass:
                                  "checkbox pm-pro-subtask-uncomplete",
                                attrs: {
                                  disabled:
                                    !subtask.meta.can_complete_task ||
                                    _vm.isArchivedTaskList(_vm.actionData.task),
                                  type: "checkbox"
                                },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    return _vm.doneUndone(subtask)
                                  }
                                }
                              })
                            : _vm._e()
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "body" }, [
                          _c("div", { staticClass: "header-wrap" }, [
                            _c("div", { staticClass: "title" }, [
                              _c(
                                "span",
                                { staticClass: "pm-pro-subtask-todo-text" },
                                [_vm._v(" " + _vm._s(subtask.title) + " ")]
                              )
                            ]),
                            _vm._v(" "),
                            _c("div", { staticClass: "title-meta" }, [
                              _c(
                                "div",
                                { staticClass: "meta-item assigned-user-wrap" },
                                _vm._l(subtask.assignees.data, function(user) {
                                  return _c(
                                    "div",
                                    {
                                      key: user.id,
                                      staticClass: "assigned-user"
                                    },
                                    [
                                      _c(
                                        "a",
                                        {
                                          staticClass: "user-anchor",
                                          attrs: {
                                            href: _vm.userTaskProfileUrl(
                                              user.id
                                            ),
                                            title: user.display_name
                                          }
                                        },
                                        [
                                          _c("img", {
                                            attrs: {
                                              src: user.avatar_url,
                                              alt: user.display_name,
                                              height: "48",
                                              width: "48"
                                            }
                                          })
                                        ]
                                      )
                                    ]
                                  )
                                }),
                                0
                              ),
                              _vm._v(" "),
                              _vm.canEditSubTask(subtask)
                                ? _c(
                                    "div",
                                    { staticClass: "meta-item copy-task" },
                                    [
                                      _c(
                                        "a",
                                        {
                                          attrs: { href: "#" },
                                          on: {
                                            click: function($event) {
                                              $event.preventDefault()
                                              return _vm.activeSubtaskToTaskModal(
                                                subtask
                                              )
                                            }
                                          }
                                        },
                                        [
                                          _c("span", {
                                            staticClass:
                                              "flaticon-pm-copy-files"
                                          })
                                        ]
                                      )
                                    ]
                                  )
                                : _vm._e(),
                              _vm._v(" "),
                              _c(
                                "div",
                                {
                                  class:
                                    "meta-item subtask-date-wrap " +
                                    _vm.subTaskDateWrap(
                                      subtask.due_date.date,
                                      subtask.start_at.date,
                                      _vm.task_start_field
                                    )
                                },
                                [
                                  _vm.task_start_field
                                    ? _c("span", [
                                        _vm._v(
                                          _vm._s(
                                            _vm.shortDateFormat(
                                              subtask.start_at.date
                                            )
                                          )
                                        )
                                      ])
                                    : _vm._e(),
                                  _vm._v(" "),
                                  _vm.isBetweenDate(
                                    _vm.task_start_field,
                                    subtask.start_at.date,
                                    subtask.due_date.date
                                  )
                                    ? _c("span", [_vm._v("–")])
                                    : _vm._e(),
                                  _vm._v(" "),
                                  _c("span", [
                                    _vm._v(
                                      _vm._s(
                                        _vm.shortDateFormat(
                                          subtask.due_date.date
                                        )
                                      )
                                    )
                                  ])
                                ]
                              ),
                              _vm._v(" "),
                              _vm.canEditSubTask(subtask)
                                ? _c("div", {
                                    staticClass:
                                      "meta-item icon-pm-pencil pm-dark-hover",
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        return _vm.editSubTask(subtask)
                                      }
                                    }
                                  })
                                : _vm._e(),
                              _vm._v(" "),
                              _vm.canEditSubTask(subtask)
                                ? _c("div", {
                                    staticClass: "meta-item icon-pm-delete",
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        return _vm.deleteSelfSubTask(
                                          subtask,
                                          _vm.actionData.task
                                        )
                                      }
                                    }
                                  })
                                : _vm._e()
                            ])
                          ])
                        ])
                      ])
                    : _vm._e()
                ]
              )
            : _vm._e()
        }),
        0
      ),
      _vm._v(" "),
      _vm.actionData.task
        ? _c(
            "ul",
            _vm._l(_vm.actionData.task.sub_tasks, function(subtask) {
              return subtask.status == "complete"
                ? _c("li", { key: subtask.ID, staticClass: "subtask-li" }, [
                    _c("div", { staticClass: "sub-task-content" }, [
                      _c("div", [
                        subtask.show_spinner
                          ? _c("span", { staticClass: "pm-spinner" })
                          : _vm._e(),
                        _vm._v(" "),
                        !subtask.show_spinner
                          ? _c("input", {
                              staticClass: "pm-pro-subtask-complete",
                              attrs: {
                                type: "checkbox",
                                disabled:
                                  !subtask.meta.can_complete_task ||
                                  _vm.isArchivedTaskList(_vm.actionData.task),
                                checked: "checked"
                              },
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.doneUndone(subtask)
                                }
                              }
                            })
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "body" }, [
                        _c("div", { staticClass: "header-wrap" }, [
                          _c("div", { staticClass: "title complete-title" }, [
                            _c(
                              "span",
                              { staticClass: "pm-pro-subtask-todo-text" },
                              [_vm._v(" " + _vm._s(subtask.title) + " ")]
                            )
                          ]),
                          _vm._v(" "),
                          _c("div", { staticClass: "title-meta" }, [
                            _c(
                              "div",
                              { staticClass: "meta-item assigned-user-wrap" },
                              _vm._l(subtask.assignees.data, function(user) {
                                return _c(
                                  "div",
                                  {
                                    key: user.id,
                                    staticClass: "assigned-user"
                                  },
                                  [
                                    _c(
                                      "a",
                                      {
                                        staticClass: "user-anchor",
                                        attrs: {
                                          href: _vm.userTaskProfileUrl(user.id),
                                          title: user.display_name
                                        }
                                      },
                                      [
                                        _c("img", {
                                          attrs: {
                                            src: user.avatar_url,
                                            alt: user.display_name,
                                            height: "48",
                                            width: "48"
                                          }
                                        })
                                      ]
                                    )
                                  ]
                                )
                              }),
                              0
                            ),
                            _vm._v(" "),
                            _c(
                              "div",
                              {
                                class:
                                  "meta-item " +
                                  _vm.subTaskDateWrap(
                                    subtask.due_date.date,
                                    subtask.start_at.date,
                                    _vm.task_start_field
                                  )
                              },
                              [
                                _vm.task_start_field
                                  ? _c("span", [
                                      _vm._v(
                                        _vm._s(
                                          _vm.shortDateFormat(
                                            subtask.start_at.date
                                          )
                                        )
                                      )
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                _vm.isBetweenDate(
                                  _vm.task_start_field,
                                  subtask.start_at.date,
                                  subtask.due_date.date
                                )
                                  ? _c("span", [_vm._v("–")])
                                  : _vm._e(),
                                _vm._v(" "),
                                _c("span", [
                                  _vm._v(
                                    _vm._s(
                                      _vm.shortDateFormat(subtask.due_date.date)
                                    )
                                  )
                                ])
                              ]
                            ),
                            _vm._v(" "),
                            _vm.canEditSubTask(subtask)
                              ? _c("div", {
                                  staticClass: "meta-item icon-pm-delete",
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      return _vm.deleteSelfSubTask(
                                        subtask,
                                        _vm.actionData.task
                                      )
                                    }
                                  }
                                })
                              : _vm._e()
                          ])
                        ])
                      ])
                    ])
                  ])
                : _vm._e()
            }),
            0
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.isSubtaskToTaskModalActive
        ? _c("subtask-to-task-modal", {
            attrs: { subtask: _vm.modalSubtask },
            on: {
              afterMoveSubtaskToTask: _vm.afterMoveSubtaskToTask,
              closeSubtaskToTaskModal: function($event) {
                _vm.isSubtaskToTaskModalActive = false
              }
            }
          })
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
    return _c("div", { staticClass: "subtask-move pm-sub-task-handaler" }, [
      _c("span", { staticClass: "icon-pm-drag-drop" })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-a80421fe", esExports)
  }
}

/***/ }),
/* 51 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "singleSubtask" },
    [
      _c("single-subtask-lists", { attrs: { actionData: _vm.actionData } }),
      _vm._v(" "),
      _vm.canCraeteSubTask(_vm.actionData.task)
        ? _c("div", { staticClass: "new-subtask-form" }, [
            !_vm.createForm
              ? _c(
                  "div",
                  {
                    staticClass: "create-area",
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.showHideCreateForm()
                      }
                    }
                  },
                  [
                    _c("span", { staticClass: "icon-plus" }, [_vm._v("+")]),
                    _vm._v(
                      "\n                " +
                        _vm._s(_vm.__("Add a subtask", "pm-pro")) +
                        "\n            "
                    )
                  ]
                )
              : _vm._e(),
            _vm._v(" "),
            _vm.createForm
              ? _c("div", { staticClass: "input-area" }, [
                  _c("div", { staticClass: "input-action-wrap" }, [
                    _c("div", [
                      _vm.show_spinner
                        ? _c("span", { staticClass: "pm-spinner" })
                        : _vm._e(),
                      _vm._v(" "),
                      !_vm.show_spinner
                        ? _c("span", { staticClass: "plus-text" }, [
                            _vm._v("+")
                          ])
                        : _vm._e()
                    ]),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.subTask.title,
                          expression: "subTask.title"
                        }
                      ],
                      staticClass: "subtask-title-field input-field",
                      attrs: { type: "text" },
                      domProps: { value: _vm.subTask.title },
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
                          return _vm.selfSubTaskFormAction()
                        },
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.subTask, "title", $event.target.value)
                        }
                      }
                    }),
                    _vm._v(" "),
                    _c("div", { staticClass: "action-icons" }, [
                      _c(
                        "span",
                        { staticClass: "pm-dark-hover" },
                        [
                          _c("pm-do-action", {
                            attrs: {
                              hook: "pm_pro_subtask_form",
                              actionData: _vm.subTask
                            }
                          })
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "span",
                        {
                          staticClass: "icon-pm-single-user pm-dark-hover",
                          on: {
                            click: function($event) {
                              if ($event.target !== $event.currentTarget) {
                                return null
                              }
                              $event.preventDefault()
                              return _vm.showHideSubtaskUserAssign()
                            }
                          }
                        },
                        [
                          _vm.subTaskUserAssign
                            ? _c(
                                "div",
                                {
                                  staticClass:
                                    "pm-multiselect-top pm-multiselect-subtask-task"
                                },
                                [
                                  _c(
                                    "div",
                                    { staticClass: "pm-multiselect-content" },
                                    [
                                      _c("div", { staticClass: "assign-to" }, [
                                        _vm._v(
                                          _vm._s(
                                            _vm.__(
                                              "Assign to",
                                              "wedevs-project-manager"
                                            )
                                          )
                                        )
                                      ]),
                                      _vm._v(" "),
                                      _c("multiselect", {
                                        attrs: {
                                          options: _vm.task_users,
                                          multiple: false,
                                          "close-on-select": false,
                                          "clear-on-select": true,
                                          "show-labels": true,
                                          searchable: true,
                                          placeholder: "Select User",
                                          "select-label": "",
                                          "selected-label": "selected",
                                          "deselect-label": "",
                                          label: "display_name",
                                          "track-by": "id",
                                          "allow-empty": true
                                        },
                                        scopedSlots: _vm._u(
                                          [
                                            {
                                              key: "option",
                                              fn: function(props) {
                                                return [
                                                  _c("img", {
                                                    staticClass:
                                                      "option__image",
                                                    attrs: {
                                                      src:
                                                        props.option.avatar_url,
                                                      alt: "No Man’s Sky"
                                                    }
                                                  }),
                                                  _vm._v(" "),
                                                  _c(
                                                    "div",
                                                    {
                                                      staticClass:
                                                        "option__desc"
                                                    },
                                                    [
                                                      _c(
                                                        "span",
                                                        {
                                                          staticClass:
                                                            "option__title"
                                                        },
                                                        [
                                                          _vm._v(
                                                            _vm._s(
                                                              props.option
                                                                .display_name
                                                            )
                                                          )
                                                        ]
                                                      )
                                                    ]
                                                  )
                                                ]
                                              }
                                            }
                                          ],
                                          null,
                                          false,
                                          487504267
                                        ),
                                        model: {
                                          value: _vm.sub_task_assign,
                                          callback: function($$v) {
                                            _vm.sub_task_assign = $$v
                                          },
                                          expression: "sub_task_assign"
                                        }
                                      })
                                    ],
                                    1
                                  )
                                ]
                              )
                            : _vm._e()
                        ]
                      ),
                      _vm._v(" "),
                      _c("span", {
                        staticClass: "icon-pm-calendar pm-dark-hover",
                        on: {
                          click: function($event) {
                            if ($event.target !== $event.currentTarget) {
                              return null
                            }
                            $event.preventDefault()
                            return _vm.showHideCalendar()
                          }
                        }
                      })
                    ]),
                    _vm._v(" "),
                    _vm.isActiveCalendar
                      ? _c("div", { staticClass: "subtask-date" }, [
                          _c("div", {
                            directives: [
                              {
                                name: "pm-datepicker",
                                rawName: "v-pm-datepicker",
                                value: "subTaskForm",
                                expression: "'subTaskForm'"
                              }
                            ],
                            staticClass:
                              "pm-date-picker-from pm-inline-date-picker-from",
                            model: {
                              value: _vm.subTask.start_at,
                              callback: function($$v) {
                                _vm.$set(_vm.subTask, "start_at", $$v)
                              },
                              expression: "subTask.start_at"
                            }
                          }),
                          _vm._v(" "),
                          _c("div", {
                            directives: [
                              {
                                name: "pm-datepicker",
                                rawName: "v-pm-datepicker",
                                value: "subTaskForm",
                                expression: "'subTaskForm'"
                              }
                            ],
                            staticClass:
                              "pm-date-picker-to pm-inline-date-picker-to",
                            model: {
                              value: _vm.subTask.due_date,
                              callback: function($$v) {
                                _vm.$set(_vm.subTask, "due_date", $$v)
                              },
                              expression: "subTask.due_date"
                            }
                          })
                        ])
                      : _vm._e()
                  ])
                ])
              : _vm._e()
          ])
        : _vm._e()
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
    require("vue-hot-reload-api")      .rerender("data-v-46719680", esExports)
  }
}

/***/ }),
/* 52 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_slack_vue__ = __webpack_require__(16);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_31ba92b2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_slack_vue__ = __webpack_require__(53);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_slack_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_31ba92b2_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_slack_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/sub_tasks/views/assets/src/components/slack.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-31ba92b2", Component.options)
  } else {
    hotAPI.reload("data-v-31ba92b2", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 53 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("h3", { staticClass: "pm-pro-slack-field-set" }, [
      _vm._v(
        "\n        " +
          _vm._s(_vm.__("Subtask", "wedevs-project-manager")) +
          "\n    "
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "pm-pro-slack-field" }, [
      _c(
        "div",
        {
          staticClass: "pm-pro-slack-field-wrap pm-pro-slack-field-wrap-right"
        },
        [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.actionData.options.subTasks.create,
                expression: "actionData.options.subTasks.create"
              }
            ],
            attrs: { type: "checkbox", id: "slack-subtask-create-box" },
            domProps: {
              checked: Array.isArray(_vm.actionData.options.subTasks.create)
                ? _vm._i(_vm.actionData.options.subTasks.create, null) > -1
                : _vm.actionData.options.subTasks.create
            },
            on: {
              change: function($event) {
                var $$a = _vm.actionData.options.subTasks.create,
                  $$el = $event.target,
                  $$c = $$el.checked ? true : false
                if (Array.isArray($$a)) {
                  var $$v = null,
                    $$i = _vm._i($$a, $$v)
                  if ($$el.checked) {
                    $$i < 0 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "create",
                        $$a.concat([$$v])
                      )
                  } else {
                    $$i > -1 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "create",
                        $$a.slice(0, $$i).concat($$a.slice($$i + 1))
                      )
                  }
                } else {
                  _vm.$set(_vm.actionData.options.subTasks, "create", $$c)
                }
              }
            }
          }),
          _vm._v(" "),
          _c("label", { attrs: { for: "slack-subtask-create-box" } }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.__("Create", "wedevs-project-manager")) +
                "\n            "
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "pm-pro-slack-field-wrap pm-pro-slack-field-wrap-left" },
        [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.actionData.options.subTasks.update,
                expression: "actionData.options.subTasks.update"
              }
            ],
            attrs: { type: "checkbox", id: "slack-subtask-update-box" },
            domProps: {
              checked: Array.isArray(_vm.actionData.options.subTasks.update)
                ? _vm._i(_vm.actionData.options.subTasks.update, null) > -1
                : _vm.actionData.options.subTasks.update
            },
            on: {
              change: function($event) {
                var $$a = _vm.actionData.options.subTasks.update,
                  $$el = $event.target,
                  $$c = $$el.checked ? true : false
                if (Array.isArray($$a)) {
                  var $$v = null,
                    $$i = _vm._i($$a, $$v)
                  if ($$el.checked) {
                    $$i < 0 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "update",
                        $$a.concat([$$v])
                      )
                  } else {
                    $$i > -1 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "update",
                        $$a.slice(0, $$i).concat($$a.slice($$i + 1))
                      )
                  }
                } else {
                  _vm.$set(_vm.actionData.options.subTasks, "update", $$c)
                }
              }
            }
          }),
          _vm._v(" "),
          _c("label", { attrs: { for: "slack-subtask-update-box" } }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.__("Update", "wedevs-project-manager")) +
                "\n            "
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c("div", { staticClass: "pm-clearfix" })
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "pm-pro-slack-field" }, [
      _c(
        "div",
        {
          staticClass: "pm-pro-slack-field-wrap pm-pro-slack-field-wrap-right"
        },
        [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.actionData.options.subTasks.complete,
                expression: "actionData.options.subTasks.complete"
              }
            ],
            attrs: { type: "checkbox", id: "slack-subtask-done-box" },
            domProps: {
              checked: Array.isArray(_vm.actionData.options.subTasks.complete)
                ? _vm._i(_vm.actionData.options.subTasks.complete, null) > -1
                : _vm.actionData.options.subTasks.complete
            },
            on: {
              change: function($event) {
                var $$a = _vm.actionData.options.subTasks.complete,
                  $$el = $event.target,
                  $$c = $$el.checked ? true : false
                if (Array.isArray($$a)) {
                  var $$v = null,
                    $$i = _vm._i($$a, $$v)
                  if ($$el.checked) {
                    $$i < 0 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "complete",
                        $$a.concat([$$v])
                      )
                  } else {
                    $$i > -1 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "complete",
                        $$a.slice(0, $$i).concat($$a.slice($$i + 1))
                      )
                  }
                } else {
                  _vm.$set(_vm.actionData.options.subTasks, "complete", $$c)
                }
              }
            }
          }),
          _vm._v(" "),
          _c("label", { attrs: { for: "slack-subtask-done-box" } }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.__("Complete", "wedevs-project-manager")) +
                "\n            "
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "pm-pro-slack-field-wrap pm-pro-slack-field-wrap-left" },
        [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.actionData.options.subTasks.incomplete,
                expression: "actionData.options.subTasks.incomplete"
              }
            ],
            attrs: { type: "checkbox", id: "slack-subtask-undone-box" },
            domProps: {
              checked: Array.isArray(_vm.actionData.options.subTasks.incomplete)
                ? _vm._i(_vm.actionData.options.subTasks.incomplete, null) > -1
                : _vm.actionData.options.subTasks.incomplete
            },
            on: {
              change: function($event) {
                var $$a = _vm.actionData.options.subTasks.incomplete,
                  $$el = $event.target,
                  $$c = $$el.checked ? true : false
                if (Array.isArray($$a)) {
                  var $$v = null,
                    $$i = _vm._i($$a, $$v)
                  if ($$el.checked) {
                    $$i < 0 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "incomplete",
                        $$a.concat([$$v])
                      )
                  } else {
                    $$i > -1 &&
                      _vm.$set(
                        _vm.actionData.options.subTasks,
                        "incomplete",
                        $$a.slice(0, $$i).concat($$a.slice($$i + 1))
                      )
                  }
                } else {
                  _vm.$set(_vm.actionData.options.subTasks, "incomplete", $$c)
                }
              }
            }
          }),
          _vm._v(" "),
          _c("label", { attrs: { for: "slack-subtask-undone-box" } }, [
            _vm._v(
              "\n                " +
                _vm._s(_vm.__("Incomplete", "wedevs-project-manager")) +
                "\n            "
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c("div", { staticClass: "pm-clearfix" })
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
    require("vue-hot-reload-api")      .rerender("data-v-31ba92b2", esExports)
  }
}

/***/ })
/******/ ]);