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
/******/ 	return __webpack_require__(__webpack_require__.s = "./editing/js/table.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./editing/js/editable/abstract.js":
/*!*****************************************!*\
  !*** ./editing/js/editable/abstract.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return AbstractEditable; });
/* harmony import */ var _templates_editable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../templates/editable */ "./editing/js/templates/editable.js");
/* harmony import */ var _modules_event_emitter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../modules/event-emitter */ "./editing/js/modules/event-emitter.js");
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var Defaults = {
  title: false,
  value: null,
  showbuttons: true,
  type: 'default',
  mode: 'inline'
};

var AbstractEditable = /*#__PURE__*/function () {
  function AbstractEditable(args) {
    _classCallCheck(this, AbstractEditable);

    this._template = new _templates_editable__WEBPACK_IMPORTED_MODULE_0__["default"]();
    this._element = null;
    this._args = this._getArgs(args);
    this._isInit = false;
    this._isDisabled = false;
    this.Events = new _modules_event_emitter__WEBPACK_IMPORTED_MODULE_1__["default"]();
    new GlobalEvents();
  }
  /**
   * @returns {AbstractEditable}
   */


  _createClass(AbstractEditable, [{
    key: "close",
    value: function close() {
      this.removeElement();
      Window.ACEditable = null;
      this.Events.emit('close', this);
      return this;
    }
  }, {
    key: "get",
    value: function get() {
      this._element = this.createElement();
      this.beforeRender();
      this.render();
      this.valueToInput(this._args.value);
      this.initEvents();
      return this._element;
    }
  }, {
    key: "getEditableTemplate",
    value: function getEditableTemplate() {
      return this._template;
    }
  }, {
    key: "disable",
    value: function disable() {
      this._element.querySelectorAll('input, select, textarea, button').forEach(function (input) {
        input.disabled = true;
      });

      this._isDisabled = true;
      return this;
    }
  }, {
    key: "enable",
    value: function enable() {
      this._element.querySelectorAll('input, select, textarea, button').forEach(function (input) {
        input.disabled = false;
      });

      this._isDisabled = false;
      return this;
    }
  }, {
    key: "beforeRender",
    value: function beforeRender() {
      this._template.setTemplate(this.getTemplate());

      this._template.addClass('-' + this._args.type);

      this._template.setError('');

      if (!this._args.hasOwnProperty('showbuttons') && !this._args.showbuttons) {
        this._template.showButtons(false);
      }
    }
  }, {
    key: "save",
    value: function save() {
      if (this._isDisabled) {
        return;
      }

      var value = this.getValue();

      if (!this.validate(value)) {
        return;
      }

      this.Events.emit('save', this);
    }
  }, {
    key: "initEvents",
    value: function initEvents() {
      var _this = this;

      if (this._isInit) {
        return;
      }

      this.getElement().addEventListener('click', function (e) {
        if (_this.preventStopPropagation(e)) {
          return;
        }

        e.stopPropagation();
      });
      this.getElement().querySelector('button[data-submit]').addEventListener('click', function (e) {
        e.preventDefault();

        _this.save();
      });
      this.getElement().querySelector('button[data-cancel]').addEventListener('click', function (e) {
        e.preventDefault();

        _this.close();
      });
      this.getElement().querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();

        _this.save();
      });
      this._isInit = true;
    }
  }, {
    key: "preventStopPropagation",
    value: function preventStopPropagation(e, type) {
      return false;
    }
  }, {
    key: "removeElement",
    value: function removeElement() {
      if (this._element) {
        this._element.remove();
      }
    }
  }, {
    key: "toggleLoading",
    value: function toggleLoading() {
      var loading = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

      if (!this.getElement()) {
        return;
      }

      if (loading) {
        this.getElement().classList.add('-loading');
      } else {
        this.getElement().classList.remove('-loading');
      }
    }
  }, {
    key: "createElement",
    value: function createElement() {
      return this._template.el;
    }
  }, {
    key: "setValue",
    value: function setValue(value) {
      this._args.value = value;
      return this;
    }
  }, {
    key: "setObjectId",
    value: function setObjectId(objectId) {
      this._args.object_id = objectId;
      return this;
    }
  }, {
    key: "getObjectId",
    value: function getObjectId() {
      return this._args.hasOwnProperty('object_id') ? this._args.object_id : false;
    }
  }, {
    key: "setError",
    value: function setError(error) {
      this._template.setError(error);
    }
  }, {
    key: "getElement",
    value: function getElement() {
      return this._element;
    }
  }, {
    key: "getValue",
    value: function getValue() {}
  }, {
    key: "getTemplate",
    value: function getTemplate() {}
  }, {
    key: "render",
    value: function render() {}
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {}
  }, {
    key: "validate",
    value: function validate() {
      if (AbstractEditable.hasFormValidation()) {
        var form = this.getElement().querySelector('form');

        if (!this.getElement().querySelector('form').checkValidity()) {
          form.reportValidity();
          return false;
        }
      }

      return true;
    } // Private

  }, {
    key: "_getArgs",
    value: function _getArgs(args) {
      this._defaults = _objectSpread({}, Defaults);

      this._setDefaults();

      return Object.assign({}, this._defaults, args);
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {}
  }, {
    key: "_handleOutsideClickEvent",
    value: function _handleOutsideClickEvent() {
      this.close();
    } // Static

  }], [{
    key: "hasFormValidation",
    value: function hasFormValidation() {
      return typeof document.createElement('input').checkValidity === 'function';
    }
  }]);

  return AbstractEditable;
}();



var GlobalEvents = /*#__PURE__*/function () {
  function GlobalEvents() {
    _classCallCheck(this, GlobalEvents);

    if (AdminColumns.Editing.Editables.loaded) {
      return;
    }

    this._setEscapeEvent();

    AdminColumns.Editing.Editables.loaded = true;
  }

  _createClass(GlobalEvents, [{
    key: "_setEscapeEvent",
    value: function _setEscapeEvent() {
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
          if (Window.ACEditable) {
            Window.ACEditable.close();
          }
        }
      }, true);
    }
  }, {
    key: "_setClickEvent",
    value: function _setClickEvent() {
      document.addEventListener('click', function (e) {
        if (e.target === document.body) {
          return;
        }

        if (e.target.classList.contains('select2-results__option')) {
          return;
        }

        if (Window.ACEditable) {
          Window.ACEditable.close();
        }
      });
    }
  }]);

  return GlobalEvents;
}();

/***/ }),

/***/ "./editing/js/editable/checkbox.js":
/*!*****************************************!*\
  !*** ./editing/js/editable/checkbox.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return CheckboxEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var CheckboxEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(CheckboxEditable, _AbstractEditable);

  var _super = _createSuper(CheckboxEditable);

  function CheckboxEditable() {
    _classCallCheck(this, CheckboxEditable);

    return _super.apply(this, arguments);
  }

  _createClass(CheckboxEditable, [{
    key: "render",
    value: function render() {
      var _this = this;

      var group = this.getElement().querySelector('.input__group.-checkbox');
      Object.keys(this._args.options).forEach(function (k) {
        var option = _this._args.options[k];
        group.insertAdjacentHTML('beforeEnd', _this._template.form.checkbox('option[]', option.value, option.text).outerHTML);
      });
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(values) {
      var _this2 = this;

      if (Array.isArray(values)) {
        values.forEach(function (value) {
          var input = _this2._element.querySelector("input[value=\"".concat(value, "\"]"));

          if (input) {
            input.checked = true;
          }
        });
      }
    }
  }, {
    key: "getValue",
    value: function getValue() {
      var array = [];
      this.getElement().querySelectorAll('form input[type=checkbox]:checked').forEach(function (c) {
        return array.push(c.value);
      });
      return array;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "<div class=\"input__group -checkbox\"></div>";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.options = [];
      this._defaults.type = 'checkbox';
    }
  }]);

  return CheckboxEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/color.js":
/*!**************************************!*\
  !*** ./editing/js/editable/color.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ColorEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var ColorEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(ColorEditable, _AbstractEditable);

  var _super = _createSuper(ColorEditable);

  function ColorEditable() {
    _classCallCheck(this, ColorEditable);

    return _super.apply(this, arguments);
  }

  _createClass(ColorEditable, [{
    key: "valueToInput",
    value: function valueToInput(value) {
      var input = this.getElement().querySelector('input');

      if (value && input) {
        input.value = value;
      }

      jQuery(input).wpColorPicker();
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this._element.querySelector('input').value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return this._template.form.input('color').outerHTML;
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'color';
    }
  }]);

  return ColorEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/date.js":
/*!*************************************!*\
  !*** ./editing/js/editable/date.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DateEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var DateEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(DateEditable, _AbstractEditable);

  var _super = _createSuper(DateEditable);

  function DateEditable() {
    _classCallCheck(this, DateEditable);

    return _super.apply(this, arguments);
  }

  _createClass(DateEditable, [{
    key: "render",
    value: function render() {
      var _this = this;

      var self = this;
      var $input = jQuery(this.getElement().querySelector('[name=date]'));
      document.body.classList.add('ac-jqui');
      $input.prop('readonly', true).datepicker({
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        firstDay: this._args.weekstart,
        showButtonPanel: true,
        onSelect: function onSelect() {
          if (!_this._args.showbuttons) {
            self.save();
          }
        },
        beforeShow: function beforeShow(input, inst) {
          setTimeout(function () {
            var datepicker = inst.dpDiv.get(0);
            datepicker.style.position = 'relative';
            datepicker.style.left = 0;
            datepicker.style.top = 0;
            self.getElement().querySelector('.input__date').append(datepicker);
          }, 0);
        }
      }).on('click', function () {
        $input.prop('readonly', false);
      }).on('blur', function () {
        $input.prop('readonly', true);
      });
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      var _this2 = this;

      if (!value) {
        return;
      }

      if (value.length === 8) {
        value = DateEditable.mapACFDateFormat(value);
      }

      setTimeout(function () {
        jQuery(_this2.getElement().querySelector('[name=date]')).val(value).datepicker('show');
      }, 0);
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.getElement().querySelector('[name=date]').value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      var input = this._template.form.input('date', '', {
        placeholder: 'yyyy-mm-dd',
        autocomplete: 'nope'
      }).outerHTML;

      return "\n\t\t\t".concat(input, "\n\t\t\t<div class=\"input__date\"></div>\n\t\t");
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.weekstart = 1;
      this._defaults.type = 'date';
      this._defaults.showbuttons = true;
    }
  }], [{
    key: "mapACFDateFormat",
    value: function mapACFDateFormat(date) {
      date = [date.slice(0, 4), '-', date.slice(4)].join('');
      date = [date.slice(0, 7), '-', date.slice(7)].join('');
      return date;
    }
  }]);

  return DateEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/date_time.js":
/*!******************************************!*\
  !*** ./editing/js/editable/date_time.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return DateTimeEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var DateTimeEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(DateTimeEditable, _AbstractEditable);

  var _super = _createSuper(DateTimeEditable);

  function DateTimeEditable() {
    _classCallCheck(this, DateTimeEditable);

    return _super.apply(this, arguments);
  }

  _createClass(DateTimeEditable, [{
    key: "render",
    value: function render() {
      var input_date = this.getElement().querySelector('[name=date]');
      var input_hours = this.getElement().querySelector('[name=time_hours]');
      var input_minutes = this.getElement().querySelector('[name=time_minutes]');
      var input_seconds = this.getElement().querySelector('[name=time_seconds]');
      document.body.classList.add('ac-jqui');
      DateTimeEditable.createSelectHours(input_hours, this._args.timeformat);
      DateTimeEditable.createSelectIncementalOptions(input_minutes);
      DateTimeEditable.createSelectIncementalOptions(input_seconds);
      jQuery(input_date).datepicker({
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        showButtonPanel: true,
        firstDay: this._args.weekstart
      });
    }
  }, {
    key: "close",
    value: function close() {
      document.body.classList.remove('ac-jqui');

      _get(_getPrototypeOf(DateTimeEditable.prototype), "close", this).call(this);
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      if (!value) {
        return;
      }

      var DateTime = new Date(value.replace(/-/g, "/"));

      if (isNaN(DateTime.getTime())) {
        return;
      }

      this.getElement().querySelector('[name=date]').value = DateTime.getFullYear() + '-' + ('0' + (DateTime.getMonth() + 1)).slice(-2) + '-' + ('0' + DateTime.getDate()).slice(-2);
      this.getElement().querySelector('[name=time_hours]').value = ('0' + DateTime.getHours()).slice(-2);
      this.getElement().querySelector('[name=time_minutes]').value = ('0' + DateTime.getMinutes()).slice(-2);
      this.getElement().querySelector('[name=time_seconds]').value = ('0' + DateTime.getSeconds()).slice(-2);
    }
  }, {
    key: "getValue",
    value: function getValue() {
      var date = this.getElement().querySelector('[name=date]').value;
      var hours = this.getElement().querySelector('[name=time_hours]').value;
      var minutes = this.getElement().querySelector('[name=time_minutes]').value;
      var seconds = this.getElement().querySelector('[name=time_seconds]').value;

      if (!date || !hours || !minutes || !seconds) {
        return false;
      }

      return "".concat(date, " ").concat(hours, ":").concat(minutes, ":").concat(seconds);
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      var input = this._template.form.input('date', '', {
        placeholder: 'yyyy-mm-dd',
        autocomplete: 'nope'
      }).outerHTML;

      return "\n\t\t\t".concat(input, "\n\t\t\t<div class=\"input__time\">\n\t\t\t\t<select name=\"time_hours\"></select>\t\t\t\t\n\t\t\t\t<select name=\"time_minutes\"></select>\t\t\t\t\n\t\t\t\t<select name=\"time_seconds\"></select>\t\t\t\t\n\t\t\t</div>\n\t\t");
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'datetime';
      this._defaults.weekstart = 0;
      this._defaults.timeformat = 12;
    }
  }], [{
    key: "createSelectHours",
    value: function createSelectHours(select, timeformat) {
      for (var number = 0; number < 24; number++) {
        var option = document.createElement('option');

        var _number = ('0' + number).slice(-2);

        option.setAttribute('value', _number);
        option.text = 12 === timeformat ? DateTimeEditable.formatAMPM(_number) : _number;
        select.append(option);
      }
    }
  }, {
    key: "formatAMPM",
    value: function formatAMPM(hours) {
      var ampm = hours >= 12 ? 'pm' : 'am';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'

      return hours + ' ' + ampm;
    }
  }, {
    key: "createSelectIncementalOptions",
    value: function createSelectIncementalOptions(select) {
      var start = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
      var end = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 60;

      for (var number = start; number < end; number++) {
        var option = document.createElement('option');

        var _number = ('0' + number).slice(-2);

        option.setAttribute('value', _number);
        option.text = _number;
        select.append(option);
      }
    }
  }]);

  return DateTimeEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/fullname.js":
/*!*****************************************!*\
  !*** ./editing/js/editable/fullname.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FullnameEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var FullnameEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(FullnameEditable, _AbstractEditable);

  var _super = _createSuper(FullnameEditable);

  function FullnameEditable() {
    _classCallCheck(this, FullnameEditable);

    return _super.apply(this, arguments);
  }

  _createClass(FullnameEditable, [{
    key: "render",
    value: function render() {
      this._element.querySelector('[name="first_name"]').setAttribute('placeholder', this._args.placeholder_first_name);

      this._element.querySelector('[name="last_name"]').setAttribute('placeholder', this._args.placeholder_last_name);
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(values) {
      if (values.hasOwnProperty('first_name')) {
        this._element.querySelector('[name="first_name"]').value = values.first_name;
      }

      if (values.hasOwnProperty('last_name')) {
        this._element.querySelector('[name="last_name"]').value = values.last_name;
      }
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return {
        'first_name': this._element.querySelector('[name="first_name"]').value,
        'last_name': this._element.querySelector('[name="last_name"]').value
      };
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "\n\t\t\t<div class=\"aceditable__form__inputs\">\n\t\t\t\t<div class=\"input__group\">\n\t\t\t\t<input name=\"first_name\" type=\"text\" placeholder=\"First name\">\n\t\t\t\t<input name=\"last_name\" type=\"text\" placeholder=\"Last name\">\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'fullname';
    }
  }]);

  return FullnameEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/media.js":
/*!**************************************!*\
  !*** ./editing/js/editable/media.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return WPLibraryEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var WPLibraryEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(WPLibraryEditable, _AbstractEditable);

  var _super = _createSuper(WPLibraryEditable);

  function WPLibraryEditable() {
    _classCallCheck(this, WPLibraryEditable);

    return _super.apply(this, arguments);
  }

  _createClass(WPLibraryEditable, [{
    key: "render",
    value: function render() {
      this.library = wp.media({
        multiple: this._args.multiple,
        library: this._args.library,
        title: this._args.title
      });
      this.displaySaveButton(false);
      this.currentValues = [];

      this._addEvents();
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      if (value) {
        this.currentValues = Array.isArray(value) ? value : [value];
      }

      if (!this._args.showbuttons) {
        this.library.open();
      }
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.currentValues;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "\n\t\t<a href=\"#\" class=\"wplib button\">Select</a>\n\t\t<div class=\"aceditable__selected\"></div>";
    }
  }, {
    key: "_addEvents",
    value: function _addEvents() {
      var _this = this;

      this.getElement().querySelector('.wplib').addEventListener('click', function (e) {
        e.preventDefault();

        _this.library.open();
      });
      this.library.on('open', function () {
        var selection = _this.library.state().get('selection');

        var value = _this.currentValues;

        if (!value || _this._args.disableSelection) {
          return;
        }

        value.forEach(function (id) {
          var attachment = wp.media.attachment(id);
          attachment.fetch();
          selection.add(attachment ? [attachment] : []);
        });
      });
      this.library.on('select', function () {
        var selection = _this.library.state().get('selection').toJSON();

        var ids = [];
        selection.forEach(function (selected) {
          ids.push(selected.id);
        });
        _this.currentValues = _this._args.multiple ? ids : ids[0];

        if (!_this._args.showbuttons) {
          _this.save();

          _this.close();
        }

        _this.SetImagesFromSelection(_this.library.state().get('selection').models);

        _this.displayLibraryButton(false);

        _this.displaySaveButton(true);
      });
      this.library.on('close', function () {
        if (_this._args.showbuttons) {
          return;
        }

        setTimeout(function () {
          _this.close();
        }, 0);
      });
    }
  }, {
    key: "displaySaveButton",
    value: function displaySaveButton(show) {
      var button = this.getElement().querySelector('.aceditable__form__controls');

      if (show) {
        button.style.display = 'inline-block';
      } else {
        button.style.display = 'none';
      }
    }
  }, {
    key: "displayLibraryButton",
    value: function displayLibraryButton() {
      var show = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      var button = this.getElement().querySelector('.wplib');

      if (show) {
        button.style.display = 'inline-block';
      } else {
        button.style.display = 'none';
      }
    }
  }, {
    key: "SetImagesFromSelection",
    value: function SetImagesFromSelection(models) {
      var _this2 = this;

      var container = this.getElement().querySelector('.aceditable__selected');

      if (models.length === 0) {
        return;
      }

      jQuery.fn.qtip.zindex = 20000000;
      models.forEach(function (model) {
        var wrapper = document.createElement('div');
        var element = null;

        switch (model.attributes.type) {
          case 'image':
            element = WPLibraryEditable.getImageElement(model);
            break;

          default:
            element = WPLibraryEditable.getFileElement(model);
        }

        if (!element) {
          return;
        }

        wrapper.append(element);
        wrapper.append(WPLibraryEditable.getInfoElement(model));
        wrapper.classList.add('aceditable__selected__item');
        jQuery(wrapper).qtip({
          content: {
            text: function text() {
              return jQuery(this).find('.aceditable__selected__item__info').html();
            }
          },
          position: {
            my: 'top center',
            at: 'bottom center'
          },
          style: {
            tip: true,
            classes: 'qtip-tipsy'
          }
        });
        element.addEventListener('click', function (e) {
          e.preventDefault();

          _this2.library.open();
        });
        container.append(wrapper);
      });
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'media';
      this._defaults.title = 'Media';
      this._defaults.multiple = true;
      this._defaults.showbuttons = false;
      this._defaults.disableSelection = false;
      this._defaults.library = {
        uploadedTo: true
      };
    }
  }], [{
    key: "getInfoElement",
    value: function getInfoElement(model) {
      var element = document.createElement('div');
      element.classList.add('aceditable__selected__item__info');
      element.innerHTML = "\n\t\t\t<div>".concat(model.attributes.title, "</div>\n\t\t\t<div>").concat(model.attributes.filename, "</div>\n\t\t\t<div>ID: ").concat(model.attributes.id, "</div>\n\t\t");
      return element;
    }
  }, {
    key: "getImageElement",
    value: function getImageElement(model) {
      var element = document.createElement('img');
      var url = model.attributes.url;

      if (model.attributes.sizes.hasOwnProperty('thumbnail')) {
        url = model.attributes.sizes.thumbnail.url;
      }

      element.setAttribute('src', url);
      element.classList.add('image');
      return element;
    }
  }, {
    key: "getFileElement",
    value: function getFileElement(model) {
      var element = document.createElement('img');
      element.setAttribute('src', model.attributes.icon);
      element.classList.add('file');
      return element;
    }
  }]);

  return WPLibraryEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/multi_input.js":
/*!********************************************!*\
  !*** ./editing/js/editable/multi_input.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return MultiInputEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
/* harmony import */ var _helpers_elements__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../helpers/elements */ "./editing/js/helpers/elements.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }




var MultiInputEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(MultiInputEditable, _AbstractEditable);

  var _super = _createSuper(MultiInputEditable);

  function MultiInputEditable() {
    _classCallCheck(this, MultiInputEditable);

    return _super.apply(this, arguments);
  }

  _createClass(MultiInputEditable, [{
    key: "render",
    value: function render() {}
  }, {
    key: "valueToInput",
    value: function valueToInput(values) {
      var _this = this;

      if (!values || Array.isArray(values) && values.length === 0) {
        values = [''];
      }

      values.forEach(function (value) {
        var input = _this.createSingleInput(value);

        _this.getElement().querySelector('.acp-ie-multi-container').append(input);
      });
    }
  }, {
    key: "createSingleInput",
    value: function createSingleInput(value) {
      var subtype = this._args.subtype;
      var container = document.createElement('div');
      var input = null;
      container.classList.add('single-input');

      switch (subtype) {
        case 'textarea':
          input = this._template.form.textarea('input', value);
          break;

        default:
          input = this._template.form.input('input', value, {
            type: this._args.subtype
          });
      }

      input.value = value;

      if (!input) {
        return;
      }

      container.append(input);
      container.append(this.createControls());
      return container;
    }
  }, {
    key: "deterimineControls",
    value: function deterimineControls() {
      var lines = this.getElement().querySelectorAll('.single-input').length;

      if (lines > 1) {
        this.getElement().querySelectorAll('.single-input__control.-remove').forEach(function (e) {
          return e.style.display = 'inline-block';
        });
      } else {
        this.getElement().querySelectorAll('.single-input__control.-remove').forEach(function (e) {
          return e.style.display = 'none';
        });
      }
    }
  }, {
    key: "createControls",
    value: function createControls() {
      var _this2 = this;

      var controls = document.createElement('div');
      controls.classList.add('single-input__controls');
      controls.innerHTML = "\n\t\t\t<span class=\"single-input__control -add\">\n\t\t\t\t<span class=\"dashicons dashicons-plus\"></span>\n\t\t\t</span>\n\t\t\t<span class=\"single-input__control -remove\">\n\t\t\t\t<span class=\"dashicons dashicons-minus\"></span>\n\t\t\t</span>\n\t\t";
      controls.querySelector('.-add').addEventListener('click', function (e) {
        _this2.eventNewRow(e);
      });
      controls.querySelector('.-remove').addEventListener('click', function (e) {
        e.target.closest('.single-input').remove();

        _this2.deterimineControls();
      });
      setTimeout(function () {
        _this2.deterimineControls();
      }, 0);
      return controls;
    }
  }, {
    key: "eventNewRow",
    value: function eventNewRow(e) {
      var input = e.target.closest('.single-input');

      if (input) {
        Object(_helpers_elements__WEBPACK_IMPORTED_MODULE_1__["insertAfter"])(this.createSingleInput(''), input);
      }
    }
  }, {
    key: "getValue",
    value: function getValue() {
      var value = [];
      this.getElement().querySelectorAll('.single-input input, .single-input textarea').forEach(function (input) {
        value.push(input.value);
      });
      return value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "<div class=\"acp-ie-multi-container\"></div>";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'multi_input';
      this._defaults.subtype = 'text';
    }
  }]);

  return MultiInputEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/select.js":
/*!***************************************!*\
  !*** ./editing/js/editable/select.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return SelectEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var SelectEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(SelectEditable, _AbstractEditable);

  var _super = _createSuper(SelectEditable);

  function SelectEditable() {
    _classCallCheck(this, SelectEditable);

    return _super.apply(this, arguments);
  }

  _createClass(SelectEditable, [{
    key: "render",
    value: function render() {
      var _this = this;

      var select = this.getElement().querySelector('select');
      Object.keys(this._args.options).forEach(function (k) {
        var option = _this._args.options[k];
        var el = document.createElement('option');
        el.setAttribute('value', option.value);
        el.innerText = option.text;
        select.append(el);
      });
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      if (!value) {
        return;
      }

      this.getElement().querySelector('select').value = value;
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.getElement().querySelector('select').value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "<select name=\"select\"></select>";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'select';
    }
  }]);

  return SelectEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/select2_dropdown.js":
/*!*************************************************!*\
  !*** ./editing/js/editable/select2_dropdown.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Select2Editable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Select2Editable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(Select2Editable, _AbstractEditable);

  var _super = _createSuper(Select2Editable);

  function Select2Editable() {
    _classCallCheck(this, Select2Editable);

    return _super.apply(this, arguments);
  }

  _createClass(Select2Editable, [{
    key: "render",
    value: function render() {
      if (!this.hasOwnProperty('formattedValues')) {
        this.formattedValues = [];
      }

      var select = this.getElement().querySelector('.acs2');

      if (this.getObjectId()) {
        select.dataset.objectId = this.getObjectId();
      }

      if (this._args.select2.multiple) {
        select.setAttribute('multiple', true);
      }

      if (!this._args.showbuttons) {
        this.getElement().querySelector('.aceditable__form__controls').style.display = 'none';
      }
    }
  }, {
    key: "close",
    value: function close() {
      var select = this.getElement().querySelector('.acs2');
      jQuery(select).ac_select2('close');

      _get(_getPrototypeOf(Select2Editable.prototype), "close", this).call(this);
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      value = value ? value : [];
      value = Array.isArray(value) ? value : [value];
      var self = this;
      var select = self.getElement().querySelector('.acs2');
      var $select = jQuery(select);

      if (!self._args.ajax && value) {
        var selected = Array.isArray(value) ? value : [value];

        this._args.select2.data.map(function (d) {
          d.selected = false;
        });

        selected.forEach(function (value) {
          self.setSelectedOption(value);
        });
      }

      if (self._args.ajax && value) {
        value.forEach(function (record) {
          Object.keys(record).forEach(function (k) {
            var option_element = document.createElement('option');
            option_element.setAttribute('selected', true);
            option_element.value = k;
            option_element.text = record[k];
            select.append(option_element);
          });
        });
      }

      $select.ac_select2(self._args.select2);

      if (!this._args.showbuttons) {
        $select.on('change', function () {
          self.save();
        });
      }
    }
  }, {
    key: "setSelectedOption",
    value: function setSelectedOption(value) {
      var data = this._args.select2.data;
      data.some(function (option) {
        if (option.value.toString() === value.toString()) {
          option.selected = true;
          return true;
        }

        return false;
      });
      return data;
    }
  }, {
    key: "getValue",
    value: function getValue() {
      var select = this.getElement().querySelector('select.acs2');

      if (this._args.select2.multiple) {
        return this.getSelectedValues();
      }

      return select.value;
    }
  }, {
    key: "getSelectedValues",
    value: function getSelectedValues() {
      var selected = this.getElement().querySelectorAll('select.acs2 option:checked');
      return Array.from(selected).map(function (option) {
        return option.value;
      });
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "<select class=\"acs2\"></select>";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'select2';
      this._defaults.showbuttons = true;
    }
  }]);

  return Select2Editable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/taxonomy.js":
/*!*****************************************!*\
  !*** ./editing/js/editable/taxonomy.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TextEditable; });
/* harmony import */ var _select2_dropdown__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./select2_dropdown */ "./editing/js/editable/select2_dropdown.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


var i18n = {
  REPLACE: ACP_Editing.i18n.replace_with,
  ADD: ACP_Editing.i18n.add,
  REMOVE: ACP_Editing.i18n.remove
};
var TEMPLATE = "\n\t<div class=\"input__group -type\">\n\t<select class=\"type\" name=\"save_strategy\">\n\t\t<option value=\"replace\">".concat(i18n.REPLACE, "..</option>\n\t\t<option value=\"add\">").concat(i18n.ADD, "..</option>\n\t\t<option value=\"remove\">").concat(i18n.REMOVE, "..</option>\n\t</select>\n\t</div>\n");

var TextEditable = /*#__PURE__*/function (_Select2Editable) {
  _inherits(TextEditable, _Select2Editable);

  var _super = _createSuper(TextEditable);

  function TextEditable() {
    _classCallCheck(this, TextEditable);

    return _super.apply(this, arguments);
  }

  _createClass(TextEditable, [{
    key: "render",
    value: function render() {
      _get(_getPrototypeOf(TextEditable.prototype), "render", this).call(this);

      this.getElement().querySelector('.aceditable__form__inputs').insertAdjacentHTML("afterbegin", TEMPLATE);

      if (this._args.mode === 'inline') {
        this.getTypeElement().style.display = 'none';
        this.getTypeElement().value = 'replace';
      } else {
        this.getTypeElement().style.display = 'block';
      }
    }
  }, {
    key: "getTypeElement",
    value: function getTypeElement() {
      return this.getElement().querySelector('[name=save_strategy]');
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return {
        terms: _get(_getPrototypeOf(TextEditable.prototype), "getValue", this).call(this),
        save_strategy: this.getElement().querySelector('[name=save_strategy]').value
      };
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'taxonomy';
    }
  }]);

  return TextEditable;
}(_select2_dropdown__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/text.js":
/*!*************************************!*\
  !*** ./editing/js/editable/text.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TextEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var TextEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(TextEditable, _AbstractEditable);

  var _super = _createSuper(TextEditable);

  function TextEditable() {
    _classCallCheck(this, TextEditable);

    return _super.apply(this, arguments);
  }

  _createClass(TextEditable, [{
    key: "render",
    value: function render() {
      var _this = this;

      if (this._args.hasOwnProperty('html_attributes') && this._args.html_attributes.hasOwnProperty('type')) {
        this.getElement().classList.add("-input-".concat(this._args.html_attributes.type));
      }

      setTimeout(function () {
        _this.getElement().querySelector('input').focus();
      }, 0);
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      if (!value) {
        return;
      }

      this.getElement().querySelector('input').value = value;
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.getElement().querySelector('input').value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return this._template.form.input('input', null, this._args.html_attributes).outerHTML;
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'text';
    }
  }]);

  return TextEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/textarea.js":
/*!*****************************************!*\
  !*** ./editing/js/editable/textarea.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TextareaEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var TextareaEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(TextareaEditable, _AbstractEditable);

  var _super = _createSuper(TextareaEditable);

  function TextareaEditable() {
    _classCallCheck(this, TextareaEditable);

    return _super.apply(this, arguments);
  }

  _createClass(TextareaEditable, [{
    key: "render",
    value: function render() {
      this.getElement().querySelector('textarea').focus();
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      var textarea = this.getElement().querySelector('textarea');
      setTimeout(function () {
        textarea.focus();
        textarea.setSelectionRange(0, 0);
      }, 0);

      if (!value) {
        return;
      }

      textarea.value = value;
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.getElement().querySelector('textarea').value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      if (!this._args.html_attributes.rows) {
        this._args.html_attributes.rows = 6;
      }

      return this._template.form.textarea('text', null, this._args.html_attributes).outerHTML;
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'textarea';
    }
  }]);

  return TextareaEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/toggle.js":
/*!***************************************!*\
  !*** ./editing/js/editable/toggle.js ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ToggleEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var ToggleEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(ToggleEditable, _AbstractEditable);

  var _super = _createSuper(ToggleEditable);

  function ToggleEditable() {
    _classCallCheck(this, ToggleEditable);

    return _super.apply(this, arguments);
  }

  _createClass(ToggleEditable, [{
    key: "render",
    value: function render() {
      var options = this._args.options;
      var firstElement = this.getFirstInput();
      var secondElement = this.getSecondInput();
      firstElement.value = options[0].value;
      secondElement.value = options[1].value;
      firstElement.parentElement.querySelector('.toggle__label').innerHTML = options[0].label;
      secondElement.parentElement.querySelector('.toggle__label').innerHTML = options[1].label;

      if (ToggleEditable.getTrueValues().includes(options[0].label.toString().toLowerCase())) {
        firstElement.parentElement.querySelector('.toggle__label').innerHTML = ToggleEditable.getDefaultTrueLabel();
        secondElement.parentElement.querySelector('.toggle__label').innerHTML = ToggleEditable.getDefaultFalseLabel();
      } else if (ToggleEditable.getFalseValues().includes(options[0].label.toString().toLowerCase())) {
        firstElement.parentElement.querySelector('.toggle__label').innerHTML = ToggleEditable.getDefaultFalseLabel();
        secondElement.parentElement.querySelector('.toggle__label').innerHTML = ToggleEditable.getDefaultTrueLabel();
      }
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      // Shortroute, don't even show the modal, just toggle and save
      if (!this._args.showbuttons && value !== null) {
        var matched = false;
        var firstElement = this.getElement().querySelector('input[data-o1]');
        var secondElement = this.getElement().querySelector('input[data-o2]');

        if (firstElement.value.toString() === value.toString()) {
          secondElement.checked = true;
          matched = true;
        }

        if (secondElement.value.toString() === value.toString()) {
          firstElement.checked = true;
          matched = true;
        } // Mismatch of value and options


        if (!matched) {
          this.getTrueInput().checked = true;
        }

        this.save();
        this.close();
      }
    }
  }, {
    key: "getFalseInput",
    value: function getFalseInput() {
      if (ToggleEditable.getFalseValues().includes(this._args.options[0].value.toString().toLowerCase())) {
        return this.getFirstInput();
      } else {
        return this.getSecondInput();
      }
    }
  }, {
    key: "getTrueInput",
    value: function getTrueInput() {
      if (ToggleEditable.getTrueValues().includes(this._args.options[0].value.toString().toLowerCase())) {
        return this.getFirstInput();
      } else {
        return this.getSecondInput();
      }
    }
  }, {
    key: "getFirstInput",
    value: function getFirstInput() {
      return this.getElement().querySelector('input[data-o1]');
    }
  }, {
    key: "getSecondInput",
    value: function getSecondInput() {
      return this.getElement().querySelector('input[data-o2]');
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.getElement().querySelector('input:checked').value;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "\n\t\t<label >\n\t\t\t<input type=\"radio\" name=\"toggle\" data-o1>\n\t\t\t<span class=\"toggle__label\"></span>\n\t\t</label>\n\t\t<label data-false>\n\t\t\t<input type=\"radio\" name=\"toggle\" data-o2>\n\t\t\t<span class=\"toggle__label\"></span>\n\t\t</label>";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'togglable';
      this._defaults.showbuttons = true;
    }
  }], [{
    key: "getTrueValues",
    value: function getTrueValues() {
      return ['true', 'yes', '1', 'on'];
    }
  }, {
    key: "getFalseValues",
    value: function getFalseValues() {
      return ['', '0', 'no', 'false', 'off'];
    }
  }, {
    key: "getDefaultTrueLabel",
    value: function getDefaultTrueLabel() {
      return '<span class="dashicons dashicons-yes"></span>';
    }
  }, {
    key: "getDefaultFalseLabel",
    value: function getDefaultFalseLabel() {
      return '<span class="dashicons dashicons-no"></span>';
    }
  }]);

  return ToggleEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/editable/wysiwyg.js":
/*!****************************************!*\
  !*** ./editing/js/editable/wysiwyg.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return WysiwygEditable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


var CLASSES = {
  POPWRAP: 'ac-edit-popper-wrapper'
};

var WysiwygEditable = /*#__PURE__*/function (_AbstractEditable) {
  _inherits(WysiwygEditable, _AbstractEditable);

  var _super = _createSuper(WysiwygEditable);

  function WysiwygEditable() {
    _classCallCheck(this, WysiwygEditable);

    return _super.apply(this, arguments);
  }

  _createClass(WysiwygEditable, [{
    key: "render",
    value: function render() {
      var _this = this;

      var id = this.getEditorId();
      var $el = jQuery(this.getElement().querySelector('textarea'));
      $el.attr('id', id);
      this.getElement().addEventListener('click', function (e) {
        return e.preventDefault();
      });
      setTimeout(function () {
        var settings = _this.getEditorSettings();

        var popper = _this.getElement().closest('.ac-edit-popper');

        var modal = _this.getElement().closest('.ac-modal.-bulkedit'); // Inline Edit specific


        if (popper) {
          popper.classList.add('-full');

          _this.wrapPopper();
        } // Bulk Edit specific


        if (modal) {
          modal.classList.add('-wysiwyg');
          settings.tinymce.height = '300px';
        }

        wp.editor.initialize(id, settings);
      });
    }
  }, {
    key: "wrapPopper",
    value: function wrapPopper() {
      var wrapper = document.createElement('div');
      var popper = this.getElement().closest('.ac-edit-popper');
      wrapper.classList.add(CLASSES.POPWRAP);
      document.body.append(wrapper);
      wrapper.appendChild(popper);
    }
  }, {
    key: "removePopperwrap",
    value: function removePopperwrap() {
      document.querySelectorAll(".".concat(CLASSES.POPWRAP)).forEach(function (el) {
        return el.remove();
      });
    }
  }, {
    key: "getEditorSettings",
    value: function getEditorSettings() {
      var settings = Object.assign({}, wp.editor.getDefaultSettings());
      settings.tinymce.toolbar1 = 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,alignright,aligncenter,link,unlink,wp_add_media,wp_more,fullscreen,wp_adv';
      settings.tinymce.toolbar2 = 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,undo,redo';
      settings.tinymce.width = '100%';
      settings.quicktags = true;
      settings.mediaButtons = true;
      return settings;
    }
  }, {
    key: "close",
    value: function close() {
      this.id = null;
      this.removePopperwrap();
      return _get(_getPrototypeOf(WysiwygEditable.prototype), "close", this).call(this);
    }
  }, {
    key: "getEditorId",
    value: function getEditorId() {
      if (!this.id) {
        this.id = 'acp_' + Math.floor(Math.random() * 10000) + '_' + Math.floor(Math.random() * 10000) + '_' + Math.floor(Math.random() * 10000);
      }

      return this.id;
    }
  }, {
    key: "valueToInput",
    value: function valueToInput(value) {
      var textarea = this.getElement().querySelector('textarea');

      if (!value || !textarea) {
        return;
      }

      textarea.value = value;
    }
  }, {
    key: "getValue",
    value: function getValue() {
      var id = this.getEditorId();
      return wp.editor.getContent(id);
    }
  }, {
    key: "preventStopPropagation",
    value: function preventStopPropagation(e, type) {
      return true;
    }
  }, {
    key: "getTemplate",
    value: function getTemplate() {
      return "<textarea></textarea>";
    }
  }, {
    key: "_setDefaults",
    value: function _setDefaults() {
      this._defaults.type = 'wysiwyg';
    }
  }]);

  return WysiwygEditable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/helpers/ajax.js":
/*!************************************!*\
  !*** ./editing/js/helpers/ajax.js ***!
  \************************************/
/*! exports provided: getEditableValues */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getEditableValues", function() { return getEditableValues; });
function getEditableValues(ids) {
  return jQuery.ajax({
    url: ajaxurl,
    method: 'post',
    data: {
      action: 'acp_editing_single_request',
      method: 'get_editable_values',
      ids: ids,
      list_screen: AC.list_screen,
      layout: AC.layout,
      _ajax_nonce: AC.ajax_nonce
    }
  });
}

/***/ }),

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

/***/ "./editing/js/helpers/polyfill.js":
/*!****************************************!*\
  !*** ./editing/js/helpers/polyfill.js ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// Source: https://github.com/jserz/js_piece/blob/master/DOM/ParentNode/append()/append().md
(function (arr) {
  arr.forEach(function (item) {
    if (item.hasOwnProperty('append')) {
      return;
    }

    Object.defineProperty(item, 'append', {
      configurable: true,
      enumerable: true,
      writable: true,
      value: function append() {
        var argArr = Array.prototype.slice.call(arguments),
            docFrag = document.createDocumentFragment();
        argArr.forEach(function (argItem) {
          var isNode = argItem instanceof Node;
          docFrag.appendChild(isNode ? argItem : document.createTextNode(String(argItem)));
        });
        this.appendChild(docFrag);
      }
    });
  });
})([Element.prototype, Document.prototype, DocumentFragment.prototype]); // Source: https://github.com/jserz/js_piece/blob/master/DOM/ParentNode/prepend()/prepend().md


(function (arr) {
  arr.forEach(function (item) {
    if (item.hasOwnProperty('prepend')) {
      return;
    }

    Object.defineProperty(item, 'prepend', {
      configurable: true,
      enumerable: true,
      writable: true,
      value: function prepend() {
        var argArr = Array.prototype.slice.call(arguments),
            docFrag = document.createDocumentFragment();
        argArr.forEach(function (argItem) {
          var isNode = argItem instanceof Node;
          docFrag.appendChild(isNode ? argItem : document.createTextNode(String(argItem)));
        });
        this.insertBefore(docFrag, this.firstChild);
      }
    });
  });
})([Element.prototype, Document.prototype, DocumentFragment.prototype]);

/***/ }),

/***/ "./editing/js/helpers/rows.js":
/*!************************************!*\
  !*** ./editing/js/helpers/rows.js ***!
  \************************************/
/*! exports provided: getColumnValueFromRowHTML */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getColumnValueFromRowHTML", function() { return getColumnValueFromRowHTML; });
function getColumnValueFromRowHTML(rowHTML, column) {
  var row = document.createElement('tr');
  column = column.replace(/\./g, '\\.');
  row.innerHTML = rowHTML;
  var col = row.querySelector("td.column-".concat(column));

  if (col) {
    var invalidElements = col.querySelectorAll('.row-actions');

    if (invalidElements) {
      invalidElements.forEach(function (element) {
        element.remove();
      });
    }

    return col.innerHTML;
  }

  return '';
}

/***/ }),

/***/ "./editing/js/middleware/cells.js":
/*!****************************************!*\
  !*** ./editing/js/middleware/cells.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _modules_data_storage__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../modules/data-storage */ "./editing/js/modules/data-storage.js");

/**
 * @param {Columns} model
 * @param {Object} config
 */

var CellMiddleware = function CellMiddleware(model, config) {
  config.forEach(function (editvalue) {
    var cell = model.get(editvalue.id, editvalue.column_name);

    if (!cell) {
      return;
    }

    var settings = cell.getSettings().editable;

    if (!settings) {
      return;
    }

    if (settings.hasOwnProperty('store_values') && !settings.store_values) {
      editvalue.formatted_value = editvalue.value;
      editvalue.value = Object.keys(editvalue.value);
    }

    if (settings.hasOwnProperty('store_single_value') && settings.store_single_value) {
      editvalue.formatted_value = editvalue.value;
      editvalue.value = Object.keys(editvalue.value)[0];
    }

    cell.dataStorage = new _modules_data_storage__WEBPACK_IMPORTED_MODULE_0__["default"](editvalue);
  });
  return model;
};

/* harmony default export */ __webpack_exports__["default"] = (CellMiddleware);

/***/ }),

/***/ "./editing/js/middleware/columns.js":
/*!******************************************!*\
  !*** ./editing/js/middleware/columns.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ColumnMiddleware; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var ColumnMiddleware = /*#__PURE__*/function () {
  function ColumnMiddleware() {
    _classCallCheck(this, ColumnMiddleware);
  }

  _createClass(ColumnMiddleware, null, [{
    key: "map",

    /**
     * @param {Columns} model
     * @param {Object} config
     */
    value: function map(model, config) {
      Object.keys(config).forEach(function (column_name) {
        var editing_settings = config[column_name];
        var settings = model.get(column_name);
        settings.editable = editing_settings.editable;
        settings.type = editing_settings.type;
        settings.editable.inline_edit = editing_settings.inline_edit;

        if (!settings.editable.hasOwnProperty('bulk_editable')) {
          settings.editable.bulk_editable = editing_settings.bulk_edit;
        }
      });
      return model;
    }
  }]);

  return ColumnMiddleware;
}();



/***/ }),

/***/ "./editing/js/middleware/editable.js":
/*!*******************************************!*\
  !*** ./editing/js/middleware/editable.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return EditableMiddleware; });
/* harmony import */ var _editable_abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editable/abstract */ "./editing/js/middleware/editable/abstract.js");
/* harmony import */ var _editable_default__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editable/default */ "./editing/js/middleware/editable/default.js");
/* harmony import */ var _editable_date__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./editable/date */ "./editing/js/middleware/editable/date.js");
/* harmony import */ var _editable_checkbox__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./editable/checkbox */ "./editing/js/middleware/editable/checkbox.js");
/* harmony import */ var _editable_color__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./editable/color */ "./editing/js/middleware/editable/color.js");
/* harmony import */ var _editable_select__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./editable/select */ "./editing/js/middleware/editable/select.js");
/* harmony import */ var _editable_select2__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./editable/select2 */ "./editing/js/middleware/editable/select2.js");
/* harmony import */ var _editable_togglable__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./editable/togglable */ "./editing/js/middleware/editable/togglable.js");
/* harmony import */ var _editable_taxonomy__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./editable/taxonomy */ "./editing/js/middleware/editable/taxonomy.js");
/* harmony import */ var _editable_media__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./editable/media */ "./editing/js/middleware/editable/media.js");
/* harmony import */ var _editable_multi_input__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./editable/multi_input */ "./editing/js/middleware/editable/multi_input.js");
/* harmony import */ var _editable_wysiwyg__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./editable/wysiwyg */ "./editing/js/middleware/editable/wysiwyg.js");
/* harmony import */ var _editable_fullname__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./editable/fullname */ "./editing/js/middleware/editable/fullname.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }















var EditableMiddleware = /*#__PURE__*/function () {
  function EditableMiddleware(editables) {
    _classCallCheck(this, EditableMiddleware);

    this.Editables = editables;
    this.middleware = new Map();
    this.middleware.set('_abstract', _editable_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);
    this.middleware.set('default', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Default"]);
    this.middleware.set('text', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Default"]);
    this.middleware.set('url', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Url"]);
    this.middleware.set('number', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Number"]);
    this.middleware.set('email', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Email"]);
    this.middleware.set('checklist', _editable_checkbox__WEBPACK_IMPORTED_MODULE_3__["default"]);
    this.middleware.set('color', _editable_color__WEBPACK_IMPORTED_MODULE_4__["default"]);
    this.middleware.set('select', _editable_select__WEBPACK_IMPORTED_MODULE_5__["default"]);
    this.middleware.set('select2_dropdown', _editable_select2__WEBPACK_IMPORTED_MODULE_6__["default"]);
    this.middleware.set('date', _editable_date__WEBPACK_IMPORTED_MODULE_2__["Date"]);
    this.middleware.set('date_time', _editable_date__WEBPACK_IMPORTED_MODULE_2__["DateTime"]);
    this.middleware.set('textarea', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Textarea"]);
    this.middleware.set('togglable', _editable_togglable__WEBPACK_IMPORTED_MODULE_7__["default"]);
    this.middleware.set('attachment', _editable_media__WEBPACK_IMPORTED_MODULE_9__["Attachment"]);
    this.middleware.set('media', _editable_media__WEBPACK_IMPORTED_MODULE_9__["Media"]);
    this.middleware.set('float', _editable_default__WEBPACK_IMPORTED_MODULE_1__["Float"]);
    this.middleware.set('multi_input', _editable_multi_input__WEBPACK_IMPORTED_MODULE_10__["default"]);
    this.middleware.set('types_multi_input', _editable_multi_input__WEBPACK_IMPORTED_MODULE_10__["default"]);
    this.middleware.set('taxonomy', _editable_taxonomy__WEBPACK_IMPORTED_MODULE_8__["default"]);
    this.middleware.set('wysiwyg', _editable_wysiwyg__WEBPACK_IMPORTED_MODULE_11__["default"]);
    this.middleware.set('fullname', _editable_fullname__WEBPACK_IMPORTED_MODULE_12__["default"]);
    document.dispatchEvent(new CustomEvent('AC_Editing_Register_Middleware', {
      detail: this
    }));
  }

  _createClass(EditableMiddleware, [{
    key: "register",
    value: function register(name, object) {
      this.middleware.set(name, object);
      return this;
    }
  }, {
    key: "get",
    value: function get(column) {
      var middleware = this.middleware.get(column.editable.type);

      if (!middleware) {
        middleware = this.middleware.get('default');
      }

      return new middleware(this.Editables, column);
    }
  }]);

  return EditableMiddleware;
}();



/***/ }),

/***/ "./editing/js/middleware/editable/abstract.js":
/*!****************************************************!*\
  !*** ./editing/js/middleware/editable/abstract.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Abstract; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var Abstract = /*#__PURE__*/function () {
  function Abstract(editables, column) {
    _classCallCheck(this, Abstract);

    this.Editables = editables;
    this.column = column;
    this.args = {};
    this.map();
  }

  _createClass(Abstract, [{
    key: "getEditable",
    value: function getEditable() {
      return false;
    }
  }, {
    key: "getArgs",
    value: function getArgs() {
      return this.args;
    }
  }, {
    key: "hasMediaActions",
    value: function hasMediaActions() {
      return false;
    }
  }, {
    key: "map",
    value: function map() {}
  }]);

  return Abstract;
}();



/***/ }),

/***/ "./editing/js/middleware/editable/checkbox.js":
/*!****************************************************!*\
  !*** ./editing/js/middleware/editable/checkbox.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Checkbox; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Checkbox = /*#__PURE__*/function (_Abstract) {
  _inherits(Checkbox, _Abstract);

  var _super = _createSuper(Checkbox);

  function Checkbox() {
    _classCallCheck(this, Checkbox);

    return _super.apply(this, arguments);
  }

  _createClass(Checkbox, [{
    key: "map",
    value: function map() {
      this.args.options = AdminColumns.Editing.Helper.formatOptionsSelect2(this.column.editable.options);
      return this.args;
    }
  }, {
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('checklist');
      return editable ? editable : false;
    }
  }]);

  return Checkbox;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/color.js":
/*!*************************************************!*\
  !*** ./editing/js/middleware/editable/color.js ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Color; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Color = /*#__PURE__*/function (_Abstract) {
  _inherits(Color, _Abstract);

  var _super = _createSuper(Color);

  function Color() {
    _classCallCheck(this, Color);

    return _super.apply(this, arguments);
  }

  _createClass(Color, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('color');
      return editable ? editable : false;
    }
  }]);

  return Color;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/date.js":
/*!************************************************!*\
  !*** ./editing/js/middleware/editable/date.js ***!
  \************************************************/
/*! exports provided: Date, DateTime */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Date", function() { return _Date; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "DateTime", function() { return DateTime; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var _Date = /*#__PURE__*/function (_Abstract) {
  _inherits(Date, _Abstract);

  var _super = _createSuper(Date);

  function Date() {
    _classCallCheck(this, Date);

    return _super.apply(this, arguments);
  }

  _createClass(Date, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('date');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.args.showbuttons = false;
      return this.args;
    }
  }]);

  return Date;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);


var DateTime = /*#__PURE__*/function (_Abstract2) {
  _inherits(DateTime, _Abstract2);

  var _super2 = _createSuper(DateTime);

  function DateTime() {
    _classCallCheck(this, DateTime);

    return _super2.apply(this, arguments);
  }

  _createClass(DateTime, [{
    key: "map",
    value: function map() {
      this.args.timeformat = this.column.editable.timeformat;
      return this.args;
    }
  }, {
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('date_time');
      return editable ? editable : false;
    }
  }]);

  return DateTime;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);

/***/ }),

/***/ "./editing/js/middleware/editable/default.js":
/*!***************************************************!*\
  !*** ./editing/js/middleware/editable/default.js ***!
  \***************************************************/
/*! exports provided: Default, Email, Number, Url, Textarea, Float */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Default", function() { return Default; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Email", function() { return Email; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Number", function() { return Number; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Url", function() { return Url; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Textarea", function() { return Textarea; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Float", function() { return Float; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


var Default = /*#__PURE__*/function (_Abstract) {
  _inherits(Default, _Abstract);

  var _super = _createSuper(Default);

  function Default() {
    _classCallCheck(this, Default);

    return _super.apply(this, arguments);
  }

  _createClass(Default, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('text');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.mapHTMLAttributes(this.column.editable);
      return this.args;
    }
  }, {
    key: "mapHTMLAttributes",
    value: function mapHTMLAttributes(editable) {
      var attributes = {};

      if (editable.hasOwnProperty('required')) {
        attributes.required = editable.required;
      }

      if (editable.hasOwnProperty('placeholder')) {
        attributes.placeholder = editable.placeholder;
      }

      if (editable.hasOwnProperty('range_max')) {
        attributes.max = editable.range_max;
      }

      if (editable.hasOwnProperty('range_min')) {
        attributes.min = editable.range_min;
      }

      if (editable.hasOwnProperty('range_step')) {
        attributes.step = editable.range_step;
      }

      if (editable.hasOwnProperty('maxlength')) {
        attributes.maxlength = editable.maxlength;
      }

      this.args.html_attributes = attributes;
    }
  }]);

  return Default;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);
var Email = /*#__PURE__*/function (_Default) {
  _inherits(Email, _Default);

  var _super2 = _createSuper(Email);

  function Email() {
    _classCallCheck(this, Email);

    return _super2.apply(this, arguments);
  }

  _createClass(Email, [{
    key: "map",
    value: function map() {
      _get(_getPrototypeOf(Email.prototype), "map", this).call(this);

      this.args.html_attributes.type = 'email';
      return this.args;
    }
  }]);

  return Email;
}(Default);
var Number = /*#__PURE__*/function (_Default2) {
  _inherits(Number, _Default2);

  var _super3 = _createSuper(Number);

  function Number() {
    _classCallCheck(this, Number);

    return _super3.apply(this, arguments);
  }

  _createClass(Number, [{
    key: "map",
    value: function map() {
      _get(_getPrototypeOf(Number.prototype), "map", this).call(this);

      this.args.html_attributes.type = 'number';
      return this.args;
    }
  }]);

  return Number;
}(Default);
var Url = /*#__PURE__*/function (_Default3) {
  _inherits(Url, _Default3);

  var _super4 = _createSuper(Url);

  function Url() {
    _classCallCheck(this, Url);

    return _super4.apply(this, arguments);
  }

  _createClass(Url, [{
    key: "map",
    value: function map() {
      _get(_getPrototypeOf(Url.prototype), "map", this).call(this);

      this.args.html_attributes.type = 'url';
      return this.args;
    }
  }]);

  return Url;
}(Default);
var Textarea = /*#__PURE__*/function (_Default4) {
  _inherits(Textarea, _Default4);

  var _super5 = _createSuper(Textarea);

  function Textarea() {
    _classCallCheck(this, Textarea);

    return _super5.apply(this, arguments);
  }

  _createClass(Textarea, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('textarea');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      _get(_getPrototypeOf(Textarea.prototype), "map", this).call(this);

      return this.args;
    }
  }]);

  return Textarea;
}(Default);
var Float = /*#__PURE__*/function (_Default5) {
  _inherits(Float, _Default5);

  var _super6 = _createSuper(Float);

  function Float() {
    _classCallCheck(this, Float);

    return _super6.apply(this, arguments);
  }

  _createClass(Float, [{
    key: "map",
    value: function map() {
      _get(_getPrototypeOf(Float.prototype), "map", this).call(this);

      this.args.html_attributes.type = 'number';
      this.args.html_attributes.step = 'any';
      return this.args;
    }
  }]);

  return Float;
}(Default);

/***/ }),

/***/ "./editing/js/middleware/editable/fullname.js":
/*!****************************************************!*\
  !*** ./editing/js/middleware/editable/fullname.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Fullname; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Fullname = /*#__PURE__*/function (_Abstract) {
  _inherits(Fullname, _Abstract);

  var _super = _createSuper(Fullname);

  function Fullname() {
    _classCallCheck(this, Fullname);

    return _super.apply(this, arguments);
  }

  _createClass(Fullname, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('fullname');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.args.placeholder_first_name = this.column.editable.placeholder_first_name;
      this.args.placeholder_last_name = this.column.editable.placeholder_last_name;
      return this.args;
    }
  }]);

  return Fullname;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/media.js":
/*!*************************************************!*\
  !*** ./editing/js/middleware/editable/media.js ***!
  \*************************************************/
/*! exports provided: Media, Attachment */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Media", function() { return Media; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Attachment", function() { return Attachment; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


var Media = /*#__PURE__*/function (_Abstract) {
  _inherits(Media, _Abstract);

  var _super = _createSuper(Media);

  function Media() {
    _classCallCheck(this, Media);

    return _super.apply(this, arguments);
  }

  _createClass(Media, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('wp_library');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      var editable = this.column.editable;
      this.args.showbuttons = false;
      this.args.multiple = !!(editable.hasOwnProperty('multiple') && editable.multiple);

      if (editable.hasOwnProperty('attachment') && editable.attachment.hasOwnProperty('library')) {
        this.args.library = editable.attachment.library;
      }

      if (editable.hasOwnProperty('attachment') && editable.attachment.hasOwnProperty('disable_select_current') && editable.attachment.disable_select_current) {
        this.args.disableSelection = true;
      }

      return this.args;
    }
  }, {
    key: "hasMediaActions",
    value: function hasMediaActions() {
      return true;
    }
  }]);

  return Media;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);
var Attachment = /*#__PURE__*/function (_Media) {
  _inherits(Attachment, _Media);

  var _super2 = _createSuper(Attachment);

  function Attachment() {
    _classCallCheck(this, Attachment);

    return _super2.apply(this, arguments);
  }

  _createClass(Attachment, [{
    key: "map",
    value: function map() {
      _get(_getPrototypeOf(Attachment.prototype), "map", this).call(this);

      this.args.attachment = true;
      return this.args;
    }
  }]);

  return Attachment;
}(Media);

/***/ }),

/***/ "./editing/js/middleware/editable/multi_input.js":
/*!*******************************************************!*\
  !*** ./editing/js/middleware/editable/multi_input.js ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return MultiInput; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var MultiInput = /*#__PURE__*/function (_Abstract) {
  _inherits(MultiInput, _Abstract);

  var _super = _createSuper(MultiInput);

  function MultiInput() {
    _classCallCheck(this, MultiInput);

    return _super.apply(this, arguments);
  }

  _createClass(MultiInput, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('multi_input');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.args.subtype = this.column.editable.subtype;
      return this.args;
    }
  }]);

  return MultiInput;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/select.js":
/*!**************************************************!*\
  !*** ./editing/js/middleware/editable/select.js ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Select; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Select = /*#__PURE__*/function (_Abstract) {
  _inherits(Select, _Abstract);

  var _super = _createSuper(Select);

  function Select() {
    _classCallCheck(this, Select);

    return _super.apply(this, arguments);
  }

  _createClass(Select, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('select');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.args.options = AdminColumns.Editing.Helper.formatOptionsSelect2(this.column.editable.options);
      return this.args;
    }
  }]);

  return Select;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/select2.js":
/*!***************************************************!*\
  !*** ./editing/js/middleware/editable/select2.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Select2; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Select2 = /*#__PURE__*/function (_Abstract) {
  _inherits(Select2, _Abstract);

  var _super = _createSuper(Select2);

  function Select2() {
    _classCallCheck(this, Select2);

    return _super.apply(this, arguments);
  }

  _createClass(Select2, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('select2');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.args.select2 = {
        width: 320,
        theme: 'acs2',
        data: AdminColumns.Editing.Helper.formatOptionsSelect2(this.column.editable.options),
        escapeMarkup: function escapeMarkup(text) {
          return jQuery('<div>' + text + '</div>').text();
        }
      };

      if (this.column.editable.ajax_populate) {
        this.args.select2.ajax = this.mapAjax();
        this.args.ajax = true;
      }

      if (this.column.editable.multiple) {
        this.args.select2.multiple = true;
        this.args.select2.closeOnSelect = false;
      }

      if (this.column.editable.tags) {
        this.args.select2.tags = true;
      }
    }
  }, {
    key: "mapAjax",
    value: function mapAjax() {
      var column_name = this.column.name;
      return {
        url: ajaxurl,
        dataType: 'json',
        delay: 500,
        data: function data(params) {
          var data = {
            action: 'acp_editing_single_request',
            method: 'get_select_values',
            layout: AC.layout,
            searchterm: params.term,
            page: params.page,
            column: column_name,
            list_screen: AC.list_screen,
            _ajax_nonce: AC.ajax_nonce
          };

          if (this[0].dataset.hasOwnProperty('objectId')) {
            data.item_id = this[0].dataset.objectId;
          }

          return data;
        },
        processResults: function processResults(response) {
          if (response.success) {
            return response.data;
          }

          return {
            results: []
          };
        }
      };
    }
  }]);

  return Select2;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/taxonomy.js":
/*!****************************************************!*\
  !*** ./editing/js/middleware/editable/taxonomy.js ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Select2; });
/* harmony import */ var _select2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./select2 */ "./editing/js/middleware/editable/select2.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Select2 = /*#__PURE__*/function (_Select2Middleware) {
  _inherits(Select2, _Select2Middleware);

  var _super = _createSuper(Select2);

  function Select2() {
    _classCallCheck(this, Select2);

    return _super.apply(this, arguments);
  }

  _createClass(Select2, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('taxonomy');
      return editable ? editable : false;
    }
  }]);

  return Select2;
}(_select2__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/togglable.js":
/*!*****************************************************!*\
  !*** ./editing/js/middleware/editable/togglable.js ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Togglable; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Togglable = /*#__PURE__*/function (_Abstract) {
  _inherits(Togglable, _Abstract);

  var _super = _createSuper(Togglable);

  function Togglable() {
    _classCallCheck(this, Togglable);

    return _super.apply(this, arguments);
  }

  _createClass(Togglable, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('toggle');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      this.args.options = this.column.editable.options;
      this.args.showbuttons = false;
      return this.args;
    }
  }, {
    key: "flattenOptionArray",
    value: function flattenOptionArray(options) {
      var result = [];
      options.forEach(function (option) {
        result.push(option.label);
      });
      return result;
    }
  }]);

  return Togglable;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/middleware/editable/wysiwyg.js":
/*!***************************************************!*\
  !*** ./editing/js/middleware/editable/wysiwyg.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Wysiwyg; });
/* harmony import */ var _abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./abstract */ "./editing/js/middleware/editable/abstract.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }



var Wysiwyg = /*#__PURE__*/function (_Abstract) {
  _inherits(Wysiwyg, _Abstract);

  var _super = _createSuper(Wysiwyg);

  function Wysiwyg() {
    _classCallCheck(this, Wysiwyg);

    return _super.apply(this, arguments);
  }

  _createClass(Wysiwyg, [{
    key: "getEditable",
    value: function getEditable() {
      var editable = this.Editables.get('wysiwyg');
      return editable ? editable : false;
    }
  }, {
    key: "map",
    value: function map() {
      return this.args;
    }
  }]);

  return Wysiwyg;
}(_abstract__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./editing/js/modules/bulk-edit-row.js":
/*!*********************************************!*\
  !*** ./editing/js/modules/bulk-edit-row.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BulkEditRow; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var SELECTORS = {
  ROWCLASS: 'acp-be-editrow'
};
var i18n = {
  BULK_EDIT: ACP_Editing.i18n.bulk_edit.bulk_edit
};

var BulkEditRow = /*#__PURE__*/function () {
  function BulkEditRow(BulkEdit) {
    _classCallCheck(this, BulkEditRow);

    this.BulkEdit = BulkEdit;
    this.Table = BulkEdit.Table;
    this.Middleware = BulkEdit.Middleware;
  }

  _createClass(BulkEditRow, [{
    key: "add",
    value: function add() {
      var thead = this.Table.el.getElementsByTagName('thead')[0];
      var headers = thead.children[0].children;
      var row = document.createElement('tr');
      row.classList.add(SELECTORS.ROWCLASS);

      for (var i = 0; i < headers.length; i++) {
        var header = headers[i];
        var column = this.Table.Columns.get(header.id);
        var cell = document.createElement('td');
        cell.classList.add("column-".concat(header.id));

        if (header.id !== 'cb' && column.editable) {
          if (column.editable.hasOwnProperty('bulk_editable') && column.editable.bulk_editable === false) {
            row.appendChild(cell);
            continue;
          }

          cell.innerHTML = "<button class=\"button\">".concat(i18n.BULK_EDIT, "</button>");
          cell.column = column;
          this.initEvents(cell);
        }

        row.appendChild(cell);
      }

      this.Table.el.querySelector('tbody').insertAdjacentElement('afterBegin', row);
      this.refresh();
      return row;
    }
  }, {
    key: "remove",
    value: function remove() {
      var row = this.Table.el.querySelector(".".concat(SELECTORS.ROWCLASS));

      if (row) {
        row.remove();
      }
    }
  }, {
    key: "initEvents",
    value: function initEvents(cell) {
      var _this = this;

      cell.querySelector('button').addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        _this.BulkEdit.onButtonTrigger(cell);
      }, true);
    }
  }, {
    key: "getRow",
    value: function getRow() {
      return this.Table.el.querySelector(".".concat(SELECTORS.ROWCLASS));
    }
  }, {
    key: "refresh",
    value: function refresh() {
      var thead = this.Table.el.getElementsByTagName('thead')[0];
      var headers = thead.children[0].children;

      for (var i = 0; i < headers.length; i++) {
        var header = headers[i];
        var isHidden = header.classList.contains('hidden');
        var col = this.getRow().querySelector(".column-".concat(header.id));

        if (!col) {
          continue;
        }

        if (isHidden) {
          col.classList.add('hidden');
        } else {
          col.classList.remove('hidden');
        }
      }
    }
  }]);

  return BulkEditRow;
}();



/***/ }),

/***/ "./editing/js/modules/bulk-edit.js":
/*!*****************************************!*\
  !*** ./editing/js/modules/bulk-edit.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BulkEdit; });
/* harmony import */ var _middleware_editable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../middleware/editable */ "./editing/js/middleware/editable.js");
/* harmony import */ var popper_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js");
/* harmony import */ var _bulk_processor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./bulk-processor */ "./editing/js/modules/bulk-processor.js");
/* harmony import */ var admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! admin-columns-js/helper/strings */ "./node_modules/admin-columns-js/helper/strings.js");
/* harmony import */ var _bulk_edit_row__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./bulk-edit-row */ "./editing/js/modules/bulk-edit-row.js");
/* harmony import */ var _bulk_notice__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./bulk-notice */ "./editing/js/modules/bulk-notice.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }







var i18n = {
  UPDATE: Object(admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_3__["Format"])(ACP_Editing.i18n.bulk_edit.form.update_values, '<span data-acp-be-count>0</span>'),
  ARE_YOU_SURE: ACP_Editing.i18n.bulk_edit.form.are_you_sure,
  YES_UPDATE: ACP_Editing.i18n.bulk_edit.form.yes_update,
  CANCEL: ACP_Editing.i18n.cancel
};
var defaults = {
  total_count: ACP_Editing.bulk_edit.total_items
};

var BulkEdit = /*#__PURE__*/function () {
  function BulkEdit(Table, Editables) {
    _classCallCheck(this, BulkEdit);

    this.Table = Table;
    this.Editables = Editables;
    this.Middleware = new _middleware_editable__WEBPACK_IMPORTED_MODULE_0__["default"](this.Editables);
    this.Notice = new _bulk_notice__WEBPACK_IMPORTED_MODULE_5__["default"](this.Table);
    this.EditableModal = false;
    this.EditRow = new _bulk_edit_row__WEBPACK_IMPORTED_MODULE_4__["default"](this);
    this._allSelected = false;
    this._isConfirmed = false;
    this.init();
  }

  _createClass(BulkEdit, [{
    key: "init",
    value: function init() {
      this.addTableEvents();
    }
  }, {
    key: "setConfirmed",
    value: function setConfirmed(confirm) {
      this._isConfirmed = confirm;
      return this;
    }
  }, {
    key: "isConfirmed",
    value: function isConfirmed() {
      if (ACP_Editing.bulk_edit.hasOwnProperty('show_confirmation') && !ACP_Editing.bulk_edit.show_confirmation) {
        return true;
      }

      return this._isConfirmed;
    }
  }, {
    key: "onSave",
    value: function onSave(editable, cell) {
      var _this = this;

      if (!this.isConfirmed()) {
        this.confirm(editable);
        return;
      }

      var job = new _bulk_processor__WEBPACK_IMPORTED_MODULE_2__["default"](cell.column, editable.getValue(), this.getItemsToProcess(), this._allSelected);
      this._locked = true;
      this.EditableModal.setProcess(job.getElement());
      this.Editable.disable();
      editable.getElement().querySelector('.aceditable__form__controls [data-submit]').style.display = 'none'; // Bind Events

      job.Events.addListener('close', function () {
        _this.EditableModal.close();
      });
      job.Events.addListener('finished', function () {
        AdminColumns.Editing.TableUpdate.updateSelectedCells(cell.column.name, editable.getValue());

        if (_this.EditableModal) {
          _this.EditableModal.setCanDismiss(true);

          _this.EditableModal.toggleCloseButton(true);
        }
      });
    }
  }, {
    key: "onButtonTrigger",
    value: function onButtonTrigger(cell) {
      var _this2 = this;

      var middleware = this.Middleware.get(cell.column);
      var editable = middleware.getEditable();
      var modal = AdminColumns.Editing.BulkEdit.Modal;
      this.setConfirmed(false);

      if (!editable || this.EditableModal) {
        return false;
      }

      var editableArgs = middleware.getArgs();
      editableArgs.showbuttons = true;
      editableArgs.mode = 'bulk';
      var Editable = new editable(editableArgs);
      var EditableModal = new modal(modal.appendMarkup(), Editable);
      Editable.getEditableTemplate().setSubmitButton('Update');
      EditableModal.setTitle("Bulk Edit \u201C".concat(cell.column.label, "\u201D"));
      EditableModal.setTotal(this.getSelectedCount());
      EditableModal.open();
      this.EditableModal = EditableModal;
      this.Editable = Editable;
      this.updateCount(); // Bind Events

      Editable.Events.addListener('save', function (editable) {
        _this2.onSave(editable, cell);
      });
      EditableModal.Events.addListener('close', function () {
        _this2.destroyEditable();

        _this2.EditableModal = null;
      });
    }
  }, {
    key: "destroyEditable",
    value: function destroyEditable() {
      this.removeConfirm();
      this.Editable.close();
      this.EditableModal.destroy();
    }
  }, {
    key: "confirm",
    value: function confirm(editable) {
      var _this3 = this;

      this.removeConfirm();
      var confirm = document.createElement('div');
      confirm.classList.add('acp-be-confirm');
      confirm.innerHTML = "\n\t\t\t<span class=\"acp-be-confirm__caret\"></span>\n\t\t\t<em class=\"acp-be-confirm__info\">".concat(i18n.UPDATE, "</em>\n\t\t\t<div class=\"acp-be-confirm__controls\"><strong>").concat(i18n.ARE_YOU_SURE, "</strong> <a data-confirm class=\"acp-be-confirm__controls__link -confirm\">").concat(i18n.YES_UPDATE, "</a> <a data-cancel class=\"acp-be-confirm__controls__link -cancel\">").concat(i18n.CANCEL, "</a></div>\n\t\t");
      document.body.append(confirm);
      new popper_js__WEBPACK_IMPORTED_MODULE_1__["default"](editable.getEditableTemplate().el.querySelector('[data-submit]'), confirm, {
        modifiers: {
          preventOverflow: {
            enabled: false
          },
          hide: {
            enabled: false
          },
          arrow: {
            element: '.acp-be-confirm__caret'
          }
        }
      });
      this._confirmElement = confirm;
      this.setConfirmed(false);
      this.updateCount(); // Bind Events

      var elDialog = this.EditableModal.el.querySelector('.ac-modal__dialog');
      var elConfirm = confirm.querySelector('[data-confirm]');
      var elCancel = confirm.querySelector('[data-cancel]');
      elDialog.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && document.querySelectorAll('.acp-be-confirm').length > 0) {
          _this3.setConfirmed(true);

          _this3.removeConfirm();

          editable.save();
        }
      });
      elConfirm.addEventListener('click', function () {
        _this3.setConfirmed(true);

        _this3.removeConfirm();

        editable.save();
      });
      elCancel.addEventListener('click', function () {
        _this3.setConfirmed(false);

        _this3.removeConfirm();
      });
    }
  }, {
    key: "addTableEvents",
    value: function addTableEvents() {
      var _this4 = this;

      var checkboxes = this.Table.el.querySelectorAll('.check-column input[type=checkbox]');
      checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
          _this4._allSelected = false;
          setTimeout(function () {
            if (_this4.Table.Selection.getCount() > 1) {
              _this4.toggleEditRows(true);
            } else {
              _this4.toggleEditRows(false);
            }

            if (_this4.Table.Selection.isAllSelected() && defaults.total_count && _this4.Table.Selection.getCount() < defaults.total_count) {
              var link = _this4.Notice.createSelectAll(defaults.total_count);

              defaults.total_count = defaults.total_count ? defaults.total_count : 'all';

              if (link) {
                link.style.display = 'inline';
                link.addEventListener('click', function () {
                  link.style.display = 'none';
                  _this4._allSelected = true;

                  _this4.updateCount();
                });
              }
            }

            _this4.updateCount();
          });
        });
      });
      this.Table.el.addEventListener('scroll', function (e) {
        var table = AdminColumns.Table.el;
        var notice = table.querySelector('.acp-be-noticerow .acp-be-notice p');

        if (!notice) {
          return;
        }

        if (document.body.classList.contains('acp-overflow-table')) {
          notice.style.left = "".concat(table.scrollLeft, "px");
        } else {
          notice.style.left = 0;
        }
      });
    }
  }, {
    key: "toggleEditRows",
    value: function toggleEditRows() {
      var show = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      this.Notice.removeNotice();
      this.EditRow.remove();

      if (show) {
        this.Notice.addNotice();
        this.EditRow.add();
      }
    }
  }, {
    key: "getSelectedCount",
    value: function getSelectedCount() {
      var count = this.Table.Selection.getCount();

      if (this._allSelected) {
        count = defaults.total_count;
      }

      return count;
    }
  }, {
    key: "updateCount",
    value: function updateCount() {
      var _this5 = this;

      document.querySelectorAll('[data-acp-be-count]').forEach(function (el) {
        el.innerHTML = _this5.getSelectedCount();
      });
    }
  }, {
    key: "getItemsToProcess",
    value: function getItemsToProcess() {
      if (this._allSelected) {
        return defaults.total_count;
      }

      return this.Table.Selection.getIDs();
    }
  }, {
    key: "removeConfirm",
    value: function removeConfirm() {
      if (!this._confirmElement) {
        return;
      }

      this._confirmElement.remove();
    }
  }]);

  return BulkEdit;
}();



/***/ }),

/***/ "./editing/js/modules/bulk-modal.js":
/*!******************************************!*\
  !*** ./editing/js/modules/bulk-modal.js ***!
  \******************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _event_emitter__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./event-emitter */ "./editing/js/modules/event-emitter.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _get(target, property, receiver) { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get; } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(receiver); } return desc.value; }; } return _get(target, property, receiver || target); }

function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }


var i18n = {
  AFFECT: 'This will affect <strong><span data-total="">12.500</span> entries</strong>'
};

var BulkEditModal = /*#__PURE__*/function (_AdminColumns$Modals$) {
  _inherits(BulkEditModal, _AdminColumns$Modals$);

  var _super = _createSuper(BulkEditModal);

  function BulkEditModal(el, Editable) {
    var _this;

    _classCallCheck(this, BulkEditModal);

    _this = _super.call(this, el);
    _this.canDismiss = true;
    _this.Editable = Editable;
    _this.Events = new _event_emitter__WEBPACK_IMPORTED_MODULE_0__["default"]();

    _this.hideProcess();

    _this.setEditable(_this.Editable.get());

    return _this;
  }

  _createClass(BulkEditModal, [{
    key: "close",
    value: function close() {
      if (!this.canDismiss) {
        return;
      }

      _get(_getPrototypeOf(BulkEditModal.prototype), "close", this).call(this);

      this.Events.emit('close');
    }
  }, {
    key: "setTotal",
    value: function setTotal(number) {
      this.el.querySelectorAll('[data-total] ').forEach(function (el) {
        el.innerHTML = number;
      });
    }
  }, {
    key: "setEditable",
    value: function setEditable(el) {
      this.Editable = el;
      this.el.querySelector('[data-editable]').append(el);
    }
  }, {
    key: "setTitle",
    value: function setTitle(title) {
      this.el.querySelector('[data-title]').innerHTML = title;
    }
  }, {
    key: "setProcess",
    value: function setProcess(el) {
      var container = this.el.querySelector('[data-process]');
      this.el.querySelector('.ac-modal__dialog__content.-info').style.display = 'none';
      container.innerHTML = '';
      container.append(el);
      container.style.display = 'block';
      this.canDismiss = false;
      this.toggleCloseButton(false);
    }
  }, {
    key: "setCanDismiss",
    value: function setCanDismiss(candismiss) {
      this.canDismiss = candismiss;
    }
  }, {
    key: "toggleCloseButton",
    value: function toggleCloseButton(show) {
      if (show) {
        this.el.querySelector('.ac-modal__dialog__close').style.display = 'block';
      } else {
        this.el.querySelector('.ac-modal__dialog__close').style.display = 'none';
      }
    }
  }, {
    key: "hideProcess",
    value: function hideProcess() {
      this.el.querySelector('[data-process]').style.display = 'none';
    }
  }], [{
    key: "appendMarkup",
    value: function appendMarkup() {
      var modal = document.createElement('div');
      modal.classList.add('ac-modal');
      modal.classList.add('-bulkedit');
      modal.innerHTML = "\n\t\t\t\t<div class=\"ac-modal__dialog\">\n\t\t\t\t\t<div class=\"ac-modal__dialog__header\">\n\t\t\t\t\t\t<span data-title></span>\n\t\t\t\t\t\t<button class=\"ac-modal__dialog__close\">\n\t\t\t\t\t\t\t<span class=\"dashicons dashicons-no\"></span>\n\t\t\t\t\t\t</button>\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"ac-modal__dialog__content -editable\" data-editable></div>\n\t\t\t\t\t<div class=\"ac-modal__dialog__content -info\">\n\t\t\t\t\t\t<span class=\"acp-be-warning__icon dashicons dashicons-info\"></span><em>".concat(i18n.AFFECT, "</em> \n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"ac-modal__dialog__content -process\" data-process>\n\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t");
      document.body.appendChild(modal);
      return modal;
    }
  }]);

  return BulkEditModal;
}(AdminColumns.Modals.defaults.modal);

AdminColumns.Editing.BulkEdit.Modal = BulkEditModal;

/***/ }),

/***/ "./editing/js/modules/bulk-notice.js":
/*!*******************************************!*\
  !*** ./editing/js/modules/bulk-notice.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BulkNotice; });
/* harmony import */ var admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! admin-columns-js/helper/strings */ "./node_modules/admin-columns-js/helper/strings.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


var i18n = {
  SELECTED: ACP_Editing.i18n.bulk_edit.selecting.selected,
  SELECTALL: ACP_Editing.i18n.bulk_edit.selecting.select_all
};
var SELECTORS = {
  ROW: 'acp-be-noticerow'
};

var BulkNotice = /*#__PURE__*/function () {
  function BulkNotice(Table) {
    _classCallCheck(this, BulkNotice);

    this.notice = null;
    this.Table = Table;
  }

  _createClass(BulkNotice, [{
    key: "addNotice",
    value: function addNotice() {
      var _this = this;

      this.createNotice();
      this.createNoticeRow();
      this.refreshNoticeRow();
      document.querySelectorAll('.hide-column-tog').forEach(function (cb) {
        cb.addEventListener('click', function () {
          _this.refreshNoticeRow();
        });
      });
    }
  }, {
    key: "removeNotice",
    value: function removeNotice() {
      var notice = this.Table.el.querySelector(".".concat(SELECTORS.ROW));

      if (notice) {
        notice.remove();
      }
    }
  }, {
    key: "createNotice",
    value: function createNotice() {
      var notice = document.createElement('div');
      notice.classList.add('acp-be-notice');
      var p = document.createElement('p');
      var text = Object(admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__["Format"])("".concat(i18n.SELECTED, " "), "<span data-acp-be-count></span>");
      p.insertAdjacentHTML('beforeEnd', text);
      notice.append(p);
      this.notice = notice;
    }
  }, {
    key: "createSelectAll",
    value: function createSelectAll(number) {
      if (document.querySelector('.ac-be-selectall')) {
        return document.querySelector('.ac-be-selectall');
      }

      if (number === false || number === 'all') {
        number = '';
      }

      if (!this.notice) {
        return;
      }

      var link = document.createElement('a');
      link.innerHTML = Object(admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__["Format"])(i18n.SELECTALL, number);
      link.classList.add('ac-be-selectall');
      this.notice.querySelector('p').append(link);
      return link;
    }
  }, {
    key: "createNoticeRow",
    value: function createNoticeRow() {
      var row = document.createElement('tr');
      var col = document.createElement('td');
      row.append(col);
      row.classList.add(SELECTORS.ROW);
      var colspan = AdminColumns.Table.el.querySelector('thead tr').children.length;
      col.setAttribute('colspan', colspan);
      col.append(this.notice);
      this.Table.el.querySelector('tbody').insertAdjacentElement('afterBegin', row);
      return row;
    }
  }, {
    key: "refreshNoticeRow",
    value: function refreshNoticeRow() {
      var cells = this.Table.el.firstElementChild.firstElementChild.querySelectorAll('td,th');
      var colspan = 0;
      cells.forEach(function (cell) {
        if (!cell.classList.contains('hidden')) {
          colspan++;
        }
      });
      this.Table.el.querySelector(".".concat(SELECTORS.ROW, " td")).setAttribute('colspan', colspan);
    }
  }]);

  return BulkNotice;
}();



/***/ }),

/***/ "./editing/js/modules/bulk-processor.js":
/*!**********************************************!*\
  !*** ./editing/js/modules/bulk-processor.js ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Processor; });
/* harmony import */ var admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! admin-columns-js/helper/strings */ "./node_modules/admin-columns-js/helper/strings.js");
/* harmony import */ var _event_emitter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./event-emitter */ "./editing/js/modules/event-emitter.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var i18n = {
  FINISHED: ACP_Editing.i18n.bulk_edit.feedback.finished,
  UPDATING: ACP_Editing.i18n.bulk_edit.feedback.updating,
  PROCESSED: Object(admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__["Format"])(ACP_Editing.i18n.bulk_edit.feedback.processed, '<span data-processed>0</span>', '<span data-total-items></span>'),
  FAIL: ACP_Editing.i18n.bulk_edit.feedback.failure,
  DONE: ACP_Editing.i18n.done,
  DONE_DESELECT: ACP_Editing.i18n.bulk_edit.selecting.done_deselect,
  CANCEL: ACP_Editing.i18n.cancel,
  ERROR: Object(admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__["Format"])(ACP_Editing.i18n.bulk_edit.feedback.error, '<span data-errors>0</span>')
};
var ProcessTemplate = "\n\t\t<div class=\"acp-be-progress\">\n\t\t\t<div class=\"acp-be-progress__timer\" data-timer></div>\n\t\t\t<div class=\"acp-be-progress__process\">".concat(i18n.PROCESSED, " (<span data-percentage></span>)</div>\n\t\t</div>\n\n\t\t<div class=\"acp-be-statusbar\">\n\t\t\t<div class=\"acp-be-statusbar__progress\" data-status-percentage></div>\n\t\t</div>\n\n\t\t<div class=\"errors\" style=\"display: none;\">\n\t\t\t<div class=\"errors__info\">").concat(i18n.ERROR, "</div>\n\t\t\t<div class=\"errors__lines\"></div>\n\t\t</div>\n\n\t\t<button data-close class=\"button\">Cancel</button>\n");

var Processor = /*#__PURE__*/function () {
  function Processor(column, value, items) {
    var processAll = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

    _classCallCheck(this, Processor);

    this.Events = new _event_emitter__WEBPACK_IMPORTED_MODULE_1__["default"]();
    this.column = column;
    this.value = value;
    this._items = items;
    this._total = Array.isArray(items) ? items.length : items;
    this._processed = 0;
    this._perPage = ACP_Editing.bulk_edit.updated_rows_per_iteration;
    this._processAll = processAll;
    this._isCanceled = false;
    this._isFinished = false;
    this.errors = [];
    this.timer = {
      seconds: 0,
      minutes: 0
    };
    this.prepareElement();
    this.startTimer();
    this.prepareJob();
    this.attachCancelEvent();
    this.updateTotal();
    this.updatePercentage(0);
  }

  _createClass(Processor, [{
    key: "getElement",
    value: function getElement() {
      return this.element;
    }
  }, {
    key: "prepareJob",
    value: function prepareJob() {
      var _this = this;

      var page = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;

      if (!this._processAll) {
        this.runJob();
        return;
      }

      if (page === 1) {
        this._items = [];
      }

      jQuery.ajax({
        url: window.location.href,
        method: 'get',
        data: {
          ac_action: 'get_editable_rows',
          ac_page: page,
          _ajax_nonce: AC.ajax_nonce
        }
      }).done(function (response) {
        if (!response.hasOwnProperty('success') || !response.success) {
          return;
        }

        if (!response.data.hasOwnProperty('editable_rows')) {
          return;
        }

        _this._items = [].concat(_this._items, response.data.editable_rows);
        _this._total = _this._items.length;

        if (response.data.rows_per_iteration === response.data.editable_rows.length) {
          _this.updateTotal();

          _this.prepareJob(page + 1);

          return;
        }

        _this.updateTotal();

        _this.runJob();
      });
    }
  }, {
    key: "runJob",
    value: function runJob() {
      var _this2 = this;

      if (this._isCanceled) {
        return;
      }

      var self = this;
      var items = self._items;
      var item_set = items.splice(0, self._perPage);

      if (item_set.length === 0) {
        self.onComplete();
        return;
      }

      this.startUpdateThread(item_set).then(function (response) {
        if (response.success) {
          var num = response.data.total;
          self._processed += num;

          _this2.updateProcessed(self._processed);

          if (num > 0 && !self._isCanceled) {
            self.runJob();
          } else {
            self.onComplete();
          }
        } else {// Todo
          //this.onFailure();
        }
      });
    }
  }, {
    key: "startUpdateThread",
    value: function startUpdateThread(ids) {
      return jQuery.ajax({
        url: ajaxurl,
        data: {
          action: 'acp_editing_bulk_request',
          method: 'save',
          list_screen: AC.list_screen,
          layout: AC.layout,
          column: this.column.name,
          ids: ids,
          value: this.value,
          _ajax_nonce: AC.ajax_nonce
        },
        method: 'POST',
        dataType: 'json'
      });
    }
  }, {
    key: "prepareElement",
    value: function prepareElement() {
      var element = document.createElement('div');
      element.classList.add('acp-be-processing');
      element.innerHTML = ProcessTemplate;
      this.element = element;
    }
  }, {
    key: "attachCancelEvent",
    value: function attachCancelEvent() {
      var _this3 = this;

      var button = this.element.querySelector('[data-close]');

      if (!button) {
        return;
      }

      button.addEventListener('click', function () {
        if (_this3._isFinished) {
          button.remove();

          _this3.Events.emit('close', {});
        } else {
          _this3._isCanceled = true;

          _this3.onComplete();
        }
      });
    }
  }, {
    key: "processResponse",
    value: function processResponse(response) {
      this.updateProcessed(response.num_rows_processed);
    }
  }, {
    key: "updateTotal",
    value: function updateTotal() {
      this.element.querySelector('[data-total-items]').innerHTML = this._total;
    }
  }, {
    key: "updateProcessed",
    value: function updateProcessed(number) {
      this.element.querySelector('[data-processed]').innerHTML = number;
      var percentage = Math.round(number / this._total * 100);
      this.updatePercentage(percentage);
    }
  }, {
    key: "updatePercentage",
    value: function updatePercentage(percentage) {
      this.element.querySelector('[data-percentage]').innerHTML = "".concat(percentage, "%");
      this.element.querySelector('[data-status-percentage]').style.width = "".concat(percentage, "%");
    }
  }, {
    key: "onComplete",
    value: function onComplete() {
      if (!this._isCanceled) {
        this.element.querySelector('.acp-be-progress__process').innerHTML = Object(admin_columns_js_helper_strings__WEBPACK_IMPORTED_MODULE_0__["Format"])(i18n.FINISHED, this._total) + ' (100%) <span class="dashicons dashicons-yes"></span>';
      }

      this.element.querySelector('[data-close]').innerHTML = i18n.DONE;
      this.element.querySelector('[data-close]').classList.add('button-primary');
      this.element.querySelector('[data-status-percentage]').classList.add('-finished');
      this._isFinished = true;
      this.stopTimer();
      this.canDismiss = false;
      this.addDeselectDoneButton();
      this.Events.emit('finished');
    }
  }, {
    key: "addDeselectDoneButton",
    value: function addDeselectDoneButton() {
      var _this4 = this;

      var button = new DoneButton();
      button.el.addEventListener('click', function (e) {
        e.preventDefault();
        AdminColumns.Table.el.querySelectorAll('.check-column input[type=checkbox]:checked').forEach(function (cb) {
          cb.checked = false;
          cb.dispatchEvent(new Event('change'));
        });

        _this4.Events.emit('close');
      });
      this.element.append(button.el);
    }
  }, {
    key: "startTimer",
    value: function startTimer() {
      var _this5 = this;

      this.addSecond();
      this.timerInterval = setInterval(function () {
        _this5.addSecond();
      }, 1000);
    }
  }, {
    key: "stopTimer",
    value: function stopTimer() {
      clearInterval(this.timerInterval);
    }
  }, {
    key: "addSecond",
    value: function addSecond() {
      var element = this.element.querySelector('[data-timer]');
      this.timer.seconds++;

      if (this.timer.seconds >= 60) {
        this.timer.seconds = 0;
        this.timer.minutes++;
      }

      element.textContent = (this.timer.minutes ? this.timer.minutes > 9 ? this.timer.minutes : "0" + this.timer.minutes : "00") + ":" + (this.timer.seconds > 9 ? this.timer.seconds : "0" + this.timer.seconds);
    }
  }]);

  return Processor;
}();



var DoneButton = /*#__PURE__*/function () {
  function DoneButton() {
    _classCallCheck(this, DoneButton);

    this.createElement();
  }

  _createClass(DoneButton, [{
    key: "createElement",
    value: function createElement() {
      var button = document.createElement('button');
      button.classList.add('button', 'done-deselect');
      button.innerText = i18n.DONE_DESELECT;
      button.dataset.close = true;
      this.el = button;
    }
  }]);

  return DoneButton;
}();

/***/ }),

/***/ "./editing/js/modules/data-storage.js":
/*!********************************************!*\
  !*** ./editing/js/modules/data-storage.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return ColumnDataStorage; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var ColumnDataStorage = /*#__PURE__*/function () {
  /**
   * @param {Object} editvalue
   */
  function ColumnDataStorage(editvalue) {
    _classCallCheck(this, ColumnDataStorage);

    this.name = editvalue.column_name;
    this.id = editvalue.id;
    this.current_revision = 0;
    this.revisions = [editvalue.value];
    this.formattedValue = editvalue.formatted_value ? editvalue.formatted_value : editvalue.value;
  }

  _createClass(ColumnDataStorage, [{
    key: "getCell",
    value: function getCell() {
      return AdminColumns.Table.Cells.get(this.id, this.name);
    }
  }, {
    key: "setFormattedValue",
    value: function setFormattedValue(value) {
      this.formattedValue = value;
    }
  }, {
    key: "getFormattedValue",
    value: function getFormattedValue() {
      return this.formattedValue;
    }
  }, {
    key: "setCurrentRevision",
    value: function setCurrentRevision(index) {
      this.current_revision = index;
      return this;
    }
  }, {
    key: "getCurrentRevision",
    value: function getCurrentRevision() {
      return this.current_revision;
    }
  }, {
    key: "getValue",
    value: function getValue() {
      return this.revisions[this.getCurrentRevision()];
    }
  }, {
    key: "updateValue",
    value: function updateValue(content) {
      return this.getCell().el.target.innerHTML = content ? content : this.getValue();
    }
  }, {
    key: "storeRevision",
    value: function storeRevision(value) {
      var num_deletes = this.revisions.length - this.getCurrentRevision() - 1; // Remove any revision that are newer than the current revision

      for (var i = 0; i < num_deletes; i++) {
        this.revisions.pop();
      }

      this.revisions.push(value);
      this.setCurrentRevision(this.getCurrentRevision() + 1);
      return this;
    }
  }, {
    key: "save",
    value: function save() {
      return jQuery.ajax({
        url: ajaxurl,
        data: {
          action: 'acp_editing_single_request',
          method: 'save',
          list_screen: AC.list_screen,
          layout: AC.layout,
          column: this.name,
          id: this.id,
          screen: AC.screen,
          value: this.getValue(),
          _ajax_nonce: AC.ajax_nonce
        },
        method: 'POST',
        dataType: 'json'
      });
    }
  }, {
    key: "undo",
    value: function undo() {
      if (!this.prev()) {
        return false;
      }

      return true;
    }
  }, {
    key: "redo",
    value: function redo() {
      if (!this.next()) {
        return false;
      }

      return true;
    }
    /**
     * @returns {boolean}
     */

  }, {
    key: "prev",
    value: function prev() {
      var currentRevision = this.getCurrentRevision();

      if (currentRevision === 0) {
        return false;
      }

      this.setCurrentRevision(currentRevision - 1);
      return true;
    }
    /**
     * @returns {boolean}
     */

  }, {
    key: "next",
    value: function next() {
      var currentRevision = this.getCurrentRevision();

      if (currentRevision === this.revisions.length - 1) {
        return false;
      }

      this.setCurrentRevision(currentRevision + 1);
      return true;
    }
  }]);

  return ColumnDataStorage;
}();



/***/ }),

/***/ "./editing/js/modules/editables.js":
/*!*****************************************!*\
  !*** ./editing/js/modules/editables.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Editables; });
/* harmony import */ var _editable_abstract__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../editable/abstract */ "./editing/js/editable/abstract.js");
/* harmony import */ var _editable_checkbox__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../editable/checkbox */ "./editing/js/editable/checkbox.js");
/* harmony import */ var _editable_color__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../editable/color */ "./editing/js/editable/color.js");
/* harmony import */ var _editable_date__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../editable/date */ "./editing/js/editable/date.js");
/* harmony import */ var _editable_date_time__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../editable/date_time */ "./editing/js/editable/date_time.js");
/* harmony import */ var _editable_text__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../editable/text */ "./editing/js/editable/text.js");
/* harmony import */ var _editable_textarea__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../editable/textarea */ "./editing/js/editable/textarea.js");
/* harmony import */ var _editable_toggle__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../editable/toggle */ "./editing/js/editable/toggle.js");
/* harmony import */ var _editable_fullname__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../editable/fullname */ "./editing/js/editable/fullname.js");
/* harmony import */ var _editable_select__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../editable/select */ "./editing/js/editable/select.js");
/* harmony import */ var _editable_select2_dropdown__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../editable/select2_dropdown */ "./editing/js/editable/select2_dropdown.js");
/* harmony import */ var _editable_media__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../editable/media */ "./editing/js/editable/media.js");
/* harmony import */ var _editable_multi_input__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../editable/multi_input */ "./editing/js/editable/multi_input.js");
/* harmony import */ var _editable_taxonomy__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../editable/taxonomy */ "./editing/js/editable/taxonomy.js");
/* harmony import */ var _editable_wysiwyg__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../editable/wysiwyg */ "./editing/js/editable/wysiwyg.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

















var Editables = /*#__PURE__*/function () {
  function Editables() {
    _classCallCheck(this, Editables);

    this.editables = {};
  }

  _createClass(Editables, [{
    key: "initEditables",
    value: function initEditables() {
      this.editables._abstract = _editable_abstract__WEBPACK_IMPORTED_MODULE_0__["default"];
      this.editables.text = _editable_text__WEBPACK_IMPORTED_MODULE_5__["default"];
      this.editables.textarea = _editable_textarea__WEBPACK_IMPORTED_MODULE_6__["default"];
      this.editables.number = _editable_text__WEBPACK_IMPORTED_MODULE_5__["default"];
      this.editables.checklist = _editable_checkbox__WEBPACK_IMPORTED_MODULE_1__["default"];
      this.editables.color = _editable_color__WEBPACK_IMPORTED_MODULE_2__["default"];
      this.editables.select = _editable_select__WEBPACK_IMPORTED_MODULE_9__["default"];
      this.editables.select2 = _editable_select2_dropdown__WEBPACK_IMPORTED_MODULE_10__["default"];
      this.editables.date = _editable_date__WEBPACK_IMPORTED_MODULE_3__["default"];
      this.editables.fullname = _editable_fullname__WEBPACK_IMPORTED_MODULE_8__["default"];
      this.editables.date_time = _editable_date_time__WEBPACK_IMPORTED_MODULE_4__["default"];
      this.editables.toggle = _editable_toggle__WEBPACK_IMPORTED_MODULE_7__["default"];
      this.editables.wp_library = _editable_media__WEBPACK_IMPORTED_MODULE_11__["default"];
      this.editables.multi_input = _editable_multi_input__WEBPACK_IMPORTED_MODULE_12__["default"];
      this.editables.taxonomy = _editable_taxonomy__WEBPACK_IMPORTED_MODULE_13__["default"];
      this.editables.wysiwyg = _editable_wysiwyg__WEBPACK_IMPORTED_MODULE_14__["default"];
      document.dispatchEvent(new CustomEvent('AC_Editing_Register_Editables', {
        detail: this
      }));
    }
  }, {
    key: "registerEditable",
    value: function registerEditable(name, object) {
      this.editables[name] = object;
      return this;
    }
  }, {
    key: "get",
    value: function get(type) {
      if (!this.editables.hasOwnProperty(type)) {
        return false;
      }

      return this.editables[type];
    }
  }, {
    key: "abstract",
    value: function abstract() {
      return this.get('_abstract');
    }
  }]);

  return Editables;
}();



/***/ }),

/***/ "./editing/js/modules/event-emitter.js":
/*!*********************************************!*\
  !*** ./editing/js/modules/event-emitter.js ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return EventEmitter; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var EventEmitter = /*#__PURE__*/function () {
  function EventEmitter() {
    _classCallCheck(this, EventEmitter);

    this.listeners = new Map();
  }

  _createClass(EventEmitter, [{
    key: "addListener",
    value: function addListener(label, callback) {
      this.listeners.has(label) || this.listeners.set(label, []);
      this.listeners.get(label).push(callback);
    }
  }, {
    key: "removeListener",
    value: function removeListener(label, callback) {
      var listeners = this.listeners.get(label),
          index;

      if (listeners && listeners.length) {
        index = listeners.reduce(function (i, listener, index) {
          return EventEmitter.isFunction(listener) && listener === callback ? i = index : i;
        }, -1);

        if (index > -1) {
          listeners.splice(index, 1);
          this.listeners.set(label, listeners);
          return true;
        }
      }

      return false;
    }
  }, {
    key: "emit",
    value: function emit(label) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      var listeners = this.listeners.get(label);

      if (listeners && listeners.length) {
        listeners.forEach(function (listener) {
          listener.apply(void 0, args);
        });
        return true;
      }

      return false;
    }
  }], [{
    key: "isFunction",
    value: function isFunction(obj) {
      return typeof obj === 'function' || false;
    }
  }]);

  return EventEmitter;
}();



/***/ }),

/***/ "./editing/js/modules/helper.js":
/*!**************************************!*\
  !*** ./editing/js/modules/helper.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var Helper = /*#__PURE__*/function () {
  function Helper() {
    _classCallCheck(this, Helper);
  }

  _createClass(Helper, null, [{
    key: "escapeRegex",
    value: function escapeRegex(value) {
      return value.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }
    /**
     * Format a list of options from a storage model for use in X-editable
     *
     * @since 1.0
     *
     * @param {Array} options List of options, can be nested (1 level max). Options have their key as the input value and their value as the input label. Parents have string 'label' and array 'options' of options.
     * @returns {Array} List of options with parents with 'text' and 'children' and options with 'id' and 'text'
     */

  }, {
    key: "formatOptionsSelect2",
    value: function formatOptionsSelect2(options) {
      var foptions = [];

      if (typeof options === "undefined") {
        return foptions;
      }

      for (var i = 0; i < options.length; i++) {
        var parent = void 0;

        if (typeof options[i].options !== 'undefined') {
          parent = {
            text: options[i].label,
            children: []
          };

          for (var j in options[i].options) {
            if (options[i].options.hasOwnProperty(j)) {
              parent.children.push({
                value: options[i].options[j].value,
                id: options[i].options[j].value,
                text: options[i].options[j].label
              });
            }
          }
        } else {
          parent = {
            value: options[i].value,
            id: options[i].value,
            text: options[i].label
          };
        }

        foptions.push(parent);
      }

      return foptions;
    }
  }]);

  return Helper;
}();

module.exports = Helper;

/***/ }),

/***/ "./editing/js/modules/inline-edit-actions.js":
/*!***************************************************!*\
  !*** ./editing/js/modules/inline-edit-actions.js ***!
  \***************************************************/
/*! exports provided: Actions, MediaActions */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Actions", function() { return Actions; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "MediaActions", function() { return MediaActions; });
/* harmony import */ var _helpers_elements__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../helpers/elements */ "./editing/js/helpers/elements.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function () { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


var Actions = /*#__PURE__*/function () {
  function Actions(EditCell) {
    _classCallCheck(this, Actions);

    this.EditCell = EditCell;
    this.Cell = this.EditCell.Cell;
    this.reset();
  }
  /*
  Remove the actions from the cell
   */


  _createClass(Actions, [{
    key: "reset",
    value: function reset() {
      var container = this.Cell.el.querySelector('.acp-ie-controls__items');

      if (container) {
        container.remove();
      }
    }
  }, {
    key: "isRevisioningEnabled",
    value: function isRevisioningEnabled() {
      return !this.Cell.getSettings().editable.hasOwnProperty('disable_revisioning');
    }
  }, {
    key: "render",
    value: function render() {
      var container = Actions.createContainer();
      var valueCell = this.Cell.el.querySelector('.acp-ie-value');
      this.Cell.el.appendChild(container);

      if (valueCell) {
        Object(_helpers_elements__WEBPACK_IMPORTED_MODULE_0__["insertAfter"])(container, valueCell);
      }

      Actions.appendButton(this.renderEdit(), container);

      if (this.isRevisioningEnabled()) {
        Actions.appendButton(this.renderUndo(), container);
        Actions.appendButton(this.renderRedo(), container);
      }

      Actions.appendButton(this.renderClear(), container);
    }
  }, {
    key: "renderEdit",
    value: function renderEdit() {
      var _this = this;

      var el = Actions.createButtonElement(ACP_Editing.i18n.edit, '-edit');
      el.insertAdjacentHTML('beforeEnd', "<span class=\"dashicons dashicons-edit\"></span>");
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        _this.EditCell.openEditable();
      });
      return el;
    }
  }, {
    key: "renderUndo",
    value: function renderUndo() {
      var _this2 = this;

      var storage = this.Cell.dataStorage;

      if (storage.current_revision === 0) {
        return false;
      }

      var el = Actions.createButtonElement(ACP_Editing.i18n.undo, '-undo');
      el.insertAdjacentHTML('beforeEnd', "<span class=\"dashicons dashicons-undo\"></span>");
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        _this2.EditCell.undo();
      });
      return el;
    }
  }, {
    key: "renderRedo",
    value: function renderRedo() {
      var _this3 = this;

      var storage = this.Cell.dataStorage;

      if (storage.current_revision >= storage.revisions.length - 1) {
        return;
      }

      var el = Actions.createButtonElement(ACP_Editing.i18n.redo, '-redo');
      el.insertAdjacentHTML('beforeEnd', "<span class=\"dashicons dashicons-redo\"></span>");
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        _this3.EditCell.redo();
      });
      return el;
    }
  }, {
    key: "renderClear",
    value: function renderClear() {
      var _this4 = this;

      var storage = this.Cell.dataStorage;

      if (!this.Cell.getSettings().editable.clear_button) {
        return;
      }

      if (!storage.getValue()) {
        return;
      }

      var el = Actions.createButtonElement(ACP_Editing.i18n["delete"], '-clear');
      el.insertAdjacentHTML('beforeEnd', "<span class=\"dashicons dashicons-no-alt\"></span>");
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        _this4.EditCell.clear();
      });
      return el;
    }
  }], [{
    key: "createContainer",
    value: function createContainer() {
      var container = document.createElement('div');
      container.classList.add('acp-ie-controls');
      return container;
    }
  }, {
    key: "appendButton",
    value: function appendButton(button, element) {
      if (button) {
        element.appendChild(button);
      }
    }
  }, {
    key: "createButtonElement",
    value: function createButtonElement(title, className) {
      var button = document.createElement('span');
      button.classList.add('acp-ie-controls__item');
      button.classList.add(className);
      button.setAttribute('title', title);
      return button;
    }
  }]);

  return Actions;
}();
var MediaActions = /*#__PURE__*/function (_Actions) {
  _inherits(MediaActions, _Actions);

  var _super = _createSuper(MediaActions);

  function MediaActions() {
    _classCallCheck(this, MediaActions);

    return _super.apply(this, arguments);
  }

  _createClass(MediaActions, [{
    key: "render",
    value: function render() {
      this.wrapImages();
    }
  }, {
    key: "wrapImages",
    value: function wrapImages() {
      var images = this.Cell.el.querySelectorAll('.ac-image');
      var globalcontrols = this.Cell.el.querySelector('.acp-ie-controls');

      if (!images) {
        return;
      }

      images.forEach(function (image) {
        var id = image.dataset.mediaId;
        var wrapper = document.createElement('div');
        var controls = document.createElement('div');
        controls.classList.add('acp-ie-image-controls');
        wrapper.dataset.mediaId = id;
        wrapper.classList.add('acp-ie-image-item');
        image.parentNode.insertBefore(wrapper, image);
        wrapper.appendChild(image);
        wrapper.appendChild(controls);
      });

      if (this.Cell.getSettings().editable.multiple) {
        this.addSingleImageDelete();
        globalcontrols.classList.add('-multiple');
      } else {
        this.moveActions();
        globalcontrols.classList.add('-single');
      }
    }
  }, {
    key: "addSingleImageDelete",
    value: function addSingleImageDelete() {
      var _this5 = this;

      var images = this.Cell.el.querySelectorAll('.acp-ie-image-item');

      if (!images) {
        return;
      }

      images.forEach(function (image) {
        var controls = image.querySelector('.acp-ie-image-controls');

        var btn_remove = _this5.renderDelete();

        controls.append(btn_remove);
      });
    }
  }, {
    key: "moveActions",
    value: function moveActions() {
      var controls = this.Cell.el.querySelector('.acp-ie-controls');
      var imageControls = this.Cell.el.querySelector('.acp-ie-image-controls');

      if (!controls || !imageControls) {
        return;
      }

      controls.querySelectorAll('.acp-ie-controls__item').forEach(function (control) {
        imageControls.append(control);
      });
      imageControls.classList.add('-single');
    }
  }, {
    key: "renderDelete",
    value: function renderDelete() {
      var _this6 = this;

      var self = this;
      var btn_remove = MediaActions.createButtonElement(ACP_Editing.i18n["delete"], '-delete');
      btn_remove.insertAdjacentHTML('beforeend', "<span class=\"dashicons dashicons-no-alt\"></span>");
      btn_remove.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var item = e.target.closest('.acp-ie-image-item');
        var dataStorage = self.Cell.dataStorage;
        var value = dataStorage.getValue();

        if (Array.isArray(value)) {
          value = value.filter(function (e) {
            return parseInt(e) !== parseInt(item.dataset.mediaId);
          });
        } else {
          value = '';
        }

        _this6.EditCell.saveValue(value);
      });
      return btn_remove;
    }
  }, {
    key: "renderDownload",
    value: function renderDownload() {
      var _this7 = this;

      var el = Actions.createButtonElement(ACP_Editing.i18n.download, '-download');
      el.insertAdjacentHTML('beforeEnd', "<span class=\"dashicons dashicons-migrate\"></span>");
      el.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        _this7.EditCell.redo();
      });
      return el;
    }
  }]);

  return MediaActions;
}(Actions);

/***/ }),

/***/ "./editing/js/modules/inline-edit-cell.js":
/*!************************************************!*\
  !*** ./editing/js/modules/inline-edit-cell.js ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return InlineEditCell; });
/* harmony import */ var _inline_edit_actions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./inline-edit-actions */ "./editing/js/modules/inline-edit-actions.js");
/* harmony import */ var popper_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js");
/* harmony import */ var _helpers_elements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../helpers/elements */ "./editing/js/helpers/elements.js");
/* harmony import */ var _helpers_rows__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../helpers/rows */ "./editing/js/helpers/rows.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }





var Selectors = {
  CELL: 'acp-ie-editable',
  VALUE: 'acp-ie-value'
};

var InlineEditCell = /*#__PURE__*/function () {
  function InlineEditCell(InlineEdit, Cell, Middleware, editable_class) {
    _classCallCheck(this, InlineEditCell);

    this._editableClass = editable_class;
    this.InlineEdit = InlineEdit;
    this.Cell = Cell;
    this.Middleware = Middleware;
    this.Editable = null;
    this.Popper = null;
    this.wrapCellContent();
    this.attachEditable();
    this.insertControls();
    this.attachEvents();
  }

  _createClass(InlineEditCell, [{
    key: "attachEditable",
    value: function attachEditable() {
      var _this = this;

      if (!this.Cell.dataStorage || this.Editable) {
        return;
      }

      var value = this.Cell.dataStorage.getFormattedValue();

      if (value === null) {
        return;
      }

      this.Editable = new this._editableClass(this.Middleware.getArgs());
      this.Editable.setValue(value);

      if (this.Cell.getObjectID()) {
        this.Editable.setObjectId(this.Cell.getObjectID());
      }

      this.Editable.Events.addListener('save', function (editable) {
        _this.saveValue(editable.getValue());
      });
      this.Editable.Events.addListener('close', function () {
        PopperGlobalEvents.destroy();

        _this.destroyPopper();
      });
    }
  }, {
    key: "attachEvents",
    value: function attachEvents() {
      var _this2 = this;

      this.getTargetElement().addEventListener('click', function (e) {
        if (!_this2.InlineEdit._enabled) {
          return;
        }

        e.preventDefault();
        e.stopPropagation();

        _this2.openEditable();
      });
      document.addEventListener('ACP_InlineEditing_Close', function () {
        if (_this2.Popper) {
          _this2.Editable.close();
        }
      });
    }
  }, {
    key: "saveValue",
    value: function saveValue(value) {
      var _this3 = this;

      this.Editable.toggleLoading(true);
      this.Cell.dataStorage.storeRevision(value).save().done(function (response) {
        if (!response.success) {
          _this3.Editable.toggleLoading(false);

          _this3.Editable.setError(response.data.message);

          return;
        }

        _this3.setValue(response.data);

        _this3.Editable.setValue(response.data.value).close();

        _this3.Editable.toggleLoading(false);

        var data = {
          column: _this3.Cell.getName(),
          item: _this3.Cell.getObjectID(),
          value: response.data.value
        };
        document.dispatchEvent(new CustomEvent('ACP_InlineEditing_After_Save', {
          detail: data
        }));
      });
    }
  }, {
    key: "openEditable",
    value: function openEditable() {
      var InlineEditable = AdminColumns.Editing.InlineEdit;
      InlineEditable.closeOpenEditables();
      var container = this.getPopperContainer();
      container.append(this.Editable.get());
      this.popper = new popper_js__WEBPACK_IMPORTED_MODULE_1__["default"](this.getTargetElement(), container, {
        modifiers: {
          arrow: {
            element: '.ac-edit-popper__caret'
          }
        }
      });

      InlineEditable._open_editables.push(this.Editable);

      PopperGlobalEvents.bind();
    }
    /**
     * Wraps the content of a cell for better control
     */

  }, {
    key: "wrapCellContent",
    value: function wrapCellContent() {
      var _this4 = this;

      var settings = this.Cell.getSettings().editable;
      var target = document.createElement('span');
      this.Cell.el.classList.add(Selectors.CELL);
      this.Cell.el.classList.add("".concat(Selectors.CELL, "--").concat(settings.type));
      target.classList.add(Selectors.VALUE);

      while (this.Cell.el.firstChild) {
        target.appendChild(this.Cell.el.firstChild);
      }

      this.Cell.el.append(target);
      var InvalidElements = target.querySelectorAll('.row-actions');

      if (InvalidElements) {
        InvalidElements.forEach(function (element) {
          _this4.Cell.el.appendChild(element);
        });
      }

      return target;
    }
  }, {
    key: "undo",
    value: function undo() {
      var _this5 = this;

      var undo = this.Cell.dataStorage.undo();

      if (undo) {
        this.Cell.dataStorage.save().done(function (response) {
          _this5.setValue(response.data);

          _this5.Editable.setValue(response.data.value);
        });
      }
    }
  }, {
    key: "redo",
    value: function redo() {
      var _this6 = this;

      var redo = this.Cell.dataStorage.redo();

      if (redo) {
        this.Cell.dataStorage.save().done(function (response) {
          _this6.setValue(response.data);

          _this6.Editable.setValue(response.data.value);
        });
      }
    }
  }, {
    key: "clear",
    value: function clear() {
      var _this7 = this;

      this.Cell.dataStorage.storeRevision('').save().done(function (response) {
        _this7.setValue(response.data);

        _this7.Editable.setValue(response.data.value);
      });
    }
  }, {
    key: "setCellValue",
    value: function setCellValue(value) {
      var rowActions = this.Cell.el.querySelector('.row-actions');
      this.Cell.el.innerHTML = value;

      if (rowActions) {
        this.Cell.el.append(rowActions);
      }

      this.afterSetValue();
    }
  }, {
    key: "setValue",
    value: function setValue(data) {
      var _this8 = this;

      var displayValue = data.display_value;

      if (displayValue) {
        this.setCellValue(displayValue);
      } else {
        this.getTargetElement().style.backgroundColor = '#f2f093';
        this.getRowsHTML([data.id]).done(function (response) {
          if (!response.hasOwnProperty('data')) {
            return;
          }

          _this8.setValueFromRowHTML(response.data.table_rows[data.id]);
        });
      }
    }
    /**
     * Give the value cell a visual feedback
     */

  }, {
    key: "valueUpdateVisual",
    value: function valueUpdateVisual() {
      var el = this.getTargetElement();

      if (!el) {
        return;
      }

      el.style.animation = 'acp-value-highlight .9s';
    }
    /**
     * Return the element that contains the value
     *
     * @returns {any}
     */

  }, {
    key: "getTargetElement",
    value: function getTargetElement() {
      var el = this.Cell.el.querySelector(".".concat(Selectors.VALUE));
      return el ? el : false;
    }
  }, {
    key: "insertControls",
    value: function insertControls() {
      var actions = new _inline_edit_actions__WEBPACK_IMPORTED_MODULE_0__["Actions"](this);
      actions.render();

      if (this.Middleware.hasMediaActions()) {
        var mediaActions = new _inline_edit_actions__WEBPACK_IMPORTED_MODULE_0__["MediaActions"](this);
        mediaActions.render();
      }

      this.moveActions();
    }
  }, {
    key: "moveActions",
    value: function moveActions() {
      var settings = this.Cell.getSettings().editable;

      if (!settings.hasOwnProperty('js') || !settings.js.hasOwnProperty('selector')) {
        return;
      }

      var el = this.Cell.el.querySelector(settings.js.selector);
      var actions = this.Cell.el.querySelector('.acp-ie-controls');
      Object(_helpers_elements__WEBPACK_IMPORTED_MODULE_2__["insertAfter"])(actions, el);
    }
  }, {
    key: "getRowsHTML",
    value: function getRowsHTML() {
      var ids = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
      return jQuery.ajax({
        url: window.location.href,
        method: 'post',
        data: {
          ac_action: 'get_table_rows',
          ac_ids: ids,
          _ajax_nonce: AC.ajax_nonce
        }
      });
    }
  }, {
    key: "afterSetValue",
    value: function afterSetValue() {
      this.wrapCellContent();
      this.valueUpdateVisual();
      this.insertControls();
      this.attachEvents();
      this.Cell.el.dispatchEvent(new CustomEvent('ACP_InlineEditing_After_SetValue'));
    }
  }, {
    key: "setValueFromRowHTML",
    value: function setValueFromRowHTML(rowHTML) {
      var column = this.Cell.getSettings();
      this.setCellValue(Object(_helpers_rows__WEBPACK_IMPORTED_MODULE_3__["getColumnValueFromRowHTML"])(rowHTML, column.name));
    }
  }, {
    key: "destroyPopper",
    value: function destroyPopper() {
      document.querySelectorAll('.ac-edit-popper').forEach(function (popper) {
        popper.remove();
      });

      if (this.popper) {
        this.popper.destroy();
      }
    }
  }, {
    key: "getPopperContainer",
    value: function getPopperContainer() {
      this.Popper = document.createElement('div');
      this.Popper.classList.add('ac-edit-popper');
      this.Popper.insertAdjacentHTML('afterbegin', '<span class="ac-edit-popper__caret">');
      document.body.append(this.Popper);
      return this.Popper;
    }
  }]);

  return InlineEditCell;
}();



var PopperGlobalEvents = /*#__PURE__*/function () {
  function PopperGlobalEvents() {
    _classCallCheck(this, PopperGlobalEvents);
  }

  _createClass(PopperGlobalEvents, null, [{
    key: "bind",
    value: function bind() {
      document.addEventListener('keyup', PopperGlobalEvents.escapeEvent);
      document.addEventListener('click', PopperGlobalEvents.clickEvent);
      document.querySelectorAll('.select2-container--acs2').forEach(function (c) {
        c.addEventListener('click', function (e) {
          e.stopPropagation();
        });
      });
    }
  }, {
    key: "destroy",
    value: function destroy() {
      document.removeEventListener('keyup', PopperGlobalEvents.escapeEvent);
      document.removeEventListener('click', PopperGlobalEvents.clickEvent);
    }
  }, {
    key: "escapeEvent",
    value: function escapeEvent(e) {
      if (e.key === "Escape") {
        document.dispatchEvent(new CustomEvent('ACP_InlineEditing_Close'));
      }
    }
  }, {
    key: "clickEvent",
    value: function clickEvent(e) {
      if (e.target === document.body) {
        return;
      }

      if (PopperGlobalEvents.checkInvalidParentClasses(e.target)) {
        return;
      }

      document.dispatchEvent(new CustomEvent('ACP_InlineEditing_Close'));
    }
  }, {
    key: "checkInvalidParentClasses",
    value: function checkInvalidParentClasses(el) {
      var isInvalid = false;
      var invalidClasses = ['wp-editor-wrap', 'media-modal', 'select2-container', 'ui-datepicker', 'ui-datepicker-header', 'ui-datepicker-buttonpane'];
      invalidClasses.map(function (c) {
        if (el.classList.contains(c)) isInvalid = true;
      });

      if (isInvalid) {
        return true;
      }

      if (!el.parentElement) {
        return false;
      }

      return PopperGlobalEvents.checkInvalidParentClasses(el.parentElement);
    }
  }]);

  return PopperGlobalEvents;
}();

/***/ }),

/***/ "./editing/js/modules/inline-edit.js":
/*!*******************************************!*\
  !*** ./editing/js/modules/inline-edit.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return InlineEdit; });
/* harmony import */ var _middleware_editable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../middleware/editable */ "./editing/js/middleware/editable.js");
/* harmony import */ var _inline_edit_cell__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./inline-edit-cell */ "./editing/js/modules/inline-edit-cell.js");
/* harmony import */ var _data_storage__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./data-storage */ "./editing/js/modules/data-storage.js");
/* harmony import */ var _helpers_ajax__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../helpers/ajax */ "./editing/js/helpers/ajax.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }






var InlineEdit = /*#__PURE__*/function () {
  function InlineEdit(Table, Editables, enabled) {
    _classCallCheck(this, InlineEdit);

    this.Editables = Editables;
    this.Table = Table;
    this.Middleware = new _middleware_editable__WEBPACK_IMPORTED_MODULE_0__["default"](this.Editables);
    this._enabled = enabled;
    this._initialised = false;
    this._open_editables = [];

    this._setTableClass();

    this._addEvents();

    this._initCells();

    this._setInputState();
  }

  _createClass(InlineEdit, [{
    key: "isEnabled",
    value: function isEnabled() {
      return this._enabled;
    }
  }, {
    key: "enable",
    value: function enable() {
      this._enabled = true;

      this._storePreference();

      this._setTableClass();

      this._initCells();

      this._setInputState();
    }
  }, {
    key: "_setInputState",
    value: function _setInputState() {
      var input = document.getElementById('acp-enable-editing');

      if (input) {
        input.checked = this.isEnabled();
      }
    }
  }, {
    key: "disable",
    value: function disable() {
      this._enabled = false;

      this._storePreference();

      this._setTableClass();
    }
    /**
     * @param {Boolean} initialized
     * @returns {InlineEdit}
     */

  }, {
    key: "setInitialised",
    value: function setInitialised() {
      var initialized = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      this._initialised = initialized;
      return this;
    }
    /**
     * @returns {boolean}
     */

  }, {
    key: "isInitialised",
    value: function isInitialised() {
      return this._initialised;
    }
  }, {
    key: "_setTableClass",
    value: function _setTableClass() {
      var table = this.Table.el;
      table.classList.add('acp-ie-table');

      if (this.isEnabled()) {
        table.classList.add('acp-ie-enabled');
        table.classList.remove('acp-ie-disabled');
      } else {
        table.classList.add('acp-ie-disabled');
        table.classList.remove('acp-ie-enabled');
      }
    }
  }, {
    key: "_initCells",
    value: function _initCells() {
      var _this = this;

      if (this.isInitialised() || !this.isEnabled()) {
        return;
      }

      var columns = this.Table.Columns.getColumns();
      Object.keys(columns).forEach(function (column_name) {
        var settings = columns[column_name];

        if (!settings || !settings.editable || !settings.editable.type) {
          return;
        }

        var middleware = _this.Middleware.get(settings);

        var editable = middleware.getEditable();

        if (!editable) {
          console.info("".concat(settings.editable.type, " does not exist or is not loaded"));
          return;
        }

        _this.Table.Cells.getByName(column_name).forEach(function (cell) {
          if (cell.hasOwnProperty('IEdit')) {
            return;
          }

          if (cell.dataStorage) {
            cell.IEdit = new _inline_edit_cell__WEBPACK_IMPORTED_MODULE_1__["default"](_this, cell, middleware, editable);
          }
        });
      });
      this.setInitialised();
    }
    /**
     * Init a Single Cell
     * @param cell
     */

  }, {
    key: "initCell",
    value: function initCell(cell) {
      var settings = cell.getSettings();

      if (!settings || !settings.editable || !settings.editable.type || !settings.editable.inline_edit || cell.hasOwnProperty('IEdit')) {
        return;
      }

      var middleware = this.Middleware.get(settings);
      var editable = middleware.getEditable();

      if (!editable) {
        return;
      }

      if (!cell.dataStorage) {
        cell.dataStorage = new _data_storage__WEBPACK_IMPORTED_MODULE_2__["default"]({
          column_name: cell.getName(),
          id: cell.getObjectID()
        });
      }

      cell.IEdit = new _inline_edit_cell__WEBPACK_IMPORTED_MODULE_1__["default"](this, cell, middleware, editable);
    }
  }, {
    key: "initRow",
    value: function initRow(id) {
      var self = this;
      Object(_helpers_ajax__WEBPACK_IMPORTED_MODULE_3__["getEditableValues"])([id]).done(function (response) {
        response.data.editable_values.forEach(function (item) {
          var cell = self.Table.Cells.get(item.id, item.column_name);
          self.initCell(cell);

          if (cell.IEdit && cell.IEdit.hasOwnProperty('Editable')) {
            cell.IEdit.Editable.setValue(item.value);
          }
        });
      });
    }
  }, {
    key: "updateRowCells",
    value: function updateRowCells(e) {
      var _this2 = this;

      var row = e.target;

      var id = this.Table._getIDFromRow(row);

      var columns = this.Table.Columns.getColumns();
      Object.keys(columns).forEach(function (column_name) {
        var cell = _this2.Table.Cells.get(id, column_name);

        if (cell) {
          cell.el = _this2.Table.getRowCellByName(row, column_name);

          if (cell.IEdit) {
            cell.IEdit.afterSetValue();
          }
        }
      });
    }
  }, {
    key: "_storePreference",
    value: function _storePreference() {
      var value = this.isEnabled() ? 1 : 0;
      return jQuery.post(ajaxurl, {
        action: 'acp_editing_single_request',
        method: 'editability_state',
        value: value,
        list_screen: AC.list_screen,
        layout: AC.layout,
        _ajax_nonce: AC.ajax_nonce
      });
    }
  }, {
    key: "_addEvents",
    value: function _addEvents() {
      var _this3 = this;

      var self = this;
      var input = document.getElementById('acp-enable-editing');

      if (input) {
        input.addEventListener('change', function () {
          if (input.checked) {
            self.enable();
          } else {
            self.disable();
          }
        });
      }

      document.addEventListener('ACP_InlineEditing_Close', function () {
        _this3.closeOpenEditables();
      });
      this.updateRowCell = this.updateRowCells.bind(this);
      jQuery(this.Table.el).on('updated', 'tr', this.updateRowCell);
    }
  }, {
    key: "closeOpenEditables",
    value: function closeOpenEditables() {
      this._open_editables.forEach(function (editable) {
        editable.close();
      });

      this._open_editables = [];
    }
  }]);

  return InlineEdit;
}();



/***/ }),

/***/ "./editing/js/modules/table-update.js":
/*!********************************************!*\
  !*** ./editing/js/modules/table-update.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return TableUpdate; });
/* harmony import */ var _helpers_rows__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../helpers/rows */ "./editing/js/helpers/rows.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var TableUpdate = /*#__PURE__*/function () {
  function TableUpdate() {
    _classCallCheck(this, TableUpdate);
  }

  _createClass(TableUpdate, [{
    key: "updateSelectedCells",
    value: function updateSelectedCells(column) {
      var _this = this;

      var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
      var ids = AdminColumns.Table.Selection.getIDs();

      if (ids.length === 0) {
        return;
      }

      this.getRows(ids).done(function (response) {
        if (!response.hasOwnProperty('data') || !response.data.hasOwnProperty('table_rows')) {
          return;
        }

        var rows = response.data.table_rows;
        Object.keys(rows).forEach(function (id) {
          var cell = AdminColumns.Table.Cells.get(id, column);
          var iEdit = cell.IEdit;

          if (iEdit) {
            iEdit.setValueFromRowHTML(rows[id]);
          } else {
            cell.getElement().innerHTML = Object(_helpers_rows__WEBPACK_IMPORTED_MODULE_0__["getColumnValueFromRowHTML"])(rows[id], column);
          }
        });

        _this.updateSelectedEditingCells(column, rows, value);
      });
    }
  }, {
    key: "updateSelectedEditingCells",
    value: function updateSelectedEditingCells(column, rows, value) {
      var ids = AdminColumns.Table.Selection.getIDs();

      if (ids.length === 0) {
        return;
      }

      var Editing_items = jQuery.ajax({
        url: ajaxurl,
        method: 'post',
        data: {
          action: 'acp_editing_single_request',
          method: 'get_editable_values',
          ids: ids,
          column: column,
          list_screen: AC.list_screen,
          layout: AC.layout,
          _ajax_nonce: AC.ajax_nonce
        }
      });
      Editing_items.done(function (response) {
        response.data.editable_values.forEach(function (setting) {
          if (setting.column_name !== column) {
            return;
          }

          var cell = AdminColumns.Table.Cells.get(setting.id, column);
          var iEdit = cell.IEdit;

          if (iEdit) {
            cell.dataStorage.storeRevision(value);
            iEdit.Editable.setValue(setting.value);
            iEdit.setValueFromRowHTML(rows[setting.id]);
          }
        });
      });
    }
  }, {
    key: "getRows",
    value: function getRows(ids) {
      return jQuery.ajax({
        url: window.location.href,
        method: 'post',
        data: {
          ac_action: 'get_table_rows',
          ac_ids: ids,
          _ajax_nonce: AC.ajax_nonce
        }
      });
    }
  }]);

  return TableUpdate;
}();



/***/ }),

/***/ "./editing/js/table.js":
/*!*****************************!*\
  !*** ./editing/js/table.js ***!
  \*****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var _modules_editables__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/editables */ "./editing/js/modules/editables.js");
/* harmony import */ var _modules_helper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/helper */ "./editing/js/modules/helper.js");
/* harmony import */ var _modules_helper__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_modules_helper__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _modules_bulk_edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/bulk-edit */ "./editing/js/modules/bulk-edit.js");
/* harmony import */ var _modules_inline_edit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modules/inline-edit */ "./editing/js/modules/inline-edit.js");
/* harmony import */ var _middleware_columns__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./middleware/columns */ "./editing/js/middleware/columns.js");
/* harmony import */ var _middleware_cells__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./middleware/cells */ "./editing/js/middleware/cells.js");
/* harmony import */ var _modules_table_update__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./modules/table-update */ "./editing/js/modules/table-update.js");
/* harmony import */ var _helpers_ajax__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./helpers/ajax */ "./editing/js/helpers/ajax.js");











__webpack_require__(/*! admin-columns-js/polyfill/customevent */ "./node_modules/admin-columns-js/polyfill/customevent.js");

__webpack_require__(/*! admin-columns-js/polyfill/nodelist */ "./node_modules/admin-columns-js/polyfill/nodelist.js");

__webpack_require__(/*! ./helpers/polyfill */ "./editing/js/helpers/polyfill.js");

global.AdminColumns = typeof AdminColumns !== "undefined" ? AdminColumns : {};
/*
 * DOM ready
 */

var isStoredListScreen = function isStoredListScreen() {
  return AC.hasOwnProperty('layout') && null !== AC.layout;
};

document.addEventListener("DOMContentLoaded", function () {
  if (!AdminColumns.hasOwnProperty('Table') || !isStoredListScreen()) {
    return;
  }

  _middleware_columns__WEBPACK_IMPORTED_MODULE_4__["default"].map(AdminColumns.Table.Columns, ACP_Editing_Columns);
  AdminColumns.Editing = {};
  AdminColumns.Editing.Editables = new _modules_editables__WEBPACK_IMPORTED_MODULE_0__["default"]();
  AdminColumns.Editing.Editables.initEditables();
  AdminColumns.Editing.Helper = _modules_helper__WEBPACK_IMPORTED_MODULE_1___default.a;

  if (hasBulkEditColumns(ACP_Editing_Columns)) {
    AdminColumns.Editing.BulkEdit = new _modules_bulk_edit__WEBPACK_IMPORTED_MODULE_2__["default"](AdminColumns.Table, AdminColumns.Editing.Editables);

    __webpack_require__(/*! ./modules/bulk-modal */ "./editing/js/modules/bulk-modal.js");
  }

  AdminColumns.Editing.TableUpdate = new _modules_table_update__WEBPACK_IMPORTED_MODULE_6__["default"]();

  if (hasInlineEditColumns(ACP_Editing_Columns)) {
    var Editing_items = Object(_helpers_ajax__WEBPACK_IMPORTED_MODULE_7__["getEditableValues"])(AdminColumns.Table._ids);
    Editing_items.done(function (response) {
      if (!response.success || !response.data) {
        console.info('Admin Columns Inline Edit could not be loaded');
        return;
      }

      if (!response.data.hasOwnProperty('editable_values')) {
        return;
      }

      var ie_enabled = ACP_Editing.inline_edit.hasOwnProperty('active') && ACP_Editing.inline_edit.active;

      if (ACP_Editing.inline_edit.hasOwnProperty('persistent') && ACP_Editing.inline_edit.persistent) {
        ie_enabled = true;
      }

      Object(_middleware_cells__WEBPACK_IMPORTED_MODULE_5__["default"])(AdminColumns.Table.Cells, response.data.editable_values);
      AdminColumns.Editing.InlineEdit = new _modules_inline_edit__WEBPACK_IMPORTED_MODULE_3__["default"](AdminColumns.Table, AdminColumns.Editing.Editables, ie_enabled);
    });
  } else {
    var ieditToggle = document.querySelector('.ac-table-button.-toggle.-iedit');

    if (ieditToggle) {
      ieditToggle.remove();
    }
  }
});

var hasInlineEditColumns = function hasInlineEditColumns(columns) {
  for (var _i = 0, _Object$keys = Object.keys(columns); _i < _Object$keys.length; _i++) {
    var column_name = _Object$keys[_i];
    var settings = columns[column_name];

    if (settings.hasOwnProperty('inline_edit') && settings.inline_edit) {
      return true;
    }
  }

  return false;
};

var hasBulkEditColumns = function hasBulkEditColumns(columns) {
  for (var _i2 = 0, _Object$keys2 = Object.keys(columns); _i2 < _Object$keys2.length; _i2++) {
    var column_name = _Object$keys2[_i2];
    var settings = columns[column_name];

    if (settings.hasOwnProperty('bulk_edit') && settings.bulk_edit) {
      return true;
    }
  }

  return false;
};
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./editing/js/templates/editable.js":
/*!******************************************!*\
  !*** ./editing/js/templates/editable.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return EditableTemplate; });
/* harmony import */ var _form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./form */ "./editing/js/templates/form.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }



var EditableTemplate = /*#__PURE__*/function () {
  function EditableTemplate() {
    _classCallCheck(this, EditableTemplate);

    this.form = _form__WEBPACK_IMPORTED_MODULE_0__["default"];
    this.setElement();
    this.setError(false);
  }

  _createClass(EditableTemplate, [{
    key: "setTemplate",
    value: function setTemplate(template) {
      var inputs = this.el.querySelector('.aceditable__form__inputs');
      inputs.innerHTML = '';
      inputs.insertAdjacentHTML('afterBegin', template);
    }
  }, {
    key: "setError",
    value: function setError(error) {
      var error_element = this.el.querySelector('.aceditable__form__error');

      if (error) {
        error_element.innerHTML = error;
        error_element.style.display = 'block';
      } else {
        error_element.style.display = 'none';
      }
    }
  }, {
    key: "addClass",
    value: function addClass(className) {
      this.el.classList.add(className);
    }
  }, {
    key: "setSubmitButton",
    value: function setSubmitButton(label) {
      this.el.querySelector('[data-submit]').innerHTML = label;
    }
  }, {
    key: "showButtons",
    value: function showButtons() {
      var show = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      var controls = this.el.querySelector('.aceditable__form__controls');

      if (show) {
        controls.style.display = 'block';
      } else {
        controls.style.display = 'none';
      }
    }
  }, {
    key: "setElement",
    value: function setElement() {
      var element = document.createElement('div');
      element.classList.add('aceditable');
      element.innerHTML = EditableTemplate.getElementMarkup();
      this.el = element;
    }
  }], [{
    key: "getElementMarkup",
    value: function getElementMarkup() {
      return "\t\t\n\t\t\t<div class=\"aceditable__content\">\n\t\t\t\t<form class=\"aceditable__form\" autocomplete=\"nope\">\n\t\t\t\t\t<div class=\"aceditable__form__inputs\">\n\t\t\t\t\t\t\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class=\"aceditable__form__controls\">\n\t\t\t\t\t\t<button data-submit=\"\" class=\"button aceditable__button -primary\"><span class=\"dashicons dashicons-yes\"></span></button>\n\t\t\t\t\t\t<button data-cancel=\"\" class=\"button aceditable__button\"><span class=\"dashicons dashicons-no\"></span></button>\n\t\t\t\t\t</div>\n\t\t\t\t</form>\n\t\t\t\t<div class=\"aceditable__form__error\"></div>\n\t\t\t</div>\n\t\t\t<div class=\"aceditable__spinner spinner\"></div>\n\t\t";
    }
  }]);

  return EditableTemplate;
}();



/***/ }),

/***/ "./editing/js/templates/form.js":
/*!**************************************!*\
  !*** ./editing/js/templates/form.js ***!
  \**************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return FormTemplate; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var FormTemplate = /*#__PURE__*/function () {
  function FormTemplate() {
    _classCallCheck(this, FormTemplate);
  }

  _createClass(FormTemplate, null, [{
    key: "input",
    value: function input(name) {
      var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var attributes = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var input = document.createElement('input');
      input.setAttribute('name', name);

      if (value) {
        input.value = value;
      }

      attributes = Object.assign({}, {
        type: 'text'
      }, attributes);
      FormTemplate.setHtmlAttributes(input, attributes);
      return input;
    }
  }, {
    key: "textarea",
    value: function textarea(name) {
      var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'ss';
      var attributes = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var textarea = document.createElement('textarea');
      textarea.setAttribute('name', name);
      FormTemplate.setHtmlAttributes(textarea, attributes);
      return textarea;
    }
  }, {
    key: "checkbox",
    value: function checkbox(name, value, label) {
      var input = document.createElement('label');
      input.classList.add('input__checkbox');
      input.innerHTML = "<input type=\"checkbox\" name=\"".concat(name, "\" class=\"input__checkbox__input\" value=\"").concat(value, "\"><span class=\"input__checkbox__label\">").concat(label, "</span>");
      return input;
    }
  }, {
    key: "setHtmlAttributes",
    value: function setHtmlAttributes(input, attributes) {
      Object.keys(attributes).forEach(function (attribute) {
        var value = attributes[attribute];
        input.setAttribute(attribute, value);
      });
    }
  }, {
    key: "inputGroup",
    value: function inputGroup(label, input) {
      return "\n\t\t<div class=\"input__group\">\n\t\t\t<label>".concat(label, "</label>\n\t\t\t<div class=\"input__controlgroup\">").concat(input, "</div>\n\t\t</div>\n\t\t");
    }
  }]);

  return FormTemplate;
}();



/***/ }),

/***/ "./node_modules/admin-columns-js/helper/strings.js":
/*!*********************************************************!*\
  !*** ./node_modules/admin-columns-js/helper/strings.js ***!
  \*********************************************************/
/*! exports provided: toPixel, Format, isFloat */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toPixel", function() { return toPixel; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Format", function() { return Format; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isFloat", function() { return isFloat; });
/**
 * Return a pixel string -> 4px
 *
 * @param number
 * @returns {string}
 */
function toPixel( number ) {
	return number + 'px';
}

/**
 * Javascript version of sprintf
 *
 * Usage: Format( '{0} is {1}', 'ES6', 'Awesome' );
 *
 * @param {String} format
 * @returns {*}
 */
function Format( format ) {
	let args = Array.prototype.slice.call( arguments, 1 );
	return format.replace( /{(\d+)}/g, function( match, number ) {
		return typeof args[ number ] !== 'undefined'
			? args[ number ]
			: match
			;
	} );
}

function isFloat( value, decimal_point_regex ) {
	let regex = new RegExp( "^[0-9]+((\." + decimal_point_regex + ")[0-9]+)?$" );

	return value.match( regex );
}

/***/ }),

/***/ "./node_modules/admin-columns-js/polyfill/customevent.js":
/*!***************************************************************!*\
  !*** ./node_modules/admin-columns-js/polyfill/customevent.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/** CustomEvent Polyfill */
(function() {

	if ( typeof window.CustomEvent === "function" ) {
		return false;
	}

	function CustomEvent( event, params ) {
		params = params || { bubbles : false, cancelable : false, detail : undefined };
		let evt = document.createEvent( 'CustomEvent' );
		evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
		return evt;
	}

	CustomEvent.prototype = window.Event.prototype;

	window.CustomEvent = CustomEvent;
})();

/***/ }),

/***/ "./node_modules/admin-columns-js/polyfill/nodelist.js":
/*!************************************************************!*\
  !*** ./node_modules/admin-columns-js/polyfill/nodelist.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

if ( window.NodeList && !NodeList.prototype.forEach ) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}

/***/ }),

/***/ "./node_modules/popper.js/dist/esm/popper.js":
/*!***************************************************!*\
  !*** ./node_modules/popper.js/dist/esm/popper.js ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/**!
 * @fileOverview Kickass library to create and place poppers near their reference elements.
 * @version 1.16.1
 * @license
 * Copyright (c) 2016 Federico Zivolo and contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
var isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined' && typeof navigator !== 'undefined';

var timeoutDuration = function () {
  var longerTimeoutBrowsers = ['Edge', 'Trident', 'Firefox'];
  for (var i = 0; i < longerTimeoutBrowsers.length; i += 1) {
    if (isBrowser && navigator.userAgent.indexOf(longerTimeoutBrowsers[i]) >= 0) {
      return 1;
    }
  }
  return 0;
}();

function microtaskDebounce(fn) {
  var called = false;
  return function () {
    if (called) {
      return;
    }
    called = true;
    window.Promise.resolve().then(function () {
      called = false;
      fn();
    });
  };
}

function taskDebounce(fn) {
  var scheduled = false;
  return function () {
    if (!scheduled) {
      scheduled = true;
      setTimeout(function () {
        scheduled = false;
        fn();
      }, timeoutDuration);
    }
  };
}

var supportsMicroTasks = isBrowser && window.Promise;

/**
* Create a debounced version of a method, that's asynchronously deferred
* but called in the minimum time possible.
*
* @method
* @memberof Popper.Utils
* @argument {Function} fn
* @returns {Function}
*/
var debounce = supportsMicroTasks ? microtaskDebounce : taskDebounce;

/**
 * Check if the given variable is a function
 * @method
 * @memberof Popper.Utils
 * @argument {Any} functionToCheck - variable to check
 * @returns {Boolean} answer to: is a function?
 */
function isFunction(functionToCheck) {
  var getType = {};
  return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}

/**
 * Get CSS computed property of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Eement} element
 * @argument {String} property
 */
function getStyleComputedProperty(element, property) {
  if (element.nodeType !== 1) {
    return [];
  }
  // NOTE: 1 DOM access here
  var window = element.ownerDocument.defaultView;
  var css = window.getComputedStyle(element, null);
  return property ? css[property] : css;
}

/**
 * Returns the parentNode or the host of the element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} parent
 */
function getParentNode(element) {
  if (element.nodeName === 'HTML') {
    return element;
  }
  return element.parentNode || element.host;
}

/**
 * Returns the scrolling parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} scroll parent
 */
function getScrollParent(element) {
  // Return body, `getScroll` will take care to get the correct `scrollTop` from it
  if (!element) {
    return document.body;
  }

  switch (element.nodeName) {
    case 'HTML':
    case 'BODY':
      return element.ownerDocument.body;
    case '#document':
      return element.body;
  }

  // Firefox want us to check `-x` and `-y` variations as well

  var _getStyleComputedProp = getStyleComputedProperty(element),
      overflow = _getStyleComputedProp.overflow,
      overflowX = _getStyleComputedProp.overflowX,
      overflowY = _getStyleComputedProp.overflowY;

  if (/(auto|scroll|overlay)/.test(overflow + overflowY + overflowX)) {
    return element;
  }

  return getScrollParent(getParentNode(element));
}

/**
 * Returns the reference node of the reference object, or the reference object itself.
 * @method
 * @memberof Popper.Utils
 * @param {Element|Object} reference - the reference element (the popper will be relative to this)
 * @returns {Element} parent
 */
function getReferenceNode(reference) {
  return reference && reference.referenceNode ? reference.referenceNode : reference;
}

var isIE11 = isBrowser && !!(window.MSInputMethodContext && document.documentMode);
var isIE10 = isBrowser && /MSIE 10/.test(navigator.userAgent);

/**
 * Determines if the browser is Internet Explorer
 * @method
 * @memberof Popper.Utils
 * @param {Number} version to check
 * @returns {Boolean} isIE
 */
function isIE(version) {
  if (version === 11) {
    return isIE11;
  }
  if (version === 10) {
    return isIE10;
  }
  return isIE11 || isIE10;
}

/**
 * Returns the offset parent of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} offset parent
 */
function getOffsetParent(element) {
  if (!element) {
    return document.documentElement;
  }

  var noOffsetParent = isIE(10) ? document.body : null;

  // NOTE: 1 DOM access here
  var offsetParent = element.offsetParent || null;
  // Skip hidden elements which don't have an offsetParent
  while (offsetParent === noOffsetParent && element.nextElementSibling) {
    offsetParent = (element = element.nextElementSibling).offsetParent;
  }

  var nodeName = offsetParent && offsetParent.nodeName;

  if (!nodeName || nodeName === 'BODY' || nodeName === 'HTML') {
    return element ? element.ownerDocument.documentElement : document.documentElement;
  }

  // .offsetParent will return the closest TH, TD or TABLE in case
  // no offsetParent is present, I hate this job...
  if (['TH', 'TD', 'TABLE'].indexOf(offsetParent.nodeName) !== -1 && getStyleComputedProperty(offsetParent, 'position') === 'static') {
    return getOffsetParent(offsetParent);
  }

  return offsetParent;
}

function isOffsetContainer(element) {
  var nodeName = element.nodeName;

  if (nodeName === 'BODY') {
    return false;
  }
  return nodeName === 'HTML' || getOffsetParent(element.firstElementChild) === element;
}

/**
 * Finds the root node (document, shadowDOM root) of the given element
 * @method
 * @memberof Popper.Utils
 * @argument {Element} node
 * @returns {Element} root node
 */
function getRoot(node) {
  if (node.parentNode !== null) {
    return getRoot(node.parentNode);
  }

  return node;
}

/**
 * Finds the offset parent common to the two provided nodes
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element1
 * @argument {Element} element2
 * @returns {Element} common offset parent
 */
function findCommonOffsetParent(element1, element2) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element1 || !element1.nodeType || !element2 || !element2.nodeType) {
    return document.documentElement;
  }

  // Here we make sure to give as "start" the element that comes first in the DOM
  var order = element1.compareDocumentPosition(element2) & Node.DOCUMENT_POSITION_FOLLOWING;
  var start = order ? element1 : element2;
  var end = order ? element2 : element1;

  // Get common ancestor container
  var range = document.createRange();
  range.setStart(start, 0);
  range.setEnd(end, 0);
  var commonAncestorContainer = range.commonAncestorContainer;

  // Both nodes are inside #document

  if (element1 !== commonAncestorContainer && element2 !== commonAncestorContainer || start.contains(end)) {
    if (isOffsetContainer(commonAncestorContainer)) {
      return commonAncestorContainer;
    }

    return getOffsetParent(commonAncestorContainer);
  }

  // one of the nodes is inside shadowDOM, find which one
  var element1root = getRoot(element1);
  if (element1root.host) {
    return findCommonOffsetParent(element1root.host, element2);
  } else {
    return findCommonOffsetParent(element1, getRoot(element2).host);
  }
}

/**
 * Gets the scroll value of the given element in the given side (top and left)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {String} side `top` or `left`
 * @returns {number} amount of scrolled pixels
 */
function getScroll(element) {
  var side = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'top';

  var upperSide = side === 'top' ? 'scrollTop' : 'scrollLeft';
  var nodeName = element.nodeName;

  if (nodeName === 'BODY' || nodeName === 'HTML') {
    var html = element.ownerDocument.documentElement;
    var scrollingElement = element.ownerDocument.scrollingElement || html;
    return scrollingElement[upperSide];
  }

  return element[upperSide];
}

/*
 * Sum or subtract the element scroll values (left and top) from a given rect object
 * @method
 * @memberof Popper.Utils
 * @param {Object} rect - Rect object you want to change
 * @param {HTMLElement} element - The element from the function reads the scroll values
 * @param {Boolean} subtract - set to true if you want to subtract the scroll values
 * @return {Object} rect - The modifier rect object
 */
function includeScroll(rect, element) {
  var subtract = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var scrollTop = getScroll(element, 'top');
  var scrollLeft = getScroll(element, 'left');
  var modifier = subtract ? -1 : 1;
  rect.top += scrollTop * modifier;
  rect.bottom += scrollTop * modifier;
  rect.left += scrollLeft * modifier;
  rect.right += scrollLeft * modifier;
  return rect;
}

/*
 * Helper to detect borders of a given element
 * @method
 * @memberof Popper.Utils
 * @param {CSSStyleDeclaration} styles
 * Result of `getStyleComputedProperty` on the given element
 * @param {String} axis - `x` or `y`
 * @return {number} borders - The borders size of the given axis
 */

function getBordersSize(styles, axis) {
  var sideA = axis === 'x' ? 'Left' : 'Top';
  var sideB = sideA === 'Left' ? 'Right' : 'Bottom';

  return parseFloat(styles['border' + sideA + 'Width']) + parseFloat(styles['border' + sideB + 'Width']);
}

function getSize(axis, body, html, computedStyle) {
  return Math.max(body['offset' + axis], body['scroll' + axis], html['client' + axis], html['offset' + axis], html['scroll' + axis], isIE(10) ? parseInt(html['offset' + axis]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Top' : 'Left')]) + parseInt(computedStyle['margin' + (axis === 'Height' ? 'Bottom' : 'Right')]) : 0);
}

function getWindowSizes(document) {
  var body = document.body;
  var html = document.documentElement;
  var computedStyle = isIE(10) && getComputedStyle(html);

  return {
    height: getSize('Height', body, html, computedStyle),
    width: getSize('Width', body, html, computedStyle)
  };
}

var classCallCheck = function (instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
};

var createClass = function () {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }

  return function (Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
}();





var defineProperty = function (obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
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

var _extends = Object.assign || function (target) {
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

/**
 * Given element offsets, generate an output similar to getBoundingClientRect
 * @method
 * @memberof Popper.Utils
 * @argument {Object} offsets
 * @returns {Object} ClientRect like output
 */
function getClientRect(offsets) {
  return _extends({}, offsets, {
    right: offsets.left + offsets.width,
    bottom: offsets.top + offsets.height
  });
}

/**
 * Get bounding client rect of given element
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} element
 * @return {Object} client rect
 */
function getBoundingClientRect(element) {
  var rect = {};

  // IE10 10 FIX: Please, don't ask, the element isn't
  // considered in DOM in some circumstances...
  // This isn't reproducible in IE10 compatibility mode of IE11
  try {
    if (isIE(10)) {
      rect = element.getBoundingClientRect();
      var scrollTop = getScroll(element, 'top');
      var scrollLeft = getScroll(element, 'left');
      rect.top += scrollTop;
      rect.left += scrollLeft;
      rect.bottom += scrollTop;
      rect.right += scrollLeft;
    } else {
      rect = element.getBoundingClientRect();
    }
  } catch (e) {}

  var result = {
    left: rect.left,
    top: rect.top,
    width: rect.right - rect.left,
    height: rect.bottom - rect.top
  };

  // subtract scrollbar size from sizes
  var sizes = element.nodeName === 'HTML' ? getWindowSizes(element.ownerDocument) : {};
  var width = sizes.width || element.clientWidth || result.width;
  var height = sizes.height || element.clientHeight || result.height;

  var horizScrollbar = element.offsetWidth - width;
  var vertScrollbar = element.offsetHeight - height;

  // if an hypothetical scrollbar is detected, we must be sure it's not a `border`
  // we make this check conditional for performance reasons
  if (horizScrollbar || vertScrollbar) {
    var styles = getStyleComputedProperty(element);
    horizScrollbar -= getBordersSize(styles, 'x');
    vertScrollbar -= getBordersSize(styles, 'y');

    result.width -= horizScrollbar;
    result.height -= vertScrollbar;
  }

  return getClientRect(result);
}

function getOffsetRectRelativeToArbitraryNode(children, parent) {
  var fixedPosition = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

  var isIE10 = isIE(10);
  var isHTML = parent.nodeName === 'HTML';
  var childrenRect = getBoundingClientRect(children);
  var parentRect = getBoundingClientRect(parent);
  var scrollParent = getScrollParent(children);

  var styles = getStyleComputedProperty(parent);
  var borderTopWidth = parseFloat(styles.borderTopWidth);
  var borderLeftWidth = parseFloat(styles.borderLeftWidth);

  // In cases where the parent is fixed, we must ignore negative scroll in offset calc
  if (fixedPosition && isHTML) {
    parentRect.top = Math.max(parentRect.top, 0);
    parentRect.left = Math.max(parentRect.left, 0);
  }
  var offsets = getClientRect({
    top: childrenRect.top - parentRect.top - borderTopWidth,
    left: childrenRect.left - parentRect.left - borderLeftWidth,
    width: childrenRect.width,
    height: childrenRect.height
  });
  offsets.marginTop = 0;
  offsets.marginLeft = 0;

  // Subtract margins of documentElement in case it's being used as parent
  // we do this only on HTML because it's the only element that behaves
  // differently when margins are applied to it. The margins are included in
  // the box of the documentElement, in the other cases not.
  if (!isIE10 && isHTML) {
    var marginTop = parseFloat(styles.marginTop);
    var marginLeft = parseFloat(styles.marginLeft);

    offsets.top -= borderTopWidth - marginTop;
    offsets.bottom -= borderTopWidth - marginTop;
    offsets.left -= borderLeftWidth - marginLeft;
    offsets.right -= borderLeftWidth - marginLeft;

    // Attach marginTop and marginLeft because in some circumstances we may need them
    offsets.marginTop = marginTop;
    offsets.marginLeft = marginLeft;
  }

  if (isIE10 && !fixedPosition ? parent.contains(scrollParent) : parent === scrollParent && scrollParent.nodeName !== 'BODY') {
    offsets = includeScroll(offsets, parent);
  }

  return offsets;
}

function getViewportOffsetRectRelativeToArtbitraryNode(element) {
  var excludeScroll = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var html = element.ownerDocument.documentElement;
  var relativeOffset = getOffsetRectRelativeToArbitraryNode(element, html);
  var width = Math.max(html.clientWidth, window.innerWidth || 0);
  var height = Math.max(html.clientHeight, window.innerHeight || 0);

  var scrollTop = !excludeScroll ? getScroll(html) : 0;
  var scrollLeft = !excludeScroll ? getScroll(html, 'left') : 0;

  var offset = {
    top: scrollTop - relativeOffset.top + relativeOffset.marginTop,
    left: scrollLeft - relativeOffset.left + relativeOffset.marginLeft,
    width: width,
    height: height
  };

  return getClientRect(offset);
}

/**
 * Check if the given element is fixed or is inside a fixed parent
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @argument {Element} customContainer
 * @returns {Boolean} answer to "isFixed?"
 */
function isFixed(element) {
  var nodeName = element.nodeName;
  if (nodeName === 'BODY' || nodeName === 'HTML') {
    return false;
  }
  if (getStyleComputedProperty(element, 'position') === 'fixed') {
    return true;
  }
  var parentNode = getParentNode(element);
  if (!parentNode) {
    return false;
  }
  return isFixed(parentNode);
}

/**
 * Finds the first parent of an element that has a transformed property defined
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Element} first transformed parent or documentElement
 */

function getFixedPositionOffsetParent(element) {
  // This check is needed to avoid errors in case one of the elements isn't defined for any reason
  if (!element || !element.parentElement || isIE()) {
    return document.documentElement;
  }
  var el = element.parentElement;
  while (el && getStyleComputedProperty(el, 'transform') === 'none') {
    el = el.parentElement;
  }
  return el || document.documentElement;
}

/**
 * Computed the boundaries limits and return them
 * @method
 * @memberof Popper.Utils
 * @param {HTMLElement} popper
 * @param {HTMLElement} reference
 * @param {number} padding
 * @param {HTMLElement} boundariesElement - Element used to define the boundaries
 * @param {Boolean} fixedPosition - Is in fixed position mode
 * @returns {Object} Coordinates of the boundaries
 */
function getBoundaries(popper, reference, padding, boundariesElement) {
  var fixedPosition = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : false;

  // NOTE: 1 DOM access here

  var boundaries = { top: 0, left: 0 };
  var offsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, getReferenceNode(reference));

  // Handle viewport case
  if (boundariesElement === 'viewport') {
    boundaries = getViewportOffsetRectRelativeToArtbitraryNode(offsetParent, fixedPosition);
  } else {
    // Handle other cases based on DOM element used as boundaries
    var boundariesNode = void 0;
    if (boundariesElement === 'scrollParent') {
      boundariesNode = getScrollParent(getParentNode(reference));
      if (boundariesNode.nodeName === 'BODY') {
        boundariesNode = popper.ownerDocument.documentElement;
      }
    } else if (boundariesElement === 'window') {
      boundariesNode = popper.ownerDocument.documentElement;
    } else {
      boundariesNode = boundariesElement;
    }

    var offsets = getOffsetRectRelativeToArbitraryNode(boundariesNode, offsetParent, fixedPosition);

    // In case of HTML, we need a different computation
    if (boundariesNode.nodeName === 'HTML' && !isFixed(offsetParent)) {
      var _getWindowSizes = getWindowSizes(popper.ownerDocument),
          height = _getWindowSizes.height,
          width = _getWindowSizes.width;

      boundaries.top += offsets.top - offsets.marginTop;
      boundaries.bottom = height + offsets.top;
      boundaries.left += offsets.left - offsets.marginLeft;
      boundaries.right = width + offsets.left;
    } else {
      // for all the other DOM elements, this one is good
      boundaries = offsets;
    }
  }

  // Add paddings
  padding = padding || 0;
  var isPaddingNumber = typeof padding === 'number';
  boundaries.left += isPaddingNumber ? padding : padding.left || 0;
  boundaries.top += isPaddingNumber ? padding : padding.top || 0;
  boundaries.right -= isPaddingNumber ? padding : padding.right || 0;
  boundaries.bottom -= isPaddingNumber ? padding : padding.bottom || 0;

  return boundaries;
}

function getArea(_ref) {
  var width = _ref.width,
      height = _ref.height;

  return width * height;
}

/**
 * Utility used to transform the `auto` placement to the placement with more
 * available space.
 * @method
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function computeAutoPlacement(placement, refRect, popper, reference, boundariesElement) {
  var padding = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 0;

  if (placement.indexOf('auto') === -1) {
    return placement;
  }

  var boundaries = getBoundaries(popper, reference, padding, boundariesElement);

  var rects = {
    top: {
      width: boundaries.width,
      height: refRect.top - boundaries.top
    },
    right: {
      width: boundaries.right - refRect.right,
      height: boundaries.height
    },
    bottom: {
      width: boundaries.width,
      height: boundaries.bottom - refRect.bottom
    },
    left: {
      width: refRect.left - boundaries.left,
      height: boundaries.height
    }
  };

  var sortedAreas = Object.keys(rects).map(function (key) {
    return _extends({
      key: key
    }, rects[key], {
      area: getArea(rects[key])
    });
  }).sort(function (a, b) {
    return b.area - a.area;
  });

  var filteredAreas = sortedAreas.filter(function (_ref2) {
    var width = _ref2.width,
        height = _ref2.height;
    return width >= popper.clientWidth && height >= popper.clientHeight;
  });

  var computedPlacement = filteredAreas.length > 0 ? filteredAreas[0].key : sortedAreas[0].key;

  var variation = placement.split('-')[1];

  return computedPlacement + (variation ? '-' + variation : '');
}

/**
 * Get offsets to the reference element
 * @method
 * @memberof Popper.Utils
 * @param {Object} state
 * @param {Element} popper - the popper element
 * @param {Element} reference - the reference element (the popper will be relative to this)
 * @param {Element} fixedPosition - is in fixed position mode
 * @returns {Object} An object containing the offsets which will be applied to the popper
 */
function getReferenceOffsets(state, popper, reference) {
  var fixedPosition = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;

  var commonOffsetParent = fixedPosition ? getFixedPositionOffsetParent(popper) : findCommonOffsetParent(popper, getReferenceNode(reference));
  return getOffsetRectRelativeToArbitraryNode(reference, commonOffsetParent, fixedPosition);
}

/**
 * Get the outer sizes of the given element (offset size + margins)
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element
 * @returns {Object} object containing width and height properties
 */
function getOuterSizes(element) {
  var window = element.ownerDocument.defaultView;
  var styles = window.getComputedStyle(element);
  var x = parseFloat(styles.marginTop || 0) + parseFloat(styles.marginBottom || 0);
  var y = parseFloat(styles.marginLeft || 0) + parseFloat(styles.marginRight || 0);
  var result = {
    width: element.offsetWidth + y,
    height: element.offsetHeight + x
  };
  return result;
}

/**
 * Get the opposite placement of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement
 * @returns {String} flipped placement
 */
function getOppositePlacement(placement) {
  var hash = { left: 'right', right: 'left', bottom: 'top', top: 'bottom' };
  return placement.replace(/left|right|bottom|top/g, function (matched) {
    return hash[matched];
  });
}

/**
 * Get offsets to the popper
 * @method
 * @memberof Popper.Utils
 * @param {Object} position - CSS position the Popper will get applied
 * @param {HTMLElement} popper - the popper element
 * @param {Object} referenceOffsets - the reference offsets (the popper will be relative to this)
 * @param {String} placement - one of the valid placement options
 * @returns {Object} popperOffsets - An object containing the offsets which will be applied to the popper
 */
function getPopperOffsets(popper, referenceOffsets, placement) {
  placement = placement.split('-')[0];

  // Get popper node sizes
  var popperRect = getOuterSizes(popper);

  // Add position, width and height to our offsets object
  var popperOffsets = {
    width: popperRect.width,
    height: popperRect.height
  };

  // depending by the popper placement we have to compute its offsets slightly differently
  var isHoriz = ['right', 'left'].indexOf(placement) !== -1;
  var mainSide = isHoriz ? 'top' : 'left';
  var secondarySide = isHoriz ? 'left' : 'top';
  var measurement = isHoriz ? 'height' : 'width';
  var secondaryMeasurement = !isHoriz ? 'height' : 'width';

  popperOffsets[mainSide] = referenceOffsets[mainSide] + referenceOffsets[measurement] / 2 - popperRect[measurement] / 2;
  if (placement === secondarySide) {
    popperOffsets[secondarySide] = referenceOffsets[secondarySide] - popperRect[secondaryMeasurement];
  } else {
    popperOffsets[secondarySide] = referenceOffsets[getOppositePlacement(secondarySide)];
  }

  return popperOffsets;
}

/**
 * Mimics the `find` method of Array
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */
function find(arr, check) {
  // use native find if supported
  if (Array.prototype.find) {
    return arr.find(check);
  }

  // use `filter` to obtain the same behavior of `find`
  return arr.filter(check)[0];
}

/**
 * Return the index of the matching object
 * @method
 * @memberof Popper.Utils
 * @argument {Array} arr
 * @argument prop
 * @argument value
 * @returns index or -1
 */
function findIndex(arr, prop, value) {
  // use native findIndex if supported
  if (Array.prototype.findIndex) {
    return arr.findIndex(function (cur) {
      return cur[prop] === value;
    });
  }

  // use `find` + `indexOf` if `findIndex` isn't supported
  var match = find(arr, function (obj) {
    return obj[prop] === value;
  });
  return arr.indexOf(match);
}

/**
 * Loop trough the list of modifiers and run them in order,
 * each of them will then edit the data object.
 * @method
 * @memberof Popper.Utils
 * @param {dataObject} data
 * @param {Array} modifiers
 * @param {String} ends - Optional modifier name used as stopper
 * @returns {dataObject}
 */
function runModifiers(modifiers, data, ends) {
  var modifiersToRun = ends === undefined ? modifiers : modifiers.slice(0, findIndex(modifiers, 'name', ends));

  modifiersToRun.forEach(function (modifier) {
    if (modifier['function']) {
      // eslint-disable-line dot-notation
      console.warn('`modifier.function` is deprecated, use `modifier.fn`!');
    }
    var fn = modifier['function'] || modifier.fn; // eslint-disable-line dot-notation
    if (modifier.enabled && isFunction(fn)) {
      // Add properties to offsets to make them a complete clientRect object
      // we do this before each modifier to make sure the previous one doesn't
      // mess with these values
      data.offsets.popper = getClientRect(data.offsets.popper);
      data.offsets.reference = getClientRect(data.offsets.reference);

      data = fn(data, modifier);
    }
  });

  return data;
}

/**
 * Updates the position of the popper, computing the new offsets and applying
 * the new style.<br />
 * Prefer `scheduleUpdate` over `update` because of performance reasons.
 * @method
 * @memberof Popper
 */
function update() {
  // if popper is destroyed, don't perform any further update
  if (this.state.isDestroyed) {
    return;
  }

  var data = {
    instance: this,
    styles: {},
    arrowStyles: {},
    attributes: {},
    flipped: false,
    offsets: {}
  };

  // compute reference element offsets
  data.offsets.reference = getReferenceOffsets(this.state, this.popper, this.reference, this.options.positionFixed);

  // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value
  data.placement = computeAutoPlacement(this.options.placement, data.offsets.reference, this.popper, this.reference, this.options.modifiers.flip.boundariesElement, this.options.modifiers.flip.padding);

  // store the computed placement inside `originalPlacement`
  data.originalPlacement = data.placement;

  data.positionFixed = this.options.positionFixed;

  // compute the popper offsets
  data.offsets.popper = getPopperOffsets(this.popper, data.offsets.reference, data.placement);

  data.offsets.popper.position = this.options.positionFixed ? 'fixed' : 'absolute';

  // run the modifiers
  data = runModifiers(this.modifiers, data);

  // the first `update` will call `onCreate` callback
  // the other ones will call `onUpdate` callback
  if (!this.state.isCreated) {
    this.state.isCreated = true;
    this.options.onCreate(data);
  } else {
    this.options.onUpdate(data);
  }
}

/**
 * Helper used to know if the given modifier is enabled.
 * @method
 * @memberof Popper.Utils
 * @returns {Boolean}
 */
function isModifierEnabled(modifiers, modifierName) {
  return modifiers.some(function (_ref) {
    var name = _ref.name,
        enabled = _ref.enabled;
    return enabled && name === modifierName;
  });
}

/**
 * Get the prefixed supported property name
 * @method
 * @memberof Popper.Utils
 * @argument {String} property (camelCase)
 * @returns {String} prefixed property (camelCase or PascalCase, depending on the vendor prefix)
 */
function getSupportedPropertyName(property) {
  var prefixes = [false, 'ms', 'Webkit', 'Moz', 'O'];
  var upperProp = property.charAt(0).toUpperCase() + property.slice(1);

  for (var i = 0; i < prefixes.length; i++) {
    var prefix = prefixes[i];
    var toCheck = prefix ? '' + prefix + upperProp : property;
    if (typeof document.body.style[toCheck] !== 'undefined') {
      return toCheck;
    }
  }
  return null;
}

/**
 * Destroys the popper.
 * @method
 * @memberof Popper
 */
function destroy() {
  this.state.isDestroyed = true;

  // touch DOM only if `applyStyle` modifier is enabled
  if (isModifierEnabled(this.modifiers, 'applyStyle')) {
    this.popper.removeAttribute('x-placement');
    this.popper.style.position = '';
    this.popper.style.top = '';
    this.popper.style.left = '';
    this.popper.style.right = '';
    this.popper.style.bottom = '';
    this.popper.style.willChange = '';
    this.popper.style[getSupportedPropertyName('transform')] = '';
  }

  this.disableEventListeners();

  // remove the popper if user explicitly asked for the deletion on destroy
  // do not use `remove` because IE11 doesn't support it
  if (this.options.removeOnDestroy) {
    this.popper.parentNode.removeChild(this.popper);
  }
  return this;
}

/**
 * Get the window associated with the element
 * @argument {Element} element
 * @returns {Window}
 */
function getWindow(element) {
  var ownerDocument = element.ownerDocument;
  return ownerDocument ? ownerDocument.defaultView : window;
}

function attachToScrollParents(scrollParent, event, callback, scrollParents) {
  var isBody = scrollParent.nodeName === 'BODY';
  var target = isBody ? scrollParent.ownerDocument.defaultView : scrollParent;
  target.addEventListener(event, callback, { passive: true });

  if (!isBody) {
    attachToScrollParents(getScrollParent(target.parentNode), event, callback, scrollParents);
  }
  scrollParents.push(target);
}

/**
 * Setup needed event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */
function setupEventListeners(reference, options, state, updateBound) {
  // Resize event listener on window
  state.updateBound = updateBound;
  getWindow(reference).addEventListener('resize', state.updateBound, { passive: true });

  // Scroll event listener on scroll parents
  var scrollElement = getScrollParent(reference);
  attachToScrollParents(scrollElement, 'scroll', state.updateBound, state.scrollParents);
  state.scrollElement = scrollElement;
  state.eventsEnabled = true;

  return state;
}

/**
 * It will add resize/scroll events and start recalculating
 * position of the popper element when they are triggered.
 * @method
 * @memberof Popper
 */
function enableEventListeners() {
  if (!this.state.eventsEnabled) {
    this.state = setupEventListeners(this.reference, this.options, this.state, this.scheduleUpdate);
  }
}

/**
 * Remove event listeners used to update the popper position
 * @method
 * @memberof Popper.Utils
 * @private
 */
function removeEventListeners(reference, state) {
  // Remove resize event listener on window
  getWindow(reference).removeEventListener('resize', state.updateBound);

  // Remove scroll event listener on scroll parents
  state.scrollParents.forEach(function (target) {
    target.removeEventListener('scroll', state.updateBound);
  });

  // Reset state
  state.updateBound = null;
  state.scrollParents = [];
  state.scrollElement = null;
  state.eventsEnabled = false;
  return state;
}

/**
 * It will remove resize/scroll events and won't recalculate popper position
 * when they are triggered. It also won't trigger `onUpdate` callback anymore,
 * unless you call `update` method manually.
 * @method
 * @memberof Popper
 */
function disableEventListeners() {
  if (this.state.eventsEnabled) {
    cancelAnimationFrame(this.scheduleUpdate);
    this.state = removeEventListeners(this.reference, this.state);
  }
}

/**
 * Tells if a given input is a number
 * @method
 * @memberof Popper.Utils
 * @param {*} input to check
 * @return {Boolean}
 */
function isNumeric(n) {
  return n !== '' && !isNaN(parseFloat(n)) && isFinite(n);
}

/**
 * Set the style to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the style to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */
function setStyles(element, styles) {
  Object.keys(styles).forEach(function (prop) {
    var unit = '';
    // add unit if the value is numeric and is one of the following
    if (['width', 'height', 'top', 'right', 'bottom', 'left'].indexOf(prop) !== -1 && isNumeric(styles[prop])) {
      unit = 'px';
    }
    element.style[prop] = styles[prop] + unit;
  });
}

/**
 * Set the attributes to the given popper
 * @method
 * @memberof Popper.Utils
 * @argument {Element} element - Element to apply the attributes to
 * @argument {Object} styles
 * Object with a list of properties and values which will be applied to the element
 */
function setAttributes(element, attributes) {
  Object.keys(attributes).forEach(function (prop) {
    var value = attributes[prop];
    if (value !== false) {
      element.setAttribute(prop, attributes[prop]);
    } else {
      element.removeAttribute(prop);
    }
  });
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} data.styles - List of style properties - values to apply to popper element
 * @argument {Object} data.attributes - List of attribute properties - values to apply to popper element
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The same data object
 */
function applyStyle(data) {
  // any property present in `data.styles` will be applied to the popper,
  // in this way we can make the 3rd party modifiers add custom styles to it
  // Be aware, modifiers could override the properties defined in the previous
  // lines of this modifier!
  setStyles(data.instance.popper, data.styles);

  // any property present in `data.attributes` will be applied to the popper,
  // they will be set as HTML attributes of the element
  setAttributes(data.instance.popper, data.attributes);

  // if arrowElement is defined and arrowStyles has some properties
  if (data.arrowElement && Object.keys(data.arrowStyles).length) {
    setStyles(data.arrowElement, data.arrowStyles);
  }

  return data;
}

/**
 * Set the x-placement attribute before everything else because it could be used
 * to add margins to the popper margins needs to be calculated to get the
 * correct popper offsets.
 * @method
 * @memberof Popper.modifiers
 * @param {HTMLElement} reference - The reference element used to position the popper
 * @param {HTMLElement} popper - The HTML element used as popper
 * @param {Object} options - Popper.js options
 */
function applyStyleOnLoad(reference, popper, options, modifierOptions, state) {
  // compute reference element offsets
  var referenceOffsets = getReferenceOffsets(state, popper, reference, options.positionFixed);

  // compute auto placement, store placement inside the data object,
  // modifiers will be able to edit `placement` if needed
  // and refer to originalPlacement to know the original value
  var placement = computeAutoPlacement(options.placement, referenceOffsets, popper, reference, options.modifiers.flip.boundariesElement, options.modifiers.flip.padding);

  popper.setAttribute('x-placement', placement);

  // Apply `position` to popper before anything else because
  // without the position applied we can't guarantee correct computations
  setStyles(popper, { position: options.positionFixed ? 'fixed' : 'absolute' });

  return options;
}

/**
 * @function
 * @memberof Popper.Utils
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Boolean} shouldRound - If the offsets should be rounded at all
 * @returns {Object} The popper's position offsets rounded
 *
 * The tale of pixel-perfect positioning. It's still not 100% perfect, but as
 * good as it can be within reason.
 * Discussion here: https://github.com/FezVrasta/popper.js/pull/715
 *
 * Low DPI screens cause a popper to be blurry if not using full pixels (Safari
 * as well on High DPI screens).
 *
 * Firefox prefers no rounding for positioning and does not have blurriness on
 * high DPI screens.
 *
 * Only horizontal placement and left/right values need to be considered.
 */
function getRoundedOffsets(data, shouldRound) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;
  var round = Math.round,
      floor = Math.floor;

  var noRound = function noRound(v) {
    return v;
  };

  var referenceWidth = round(reference.width);
  var popperWidth = round(popper.width);

  var isVertical = ['left', 'right'].indexOf(data.placement) !== -1;
  var isVariation = data.placement.indexOf('-') !== -1;
  var sameWidthParity = referenceWidth % 2 === popperWidth % 2;
  var bothOddWidth = referenceWidth % 2 === 1 && popperWidth % 2 === 1;

  var horizontalToInteger = !shouldRound ? noRound : isVertical || isVariation || sameWidthParity ? round : floor;
  var verticalToInteger = !shouldRound ? noRound : round;

  return {
    left: horizontalToInteger(bothOddWidth && !isVariation && shouldRound ? popper.left - 1 : popper.left),
    top: verticalToInteger(popper.top),
    bottom: verticalToInteger(popper.bottom),
    right: horizontalToInteger(popper.right)
  };
}

var isFirefox = isBrowser && /Firefox/i.test(navigator.userAgent);

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function computeStyle(data, options) {
  var x = options.x,
      y = options.y;
  var popper = data.offsets.popper;

  // Remove this legacy support in Popper.js v2

  var legacyGpuAccelerationOption = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'applyStyle';
  }).gpuAcceleration;
  if (legacyGpuAccelerationOption !== undefined) {
    console.warn('WARNING: `gpuAcceleration` option moved to `computeStyle` modifier and will not be supported in future versions of Popper.js!');
  }
  var gpuAcceleration = legacyGpuAccelerationOption !== undefined ? legacyGpuAccelerationOption : options.gpuAcceleration;

  var offsetParent = getOffsetParent(data.instance.popper);
  var offsetParentRect = getBoundingClientRect(offsetParent);

  // Styles
  var styles = {
    position: popper.position
  };

  var offsets = getRoundedOffsets(data, window.devicePixelRatio < 2 || !isFirefox);

  var sideA = x === 'bottom' ? 'top' : 'bottom';
  var sideB = y === 'right' ? 'left' : 'right';

  // if gpuAcceleration is set to `true` and transform is supported,
  //  we use `translate3d` to apply the position to the popper we
  // automatically use the supported prefixed version if needed
  var prefixedProperty = getSupportedPropertyName('transform');

  // now, let's make a step back and look at this code closely (wtf?)
  // If the content of the popper grows once it's been positioned, it
  // may happen that the popper gets misplaced because of the new content
  // overflowing its reference element
  // To avoid this problem, we provide two options (x and y), which allow
  // the consumer to define the offset origin.
  // If we position a popper on top of a reference element, we can set
  // `x` to `top` to make the popper grow towards its top instead of
  // its bottom.
  var left = void 0,
      top = void 0;
  if (sideA === 'bottom') {
    // when offsetParent is <html> the positioning is relative to the bottom of the screen (excluding the scrollbar)
    // and not the bottom of the html element
    if (offsetParent.nodeName === 'HTML') {
      top = -offsetParent.clientHeight + offsets.bottom;
    } else {
      top = -offsetParentRect.height + offsets.bottom;
    }
  } else {
    top = offsets.top;
  }
  if (sideB === 'right') {
    if (offsetParent.nodeName === 'HTML') {
      left = -offsetParent.clientWidth + offsets.right;
    } else {
      left = -offsetParentRect.width + offsets.right;
    }
  } else {
    left = offsets.left;
  }
  if (gpuAcceleration && prefixedProperty) {
    styles[prefixedProperty] = 'translate3d(' + left + 'px, ' + top + 'px, 0)';
    styles[sideA] = 0;
    styles[sideB] = 0;
    styles.willChange = 'transform';
  } else {
    // othwerise, we use the standard `top`, `left`, `bottom` and `right` properties
    var invertTop = sideA === 'bottom' ? -1 : 1;
    var invertLeft = sideB === 'right' ? -1 : 1;
    styles[sideA] = top * invertTop;
    styles[sideB] = left * invertLeft;
    styles.willChange = sideA + ', ' + sideB;
  }

  // Attributes
  var attributes = {
    'x-placement': data.placement
  };

  // Update `data` attributes, styles and arrowStyles
  data.attributes = _extends({}, attributes, data.attributes);
  data.styles = _extends({}, styles, data.styles);
  data.arrowStyles = _extends({}, data.offsets.arrow, data.arrowStyles);

  return data;
}

/**
 * Helper used to know if the given modifier depends from another one.<br />
 * It checks if the needed modifier is listed and enabled.
 * @method
 * @memberof Popper.Utils
 * @param {Array} modifiers - list of modifiers
 * @param {String} requestingName - name of requesting modifier
 * @param {String} requestedName - name of requested modifier
 * @returns {Boolean}
 */
function isModifierRequired(modifiers, requestingName, requestedName) {
  var requesting = find(modifiers, function (_ref) {
    var name = _ref.name;
    return name === requestingName;
  });

  var isRequired = !!requesting && modifiers.some(function (modifier) {
    return modifier.name === requestedName && modifier.enabled && modifier.order < requesting.order;
  });

  if (!isRequired) {
    var _requesting = '`' + requestingName + '`';
    var requested = '`' + requestedName + '`';
    console.warn(requested + ' modifier is required by ' + _requesting + ' modifier in order to work, be sure to include it before ' + _requesting + '!');
  }
  return isRequired;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function arrow(data, options) {
  var _data$offsets$arrow;

  // arrow depends on keepTogether in order to work
  if (!isModifierRequired(data.instance.modifiers, 'arrow', 'keepTogether')) {
    return data;
  }

  var arrowElement = options.element;

  // if arrowElement is a string, suppose it's a CSS selector
  if (typeof arrowElement === 'string') {
    arrowElement = data.instance.popper.querySelector(arrowElement);

    // if arrowElement is not found, don't run the modifier
    if (!arrowElement) {
      return data;
    }
  } else {
    // if the arrowElement isn't a query selector we must check that the
    // provided DOM node is child of its popper node
    if (!data.instance.popper.contains(arrowElement)) {
      console.warn('WARNING: `arrow.element` must be child of its popper element!');
      return data;
    }
  }

  var placement = data.placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var isVertical = ['left', 'right'].indexOf(placement) !== -1;

  var len = isVertical ? 'height' : 'width';
  var sideCapitalized = isVertical ? 'Top' : 'Left';
  var side = sideCapitalized.toLowerCase();
  var altSide = isVertical ? 'left' : 'top';
  var opSide = isVertical ? 'bottom' : 'right';
  var arrowElementSize = getOuterSizes(arrowElement)[len];

  //
  // extends keepTogether behavior making sure the popper and its
  // reference have enough pixels in conjunction
  //

  // top/left side
  if (reference[opSide] - arrowElementSize < popper[side]) {
    data.offsets.popper[side] -= popper[side] - (reference[opSide] - arrowElementSize);
  }
  // bottom/right side
  if (reference[side] + arrowElementSize > popper[opSide]) {
    data.offsets.popper[side] += reference[side] + arrowElementSize - popper[opSide];
  }
  data.offsets.popper = getClientRect(data.offsets.popper);

  // compute center of the popper
  var center = reference[side] + reference[len] / 2 - arrowElementSize / 2;

  // Compute the sideValue using the updated popper offsets
  // take popper margin in account because we don't have this info available
  var css = getStyleComputedProperty(data.instance.popper);
  var popperMarginSide = parseFloat(css['margin' + sideCapitalized]);
  var popperBorderSide = parseFloat(css['border' + sideCapitalized + 'Width']);
  var sideValue = center - data.offsets.popper[side] - popperMarginSide - popperBorderSide;

  // prevent arrowElement from being placed not contiguously to its popper
  sideValue = Math.max(Math.min(popper[len] - arrowElementSize, sideValue), 0);

  data.arrowElement = arrowElement;
  data.offsets.arrow = (_data$offsets$arrow = {}, defineProperty(_data$offsets$arrow, side, Math.round(sideValue)), defineProperty(_data$offsets$arrow, altSide, ''), _data$offsets$arrow);

  return data;
}

/**
 * Get the opposite placement variation of the given one
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement variation
 * @returns {String} flipped placement variation
 */
function getOppositeVariation(variation) {
  if (variation === 'end') {
    return 'start';
  } else if (variation === 'start') {
    return 'end';
  }
  return variation;
}

/**
 * List of accepted placements to use as values of the `placement` option.<br />
 * Valid placements are:
 * - `auto`
 * - `top`
 * - `right`
 * - `bottom`
 * - `left`
 *
 * Each placement can have a variation from this list:
 * - `-start`
 * - `-end`
 *
 * Variations are interpreted easily if you think of them as the left to right
 * written languages. Horizontally (`top` and `bottom`), `start` is left and `end`
 * is right.<br />
 * Vertically (`left` and `right`), `start` is top and `end` is bottom.
 *
 * Some valid examples are:
 * - `top-end` (on top of reference, right aligned)
 * - `right-start` (on right of reference, top aligned)
 * - `bottom` (on bottom, centered)
 * - `auto-end` (on the side with more space available, alignment depends by placement)
 *
 * @static
 * @type {Array}
 * @enum {String}
 * @readonly
 * @method placements
 * @memberof Popper
 */
var placements = ['auto-start', 'auto', 'auto-end', 'top-start', 'top', 'top-end', 'right-start', 'right', 'right-end', 'bottom-end', 'bottom', 'bottom-start', 'left-end', 'left', 'left-start'];

// Get rid of `auto` `auto-start` and `auto-end`
var validPlacements = placements.slice(3);

/**
 * Given an initial placement, returns all the subsequent placements
 * clockwise (or counter-clockwise).
 *
 * @method
 * @memberof Popper.Utils
 * @argument {String} placement - A valid placement (it accepts variations)
 * @argument {Boolean} counter - Set to true to walk the placements counterclockwise
 * @returns {Array} placements including their variations
 */
function clockwise(placement) {
  var counter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var index = validPlacements.indexOf(placement);
  var arr = validPlacements.slice(index + 1).concat(validPlacements.slice(0, index));
  return counter ? arr.reverse() : arr;
}

var BEHAVIORS = {
  FLIP: 'flip',
  CLOCKWISE: 'clockwise',
  COUNTERCLOCKWISE: 'counterclockwise'
};

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function flip(data, options) {
  // if `inner` modifier is enabled, we can't use the `flip` modifier
  if (isModifierEnabled(data.instance.modifiers, 'inner')) {
    return data;
  }

  if (data.flipped && data.placement === data.originalPlacement) {
    // seems like flip is trying to loop, probably there's not enough space on any of the flippable sides
    return data;
  }

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, options.boundariesElement, data.positionFixed);

  var placement = data.placement.split('-')[0];
  var placementOpposite = getOppositePlacement(placement);
  var variation = data.placement.split('-')[1] || '';

  var flipOrder = [];

  switch (options.behavior) {
    case BEHAVIORS.FLIP:
      flipOrder = [placement, placementOpposite];
      break;
    case BEHAVIORS.CLOCKWISE:
      flipOrder = clockwise(placement);
      break;
    case BEHAVIORS.COUNTERCLOCKWISE:
      flipOrder = clockwise(placement, true);
      break;
    default:
      flipOrder = options.behavior;
  }

  flipOrder.forEach(function (step, index) {
    if (placement !== step || flipOrder.length === index + 1) {
      return data;
    }

    placement = data.placement.split('-')[0];
    placementOpposite = getOppositePlacement(placement);

    var popperOffsets = data.offsets.popper;
    var refOffsets = data.offsets.reference;

    // using floor because the reference offsets may contain decimals we are not going to consider here
    var floor = Math.floor;
    var overlapsRef = placement === 'left' && floor(popperOffsets.right) > floor(refOffsets.left) || placement === 'right' && floor(popperOffsets.left) < floor(refOffsets.right) || placement === 'top' && floor(popperOffsets.bottom) > floor(refOffsets.top) || placement === 'bottom' && floor(popperOffsets.top) < floor(refOffsets.bottom);

    var overflowsLeft = floor(popperOffsets.left) < floor(boundaries.left);
    var overflowsRight = floor(popperOffsets.right) > floor(boundaries.right);
    var overflowsTop = floor(popperOffsets.top) < floor(boundaries.top);
    var overflowsBottom = floor(popperOffsets.bottom) > floor(boundaries.bottom);

    var overflowsBoundaries = placement === 'left' && overflowsLeft || placement === 'right' && overflowsRight || placement === 'top' && overflowsTop || placement === 'bottom' && overflowsBottom;

    // flip the variation if required
    var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;

    // flips variation if reference element overflows boundaries
    var flippedVariationByRef = !!options.flipVariations && (isVertical && variation === 'start' && overflowsLeft || isVertical && variation === 'end' && overflowsRight || !isVertical && variation === 'start' && overflowsTop || !isVertical && variation === 'end' && overflowsBottom);

    // flips variation if popper content overflows boundaries
    var flippedVariationByContent = !!options.flipVariationsByContent && (isVertical && variation === 'start' && overflowsRight || isVertical && variation === 'end' && overflowsLeft || !isVertical && variation === 'start' && overflowsBottom || !isVertical && variation === 'end' && overflowsTop);

    var flippedVariation = flippedVariationByRef || flippedVariationByContent;

    if (overlapsRef || overflowsBoundaries || flippedVariation) {
      // this boolean to detect any flip loop
      data.flipped = true;

      if (overlapsRef || overflowsBoundaries) {
        placement = flipOrder[index + 1];
      }

      if (flippedVariation) {
        variation = getOppositeVariation(variation);
      }

      data.placement = placement + (variation ? '-' + variation : '');

      // this object contains `position`, we want to preserve it along with
      // any additional property we may add in the future
      data.offsets.popper = _extends({}, data.offsets.popper, getPopperOffsets(data.instance.popper, data.offsets.reference, data.placement));

      data = runModifiers(data.instance.modifiers, data, 'flip');
    }
  });
  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function keepTogether(data) {
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var placement = data.placement.split('-')[0];
  var floor = Math.floor;
  var isVertical = ['top', 'bottom'].indexOf(placement) !== -1;
  var side = isVertical ? 'right' : 'bottom';
  var opSide = isVertical ? 'left' : 'top';
  var measurement = isVertical ? 'width' : 'height';

  if (popper[side] < floor(reference[opSide])) {
    data.offsets.popper[opSide] = floor(reference[opSide]) - popper[measurement];
  }
  if (popper[opSide] > floor(reference[side])) {
    data.offsets.popper[opSide] = floor(reference[side]);
  }

  return data;
}

/**
 * Converts a string containing value + unit into a px value number
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} str - Value + unit string
 * @argument {String} measurement - `height` or `width`
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @returns {Number|String}
 * Value in pixels, or original string if no values were extracted
 */
function toValue(str, measurement, popperOffsets, referenceOffsets) {
  // separate value from unit
  var split = str.match(/((?:\-|\+)?\d*\.?\d*)(.*)/);
  var value = +split[1];
  var unit = split[2];

  // If it's not a number it's an operator, I guess
  if (!value) {
    return str;
  }

  if (unit.indexOf('%') === 0) {
    var element = void 0;
    switch (unit) {
      case '%p':
        element = popperOffsets;
        break;
      case '%':
      case '%r':
      default:
        element = referenceOffsets;
    }

    var rect = getClientRect(element);
    return rect[measurement] / 100 * value;
  } else if (unit === 'vh' || unit === 'vw') {
    // if is a vh or vw, we calculate the size based on the viewport
    var size = void 0;
    if (unit === 'vh') {
      size = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    } else {
      size = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    }
    return size / 100 * value;
  } else {
    // if is an explicit pixel unit, we get rid of the unit and keep the value
    // if is an implicit unit, it's px, and we return just the value
    return value;
  }
}

/**
 * Parse an `offset` string to extrapolate `x` and `y` numeric offsets.
 * @function
 * @memberof {modifiers~offset}
 * @private
 * @argument {String} offset
 * @argument {Object} popperOffsets
 * @argument {Object} referenceOffsets
 * @argument {String} basePlacement
 * @returns {Array} a two cells array with x and y offsets in numbers
 */
function parseOffset(offset, popperOffsets, referenceOffsets, basePlacement) {
  var offsets = [0, 0];

  // Use height if placement is left or right and index is 0 otherwise use width
  // in this way the first offset will use an axis and the second one
  // will use the other one
  var useHeight = ['right', 'left'].indexOf(basePlacement) !== -1;

  // Split the offset string to obtain a list of values and operands
  // The regex addresses values with the plus or minus sign in front (+10, -20, etc)
  var fragments = offset.split(/(\+|\-)/).map(function (frag) {
    return frag.trim();
  });

  // Detect if the offset string contains a pair of values or a single one
  // they could be separated by comma or space
  var divider = fragments.indexOf(find(fragments, function (frag) {
    return frag.search(/,|\s/) !== -1;
  }));

  if (fragments[divider] && fragments[divider].indexOf(',') === -1) {
    console.warn('Offsets separated by white space(s) are deprecated, use a comma (,) instead.');
  }

  // If divider is found, we divide the list of values and operands to divide
  // them by ofset X and Y.
  var splitRegex = /\s*,\s*|\s+/;
  var ops = divider !== -1 ? [fragments.slice(0, divider).concat([fragments[divider].split(splitRegex)[0]]), [fragments[divider].split(splitRegex)[1]].concat(fragments.slice(divider + 1))] : [fragments];

  // Convert the values with units to absolute pixels to allow our computations
  ops = ops.map(function (op, index) {
    // Most of the units rely on the orientation of the popper
    var measurement = (index === 1 ? !useHeight : useHeight) ? 'height' : 'width';
    var mergeWithPrevious = false;
    return op
    // This aggregates any `+` or `-` sign that aren't considered operators
    // e.g.: 10 + +5 => [10, +, +5]
    .reduce(function (a, b) {
      if (a[a.length - 1] === '' && ['+', '-'].indexOf(b) !== -1) {
        a[a.length - 1] = b;
        mergeWithPrevious = true;
        return a;
      } else if (mergeWithPrevious) {
        a[a.length - 1] += b;
        mergeWithPrevious = false;
        return a;
      } else {
        return a.concat(b);
      }
    }, [])
    // Here we convert the string values into number values (in px)
    .map(function (str) {
      return toValue(str, measurement, popperOffsets, referenceOffsets);
    });
  });

  // Loop trough the offsets arrays and execute the operations
  ops.forEach(function (op, index) {
    op.forEach(function (frag, index2) {
      if (isNumeric(frag)) {
        offsets[index] += frag * (op[index2 - 1] === '-' ? -1 : 1);
      }
    });
  });
  return offsets;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @argument {Number|String} options.offset=0
 * The offset value as described in the modifier description
 * @returns {Object} The data object, properly modified
 */
function offset(data, _ref) {
  var offset = _ref.offset;
  var placement = data.placement,
      _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var basePlacement = placement.split('-')[0];

  var offsets = void 0;
  if (isNumeric(+offset)) {
    offsets = [+offset, 0];
  } else {
    offsets = parseOffset(offset, popper, reference, basePlacement);
  }

  if (basePlacement === 'left') {
    popper.top += offsets[0];
    popper.left -= offsets[1];
  } else if (basePlacement === 'right') {
    popper.top += offsets[0];
    popper.left += offsets[1];
  } else if (basePlacement === 'top') {
    popper.left += offsets[0];
    popper.top -= offsets[1];
  } else if (basePlacement === 'bottom') {
    popper.left += offsets[0];
    popper.top += offsets[1];
  }

  data.popper = popper;
  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function preventOverflow(data, options) {
  var boundariesElement = options.boundariesElement || getOffsetParent(data.instance.popper);

  // If offsetParent is the reference element, we really want to
  // go one step up and use the next offsetParent as reference to
  // avoid to make this modifier completely useless and look like broken
  if (data.instance.reference === boundariesElement) {
    boundariesElement = getOffsetParent(boundariesElement);
  }

  // NOTE: DOM access here
  // resets the popper's position so that the document size can be calculated excluding
  // the size of the popper element itself
  var transformProp = getSupportedPropertyName('transform');
  var popperStyles = data.instance.popper.style; // assignment to help minification
  var top = popperStyles.top,
      left = popperStyles.left,
      transform = popperStyles[transformProp];

  popperStyles.top = '';
  popperStyles.left = '';
  popperStyles[transformProp] = '';

  var boundaries = getBoundaries(data.instance.popper, data.instance.reference, options.padding, boundariesElement, data.positionFixed);

  // NOTE: DOM access here
  // restores the original style properties after the offsets have been computed
  popperStyles.top = top;
  popperStyles.left = left;
  popperStyles[transformProp] = transform;

  options.boundaries = boundaries;

  var order = options.priority;
  var popper = data.offsets.popper;

  var check = {
    primary: function primary(placement) {
      var value = popper[placement];
      if (popper[placement] < boundaries[placement] && !options.escapeWithReference) {
        value = Math.max(popper[placement], boundaries[placement]);
      }
      return defineProperty({}, placement, value);
    },
    secondary: function secondary(placement) {
      var mainSide = placement === 'right' ? 'left' : 'top';
      var value = popper[mainSide];
      if (popper[placement] > boundaries[placement] && !options.escapeWithReference) {
        value = Math.min(popper[mainSide], boundaries[placement] - (placement === 'right' ? popper.width : popper.height));
      }
      return defineProperty({}, mainSide, value);
    }
  };

  order.forEach(function (placement) {
    var side = ['left', 'top'].indexOf(placement) !== -1 ? 'primary' : 'secondary';
    popper = _extends({}, popper, check[side](placement));
  });

  data.offsets.popper = popper;

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function shift(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var shiftvariation = placement.split('-')[1];

  // if shift shiftvariation is specified, run the modifier
  if (shiftvariation) {
    var _data$offsets = data.offsets,
        reference = _data$offsets.reference,
        popper = _data$offsets.popper;

    var isVertical = ['bottom', 'top'].indexOf(basePlacement) !== -1;
    var side = isVertical ? 'left' : 'top';
    var measurement = isVertical ? 'width' : 'height';

    var shiftOffsets = {
      start: defineProperty({}, side, reference[side]),
      end: defineProperty({}, side, reference[side] + reference[measurement] - popper[measurement])
    };

    data.offsets.popper = _extends({}, popper, shiftOffsets[shiftvariation]);
  }

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by update method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function hide(data) {
  if (!isModifierRequired(data.instance.modifiers, 'hide', 'preventOverflow')) {
    return data;
  }

  var refRect = data.offsets.reference;
  var bound = find(data.instance.modifiers, function (modifier) {
    return modifier.name === 'preventOverflow';
  }).boundaries;

  if (refRect.bottom < bound.top || refRect.left > bound.right || refRect.top > bound.bottom || refRect.right < bound.left) {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === true) {
      return data;
    }

    data.hide = true;
    data.attributes['x-out-of-boundaries'] = '';
  } else {
    // Avoid unnecessary DOM access if visibility hasn't changed
    if (data.hide === false) {
      return data;
    }

    data.hide = false;
    data.attributes['x-out-of-boundaries'] = false;
  }

  return data;
}

/**
 * @function
 * @memberof Modifiers
 * @argument {Object} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {Object} The data object, properly modified
 */
function inner(data) {
  var placement = data.placement;
  var basePlacement = placement.split('-')[0];
  var _data$offsets = data.offsets,
      popper = _data$offsets.popper,
      reference = _data$offsets.reference;

  var isHoriz = ['left', 'right'].indexOf(basePlacement) !== -1;

  var subtractLength = ['top', 'left'].indexOf(basePlacement) === -1;

  popper[isHoriz ? 'left' : 'top'] = reference[basePlacement] - (subtractLength ? popper[isHoriz ? 'width' : 'height'] : 0);

  data.placement = getOppositePlacement(placement);
  data.offsets.popper = getClientRect(popper);

  return data;
}

/**
 * Modifier function, each modifier can have a function of this type assigned
 * to its `fn` property.<br />
 * These functions will be called on each update, this means that you must
 * make sure they are performant enough to avoid performance bottlenecks.
 *
 * @function ModifierFn
 * @argument {dataObject} data - The data object generated by `update` method
 * @argument {Object} options - Modifiers configuration and options
 * @returns {dataObject} The data object, properly modified
 */

/**
 * Modifiers are plugins used to alter the behavior of your poppers.<br />
 * Popper.js uses a set of 9 modifiers to provide all the basic functionalities
 * needed by the library.
 *
 * Usually you don't want to override the `order`, `fn` and `onLoad` props.
 * All the other properties are configurations that could be tweaked.
 * @namespace modifiers
 */
var modifiers = {
  /**
   * Modifier used to shift the popper on the start or end of its reference
   * element.<br />
   * It will read the variation of the `placement` property.<br />
   * It can be one either `-end` or `-start`.
   * @memberof modifiers
   * @inner
   */
  shift: {
    /** @prop {number} order=100 - Index used to define the order of execution */
    order: 100,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: shift
  },

  /**
   * The `offset` modifier can shift your popper on both its axis.
   *
   * It accepts the following units:
   * - `px` or unit-less, interpreted as pixels
   * - `%` or `%r`, percentage relative to the length of the reference element
   * - `%p`, percentage relative to the length of the popper element
   * - `vw`, CSS viewport width unit
   * - `vh`, CSS viewport height unit
   *
   * For length is intended the main axis relative to the placement of the popper.<br />
   * This means that if the placement is `top` or `bottom`, the length will be the
   * `width`. In case of `left` or `right`, it will be the `height`.
   *
   * You can provide a single value (as `Number` or `String`), or a pair of values
   * as `String` divided by a comma or one (or more) white spaces.<br />
   * The latter is a deprecated method because it leads to confusion and will be
   * removed in v2.<br />
   * Additionally, it accepts additions and subtractions between different units.
   * Note that multiplications and divisions aren't supported.
   *
   * Valid examples are:
   * ```
   * 10
   * '10%'
   * '10, 10'
   * '10%, 10'
   * '10 + 10%'
   * '10 - 5vh + 3%'
   * '-10px + 5vh, 5px - 6%'
   * ```
   * > **NB**: If you desire to apply offsets to your poppers in a way that may make them overlap
   * > with their reference element, unfortunately, you will have to disable the `flip` modifier.
   * > You can read more on this at this [issue](https://github.com/FezVrasta/popper.js/issues/373).
   *
   * @memberof modifiers
   * @inner
   */
  offset: {
    /** @prop {number} order=200 - Index used to define the order of execution */
    order: 200,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: offset,
    /** @prop {Number|String} offset=0
     * The offset value as described in the modifier description
     */
    offset: 0
  },

  /**
   * Modifier used to prevent the popper from being positioned outside the boundary.
   *
   * A scenario exists where the reference itself is not within the boundaries.<br />
   * We can say it has "escaped the boundaries" — or just "escaped".<br />
   * In this case we need to decide whether the popper should either:
   *
   * - detach from the reference and remain "trapped" in the boundaries, or
   * - if it should ignore the boundary and "escape with its reference"
   *
   * When `escapeWithReference` is set to`true` and reference is completely
   * outside its boundaries, the popper will overflow (or completely leave)
   * the boundaries in order to remain attached to the edge of the reference.
   *
   * @memberof modifiers
   * @inner
   */
  preventOverflow: {
    /** @prop {number} order=300 - Index used to define the order of execution */
    order: 300,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: preventOverflow,
    /**
     * @prop {Array} [priority=['left','right','top','bottom']]
     * Popper will try to prevent overflow following these priorities by default,
     * then, it could overflow on the left and on top of the `boundariesElement`
     */
    priority: ['left', 'right', 'top', 'bottom'],
    /**
     * @prop {number} padding=5
     * Amount of pixel used to define a minimum distance between the boundaries
     * and the popper. This makes sure the popper always has a little padding
     * between the edges of its container
     */
    padding: 5,
    /**
     * @prop {String|HTMLElement} boundariesElement='scrollParent'
     * Boundaries used by the modifier. Can be `scrollParent`, `window`,
     * `viewport` or any DOM element.
     */
    boundariesElement: 'scrollParent'
  },

  /**
   * Modifier used to make sure the reference and its popper stay near each other
   * without leaving any gap between the two. Especially useful when the arrow is
   * enabled and you want to ensure that it points to its reference element.
   * It cares only about the first axis. You can still have poppers with margin
   * between the popper and its reference element.
   * @memberof modifiers
   * @inner
   */
  keepTogether: {
    /** @prop {number} order=400 - Index used to define the order of execution */
    order: 400,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: keepTogether
  },

  /**
   * This modifier is used to move the `arrowElement` of the popper to make
   * sure it is positioned between the reference element and its popper element.
   * It will read the outer size of the `arrowElement` node to detect how many
   * pixels of conjunction are needed.
   *
   * It has no effect if no `arrowElement` is provided.
   * @memberof modifiers
   * @inner
   */
  arrow: {
    /** @prop {number} order=500 - Index used to define the order of execution */
    order: 500,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: arrow,
    /** @prop {String|HTMLElement} element='[x-arrow]' - Selector or node used as arrow */
    element: '[x-arrow]'
  },

  /**
   * Modifier used to flip the popper's placement when it starts to overlap its
   * reference element.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   *
   * **NOTE:** this modifier will interrupt the current update cycle and will
   * restart it if it detects the need to flip the placement.
   * @memberof modifiers
   * @inner
   */
  flip: {
    /** @prop {number} order=600 - Index used to define the order of execution */
    order: 600,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: flip,
    /**
     * @prop {String|Array} behavior='flip'
     * The behavior used to change the popper's placement. It can be one of
     * `flip`, `clockwise`, `counterclockwise` or an array with a list of valid
     * placements (with optional variations)
     */
    behavior: 'flip',
    /**
     * @prop {number} padding=5
     * The popper will flip if it hits the edges of the `boundariesElement`
     */
    padding: 5,
    /**
     * @prop {String|HTMLElement} boundariesElement='viewport'
     * The element which will define the boundaries of the popper position.
     * The popper will never be placed outside of the defined boundaries
     * (except if `keepTogether` is enabled)
     */
    boundariesElement: 'viewport',
    /**
     * @prop {Boolean} flipVariations=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the reference element overlaps its boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariations: false,
    /**
     * @prop {Boolean} flipVariationsByContent=false
     * The popper will switch placement variation between `-start` and `-end` when
     * the popper element overlaps its reference boundaries.
     *
     * The original placement should have a set variation.
     */
    flipVariationsByContent: false
  },

  /**
   * Modifier used to make the popper flow toward the inner of the reference element.
   * By default, when this modifier is disabled, the popper will be placed outside
   * the reference element.
   * @memberof modifiers
   * @inner
   */
  inner: {
    /** @prop {number} order=700 - Index used to define the order of execution */
    order: 700,
    /** @prop {Boolean} enabled=false - Whether the modifier is enabled or not */
    enabled: false,
    /** @prop {ModifierFn} */
    fn: inner
  },

  /**
   * Modifier used to hide the popper when its reference element is outside of the
   * popper boundaries. It will set a `x-out-of-boundaries` attribute which can
   * be used to hide with a CSS selector the popper when its reference is
   * out of boundaries.
   *
   * Requires the `preventOverflow` modifier before it in order to work.
   * @memberof modifiers
   * @inner
   */
  hide: {
    /** @prop {number} order=800 - Index used to define the order of execution */
    order: 800,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: hide
  },

  /**
   * Computes the style that will be applied to the popper element to gets
   * properly positioned.
   *
   * Note that this modifier will not touch the DOM, it just prepares the styles
   * so that `applyStyle` modifier can apply it. This separation is useful
   * in case you need to replace `applyStyle` with a custom implementation.
   *
   * This modifier has `850` as `order` value to maintain backward compatibility
   * with previous versions of Popper.js. Expect the modifiers ordering method
   * to change in future major versions of the library.
   *
   * @memberof modifiers
   * @inner
   */
  computeStyle: {
    /** @prop {number} order=850 - Index used to define the order of execution */
    order: 850,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: computeStyle,
    /**
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: true,
    /**
     * @prop {string} [x='bottom']
     * Where to anchor the X axis (`bottom` or `top`). AKA X offset origin.
     * Change this if your popper should grow in a direction different from `bottom`
     */
    x: 'bottom',
    /**
     * @prop {string} [x='left']
     * Where to anchor the Y axis (`left` or `right`). AKA Y offset origin.
     * Change this if your popper should grow in a direction different from `right`
     */
    y: 'right'
  },

  /**
   * Applies the computed styles to the popper element.
   *
   * All the DOM manipulations are limited to this modifier. This is useful in case
   * you want to integrate Popper.js inside a framework or view library and you
   * want to delegate all the DOM manipulations to it.
   *
   * Note that if you disable this modifier, you must make sure the popper element
   * has its position set to `absolute` before Popper.js can do its work!
   *
   * Just disable this modifier and define your own to achieve the desired effect.
   *
   * @memberof modifiers
   * @inner
   */
  applyStyle: {
    /** @prop {number} order=900 - Index used to define the order of execution */
    order: 900,
    /** @prop {Boolean} enabled=true - Whether the modifier is enabled or not */
    enabled: true,
    /** @prop {ModifierFn} */
    fn: applyStyle,
    /** @prop {Function} */
    onLoad: applyStyleOnLoad,
    /**
     * @deprecated since version 1.10.0, the property moved to `computeStyle` modifier
     * @prop {Boolean} gpuAcceleration=true
     * If true, it uses the CSS 3D transformation to position the popper.
     * Otherwise, it will use the `top` and `left` properties
     */
    gpuAcceleration: undefined
  }
};

/**
 * The `dataObject` is an object containing all the information used by Popper.js.
 * This object is passed to modifiers and to the `onCreate` and `onUpdate` callbacks.
 * @name dataObject
 * @property {Object} data.instance The Popper.js instance
 * @property {String} data.placement Placement applied to popper
 * @property {String} data.originalPlacement Placement originally defined on init
 * @property {Boolean} data.flipped True if popper has been flipped by flip modifier
 * @property {Boolean} data.hide True if the reference element is out of boundaries, useful to know when to hide the popper
 * @property {HTMLElement} data.arrowElement Node used as arrow by arrow modifier
 * @property {Object} data.styles Any CSS property defined here will be applied to the popper. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.arrowStyles Any CSS property defined here will be applied to the popper arrow. It expects the JavaScript nomenclature (eg. `marginBottom`)
 * @property {Object} data.boundaries Offsets of the popper boundaries
 * @property {Object} data.offsets The measurements of popper, reference and arrow elements
 * @property {Object} data.offsets.popper `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.reference `top`, `left`, `width`, `height` values
 * @property {Object} data.offsets.arrow] `top` and `left` offsets, only one of them will be different from 0
 */

/**
 * Default options provided to Popper.js constructor.<br />
 * These can be overridden using the `options` argument of Popper.js.<br />
 * To override an option, simply pass an object with the same
 * structure of the `options` object, as the 3rd argument. For example:
 * ```
 * new Popper(ref, pop, {
 *   modifiers: {
 *     preventOverflow: { enabled: false }
 *   }
 * })
 * ```
 * @type {Object}
 * @static
 * @memberof Popper
 */
var Defaults = {
  /**
   * Popper's placement.
   * @prop {Popper.placements} placement='bottom'
   */
  placement: 'bottom',

  /**
   * Set this to true if you want popper to position it self in 'fixed' mode
   * @prop {Boolean} positionFixed=false
   */
  positionFixed: false,

  /**
   * Whether events (resize, scroll) are initially enabled.
   * @prop {Boolean} eventsEnabled=true
   */
  eventsEnabled: true,

  /**
   * Set to true if you want to automatically remove the popper when
   * you call the `destroy` method.
   * @prop {Boolean} removeOnDestroy=false
   */
  removeOnDestroy: false,

  /**
   * Callback called when the popper is created.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onCreate}
   */
  onCreate: function onCreate() {},

  /**
   * Callback called when the popper is updated. This callback is not called
   * on the initialization/creation of the popper, but only on subsequent
   * updates.<br />
   * By default, it is set to no-op.<br />
   * Access Popper.js instance with `data.instance`.
   * @prop {onUpdate}
   */
  onUpdate: function onUpdate() {},

  /**
   * List of modifiers used to modify the offsets before they are applied to the popper.
   * They provide most of the functionalities of Popper.js.
   * @prop {modifiers}
   */
  modifiers: modifiers
};

/**
 * @callback onCreate
 * @param {dataObject} data
 */

/**
 * @callback onUpdate
 * @param {dataObject} data
 */

// Utils
// Methods
var Popper = function () {
  /**
   * Creates a new Popper.js instance.
   * @class Popper
   * @param {Element|referenceObject} reference - The reference element used to position the popper
   * @param {Element} popper - The HTML / XML element used as the popper
   * @param {Object} options - Your custom options to override the ones defined in [Defaults](#defaults)
   * @return {Object} instance - The generated Popper.js instance
   */
  function Popper(reference, popper) {
    var _this = this;

    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    classCallCheck(this, Popper);

    this.scheduleUpdate = function () {
      return requestAnimationFrame(_this.update);
    };

    // make update() debounced, so that it only runs at most once-per-tick
    this.update = debounce(this.update.bind(this));

    // with {} we create a new object with the options inside it
    this.options = _extends({}, Popper.Defaults, options);

    // init state
    this.state = {
      isDestroyed: false,
      isCreated: false,
      scrollParents: []
    };

    // get reference and popper elements (allow jQuery wrappers)
    this.reference = reference && reference.jquery ? reference[0] : reference;
    this.popper = popper && popper.jquery ? popper[0] : popper;

    // Deep merge modifiers options
    this.options.modifiers = {};
    Object.keys(_extends({}, Popper.Defaults.modifiers, options.modifiers)).forEach(function (name) {
      _this.options.modifiers[name] = _extends({}, Popper.Defaults.modifiers[name] || {}, options.modifiers ? options.modifiers[name] : {});
    });

    // Refactoring modifiers' list (Object => Array)
    this.modifiers = Object.keys(this.options.modifiers).map(function (name) {
      return _extends({
        name: name
      }, _this.options.modifiers[name]);
    })
    // sort the modifiers by order
    .sort(function (a, b) {
      return a.order - b.order;
    });

    // modifiers have the ability to execute arbitrary code when Popper.js get inited
    // such code is executed in the same order of its modifier
    // they could add new properties to their options configuration
    // BE AWARE: don't add options to `options.modifiers.name` but to `modifierOptions`!
    this.modifiers.forEach(function (modifierOptions) {
      if (modifierOptions.enabled && isFunction(modifierOptions.onLoad)) {
        modifierOptions.onLoad(_this.reference, _this.popper, _this.options, modifierOptions, _this.state);
      }
    });

    // fire the first update to position the popper in the right place
    this.update();

    var eventsEnabled = this.options.eventsEnabled;
    if (eventsEnabled) {
      // setup event listeners, they will take care of update the position in specific situations
      this.enableEventListeners();
    }

    this.state.eventsEnabled = eventsEnabled;
  }

  // We can't use class properties because they don't get listed in the
  // class prototype and break stuff like Sinon stubs


  createClass(Popper, [{
    key: 'update',
    value: function update$$1() {
      return update.call(this);
    }
  }, {
    key: 'destroy',
    value: function destroy$$1() {
      return destroy.call(this);
    }
  }, {
    key: 'enableEventListeners',
    value: function enableEventListeners$$1() {
      return enableEventListeners.call(this);
    }
  }, {
    key: 'disableEventListeners',
    value: function disableEventListeners$$1() {
      return disableEventListeners.call(this);
    }

    /**
     * Schedules an update. It will run on the next UI update available.
     * @method scheduleUpdate
     * @memberof Popper
     */


    /**
     * Collection of utilities useful when writing custom modifiers.
     * Starting from version 1.7, this method is available only if you
     * include `popper-utils.js` before `popper.js`.
     *
     * **DEPRECATION**: This way to access PopperUtils is deprecated
     * and will be removed in v2! Use the PopperUtils module directly instead.
     * Due to the high instability of the methods contained in Utils, we can't
     * guarantee them to follow semver. Use them at your own risk!
     * @static
     * @private
     * @type {Object}
     * @deprecated since version 1.8
     * @member Utils
     * @memberof Popper
     */

  }]);
  return Popper;
}();

/**
 * The `referenceObject` is an object that provides an interface compatible with Popper.js
 * and lets you use it as replacement of a real DOM node.<br />
 * You can use this method to position a popper relatively to a set of coordinates
 * in case you don't have a DOM node to use as reference.
 *
 * ```
 * new Popper(referenceObject, popperNode);
 * ```
 *
 * NB: This feature isn't supported in Internet Explorer 10.
 * @name referenceObject
 * @property {Function} data.getBoundingClientRect
 * A function that returns a set of coordinates compatible with the native `getBoundingClientRect` method.
 * @property {number} data.clientWidth
 * An ES6 getter that will return the width of the virtual reference element.
 * @property {number} data.clientHeight
 * An ES6 getter that will return the height of the virtual reference element.
 */


Popper.Utils = (typeof window !== 'undefined' ? window : global).PopperUtils;
Popper.placements = placements;
Popper.Defaults = Defaults;

/* harmony default export */ __webpack_exports__["default"] = (Popper);
//# sourceMappingURL=popper.js.map

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;

// This works in non-strict mode
g = (function() {
	return this;
})();

try {
	// This works if eval is allowed (see CSP)
	g = g || new Function("return this")();
} catch (e) {
	// This works if the window reference is available
	if (typeof window === "object") g = window;
}

// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}

module.exports = g;


/***/ })

/******/ });
//# sourceMappingURL=table.js.map