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
/******/ 	return __webpack_require__(__webpack_require__.s = "./export/js/listscreen.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./editing/js/helpers/elements.js":
/*!****************************************!*\
  !*** ./editing/js/helpers/elements.js ***!
  \****************************************/
/*! exports provided: insertAfter, insertBefore */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertAfter", function() { return insertAfter; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "insertBefore", function() { return insertBefore; });
function insertAfter(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}
function insertBefore(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode);
}

/***/ }),

/***/ "./export/js/helper/string.js":
/*!************************************!*\
  !*** ./export/js/helper/string.js ***!
  \************************************/
/*! exports provided: guidGenerator, format */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "guidGenerator", function() { return guidGenerator; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "format", function() { return format; });
/**
 * @return {string}
 */
var guidGenerator = function guidGenerator() {
  /**
   * @return {string}
   */
  var S4 = function S4() {
    return ((1 + Math.random()) * 0x10000 | 0).toString(16).substring(1);
  };

  return S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4();
};

var format = function format(text) {
  for (var _len = arguments.length, values = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    values[_key - 1] = arguments[_key];
  }

  return text.replace(/{(\d)}/g, function (_, index) {
    return values[Number(index)];
  });
};



/***/ }),

/***/ "./export/js/listscreen.js":
/*!*********************************!*\
  !*** ./export/js/listscreen.js ***!
  \*********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _table_button__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./table/button */ "./export/js/table/button.js");
/* harmony import */ var _table_pageblocker__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./table/pageblocker */ "./export/js/table/pageblocker.js");
/* harmony import */ var _table_exporter__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./table/exporter */ "./export/js/table/exporter.js");
/* harmony import */ var _table_screen_option__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./table/screen-option */ "./export/js/table/screen-option.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }






var nanobus = __webpack_require__(/*! nanobus */ "./node_modules/nanobus/index.js");

document.addEventListener('DOMContentLoaded', function () {
  var button = document.querySelector('.ac-table-button.-export');
  AdminColumns.Export = {
    Exporter: new Export(button)
  };
  var elScreenOption = document.querySelector('#acp_export_show_export_button');

  if (elScreenOption) {
    AdminColumns.Export.ScreenOption = new _table_screen_option__WEBPACK_IMPORTED_MODULE_3__["default"](elScreenOption);
    AdminColumns.Export.ScreenOption.init();
  }
});

var Export = /*#__PURE__*/function () {
  function Export(button) {
    _classCallCheck(this, Export);

    if (!button) {
      return;
    }

    this.events = nanobus();
    this._button = new _table_button__WEBPACK_IMPORTED_MODULE_0__["default"](button);

    this._init();
  }

  _createClass(Export, [{
    key: "_init",
    value: function _init() {
      var _this = this;

      this._button.events.on('export', function () {
        _this["export"]();
      });

      this.events.on('completed', function () {
        _table_pageblocker__WEBPACK_IMPORTED_MODULE_1__["default"].disable();

        _this._button.enable();
      });
    }
  }, {
    key: "export",
    value: function _export() {
      var _this2 = this;

      _table_pageblocker__WEBPACK_IMPORTED_MODULE_1__["default"].enable(); // Initiate Exporter

      var exporter = new _table_exporter__WEBPACK_IMPORTED_MODULE_2__["Exporter"]();
      exporter.events.on(_table_exporter__WEBPACK_IMPORTED_MODULE_2__["EXPORTER_EVENTS"].COMPLETED, function () {
        _this2.events.emit('completed');
      });
      exporter.start();
    }
  }]);

  return Export;
}();

/***/ }),

/***/ "./export/js/table/button.js":
/*!***********************************!*\
  !*** ./export/js/table/button.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ExportButton; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var nanobus = __webpack_require__(/*! nanobus */ "./node_modules/nanobus/index.js");

var ExportButton = /*#__PURE__*/function () {
  function ExportButton(element) {
    _classCallCheck(this, ExportButton);

    this.element = element;
    this.events = nanobus();
    this.initEvents();
  }

  _createClass(ExportButton, [{
    key: "disable",
    value: function disable() {
      this.element.classList.add('disabled');
    }
  }, {
    key: "enable",
    value: function enable() {
      this.element.classList.remove('disabled');
    }
  }, {
    key: "isEnabled",
    value: function isEnabled() {
      return !this.element.classList.contains('disabled');
    }
  }, {
    key: "initEvents",
    value: function initEvents() {
      var _this = this;

      this.element.addEventListener('click', function (e) {
        e.preventDefault();

        if (!_this.isEnabled()) {
          return;
        }

        _this.disable();

        _this.events.emit('export');
      });
    }
  }]);

  return ExportButton;
}();



/***/ }),

/***/ "./export/js/table/exporter.js":
/*!*************************************!*\
  !*** ./export/js/table/exporter.js ***!
  \*************************************/
/*! exports provided: EXPORTER_EVENTS, Exporter */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "EXPORTER_EVENTS", function() { return EXPORTER_EVENTS; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Exporter", function() { return Exporter; });
/* harmony import */ var _helper_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../helper/string */ "./export/js/helper/string.js");
/* harmony import */ var _message__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./message */ "./export/js/table/message.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var nanobus = __webpack_require__(/*! nanobus */ "./node_modules/nanobus/index.js");

var EXPORTER_EVENTS = {
  COMPLETED: 'completed'
};
var Exporter = /*#__PURE__*/function () {
  function Exporter() {
    _classCallCheck(this, Exporter);

    this.events = nanobus();
    this.message = new _message__WEBPACK_IMPORTED_MODULE_1__["default"]();
    this.counter = 0;
    this.hash = Object(_helper_string__WEBPACK_IMPORTED_MODULE_0__["guidGenerator"])();
  }

  _createClass(Exporter, [{
    key: "start",
    value: function start() {
      this.message.render();
      this.counter = 0;
      this.run();
    }
  }, {
    key: "continue",
    value: function _continue() {
      this.counter++;
      this.run();
    }
  }, {
    key: "run",
    value: function run() {
      var _this = this;

      this.call().success(function (result) {
        if (!result.success) {
          var errorMsg = result.data ? result.data : ACP_Export.i18n.export_error;

          _this.message.setError(errorMsg);

          _this.message.makeDismissible();

          _this.events.emit(EXPORTER_EVENTS.COMPLETED);

          return;
        }

        if (result.data['num_rows_processed'] > 0) {
          _this.message.addProcessed(result.data['num_rows_processed']);

          _this["continue"]();
        } else {
          _this.complete(result.data);
        }
      });
    }
  }, {
    key: "complete",
    value: function complete(result) {
      this.events.emit(EXPORTER_EVENTS.COMPLETED);
      this.message.setDownloadUrl(result['download_url']);
      this.message.complete();
      window.location.href = result['download_url'];
    }
  }, {
    key: "call",
    value: function call() {
      var data = {
        _wpnonce: ACP_Export.nonce,
        acp_export_action: 'acp_export_listscreen_export',
        acp_export_hash: this.hash,
        acp_export_counter: this.counter
      };
      return jQuery.ajax({
        method: 'get',
        url: window.location.href,
        data: data
      });
    }
  }]);

  return Exporter;
}();

/***/ }),

/***/ "./export/js/table/message.js":
/*!************************************!*\
  !*** ./export/js/table/message.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Message; });
/* harmony import */ var _helper_string__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../helper/string */ "./export/js/helper/string.js");
/* harmony import */ var _editing_js_helpers_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../editing/js/helpers/elements */ "./editing/js/helpers/elements.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var Message = /*#__PURE__*/function () {
  function Message() {
    _classCallCheck(this, Message);

    this.element = this.createElement();
    this.data = {
      processed: 0,
      total: ACP_Export.total_num_items,
      download_url: '#'
    };
    this.updateData();
  }

  _createClass(Message, [{
    key: "createElement",
    value: function createElement() {
      var element = document.createElement('div');
      element.classList.add('acp-export-notice', 'notice', 'updated');
      element.innerHTML = TplMessage;
      return element;
    }
  }, {
    key: "setError",
    value: function setError(error) {
      this.element.classList.add('error');
      this.element.innerHTML = "<p>".concat(error, "</p>");
      this.makeDismissible();
    }
  }, {
    key: "addProcessed",
    value: function addProcessed(number) {
      this.data.processed += number;
      this.updateData();
    }
  }, {
    key: "setProcessed",
    value: function setProcessed(number) {
      this.data.processed = number;
    }
  }, {
    key: "setTotal",
    value: function setTotal(number) {
      this.data.total = number;
    }
  }, {
    key: "calcPercentage",
    value: function calcPercentage() {
      return Math.ceil(this.data.processed / this.data.total * 100);
    }
  }, {
    key: "render",
    value: function render() {
      var _this = this;

      document.querySelectorAll('.wp-header-end').forEach(function (el) {
        Object(_editing_js_helpers_elements__WEBPACK_IMPORTED_MODULE_1__["insertAfter"])(_this.element, el);
      });
    }
  }, {
    key: "updateData",
    value: function updateData() {
      this.element.querySelector('.num-processed').innerText = this.data.processed;
      this.element.querySelector('.total-num-items').innerText = this.data.total;
      this.element.querySelector('.percentage-processed').innerText = this.calcPercentage();
      this.element.querySelector('[data-download]').setAttribute('href', this.data.download_url);
    }
  }, {
    key: "setDownloadUrl",
    value: function setDownloadUrl(url) {
      this.data.download_url = url;
      this.updateData();
    }
  }, {
    key: "complete",
    value: function complete() {
      this.makeDismissible();
      this.element.querySelector('.exporting').style.display = 'none';
      this.element.querySelector('.export-completed').style.display = 'block';
    }
  }, {
    key: "makeDismissible",
    value: function makeDismissible() {
      var _this2 = this;

      var button = new DismissButton();
      this.element.classList.add('is-dismissible');
      this.element.appendChild(button.get());
      button.get().addEventListener('click', function (e) {
        e.preventDefault();

        _this2.element.remove();
      });
    }
  }]);

  return Message;
}();



var DismissButton = /*#__PURE__*/function () {
  function DismissButton() {
    _classCallCheck(this, DismissButton);

    this.element = this._create();
  }

  _createClass(DismissButton, [{
    key: "_create",
    value: function _create() {
      var element = document.createElement('button');
      element.classList.add('notice-dismiss');
      element.innerHTML = "<span class=\"screen-reader-text\"> ".concat(ACP_Export.i18n.dismiss, "</span>");
      return element;
    }
  }, {
    key: "get",
    value: function get() {
      return this.element;
    }
  }]);

  return DismissButton;
}();

var NumItemsProcessed = '<span class="num-processed"></span>';
var TotalNumItems = "<span class=\"total-num-items\"></span>";
var PercentageProcessed = '<span class="percentage-processed"></span>';
var TplMessage = "\n\t          <div class=\"exporting\">\n\t            <p>\n\t              <span class=\"spinner is-active\"></span>\n\t              ".concat(ACP_Export.i18n.exporting, "\n\t              ").concat(Object(_helper_string__WEBPACK_IMPORTED_MODULE_0__["format"])(ACP_Export.i18n.processed, NumItemsProcessed, TotalNumItems, PercentageProcessed), "\n\t            </p>\n\t          </div>\n\t          <div class=\"export-completed hidden\">\n\t            <p>\n\t              ").concat(Object(_helper_string__WEBPACK_IMPORTED_MODULE_0__["format"])(ACP_Export.i18n.export_completed, TotalNumItems), "\n\t              <a href=\"#\" class=\"button button-secondary\" data-download>").concat(ACP_Export.i18n.download_file, "</a>\n\t            </p>\n\t          </div>\n\t      ");

/***/ }),

/***/ "./export/js/table/pageblocker.js":
/*!****************************************!*\
  !*** ./export/js/table/pageblocker.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return PageBlocker; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var PageBlocker = /*#__PURE__*/function () {
  function PageBlocker() {
    _classCallCheck(this, PageBlocker);
  }

  _createClass(PageBlocker, null, [{
    key: "enable",
    value: function enable() {
      window.onbeforeunload = function () {
        return ACP_Export.i18n.leaving;
      };
    }
  }, {
    key: "disable",
    value: function disable() {
      window.onbeforeunload = function () {};
    }
  }]);

  return PageBlocker;
}();



/***/ }),

/***/ "./export/js/table/screen-option.js":
/*!******************************************!*\
  !*** ./export/js/table/screen-option.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ScreenOption; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var ScreenOption = /*#__PURE__*/function () {
  function ScreenOption(element) {
    _classCallCheck(this, ScreenOption);

    this.element = element;
  }

  _createClass(ScreenOption, [{
    key: "init",
    value: function init() {
      var _this = this;

      this.element.querySelector('input').addEventListener('click', function () {
        _this.isActive() ? _this.activate() : _this.deactivate();
      });
    }
  }, {
    key: "activate",
    value: function activate() {
      this.element.querySelector('input').checked = true;
      this.persist();
      document.body.classList.remove('ac-hide-export-button');
      jQuery('.ac-table-actions-buttons').trigger('update');
    }
  }, {
    key: "deactivate",
    value: function deactivate() {
      this.element.querySelector('input').checked = false;
      this.persist();
      document.body.classList.add('ac-hide-export-button');
      jQuery('.ac-table-actions-buttons').trigger('update');
    }
  }, {
    key: "isActive",
    value: function isActive() {
      return this.element.querySelector('input').checked;
    }
  }, {
    key: "persist",
    value: function persist() {
      return jQuery.post(ajaxurl, {
        action: 'acp_export_show_export_button',
        value: this.isActive(),
        layout: AC.layout,
        list_screen: AC.list_screen,
        _ajax_nonce: AC.ajax_nonce
      });
    }
  }]);

  return ScreenOption;
}();



/***/ }),

/***/ "./node_modules/nanoassert/index.js":
/*!******************************************!*\
  !*** ./node_modules/nanoassert/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

assert.notEqual = notEqual
assert.notOk = notOk
assert.equal = equal
assert.ok = assert

module.exports = assert

function equal (a, b, m) {
  assert(a == b, m) // eslint-disable-line eqeqeq
}

function notEqual (a, b, m) {
  assert(a != b, m) // eslint-disable-line eqeqeq
}

function notOk (t, m) {
  assert(!t, m)
}

function assert (t, m) {
  if (!t) throw new Error(m || 'AssertionError')
}


/***/ }),

/***/ "./node_modules/nanobus/index.js":
/*!***************************************!*\
  !*** ./node_modules/nanobus/index.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var splice = __webpack_require__(/*! remove-array-items */ "./node_modules/remove-array-items/index.js")
var nanotiming = __webpack_require__(/*! nanotiming */ "./node_modules/nanotiming/browser.js")
var assert = __webpack_require__(/*! assert */ "./node_modules/nanoassert/index.js")

module.exports = Nanobus

function Nanobus (name) {
  if (!(this instanceof Nanobus)) return new Nanobus(name)

  this._name = name || 'nanobus'
  this._starListeners = []
  this._listeners = {}
}

Nanobus.prototype.emit = function (eventName) {
  assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.emit: eventName should be type string or symbol')

  var data = []
  for (var i = 1, len = arguments.length; i < len; i++) {
    data.push(arguments[i])
  }

  var emitTiming = nanotiming(this._name + "('" + eventName.toString() + "')")
  var listeners = this._listeners[eventName]
  if (listeners && listeners.length > 0) {
    this._emit(this._listeners[eventName], data)
  }

  if (this._starListeners.length > 0) {
    this._emit(this._starListeners, eventName, data, emitTiming.uuid)
  }
  emitTiming()

  return this
}

Nanobus.prototype.on = Nanobus.prototype.addListener = function (eventName, listener) {
  assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.on: eventName should be type string or symbol')
  assert.equal(typeof listener, 'function', 'nanobus.on: listener should be type function')

  if (eventName === '*') {
    this._starListeners.push(listener)
  } else {
    if (!this._listeners[eventName]) this._listeners[eventName] = []
    this._listeners[eventName].push(listener)
  }
  return this
}

Nanobus.prototype.prependListener = function (eventName, listener) {
  assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.prependListener: eventName should be type string or symbol')
  assert.equal(typeof listener, 'function', 'nanobus.prependListener: listener should be type function')

  if (eventName === '*') {
    this._starListeners.unshift(listener)
  } else {
    if (!this._listeners[eventName]) this._listeners[eventName] = []
    this._listeners[eventName].unshift(listener)
  }
  return this
}

Nanobus.prototype.once = function (eventName, listener) {
  assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.once: eventName should be type string or symbol')
  assert.equal(typeof listener, 'function', 'nanobus.once: listener should be type function')

  var self = this
  this.on(eventName, once)
  function once () {
    listener.apply(self, arguments)
    self.removeListener(eventName, once)
  }
  return this
}

Nanobus.prototype.prependOnceListener = function (eventName, listener) {
  assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.prependOnceListener: eventName should be type string or symbol')
  assert.equal(typeof listener, 'function', 'nanobus.prependOnceListener: listener should be type function')

  var self = this
  this.prependListener(eventName, once)
  function once () {
    listener.apply(self, arguments)
    self.removeListener(eventName, once)
  }
  return this
}

Nanobus.prototype.removeListener = function (eventName, listener) {
  assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.removeListener: eventName should be type string or symbol')
  assert.equal(typeof listener, 'function', 'nanobus.removeListener: listener should be type function')

  if (eventName === '*') {
    this._starListeners = this._starListeners.slice()
    return remove(this._starListeners, listener)
  } else {
    if (typeof this._listeners[eventName] !== 'undefined') {
      this._listeners[eventName] = this._listeners[eventName].slice()
    }

    return remove(this._listeners[eventName], listener)
  }

  function remove (arr, listener) {
    if (!arr) return
    var index = arr.indexOf(listener)
    if (index !== -1) {
      splice(arr, index, 1)
      return true
    }
  }
}

Nanobus.prototype.removeAllListeners = function (eventName) {
  if (eventName) {
    if (eventName === '*') {
      this._starListeners = []
    } else {
      this._listeners[eventName] = []
    }
  } else {
    this._starListeners = []
    this._listeners = {}
  }
  return this
}

Nanobus.prototype.listeners = function (eventName) {
  var listeners = eventName !== '*'
    ? this._listeners[eventName]
    : this._starListeners

  var ret = []
  if (listeners) {
    var ilength = listeners.length
    for (var i = 0; i < ilength; i++) ret.push(listeners[i])
  }
  return ret
}

Nanobus.prototype._emit = function (arr, eventName, data, uuid) {
  if (typeof arr === 'undefined') return
  if (arr.length === 0) return
  if (data === undefined) {
    data = eventName
    eventName = null
  }

  if (eventName) {
    if (uuid !== undefined) {
      data = [eventName].concat(data, uuid)
    } else {
      data = [eventName].concat(data)
    }
  }

  var length = arr.length
  for (var i = 0; i < length; i++) {
    var listener = arr[i]
    listener.apply(listener, data)
  }
}


/***/ }),

/***/ "./node_modules/nanoscheduler/index.js":
/*!*********************************************!*\
  !*** ./node_modules/nanoscheduler/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var assert = __webpack_require__(/*! assert */ "./node_modules/nanoassert/index.js")

var hasWindow = typeof window !== 'undefined'

function createScheduler () {
  var scheduler
  if (hasWindow) {
    if (!window._nanoScheduler) window._nanoScheduler = new NanoScheduler(true)
    scheduler = window._nanoScheduler
  } else {
    scheduler = new NanoScheduler()
  }
  return scheduler
}

function NanoScheduler (hasWindow) {
  this.hasWindow = hasWindow
  this.hasIdle = this.hasWindow && window.requestIdleCallback
  this.method = this.hasIdle ? window.requestIdleCallback.bind(window) : this.setTimeout
  this.scheduled = false
  this.queue = []
}

NanoScheduler.prototype.push = function (cb) {
  assert.equal(typeof cb, 'function', 'nanoscheduler.push: cb should be type function')

  this.queue.push(cb)
  this.schedule()
}

NanoScheduler.prototype.schedule = function () {
  if (this.scheduled) return

  this.scheduled = true
  var self = this
  this.method(function (idleDeadline) {
    var cb
    while (self.queue.length && idleDeadline.timeRemaining() > 0) {
      cb = self.queue.shift()
      cb(idleDeadline)
    }
    self.scheduled = false
    if (self.queue.length) self.schedule()
  })
}

NanoScheduler.prototype.setTimeout = function (cb) {
  setTimeout(cb, 0, {
    timeRemaining: function () {
      return 1
    }
  })
}

module.exports = createScheduler


/***/ }),

/***/ "./node_modules/nanotiming/browser.js":
/*!********************************************!*\
  !*** ./node_modules/nanotiming/browser.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var scheduler = __webpack_require__(/*! nanoscheduler */ "./node_modules/nanoscheduler/index.js")()
var assert = __webpack_require__(/*! assert */ "./node_modules/nanoassert/index.js")

var perf
nanotiming.disabled = true
try {
  perf = window.performance
  nanotiming.disabled = window.localStorage.DISABLE_NANOTIMING === 'true' || !perf.mark
} catch (e) { }

module.exports = nanotiming

function nanotiming (name) {
  assert.equal(typeof name, 'string', 'nanotiming: name should be type string')

  if (nanotiming.disabled) return noop

  var uuid = (perf.now() * 10000).toFixed() % Number.MAX_SAFE_INTEGER
  var startName = 'start-' + uuid + '-' + name
  perf.mark(startName)

  function end (cb) {
    var endName = 'end-' + uuid + '-' + name
    perf.mark(endName)

    scheduler.push(function () {
      var err = null
      try {
        var measureName = name + ' [' + uuid + ']'
        perf.measure(measureName, startName, endName)
        perf.clearMarks(startName)
        perf.clearMarks(endName)
      } catch (e) { err = e }
      if (cb) cb(err, name)
    })
  }

  end.uuid = uuid
  return end
}

function noop (cb) {
  if (cb) {
    scheduler.push(function () {
      cb(new Error('nanotiming: performance API unavailable'))
    })
  }
}


/***/ }),

/***/ "./node_modules/remove-array-items/index.js":
/*!**************************************************!*\
  !*** ./node_modules/remove-array-items/index.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


/**
 * Remove a range of items from an array
 *
 * @function removeItems
 * @param {Array<*>} arr The target array
 * @param {number} startIdx The index to begin removing from (inclusive)
 * @param {number} removeCount How many items to remove
 */
module.exports = function removeItems (arr, startIdx, removeCount) {
  var i, length = arr.length

  if (startIdx >= length || removeCount === 0) {
    return
  }

  removeCount = (startIdx + removeCount > length ? length - startIdx : removeCount)

  var len = length - removeCount

  for (i = startIdx; i < len; ++i) {
    arr[i] = arr[i + removeCount]
  }

  arr.length = len
}


/***/ })

/******/ });
//# sourceMappingURL=listscreen.js.map