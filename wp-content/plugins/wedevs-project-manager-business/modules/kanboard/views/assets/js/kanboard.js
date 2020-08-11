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
/******/ 	return __webpack_require__(__webpack_require__.s = 9);
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

var listToStyles = __webpack_require__(15)

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

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = {
    data: function data() {
        return {
            abort: false,
            formSubmit: false,
            sortableRequestStatus: true,
            sortableData: ''
        };
    },


    methods: {
        removeTaskFromBoard: function removeTaskFromBoard(board_id, task_id) {
            if (!confirm(__("Are you sure?", 'pm-pro'))) {
                return;
            }

            var self = this;
            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/kanboard/' + board_id + '/tasks/' + task_id + '/delete',
                type: 'POST',
                data: {},
                success: function success(res) {
                    self.$store.commit('kanboard/deleteTask', {
                        task_id: task_id,
                        board_id: board_id
                    });
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
        selfCancelTaskForm: function selfCancelTaskForm(board) {
            board.task_title = '';
            this.newList = '';
            this.taskAssigns = '';
            this.endDate = '';

            this.showHideTaskForm(board);
        },
        loadMore: function loadMore(board_id) {
            var self = this;
            var boards = this.$store.state.kanboard.boards;
            var index = this.getIndex(boards, board_id, 'id');
            var page = boards[index].task.meta.pagination.current_page;

            var args = {
                conditions: {
                    page: parseInt(page) + 1
                },
                project_id: this.project_id,
                board_id: board_id,

                callback: function callback(res) {
                    if (res.data.length) {
                        self.$store.commit('kanboard/setLoadMoreData', {
                            board_id: board_id,
                            res: res
                        });
                    }
                }
            };

            this.getBoard(args);
        },
        updateTaskOrder: function updateTaskOrder(args, callback) {
            var self = this;

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/kanboard/task-order',
                type: 'POST',
                data: args,
                success: function success(res) {

                    if (args.is_move == 'yes') {
                        self.$store.commit('kanboard/updateCount', {
                            board_id: args.sender_section_id,
                            number: 1,
                            status: 'addition'
                        });

                        self.$store.commit('kanboard/updateTask', {
                            dropSectionId: args.section_id,
                            senderSectionId: args.sender_section_id,
                            task: res.data.drag_task.data,
                            dragabel_task_id: args.dragabel_task_id,
                            receive_task_index: args.receive_task_index
                        });
                    }

                    if (typeof callback != 'undefined') {
                        callback(res);
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
        updateBoardOrder: function updateBoardOrder(args, callback) {

            var self = this;
            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/kanboard/board-order',
                type: 'POST',
                data: {
                    board_orders: args
                },
                success: function success(res) {

                    self.$store.commit('kanboard/updateBoard', args);

                    if (typeof callback == 'function') {
                        callback(true, res);
                    }
                },
                error: function error(res) {
                    //Showing error
                    res.responseJSON.message.forEach(function (value, index) {
                        pm.Toastr.error(value);
                    });
                    if (typeof callback == 'function') {
                        callback(false, res);
                    }
                }
            };

            self.httpRequest(request_data);
        },
        addSearchableTask: function addSearchableTask(args) {
            var self = this;

            // self.$store.commit('kanboard/setSearchableTask',
            //     {
            //         task: args.task,
            //         board: args.board
            //     }
            // );

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + args.data.project_id + '/kanboard/' + args.data.board_id + '/task/' + args.data.task_id,
                type: 'POST',
                data: args.data,
                success: function success(res) {
                    if (res.success) {
                        self.showHideTaskSearch(args.board);

                        self.$store.commit('kanboard/setSearchableTask', {
                            task: args.task,
                            board: args.board
                        });

                        // Display a success toast, with a title
                        //pm.Toastr.success(res.message);

                        if (typeof args.callback === 'function') {
                            args.callback(res);
                        }
                    } else {
                        pm.Toastr.error(__('This task already included in others column.', 'pm-pro'));
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
        newTask: function newTask(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            if (self.formSubmit) {
                return;
            };
            self.formSubmit = true;

            var request_data = {
                url: self.base_url + '/pm/v2/projects/' + self.project_id + '/tasks',
                type: 'POST',
                data: args.data,
                success: function success(res) {
                    self.$store.commit('kanboard/setNewTask', {
                        board_id: args.data.kan_board_id,
                        task: res.data
                    });

                    self.selfCancelTaskForm(args.board);
                    // Display a success toast, with a title
                    //pm.Toastr.success(res.message);
                    self.formSubmit = false;
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
        showHideAction: function showHideAction(board, status) {
            status = status || 'toggle';
            if (!this.is_manager()) {
                return;
            }
            if (status === 'toggle') {
                status = board.action ? false : true;
            }

            if (status) {
                this.$store.commit('kanboard/showHideTaskSearch', {
                    board_id: board.id,
                    status: false
                });
            }

            this.$store.commit('kanboard/showHideAction', {
                board_id: board.id,
                status: status
            });
        },
        showHideTaskSearch: function showHideTaskSearch(board, status) {
            var self = this;
            status = status || 'toggle';
            if (status === 'toggle') {
                status = board.searchTask ? false : true;
            }

            if (status === true) {
                this.$store.commit('kanboard/showHideAction', {
                    board_id: board.id,
                    status: false
                });
            }

            this.$store.commit('kanboard/showHideTaskSearch', {
                board_id: board.id,
                status: status
            });
        },
        showHideBoardTitleUpdate: function showHideBoardTitleUpdate(board, status) {
            status = status || 'toggle';
            if (!this.is_manager()) {
                return;
            }

            if (status === 'toggle') {
                status = board.isUpdateMode ? false : true;
            }
            this.$store.commit('kanboard/showHideBoardTitleUpdate', {
                board_id: board.id,
                status: status
            });
        },
        showHideTaskForm: function showHideTaskForm(board, status) {
            status = status || 'toggle';
            if (status === 'toggle') {
                status = board.newTaskForm ? false : true;
            }

            this.$store.commit('kanboard/showHideTaskForm', {
                board_id: board.id,
                status: status
            });
        },
        showListsDropDown: function showListsDropDown(board, status) {
            status = status || 'toggle';
            if (status === 'toggle') {
                status = board.is_active_lists_drop_dwon ? false : true;
            }

            if (status === true) {
                this.$store.commit('kanboard/showUsersDropDown', {
                    board_id: board.id,
                    status: false
                });
                this.$store.commit('kanboard/showTaskEndField', {
                    board_id: board.id,
                    status: false
                });
            }

            this.$store.commit('kanboard/showListsDropDown', {
                board_id: board.id,
                status: status
            });

            pm.Vue.nextTick(function () {
                jQuery('.cpm-todo-lists-drop-down-wrap').find('.multiselect__input').focus();
            });
        },
        showUserDropDown: function showUserDropDown(board, status) {
            status = status || 'toggle';
            if (status === 'toggle') {
                status = board.is_active_assignUser_dropDown ? false : true;
            }

            if (status === true) {
                this.$store.commit('kanboard/showTaskEndField', {
                    board_id: board.id,
                    status: false
                });

                this.$store.commit('kanboard/showListsDropDown', {
                    board_id: board.id,
                    status: false
                });
            }

            this.$store.commit('kanboard/showUsersDropDown', {
                board_id: board.id,
                status: status
            });
        },
        showTaskEndField: function showTaskEndField(board, status) {
            status = status || 'toggle';
            if (status === 'toggle') {
                status = board.is_enable_due_date ? false : true;
            }

            if (status === true) {
                this.$store.commit('kanboard/showUsersDropDown', {
                    board_id: board.id,
                    status: false
                });
                this.$store.commit('kanboard/showListsDropDown', {
                    board_id: board.id,
                    status: false
                });
            }

            this.$store.commit('kanboard/showTaskEndField', {
                board_id: board.id,
                status: status
            });

            pm.Vue.nextTick(function () {
                jQuery('.cpm-inline-task-end-date-wrap').find('input[type="text"]').focus();
            });
        },
        getLists: function getLists() {
            var self = this;
            var request = {
                url: self.base_url + '/pm/v2/projects/' + self.project_id + '/task-lists/?with=incomplete_tasks&per_page=2000',
                success: function success(res) {
                    self.$store.commit('kanboard/setLists', res.data);
                }
            };

            self.httpRequest(request);
        },
        newBoard: function newBoard(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + args.data.project_id + '/kanboard/',
                type: 'POST',
                data: args.data,
                success: function success(res) {
                    self.addBoardMeta(res.data);
                    self.$store.commit('kanboard/afterCreateNewBoard', res.data);

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
        addBoardMeta: function addBoardMeta(board) {
            board.task = {
                data: []
            };
            board.newTaskForm = false;
            board.is_active_lists_drop_dwon = false;
            board.is_active_assignUser_dropDown = false;
            board.is_enable_due_date = false;
            board.users = [];
            board.endDate = '';
            board.list = {}, board.task_title = '';
            board.action = false;
            board.searchTask = false;
            board.isUpdateMode = false;
            board.taskLoading = false;
        },
        getKanboard: function getKanboard(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + args.data.project_id + '/kanboard',
                data: args.data,
                success: function success(res) {
                    res.data.forEach(function (board) {
                        self.addBoardMeta(board);
                    });

                    self.$store.commit('kanboard/setBoard', res.data);

                    if (args.data.search != 'active') {
                        res.data.forEach(function (board) {
                            var args = {
                                board_id: board.id,
                                project_id: board.project_id,
                                callback: function callback(res) {
                                    self.$store.commit('kanboard/setBoardable', {
                                        board_id: args.board_id,
                                        tasks: res
                                    });
                                }
                            };

                            self.getBoard(args);
                        });
                    }

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
        getBoard: function getBoard(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var conditions = self.generateConditions(args.conditions);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + args.project_id + '/kanboard/' + args.board_id + '/?' + conditions,
                data: args.data,
                success: function success(res) {

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
        deleteBoard: function deleteBoard(args) {
            if (!confirm(this.text.are_you_sure)) {
                return;
            }
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + args.data.project_id + '/kanboard/' + args.data.board_id + '/delete',
                data: args.data,
                type: 'POST',
                success: function success(res) {

                    self.$store.commit('kanboard/afterDeleteBoard', args.data.board_id);
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


        searchTask: function searchTask(args) {
            var self = this,
                timeout = 2000,
                timer;

            clearTimeout(timer);

            timer = setTimeout(function () {
                if (self.abort) {
                    self.abort.abort();
                }

                var conditions = self.generateConditions(args.conditions);

                var requestData = {
                    url: self.base_url + '/pm/v2/projects/' + args.project_id + '/tasks/?' + conditions,
                    data: {},

                    success: function success(res) {
                        self.$store.commit('kanboard/setSearchTasks', res.data);
                        if (typeof args.callback != 'undefined') {
                            args.callback(res);
                        }
                    }
                };

                self.abort = self.httpRequest(requestData);
            }, timeout);
        },

        updateBoard: function updateBoard(args) {
            var self = this,
                pre_define = {
                data: {},
                callback: false
            },
                args = jQuery.extend(true, pre_define, args);

            var requestData = {
                url: self.base_url + '/pm-pro/v2/projects/' + args.project_id + '/kanboard/' + args.board_id + '/update',
                data: args.data,
                type: 'POST',
                success: function success(res) {

                    self.$store.commit('kanboard/afterUpdateBoard', {
                        board_id: args.board_id,
                        res: res.data
                    });

                    self.showHideBoardTitleUpdate(args.board, false);

                    if (typeof callback != 'undefined') {
                        callback(res);
                    }
                }
            };

            self.httpRequest(requestData);
        }
    }
};

/***/ }),
/* 4 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__kanboard_vue__ = __webpack_require__(5);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__ = __webpack_require__(3);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__automation__ = __webpack_require__(19);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__import_task_modal__ = __webpack_require__(23);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    created: function created() {
        var self = this;
        //jQuery(window).on('click', this.hideTaskForm);
        this.getLists();

        pmBus.$on('pm_after_close_single_task_modal', this.afterCloseSingleTaskModal);
        pmBus.$on('pm_before_destroy_single_task', this.updateKanBoardSingleTask);
        pmBus.$on('pm_generate_task_url', this.generateTaskUrl);

        this.selfGetKanboard(function () {
            if (self.$route.query.filterTask == 'active') {
                self.filterRequent(function () {
                    self.$store.commit('kanboard/isFetchBoard', true);
                    self.searchForm.active = true;
                });
            }
        });
    },
    data: function data() {
        return {
            isActiveImportModal: false,
            searchForm: {
                active: false
            },
            background: {
                boardHeader: '#fbfcfd'
            },
            colorPicker: {
                canApply: true
            },
            showHideColoerPickerBtn: {
                status: true
            },
            automation: {
                isActiveAutomation: false,
                elements: []
            },
            board: {},
            fullscreen: false,
            search_task: __('Search Task', 'pm-pro'),
            add_task_text: __('Add Task', 'pm-pro'),
            remove_task_broad: __('Remove task from this board', 'pm-pro'),
            add_new_task_text: __('Add New Task', 'pm-pro'),
            select_task_list: __('Select Task List', 'pm-pro'),
            select_user_text: __('Select User', 'pm-pro'),
            add_text: __('Add', 'pm-pro'),
            add_new_section: __('Add new section', 'pm-pro'),
            boardTitle: '',
            list_id: null,
            taskAssigns: [],
            endDate: '',
            selectedTasks: [],
            taskId: false,
            projectId: false,
            boardId: false,
            newTaskRequest: true,
            value: '',
            options: ['Select option', 'options', 'selected', 'mulitple', 'label', 'searchable', 'clearOnSelect', 'hideSelected', 'maxHeight', 'allowEmpty', 'showLabels', 'onChange', 'touched'],
            filterQueryActive: false,
            importTask: {
                isActive: false
            },
            importOptions: {
                board: ''
            }

        };
    },


    mixins: [__WEBPACK_IMPORTED_MODULE_1__helpers_mixin_mixin___default.a],

    watch: {
        background: {
            handler: function handler(data) {
                this.boarddata.boardHeader;
            },


            deep: true
        }
    },

    components: {
        'multiselect': pm.Multiselect.Multiselect,
        'kanboard-menu': __WEBPACK_IMPORTED_MODULE_0__kanboard_vue__["default"],
        'single-task': pm.SingleTask,
        'automation': __WEBPACK_IMPORTED_MODULE_2__automation__["a" /* default */],
        'import-task-modal': __WEBPACK_IMPORTED_MODULE_3__import_task_modal__["a" /* default */]
    },
    computed: {
        optionTasks: function optionTasks() {
            return this.$store.state.kanboard.tasks;
        },
        projectUsers: function projectUsers() {
            return this.$store.state.project.assignees.data;
        },

        list: {
            get: function get() {
                if (!this.list_id && this.lists && this.lists.length) {
                    return this.lists[0];
                } else if (this.list_id) {
                    return _.find(this.lists, {
                        id: this.list_id
                    });
                }

                return null;
            },
            set: function set(list) {
                this.list_id = list.id;
            }
        },
        lists: function lists() {
            return this.$store.state.kanboard.lists;
        },
        boards: function boards() {
            return this.$store.state.kanboard.boards;
        },
        canCraeteTask: function canCraeteTask() {
            return this.user_can('create_task');
        },
        isFetchBoard: function isFetchBoard() {
            return this.$store.state.kanboard.isFetchBoard;
        },
        counts: function counts() {
            return this.$store.state.kanboard.count;
        }
    },

    methods: {
        activeImportTaskModal: function activeImportTaskModal(board) {
            this.isActiveImportModal = true;
            this.importOptions.board = board;
        },
        deactiveImportTaskModal: function deactiveImportTaskModal(board) {
            this.isActiveImportModal = false;
        },
        clearKanboardFilter: function clearKanboardFilter() {
            if (this.$route.query.filterTask != 'active') {
                return;
            }
            var self = this;
            this.$router.push({
                query: {}
            });
            this.$store.commit('kanboard/isFetchBoard', false);
            this.selfGetKanboard(function () {
                self.searchForm.active = false;
                self.$store.commit('kanboard/isFetchBoard', true);
            });
        },
        searchCancel: function searchCancel() {

            this.$store.commit('kanboard/afterSearchCancel');
            this.searchForm.active = false;
        },
        filterRequent: function filterRequent(callback) {
            var self = this;

            self.httpRequest({
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/kanboard/filter',
                type: 'POST',
                data: self.$route.query,
                success: function success(res) {
                    self.$store.commit('kanboard/setSearchContent', {
                        boards: res.data
                    });
                    self.filterQueryActive = true;

                    if (typeof callback !== 'undefined') {
                        callback(res);
                    }
                }
            });
        },
        listSearchAction: function listSearchAction(formField, callback) {
            var self = this;
            var query = {
                users: this.filterUsersId(formField.user),
                title: formField.title,
                lists: this.filterListsId(formField.list),
                dueDate: formField.dueDate.id,
                status: formField.status,
                filterTask: 'active'
            };

            this.$router.push({
                name: 'kanboard',
                params: {
                    project_id: this.project_id
                },
                query: query
            });

            this.filterRequent(function (res) {
                callback(res);
            });
        },
        filterUsersId: function filterUsersId(users) {
            var ids = [];

            if (users && typeof users != 'undefined') {
                ids.push(users.id);
            }

            return ids;
        },
        filterListsId: function filterListsId(lists) {
            var ids = [];

            ids.push(lists.id);

            return ids;
        },
        can_edit_task: function can_edit_task(task) {
            var user = PM_Vars.current_user;
            if (this.is_manager()) {
                return true;
            }

            if (typeof task.id === 'undefined' && this.canCraeteTask) {
                return true;
            }

            if (task.creator.data.id == user.ID) {
                return true;
            }

            return false;
        },
        boardTextColor: function boardTextColor(board) {

            if (typeof board.header_background == 'undefined') {
                return '#848484;';
            }

            if (board.header_background == '') {
                return '#848484;';
            }

            var textColor = this.getTextColor(board.header_background);

            if (textColor == '') {
                return '#848484;';
            }

            return textColor + ';';
        },
        boardBtnColor: function boardBtnColor(board) {

            if (typeof board.header_background == 'undefined') {
                return '#d6d6d6;';
            }

            if (board.header_background == '') {
                return '#d6d6d6;';
            }

            var textColor = this.getTextColor(board.header_background);

            if (textColor == '') {
                return '#d6d6d6;';
            }

            return textColor + ';';
        },
        getBackground: function getBackground(board) {
            if (typeof board.header_background == 'undefined') {
                return '#fbfcfd;';
            }

            return board.header_background + ';';
        },
        setColumnBackgroundColor: function setColumnBackgroundColor(board) {

            if (!this.colorPicker.canApply) {
                return;
            }

            if (!board.header_background) {
                board.header_background = '#fbfcfd';
            }

            var self = this;
            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/kanboard/' + board.id + '/header_background',
                type: 'POST',
                data: {
                    header_background: board.header_background
                },
                success: function success(res) {

                    pm.Toastr.success(__('Set color successfully!', 'pm-pro'));
                    self.colorPicker.canApply = true;
                    self.showColorPicker(board);
                },
                error: function error(res) {
                    //Showing error

                    pm.Toastr.error(res.data.error);
                }
            };

            this.colorPicker.canApply = false;

            self.httpRequest(request_data);
        },
        backColorPicker: function backColorPicker(board) {
            board.colorPicker = false;
        },
        showColorPicker: function showColorPicker(board) {
            if (typeof board.colorPicker == 'undefined') {
                pm.Vue.set(board, 'colorPicker', true);
            } else {
                board.colorPicker = board.colorPicker ? false : true;
            }
        },
        automationPopUp: function automationPopUp(board) {
            this.board = board;
            this.automation.isActiveAutomation = true;
            this.automation.elements = board.automation;
            this.CloseBoardAction();
        },
        toggleFullscreen: function toggleFullscreen() {

            this.$fullscreen.toggle(this.$el.querySelector('.pm-kanboard-fullscreen'), {
                wrap: false,
                callback: this.fullscreenChange
            });
        },
        fullscreenChange: function fullscreenChange(fullscreen) {
            this.fullscreen = fullscreen;
        },
        generateTaskUrl: function generateTaskUrl(task) {
            var url = this.$router.resolve({
                name: 'kanboard_single_task',
                params: {
                    task_id: task.id,
                    project_id: task.project_id
                }
            }).href;
            var url = PM_Vars.project_page + url;

            this.copy(url);
        },
        updateKanBoardSingleTask: function updateKanBoardSingleTask(task) {
            var board_index = this.getIndex(this.boards, this.boardId, 'id');
            if (board_index !== false) {
                var task_index = this.getIndex(this.boards[board_index].task.data, task.id, 'id');

                if (task_index !== false) {
                    this.boards[board_index].task.data.splice(task_index, 1, task);
                }
            }
        },
        afterCloseSingleTaskModal: function afterCloseSingleTaskModal() {
            if (this.$route.name == 'kanboard_single_task') {
                this.$router.push({
                    name: 'kanboard'
                });
            } else {
                this.taskId = false;
                this.projectId = false;
            }
        },
        getSingleTask: function getSingleTask(task, board) {
            this.boardId = board.id;
            this.taskId = task.id;
            this.projectId = task.project_id;
        },
        closeBroadTitleInput: function closeBroadTitleInput() {

            this.boards.map(function (broad) {
                if (broad.isUpdateMode) {
                    broad.isUpdateMode = false;
                }
            });
        },
        CloseTaskSearch: function CloseTaskSearch() {
            this.boards.map(function (broad) {
                if (broad.searchTask) {
                    broad.searchTask = false;
                }
            });
        },
        CloseBoardAction: function CloseBoardAction() {
            this.boards.map(function (broad) {
                if (broad.action) {
                    broad.action = false;
                }
            });
        },
        selfDeleteBoard: function selfDeleteBoard(board) {
            var args = {
                data: {
                    board_id: board.id,
                    project_id: this.project_id
                },

                callback: function callback() {}
            };

            this.deleteBoard(args);
        },
        selfNewTask: function selfNewTask(board) {
            var self = this;

            if (!self.newTaskRequest) {
                return;
            }

            if (board.task_title == '') {
                return;
            }
            // if(this.list) {
            //     let lists = this.$store.state.kanboard.lists;
            //     this.list_id = lists ? lists[0].id : '';
            // }

            var args = {
                data: {
                    assignees: this.taskAssigns.map(function (user) {
                        return user.id;
                    }),
                    title: board.task_title,
                    due_date: this.endDate,
                    board_id: this.list.id,
                    kan_board_id: board.id
                },
                board: board,
                callback: function callback(res) {
                    self.newTaskRequest = true;
                    board.task_title = '';
                    self.taskAssigns = [];
                    self.endDate = '';
                    self.list_id = '';
                }
            };
            self.newTaskRequest = false;
            this.newTask(args);
        },
        selfShowHideTaskForm: function selfShowHideTaskForm(board) {
            if (board.newTaskForm) {
                return true;
            }

            this.$store.commit('kanboard/hideAllTaskForm');

            this.showHideTaskForm(board);
        },
        hideTaskForm: function hideTaskForm() {},
        taskCount: function taskCount(counts, board_id) {
            if (typeof counts[board_id] == 'undefined') {
                return '00';
            }

            return counts[board_id].totalTask;

            // let data = task.data || false;

            // return data ? task.data.length : '00';
        },
        selfNewBoard: function selfNewBoard() {
            var self = this;
            if (this.boardTitle == '') {
                return;
            }
            var args = {
                data: {
                    project_id: this.project_id,
                    board_title: this.boardTitle,
                    order: 0
                },

                callback: function callback(res) {
                    self.boardTitle = '';
                }
            };

            this.newBoard(args);
        },
        selfGetKanboard: function selfGetKanboard(_callback) {
            var args = {
                data: {
                    project_id: this.project_id,
                    search: this.$route.query.filterTask
                },
                callback: function callback() {
                    pm.NProgress.done();

                    if (typeof _callback != 'undefined') {
                        _callback();
                    }
                }
            };

            this.getKanboard(args);
        },
        asyncFind: function asyncFind(board, val) {
            if (val.length < 3) {
                board.taskLoading = false;
                return;
            }
            board.taskLoading = true;
            var args = {
                conditions: {
                    s: val
                },
                project_id: this.project_id,
                callback: function callback() {
                    board.taskLoading = false;
                }
            };

            this.searchTask(args);
        },
        saveSingleTask: function saveSingleTask(board, task) {
            if (!task) {
                return;
            }
            var args = {
                data: {
                    project_id: this.project_id,
                    task_id: task.id,
                    board_id: board.id
                },
                task: task,
                board: board,
                callback: function callback() {}
            };

            this.addSearchableTask(args);
        },
        boardNameUpdate: function boardNameUpdate(board, event) {
            var title = jQuery(event.target).val().trim();
            if (board.title == title || board.title == '') {
                return;
            }

            var args = {
                data: {
                    title: title

                },
                board_id: board.id,
                project_id: this.project_id,
                board: board,
                callback: function callback(res) {}
            };

            this.updateBoard(args);
        },


        /**
         * CSS class for task date
         *
         * @param  string start_date
         * @param  string due_date
         *
         * @return string
         */
        taskDateWrap: function taskDateWrap(start_date, due_date) {

            if (start_date == null && due_date == null) {
                return false;
            }

            if (start_date == '' && due_date == '') {
                return false;
            }

            start_date = new Date(start_date);
            start_date = pm.Moment(start_date).format('YYYY-MM-DD');

            due_date = new Date(due_date);
            due_date = pm.Moment(due_date).format('YYYY-MM-DD');

            var today = pm.Moment().format('YYYY-MM-DD'),
                due_day = pm.Moment(due_date).format('YYYY-MM-DD');

            if (!pm.Moment(String(due_day), 'YYYY-MM-DD').isValid() && !pm.Moment(start_date, 'YYYY-MM-DD').isValid()) {
                return false;
            }

            if (pm.Moment(String(due_day), 'YYYY-MM-DD').isValid()) {
                return pm.Moment(String(today), 'YYYY-MM-DD').isSameOrBefore(String(due_day)) ? 'cpm-current-date' : 'cpm-due-date';
            }

            return 'cpm-current-date';
        },

        hasDueDate: function hasDueDate(task) {

            if (task.due_date.date == null) {

                return false;
            }

            return true;
        },
        afterImport: function afterImport(height) {
            var self = this;
            height = height || 0;
            this.$off('afterImport', this.afterImport);

            var board = jQuery('div.kbc-sortable-section[data-section_id=' + this.importOptions.board.id + ']'),
                content = board.find('.kbc-kanboard-sortable-wrap');

            setTimeout(function () {
                content.animate({
                    scrollTop: board.find('.kbc-kanboard-sortable-wrap .kbc-td-contents').last().offset().top - content.offset().top - 50
                });
            }, 500);
        }
    },
    destroyed: function destroyed() {
        this.$store.commit('kanboard/setDefaultStore');
    }
});

/***/ }),
/* 5 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_kanboard_vue__ = __webpack_require__(6);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_58952aac_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_kanboard_vue__ = __webpack_require__(18);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(16)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_kanboard_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_58952aac_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_kanboard_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/kanboard/views/assets/src/components/kanboard.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-58952aac", Component.options)
  } else {
    hotAPI.reload("data-v-58952aac", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 6 */
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



/* harmony default export */ __webpack_exports__["a"] = ({

    props: {
        actionData: {
            type: Object,
            default: function _default() {
                return {};
            }
        },

        searchForm: {
            type: Object,
            default: function _default() {
                active: false;
            }
        }
    },
    data: function data() {
        return {
            list_view: __('List views', 'pm-pro'),
            kanboard_text: __('Kanboard', 'pm-pro'),
            isFetchProject: false
        };
    },

    mixins: [__WEBPACK_IMPORTED_MODULE_0__helpers_mixin_mixin___default.a],

    computed: {},

    created: function created() {},


    methods: {
        showHideSearchForm: function showHideSearchForm() {
            this.searchForm.active = this.searchForm.active ? false : true;
        },
        isKanboardFilterActive: function isKanboardFilterActive() {
            if (this.$route.name == 'kanboard') {
                return true;
            };

            return false;
        },
        isActiveBtn: function isActiveBtn(name) {
            var routeName = this.$route.name;

            if (routeName == name) {
                return 'active-list-header-btn';
            }
        },
        setKanboardHoverClass: function setKanboardHoverClass() {
            if (this.$route.name == 'kanboard') {
                return 'cpmkanboard-hover';
            }

            return '';
        },
        setListHoverClass: function setListHoverClass() {
            if (this.$route.name == 'task_lists') {
                return 'to-do-list-hover';
            }

            return '';
        },
        listsUrl: function listsUrl() {
            return '#/projects/' + this.project_id + '/task-lists';
        },
        kanboardUrl: function kanboardUrl() {
            return '#/projects/' + this.project_id + '/kanboard';
        },
        viewLists: function viewLists() {

            if (this.$route.name == 'task_lists') {
                return;
            }

            this.$store.commit('updateListViewType', 'task_lists');
            this.viewChange('list');
        },
        viewKanboard: function viewKanboard() {

            if (this.$route.name == 'kanboard') {
                return;
            }

            this.$store.commit('updateListViewType', 'kanboard');
            this.viewChange('kanboard');
        },
        viewChange: function viewChange(viewType) {

            var self = this;
            // var view = '';

            // if(
            // 	self.$store.state.projectMeta.hasOwnProperty('list_view_type')
            // 		&&
            // 	self.$store.state.projectMeta.list_view_type
            // ) {
            // 	view = self.$store.state.projectMeta.list_view_type.meta_value;
            // }

            // if(view == viewType) {
            // 	return;
            // }

            var request_data = {
                url: self.base_url + '/pm-pro/v2/projects/' + self.project_id + '/list-view-type',
                type: 'POST',
                data: {
                    view_type: viewType
                },
                success: function success(res) {
                    //self.$store.commit( 'updateListViewType', viewType );
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
        automation: {
            type: [Object],
            default: function _default() {
                return {};
            }
        },

        board: {
            type: [Object],
            default: function _default() {
                return {};
            }
        }
    },

    data: function data() {
        return {
            submitProcessing: false,
            actions: {
                type: 0,
                todo: {
                    section: '',
                    lists: []
                },
                inProgress: {
                    reOpened: 'yes'
                },
                done: {
                    completed: 'yes'
                },
                users: [],
                taskStatus: ''

            },
            loadingListSearch: false,
            listAbort: '',
            timeout: ''
        };
    },


    computed: {
        projectUsers: function projectUsers() {
            if (typeof this.$store.state.project.assignees === 'undefined') {
                return [];
            }
            var users = [];
            this.$store.state.project.assignees.data.forEach(function (user) {
                users.push({
                    id: user.id,
                    display_name: user.display_name,
                    avatar_url: user.avatar_url
                });
            });

            return users;
        },
        projectLists: function projectLists() {
            var lists = [];
            this.$store.state.kanboard.lists.forEach(function (list) {
                lists.push({
                    id: parseInt(list.id),
                    title: list.title
                });
            });

            return lists;
        }
    },

    components: {
        'multiselect': pm.Multiselect.Multiselect
    },

    created: function created() {
        if (!jQuery.isEmptyObject(this.automation.elements)) {
            this.actions = jQuery.extend(true, this.actions, this.automation.elements);

            this.actions.todo.lists.forEach(function (list) {
                list.id = parseInt(list.id);
            });
        }
    },


    methods: {
        submitBtnClass: function submitBtnClass() {
            return this.submitProcessing ? 'submit-btn-text update pm-button pm-primary' : 'update pm-button pm-primary';
        },
        close: function close() {
            this.automation.isActiveAutomation = false;
        },
        asyncListsFind: function asyncListsFind(title) {
            if (title == '') return;
            var self = this;
            clearTimeout(this.timeout);

            // Make a new timeout set to go off in 800ms
            this.timeout = setTimeout(function () {
                self.findLists(title);
            }, 500);
        },
        findLists: function findLists(title) {
            var self = this;

            if (self.listAbort) {
                self.listAbort.abort();
            }

            var request = {
                url: self.base_url + '/pm/v2/projects/' + this.project_id + '/lists/search?title=' + title,
                success: function success(res) {
                    self.loadingListSearch = false;
                    self.$store.commit('kanboard/setSearchLists', res.data);
                }
            };
            self.loadingListSearch = true;
            self.listAbort = self.httpRequest(request);
        },
        saveAutomation: function saveAutomation() {
            var self = this;
            if (self.submitProcessing) {
                return;
            }
            var request = {
                data: {
                    board_id: self.board.id,
                    data: self.actions
                },
                type: 'POST',
                url: self.base_url + '/pm-pro/v2/projects/' + this.project_id + '/boards/' + self.board.id + '/automation',
                success: function success(res) {
                    self.submitProcessing = false;
                    pm.Vue.set(self.board, 'automation', self.actions);
                    self.close();
                    self.reloadBoard(self.board.id);
                    pm.Toastr.success(__('Activated automation for this column', 'pm-pro'));
                },
                error: function error(res) {
                    self.submitProcessing = false;
                    //Showing error
                    // res.responseJSON.data.error.forEach( function( value, index ) {
                    pm.Toastr.error(res.responseJSON.data.error);
                    // });
                }
            };
            self.submitProcessing = true;
            self.httpRequest(request);
        },
        reloadBoard: function reloadBoard(boardId) {
            var self = this;
            var args = {
                project_id: this.project_id,
                board_id: boardId,
                callback: function callback(res) {
                    self.$store.commit('kanboard/updateBoardTaks', {
                        tasks: res.data,
                        board_id: boardId
                    });
                }
            };

            this.getBoard(args);
        }
    }
});

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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
        options: {
            type: [Object],
            default: function _default() {
                return {
                    board: {}
                };
            }
        }
    },
    data: function data() {
        return {
            isListLoaded: false,
            isTaskLoading: false,
            submitProcessing: false,
            allSelected: false,
            selected: [],
            content: [],
            tasks: [],
            completeTasks: [],
            incompleteTasks: [],
            tab: {
                all: true,
                complete: false,
                incomplete: false
            },
            listDropDownOptions: {
                placeholder: __('Select Task List', 'pm-pro')
            }
        };
    },

    methods: {
        afterFetchList: function afterFetchList() {
            this.isListLoaded = true;
        },
        getClass: function getClass(status) {
            if (status == 'all' && this.tab.all) {
                return 'pm-button pm-primary';
            }

            if (status == 'complete' && this.tab.complete) {
                return 'pm-button pm-primary';
            }

            if (status == 'incomplete' && this.tab.incomplete) {
                return 'pm-button pm-primary';
            }

            return 'pm-button pm-secondary';
        },
        fetchTasks: function fetchTasks(val) {
            var self = this;

            var request = {
                data: {

                    with: 'assignees, total_comments',
                    lists: [val.id],
                    project_id: this.project_id
                },
                url: self.base_url + '/pm-pro/v2/projects/' + this.project_id + '/kanboard/tasks',
                success: function success(res) {

                    self.removeBoardTasks(res.data);

                    self.setAllTasks(res.data);
                    self.setCompletedTasks(res.data);
                    self.setIncompletedTasks(res.data);

                    self.getContent();
                    self.isTaskLoading = false;
                }
            };
            self.isTaskLoading = true;
            self.httpRequest(request);
        },
        removeBoardTasks: function removeBoardTasks(dbTasks) {

            for (var i = dbTasks.length - 1; i >= 0; i--) {
                for (var j = 0; j < this.options.board.task.data.length; j++) {
                    if (dbTasks[i] && dbTasks[i].id === this.options.board.task.data[j].id) {
                        dbTasks.splice(i, 1);
                    }
                }
            }
        },
        getContent: function getContent() {
            if (this.tab.all) {
                this.content = this.tasks;
            }

            if (this.tab.complete) {
                this.content = this.completeTasks;
            }

            if (this.tab.incomplete) {
                this.content = this.incompleteTasks;
            }
        },
        ativeTab: function ativeTab(tab) {
            var tab = tab || 'all';

            if (tab == 'all') {
                this.tab.all = true;
                this.tab.complete = false;
                this.tab.incomplete = false;
            }

            if (tab == 'complete') {
                this.tab.all = false;
                this.tab.complete = true;
                this.tab.incomplete = false;
            }

            if (tab == 'incomplete') {
                this.tab.all = false;
                this.tab.complete = false;
                this.tab.incomplete = true;
            }
        },
        setCompletedTasks: function setCompletedTasks(tasks) {
            var setTasks = [];

            tasks.forEach(function (task) {
                if (task.status == 'complete') {
                    setTasks.push(task);
                }
            });

            this.completeTasks = setTasks;
        },
        setIncompletedTasks: function setIncompletedTasks(tasks) {
            var setTasks = [];

            tasks.forEach(function (task) {
                if (task.status == 'incomplete') {
                    setTasks.push(task);
                }
            });

            this.incompleteTasks = setTasks;
        },
        setAllTasks: function setAllTasks(tasks) {
            this.tasks = tasks;
        },
        getTasks: function getTasks(status) {
            var status = status || 'all';
            this.content = [];
            this.selected = [];
            this.allSelected = false;
            this.ativeTab(status);
            this.getContent();
        },
        setSelected: function setSelected(task, ele) {
            if (ele.target.checked) {
                var index = this.selected.indexOf(task.id);

                if (index == -1) {
                    task.isSelected = true;
                    this.selected.push(task.id);
                }
            } else {
                var _index = this.selected.indexOf(task.id);

                if (_index != -1) {
                    task.isSelected = false;
                    this.selected.splice(_index, 1);
                }
            }

            if (this.selected.length == this.content.length) {
                this.allSelected = true;
            } else {
                this.allSelected = false;
            }
        },
        isSelected: function isSelected(task) {
            return task.isSelected ? 'selected' : '';
        },
        selectAll: function selectAll(ele) {
            var self = this;
            // var content = this.content;

            if (ele.target.checked) {
                self.selected = [];
                self.content.forEach(function (task) {
                    self.selected.push(task.id);
                    task.isSelected = true;
                });
            } else {
                self.selected = [];
                self.content.forEach(function (task) {
                    task.isSelected = false;
                });
            }
            //this.content = [];

            //this.content = content;

            //console.log(self.selected);
        },
        submitBtnClass: function submitBtnClass() {
            return this.submitProcessing ? 'submit-btn-text update pm-button pm-primary' : 'update pm-button pm-primary';
        },
        close: function close() {
            this.$emit('closeImportTaskModal');
        },
        importTasks: function importTasks() {
            var self = this;

            if (self.submitProcessing) {
                return;
            }

            if (!this.selected.length) {
                pm.Toastr.warning(__('You do not select any task for importing', 'pm-pro'));
                return;
            }

            var request = {
                type: 'POST',
                data: {

                    board_id: this.options.board.id,
                    items: this.selected
                },
                url: self.base_url + '/pm-pro/v2/projects/' + this.project_id + '/kanboard/import-tasks',

                success: function success(res) {
                    var tasks = [];
                    self.selected.forEach(function (id) {
                        var index = self.getIndex(self.content, parseInt(id), 'id');

                        self.$store.commit('kanboard/setSearchableTask', {
                            task: self.content[index],
                            board: self.options.board
                        });
                    });

                    self.submitProcessing = false;
                    self.close();
                    self.$emit('afterImport');
                }
            };
            self.submitProcessing = true;
            self.httpRequest(request);
        }
    }
});

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__.p = PM_Pro_Vars.dir_url + 'modules/kanboard/views/assets/js/';

__webpack_require__(10);

/***/ }),
/* 10 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _router = __webpack_require__(11);

var _router2 = _interopRequireDefault(_router);

var _directive = __webpack_require__(28);

var _directive2 = _interopRequireDefault(_directive);

var _kanboard = __webpack_require__(5);

var _kanboard2 = _interopRequireDefault(_kanboard);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevs_PM_Components.push({
	hook: 'pm-inline-list-button',
	component: 'pm-inline-list',
	property: _kanboard2.default
});

// const kanboard = resolve => {
//     require.ensure(['./components/kanboard.vue'], () => {
//         resolve(require('./components/kanboard.vue'));
//     });
// }

/***/ }),
/* 11 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _kanboardContent = __webpack_require__(12);

var _kanboardContent2 = _interopRequireDefault(_kanboardContent);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

weDevsPmProAddonRegisterModule('kanboard', 'kanboard');

// const kanboarPage = resolve => {
//     require.ensure(['./../components/kanboard-content.vue'], () => {
//         resolve(require('./../components/kanboard-content.vue'));
//     });
// }

weDevsPMRegisterChildrenRoute('project_root', [{
    path: 'projects/:project_id/kanboard/',
    component: _kanboardContent2.default,
    name: 'kanboard',
    children: [{
        path: 'tasks/:task_id',
        components: {
            'kanboard-single-task': pm.SingleTask
        },
        name: 'kanboard_single_task'
    }]
}]);

/***/ }),
/* 12 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_kanboard_content_vue__ = __webpack_require__(4);
/* empty harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_32538f50_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_kanboard_content_vue__ = __webpack_require__(27);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(13)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_kanboard_content_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_32538f50_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_kanboard_content_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/kanboard/views/assets/src/components/kanboard-content.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-32538f50", Component.options)
  } else {
    hotAPI.reload("data-v-32538f50", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["default"] = (Component.exports);


/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(14);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("7d94f46f", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-32538f50\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./kanboard-content.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-32538f50\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./kanboard-content.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.kbc-action-wrap .pm-dorpdown-menu-ul .pm-dorpdown-menu-li-content .icon-pm-task-list:before {\n  color: #19bc9b;\n}\n.kbc-action-wrap .pm-dorpdown-menu-ul .pm-dorpdown-menu-li-content .flaticon-color-picker:before {\n  color: #3b80f4;\n}\n.kbc-action-wrap .pm-dorpdown-menu-ul .pm-dorpdown-menu-li-content .fa-trash:before {\n  color: #ca4a1f;\n}\n.kbc-action-wrap .content-action-wrap .button-group .apply {\n  position: relative;\n  margin: 8px;\n}\n.kbc-action-wrap .content-action-wrap .button-group .apply .apply-text-color {\n  color: #1A9ED4 !important;\n}\n.kbc-action-wrap .content-action-wrap .button-group .apply .pm-button {\n  font-size: 13px;\n}\n.pm-kanboard .kbc-kanboard {\n  display: flex;\n}\n.pm-kanboard .kbc-kanboard .kbc-section-action {\n  width: 50px;\n}\n.pm-kanboard .kbc-kanboard .kbc-section-action .kbc-action-icon-wrap {\n  display: flex;\n  justify-content: space-between;\n}\n.pm-kanboard .kbc-kanboard .kbc-table-wrap {\n  flex: 5;\n}\n.pm-kanboard .kbc-kanboard .list-search-menu {\n  -webkit-box-shadow: -4px 1px 20px -10px rgba(0, 0, 0, 0.75);\n  -moz-box-shadow: -4px 1px 20px -10px rgba(0, 0, 0, 0.75);\n  box-shadow: -4px 1px 20px -10px rgba(0, 0, 0, 0.75);\n  border-left: 1px solid #E5E4E4;\n  border-top: 1px solid #E5E4E4;\n  box-shadow: -5px 0px 20px 0px rgba(234, 234, 234, 0.75);\n  z-index: 999;\n}\n.pm-kanboard .pm-kanboard-fullscreen {\n  position: relative;\n}\n.pm-kanboard .pm-kanboard-fullscreen .loadmoreanimation {\n  display: block !important;\n  position: absolute;\n  top: 43%;\n  left: 36%;\n  z-index: 99;\n}\n.pm-kanboard .pm-kanboard-fullscreen .kbc-table-opacity {\n  background: #fff;\n  opacity: 0.1;\n}\n.pm-kanboard .pm-kanboard-fullscreen .cursor-none * {\n  cursor: none;\n}\n.pm-kanboard .pm-kanboard-fullscreen-active {\n  background: #f1f1f1;\n  padding: 22px 15px 15px 22px;\n}\n.pm-kanboard .pm-kanboard-fullscreen-active .fullscreen-view-btn {\n  position: absolute;\n  bottom: 15px;\n  z-index: 99;\n  right: 15px;\n  border-radius: 100%;\n  height: 50px;\n  width: 50px;\n  display: flex;\n  align-items: center;\n  padding: 0;\n  justify-content: center;\n  border-color: #1a9ed4;\n  box-shadow: 0px 0px 7px 0px #1a9ed4;\n}\n.pm-kanboard .pm-kanboard-fullscreen-active .kbc-tasks-wrap {\n  height: 70vh !important;\n}\n.pm-kanboard .pm-kanboard-fullscreen-active .kbc-sortable-section {\n  width: 300px !important;\n}\n.pm-kanboard .fullscreen-view-btn {\n  display: inline-flex;\n  height: 30px;\n  font-size: 12px;\n  padding: 0 13px;\n  align-items: center;\n  background: #fff;\n  border: 1px solid #E2E2E2;\n  border-radius: 3px;\n  border-top-right-radius: 0;\n  border-bottom-right-radius: 0;\n  color: #788383;\n  border-right-color: #fff;\n  white-space: nowrap;\n  padding: 0 12px;\n  margin-right: 10px;\n  border-right: 1px solid #e2e2e2;\n}\n.pm-kanboard .kbc-action-wrap {\n  position: relative;\n  top: -23px;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap {\n  position: absolute;\n  z-index: 9999;\n  background-color: #ffffff;\n  display: block;\n  box-shadow: 0px 6px 40px 0px rgba(46, 78, 90, 0.6);\n  text-align: left;\n  margin: 0;\n  border-radius: 3px;\n  top: 34px;\n  left: -61px;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .button-group .apply {\n  position: relative;\n  margin: 8px;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .button-group .apply .apply-text-color {\n  color: #1A9ED4 !important;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .button-group .apply .pm-button {\n  font-size: 13px;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .header {\n  padding: 8px 10px;\n  border-bottom: 1px solid #e1e1e1;\n  color: #555;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .header span {\n  margin-right: 10px;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .header .flaticon-pm-left-arrow {\n  vertical-align: middle;\n}\n.pm-kanboard .kbc-action-wrap .content-action-wrap .content {\n  padding: 12px 10px 10px 10px;\n}\n.pm-kanboard .kbc-action-wrap .color-picker-wrap {\n  padding: 5px;\n}\n.pm-kanboard .kbc-action-wrap .kbc-triangle-up {\n  position: absolute;\n  z-index: 99999;\n  border-width: 8px;\n  border-style: solid;\n  border-color: transparent transparent #fff transparent;\n  top: 19px;\n  left: 18px;\n  border-radius: 3px;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul {\n  position: absolute;\n  z-index: 9999;\n  background-color: #ffffff;\n  display: block;\n  box-shadow: 0px 6px 40px 0px rgba(46, 78, 90, 0.6);\n  text-align: left;\n  margin: 0;\n  border-radius: 3px;\n  padding: 8px 0 0 0;\n  top: 34px;\n  left: -118px;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul .kbc-action-li {\n  margin: 0 !important;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul .kbc-action-li .wpup-action-link {\n  display: block;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul .kbc-action-li .flaticon-pm-automation {\n  color: #2d96dd;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul .kbc-action-li .flaticon-pm-settings {\n  color: #5b9dd9;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul .kbc-action-li .flaticon-edit-tools {\n  color: #f39c12;\n}\n.pm-kanboard .kbc-action-wrap .pm-dorpdown-menu-ul .kbc-action-li .flaticon-color-picker:before {\n  color: #19bc9b;\n  font-weight: 900;\n  font-size: 0.9rem !important;\n}\n.pm-kanboard .kanboard-menu-wrap {\n  margin: 10px 0px;\n  display: block;\n  overflow: hidden;\n}\n.pm-kanboard .kanboard-menu-wrap .fullscreen-view-btn .icon-pm-fullscreen:before {\n  vertical-align: middle;\n}\n.pm-kanboard .kanboard-menu-wrap .fullscreen-view-btn .icon-pm-fullscreen-text {\n  margin-left: 8px;\n  font-size: 12px;\n  font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif !important;\n  font-weight: 600;\n}\n.pm-kanboard .kanboard-menu-wrap .fullscreen-view-btn:hover {\n  border-color: #1A9ED4;\n}\n.pm-kanboard .kanboard-menu-wrap .fullscreen-view-btn:hover .icon-pm-fullscreen,\n.pm-kanboard .kanboard-menu-wrap .fullscreen-view-btn:hover .icon-pm-fullscreen-text {\n  color: #1A9ED4 !important;\n}\n.pm-kanboard .cpm-single-task-field-multiselect-wrap .cpm-multiselect-cross,\n.pm-kanboard .cpm-todo-lists-drop-down-wrap .cpm-multiselect-cross {\n  display: none;\n}\n.pm-kanboard .pm-pro-kanboard-action-hrf {\n  display: inline-block;\n}\n.pm-kanboard .pm-pro-kanboard-del-btn {\n  padding: 0 10px;\n}\n.pm-kanboard .pm-pro-multiselect-wrap {\n  position: absolute;\n  z-index: 1;\n  width: 232px;\n  left: -60px;\n  top: 24px;\n}\n.pm-kanboard .pm-pro-multiselect-wrap .multiselect__tag {\n  display: block !important;\n}\n.pm-kanboard .pm-pro-multiselect-wrap .multiselect__tags {\n  padding-bottom: 8px !important;\n}\n.pm-kanboard .pm-pro-kanboard-title-field {\n  width: 95%;\n}\n.pm-kanboard .kbc-new-task-popover .multiselect {\n  width: 200px;\n}\n.pm-kanboard .kbc-new-task-popover .multiselect__tag {\n  display: block;\n}\n.pm-kanboard .kbc-new-task-popover .cpm-multiselect {\n  position: relative;\n}\n.pm-kanboard .kbc-new-task-popover .cpm-multiselect-cross {\n  position: absolute;\n  z-index: 9999;\n  right: 7%;\n  top: 22px;\n  color: #5a5a5a52;\n}\n.pm-kanboard .cpm-inline-task-end-date-wrap {\n  position: relative;\n}\n.pm-kanboard .cpm-single-task-field-end-link {\n  position: absolute;\n  z-index: 9999;\n  right: 7.4%;\n  top: 4px;\n  color: #5a5a5a52;\n}\n.pm-kanboard .kbc-section-footer .button-group-wrap {\n  display: flex;\n  justify-content: flex-end;\n  margin-top: 20px;\n}\n.pm-kanboard .kbc-section-footer .button-group-wrap .pm-secondary {\n  margin-right: 6px !important;\n}\n.pm-kanboard .kbc-section-footer .button-group-wrap .new-task-btn-wrap {\n  position: relative;\n}\n.pm-kanboard .kbc-section-footer .button-group-wrap .new-task-btn-wrap .new-task-btn-color {\n  color: #1A9ED4 !important;\n}\n.pm-kanboard .kbc-section-footer .button-group-wrap .new-task-btn-wrap .pm-circle-spinner {\n  position: absolute;\n  top: 50%;\n  left: 50%;\n  margin-top: -12px;\n  margin-left: -8px;\n}\n.pm-kanboard .kbc-section-footer .button-group-wrap .new-task-btn-wrap .pm-circle-spinner:after {\n  content: \"\";\n  border: 2px solid #fff;\n  border-radius: 50%;\n  display: inline-block;\n  height: 10px;\n  width: 10px;\n  margin-left: 0 !important;\n  border-color: #fff #fff #fff transparent;\n  -webkit-animation: pm-circle-spinner 1s infinite;\n  animation: pm-circle-spinner 1s infinite;\n  vertical-align: middle;\n}\n", ""]);

// exports


/***/ }),
/* 15 */
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
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(17);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("8de4d03e", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-58952aac\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./kanboard.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-58952aac\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./kanboard.vue");
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

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.pm-list-header-menu {\n  display: flex;\n  align-items: center;\n}\n.pm-list-header-menu .task-filter {\n  display: flex;\n  align-items: center;\n  height: 30px;\n  color: #788383;\n  padding: 0 13px;\n  border-radius: 3px;\n  font-size: 12px;\n  border: 1px solid #e4e5e5;\n  background: #fff;\n  margin-left: 10px;\n}\n.pm-list-header-menu .task-filter:hover {\n  border-color: #1A9ED4 !important;\n  color: #1A9ED4;\n}\n.pm-list-header-menu .task-filter:hover .icon-pm-filter {\n  color: #1A9ED4;\n}\n.pm-list-header-menu .task-filter .icon-pm-filter {\n  margin-right: 5px;\n  color: #d4d6d6;\n}\n.pm-list-header-menu {\n  float: right;\n}\n.pm-list-header-menu .kanboard-btn {\n  display: flex;\n  align-items: center;\n  background: #fff;\n  border: 1px solid #E2E2E2;\n  border-radius: 3px;\n  border-top-left-radius: 0;\n  border-bottom-left-radius: 0;\n  color: #788383;\n  white-space: nowrap;\n}\n.pm-list-header-menu .active-list-header-btn {\n  border-color: #1A9ED4 !important;\n  border-right: 1px solid #1A9ED4 !important;\n  color: #1A9ED4;\n}\n.pm-list-header-menu .list-view-btn {\n  display: flex;\n  align-items: center;\n  background: #fff;\n  border: 1px solid #E2E2E2;\n  border-radius: 3px;\n  border-top-right-radius: 0;\n  border-bottom-right-radius: 0;\n  color: #788383;\n  border-right-color: #fff;\n  white-space: nowrap;\n}\n.pm-list-header-menu .list-action-group {\n  height: 30px;\n  font-size: 12px;\n  padding: 0 13px;\n  border-radius: 3px;\n  white-space: nowrap;\n}\n.icon-pm-list-view {\n  margin-right: 5px;\n}\n.icon-pm-kanboard-view {\n  margin-right: 5px;\n}\n.icon-pm-list-view:before {\n  content: \"\\E925\";\n  color: #d7dee2;\n}\n.active-list-header-btn .icon-pm-kanboard-view:before {\n  content: \"\\E924\";\n  color: #1a9ed4;\n}\n", ""]);

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
    { staticClass: "pm-list-header-menu" },
    [
      _c(
        "a",
        {
          class:
            _vm.isActiveBtn("task_lists") + " list-view-btn list-action-group",
          attrs: { title: _vm.list_view, href: _vm.listsUrl() },
          on: {
            click: function($event) {
              return _vm.viewLists("list")
            }
          }
        },
        [
          _c("span", { staticClass: "icon-pm-list-view" }),
          _vm._v(" "),
          _c("span", [_vm._v(_vm._s(_vm.__("List View", "pm-pro")))])
        ]
      ),
      _vm._v(" "),
      _c(
        "a",
        {
          class:
            _vm.isActiveBtn("kanboard") +
            " kanboard-btn list-action-group active-header-btn",
          attrs: { title: _vm.kanboard_text, href: _vm.kanboardUrl() },
          on: {
            click: function($event) {
              return _vm.viewKanboard("kanboard")
            }
          }
        },
        [
          _c("span", { staticClass: "icon-pm-kanboard-view" }),
          _vm._v(" "),
          _c("span", [_vm._v(_vm._s(_vm.__("Kanban", "pm-pro")))])
        ]
      ),
      _vm._v(" "),
      _vm.isKanboardFilterActive()
        ? _c(
            "a",
            {
              class:
                "active-task-filter task-filter list-action-group task-filter-btn",
              attrs: {
                title: _vm.__("Task Filter", "wedevs-project-manager"),
                href: "#"
              },
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.showHideSearchForm()
                }
              }
            },
            [
              _c("span", { staticClass: "icon-pm-filter" }),
              _vm._v(" "),
              _c("span", [
                _vm._v(_vm._s(_vm.__("Filter", "wedevs-project-manager")))
              ])
            ]
          )
        : _vm._e(),
      _vm._v(" "),
      _c("router-view", { attrs: { name: "kanboard-single-task" } })
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
    require("vue-hot-reload-api")      .rerender("data-v-58952aac", esExports)
  }
}

/***/ }),
/* 19 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_automation_vue__ = __webpack_require__(7);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7357b1b5_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_automation_vue__ = __webpack_require__(22);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(20)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_automation_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_7357b1b5_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_automation_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/kanboard/views/assets/src/components/automation.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7357b1b5", Component.options)
  } else {
    hotAPI.reload("data-v-7357b1b5", Component.options)
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
var update = __webpack_require__(1)("45a944c3", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7357b1b5\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./automation.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-7357b1b5\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./automation.vue");
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

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-automation .head {\n  width: 545px !important;\n  background-color: #f6f8fa;\n  border-bottom: 1px solid #eee;\n  padding: 16px;\n  font-size: 14px;\n  font-weight: 600;\n  color: #24292e;\n  position: fixed;\n  top: 45px;\n}\n.pm-pro-automation .automation-popup-body .content {\n  padding: 16px;\n  height: 100%;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset {\n  color: #24292e;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .select-type {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .select-type select {\n  min-width: 135px;\n  color: #24292e;\n  border-color: #ccc;\n  margin-left: 7px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .type-header {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap {\n  margin-top: 5px;\n  margin-bottom: 10px;\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__select {\n  display: none;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option .option-image-wrap .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap {\n  margin: 15px 0 8px 15px;\n  padding-left: 14px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .event-checkbox {\n  float: left;\n  margin: 5px 0 0 -22px;\n  vertical-align: middle;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .label-title {\n  color: #24292e;\n  font-weight: 600;\n  font-size: 14px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .note {\n  color: #586069;\n  display: block;\n  font-size: 12px;\n  font-weight: 400;\n  margin: 0;\n}\n.pm-pro-automation .automation-popup-body .content .first-event {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .none {\n  margin-top: 16px;\n  color: #24292e;\n  font-weight: 400;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .type-header {\n  font-weight: 600;\n  font-size: 14px;\n  padding-bottom: 4px;\n  border-bottom: 1px solid #e1e4e8;\n  color: #24292e;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap {\n  margin-top: 20px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-assign-note {\n  margin: 5px 0 0px 7px;\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap {\n  margin-left: 7px;\n  margin-top: 5px;\n  margin-bottom: 10px;\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__select {\n  display: none;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option .option-image-wrap .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-wrap .event-wrap .event-checkbox {\n  border-radius: 100%;\n}\n.pm-pro-automation .automation-popup-body .button-group {\n  position: fixed;\n  display: block;\n  background: #f6f8fa;\n  width: 545px !important;\n  border-top: 1px solid #eee;\n  padding: 12px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside {\n  display: flex;\n  float: right;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .cancel-btn-wrap {\n  margin-right: 10px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .submit-btn-text {\n  color: #199ed4 !important;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap {\n  position: relative;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap .pm-circle-spinner {\n  position: absolute;\n  left: 50%;\n  top: 50%;\n  margin-left: -16px;\n  margin-top: -11px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap .pm-circle-spinner:after {\n  height: 10px;\n  width: 10px;\n  border-color: #fff #fff #fff transparent;\n}\n.automation-popup-container {\n  width: 545px !important;\n  top: 99px !important;\n  height: 485px !important;\n  border-radius: 0 !important;\n}\n.automation-popup-container .automation-popup-body {\n  overflow: scroll;\n  overflow-x: hidden;\n  height: 100%;\n  width: auto;\n}\n", ""]);

// exports


/***/ }),
/* 22 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "popup-mask pm-pro-automation" }, [
    _c("div", { staticClass: "popup-container automation-popup-container" }, [
      _c("div", { staticClass: "automation-popup-body" }, [
        _c("div", { staticClass: "head" }, [
          _c("span", [_vm._v(_vm._s(_vm.__("Manage Automation", "pm-pro")))])
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "content" }, [
          _c("div", { staticClass: "preset-wrap" }, [
            _c("div", { staticClass: "preset" }, [
              _c("div", [
                _vm._v(
                  "\n                            " +
                    _vm._s(
                      _vm.__(
                        "Choose a preset to automate your kanbanboard and sync with Task Lists",
                        "pm-pro"
                      )
                    ) +
                    "\n                        "
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "type-header" }, [
                _vm._v(
                  "\n                            " +
                    _vm._s(_vm.__("Move Tasks", "pm-pro")) +
                    "\n                        "
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "select-type" }, [
                _c(
                  "select",
                  {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.actions.type,
                        expression: "actions.type"
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
                          _vm.actions,
                          "type",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      }
                    }
                  },
                  [
                    _c("option", { attrs: { value: "0" } }, [
                      _vm._v(_vm._s(_vm.__("Select Type", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "none" } }, [
                      _vm._v(_vm._s(_vm.__("None", "pm-pro")) + " ")
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "todo" } }, [
                      _vm._v(_vm._s(_vm.__("To Do", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "in_progress" } }, [
                      _vm._v(_vm._s(_vm.__("In Progress", "pm-pro")))
                    ]),
                    _vm._v(" "),
                    _c("option", { attrs: { value: "done" } }, [
                      _vm._v(_vm._s(_vm.__("Done", "pm-pro")))
                    ])
                  ]
                )
              ])
            ]),
            _vm._v(" "),
            _vm.actions.type === "none"
              ? _c("div", { staticClass: "none" }, [
                  _c("div", [
                    _vm._v(
                      _vm._s(
                        _vm.__("This column will not to be automated", "pm-pro")
                      )
                    )
                  ])
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.actions.type != "0" && _vm.actions.type != "none"
              ? _c("div", { staticClass: "preset-element" }, [
                  _vm.actions.type === "todo"
                    ? _c("div", { staticClass: "to-do" }, [
                        _c("div", { staticClass: "type-header" }, [
                          _vm._v(
                            _vm._s(_vm.__("Move task here when...", "pm-pro"))
                          )
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "event-wrap first-event" }, [
                          _c("label", [
                            _c("input", {
                              directives: [
                                {
                                  name: "model",
                                  rawName: "v-model",
                                  value: _vm.actions.todo.section,
                                  expression: "actions.todo.section"
                                }
                              ],
                              staticClass: "event-checkbox",
                              attrs: { value: "newlyadded", type: "radio" },
                              domProps: {
                                checked: _vm._q(
                                  _vm.actions.todo.section,
                                  "newlyadded"
                                )
                              },
                              on: {
                                change: function($event) {
                                  return _vm.$set(
                                    _vm.actions.todo,
                                    "section",
                                    "newlyadded"
                                  )
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("span", { staticClass: "label-title" }, [
                              _vm._v(_vm._s(_vm.__("Newly added", "pm-pro")))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("p", { staticClass: "note" }, [
                            _c("span", [
                              _vm._v(
                                _vm._s(
                                  _vm.__(
                                    "Tasks you have added recently on any task lists will be automatically moved here.",
                                    "pm-pro"
                                  )
                                )
                              )
                            ])
                          ])
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "event-wrap" }, [
                          _c("label", [
                            _c("input", {
                              directives: [
                                {
                                  name: "model",
                                  rawName: "v-model",
                                  value: _vm.actions.todo.section,
                                  expression: "actions.todo.section"
                                }
                              ],
                              staticClass: "event-checkbox",
                              attrs: { value: "lists", type: "radio" },
                              domProps: {
                                checked: _vm._q(
                                  _vm.actions.todo.section,
                                  "lists"
                                )
                              },
                              on: {
                                change: function($event) {
                                  return _vm.$set(
                                    _vm.actions.todo,
                                    "section",
                                    "lists"
                                  )
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("span", { staticClass: "label-title" }, [
                              _vm._v(_vm._s(_vm.__("Task lists", "pm-pro")))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("p", { staticClass: "note" }, [
                            _c("span", [
                              _vm._v(
                                _vm._s(
                                  _vm.__(
                                    "Tasks added only in the selected task lists will be automatically moved here.",
                                    "pm-pro"
                                  )
                                )
                              )
                            ])
                          ]),
                          _vm._v(" "),
                          _vm.actions.todo.section === "lists"
                            ? _c(
                                "div",
                                { staticClass: "lists-drop-down-wrap" },
                                [
                                  _c("multiselect", {
                                    staticClass: "drop-down",
                                    attrs: {
                                      options: _vm.projectLists,
                                      multiple: true,
                                      "close-on-select": false,
                                      "clear-on-select": true,
                                      "show-labels": true,
                                      searchable: true,
                                      placeholder: _vm.__(
                                        "Search Task Lists",
                                        "wedevs-project-manager"
                                      ),
                                      "select-label": "",
                                      "selected-label": "selected",
                                      "deselect-label": "",
                                      label: "title",
                                      "track-by": "id",
                                      loading: _vm.loadingListSearch,
                                      "allow-empty": true
                                    },
                                    on: { "search-change": _vm.asyncListsFind },
                                    scopedSlots: _vm._u(
                                      [
                                        {
                                          key: "option",
                                          fn: function(props) {
                                            return [
                                              _c(
                                                "div",
                                                { staticClass: "option__desc" },
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
                                                          props.option.title
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
                                      3145640762
                                    ),
                                    model: {
                                      value: _vm.actions.todo.lists,
                                      callback: function($$v) {
                                        _vm.$set(_vm.actions.todo, "lists", $$v)
                                      },
                                      expression: "actions.todo.lists"
                                    }
                                  })
                                ],
                                1
                              )
                            : _vm._e()
                        ])
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.actions.type === "in_progress"
                    ? _c("div", { staticClass: "in-progress" }, [
                        _c("div", { staticClass: "event-wrap" }, [
                          _c("label", [
                            _c("input", {
                              directives: [
                                {
                                  name: "model",
                                  rawName: "v-model",
                                  value: _vm.actions.inProgress.reOpened,
                                  expression: "actions.inProgress.reOpened"
                                }
                              ],
                              staticClass: "event-checkbox",
                              attrs: { "true-value": "yes", type: "checkbox" },
                              domProps: {
                                checked: Array.isArray(
                                  _vm.actions.inProgress.reOpened
                                )
                                  ? _vm._i(
                                      _vm.actions.inProgress.reOpened,
                                      null
                                    ) > -1
                                  : _vm._q(
                                      _vm.actions.inProgress.reOpened,
                                      "yes"
                                    )
                              },
                              on: {
                                change: function($event) {
                                  var $$a = _vm.actions.inProgress.reOpened,
                                    $$el = $event.target,
                                    $$c = $$el.checked ? "yes" : false
                                  if (Array.isArray($$a)) {
                                    var $$v = null,
                                      $$i = _vm._i($$a, $$v)
                                    if ($$el.checked) {
                                      $$i < 0 &&
                                        _vm.$set(
                                          _vm.actions.inProgress,
                                          "reOpened",
                                          $$a.concat([$$v])
                                        )
                                    } else {
                                      $$i > -1 &&
                                        _vm.$set(
                                          _vm.actions.inProgress,
                                          "reOpened",
                                          $$a
                                            .slice(0, $$i)
                                            .concat($$a.slice($$i + 1))
                                        )
                                    }
                                  } else {
                                    _vm.$set(
                                      _vm.actions.inProgress,
                                      "reOpened",
                                      $$c
                                    )
                                  }
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("span", { staticClass: "label-title" }, [
                              _vm._v(_vm._s(_vm.__("Reopened tasks", "pm-pro")))
                            ])
                          ]),
                          _vm._v(" "),
                          _c("p", { staticClass: "note" }, [
                            _c("span", [
                              _vm._v(
                                _vm._s(
                                  _vm.__(
                                    "If a closed task in this project reopens, it will automatically move here.",
                                    "pm-pro"
                                  )
                                )
                              )
                            ])
                          ])
                        ])
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.actions.type === "done"
                    ? _c("div", { staticClass: "done" }, [
                        _c("div", { staticClass: "type-header" }, [
                          _vm._v(
                            _vm._s(_vm.__("Move issue here when...", "pm-pro"))
                          )
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "event-wrap" }, [
                          _c("label", [
                            _c("input", {
                              directives: [
                                {
                                  name: "model",
                                  rawName: "v-model",
                                  value: _vm.actions.done.completed,
                                  expression: "actions.done.completed"
                                }
                              ],
                              staticClass: "event-checkbox",
                              attrs: { "true-value": "yes", type: "checkbox" },
                              domProps: {
                                checked: Array.isArray(
                                  _vm.actions.done.completed
                                )
                                  ? _vm._i(_vm.actions.done.completed, null) >
                                    -1
                                  : _vm._q(_vm.actions.done.completed, "yes")
                              },
                              on: {
                                change: function($event) {
                                  var $$a = _vm.actions.done.completed,
                                    $$el = $event.target,
                                    $$c = $$el.checked ? "yes" : false
                                  if (Array.isArray($$a)) {
                                    var $$v = null,
                                      $$i = _vm._i($$a, $$v)
                                    if ($$el.checked) {
                                      $$i < 0 &&
                                        _vm.$set(
                                          _vm.actions.done,
                                          "completed",
                                          $$a.concat([$$v])
                                        )
                                    } else {
                                      $$i > -1 &&
                                        _vm.$set(
                                          _vm.actions.done,
                                          "completed",
                                          $$a
                                            .slice(0, $$i)
                                            .concat($$a.slice($$i + 1))
                                        )
                                    }
                                  } else {
                                    _vm.$set(_vm.actions.done, "completed", $$c)
                                  }
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("span", { staticClass: "label-title" }, [
                              _vm._v(
                                _vm._s(_vm.__("Completed tasks", "pm-pro"))
                              )
                            ])
                          ]),
                          _vm._v(" "),
                          _c("p", { staticClass: "note" }, [
                            _c("span", [
                              _vm._v(
                                _vm._s(
                                  _vm.__(
                                    "Issues will automatically move here when added to this project.",
                                    "pm-pro"
                                  )
                                )
                              )
                            ])
                          ])
                        ])
                      ])
                    : _vm._e()
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "assing-user-wrap" }, [
            _c("div", { staticClass: "type-header" }, [
              _vm._v(
                "\n                        " +
                  _vm._s(_vm.__("Assign User", "pm-pro")) +
                  "\n                    "
              )
            ]),
            _vm._v(" "),
            _c("p", { staticClass: "note user-assign-note" }, [
              _c("span", [
                _vm._v(
                  _vm._s(
                    _vm.__(
                      "Select team members to be assigned automatically when a task droppped in this column.",
                      "pm-pro"
                    )
                  )
                )
              ])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "user-drop-down-wrap" },
              [
                _c("multiselect", {
                  staticClass: "drop-down",
                  attrs: {
                    options: _vm.projectUsers,
                    multiple: true,
                    "show-labels": false,
                    placeholder: _vm.__("Select users", "pm-pro"),
                    label: "display_name",
                    "track-by": "id"
                  },
                  scopedSlots: _vm._u([
                    {
                      key: "option",
                      fn: function(props) {
                        return [
                          _c("div", { staticClass: "option-image-wrap" }, [
                            _c("img", {
                              staticClass: "option__image",
                              attrs: { src: props.option.avatar_url }
                            })
                          ]),
                          _vm._v(" "),
                          _c("div", { staticClass: "option__desc" }, [
                            _c("span", { staticClass: "option__title" }, [
                              _vm._v(_vm._s(props.option.display_name))
                            ])
                          ])
                        ]
                      }
                    }
                  ]),
                  model: {
                    value: _vm.actions.users,
                    callback: function($$v) {
                      _vm.$set(_vm.actions, "users", $$v)
                    },
                    expression: "actions.users"
                  }
                })
              ],
              1
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "task-status-wrap" }, [
            _c("div", { staticClass: "type-header" }, [
              _vm._v(
                "\n                        " +
                  _vm._s(_vm.__("Change task status", "pm-pro")) +
                  "\n                    "
              )
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "event-wrap first-event" }, [
              _c("label", [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.actions.taskStatus,
                      expression: "actions.taskStatus"
                    }
                  ],
                  staticClass: "event-checkbox",
                  attrs: { value: "none", type: "radio" },
                  domProps: { checked: _vm._q(_vm.actions.taskStatus, "none") },
                  on: {
                    change: function($event) {
                      return _vm.$set(_vm.actions, "taskStatus", "none")
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "label-title" }, [
                  _vm._v(_vm._s(_vm.__("None", "pm-pro")))
                ])
              ]),
              _vm._v(" "),
              _c("p", { staticClass: "note" }, [
                _c("span", [
                  _vm._v(
                    _vm._s(
                      _vm.__(
                        "Dropping task here has no progress status",
                        "pm-pro"
                      )
                    )
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "event-wrap first-event" }, [
              _c("label", [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.actions.taskStatus,
                      expression: "actions.taskStatus"
                    }
                  ],
                  staticClass: "event-checkbox",
                  attrs: { value: "completed", type: "radio" },
                  domProps: {
                    checked: _vm._q(_vm.actions.taskStatus, "completed")
                  },
                  on: {
                    change: function($event) {
                      return _vm.$set(_vm.actions, "taskStatus", "completed")
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "label-title" }, [
                  _vm._v(_vm._s(_vm.__("Completed task", "pm-pro")))
                ])
              ]),
              _vm._v(" "),
              _c("p", { staticClass: "note" }, [
                _c("span", [
                  _vm._v(
                    _vm._s(
                      _vm.__(
                        "Dropping task here will automatically mark the task as complete.",
                        "pm-pro"
                      )
                    )
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "event-wrap" }, [
              _c("label", [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.actions.taskStatus,
                      expression: "actions.taskStatus"
                    }
                  ],
                  staticClass: "event-checkbox",
                  attrs: { value: "incomplete", type: "radio" },
                  domProps: {
                    checked: _vm._q(_vm.actions.taskStatus, "incomplete")
                  },
                  on: {
                    change: function($event) {
                      return _vm.$set(_vm.actions, "taskStatus", "incomplete")
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "label-title" }, [
                  _vm._v(_vm._s(_vm.__("Incompleted task", "pm-pro")))
                ])
              ]),
              _vm._v(" "),
              _c("p", { staticClass: "note" }, [
                _c("span", [
                  _vm._v(
                    _vm._s(
                      _vm.__(
                        "Dropping task here will automatically mark the task as incomplete.",
                        "pm-pro"
                      )
                    )
                  )
                ])
              ])
            ])
          ])
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
                      return _vm.saveAutomation()
                    }
                  }
                },
                [_vm._v(_vm._s(_vm.__("Update Automation", "pm-pro")))]
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
  ])
}
var staticRenderFns = []
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-7357b1b5", esExports)
  }
}

/***/ }),
/* 23 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_import_task_modal_vue__ = __webpack_require__(8);
/* unused harmony namespace reexport */
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_48e6af5f_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_import_task_modal_vue__ = __webpack_require__(26);
var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(24)
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
  __WEBPACK_IMPORTED_MODULE_0__babel_loader_node_modules_vue_loader_lib_selector_type_script_index_0_import_task_modal_vue__["a" /* default */],
  __WEBPACK_IMPORTED_MODULE_1__node_modules_vue_loader_lib_template_compiler_index_id_data_v_48e6af5f_hasScoped_false_buble_transforms_node_modules_vue_loader_lib_selector_type_template_index_0_import_task_modal_vue__["a" /* default */],
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "modules/kanboard/views/assets/src/components/import-task-modal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-48e6af5f", Component.options)
  } else {
    hotAPI.reload("data-v-48e6af5f", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

/* harmony default export */ __webpack_exports__["a"] = (Component.exports);


/***/ }),
/* 24 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(25);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(1)("e7fd60c8", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-48e6af5f\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./import-task-modal.vue", function() {
     var newContent = require("!!../../../../../../node_modules/css-loader/index.js!../../../../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-48e6af5f\",\"scoped\":false,\"hasInlineConfig\":false}!../../../../../../node_modules/less-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./import-task-modal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 25 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(0)(false);
// imports


// module
exports.push([module.i, "\n.pm-pro-import-task-modal .pm-pro-form-wrap {\n  background: #eee;\n  opacity: 0.3;\n  display: none;\n}\n.pm-pro-import-task-modal .loading-animation {\n  display: flex;\n  align-items: center;\n  margin-left: 33%;\n  color: #000;\n}\n.pm-pro-import-task-modal .loading-animation .load-spinner {\n  margin: 0;\n}\n.pm-pro-automation .head {\n  width: 500px !important;\n  background-color: #f6f8fa;\n  border-bottom: 1px solid #eee;\n  padding: 16px;\n  font-size: 14px;\n  font-weight: 600;\n  color: #24292e;\n  position: fixed;\n  top: 45px;\n}\n.pm-pro-automation .automation-popup-body .content {\n  padding: 16px;\n  height: 100%;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__single {\n  width: auto;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__spinner {\n  top: 16px !important;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__select {\n  display: block;\n}\n.pm-pro-automation .automation-popup-body .content .multiselect__select:before {\n  position: relative;\n  right: 0;\n  top: 17px;\n  color: #999;\n  margin-top: 4px;\n  border-style: solid;\n  border-width: 5px 5px 0;\n  border-color: #999 transparent transparent;\n  content: \"\";\n  z-index: 999;\n}\n.pm-pro-automation .automation-popup-body .content .tab-link {\n  border-radius: 3px 0px 0px 3px !important;\n}\n.pm-pro-automation .automation-popup-body .content .tasks-wrap {\n  border: 1px solid #f1f1f1;\n  height: 200px;\n  overflow: auto;\n  padding: 10px 10px;\n  color: #555;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .tasks-wrap .incomplete {\n  background: #f5734752;\n  padding: 1px 3px;\n  color: #444;\n  font-size: 11px;\n  margin-left: 10px;\n  line-height: 0;\n  height: 100%;\n  width: 100%;\n  border-radius: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .tasks-wrap .complete {\n  background: #81c9e678;\n  padding: 1px 3px;\n  color: #444;\n  font-size: 11px;\n  margin-left: 10px;\n  line-height: 0;\n  height: 100%;\n  width: 100%;\n  border-radius: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .all-select-wrap {\n  border: 1px solid #f1f1f1;\n  padding: 10px;\n  color: #555;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab {\n  display: flex;\n  justify-content: center;\n  margin: 15px 0 !important;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab .first {\n  border-radius: 3px 0px 0px 3px !important;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab .second {\n  border-radius: 0px !important;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-tab .third {\n  border-radius: 0px 3px 3px 0px !important;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset {\n  color: #24292e;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .select-type {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .select-type select {\n  min-width: 135px;\n  color: #24292e;\n  border-color: #ccc;\n  margin-left: 7px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset .type-header {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap {\n  margin-top: 5px;\n  margin-bottom: 10px;\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option .option-image-wrap .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-pro-automation .automation-popup-body .content .preset-wrap .preset-element .lists-drop-down-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap {\n  margin: 15px 0 8px 15px;\n  padding-left: 14px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .event-checkbox {\n  float: left;\n  margin: 5px 0 0 -22px;\n  vertical-align: middle;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .label-title {\n  color: #24292e;\n  font-weight: 600;\n  font-size: 14px;\n}\n.pm-pro-automation .automation-popup-body .content .event-wrap .note {\n  color: #586069;\n  display: block;\n  font-size: 12px;\n  font-weight: 400;\n  margin: 0;\n}\n.pm-pro-automation .automation-popup-body .content .first-event {\n  margin-top: 10px;\n}\n.pm-pro-automation .automation-popup-body .content .none {\n  margin-top: 16px;\n  color: #24292e;\n  font-weight: 400;\n  font-size: 13px;\n}\n.pm-pro-automation .automation-popup-body .content .type-header {\n  font-weight: 600;\n  font-size: 14px;\n  padding-bottom: 4px;\n  border-bottom: 1px solid #e1e4e8;\n  color: #24292e;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap {\n  margin-top: 20px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-assign-note {\n  margin: 5px 0 0px 7px;\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap {\n  margin-left: 7px;\n  margin-top: 5px;\n  margin-bottom: 10px;\n  min-height: auto;\n  margin-right: 8px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__single {\n  margin-bottom: 0;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__input {\n  border: none;\n  box-shadow: none;\n  margin: 0;\n  font-size: 14px;\n  vertical-align: baseline;\n  height: 0;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option {\n  font-weight: normal;\n  white-space: normal;\n  padding: 6px 12px;\n  line-height: 25px;\n  font-size: 14px;\n  display: flex;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option .option-image-wrap .option__image {\n  border-radius: 100%;\n  height: 16px;\n  width: 16px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__element .multiselect__option .option__desc {\n  line-height: 20px;\n  font-size: 13px;\n  margin-left: 5px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags {\n  min-height: auto;\n  padding: 4px;\n  border-color: #ddd;\n  border-radius: 3px;\n  white-space: normal;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__single {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__tags-wrap {\n  font-size: 12px;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__spinner {\n  position: absolute;\n  right: 24px;\n  top: 14px;\n  width: auto;\n  height: auto;\n  z-index: 99;\n}\n.pm-pro-automation .automation-popup-body .content .assing-user-wrap .user-drop-down-wrap .multiselect__tags .multiselect__tag {\n  margin-bottom: 0;\n  overflow: visible;\n  border-radius: 3px;\n  margin-top: 2px;\n}\n.pm-pro-automation .automation-popup-body .content .task-status-wrap .event-wrap .event-checkbox {\n  border-radius: 100%;\n}\n.pm-pro-automation .automation-popup-body .button-group {\n  position: fixed;\n  display: block;\n  background: #f6f8fa;\n  width: 500px !important;\n  border-top: 1px solid #eee;\n  padding: 12px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside {\n  display: flex;\n  float: right;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .cancel-btn-wrap {\n  margin-right: 10px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .submit-btn-text {\n  color: #199ed4 !important;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap {\n  position: relative;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap .pm-circle-spinner {\n  position: absolute;\n  left: 50%;\n  top: 50%;\n  margin-left: -16px;\n  margin-top: -11px;\n}\n.pm-pro-automation .automation-popup-body .button-group .button-group-inside .update-btn-wrap .pm-circle-spinner:after {\n  height: 10px;\n  width: 10px;\n  border-color: #fff #fff #fff transparent;\n}\n.automation-popup-container {\n  width: 500px !important;\n  top: 99px !important;\n  height: 396px !important;\n  border-radius: 0 !important;\n}\n.automation-popup-container .automation-popup-body {\n  overflow: scroll;\n  overflow-x: hidden;\n  height: 100%;\n  width: auto;\n}\n", ""]);

// exports


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
    { staticClass: "popup-mask pm-pro-automation pm-pro-import-task-modal" },
    [
      _c("div", { staticClass: "popup-container automation-popup-container" }, [
        _c("div", { staticClass: "automation-popup-body" }, [
          _c("div", { staticClass: "head" }, [
            _c("span", [
              _vm._v(_vm._s(_vm.__("Import From Task List", "pm-pro")))
            ])
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
            _c("div", { class: !_vm.isListLoaded ? "pm-pro-form-wrap" : "" }, [
              _c(
                "div",
                [
                  _c("pm-list-drop-down", {
                    attrs: { options: _vm.listDropDownOptions },
                    on: {
                      onChange: _vm.fetchTasks,
                      afterGetLists: _vm.afterFetchList
                    }
                  })
                ],
                1
              ),
              _vm._v(" "),
              _c("div", { staticClass: "task-status-tab" }, [
                _c("div", [
                  _c(
                    "a",
                    {
                      class: "first tab-link " + _vm.getClass("all"),
                      attrs: { href: "#" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          return _vm.getTasks("all")
                        }
                      }
                    },
                    [_vm._v(_vm._s(_vm.__("All", "pm-pro")))]
                  )
                ]),
                _vm._v(" "),
                _c("div", [
                  _c(
                    "a",
                    {
                      class: "second tab-link " + _vm.getClass("complete"),
                      attrs: { href: "#" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          return _vm.getTasks("complete")
                        }
                      }
                    },
                    [_vm._v(_vm._s(_vm.__("Completed", "pm-pro")))]
                  )
                ]),
                _vm._v(" "),
                _c("div", [
                  _c(
                    "a",
                    {
                      class: "third tab-link " + _vm.getClass("incomplete"),
                      attrs: { href: "#" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          return _vm.getTasks("incomplete")
                        }
                      }
                    },
                    [_vm._v(_vm._s(_vm.__("Incomplete", "pm-pro")))]
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "all-select-wrap" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.allSelected,
                      expression: "allSelected"
                    }
                  ],
                  staticClass: "checkbox",
                  attrs: { id: "all-select", type: "checkbox" },
                  domProps: {
                    checked: Array.isArray(_vm.allSelected)
                      ? _vm._i(_vm.allSelected, null) > -1
                      : _vm.allSelected
                  },
                  on: {
                    input: function($event) {
                      return _vm.selectAll($event)
                    },
                    change: function($event) {
                      var $$a = _vm.allSelected,
                        $$el = $event.target,
                        $$c = $$el.checked ? true : false
                      if (Array.isArray($$a)) {
                        var $$v = null,
                          $$i = _vm._i($$a, $$v)
                        if ($$el.checked) {
                          $$i < 0 && (_vm.allSelected = $$a.concat([$$v]))
                        } else {
                          $$i > -1 &&
                            (_vm.allSelected = $$a
                              .slice(0, $$i)
                              .concat($$a.slice($$i + 1)))
                        }
                      } else {
                        _vm.allSelected = $$c
                      }
                    }
                  }
                }),
                _vm._v(" "),
                _c("label", { attrs: { for: "all-select" } }, [
                  _vm._v(_vm._s(_vm.__("Select All", "pm-pro")))
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "tasks-wrap" }, [
                _vm.isTaskLoading
                  ? _c("div", { staticClass: "loading-animation" }, [
                      _c("div", { staticClass: "loading-projects-title" }, [
                        _vm._v(_vm._s(_vm.__("Loading Tasks", "pm-pro")))
                      ]),
                      _vm._v(" "),
                      _vm._m(1)
                    ])
                  : _vm._e(),
                _vm._v(" "),
                !_vm.isTaskLoading && _vm.content.length
                  ? _c(
                      "ul",
                      { staticClass: "task-ul" },
                      _vm._l(_vm.content, function(task) {
                        return _c("li", [
                          _c("div", [
                            _c("input", {
                              directives: [
                                {
                                  name: "model",
                                  rawName: "v-model",
                                  value: _vm.selected,
                                  expression: "selected"
                                }
                              ],
                              staticClass: "checkbox",
                              attrs: {
                                id: "task-" + task.id,
                                type: "checkbox"
                              },
                              domProps: {
                                value: task.id,
                                checked: Array.isArray(_vm.selected)
                                  ? _vm._i(_vm.selected, task.id) > -1
                                  : _vm.selected
                              },
                              on: {
                                input: function($event) {
                                  return _vm.setSelected(task, $event)
                                },
                                change: function($event) {
                                  var $$a = _vm.selected,
                                    $$el = $event.target,
                                    $$c = $$el.checked ? true : false
                                  if (Array.isArray($$a)) {
                                    var $$v = task.id,
                                      $$i = _vm._i($$a, $$v)
                                    if ($$el.checked) {
                                      $$i < 0 &&
                                        (_vm.selected = $$a.concat([$$v]))
                                    } else {
                                      $$i > -1 &&
                                        (_vm.selected = $$a
                                          .slice(0, $$i)
                                          .concat($$a.slice($$i + 1)))
                                    }
                                  } else {
                                    _vm.selected = $$c
                                  }
                                }
                              }
                            }),
                            _vm._v(" "),
                            _c("label", { attrs: { for: "task-" + task.id } }, [
                              _c("span", [_vm._v(_vm._s(task.title))]),
                              _vm._v(" "),
                              _vm.tab.all
                                ? _c(
                                    "span",
                                    {
                                      class:
                                        task.status == "incomplete"
                                          ? "incomplete"
                                          : "complete"
                                    },
                                    [
                                      _vm._v(
                                        "\n                                            " +
                                          _vm._s(
                                            task.status == "complete"
                                              ? _vm.__("Completed", "pm-pro")
                                              : _vm.ucfirst(task.status)
                                          ) +
                                          "\n                                        "
                                      )
                                    ]
                                  )
                                : _vm._e()
                            ])
                          ])
                        ])
                      }),
                      0
                    )
                  : _vm._e(),
                _vm._v(" "),
                !_vm.isTaskLoading && !_vm.content.length
                  ? _c("ul", { staticClass: "task-ul" }, [
                      _c("li", [
                        _c("div", [
                          _vm._v(
                            "\n                                    " +
                              _vm._s(_vm.__("No Task Found!", "pm-pro")) +
                              "\n                                "
                          )
                        ])
                      ])
                    ])
                  : _vm._e()
              ])
            ])
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
                        return _vm.importTasks()
                      }
                    }
                  },
                  [_vm._v(_vm._s(_vm.__("Import Tasks", "pm-pro")))]
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
  },
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
    require("vue-hot-reload-api")      .rerender("data-v-48e6af5f", esExports)
  }
}

/***/ }),
/* 27 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    { staticClass: "pm-wrap pm pm-kanboard" },
    [
      _c("pm-header"),
      _vm._v(" "),
      _c("pm-heder-menu", { attrs: { current: "task_lists" } }),
      _vm._v(" "),
      _c("div", { staticClass: "pm-kanboard-fullscreen" }, [
        _c(
          "div",
          { staticClass: "kanboard-menu-wrap" },
          [
            _c(
              "a",
              {
                class: "fullscreen-view-btn list-action-group",
                attrs: { title: _vm.__("Fullscreen", "pm-pro"), href: "#" },
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.toggleFullscreen($event)
                  }
                }
              },
              [
                _c("span", { staticClass: "icon-pm-fullscreen" }),
                _vm._v(" "),
                _c("span", { staticClass: "icon-pm-fullscreen-text" }, [
                  _vm._v(_vm._s(_vm.__("Fullscreen", "pm-pro")))
                ])
              ]
            ),
            _vm._v(" "),
            _c("kanboard-menu", { attrs: { searchForm: _vm.searchForm } }),
            _c("br")
          ],
          1
        ),
        _vm._v(" "),
        !_vm.isFetchBoard
          ? _c("div", { staticClass: "pm-data-load-before" }, [_vm._m(0)])
          : _vm._e(),
        _vm._v(" "),
        _vm.isFetchBoard
          ? _c(
              "div",
              {
                class: _vm.fullscreen
                  ? "pm-kanboard-fullscreen-active kbc-kanboard"
                  : "kbc-kanboard"
              },
              [
                !_vm.sortableRequestStatus
                  ? _c("div", { staticClass: "loadmoreanimation" }, [_vm._m(1)])
                  : _vm._e(),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    class: !_vm.sortableRequestStatus
                      ? "cursor-none kbc-table-opacity kbc-table-wrap"
                      : "kbc-table-wrap"
                  },
                  [
                    _c(
                      "div",
                      {
                        directives: [
                          {
                            name: "kbc-section-sortable",
                            rawName: "v-kbc-section-sortable"
                          }
                        ],
                        staticClass:
                          "kbc-th-wrap kbc-section-order-wrap ui-sortable"
                      },
                      [
                        _vm._l(_vm.boards, function(board, board_index) {
                          return _c(
                            "div",
                            {
                              key: board.id + "-column",
                              staticClass:
                                "kbc-th kbc-sortable-section kbc-section-order-by ui-sortable-handle",
                              attrs: { "data-section_id": board.id }
                            },
                            [
                              _c(
                                "div",
                                { staticClass: "kbc-section-background" },
                                [
                                  _c(
                                    "div",
                                    {
                                      staticClass: "kbc-section-header-wrap",
                                      style:
                                        "color:" +
                                        _vm.boardTextColor(board) +
                                        " background: " +
                                        _vm.getBackground(board)
                                    },
                                    [
                                      _c(
                                        "div",
                                        { staticClass: "kbc-section-header" },
                                        [
                                          _c(
                                            "div",
                                            {
                                              staticClass: "kbc-section-title",
                                              attrs: { title: "Open" }
                                            },
                                            [
                                              !board.isUpdateMode
                                                ? _c(
                                                    "span",
                                                    {
                                                      on: {
                                                        click: function(
                                                          $event
                                                        ) {
                                                          $event.preventDefault()
                                                          return _vm.showHideBoardTitleUpdate(
                                                            board
                                                          )
                                                        }
                                                      }
                                                    },
                                                    [
                                                      _vm._v(
                                                        _vm._s(board.title)
                                                      )
                                                    ]
                                                  )
                                                : _vm._e(),
                                              _vm._v(" "),
                                              board.isUpdateMode
                                                ? _c("span", [
                                                    _c("input", {
                                                      directives: [
                                                        {
                                                          name:
                                                            "pm-click-outside",
                                                          rawName:
                                                            "v-pm-click-outside",
                                                          value:
                                                            _vm.closeBroadTitleInput,
                                                          expression:
                                                            "closeBroadTitleInput"
                                                        }
                                                      ],
                                                      staticClass:
                                                        "pm-pro-kanboard-title-field",
                                                      attrs: {
                                                        type: "text",
                                                        required: ""
                                                      },
                                                      domProps: {
                                                        value: board.title
                                                      },
                                                      on: {
                                                        blur: function($event) {
                                                          return _vm.boardNameUpdate(
                                                            board,
                                                            $event
                                                          )
                                                        },
                                                        keyup: function(
                                                          $event
                                                        ) {
                                                          if (
                                                            !$event.type.indexOf(
                                                              "key"
                                                            ) &&
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
                                                          return _vm.boardNameUpdate(
                                                            board,
                                                            $event
                                                          )
                                                        }
                                                      }
                                                    })
                                                  ])
                                                : _vm._e()
                                            ]
                                          )
                                        ]
                                      ),
                                      _vm._v(" "),
                                      _vm.is_manager() ||
                                      _vm.has_manage_capability()
                                        ? _c(
                                            "div",
                                            {
                                              staticClass:
                                                "kbc-section-action kbc-non-sortable"
                                            },
                                            [
                                              _c(
                                                "div",
                                                {
                                                  staticClass:
                                                    "kbc-action-icon-wrap"
                                                },
                                                [
                                                  _c(
                                                    "a",
                                                    {
                                                      staticClass:
                                                        "pm-pro-kanboard-action-hrf",
                                                      attrs: {
                                                        title:
                                                          _vm.add_task_text,
                                                        href: "#"
                                                      },
                                                      on: {
                                                        click: function(
                                                          $event
                                                        ) {
                                                          $event.preventDefault()
                                                          return _vm.showHideTaskSearch(
                                                            board
                                                          )
                                                        }
                                                      }
                                                    },
                                                    [
                                                      _c(
                                                        "span",
                                                        {
                                                          style:
                                                            "color: " +
                                                            _vm.boardBtnColor(
                                                              board
                                                            )
                                                        },
                                                        [
                                                          _c("i", {
                                                            staticClass:
                                                              "fa fa-plus",
                                                            attrs: {
                                                              "aria-hidden":
                                                                "true"
                                                            }
                                                          })
                                                        ]
                                                      )
                                                    ]
                                                  ),
                                                  _vm._v(" "),
                                                  board.searchTask
                                                    ? _c(
                                                        "div",
                                                        {
                                                          directives: [
                                                            {
                                                              name:
                                                                "pm-click-outside",
                                                              rawName:
                                                                "v-pm-click-outside",
                                                              value:
                                                                _vm.CloseTaskSearch,
                                                              expression:
                                                                "CloseTaskSearch"
                                                            }
                                                          ],
                                                          staticClass:
                                                            "pm-pro-multiselect-wrap"
                                                        },
                                                        [
                                                          _c(
                                                            "multiselect",
                                                            {
                                                              directives: [
                                                                {
                                                                  name:
                                                                    "kbc-kanboard-autofocus",
                                                                  rawName:
                                                                    "v-kbc-kanboard-autofocus"
                                                                }
                                                              ],
                                                              ref:
                                                                "kbc_kanboard_autofocus",
                                                              refInFor: true,
                                                              attrs: {
                                                                "select-label":
                                                                  "",
                                                                "selected-label":
                                                                  "selected",
                                                                "deselect-label":
                                                                  "",
                                                                id: "ajax",
                                                                label: "title",
                                                                "track-by":
                                                                  "id",
                                                                placeholder:
                                                                  _vm.search_task,
                                                                "open-direction":
                                                                  "",
                                                                multiple: false,
                                                                searchable: true,
                                                                loading:
                                                                  board.taskLoading,
                                                                options:
                                                                  _vm.optionTasks
                                                              },
                                                              on: {
                                                                "search-change": function(
                                                                  $event
                                                                ) {
                                                                  return _vm.asyncFind(
                                                                    board,
                                                                    $event
                                                                  )
                                                                },
                                                                input: function(
                                                                  $event
                                                                ) {
                                                                  return _vm.saveSingleTask(
                                                                    board,
                                                                    $event
                                                                  )
                                                                }
                                                              },
                                                              model: {
                                                                value:
                                                                  _vm.selectedTasks,
                                                                callback: function(
                                                                  $$v
                                                                ) {
                                                                  _vm.selectedTasks = $$v
                                                                },
                                                                expression:
                                                                  "selectedTasks"
                                                              }
                                                            },
                                                            [
                                                              _c(
                                                                "span",
                                                                {
                                                                  attrs: {
                                                                    slot:
                                                                      "noResult"
                                                                  },
                                                                  slot:
                                                                    "noResult"
                                                                },
                                                                [
                                                                  _vm._v(
                                                                    _vm._s(
                                                                      _vm.__(
                                                                        "No task found.",
                                                                        "pm-pro"
                                                                      )
                                                                    )
                                                                  )
                                                                ]
                                                              )
                                                            ]
                                                          )
                                                        ],
                                                        1
                                                      )
                                                    : _vm._e(),
                                                  _vm._v(" "),
                                                  _c("pm-dropdown-menu", [
                                                    _c(
                                                      "a",
                                                      {
                                                        staticClass:
                                                          "pm-pro-kanboard-action-hrf pm-pro-kanboard-del-btn",
                                                        attrs: {
                                                          slot: "clickButton",
                                                          href: "#"
                                                        },
                                                        on: {
                                                          click: function(
                                                            $event
                                                          ) {
                                                            $event.preventDefault()
                                                            return _vm.showHideAction(
                                                              board
                                                            )
                                                          }
                                                        },
                                                        slot: "clickButton"
                                                      },
                                                      [
                                                        _c(
                                                          "span",
                                                          {
                                                            style:
                                                              "color: " +
                                                              _vm.boardBtnColor(
                                                                board
                                                              )
                                                          },
                                                          [
                                                            _c("i", {
                                                              staticClass:
                                                                "fa fa-ellipsis-v",
                                                              attrs: {
                                                                "aria-hidden":
                                                                  "true"
                                                              }
                                                            })
                                                          ]
                                                        )
                                                      ]
                                                    ),
                                                    _vm._v(" "),
                                                    _c(
                                                      "div",
                                                      {
                                                        staticClass:
                                                          "kbc-action-wrap"
                                                      },
                                                      [
                                                        !board.colorPicker
                                                          ? _c(
                                                              "ul",
                                                              {
                                                                staticClass:
                                                                  "pm-dorpdown-menu-ul"
                                                              },
                                                              [
                                                                _c(
                                                                  "li",
                                                                  {
                                                                    staticClass:
                                                                      "pm-dorpdown-menu-li first"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "div",
                                                                      {
                                                                        staticClass:
                                                                          "pm-dorpdown-menu-li-content"
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "a",
                                                                          {
                                                                            staticClass:
                                                                              "pm-dorpdown-menu-link",
                                                                            attrs: {
                                                                              href:
                                                                                "#"
                                                                            },
                                                                            on: {
                                                                              click: function(
                                                                                $event
                                                                              ) {
                                                                                $event.preventDefault()
                                                                                return _vm.automationPopUp(
                                                                                  board
                                                                                )
                                                                              }
                                                                            }
                                                                          },
                                                                          [
                                                                            _c(
                                                                              "i",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-dorpdown-menu-icon flaticon-pm-settings"
                                                                              }
                                                                            ),
                                                                            _vm._v(
                                                                              " "
                                                                            ),
                                                                            _c(
                                                                              "span",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-dorpdown-menu-text"
                                                                              },
                                                                              [
                                                                                _vm._v(
                                                                                  _vm._s(
                                                                                    _vm.__(
                                                                                      "Manage Automation",
                                                                                      "pm-pro"
                                                                                    )
                                                                                  )
                                                                                )
                                                                              ]
                                                                            )
                                                                          ]
                                                                        )
                                                                      ]
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "li",
                                                                  {
                                                                    staticClass:
                                                                      "pm-dorpdown-menu-li"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "div",
                                                                      {
                                                                        staticClass:
                                                                          "pm-dorpdown-menu-li-content"
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "a",
                                                                          {
                                                                            staticClass:
                                                                              "pm-dorpdown-menu-link",
                                                                            attrs: {
                                                                              href:
                                                                                "#"
                                                                            },
                                                                            on: {
                                                                              click: function(
                                                                                $event
                                                                              ) {
                                                                                $event.preventDefault()
                                                                                return _vm.activeImportTaskModal(
                                                                                  board
                                                                                )
                                                                              }
                                                                            }
                                                                          },
                                                                          [
                                                                            _c(
                                                                              "i",
                                                                              {
                                                                                staticClass:
                                                                                  "logo icon-pm-task-list pm-dorpdown-menu-icon"
                                                                              }
                                                                            ),
                                                                            _vm._v(
                                                                              " "
                                                                            ),
                                                                            _c(
                                                                              "span",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-dorpdown-menu-text"
                                                                              },
                                                                              [
                                                                                _vm._v(
                                                                                  _vm._s(
                                                                                    _vm.__(
                                                                                      "Import Task",
                                                                                      "pm-pro"
                                                                                    )
                                                                                  )
                                                                                )
                                                                              ]
                                                                            )
                                                                          ]
                                                                        )
                                                                      ]
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "li",
                                                                  {
                                                                    staticClass:
                                                                      "pm-dorpdown-menu-li"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "div",
                                                                      {
                                                                        staticClass:
                                                                          "pm-dorpdown-menu-li-content"
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "a",
                                                                          {
                                                                            staticClass:
                                                                              "pm-dorpdown-menu-link",
                                                                            attrs: {
                                                                              href:
                                                                                "#"
                                                                            },
                                                                            on: {
                                                                              click: function(
                                                                                $event
                                                                              ) {
                                                                                $event.preventDefault()
                                                                                return _vm.showColorPicker(
                                                                                  board
                                                                                )
                                                                              }
                                                                            }
                                                                          },
                                                                          [
                                                                            _c(
                                                                              "i",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-icon flaticon-color-picker pm-dorpdown-menu-icon"
                                                                              }
                                                                            ),
                                                                            _vm._v(
                                                                              " "
                                                                            ),
                                                                            _c(
                                                                              "span",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-dorpdown-menu-text"
                                                                              },
                                                                              [
                                                                                _vm._v(
                                                                                  _vm._s(
                                                                                    _vm.__(
                                                                                      "Background Color",
                                                                                      "pm-pro"
                                                                                    )
                                                                                  )
                                                                                )
                                                                              ]
                                                                            )
                                                                          ]
                                                                        )
                                                                      ]
                                                                    )
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "li",
                                                                  {
                                                                    staticClass:
                                                                      "pm-dorpdown-menu-li"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "div",
                                                                      {
                                                                        staticClass:
                                                                          "pm-dorpdown-menu-li-content"
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "a",
                                                                          {
                                                                            staticClass:
                                                                              "pm-dorpdown-menu-link",
                                                                            attrs: {
                                                                              href:
                                                                                "#"
                                                                            },
                                                                            on: {
                                                                              click: function(
                                                                                $event
                                                                              ) {
                                                                                $event.preventDefault()
                                                                                return _vm.selfDeleteBoard(
                                                                                  board
                                                                                )
                                                                              }
                                                                            }
                                                                          },
                                                                          [
                                                                            _c(
                                                                              "i",
                                                                              {
                                                                                staticClass:
                                                                                  "fa fa-trash pm-dorpdown-menu-icon",
                                                                                attrs: {
                                                                                  "aria-hidden":
                                                                                    "true"
                                                                                }
                                                                              }
                                                                            ),
                                                                            _vm._v(
                                                                              " "
                                                                            ),
                                                                            _c(
                                                                              "span",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-dorpdown-menu-text"
                                                                              },
                                                                              [
                                                                                _vm._v(
                                                                                  _vm._s(
                                                                                    _vm.__(
                                                                                      "Delete",
                                                                                      "pm-pro"
                                                                                    )
                                                                                  )
                                                                                )
                                                                              ]
                                                                            )
                                                                          ]
                                                                        )
                                                                      ]
                                                                    )
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          : _vm._e(),
                                                        _vm._v(" "),
                                                        board.colorPicker
                                                          ? _c(
                                                              "div",
                                                              {
                                                                staticClass:
                                                                  "content-action-wrap"
                                                              },
                                                              [
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    staticClass:
                                                                      "header"
                                                                  },
                                                                  [
                                                                    _c("span", [
                                                                      _c(
                                                                        "a",
                                                                        {
                                                                          attrs: {
                                                                            href:
                                                                              "#"
                                                                          },
                                                                          on: {
                                                                            click: function(
                                                                              $event
                                                                            ) {
                                                                              $event.preventDefault()
                                                                              return _vm.backColorPicker(
                                                                                board
                                                                              )
                                                                            }
                                                                          }
                                                                        },
                                                                        [
                                                                          _c(
                                                                            "i",
                                                                            {
                                                                              staticClass:
                                                                                "flaticon-pm-left-arrow"
                                                                            }
                                                                          )
                                                                        ]
                                                                      )
                                                                    ]),
                                                                    _vm._v(" "),
                                                                    _c("span", [
                                                                      _vm._v(
                                                                        _vm._s(
                                                                          _vm.__(
                                                                            "Select Banground Color",
                                                                            "pm-pro"
                                                                          )
                                                                        )
                                                                      )
                                                                    ])
                                                                  ]
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    staticClass:
                                                                      "content"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "pm-color-picker",
                                                                      {
                                                                        attrs: {
                                                                          showHide:
                                                                            _vm.showHideColoerPickerBtn
                                                                        },
                                                                        model: {
                                                                          value:
                                                                            board.header_background,
                                                                          callback: function(
                                                                            $$v
                                                                          ) {
                                                                            _vm.$set(
                                                                              board,
                                                                              "header_background",
                                                                              $$v
                                                                            )
                                                                          },
                                                                          expression:
                                                                            "board.header_background"
                                                                        }
                                                                      }
                                                                    )
                                                                  ],
                                                                  1
                                                                ),
                                                                _vm._v(" "),
                                                                _c(
                                                                  "div",
                                                                  {
                                                                    staticClass:
                                                                      "button-group"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "div",
                                                                      {
                                                                        staticClass:
                                                                          "apply"
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "a",
                                                                          {
                                                                            class: !_vm
                                                                              .colorPicker
                                                                              .canApply
                                                                              ? "apply-text-color button button-primary"
                                                                              : "button button-primary",
                                                                            attrs: {
                                                                              href:
                                                                                "#"
                                                                            },
                                                                            on: {
                                                                              click: function(
                                                                                $event
                                                                              ) {
                                                                                $event.preventDefault()
                                                                                return _vm.setColumnBackgroundColor(
                                                                                  board
                                                                                )
                                                                              }
                                                                            }
                                                                          },
                                                                          [
                                                                            _vm._v(
                                                                              "\n                                                                " +
                                                                                _vm._s(
                                                                                  _vm.__(
                                                                                    "Apply",
                                                                                    "pm-pro"
                                                                                  )
                                                                                ) +
                                                                                "\n                                                            "
                                                                            )
                                                                          ]
                                                                        ),
                                                                        _vm._v(
                                                                          " "
                                                                        ),
                                                                        !_vm
                                                                          .colorPicker
                                                                          .canApply
                                                                          ? _c(
                                                                              "div",
                                                                              {
                                                                                staticClass:
                                                                                  "pm-circle-spinner-white"
                                                                              }
                                                                            )
                                                                          : _vm._e()
                                                                      ]
                                                                    )
                                                                  ]
                                                                )
                                                              ]
                                                            )
                                                          : _vm._e()
                                                      ]
                                                    )
                                                  ])
                                                ],
                                                1
                                              ),
                                              _vm._v(" "),
                                              _c("div", {
                                                staticClass: "kbc-clearfix"
                                              })
                                            ]
                                          )
                                        : _vm._e(),
                                      _vm._v(" "),
                                      _c("div", { staticClass: "kbc-clearfix" })
                                    ]
                                  ),
                                  _vm._v(" "),
                                  _c("div", { staticClass: "kbc-tasks-wrap" }, [
                                    _c(
                                      "div",
                                      {
                                        directives: [
                                          {
                                            name: "kbc-sortable",
                                            rawName: "v-kbc-sortable"
                                          },
                                          {
                                            name: "kbc-load-more",
                                            rawName: "v-kbc-load-more"
                                          }
                                        ],
                                        staticClass:
                                          "kbc-kanboard-sortable-wrap  kbc-sortable-connented ui-sortable",
                                        attrs: {
                                          "data-task_length":
                                            board.task.data.length,
                                          "data-section_id": board.id
                                        }
                                      },
                                      _vm._l(board.task.data, function(
                                        task,
                                        task_index
                                      ) {
                                        return _c(
                                          "div",
                                          {
                                            key: task.id,
                                            staticClass: "kbc-td-contents",
                                            attrs: {
                                              "data-order": task_index,
                                              "data-task_id": task.id
                                            }
                                          },
                                          [
                                            _c(
                                              "div",
                                              {
                                                staticClass:
                                                  "kbc-content-inside"
                                              },
                                              [
                                                _c(
                                                  "div",
                                                  {
                                                    staticClass:
                                                      "kbc-title-wrap"
                                                  },
                                                  [
                                                    _c(
                                                      "a",
                                                      {
                                                        attrs: { href: "#" },
                                                        on: {
                                                          click: function(
                                                            $event
                                                          ) {
                                                            $event.preventDefault()
                                                            return _vm.getSingleTask(
                                                              task,
                                                              board
                                                            )
                                                          }
                                                        }
                                                      },
                                                      [
                                                        _vm._v(
                                                          _vm._s(task.title)
                                                        )
                                                      ]
                                                    )
                                                  ]
                                                ),
                                                _vm._v(" "),
                                                _c(
                                                  "div",
                                                  {
                                                    staticClass:
                                                      "kbc-after-task-title-wrap"
                                                  },
                                                  [
                                                    _c(
                                                      "div",
                                                      {
                                                        staticClass:
                                                          "kbc-task-user-wrap"
                                                      },
                                                      _vm._l(
                                                        task.assignees.data,
                                                        function(
                                                          user,
                                                          user_index
                                                        ) {
                                                          return _c(
                                                            "span",
                                                            {
                                                              key: user_index,
                                                              staticClass:
                                                                "cpm-assigned-user"
                                                            },
                                                            [
                                                              _c(
                                                                "a",
                                                                {
                                                                  attrs: {
                                                                    href: "",
                                                                    title:
                                                                      user.display_name
                                                                  }
                                                                },
                                                                [
                                                                  _c("img", {
                                                                    staticClass:
                                                                      "avatar avatar-48 photo",
                                                                    attrs: {
                                                                      alt:
                                                                        user.display_name,
                                                                      src:
                                                                        user.avatar_url,
                                                                      srcset:
                                                                        user.avatar_url,
                                                                      height:
                                                                        "48",
                                                                      width:
                                                                        "48"
                                                                    }
                                                                  })
                                                                ]
                                                              )
                                                            ]
                                                          )
                                                        }
                                                      ),
                                                      0
                                                    ),
                                                    _vm._v(" "),
                                                    _vm.hasDueDate(task)
                                                      ? _c(
                                                          "div",
                                                          {
                                                            staticClass:
                                                              "kbc-task-date-wrap"
                                                          },
                                                          [
                                                            _c(
                                                              "span",
                                                              {
                                                                class: _vm.taskDateWrap(
                                                                  task.start_at
                                                                    .date,
                                                                  task.due_date
                                                                    .date
                                                                )
                                                              },
                                                              [
                                                                task.start_at
                                                                  .date !=
                                                                  null ||
                                                                task.due_date
                                                                  .date != null
                                                                  ? _c("i", {
                                                                      staticClass:
                                                                        "fa fa-clock-o",
                                                                      attrs: {
                                                                        "aria-hidden":
                                                                          "true"
                                                                      }
                                                                    })
                                                                  : _vm._e(),
                                                                _vm._v(" "),
                                                                _c("span", [
                                                                  _vm._v(
                                                                    _vm._s(
                                                                      _vm.shortDateFormat(
                                                                        task
                                                                          .due_date
                                                                          .date
                                                                      )
                                                                    )
                                                                  )
                                                                ])
                                                              ]
                                                            )
                                                          ]
                                                        )
                                                      : _vm._e(),
                                                    _vm._v(" "),
                                                    _c("div", {
                                                      staticClass:
                                                        "kbc-clearfix"
                                                    })
                                                  ]
                                                )
                                              ]
                                            ),
                                            _vm._v(" "),
                                            _c(
                                              "div",
                                              {
                                                staticClass:
                                                  "kbc-after-inside-content"
                                              },
                                              [
                                                _c(
                                                  "router-link",
                                                  {
                                                    staticClass:
                                                      "kbc-comment-count",
                                                    attrs: {
                                                      to: {
                                                        name: "single_task",
                                                        params: {
                                                          list_id:
                                                            typeof task.task_list !=
                                                            "undefined"
                                                              ? task.task_list
                                                                  .data.id
                                                              : "",
                                                          task_id: task.id,
                                                          project_id:
                                                            _vm.project_id,
                                                          task: task
                                                        }
                                                      }
                                                    }
                                                  },
                                                  [
                                                    _c("span", [
                                                      _c("i", {
                                                        staticClass:
                                                          "fa fa-commenting-o wpup-right-spage",
                                                        attrs: {
                                                          "aria-hidden": "true"
                                                        }
                                                      }),
                                                      _vm._v(
                                                        "\n                                                    " +
                                                          _vm._s(
                                                            task.meta
                                                              .total_comment
                                                          ) +
                                                          "\n                                                "
                                                      )
                                                    ])
                                                  ]
                                                ),
                                                _vm._v(" "),
                                                _vm.can_edit_task(task)
                                                  ? _c(
                                                      "a",
                                                      {
                                                        staticClass:
                                                          "pm-pro-kanboard-action-hrf remove-from-board  pm-right",
                                                        attrs: {
                                                          title:
                                                            _vm.remove_task_broad,
                                                          href: "#"
                                                        },
                                                        on: {
                                                          click: function(
                                                            $event
                                                          ) {
                                                            $event.preventDefault()
                                                            return _vm.removeTaskFromBoard(
                                                              board.id,
                                                              task.id
                                                            )
                                                          }
                                                        }
                                                      },
                                                      [_vm._m(2, true)]
                                                    )
                                                  : _vm._e()
                                              ],
                                              1
                                            )
                                          ]
                                        )
                                      }),
                                      0
                                    )
                                  ]),
                                  _vm._v(" "),
                                  _vm.canCraeteTask
                                    ? _c(
                                        "div",
                                        { staticClass: "kbc-section-footer" },
                                        [
                                          _c(
                                            "form",
                                            {
                                              attrs: { action: "post" },
                                              on: {
                                                submit: function($event) {
                                                  $event.preventDefault()
                                                  return _vm.selfNewTask(board)
                                                }
                                              }
                                            },
                                            [
                                              _c(
                                                "div",
                                                {
                                                  staticClass:
                                                    "kbc-content-inside"
                                                },
                                                [
                                                  _c("input", {
                                                    directives: [
                                                      {
                                                        name: "model",
                                                        rawName: "v-model",
                                                        value: board.task_title,
                                                        expression:
                                                          "board.task_title"
                                                      }
                                                    ],
                                                    staticClass:
                                                      "kbc-section-new-task",
                                                    attrs: {
                                                      required: "required",
                                                      "data-section_id": "1",
                                                      "data-new-task-field":
                                                        "1",
                                                      type: "text",
                                                      placeholder:
                                                        _vm.add_new_task_text
                                                    },
                                                    domProps: {
                                                      value: board.task_title
                                                    },
                                                    on: {
                                                      click: function($event) {
                                                        $event.preventDefault()
                                                        return _vm.selfShowHideTaskForm(
                                                          board
                                                        )
                                                      },
                                                      input: function($event) {
                                                        if (
                                                          $event.target
                                                            .composing
                                                        ) {
                                                          return
                                                        }
                                                        _vm.$set(
                                                          board,
                                                          "task_title",
                                                          $event.target.value
                                                        )
                                                      }
                                                    }
                                                  }),
                                                  _vm._v(" "),
                                                  board.newTaskForm
                                                    ? _c(
                                                        "div",
                                                        {
                                                          staticClass:
                                                            "kbc-after-task-title-wrap"
                                                        },
                                                        [
                                                          _c(
                                                            "div",
                                                            {
                                                              staticClass:
                                                                "kbc-task-user-wrap"
                                                            },
                                                            _vm._l(
                                                              _vm.taskAssigns,
                                                              function(
                                                                board_user,
                                                                board_user_index
                                                              ) {
                                                                return _c(
                                                                  "span",
                                                                  {
                                                                    key: board_user_index,
                                                                    staticClass:
                                                                      "cpm-assigned-user"
                                                                  },
                                                                  [
                                                                    _c("img", {
                                                                      staticClass:
                                                                        "avatar avatar-48 photo",
                                                                      attrs: {
                                                                        alt:
                                                                          board_user.display_name,
                                                                        src:
                                                                          board_user.avatar_url,
                                                                        srcset:
                                                                          board_user.avatar_url,
                                                                        height:
                                                                          "48",
                                                                        width:
                                                                          "48"
                                                                      }
                                                                    })
                                                                  ]
                                                                )
                                                              }
                                                            ),
                                                            0
                                                          ),
                                                          _vm._v(" "),
                                                          _c(
                                                            "div",
                                                            {
                                                              staticClass:
                                                                "kbc-task-date-wrap"
                                                            },
                                                            [
                                                              _c(
                                                                "span",
                                                                {
                                                                  staticClass:
                                                                    "cpm-current-date",
                                                                  class: _vm.taskDateWrap(
                                                                    "",
                                                                    _vm.endDate
                                                                  )
                                                                },
                                                                [
                                                                  _vm.endDate !=
                                                                  ""
                                                                    ? _c("i", {
                                                                        staticClass:
                                                                          "fa fa-clock-o",
                                                                        attrs: {
                                                                          "aria-hidden":
                                                                            "true"
                                                                        }
                                                                      })
                                                                    : _vm._e(),
                                                                  _vm._v(" "),
                                                                  _c("span", [
                                                                    _vm._v(
                                                                      _vm._s(
                                                                        _vm.shortDateFormat(
                                                                          _vm.endDate
                                                                        )
                                                                      )
                                                                    )
                                                                  ])
                                                                ]
                                                              )
                                                            ]
                                                          ),
                                                          _vm._v(" "),
                                                          _c("div", {
                                                            staticClass:
                                                              "kbc-clearfix"
                                                          })
                                                        ]
                                                      )
                                                    : _vm._e()
                                                ]
                                              ),
                                              _vm._v(" "),
                                              board.newTaskForm
                                                ? _c(
                                                    "div",
                                                    {
                                                      staticClass:
                                                        "kbc-popover kbc-new-task-popover"
                                                    },
                                                    [
                                                      _c("div", {
                                                        staticClass: "kbc-arrow"
                                                      }),
                                                      _vm._v(" "),
                                                      _c("div", [
                                                        _c(
                                                          "div",
                                                          {
                                                            staticClass:
                                                              "cpm-todo-lists-drop-down-wrap"
                                                          },
                                                          [
                                                            _c("div", [
                                                              _c(
                                                                "div",
                                                                {
                                                                  staticClass:
                                                                    "cpm-todo-lists-drop-down-wrap"
                                                                },
                                                                [
                                                                  !board.is_active_lists_drop_dwon
                                                                    ? _c(
                                                                        "div",
                                                                        [
                                                                          _c(
                                                                            "a",
                                                                            {
                                                                              staticClass:
                                                                                "cpm-inline-task-event",
                                                                              attrs: {
                                                                                href:
                                                                                  "#"
                                                                              },
                                                                              on: {
                                                                                click: function(
                                                                                  $event
                                                                                ) {
                                                                                  $event.preventDefault()
                                                                                  return _vm.showListsDropDown(
                                                                                    board
                                                                                  )
                                                                                }
                                                                              }
                                                                            },
                                                                            [
                                                                              _c(
                                                                                "i",
                                                                                {
                                                                                  staticClass:
                                                                                    "fa fa-list-ul cpm-inline-task-lists-icon",
                                                                                  attrs: {
                                                                                    "aria-hidden":
                                                                                      "true"
                                                                                  }
                                                                                }
                                                                              ),
                                                                              _vm._v(
                                                                                "\n                                                                " +
                                                                                  _vm._s(
                                                                                    _vm.__(
                                                                                      "Lists",
                                                                                      "pm-pro"
                                                                                    )
                                                                                  ) +
                                                                                  "\n\n                                                            "
                                                                              )
                                                                            ]
                                                                          )
                                                                        ]
                                                                      )
                                                                    : _vm._e(),
                                                                  _vm._v(" "),
                                                                  board.is_active_lists_drop_dwon
                                                                    ? _c(
                                                                        "div",
                                                                        {
                                                                          staticClass:
                                                                            "cpm-multiselect"
                                                                        },
                                                                        [
                                                                          _c(
                                                                            "a",
                                                                            {
                                                                              staticClass:
                                                                                "cpm-multiselect-cross",
                                                                              attrs: {
                                                                                href:
                                                                                  "#"
                                                                              },
                                                                              on: {
                                                                                click: function(
                                                                                  $event
                                                                                ) {
                                                                                  $event.preventDefault()
                                                                                  return _vm.showListsDropDown(
                                                                                    board
                                                                                  )
                                                                                }
                                                                              }
                                                                            },
                                                                            [
                                                                              _c(
                                                                                "i",
                                                                                {
                                                                                  staticClass:
                                                                                    "fa fa-times-circle-o",
                                                                                  attrs: {
                                                                                    "aria-hidden":
                                                                                      "true"
                                                                                  }
                                                                                }
                                                                              )
                                                                            ]
                                                                          ),
                                                                          _vm._v(
                                                                            " "
                                                                          ),
                                                                          _c(
                                                                            "multiselect",
                                                                            {
                                                                              attrs: {
                                                                                options:
                                                                                  _vm.lists,
                                                                                multiple: false,
                                                                                "show-labels": true,
                                                                                placeholder:
                                                                                  _vm.select_task_list,
                                                                                "select-label":
                                                                                  "",
                                                                                "selected-label":
                                                                                  "selected",
                                                                                "deselect-label":
                                                                                  "",
                                                                                label:
                                                                                  "title",
                                                                                "track-by":
                                                                                  "id",
                                                                                loading: false,
                                                                                "allow-empty": false
                                                                              },
                                                                              model: {
                                                                                value:
                                                                                  _vm.list,
                                                                                callback: function(
                                                                                  $$v
                                                                                ) {
                                                                                  _vm.list = $$v
                                                                                },
                                                                                expression:
                                                                                  "list"
                                                                              }
                                                                            }
                                                                          )
                                                                        ],
                                                                        1
                                                                      )
                                                                    : _vm._e()
                                                                ]
                                                              )
                                                            ])
                                                          ]
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "div",
                                                          {
                                                            staticClass:
                                                              "cpm-single-task-field-multiselect-wrap"
                                                          },
                                                          [
                                                            !board.is_active_assignUser_dropDown
                                                              ? _c("div", [
                                                                  _c(
                                                                    "a",
                                                                    {
                                                                      staticClass:
                                                                        "cpm-inline-task-event",
                                                                      attrs: {
                                                                        href:
                                                                          "#"
                                                                      },
                                                                      on: {
                                                                        click: function(
                                                                          $event
                                                                        ) {
                                                                          $event.preventDefault()
                                                                          return _vm.showUserDropDown(
                                                                            board
                                                                          )
                                                                        }
                                                                      }
                                                                    },
                                                                    [
                                                                      _c("i", {
                                                                        staticClass:
                                                                          "fa fa-user-plus cpm-inline-task-users-icon",
                                                                        attrs: {
                                                                          "aria-hidden":
                                                                            "true"
                                                                        }
                                                                      }),
                                                                      _vm._v(
                                                                        "\n                                                            " +
                                                                          _vm._s(
                                                                            _vm.__(
                                                                              "Assign User ",
                                                                              "pm-pro"
                                                                            )
                                                                          ) +
                                                                          "\n\n                                                        "
                                                                      )
                                                                    ]
                                                                  )
                                                                ])
                                                              : _vm._e(),
                                                            _vm._v(" "),
                                                            board.is_active_assignUser_dropDown
                                                              ? _c(
                                                                  "div",
                                                                  {
                                                                    staticClass:
                                                                      "cpm-multiselect"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "a",
                                                                      {
                                                                        staticClass:
                                                                          "cpm-multiselect-cross",
                                                                        attrs: {
                                                                          href:
                                                                            "#"
                                                                        },
                                                                        on: {
                                                                          click: function(
                                                                            $event
                                                                          ) {
                                                                            $event.preventDefault()
                                                                            return _vm.showUserDropDown(
                                                                              board
                                                                            )
                                                                          }
                                                                        }
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "i",
                                                                          {
                                                                            staticClass:
                                                                              "fa fa-times-circle-o",
                                                                            attrs: {
                                                                              "aria-hidden":
                                                                                "true"
                                                                            }
                                                                          }
                                                                        )
                                                                      ]
                                                                    ),
                                                                    _vm._v(" "),
                                                                    _c(
                                                                      "multiselect",
                                                                      {
                                                                        attrs: {
                                                                          options:
                                                                            _vm.projectUsers,
                                                                          multiple: true,
                                                                          "close-on-select": false,
                                                                          "clear-on-select": true,
                                                                          "hide-selected": false,
                                                                          "show-labels": true,
                                                                          placeholder:
                                                                            _vm.select_user_text,
                                                                          "select-label":
                                                                            "",
                                                                          "selected-label":
                                                                            "selected",
                                                                          "deselect-label":
                                                                            "",
                                                                          taggable: true,
                                                                          label:
                                                                            "display_name",
                                                                          "track-by":
                                                                            "id",
                                                                          loading: false,
                                                                          "allow-empty": true
                                                                        },
                                                                        scopedSlots: _vm._u(
                                                                          [
                                                                            {
                                                                              key:
                                                                                "option",
                                                                              fn: function(
                                                                                props
                                                                              ) {
                                                                                return [
                                                                                  _c(
                                                                                    "div",
                                                                                    [
                                                                                      _c(
                                                                                        "img",
                                                                                        {
                                                                                          staticClass:
                                                                                            "option__image",
                                                                                          attrs: {
                                                                                            height:
                                                                                              "16",
                                                                                            width:
                                                                                              "16",
                                                                                            src:
                                                                                              props
                                                                                                .option
                                                                                                .avatar_url
                                                                                          }
                                                                                        }
                                                                                      ),
                                                                                      _vm._v(
                                                                                        " "
                                                                                      ),
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
                                                                                                  props
                                                                                                    .option
                                                                                                    .display_name
                                                                                                )
                                                                                              )
                                                                                            ]
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
                                                                          true
                                                                        ),
                                                                        model: {
                                                                          value:
                                                                            _vm.taskAssigns,
                                                                          callback: function(
                                                                            $$v
                                                                          ) {
                                                                            _vm.taskAssigns = $$v
                                                                          },
                                                                          expression:
                                                                            "taskAssigns"
                                                                        }
                                                                      }
                                                                    )
                                                                  ],
                                                                  1
                                                                )
                                                              : _vm._e()
                                                          ]
                                                        ),
                                                        _vm._v(" "),
                                                        _c(
                                                          "div",
                                                          {
                                                            staticClass:
                                                              "cpm-inline-task-end-date-wrap"
                                                          },
                                                          [
                                                            !board.is_enable_due_date
                                                              ? _c(
                                                                  "a",
                                                                  {
                                                                    staticClass:
                                                                      "cpm-inline-task-event",
                                                                    attrs: {
                                                                      href: "#"
                                                                    },
                                                                    on: {
                                                                      click: function(
                                                                        $event
                                                                      ) {
                                                                        $event.preventDefault()
                                                                        return _vm.showTaskEndField(
                                                                          board
                                                                        )
                                                                      }
                                                                    }
                                                                  },
                                                                  [
                                                                    _c("i", {
                                                                      staticClass:
                                                                        "fa fa-calendar cpm-inline-task-end-date-icon",
                                                                      attrs: {
                                                                        "aria-hidden":
                                                                          "true"
                                                                      }
                                                                    }),
                                                                    _vm._v(
                                                                      "\n                                                     " +
                                                                        _vm._s(
                                                                          _vm.__(
                                                                            "Due Date",
                                                                            "pm-pro"
                                                                          )
                                                                        ) +
                                                                        "\n\n                                                "
                                                                    )
                                                                  ]
                                                                )
                                                              : _vm._e(),
                                                            _vm._v(" "),
                                                            board.is_enable_due_date
                                                              ? _c(
                                                                  "div",
                                                                  {
                                                                    staticClass:
                                                                      "cpm-single-task-field-end-wrap"
                                                                  },
                                                                  [
                                                                    _c(
                                                                      "div",
                                                                      {
                                                                        staticClass:
                                                                          "cpm-date-picker-to cpm-inline-date-picker-to"
                                                                      },
                                                                      [
                                                                        _c(
                                                                          "a",
                                                                          {
                                                                            staticClass:
                                                                              "cpm-single-task-field-end-link",
                                                                            attrs: {
                                                                              href:
                                                                                "#"
                                                                            },
                                                                            on: {
                                                                              click: function(
                                                                                $event
                                                                              ) {
                                                                                $event.preventDefault()
                                                                                return _vm.showTaskEndField(
                                                                                  board
                                                                                )
                                                                              }
                                                                            }
                                                                          },
                                                                          [
                                                                            _c(
                                                                              "i",
                                                                              {
                                                                                staticClass:
                                                                                  "fa fa-times-circle-o",
                                                                                attrs: {
                                                                                  "aria-hidden":
                                                                                    "true"
                                                                                }
                                                                              }
                                                                            )
                                                                          ]
                                                                        ),
                                                                        _vm._v(
                                                                          " "
                                                                        ),
                                                                        _c(
                                                                          "pm-date-picker",
                                                                          {
                                                                            model: {
                                                                              value:
                                                                                _vm.endDate,
                                                                              callback: function(
                                                                                $$v
                                                                              ) {
                                                                                _vm.endDate = $$v
                                                                              },
                                                                              expression:
                                                                                "endDate"
                                                                            }
                                                                          }
                                                                        )
                                                                      ],
                                                                      1
                                                                    )
                                                                  ]
                                                                )
                                                              : _vm._e()
                                                          ]
                                                        )
                                                      ])
                                                    ]
                                                  )
                                                : _vm._e(),
                                              _vm._v(" "),
                                              board.newTaskForm
                                                ? _c(
                                                    "div",
                                                    {
                                                      staticClass:
                                                        "button-group-wrap"
                                                    },
                                                    [
                                                      _c(
                                                        "div",
                                                        {
                                                          staticClass:
                                                            "cancel-task-btn-wrap"
                                                        },
                                                        [
                                                          _c(
                                                            "a",
                                                            {
                                                              staticClass:
                                                                "pm-button pm-secondary",
                                                              attrs: {
                                                                href: "#"
                                                              },
                                                              on: {
                                                                click: function(
                                                                  $event
                                                                ) {
                                                                  $event.preventDefault()
                                                                  return _vm.selfCancelTaskForm(
                                                                    board
                                                                  )
                                                                }
                                                              }
                                                            },
                                                            [
                                                              _vm._v(
                                                                "\n                                                 " +
                                                                  _vm._s(
                                                                    _vm.__(
                                                                      "Cancel",
                                                                      "pm-pro"
                                                                    )
                                                                  ) +
                                                                  "\n                                            "
                                                              )
                                                            ]
                                                          )
                                                        ]
                                                      ),
                                                      _vm._v(" "),
                                                      _c(
                                                        "div",
                                                        {
                                                          staticClass:
                                                            "new-task-btn-wrap"
                                                        },
                                                        [
                                                          _c("input", {
                                                            class: !_vm.newTaskRequest
                                                              ? "new-task-btn-color pm-button pm-primary"
                                                              : "pm-button pm-primary",
                                                            attrs: {
                                                              type: "submit",
                                                              href: "#"
                                                            },
                                                            domProps: {
                                                              value:
                                                                _vm.add_text
                                                            }
                                                          }),
                                                          _vm._v(" "),
                                                          !_vm.newTaskRequest
                                                            ? _c("div", {
                                                                staticClass:
                                                                  "pm-circle-spinner"
                                                              })
                                                            : _vm._e()
                                                        ]
                                                      )
                                                    ]
                                                  )
                                                : _vm._e()
                                            ]
                                          )
                                        ]
                                      )
                                    : _vm._e()
                                ]
                              )
                            ]
                          )
                        }),
                        _vm._v(" "),
                        _vm.is_manager() || _vm.has_manage_capability()
                          ? _c(
                              "div",
                              {
                                staticClass:
                                  "kbc-th kbc-th-new-section kbc-non-sortable ui-sortable-handle"
                              },
                              [
                                _c(
                                  "div",
                                  { staticClass: "kbc-section-header" },
                                  [
                                    _c("div", [
                                      _c("input", {
                                        directives: [
                                          {
                                            name: "model",
                                            rawName: "v-model",
                                            value: _vm.boardTitle,
                                            expression: "boardTitle"
                                          }
                                        ],
                                        staticClass: "wpup-new-section-field",
                                        attrs: {
                                          type: "text",
                                          placeholder: _vm.add_new_section,
                                          required: ""
                                        },
                                        domProps: { value: _vm.boardTitle },
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
                                            return _vm.selfNewBoard()
                                          },
                                          input: function($event) {
                                            if ($event.target.composing) {
                                              return
                                            }
                                            _vm.boardTitle = $event.target.value
                                          }
                                        }
                                      })
                                    ])
                                  ]
                                )
                              ]
                            )
                          : _vm._e()
                      ],
                      2
                    )
                  ]
                ),
                _vm._v(" "),
                _vm.searchForm.active
                  ? _c("pm-list-search", {
                      on: {
                        listSearch: _vm.listSearchAction,
                        listSearchCancel: _vm.searchCancel,
                        clearFilter: _vm.clearKanboardFilter
                      }
                    })
                  : _vm._e(),
                _vm._v(" "),
                parseInt(_vm.taskId) &&
                parseInt(_vm.projectId) &&
                _vm.fullscreen
                  ? _c(
                      "div",
                      [
                        _c("single-task", {
                          attrs: {
                            taskId: _vm.taskId,
                            projectId: _vm.projectId
                          }
                        })
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _vm.automation.isActiveAutomation
                  ? _c(
                      "div",
                      [
                        _c("automation", {
                          attrs: {
                            automation: _vm.automation,
                            board: _vm.board
                          }
                        })
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _vm.isActiveImportModal
                  ? _c(
                      "div",
                      [
                        _c("import-task-modal", {
                          attrs: { options: _vm.importOptions },
                          on: {
                            closeImportTaskModal: _vm.deactiveImportTaskModal,
                            afterImport: _vm.afterImport
                          }
                        })
                      ],
                      1
                    )
                  : _vm._e()
              ],
              1
            )
          : _vm._e()
      ]),
      _vm._v(" "),
      parseInt(_vm.taskId) && parseInt(_vm.projectId) && !_vm.fullscreen
        ? _c(
            "div",
            [
              _c("single-task", {
                attrs: { taskId: _vm.taskId, projectId: _vm.projectId }
              })
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
  },
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
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "kbc-spical-char" }, [
      _c("i", { staticClass: "fa fa-minus", attrs: { "aria-hidden": "true" } })
    ])
  }
]
render._withStripped = true
var esExports = { render: render, staticRenderFns: staticRenderFns }
/* harmony default export */ __webpack_exports__["a"] = (esExports);
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-32538f50", esExports)
  }
}

/***/ }),
/* 28 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var pmProkanboard = {
    boardSort: function boardSort(el, binding, vnode) {
        jQuery(el).sortable({
            connectWith: ".kbc-sortable-section",
            placeholder: "kbc-section-sortable-placeholder",
            cancel: '.kbc-non-sortable,input',

            start: function start(event, ui) {
                var content = jQuery(ui.item),
                    height = content.outerHeight(),
                    width = content.outerWidth();
            },
            beforeStop: function beforeStop(event, ui) {

                var sections = jQuery('.kbc-section-order-wrap').find('.kbc-section-order-by'),
                    section_orders = [];

                sections.map(function (index, section_dom) {
                    var section_id = jQuery(section_dom).data('section_id'),
                        order = index;

                    section_orders.push({
                        'section_id': section_id,
                        'order': order
                    });
                });

                if (vnode.context.sortableRequestStatus) {
                    vnode.context.sortableRequestStatus = false;
                    vnode.context.updateBoardOrder(section_orders, function (success, res) {
                        vnode.context.sortableRequestStatus = true;
                        if (!success) {
                            jQuery(el).sortable('cancel');
                        }
                    });
                }
            }
        }).disableSelection();
    },
    taskSortable: function taskSortable(el, binding, vnode) {
        var parent = vnode.context;

        jQuery(el).sortable({
            connectWith: ".kbc-sortable-connented",
            placeholder: "kbc-sortable-placeholder",

            start: function start(event, ui) {
                var content = jQuery(ui.item),
                    height = content.outerHeight(),
                    width = content.outerWidth();

                jQuery(ui.item).closest('.kbc-kanboard-sortable-wrap').find('.kbc-sortable-placeholder').css({
                    height: height,
                    width: width
                });
            },
            stop: function stop(event, ui) {
                pmProkanboard.taskSorting(event, ui, parent, false, vnode);
            },
            receive: function receive(event, ui) {
                pmProkanboard.taskSorting(event, ui, parent, true, vnode);
            }
        }).disableSelection();
    },


    taskSorting: function taskSorting(event, ui, parent, is_move, vnode) {
        var wrap = jQuery(ui.item).closest('.kbc-sortable-connented'),
            task_contents = wrap.find('.kbc-td-contents'),
            section_id = wrap.data('section_id'),
            task_ids = [],
            dragabel_task_id = ui.item.data('task_id');

        if (is_move) {

            var sender_section_id = ui.sender.data('section_id'),
                receive_section_id = ui.item.closest('.kbc-sortable-section').data('section_id'),
                task_id = ui.item.data('task_id'),
                sender_total_task = ui.item.closest('.kbc-sortable-section').find('.kbc-td-contents').length,
                receive_task_index = ui.item.index(),
                dragabel_task_id = dragabel_task_id,
                is_move = 'yes';
        } else {
            var sender_section_id = false,
                receive_section_id = ui.item.closest('.kbc-sortable-section').data('section_id'),
                task_id = ui.item.data('task_id'),
                sender_total_task = false,
                receive_task_index = ui.item.index(),
                dragabel_task_id = dragabel_task_id,
                is_move = 'no';
        }

        task_contents.map(function (index, task_el) {
            var task_id = jQuery(task_el).data('task_id');
            task_ids.push(task_id);
        });

        var request_data = {
            section_id: section_id,
            sender_section_id: sender_section_id,
            sender_total_task: sender_total_task,
            dragabel_task_id: dragabel_task_id,
            task_ids: task_ids,
            is_move: is_move,
            receive_task_index: receive_task_index
        };

        vnode.context.sortableData = request_data;

        if (vnode.context.sortableRequestStatus) {
            vnode.context.sortableRequestStatus = false;

            vnode.context.updateTaskOrder(request_data, function () {
                vnode.context.sortableRequestStatus = true;

                if (request_data.is_move == 'yes') {
                    vnode.context.updateTaskOrder(vnode.context.sortableData);
                }
            });
        }
    },

    loadMore: function loadMore(el, binding, vnode) {
        var self = this;
        jQuery(el).bind('scroll', function () {
            if (jQuery(this).scrollTop() + jQuery(this).innerHeight() >= jQuery(this)[0].scrollHeight) {

                var board_id = jQuery(this).data('section_id');

                vnode.context.loadMore(board_id);
            }
        });
    }
};

pm.Vue.directive('kbc-section-sortable', {
    inserted: function inserted(el, binding, vnode) {

        if (vnode.context.is_manager() || vnode.context.has_manage_capability()) {
            pmProkanboard.boardSort(el, binding, vnode);
        }
    }
});

pm.Vue.directive('kbc-sortable', {
    bind: function bind(el, binding, vnode) {
        pmProkanboard.taskSortable(el, binding, vnode);
    }
});

pm.Vue.directive('kbc-sortable', {
    bind: function bind(el, binding, vnode) {
        pmProkanboard.taskSortable(el, binding, vnode);
    }
});

pm.Vue.directive('kbc-load-more', {
    bind: function bind(el, binding, vnode) {
        pmProkanboard.loadMore(el, binding, vnode);
    }

});

pm.Vue.directive('kbc-kanboard-autofocus', {
    inserted: function inserted(el, binding, vnode) {
        vnode.elm.focus();
    }
});

/***/ })
/******/ ]);