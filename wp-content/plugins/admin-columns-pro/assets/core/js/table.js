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
/******/ 	return __webpack_require__(__webpack_require__.s = "./core/js/table.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./core/js/table.js":
/*!**************************!*\
  !*** ./core/js/table.js ***!
  \**************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _table_refresher__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./table/refresher */ "./core/js/table/refresher.js");
/* harmony import */ var _table_layout_switcher__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./table/layout-switcher */ "./core/js/table/layout-switcher.js");



__webpack_require__(/*! es6-object-assign */ "./node_modules/es6-object-assign/index.js").polyfill();

document.addEventListener('AC_Table_Ready', function (e) {
  var table = e.detail.table;
  table.Refresher = new _table_refresher__WEBPACK_IMPORTED_MODULE_0__["default"](table);
  var switcher = document.querySelector('.layout-switcher');

  if (switcher) {
    var layout_switcher = new _table_layout_switcher__WEBPACK_IMPORTED_MODULE_1__["default"](switcher);
    layout_switcher.place();
  }
});

/***/ }),

/***/ "./core/js/table/layout-switcher.js":
/*!******************************************!*\
  !*** ./core/js/table/layout-switcher.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var LayoutSwitcher =
/** @class */
function () {
  function LayoutSwitcher(element) {
    this.element = element;
  }

  LayoutSwitcher.prototype.place = function () {
    var _this = this;

    ['.wrap > a.page-title-action', '.wrap h1', '.wrap h2', '.wrap div'].some(function (selector) {
      return _this.tryPlacement(selector);
    });
  };

  LayoutSwitcher.prototype.tryPlacement = function (selector) {
    var predecessor = document.querySelector(selector);

    if (!predecessor) {
      return false;
    }

    insertAfter(this.element, predecessor);
    this.element.classList.add('-moved');
    return true;
  };

  return LayoutSwitcher;
}();

/* harmony default export */ __webpack_exports__["default"] = (LayoutSwitcher);

function insertAfter(newNode, referenceNode) {
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

/***/ }),

/***/ "./core/js/table/refresher.js":
/*!************************************!*\
  !*** ./core/js/table/refresher.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Refresher =
/** @class */
function () {
  function Refresher(table) {
    this.table = table;
  }

  Refresher.prototype.getRows = function (ids) {
    return jQuery.ajax({
      url: window.location.href,
      method: 'post',
      data: {
        ac_action: 'get_table_rows',
        ac_ids: ids,
        _ajax_nonce: AC.ajax_nonce
      }
    });
  };

  Refresher.prototype.updateRow = function (id) {
    var _this = this;

    var ajax = this.getRows([id]);
    ajax.done(function (response) {
      if (response.success) {
        _this.populateRow(id, response.data.table_rows[id]);
      }
    });
  };

  Refresher.prototype.populateRow = function (id, rowdata) {
    // Only change if value has changed
    var element = document.createElement('table');
    element.insertAdjacentHTML('beforeend', rowdata);
    this.table.Cells.getByID(id).forEach(function (cell) {
      var td = element.querySelector("td.column-" + cell.getName());
      cell.setValue(td.innerHTML);
    });
  };

  return Refresher;
}();

/* harmony default export */ __webpack_exports__["default"] = (Refresher);

/***/ }),

/***/ "./node_modules/es6-object-assign/index.js":
/*!*************************************************!*\
  !*** ./node_modules/es6-object-assign/index.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/**
 * Code refactored from Mozilla Developer Network:
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
 */



function assign(target, firstSource) {
  if (target === undefined || target === null) {
    throw new TypeError('Cannot convert first argument to object');
  }

  var to = Object(target);
  for (var i = 1; i < arguments.length; i++) {
    var nextSource = arguments[i];
    if (nextSource === undefined || nextSource === null) {
      continue;
    }

    var keysArray = Object.keys(Object(nextSource));
    for (var nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex++) {
      var nextKey = keysArray[nextIndex];
      var desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
      if (desc !== undefined && desc.enumerable) {
        to[nextKey] = nextSource[nextKey];
      }
    }
  }
  return to;
}

function polyfill() {
  if (!Object.assign) {
    Object.defineProperty(Object, 'assign', {
      enumerable: false,
      configurable: true,
      writable: true,
      value: assign
    });
  }
}

module.exports = {
  assign: assign,
  polyfill: polyfill
};


/***/ })

/******/ });
//# sourceMappingURL=table.js.map