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
/******/ 	return __webpack_require__(__webpack_require__.s = "./core/js/feedback.ts");
/******/ })
/************************************************************************/
/******/ ({

/***/ "../admin-columns/src/js/modules/modal.ts":
/*!************************************************!*\
  !*** ../admin-columns/src/js/modules/modal.ts ***!
  \************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Modal =
/** @class */
function () {
  function Modal(el) {
    if (!el) {
      return;
    }

    this.el = el;
    this.dialog = el.querySelector('.ac-modal__dialog');
    this.initEvents();
  }

  Modal.prototype.initEvents = function () {
    var _this = this;

    var self = this;
    document.addEventListener('keydown', function (e) {
      var keyName = e.key;

      if (!_this.isOpen()) {
        return;
      }

      if ('Escape' === keyName) {
        _this.close();
      }
    });
    var dismissButtons = this.el.querySelectorAll('[data-dismiss="modal"], .ac-modal__dialog__close');

    if (dismissButtons.length > 0) {
      dismissButtons.forEach(function (b) {
        b.addEventListener('click', function (e) {
          e.preventDefault();
          self.close();
        });
      });
    }

    this.el.addEventListener('click', function (e) {
      if (e.target.classList.contains('ac-modal')) {
        self.close();
      }
    });
  };

  Modal.prototype.isOpen = function () {
    return this.el.classList.contains('-active');
  };

  Modal.prototype.close = function () {
    this.onClose();
    this.el.classList.remove('-active');
  };

  Modal.prototype.open = function () {
    var _this = this; //short delay in order to allow bubbling events to bind before opening


    setTimeout(function () {
      _this.onOpen();

      _this.el.removeAttribute('style');

      _this.el.classList.add('-active');
    });
  };

  Modal.prototype.destroy = function () {
    this.el.remove();
  };

  Modal.prototype.onClose = function () {};

  Modal.prototype.onOpen = function () {};

  return Modal;
}();

/* harmony default export */ __webpack_exports__["default"] = (Modal);

/***/ }),

/***/ "./core/js/feedback.ts":
/*!*****************************!*\
  !*** ./core/js/feedback.ts ***!
  \*****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var AC_modules_modal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! AC/modules/modal */ "../admin-columns/src/js/modules/modal.ts");


var Feedback =
/** @class */
function () {
  function Feedback(form) {
    this.form = form;
    this.initEvents();
  }

  Feedback.prototype.initEvents = function () {
    var self = this;
    var $form = jQuery(this.form);
    $form.submit(function (e) {
      e.preventDefault();
    });
    $form.find('[name=frm_ac_fb_submit]').on('click', function () {
      self._sendData().done(function (response) {
        if (response.success) {
          $form.find('.ac-modal__dialog__footer').remove();
          $form.find('.ac-modal__dialog__content').html(response.data);
        } else {
          $form.find('.ac-feedback__error').html(response.data);
        }
      });
    });
  };

  Feedback.prototype._sendData = function () {
    var $form = jQuery(this.form);
    return jQuery.ajax({
      url: ajaxurl,
      method: 'post',
      data: {
        action: 'acp-send-feedback',
        email: $form.find('[name=name]').val(),
        feedback: $form.find('[name=feedback]').val(),
        _ajax_nonce: $form.find('[name=_ajax_nonce]').val()
      }
    });
  };

  return Feedback;
}();

document.addEventListener("DOMContentLoaded", function () {
  AdminColumns.Modals.register(new AC_modules_modal__WEBPACK_IMPORTED_MODULE_0__["default"](document.getElementById('ac-modal-feedback')), 'feedback');
  new Feedback(document.querySelector('#frm-ac-feedback'));
});

/***/ })

/******/ });
//# sourceMappingURL=feedback.js.map