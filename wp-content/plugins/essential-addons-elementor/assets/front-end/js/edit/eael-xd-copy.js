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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/edit/eael-xd-copy.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/edit/eael-xd-copy.js":
/*!*************************************!*\
  !*** ./src/js/edit/eael-xd-copy.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nwindow.XdUtils = window.XdUtils || function () {\n  function a(a, b) {\n    var c,\n      d = b || {};\n    for (c in a) a.hasOwnProperty(c) && (d[c] = a[c]);\n    return d;\n  }\n  return {\n    extend: a\n  };\n}(), window.xdLocalStorage = window.xdLocalStorage || function () {\n  function a(a) {\n    k[a.id] && (k[a.id](a), delete k[a.id]);\n  }\n  function b(b) {\n    var c;\n    try {\n      c = JSON.parse(b.data);\n    } catch (a) {}\n    c && c.namespace === h && (\"iframe-ready\" === c.id ? (m = !0, i.initCallback()) : a(c));\n  }\n  function c(a, b, c, d) {\n    j++, k[j] = d;\n    var e = {\n      namespace: h,\n      id: j,\n      action: a,\n      key: b,\n      value: c\n    };\n    g.contentWindow.postMessage(JSON.stringify(e), \"*\");\n  }\n  function d(a) {\n    i = XdUtils.extend(a, i);\n    var c = document.createElement(\"div\");\n    window.addEventListener ? window.addEventListener(\"message\", b, !1) : window.attachEvent(\"onmessage\", b), c.innerHTML = '<iframe id=\"' + i.iframeId + '\" src=' + i.iframeUrl + ' style=\"display: none;\"></iframe>', document.body.appendChild(c), g = document.getElementById(i.iframeId);\n  }\n  function e() {\n    return l ? !!m || (console.log(\"You must wait for iframe ready message before using the api.\"), !1) : (console.log(\"You must call xdLocalStorage.init() before using it.\"), !1);\n  }\n  function f() {\n    return \"complete\" === document.readyState;\n  }\n  var g,\n    h = \"eael-xd-copy-message\",\n    i = {\n      iframeId: \"cross-domain-iframe\",\n      iframeUrl: void 0,\n      initCallback: function initCallback() {}\n    },\n    j = -1,\n    k = {},\n    l = !1,\n    m = !0;\n  return {\n    init: function init(a) {\n      if (!a.iframeUrl) throw \"You must specify iframeUrl\";\n      if (l) return void console.log(\"xdLocalStorage was already initialized!\");\n      l = !0, f() ? d(a) : document.addEventListener ? document.addEventListener(\"readystatechange\", function () {\n        f() && d(a);\n      }) : document.attachEvent(\"readystatechange\", function () {\n        f() && d(a);\n      });\n    },\n    setItem: function setItem(a, b, d) {\n      e() && c(\"set\", a, b, d);\n    },\n    getItem: function getItem(a, b) {\n      e() && c(\"get\", a, null, b);\n    },\n    removeItem: function removeItem(a, b) {\n      e() && c(\"remove\", a, null, b);\n    },\n    key: function key(a, b) {\n      e() && c(\"key\", a, null, b);\n    },\n    getSize: function getSize(a) {\n      e() && c(\"size\", null, null, a);\n    },\n    getLength: function getLength(a) {\n      e() && c(\"length\", null, null, a);\n    },\n    clear: function clear(a) {\n      e() && c(\"clear\", null, null, a);\n    },\n    wasInit: function wasInit() {\n      return l;\n    }\n  };\n}();\n\n// Initialize xdLocalStorage\nxdLocalStorage.init({\n  iframeUrl: \"https://app.essential-addons.com/cross-domain-copy-paste/\",\n  initCallback: function initCallback() {}\n});\n(function ($) {\n  var buttons = [];\n\n  //Get Unique ID\n  function a(b) {\n    return b.forEach(function (b) {\n      b.id = elementorCommon.helpers.getUniqueId(), 0 < b.elements.length && a(b.elements);\n    }), b;\n  }\n\n  // XD Copy Data import functionality\n  function eaPasteHandler(b, c) {\n    var d = c,\n      e = c.model.get(\"elType\"),\n      f = b.elementcode.elType,\n      g = b.elementcode,\n      h = JSON.stringify(g);\n    var i = /\\.(jpg|png|jpeg|gif|svg|webp|psd|bmp)/gi.test(h),\n      j = {\n        elType: f,\n        settings: g.settings\n      },\n      k = null,\n      l = {\n        index: 0\n      };\n    switch (f) {\n      case \"container\":\n      case \"section\":\n        j.elements = a(g.elements), k = elementor.getPreviewContainer();\n        break;\n      case \"column\":\n        j.elements = a(g.elements);\n        \"section\" === e ? k = d.getContainer() : \"column\" === e ? (k = d.getContainer().parent, l.index = d.getOption(\"_index\") + 1) : \"widget\" === e ? (k = d.getContainer().parent.parent, l.index = d.getContainer().parent.view.getOption(\"_index\") + 1) : void 0;\n        break;\n      case \"widget\":\n        j.widgetType = b.elementtype, k = d.getContainer();\n        \"section\" === e ? k = d.children.findByIndex(0).getContainer() : \"column\" === e ? k = d.getContainer() : \"widget\" === e ? (k = d.getContainer().parent, l.index = d.getOption(\"_index\") + 1) : void 0;\n    }\n    var m = $e.run(\"document/elements/create\", {\n      model: j,\n      container: k,\n      options: l\n    });\n    i && jQuery.ajax({\n      url: eael_xd_copy.ajax_url,\n      method: \"POST\",\n      data: {\n        nonce: eael_xd_copy.nonce,\n        action: \"eael_xd_copy_fetch_content\",\n        xd_copy_data: h\n      }\n    }).done(function (a) {\n      if (a.success) {\n        var b = a.data[0];\n        j.elType = b.elType, j.settings = b.settings, \"widget\" === j.elType ? j.widgetType = b.widgetType : j.elements = b.elements, $e.run(\"document/elements/delete\", {\n          container: m\n        }), $e.run(\"document/elements/create\", {\n          model: j,\n          container: k,\n          options: l\n        });\n      }\n    });\n  }\n  function eaPagePasteHandler(pageElements) {\n    jQuery.ajax({\n      url: eael_xd_copy.ajax_url,\n      method: \"POST\",\n      data: {\n        nonce: eael_xd_copy.nonce,\n        action: \"eael_xd_copy_fetch_content\",\n        xd_copy_data: pageElements\n      }\n    }).done(function (e) {\n      if (e.success) {\n        elementor.previewView.addChildModel(e.data[0]);\n        elementor.notifications.showToast({\n          message: eael_xd_copy.i18n.full_page_paste_message,\n          buttons: buttons\n        });\n        $('#elementor-panel-footer-saver-publish, #elementor-panel-footer-saver-options').find('.elementor-disabled').removeClass('elementor-disabled');\n      }\n    }).fail(function () {\n      elementor.notifications.showToast({\n        message: elementor.translate('Something went wrong!'),\n        buttons: buttons\n      });\n    });\n  }\n\n  // Added XD Copy Context Menu\n  var XdCopyType = [\"container\", \"section\", \"column\", \"widget\"];\n  XdCopyType.forEach(function (XdType, index) {\n    elementor.hooks.addFilter(\"elements/\" + XdType + \"/contextMenuGroups\", function (groups, element) {\n      groups.splice(1, 0, {\n        name: \"eael_\" + XdType,\n        actions: [{\n          name: \"ea_copy\",\n          title: eael_xd_copy.i18n.ea_copy,\n          icon: \"eicon-copy\",\n          shortcut: '<i class=\"eaicon-badge\"></i>',\n          callback: function callback() {\n            var copiedElement = {};\n            copiedElement.elementtype = XdType === \"widget\" ? element.model.get(\"widgetType\") : null;\n            copiedElement.elementcode = element.model.toJSON();\n            xdLocalStorage.setItem(\"eael-xd-copy-data\", JSON.stringify(copiedElement), function (data) {\n              elementor.notifications.showToast({\n                message: eael_xd_copy.i18n[XdType + \"_message\"],\n                buttons: buttons\n              });\n            });\n          }\n        }, {\n          name: \"ea_paste\",\n          title: eael_xd_copy.i18n.ea_paste,\n          icon: \"eicon-import-kit\",\n          shortcut: '<i class=\"eaicon-badge\"></i>',\n          callback: function callback() {\n            xdLocalStorage.getItem(\"eael-xd-copy-data\", function (newElement) {\n              eaPasteHandler(JSON.parse(newElement.value), element);\n              elementor.notifications.showToast({\n                message: eael_xd_copy.i18n.paste_message,\n                buttons: buttons\n              });\n            });\n          }\n        }]\n      });\n      return groups;\n    });\n  });\n  $(document).on('click', '.elementor-context-menu-list__item-ea_copy_all', function () {\n    $(this).closest('.elementor-context-menu').hide();\n    var copiedPage = Object.values(elementor.getPreviewView().children._views).map(function (e) {\n      return e.getContainer().model.toJSON();\n    });\n    xdLocalStorage.setItem(\"eael-xd-copy-all-data\", JSON.stringify(copiedPage), function (data) {\n      elementor.notifications.showToast({\n        message: eael_xd_copy.i18n[\"full_page_message\"],\n        buttons: buttons\n      });\n    });\n  });\n  $(document).on('click', '.elementor-context-menu-list__item-ea_paste_all', function () {\n    $(this).closest('.elementor-context-menu').hide();\n    xdLocalStorage.getItem(\"eael-xd-copy-all-data\", function (pageElements) {\n      elementor.notifications.showToast({\n        message: eael_xd_copy.i18n[\"full_page_pasting_message\"],\n        buttons: buttons\n      });\n      eaPagePasteHandler(JSON.stringify(JSON.parse(pageElements.value)));\n    });\n  });\n  var observer = new MutationObserver(function (mutations_list) {\n    mutations_list.forEach(function (mutation) {\n      mutation.addedNodes.forEach(function (added_node) {\n        if ($(added_node).find('.elementor-context-menu-list__group.elementor-context-menu-list__group-paste').length) {\n          $('.elementor-context-menu-list__group.elementor-context-menu-list__group-paste').closest('.elementor-context-menu-list').prepend(\"<div class=\\\"elementor-context-menu-list__group elementor-context-menu-list__group-ea_copy_all\\\"><div class=\\\"elementor-context-menu-list__item elementor-context-menu-list__item-ea_copy_all\\\"><div class=\\\"elementor-context-menu-list__item__icon\\\"><i class=\\\"eicon-copy\\\"></i></div><div class=\\\"elementor-context-menu-list__item__title\\\">\".concat(eael_xd_copy.i18n.ea_copy_all, \"</div><div class=\\\"elementor-context-menu-list__item__shortcut\\\"><i class=\\\"eaicon-badge\\\"></i></div></div><div class=\\\"elementor-context-menu-list__item elementor-context-menu-list__item-ea_paste_all\\\"><div class=\\\"elementor-context-menu-list__item__icon\\\"><i class=\\\"eicon-import-kit\\\"></i></div><div class=\\\"elementor-context-menu-list__item__title\\\">\").concat(eael_xd_copy.i18n.ea_paste_all, \"</div><div class=\\\"elementor-context-menu-list__item__shortcut\\\"><i class=\\\"eaicon-badge\\\"></i></div></div></div>\"));\n          observer.disconnect();\n        }\n      });\n    });\n  });\n  observer.observe(document.querySelector(\"body\"), {\n    subtree: false,\n    childList: true\n  });\n})(jQuery);\n\n//# sourceURL=webpack:///./src/js/edit/eael-xd-copy.js?");

/***/ })

/******/ });