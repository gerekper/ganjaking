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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/edit/advanced-data-table.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/edit/advanced-data-table.js":
/*!********************************************!*\
  !*** ./src/js/edit/advanced-data-table.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _typeof(o) { \"@babel/helpers - typeof\"; return _typeof = \"function\" == typeof Symbol && \"symbol\" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && \"function\" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? \"symbol\" : typeof o; }, _typeof(o); }\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, \"prototype\", { writable: false }); return Constructor; }\nfunction _toPropertyKey(t) { var i = _toPrimitive(t, \"string\"); return \"symbol\" == _typeof(i) ? i : String(i); }\nfunction _toPrimitive(t, r) { if (\"object\" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || \"default\"); if (\"object\" != _typeof(i)) return i; throw new TypeError(\"@@toPrimitive must return a primitive value.\"); } return (\"string\" === r ? String : Number)(t); }\nvar advancedDataTableProEdit = /*#__PURE__*/function () {\n  function advancedDataTableProEdit() {\n    _classCallCheck(this, advancedDataTableProEdit);\n    ea.hooks.addAction(\"advancedDataTable.afterInitPanel\", \"ea\", this.afterPanelInit);\n    ea.hooks.addAction(\"advancedDataTable.panelAction\", \"ea\", this.panelAction);\n  }\n  _createClass(advancedDataTableProEdit, [{\n    key: \"afterPanelInit\",\n    value: function afterPanelInit(panel, model, view) {\n      setTimeout(function () {\n        var select = panel.el.querySelector('[data-setting=\"ea_adv_data_table_source_remote_table\"]');\n        if (select != null && select.length == 0) {\n          model.attributes.settings.attributes.ea_adv_data_table_source_remote_tables.forEach(function (opt, index) {\n            select[index] = new Option(opt, opt, false, opt == model.attributes.settings.attributes.ea_adv_data_table_source_remote_table);\n          });\n        }\n      }, 50);\n      panel.el.addEventListener(\"mousedown\", function (e) {\n        if (e.target.classList.contains(\"elementor-section-title\") || e.target.parentNode.classList.contains(\"elementor-panel-navigation-tab\")) {\n          setTimeout(function () {\n            var select = panel.el.querySelector('[data-setting=\"ea_adv_data_table_source_remote_table\"]');\n            if (select != null && select.length == 0) {\n              model.attributes.settings.attributes.ea_adv_data_table_source_remote_tables.forEach(function (opt, index) {\n                select[index] = new Option(opt, opt, false, opt == model.attributes.settings.attributes.ea_adv_data_table_source_remote_table);\n              });\n            }\n          }, 50);\n        }\n      });\n    }\n  }, {\n    key: \"panelAction\",\n    value: function panelAction(panel, model, view, event) {\n      if (event.target.dataset.event == \"ea:advTable:connect\") {\n        var button = event.target;\n        button.innerHTML = \"Connecting\";\n        jQuery.ajax({\n          url: localize.ajaxurl,\n          type: \"post\",\n          data: {\n            action: \"connect_remote_db\",\n            security: localize.nonce,\n            host: model.attributes.settings.attributes.ea_adv_data_table_source_remote_host,\n            username: model.attributes.settings.attributes.ea_adv_data_table_source_remote_username,\n            password: model.attributes.settings.attributes.ea_adv_data_table_source_remote_password,\n            database: model.attributes.settings.attributes.ea_adv_data_table_source_remote_database\n          },\n          success: function success(response) {\n            if (response.connected == true) {\n              button.innerHTML = \"Connected\";\n              ea.hooks.doAction(\"advancedDataTable.updateFromView\", view, {\n                ea_adv_data_table_source_remote_connected: true,\n                ea_adv_data_table_source_remote_tables: response.tables\n              }, true);\n\n              // reload panel\n              panel.content.el.querySelector(\".elementor-section-title\").click();\n              panel.content.el.querySelector(\".elementor-section-title\").click();\n              var select = panel.el.querySelector('[data-setting=\"ea_adv_data_table_source_remote_table\"]');\n              select.length = 0;\n              response.tables.forEach(function (opt, index) {\n                select[index] = new Option(opt, opt);\n              });\n            } else {\n              button.innerHTML = \"Failed\";\n            }\n          },\n          error: function error() {\n            button.innerHTML = \"Failed\";\n          }\n        });\n        setTimeout(function () {\n          button.innerHTML = \"Connect\";\n        }, 2000);\n      } else if (event.target.dataset.event == \"ea:advTable:disconnect\") {\n        ea.hooks.doAction(\"advancedDataTable.updateFromView\", view, {\n          ea_adv_data_table_source_remote_connected: false,\n          ea_adv_data_table_source_remote_tables: []\n        }, true);\n\n        // reload panel\n        panel.content.el.querySelector(\".elementor-section-title\").click();\n        panel.content.el.querySelector(\".elementor-section-title\").click();\n      }\n    }\n  }]);\n  return advancedDataTableProEdit;\n}();\nea.hooks.addAction(\"editMode.init\", \"ea\", function () {\n  new advancedDataTableProEdit();\n});\n\n//# sourceURL=webpack:///./src/js/edit/advanced-data-table.js?");

/***/ })

/******/ });