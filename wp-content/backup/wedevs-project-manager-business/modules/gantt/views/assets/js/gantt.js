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
/******/ 	return __webpack_require__(__webpack_require__.s = 8);
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
        return {
            abort: false
        };
    },


    methods: {
        removeLink: function removeLink(link_id) {
            var self = this;
            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/gantt/' + link_id + '/delete',
                type: 'POST',
                data: {},
                success: function success(res) {},
                error: function error(res) {
                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        saveLink: function saveLink(link) {
            var self = this;
            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/gantt',
                type: 'POST',
                data: link,
                success: function success(res) {},
                error: function error(res) {
                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        },
        updateList: function updateList(list) {
            var index = this.getIndex(this.$store.state.gantt.ganttLists, list.list_id, 'id');
            var updateList = this.$store.state.gantt.ganttLists[index];

            this.$store.commit('gantt/setUpdateList', updateList);

            this.listFormActivity(true);
        },
        updateGanttTask: function updateGanttTask(task) {
            var list_index = this.getIndex(this.$store.state.gantt.ganttLists, task.list_id, 'id');
            var task_index = this.getIndex(this.$store.state.gantt.ganttLists[list_index].incomplete_tasks.data, task.id, 'id');

            var task = this.$store.state.gantt.ganttLists[list_index].incomplete_tasks.data[task_index];

            this.$store.commit('gantt/setUpdateTask', task);
            this.taskFormActivity(true);
            this.setNewTask(false);
        },
        updateTask: function updateTask(task) {
            var self = this;
            var due_date = pm.Moment(task.end_date).subtract(1, "days");
            var request_data = {
                url: self.base_url + '/pm/v2/projects/' + self.project_id + '/tasks/' + task.id + '/update',
                type: 'POST',
                data: {
                    title: task.text,
                    start_at: pm.Moment(task.start_date).format('YYYY-MM-DD'),
                    due_date: pm.Moment(due_date).format('YYYY-MM-DD')
                },
                success: function success(res) {
                    pm.NProgress.done();
                },
                error: function error(res) {
                    //Showing error
                    // res.responseJSON.message.forEach( function( value, index ) {
                    //     pm.Toastr.error(value);
                    // });
                }
            };

            self.httpRequest(request_data);
        },

        /**
        * Retrive All task list
        * @param  {[object]}   args SSR url condition
        * @param  {Function} callback  [description]
        * @return {[void]}             [description]
        */
        getLists: function getLists(args) {
            var self = this,
                pre_define = {
                condition: {
                    with: 'incomplete_tasks,complete_tasks',
                    per_page: 1000, //this.getSettings('list_per_page', 10),
                    page: this.setCurrentPageNumber()
                },
                callback: false
            };

            var args = jQuery.extend(true, pre_define, args);
            var condition = this.generateConditions(args.condition);
            var request = {
                url: self.base_url + '/pm/v2/projects/' + self.project_id + '/task-lists?' + condition,
                success: function success(res) {

                    var tasks = self.filterTasks(res.data, true);
                    var links = self.filterLinks(res.data);

                    self.$store.commit('gantt/setList', res.data);
                    self.$store.commit('gantt/setTasks', tasks);
                    self.$store.commit('gantt/setLinks', links);
                    pm.NProgress.done();

                    if (typeof args.callback === 'function') {
                        args.callback(self, res);
                    }
                }
            };
            self.httpRequest(request);
        },
        generatePassword: function generatePassword() {
            var length = 15,
                charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                retVal = "";
            for (var i = 0, n = charset.length; i < length; ++i) {
                retVal += charset.charAt(Math.floor(Math.random() * n));
            }
            return retVal;
        },
        filterTasks: function filterTasks(lists, withProject) {
            var self = this;
            var tasks = [];
            withProject = withProject || false;

            if (withProject) {
                var project_start = '',
                    duration = '';

                if (typeof this.$store.state.project.meta != 'undefined' && this.$store.state.project.meta.data.total_task_lists < 1) {
                    project_start = this.$store.state.project.created_at.date;
                    project_start = new Date(project_start);
                    project_start = pm.Moment(project_start).format('DD-MM-YYYY');
                    duration = 1;
                }

                tasks.push({
                    "id": this.project_id + '-project',
                    "text": this.$store.state.project.title,
                    "start_date": project_start,
                    "duration": duration,
                    "progress": 0,
                    "open": true,
                    'type': 'project',
                    'group': 'Project',
                    'action': 'Add',
                    'project_id': this.project_id
                });
            }

            lists.forEach(function (list) {

                var task_length = list.incomplete_tasks.data.length;
                var complete_task_length = list.complete_tasks.data.length;

                var list_start = '',
                    duration = '';

                if (!task_length && !complete_task_length) {
                    list_start = list.created_at.date;
                    list_start = new Date(list_start);
                    list_start = pm.Moment(list_start).format('DD-MM-YYYY');
                    duration = 1;
                }

                tasks.push({
                    'id': list.id + '-list',
                    'text': list.title,
                    'start_date': list_start,
                    'duration': duration,
                    'progress': '',
                    'parent': self.project_id + '-project',
                    'open': true,
                    'type': 'list',
                    'group': 'List',
                    'new_event': 'Add',
                    'list_id': list.id
                });

                list.incomplete_tasks.data.forEach(function (task) {

                    var start = task.start_at.date ? task.start_at.date : task.created_at.date;
                    var end = task.due_date.date ? task.due_date.date : start;
                    var start_date = new Date(start);
                    var end_date = new Date(end);
                    end_date = pm.Moment(end_date, 'DD-MM-YYYY').add(1, 'days');

                    tasks.push({
                        'id': task.id,
                        'text': task.title,
                        'start_date': pm.Moment(start_date).format('DD-MM-YYYY'),
                        'end_date': end_date._d,
                        //'duration': self.getDuration(task),
                        'progress': 0,
                        'parent': list.id + '-list',
                        'list_id': task.task_list_id,
                        'open': true,
                        'priority': 3,
                        'type': 'task',
                        'group': 'Task',
                        'new_event': ''
                    });
                });

                list.complete_tasks.data.forEach(function (task) {
                    var start = task.start_at.date ? task.start_at.date : task.created_at.date;
                    var start_date = new Date(start);

                    tasks.push({
                        'id': task.id,
                        'text': task.title,
                        'start_date': pm.Moment(start_date).format('DD-MM-YYYY'),
                        'duration': self.getDuration(task),
                        'progress': 0,
                        'parent': list.id + '-task',
                        'list_id': task.task_list_id,
                        'open': true,
                        'priority': 3,
                        'type': 'complete_task',
                        'group': 'Task',
                        'new_event': ''
                    });
                });
            });

            return tasks;
        },
        getDuration: function getDuration(task) {
            var start = task.start_at.date ? task.start_at.date : task.created_at.date;
            var due = task.due_date.date ? task.due_date.date : start;
            var start_date = pm.Moment(new Date(start));
            var due_date = pm.Moment(new Date(due));

            var duration = pm.Moment.duration(due_date.diff(start_date));
            var days = duration.asDays();

            return parseInt(days) + 1;
        },
        filterLinks: function filterLinks(lists) {
            var links = [];
            lists.forEach(function (list) {
                list.incomplete_tasks.data.forEach(function (task) {
                    task.gantt_links.data.forEach(function (link) {
                        links.push({
                            "id": link.id,
                            "source": link.source,
                            "target": link.target,
                            "type": link.type
                        });
                    });
                });
            });

            return links;
        },
        listFormActivity: function listFormActivity(status) {
            this.$store.commit('gantt/changeListFormStatus', status);
        },
        taskFormActivity: function taskFormActivity(status) {
            this.$store.commit('gantt/changeTaskFormStatus', status);
        },
        setListId: function setListId(listId) {
            this.$store.commit('gantt/setListId', listId);
        },
        setNewTask: function setNewTask(status) {
            this.$store.commit('gantt/setNewTask', status);
        },
        createList: function createList(callback) {
            var self = this;
            this.listFormActivity(true);
        },
        createTask: function createTask(list_id) {
            this.setListId(list_id);
            this.taskFormActivity(true);
            this.setNewTask(true);
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

var listToStyles = __webpack_require__(14)

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
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__list_form_vue__ = __webpack_require__(15);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__task_form_vue__ = __webpack_require__(19);
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
        next(function (vm) {});
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_2__helpers_mixin_mixin___default.a],

    data: function data() {
        return {
            loading: true,
            taskFormSubmit: false
        };
    },
    created: function created() {
        var self = this;
        self.getGlobalMilestones();

        pmBus.$on('pm_after_create_list', this.afterCreateList);
        pmBus.$on('pm_after_create_task', this.afterCreateTask);
        pmBus.$on('pm_after_update_list', this.afterUpdateList);
        pmBus.$on('pm_after_update_task', this.afterUpdateTask);
        pmBus.$on('pm_after_fetch_project', this.selfGetLists);

        if (this.$route.name == 'gantt') {
            pm_add_filter('before_task_save', function (data) {
                if (typeof self.$store.state.gantt !== 'undefined') {
                    data.board_id = self.$store.state.gantt.list_id;
                }
                return data;
            });
        }
    },
    destroyed: function destroyed() {
        pm_remove_filter('before_task_save', '');
    },


    components: {
        'list-form': __WEBPACK_IMPORTED_MODULE_0__list_form_vue__["a" /* default */],
        'task-form': __WEBPACK_IMPORTED_MODULE_1__task_form_vue__["a" /* default */]
    },

    computed: {
        project: function project() {
            var self = this;
            var project = this.$store.state.project;

            if (_.isEmpty(project)) {
                return false;
            }

            return project;
        },
        tasks: function tasks() {
            if (this.$store.state.gantt.tasks.length) {
                return this.$store.state.gantt.tasks;
            }

            return [];
        },
        links: function links() {
            return this.$store.state.gantt.links;
        },
        isActiveListForm: function isActiveListForm() {
            return this.$store.state.gantt.isActiveListForm;
        },
        isActiveTaskForm: function isActiveTaskForm() {
            return this.$store.state.gantt.isActiveTaskForm;
        },
        updatedList: function updatedList() {
            return this.$store.state.gantt.updatedList;
        }
    },

    methods: {
        selfGetLists: function selfGetLists() {
            var self = this;
            //Fetch todo lists after getting project
            this.getLists({
                callback: function callback() {
                    self.loading = false;
                }
            });
        },
        afterUpdateList: function afterUpdateList(res) {
            this.ganttAddListMeta(res.data);

            this.$store.commit('gantt/afterUpdateList', res.data);

            gantt.getTask(res.data.list_id + '-list').text = res.data.title; //changes task's data
            // gantt.updateTask(res.data.id + '-list');
            this.listFormActivity(false);
        },
        afterUpdateTask: function afterUpdateTask(res) {
            this.taskFormSubmit = true;
            this.$store.commit('gantt/afterUpdateTask', res.data);

            var start = res.data.start_at.date ? res.data.start_at.date : res.data.created_at.date;
            var end = res.data.due_date.date ? res.data.due_date.date : start;
            var start_date = new Date(start);
            var end_date = new Date(end);
            end_date.setDate(end_date.getDate() + 1);

            gantt.getTask(res.data.id).text = res.data.title; //changes task's data
            gantt.getTask(res.data.id).start_date = start_date; //pm.Moment(start_date).format('DD-MM-YYYY');
            gantt.getTask(res.data.id).end_date = end_date;

            gantt.updateTask(res.data.id);
            this.taskFormActivity(false);
        },
        afterCreateList: function afterCreateList(res) {
            this.ganttAddListMeta(res.data);
            this.$store.commit('gantt/afterCreateNewList', res.data);

            var prevTasks = gantt.$data.tasksStore.pull;
            var list_start = '',
                duration = '';

            if (!res.data.incomplete_tasks.data.length) {
                list_start = res.data.created_at.date;
                list_start = new Date(list_start);
                list_start = pm.Moment(list_start).format('DD-MM-YYYY');
                duration = 1;
            }

            this.listFormActivity(false);

            var tasks = [];

            var list = {
                "id": res.data.id + '-list',
                "text": res.data.title,
                "start_date": list_start,
                "duration": duration,
                "progress": 0,
                "open": true,
                'action': 'Add',
                'parent': this.project_id + '-project',
                'type': 'list',
                'group': 'List',
                'new_event': 'Add',
                'list_id': res.data.id
            };

            gantt.addTask(list, this.project_id + '-project', 0);
        },
        ganttAddListMeta: function ganttAddListMeta(list) {
            list.list_id = list.id;

            if (typeof list.incomplete_tasks == 'undefined') {
                list.incomplete_tasks = [];
            }
        },
        afterCreateTask: function afterCreateTask(res, args) {
            if (this.$route.name != 'gantt') {
                return;
            }

            this.$store.commit('gantt/afterCreateTask', {
                listId: res.data.task_list.data.id,
                task: res.data
            });

            var start = res.data.start_at.date ? res.data.start_at.date : res.data.created_at.date;
            var end = res.data.due_date.date ? res.data.due_date.date : start;
            var start_date = new Date(start);
            var end_date = new Date(end);
            end_date = pm.Moment(end_date, 'DD-MM-YYYY').add(1, 'days');

            this.taskFormActivity(false);

            var newTask = {
                "id": res.data.id,
                'index': 1,
                "text": res.data.title,
                "start_date": pm.Moment(start_date).format('DD-MM-YYYY'),
                "end_date": end_date._d,
                "progress": 0,
                "open": true,
                'type': 'task',
                'group': 'Task',
                'parent': res.data.task_list.data.id + '-list',
                'list_id': res.data.task_list.data.id
            };

            gantt.addTask(newTask);

            this.setListId(false);
        }
    }

});

/***/ }),
/* 5 */
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



/* harmony default export */ __webpack_exports__["a"] = ({
    props: {
        list: {
            type: [Object],
            default: function _default() {
                return {};
            }
        }
    },
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    methods: {
        hasList: function hasList(list) {
            if (list.hasOwnProperty('id')) {
                return true;
            }

            return false;
        },
        deleteList: function deleteList(list) {

            var self = this;

            var request_data = {
                url: self.base_url + '/pm/v2/projects/' + self.project_id + '/task-lists/' + list.id,
                type: 'DELETE',
                data: {},
                success: function success(res) {
                    gantt.deleteTask(list.id + '-list');
                    self.$store.commit('gantt/deleteTask', list.id);
                    self.listFormActivity(false);
                },
                error: function error(res) {
                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
        }
    }
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
//
//
//
//
//
//
//
//
//
//
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
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],
    props: {
        task: {
            type: [Object],
            default: function _default() {
                return {};
            }
        }
    },
    computed: {
        isNewTask: function isNewTask() {
            return this.$store.state.gantt.newTask;
        },

        updateTask: {
            get: function get() {
                return this.$store.state.gantt.updatedTask;
            },
            set: function set() {}
        },

        list: function list() {
            return {
                id: this.$store.state.gantt.list_id
            };
        },
        tasks: function tasks() {
            if (this.$store.state.gantt.tasks.length) {
                return this.$store.state.gantt.tasks;
            }

            return [];
        }
    },
    methods: {
        hasTask: function hasTask(task) {
            if (task.hasOwnProperty('id')) {
                return true;
            }

            return false;
        },
        deleteTask: function deleteTask(task) {

            var self = this;

            var request_data = {
                url: self.base_url + '/pm/v2/projects/' + self.project_id + '/tasks/' + task.id + '/delete',
                type: 'POST',
                data: {},
                success: function success(res) {
                    gantt.deleteTask(task.id);

                    self.$store.commit('gantt/afterDeleteTask', {
                        'task': task,
                        'list': task.task_list.data
                    });

                    self.taskFormActivity(false);
                },
                error: function error(res) {
                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                }
            };

            self.httpRequest(request_data);
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
//
//
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
            type: [Array, Object],
            default: function _default() {
                return [];
            }
        }
    },
    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],

    watch: {
        actionData: function actionData(menu) {}
    },

    methods: {
        setActiveMenu: function setActiveMenu(item) {
            var name = this.$route.name;

            if (name == item) {
                return 'active';
            }
        }
    }
});

/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/gantt/views/assets/js/';

__webpack_require__(9);

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(10);

var _router2 = _interopRequireDefault(_router);

var _directive = __webpack_require__(24);

var _directive2 = _interopRequireDefault(_directive);

var _tabMenu = __webpack_require__(25);

var _tabMenu2 = _interopRequireDefault(_tabMenu);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevs_PM_Components.push({
	hook: 'pm-header-menu',
	component: 'gantt-tab-menu',
	property: _tabMenu2.default
});

// const Gantt = resolve => {
//     require.ensure(['./components/tab-menu.vue'], () => {
//         resolve(require('./components/tab-menu.vue'));
//     });
// }

//import 'dhtmlx-gantt';

/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _gantt = __webpack_require__(11);

var _gantt2 = _interopRequireDefault(_gantt);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevsPmProAddonRegisterModule('gantt', 'gantt');

// const gantt = resolve => {
//     require.ensure(['./../components/gantt.vue'], () => {
//         resolve(require('./../components/gantt.vue'));
//     });
// }

weDevsPMRegisterChildrenRoute('project_root', [{
    path: ':project_id/gantt/',
    component: _gantt2.default,
    name: 'gantt'
}]);

/***/ }),
/* 11 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_gantt_vue__ = __webpack_require__(4);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_071dfe30_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_gantt_vue__ = __webpack_require__(23);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(12)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_gantt_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_071dfe30_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_gantt_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/gantt/views/assets/src/components/gantt.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-071dfe30", Component.options)
  } else {
    hotAPI.reload("data-v-071dfe30", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(13);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("4fe56ace", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-071dfe30\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./gantt.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-071dfe30\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./gantt.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.pm-gantt-wrap {\n    margin-top: 10px;\n}\n#pm-gantt {\n    width: 100%;\n    height: calc(100vh - 280px);\n}\n.gantt_task_line.pm-pro-gantt-project {\n    background: #9B59B6 !important;\n    font-weight: 800;\n    border: none;\n}\n.gantt_task_line.pm-pro-gantt-list {\n    background: #00AEFF;\n    font-weight: 600;\n    border: none;\n}\n.gantt_task_line.pm-pro-gantt-task {\n    background: #2ECC71;\n    border: none;\n}\n.pm-pro-gantt-task .gantt_task_progress {\n    background: #1ABC9C;\n}\n.pmpro-gantt-add-new-btn {\n    color: #a7a7a6;\n    cursor: pointer;\n    padding: 8px;\n}\n.pmpro-gantt-add-new-btn:hover {\n    color: #454545;\n}\n.pm-pro-gantt-complete-task {\n    background: #6a67ce;\n    border: none;\n}\n.grunt-color-plate {\n    margin: 0px 5px 8px 0px;\n    float: right;\n    clear: both;\n    display: block;\n    overflow: hidden;\n    font-weight: 600;\n}\n.grunt-color-plate p {\n    display: inline-block;\n    margin: 0px;\n    padding: 0px;\n}\n.grunt-color-plate p:before {\n    content: \"\";\n    margin: 0px 5px;\n    width: 44px;\n    height: 18px;\n    display: inline-block;\n    vertical-align: middle;\n}\n.grunt-color-plate p.project-color:before {\n    background: #9B59B6;\n}\n.grunt-color-plate p.task-list-color:before {\n    background: #00AEFF;\n}\n.grunt-color-plate p.task-color:before{\n    background: #2ECC71;\n}\n\n", ""]);

// exports


/***/ }),
/* 14 */
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
/* 15 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_list_form_vue__ = __webpack_require__(5);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_208b7cef_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_list_form_vue__ = __webpack_require__(18);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(16)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_list_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_208b7cef_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_list_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/gantt/views/assets/src/components/list-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-208b7cef", Component.options)
  } else {
    hotAPI.reload("data-v-208b7cef", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(17);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(3)("9cad7894", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-208b7cef\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./list-form.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-208b7cef\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./list-form.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-new-list-modal-content .list-cancel {\n    display: none;\n}\n.pm-pro-new-list-modal-content .pm-new-todolist-form,\n.pm-pro-new-list-modal-content .pm-update-todolist-form {\n    border: none;\n    margin: 0;\n    padding: 0;\n}\n.pm-pro-new-list-modal-content .pm-new-todolist-form {\n    width: 100%;\n}\n.pm-modal-conetnt {\n    position: relative;\n}\n.pm-modal-conetnt .del-btn {\n    position: absolute;\n    bottom: 5px;\n    left: 15%;\n}\n", ""]);

// exports


/***/ }),
/* 18 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "modal-mask pm-task-modal modal-transition" },
    [
      _c("div", { staticClass: "modal-wrapper" }, [
        _c(
          "div",
          { staticClass: "modal-container", staticStyle: { width: "700px" } },
          [
            _c("div", { staticClass: "modal-header" }, [
              _c("span", { staticClass: "pm-right close-vue-modal" }, [
                _c(
                  "a",
                  {
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.listFormActivity(false)
                      }
                    }
                  },
                  [_c("span", { staticClass: "dashicons dashicons-no" })]
                )
              ]),
              _vm._v(" "),
              !_vm.list.id
                ? _c("h3", [_vm._v(_vm._s(_vm.__("New Task List", "pm-pro")))])
                : _c("h3", [_vm._v(_vm._s(_vm.__("Edit Task List", "pm-pro")))])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "modal-body pm-todolist" }, [
              _c("div", { staticClass: "pm-col-12 pm-todo" }, [
                _c(
                  "div",
                  {
                    staticClass:
                      "pm-modal-conetnt pm-pro-new-list-modal-content"
                  },
                  [
                    _c("pm-new-list-form", { attrs: { list: _vm.list } }),
                    _vm._v(" "),
                    _vm.hasList(_vm.list)
                      ? _c(
                          "a",
                          {
                            staticClass: "button button-secondary del-btn",
                            attrs: { href: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.deleteList(_vm.list)
                              }
                            }
                          },
                          [_vm._v(_vm._s(_vm.__("Delete", "pm-pro")))]
                        )
                      : _vm._e()
                  ],
                  1
                ),
                _vm._v(" "),
                _c("div", { staticClass: "clearfix" })
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "clearfix" })
          ]
        )
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
    require("vue-hot-reload-api")      .rerender("data-v-208b7cef", esExports)
  }
}

/***/ }),
/* 19 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_form_vue__ = __webpack_require__(6);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7efce830_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_form_vue__ = __webpack_require__(22);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_task_form_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7efce830_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_task_form_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/gantt/views/assets/src/components/task-form.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7efce830", Component.options)
  } else {
    hotAPI.reload("data-v-7efce830", Component.options)
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
var update = __webpack_require__(3)("20babeae", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7efce830\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-form.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7efce830\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./task-form.vue");
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

exports = module.exports = __webpack_require__(2)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-new-task-modal-content .list-cancel {\n    display: none;\n}\n.pm-pro-new-task-modal-content .todo-cancel {\n    display: none;\n}\n.pm-pro-new-task-modal-content .pm-task-due-field {\n    /*display: none;*/\n}\n.pm-pro-new-task-modal-content .task-title {\n    margin-bottom: 10px;\n}\n.pm-pro-new-task-modal-content .task-title input,\n.pm-pro-new-task-modal-content .content textarea {\n    width: 100%;\n    padding: 10px;\n}\n.pm-pro-new-task-modal-content .multiselect .multiselect__tags {\n    border-radius: 0px !important;\n}\n.pm-pro-new-task-modal-content .multiselect {\n    width: 100%;\n}\n.pm-pro-new-task-modal-content .content textarea {\n    height: 90px;\n}\n.pm-pro-new-task-modal-content .content {\n    margin-bottom: 5px;\n}\n.pm-pro-new-task-modal-content .submit {\n    padding: 9px 0 0 0;\n}\n.pm-pro-new-list-modal-content .pm-new-todolist-form {\n    width: 100%;\n}\n.pm-modal-conetnt {\n    position: relative;\n}\n.pm-modal-conetnt .del-btn {\n    position: absolute;\n    bottom: 38px;\n    left: 16%;\n}\n.pm-pro-new-task-modal-content .pm-datepickter-to.hasDatepicker ,\n.pm-pro-new-task-modal-content .pm-datepickter-from.hasDatepicker {\n    width: 100%;\n    margin-bottom: 10px;\n    padding: 10px !important;\n}\n.pm-pro-new-task-modal-content .pm-make-privacy{\n    margin: 10px 5px;\n}\n", ""]);

// exports


/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "modal-mask pm-task-modal modal-transition" },
    [
      _c("div", { staticClass: "modal-wrapper" }, [
        _c(
          "div",
          { staticClass: "modal-container", staticStyle: { width: "700px" } },
          [
            _c("div", { staticClass: "modal-header" }, [
              _c("span", { staticClass: "pm-right close-vue-modal" }, [
                _c(
                  "a",
                  {
                    on: {
                      click: function($event) {
                        $event.preventDefault()
                        return _vm.taskFormActivity(false)
                      }
                    }
                  },
                  [_c("span", { staticClass: "dashicons dashicons-no" })]
                )
              ]),
              _vm._v(" "),
              _vm.isNewTask
                ? _c("h3", [_vm._v(_vm._s(_vm.__("New Task", "pm-pro")))])
                : _c("h3", [_vm._v(_vm._s(_vm.__("Edit Task", "pm-pro")))])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "modal-body pm-todolist" }, [
              _c("div", { staticClass: "pm-col-12 pm-todo" }, [
                _c(
                  "div",
                  {
                    staticClass:
                      "pm-modal-conetnt pm-pro-new-task-modal-content"
                  },
                  [
                    _vm.isNewTask
                      ? _c("pm-new-task-form", { attrs: { list: _vm.list } })
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.isNewTask
                      ? _c("pm-new-task-form", {
                          attrs: { list: _vm.list, task: _vm.updateTask }
                        })
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.isNewTask
                      ? _c(
                          "a",
                          {
                            staticClass: "button button-secondary del-btn",
                            attrs: { href: "#" },
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.deleteTask(_vm.updateTask)
                              }
                            }
                          },
                          [_vm._v(_vm._s(_vm.__("Delete", "pm-pro")))]
                        )
                      : _vm._e()
                  ],
                  1
                ),
                _vm._v(" "),
                _c("div", { staticClass: "clearfix" })
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "clearfix" })
          ]
        )
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
    require("vue-hot-reload-api")      .rerender("data-v-7efce830", esExports)
  }
}

/***/ }),
/* 23 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-wrap pm pm-front-end" },
    [
      _c("pm-header"),
      _vm._v(" "),
      _c("pm-heder-menu"),
      _vm._v(" "),
      _vm.loading
        ? _c("div", { staticClass: "pm-data-load-before" }, [_vm._m(0)])
        : _vm._e(),
      _vm._v(" "),
      _vm.project
        ? _c(
            "div",
            { staticClass: "pm-gantt-wrap" },
            [
              _c("div", { staticClass: "grunt-color-plate" }, [
                _c("p", { staticClass: "project-color" }, [
                  _vm._v(_vm._s(_vm.__("Project", "pm-pro")))
                ]),
                _vm._v(" "),
                _c("p", { staticClass: "task-list-color" }, [
                  _vm._v(_vm._s(_vm.__("Task List", "pm-pro")))
                ]),
                _vm._v(" "),
                _c("p", { staticClass: "task-color" }, [
                  _vm._v(_vm._s(_vm.__("Task", "pm-pro")))
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "clear" }),
              _vm._v(" "),
              !_vm.loading
                ? _c("div", { staticClass: "pm-gantt-containter" }, [
                    _c("div", {
                      directives: [{ name: "pm-gantt", rawName: "v-pm-gantt" }],
                      attrs: { id: "pm-gantt" }
                    })
                  ])
                : _vm._e(),
              _vm._v(" "),
              _c(
                "transition",
                { attrs: { name: "modal" } },
                [
                  _vm.isActiveListForm
                    ? _c("list-form", { attrs: { list: _vm.updatedList } })
                    : _vm._e()
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "transition",
                { attrs: { name: "modal" } },
                [_vm.isActiveTaskForm ? _c("task-form") : _vm._e()],
                1
              )
            ],
            1
          )
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
    require("vue-hot-reload-api")      .rerender("data-v-071dfe30", esExports)
  }
}

/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var showingTaskForm = false;

var pmProgantt = {
    showingTaskForm: false,

    init: function init(el, binding, vnode) {
        var context = vnode.context;
        jQuery('.pm-gantt-containter').on('click', '.pmpro-gantt-new-project', {
            vmEl: el,
            vmBinging: binding,
            vm: vnode
        }, this.newProject);

        jQuery('.pm-gantt-containter').on('click', '.pmpro-gantt-new-task', {
            vmEl: el,
            vmBinging: binding,
            vm: vnode
        }, this.newTask);

        var demo_tasks = {
            "data": vnode.context.tasks,
            "links": vnode.context.links
        };

        gantt.config.columns = [{ name: "text", label: context.__('Title', 'pm-pro'), tree: true, width: 120 }, { name: "start_date", label: context.__('Start time', 'pm-pro'), align: "center", resize: true }, { name: "group", label: context.__('Group', 'pm-pro'), align: "center" }, { name: "action", label: context.__('Action', 'pm-pro'), align: "center",
            template: function template(obj) {

                if (obj.type == 'project' && context.user_can('create_list')) {
                    return '<i class="fa  pmpro-gantt-add-new-btn pmpro-gantt-new-project fa-plus" aria-hidden="true"></i>';
                } else if (obj.type == 'list' && context.user_can('create_task')) {
                    return '<i data-list_id="' + obj.list_id + '" class="fa pmpro-gantt-add-new-btn pmpro-gantt-new-task fa-plus" aria-hidden="true"></i>';
                } else {
                    return '';
                }
            }
        }];

        gantt.templates.task_class = function (start, end, task) {

            switch (task.type) {
                case "project":
                    return "pm-pro-gantt-project";
                    break;
                case "list":
                    return "pm-pro-gantt-list";
                    break;
                case "complete_task":
                    return "pm-pro-gantt-complete-task";
                    break;
                default:
                    return "pm-pro-gantt-task";
                    break;
            }
        };

        var hasUnderscore = PM_Vars.locale.indexOf('_');
        var locale = '';

        if (hasUnderscore != -1) {
            locale = PM_Vars.locale.substr(0, PM_Vars.locale.indexOf('_'));
        } else {
            locale = PM_Vars.locale;
        }

        if (!locale) {
            locale = 'en';
        }

        gantt.config.work_time = true; // removes non-working time from calculations
        gantt.config.skip_off_time = true;
        gantt.config.drag_progress = false;
        gantt.config.scale_offset_minimal = false;
        gantt.config.scale_unit = "month";
        gantt.config.date_scale = "%F, %Y";
        gantt.i18n.setLocale(locale);

        gantt.config.subscales = [{ unit: "day", step: 1, date: "%j, %D" }];

        gantt.init(el);

        gantt.clearAll();

        gantt.parse(demo_tasks);

        gantt.attachEvent("onBeforeLinkAdd", function (id, link) {
            var isValid = pmProgantt.linkValidation(link, vnode);

            if (isValid) {
                vnode.context.saveLink(link);
            }

            return isValid;
        });

        gantt.attachEvent("onBeforeTaskChanged", function (id, mode, task) {
            var boolean = [];

            task.$source.forEach(function (link_id) {
                var link = gantt.$data.linksStore.pull[link_id];
                var valid = pmProgantt.linkValidation(link, vnode);

                boolean.push(valid);
            });

            task.$target.forEach(function (link_id) {
                var link = gantt.$data.linksStore.pull[link_id];
                var valid = pmProgantt.linkValidation(link, vnode);

                boolean.push(valid);
            });

            var isValid = boolean.indexOf(false);

            if (isValid == -1) {
                return true;
            }

            return false;
        });

        gantt.attachEvent("onAfterTaskUpdate", function (id, item) {
            if (vnode.context.taskFormSubmit) {
                return;
            }
            vnode.context.updateTask(item);
        });

        gantt.attachEvent("onAfterLinkDelete", function (id, item) {
            vnode.context.removeLink(id);
        });

        gantt.attachEvent("onBeforeLightbox", function (id) {
            var task = gantt.$data.tasksStore.pull[id];
            if (task.type == 'list') {
                if (!showingTaskForm) {
                    vnode.context.updateList(task);
                } else {
                    showingTaskForm = false;
                }
            }

            if (task.type == 'task') {
                vnode.context.updateGanttTask(task);
            }
            return false;
        });
    },
    newProject: function newProject(evt) {
        var vm = evt.data.vm.context;
        vm.createList(function (list) {});
    },
    newTask: function newTask(evt) {
        showingTaskForm = true;

        var vm = evt.data.vm.context;
        var list_id = jQuery(evt.target).data('list_id');
        vm.createTask(list_id);
    },
    linkValidation: function linkValidation(link, vnode) {
        if (link.type == 1) {
            return pmProgantt.linkValidationOne(link, vnode);
        }

        if (link.type == 3) {
            return pmProgantt.linkValidationThree(link, vnode);
        }

        if (link.type == 0) {
            return pmProgantt.linkValidationZero(link, vnode);
        }

        if (link.type == 2) {
            return pmProgantt.linkValidationTwo(link, vnode);
        }
    },
    linkValidationZero: function linkValidationZero(link, vnode) {
        var source_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.source, 'id');
        var target_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.target, 'id');
        var context = vnode.context;
        if (!pmProgantt.checkTypeValidation(source_index, target_index)) {
            return false;
        }

        if (target_index === false) {
            return true;
        }

        var source_end = gantt.$data.tasksStore.pull[source_index].end_date;
        source_end = pm.Moment(source_end).subtract(1, "days");
        var target_start = gantt.$data.tasksStore.pull[target_index].start_date;

        var isSame = pm.Moment(source_end).isSameOrBefore(target_start);

        if (isSame) {
            return true;
        }

        var source_text = gantt.$data.tasksStore.pull[source_index].text;
        var target_text = gantt.$data.tasksStore.pull[target_index].text;

        pm.Toastr.error(target_text + ' ' + context.__('will start after completing ', 'pm-pro') + ' ' + source_text);

        return false;
    },
    linkValidationThree: function linkValidationThree(link, vnode) {
        var source_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.source, 'id');
        var target_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.target, 'id');
        var context = vnode.context;
        if (!pmProgantt.checkTypeValidation(source_index, target_index)) {
            return false;
        }

        var source_start = gantt.$data.tasksStore.pull[source_index].start_date;
        var target_end = gantt.$data.tasksStore.pull[target_index].end_date;
        target_end = pm.Moment(target_end).subtract(1, "days");

        var isSame = pm.Moment(source_start).isSameOrBefore(target_end);

        if (isSame) {
            return true;
        }

        var source_text = gantt.$data.tasksStore.pull[source_index].text;
        var target_text = gantt.$data.tasksStore.pull[target_index].text;

        pm.Toastr.error(target_text + ' ' + context.__('will complete after start', 'pm-pro') + ' ' + source_text);

        return false;
    },
    linkValidationTwo: function linkValidationTwo(link, vnode) {
        var source_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.source, 'id');
        var target_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.target, 'id');
        var context = vnode.context;
        if (!pmProgantt.checkTypeValidation(source_index, target_index)) {
            return false;
        }

        var source_end = gantt.$data.tasksStore.pull[source_index].end_date;
        source_end = pm.Moment(source_end).subtract(1, "days");
        var target_end = gantt.$data.tasksStore.pull[target_index].end_date;
        target_end = pm.Moment(target_end).subtract(1, "days");

        var isSame = pm.Moment(source_end).isSameOrBefore(target_end);

        if (isSame) {
            return true;
        }

        var source_text = gantt.$data.tasksStore.pull[source_index].text;
        var target_text = gantt.$data.tasksStore.pull[target_index].text;

        pm.Toastr.error(target_text + ' ' + context.__('will complete after completing the', 'pm-pro') + ' ' + source_text);

        return false;
    },
    linkValidationOne: function linkValidationOne(link, vnode) {
        var source_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.source, 'id');
        var target_index = vnode.context.getIndex(gantt.$data.tasksStore.pull, link.target, 'id');
        var context = vnode.context;
        if (!pmProgantt.checkTypeValidation(source_index, target_index)) {
            return false;
        }

        var source_start = gantt.$data.tasksStore.pull[source_index].start_date;
        var target_start = gantt.$data.tasksStore.pull[target_index].start_date;

        var isSame = pm.Moment(source_start).isSameOrBefore(target_start);

        if (isSame) {
            return true;
        }

        var source_text = gantt.$data.tasksStore.pull[source_index].text;
        var target_text = gantt.$data.tasksStore.pull[target_index].text;

        pm.Toastr.error(context.sprintf(context.__('%1$s will start with the %2$s or after completing the %3$s', 'pm-pro'), target_text, source_text, source_text));

        return false;
    },
    checkTypeValidation: function checkTypeValidation(source_index, target_index) {
        var source = gantt.$data.tasksStore.pull[source_index];
        var target = gantt.$data.tasksStore.pull[target_index];

        var ignoreType = ['list', 'project'];
        if (typeof source != "undefined" || typeof target != "undefined") {
            if (ignoreType.indexOf(source.type) != -1 || ignoreType.indexOf(target.type) != -1) {
                return false;
            }
        }

        return true;
    }
};

pm.Vue.directive('pm-gantt', {
    inserted: function inserted(el, binding, vnode) {
        pmProgantt.init(el, binding, vnode);
    }
});

/***/ }),
/* 25 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_tab_menu_vue__ = __webpack_require__(7);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_f50e302a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_tab_menu_vue__ = __webpack_require__(26);
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_tab_menu_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_f50e302a_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_tab_menu_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/gantt/views/assets/src/components/tab-menu.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-f50e302a", Component.options)
  } else {
    hotAPI.reload("data-v-f50e302a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 26 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "menu-item" },
    [
      _c(
        "router-link",
        {
          class: _vm.setActiveMenu("gantt"),
          attrs: {
            to: {
              name: "gantt",
              param: {
                project_id: _vm.project_id
              }
            }
          }
        },
        [
          _c("span", { staticClass: "logo icon-pm-gantchart" }),
          _vm._v(" "),
          _c("span", [_vm._v(" " + _vm._s(_vm.__("Gantt Chart", "pm-pro")))])
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
    require("vue-hot-reload-api")      .rerender("data-v-f50e302a", esExports)
  }
}

/***/ })
/******/ ]);