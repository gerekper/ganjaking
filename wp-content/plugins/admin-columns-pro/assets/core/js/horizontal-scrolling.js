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
/******/ 	return __webpack_require__(__webpack_require__.s = "./core/js/horizontal-scrolling.ts");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./core/js/horizontal-scrolling.ts":
/*!*****************************************!*\
  !*** ./core/js/horizontal-scrolling.ts ***!
  \*****************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _horizontal_scrolling_main__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./horizontal-scrolling/main */ "./core/js/horizontal-scrolling/main.ts");

jQuery(document).ready(function () {
  var table = document.querySelector('.wp-list-table');

  if (table) {
    var MainModule_1 = new _horizontal_scrolling_main__WEBPACK_IMPORTED_MODULE_0__["default"](table);
    jQuery('#acp_overflow_list_screen_table-yes').on('click', function () {
      if (jQuery(this).is(':checked')) {
        MainModule_1.enable();
        MainModule_1.store();
      } else {
        MainModule_1.disable();
        MainModule_1.store();
      }
    });
    AdminColumns.HorizontalScrolling = MainModule_1;
  }
});

/***/ }),

/***/ "./core/js/horizontal-scrolling/indicator.ts":
/*!***************************************************!*\
  !*** ./core/js/horizontal-scrolling/indicator.ts ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var ScrollIndicator =
/** @class */
function () {
  function ScrollIndicator(table, wrapper) {
    this.table = table;
    this.wrapper = wrapper;
    this.initialX = 0;
    this.xOffset = 0;
    this.maxX = 0;
    this.tempX = 0;
    this.active = false;
    this.create();
    this.updateDraggerWidth();
    this.updateWidth();
  }

  ScrollIndicator.prototype.disable = function () {
    this.element.style.display = 'none';
  };

  ScrollIndicator.prototype.enable = function () {
    this.element.style.display = 'block';
  };

  ScrollIndicator.prototype.hide = function () {
    this.element.classList.add('-hidden');
  };

  ScrollIndicator.prototype.show = function () {
    this.element.classList.remove('-hidden');
  };

  ScrollIndicator.prototype.create = function () {
    var _this = this;

    var element = document.createElement('div');
    var dragger = document.createElement('div');
    element.classList.add('acp-scrolling-indicator');
    element.classList.add('-start');
    element.classList.add('-hidden');
    dragger.classList.add('acp-scrolling-indicator__dragger');
    element.appendChild(dragger);
    document.body.appendChild(element);
    this.element = element;
    this.dragger = dragger;
    setTimeout(function () {
      _this.maxX = element.clientWidth - dragger.offsetWidth;
    });
    this.initEvents();
    this.disable();
    this.updateYPosition();
  };

  ScrollIndicator.prototype.updateWidth = function () {
    this.element.style.width = this.table.offsetWidth + 'px';
  };

  ScrollIndicator.prototype.updateYPosition = function () {
    var tableBottom = this.table.getBoundingClientRect().bottom;
    var bottom = window.innerHeight - tableBottom;

    if (bottom > this.element.offsetHeight) {
      this.element.style.top = window.innerHeight - bottom + 'px';
    } else {
      this.element.style.top = 'inherit';
    }
  };

  ScrollIndicator.prototype.updateDraggerWidth = function () {
    var percentage = Math.round(this.table.offsetWidth / this.table.scrollWidth * 100);

    if (percentage === 100) {
      this.element.classList.add('-fits');
    } else {
      this.element.classList.remove('-fits');
    }

    this.dragger.style.width = percentage + "%";
    this.maxX = this.element.clientWidth - this.dragger.offsetWidth;
  };

  ScrollIndicator.prototype.initEvents = function () {
    var _this = this;

    document.addEventListener('scroll', function () {
      _this.updateYPosition();

      _this.updateWidth();
    });
    window.addEventListener("touchmove", function (e) {
      return _this.drag(e);
    }, false);
    window.addEventListener("mousemove", function (e) {
      return _this.drag(e);
    }, false);
    window.addEventListener('mouseup', function (e) {
      return _this.dragEnd();
    });
    this.dragger.addEventListener("touchstart", function (e) {
      return _this.dragStart(e);
    });
    this.dragger.addEventListener("touchend", function () {
      return _this.dragEnd();
    });
    this.dragger.addEventListener("mousedown", function (e) {
      return _this.dragStart(e);
    });
    this.dragger.addEventListener("mouseup", function () {
      return _this.dragEnd();
    });
    this.table.addEventListener('scroll', function () {
      _this.updateIndicator();
    });
    window.addEventListener('resize', function () {
      clearTimeout(_this.timeout);
      _this.timeout = setTimeout(function () {
        _this.updateWidth();

        _this.updateDraggerWidth();
      }, 100);
    }); // Screen Option fix

    onElementHeightChange(document.getElementById('wpbody-content'), function () {
      _this.refreshPosition(300);
    });
  };

  ScrollIndicator.prototype.refreshPosition = function (delay) {
    var _this = this;

    if (delay === void 0) {
      delay = 100;
    }

    setTimeout(function () {
      _this.show();

      _this.updateYPosition();
    }, delay);
  };

  ScrollIndicator.prototype.updateIndicator = function () {
    if (this.active) {
      return;
    }

    var percentage = this.wrapper.getOffsetPercentage() / 100;
    var offset = Math.round(this.maxX * percentage);

    if (offset > this.maxX) {
      offset = this.maxX;
    }

    this.xOffset = offset;
    this.setTranslate(offset);
  };

  ScrollIndicator.prototype.getCurrentOffset = function () {
    return this.xOffset;
  };

  ScrollIndicator.prototype.dragStart = function (e) {
    this.initialX = DragHelper.getX(e);
    this.active = true;
  };

  ScrollIndicator.prototype.dragEnd = function () {
    if (!this.active) {
      return;
    }

    this.initialX = 0;
    this.active = false;
    this.xOffset = this.tempX;
    AdminColumns.HorizontalScrolling.wrapper.setOffset(this.percentage);
  };

  ScrollIndicator.prototype.drag = function (e) {
    if (this.active) {
      e.preventDefault();
      var movementX = DragHelper.getX(e) - this.initialX;
      var newXpos = this.getCurrentOffset() + movementX;
      if (newXpos < 0) newXpos = 0;
      if (newXpos > this.maxX) newXpos = this.maxX;
      this.tempX = newXpos;
      this.percentage = newXpos / this.maxX * 100;
      this.setTranslate(newXpos);
      AdminColumns.HorizontalScrolling.wrapper.setOffset(this.percentage);
    }
  };

  ScrollIndicator.prototype.setTranslate = function (xPos) {
    if (xPos === 0) {
      this.element.classList.add('-start');
    } else {
      this.element.classList.remove('-start');
    }

    this.dragger.style.left = xPos + "px";
  };

  return ScrollIndicator;
}();

/* harmony default export */ __webpack_exports__["default"] = (ScrollIndicator);

var onElementHeightChange = function onElementHeightChange(element, callback) {
  var lastHeight = element.clientHeight,
      newHeight;

  (function run() {
    newHeight = element.clientHeight;

    if (lastHeight !== newHeight) {
      callback(newHeight);
    }

    lastHeight = newHeight;
    var timer = parseInt(element.dataset.onElementHeightChangeTimer);

    if (timer) {
      clearTimeout(parseInt(element.dataset.onElementHeightChangeTimer));
    }

    element.dataset.onElementHeightChangeTimer = setTimeout(run, 200);
  })();
};

var DragHelper =
/** @class */
function () {
  function DragHelper() {}

  DragHelper.getX = function (e) {
    if (e.type === "touchmove") {
      return e.touches[0].clientX;
    } else {
      return e.clientX;
    }
  };

  DragHelper.getY = function (e) {
    if (e.type === "touchmove") {
      return e.touches[0].clientY;
    } else {
      return e.clientY;
    }
  };

  return DragHelper;
}();

/***/ }),

/***/ "./core/js/horizontal-scrolling/main.ts":
/*!**********************************************!*\
  !*** ./core/js/horizontal-scrolling/main.ts ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _indicator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./indicator */ "./core/js/horizontal-scrolling/indicator.ts");
/* harmony import */ var _table_wrapper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./table-wrapper */ "./core/js/horizontal-scrolling/table-wrapper.ts");
/* harmony import */ var _scroll_position_preference__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./scroll-position-preference */ "./core/js/horizontal-scrolling/scroll-position-preference.ts");



var CONST = {
  BODY_CLASS: 'acp-overflow-table'
};

var HorizontalScrolling =
/** @class */
function () {
  function HorizontalScrolling(table) {
    this.enabled = document.body.classList.contains(CONST.BODY_CLASS);
    this.table = table;
    this.wrapper = new _table_wrapper__WEBPACK_IMPORTED_MODULE_1__["default"](this.table);
    this.indicator = new _indicator__WEBPACK_IMPORTED_MODULE_0__["default"](this.table, this.wrapper);
    this.scrollPreference = new _scroll_position_preference__WEBPACK_IMPORTED_MODULE_2__["default"](this);

    if (this.isEnabled()) {
      this.wrapper.wrap();
      this.wrapper.enable();

      if (ACP_Horizontal_Scrolling.hasOwnProperty('indicator_enabled') && ACP_Horizontal_Scrolling.indicator_enabled) {
        this.indicator.enable();
      }
    }
  }

  HorizontalScrolling.prototype.isEnabled = function () {
    return this.enabled;
  };

  HorizontalScrolling.prototype.enable = function () {
    document.body.classList.add(CONST.BODY_CLASS);
    this.enabled = true;
    this.wrapper.wrap();
    this.wrapper.enable();
    this.wrapper.checkWrapperState();
    this.indicator.enable();
  };

  HorizontalScrolling.prototype.disable = function () {
    this.wrapper.disable();
    this.enabled = false;
    this.indicator.disable();
    document.body.classList.remove(CONST.BODY_CLASS);
  };

  HorizontalScrolling.prototype.getScrollLeft = function () {
    return this.table.scrollLeft;
  };

  HorizontalScrolling.prototype.setScrollLeft = function (offset) {
    this.table.scrollLeft = offset;
  };

  HorizontalScrolling.prototype.store = function () {
    jQuery.post(ajaxurl, {
      action: 'acp_update_table_option_overflow',
      value: this.isEnabled(),
      layout: AC.layout,
      list_screen: AC.list_screen,
      _ajax_nonce: AC.ajax_nonce
    });
  };

  return HorizontalScrolling;
}();

/* harmony default export */ __webpack_exports__["default"] = (HorizontalScrolling);

/***/ }),

/***/ "./core/js/horizontal-scrolling/scroll-position-preference.ts":
/*!********************************************************************!*\
  !*** ./core/js/horizontal-scrolling/scroll-position-preference.ts ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var js_cookie__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! js-cookie */ "./node_modules/js-cookie/src/js.cookie.js");
/* harmony import */ var js_cookie__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(js_cookie__WEBPACK_IMPORTED_MODULE_0__);
// @ts-ignore


var ScrollPositionPreference =
/** @class */
function () {
  function ScrollPositionPreference(HorizontalScrolling) {
    this.HorizontalScrolling = HorizontalScrolling;
    this.timeout = 0;
    this.scrolling = false;
    this.offset = 0;
    this.init();
    this.initPosition();
  }

  ScrollPositionPreference.prototype.init = function () {
    var _this = this;

    window.addEventListener('beforeunload', function () {
      return _this.storeCurrentPosition();
    });
  };

  ScrollPositionPreference.prototype.getCacheKey = function () {
    return "hsoffset_" + AC.list_screen + "_" + AC.layout;
  };

  ScrollPositionPreference.prototype.storeCurrentPosition = function () {
    var inFiveSeconds = new Date(new Date().getTime() + 5 * 1000);
    js_cookie__WEBPACK_IMPORTED_MODULE_0___default.a.set(this.getCacheKey(), this.HorizontalScrolling.getScrollLeft(), {
      expires: inFiveSeconds
    });
  };

  ScrollPositionPreference.prototype.initPosition = function () {
    var _this = this;

    var scrollLeft = js_cookie__WEBPACK_IMPORTED_MODULE_0___default.a.get(this.getCacheKey());

    if (scrollLeft) {
      setTimeout(function () {
        return _this.HorizontalScrolling.setScrollLeft(scrollLeft);
      }, 100);
    }
  };

  return ScrollPositionPreference;
}();

/* harmony default export */ __webpack_exports__["default"] = (ScrollPositionPreference);

/***/ }),

/***/ "./core/js/horizontal-scrolling/table-wrapper.ts":
/*!*******************************************************!*\
  !*** ./core/js/horizontal-scrolling/table-wrapper.ts ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var CONST = {
  WRAPPER_CLASS: 'acp-hts-wrapper',
  OVERFLOW_CLASS: '-overflow',
  MORE_CLASS: '-more',
  LESS_CLASS: '-less'
};

var TableWrapper =
/** @class */
function () {
  function TableWrapper(table) {
    this.enabled = false;
    this.table = table;
    this.wrapper = null;
    this.initEvents();
  }

  TableWrapper.prototype.enable = function () {
    this.enabled = true;
    this.checkWrapperState();
  };

  TableWrapper.prototype.disable = function () {
    this.enabled = false;
  };

  TableWrapper.prototype.initEvents = function () {
    var _this = this;

    this.table.addEventListener('scroll', function () {
      _this.checkWrapperState();
    });
    window.addEventListener('resize', function () {
      clearTimeout(_this.timeout);
      _this.timeout = setTimeout(function () {
        _this.checkWrapperState();
      }, 100);
    });
  };

  TableWrapper.prototype.wrap = function () {
    if (this.table.parentElement.classList.contains(CONST.WRAPPER_CLASS)) {
      return;
    }

    this.wrapper = document.createElement('div');
    this.wrapper.classList.add(CONST.WRAPPER_CLASS);
    this.table.parentNode.insertBefore(this.wrapper, this.table);
    this.wrapper.appendChild(this.table);
  };

  TableWrapper.prototype.setOffset = function (percentage) {
    var scrollableAreaWidth = this.table.scrollWidth - this.table.offsetWidth;
    var offsetX = scrollableAreaWidth * (percentage / 100);
    this.table.scrollLeft = Math.round(offsetX);
  };

  TableWrapper.prototype.getOffsetPercentage = function () {
    var scrollableAreaWidth = this.table.scrollWidth - this.table.offsetWidth;
    var offset = this.table.scrollLeft;
    var percentage = offset / scrollableAreaWidth * 100;
    return Math.round(percentage);
  };

  TableWrapper.prototype.checkWrapperState = function () {
    if (!this.enabled) {
      return;
    }

    var total = this.table.scrollWidth;
    var width = this.table.offsetWidth;
    var scrollLeft = this.table.scrollLeft;
    this.wrapper.classList.remove(CONST.LESS_CLASS, CONST.MORE_CLASS, CONST.OVERFLOW_CLASS);

    if (total > width) {
      this.wrapper.classList.add(CONST.OVERFLOW_CLASS);

      if (scrollLeft + width + 10 < total) {
        this.wrapper.classList.add(CONST.MORE_CLASS);
      }

      if (scrollLeft > 0) {
        this.wrapper.classList.add(CONST.LESS_CLASS);
      }
    }
  };

  return TableWrapper;
}();

/* harmony default export */ __webpack_exports__["default"] = (TableWrapper);

/***/ }),

/***/ "./node_modules/js-cookie/src/js.cookie.js":
/*!*************************************************!*\
  !*** ./node_modules/js-cookie/src/js.cookie.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
 * JavaScript Cookie v2.2.1
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
;(function (factory) {
	var registeredInModuleLoader;
	if (true) {
		!(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) :
				__WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
		registeredInModuleLoader = true;
	}
	if (true) {
		module.exports = factory();
		registeredInModuleLoader = true;
	}
	if (!registeredInModuleLoader) {
		var OldCookies = window.Cookies;
		var api = window.Cookies = factory();
		api.noConflict = function () {
			window.Cookies = OldCookies;
			return api;
		};
	}
}(function () {
	function extend () {
		var i = 0;
		var result = {};
		for (; i < arguments.length; i++) {
			var attributes = arguments[ i ];
			for (var key in attributes) {
				result[key] = attributes[key];
			}
		}
		return result;
	}

	function decode (s) {
		return s.replace(/(%[0-9A-Z]{2})+/g, decodeURIComponent);
	}

	function init (converter) {
		function api() {}

		function set (key, value, attributes) {
			if (typeof document === 'undefined') {
				return;
			}

			attributes = extend({
				path: '/'
			}, api.defaults, attributes);

			if (typeof attributes.expires === 'number') {
				attributes.expires = new Date(new Date() * 1 + attributes.expires * 864e+5);
			}

			// We're using "expires" because "max-age" is not supported by IE
			attributes.expires = attributes.expires ? attributes.expires.toUTCString() : '';

			try {
				var result = JSON.stringify(value);
				if (/^[\{\[]/.test(result)) {
					value = result;
				}
			} catch (e) {}

			value = converter.write ?
				converter.write(value, key) :
				encodeURIComponent(String(value))
					.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);

			key = encodeURIComponent(String(key))
				.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent)
				.replace(/[\(\)]/g, escape);

			var stringifiedAttributes = '';
			for (var attributeName in attributes) {
				if (!attributes[attributeName]) {
					continue;
				}
				stringifiedAttributes += '; ' + attributeName;
				if (attributes[attributeName] === true) {
					continue;
				}

				// Considers RFC 6265 section 5.2:
				// ...
				// 3.  If the remaining unparsed-attributes contains a %x3B (";")
				//     character:
				// Consume the characters of the unparsed-attributes up to,
				// not including, the first %x3B (";") character.
				// ...
				stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
			}

			return (document.cookie = key + '=' + value + stringifiedAttributes);
		}

		function get (key, json) {
			if (typeof document === 'undefined') {
				return;
			}

			var jar = {};
			// To prevent the for loop in the first place assign an empty array
			// in case there are no cookies at all.
			var cookies = document.cookie ? document.cookie.split('; ') : [];
			var i = 0;

			for (; i < cookies.length; i++) {
				var parts = cookies[i].split('=');
				var cookie = parts.slice(1).join('=');

				if (!json && cookie.charAt(0) === '"') {
					cookie = cookie.slice(1, -1);
				}

				try {
					var name = decode(parts[0]);
					cookie = (converter.read || converter)(cookie, name) ||
						decode(cookie);

					if (json) {
						try {
							cookie = JSON.parse(cookie);
						} catch (e) {}
					}

					jar[name] = cookie;

					if (key === name) {
						break;
					}
				} catch (e) {}
			}

			return key ? jar[key] : jar;
		}

		api.set = set;
		api.get = function (key) {
			return get(key, false /* read as raw */);
		};
		api.getJSON = function (key) {
			return get(key, true /* read as json */);
		};
		api.remove = function (key, attributes) {
			set(key, '', extend(attributes, {
				expires: -1
			}));
		};

		api.defaults = {};

		api.withConverter = init;

		return api;
	}

	return init(function () {});
}));


/***/ })

/******/ });
//# sourceMappingURL=horizontal-scrolling.js.map