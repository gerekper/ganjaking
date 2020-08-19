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
/******/ 	return __webpack_require__(__webpack_require__.s = "./search/js/table.js");
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
var Modal = /** @class */ (function () {
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
        var _this = this;
        //short delay in order to allow bubbling events to bind before opening
        setTimeout(function () {
            _this.onOpen();
            _this.el.removeAttribute('style');
            _this.el.classList.add('-active');
        });
    };
    Modal.prototype.destroy = function () {
        this.el.remove();
    };
    Modal.prototype.onClose = function () {
    };
    Modal.prototype.onOpen = function () {
    };
    return Modal;
}());
/* harmony default export */ __webpack_exports__["default"] = (Modal);


/***/ }),

/***/ "./node_modules/axios/index.js":
/*!*************************************!*\
  !*** ./node_modules/axios/index.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./lib/axios */ "./node_modules/axios/lib/axios.js");


/***/ }),

/***/ "./node_modules/axios/lib/adapters/xhr.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/adapters/xhr.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var settle = __webpack_require__(/*! ./../core/settle */ "./node_modules/axios/lib/core/settle.js");
var buildURL = __webpack_require__(/*! ./../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var buildFullPath = __webpack_require__(/*! ../core/buildFullPath */ "./node_modules/axios/lib/core/buildFullPath.js");
var parseHeaders = __webpack_require__(/*! ./../helpers/parseHeaders */ "./node_modules/axios/lib/helpers/parseHeaders.js");
var isURLSameOrigin = __webpack_require__(/*! ./../helpers/isURLSameOrigin */ "./node_modules/axios/lib/helpers/isURLSameOrigin.js");
var createError = __webpack_require__(/*! ../core/createError */ "./node_modules/axios/lib/core/createError.js");
module.exports = function xhrAdapter(config) {
    return new Promise(function dispatchXhrRequest(resolve, reject) {
        var requestData = config.data;
        var requestHeaders = config.headers;
        if (utils.isFormData(requestData)) {
            delete requestHeaders['Content-Type']; // Let the browser set it
        }
        var request = new XMLHttpRequest();
        // HTTP basic authentication
        if (config.auth) {
            var username = config.auth.username || '';
            var password = config.auth.password || '';
            requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
        }
        var fullPath = buildFullPath(config.baseURL, config.url);
        request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);
        // Set the request timeout in MS
        request.timeout = config.timeout;
        // Listen for ready state
        request.onreadystatechange = function handleLoad() {
            if (!request || request.readyState !== 4) {
                return;
            }
            // The request errored out and we didn't get a response, this will be
            // handled by onerror instead
            // With one exception: request that using file: protocol, most browsers
            // will return status as 0 even though it's a successful request
            if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
                return;
            }
            // Prepare the response
            var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
            var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
            var response = {
                data: responseData,
                status: request.status,
                statusText: request.statusText,
                headers: responseHeaders,
                config: config,
                request: request
            };
            settle(resolve, reject, response);
            // Clean up request
            request = null;
        };
        // Handle browser request cancellation (as opposed to a manual cancellation)
        request.onabort = function handleAbort() {
            if (!request) {
                return;
            }
            reject(createError('Request aborted', config, 'ECONNABORTED', request));
            // Clean up request
            request = null;
        };
        // Handle low level network errors
        request.onerror = function handleError() {
            // Real errors are hidden from us by the browser
            // onerror should only fire if it's a network error
            reject(createError('Network Error', config, null, request));
            // Clean up request
            request = null;
        };
        // Handle timeout
        request.ontimeout = function handleTimeout() {
            var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded';
            if (config.timeoutErrorMessage) {
                timeoutErrorMessage = config.timeoutErrorMessage;
            }
            reject(createError(timeoutErrorMessage, config, 'ECONNABORTED', request));
            // Clean up request
            request = null;
        };
        // Add xsrf header
        // This is only done if running in a standard browser environment.
        // Specifically not if we're in a web worker, or react-native.
        if (utils.isStandardBrowserEnv()) {
            var cookies = __webpack_require__(/*! ./../helpers/cookies */ "./node_modules/axios/lib/helpers/cookies.js");
            // Add xsrf header
            var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
                cookies.read(config.xsrfCookieName) :
                undefined;
            if (xsrfValue) {
                requestHeaders[config.xsrfHeaderName] = xsrfValue;
            }
        }
        // Add headers to the request
        if ('setRequestHeader' in request) {
            utils.forEach(requestHeaders, function setRequestHeader(val, key) {
                if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
                    // Remove Content-Type if data is undefined
                    delete requestHeaders[key];
                }
                else {
                    // Otherwise add header to the request
                    request.setRequestHeader(key, val);
                }
            });
        }
        // Add withCredentials to request if needed
        if (!utils.isUndefined(config.withCredentials)) {
            request.withCredentials = !!config.withCredentials;
        }
        // Add responseType to request if needed
        if (config.responseType) {
            try {
                request.responseType = config.responseType;
            }
            catch (e) {
                // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
                // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
                if (config.responseType !== 'json') {
                    throw e;
                }
            }
        }
        // Handle progress if needed
        if (typeof config.onDownloadProgress === 'function') {
            request.addEventListener('progress', config.onDownloadProgress);
        }
        // Not all browsers support upload events
        if (typeof config.onUploadProgress === 'function' && request.upload) {
            request.upload.addEventListener('progress', config.onUploadProgress);
        }
        if (config.cancelToken) {
            // Handle cancellation
            config.cancelToken.promise.then(function onCanceled(cancel) {
                if (!request) {
                    return;
                }
                request.abort();
                reject(cancel);
                // Clean up request
                request = null;
            });
        }
        if (requestData === undefined) {
            requestData = null;
        }
        // Send the request
        request.send(requestData);
    });
};


/***/ }),

/***/ "./node_modules/axios/lib/axios.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/axios.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");
var Axios = __webpack_require__(/*! ./core/Axios */ "./node_modules/axios/lib/core/Axios.js");
var mergeConfig = __webpack_require__(/*! ./core/mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
var defaults = __webpack_require__(/*! ./defaults */ "./node_modules/axios/lib/defaults.js");
/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
    var context = new Axios(defaultConfig);
    var instance = bind(Axios.prototype.request, context);
    // Copy axios.prototype to instance
    utils.extend(instance, Axios.prototype, context);
    // Copy context to instance
    utils.extend(instance, context);
    return instance;
}
// Create the default instance to be exported
var axios = createInstance(defaults);
// Expose Axios class to allow class inheritance
axios.Axios = Axios;
// Factory for creating new instances
axios.create = function create(instanceConfig) {
    return createInstance(mergeConfig(axios.defaults, instanceConfig));
};
// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__(/*! ./cancel/Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");
axios.CancelToken = __webpack_require__(/*! ./cancel/CancelToken */ "./node_modules/axios/lib/cancel/CancelToken.js");
axios.isCancel = __webpack_require__(/*! ./cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");
// Expose all/spread
axios.all = function all(promises) {
    return Promise.all(promises);
};
axios.spread = __webpack_require__(/*! ./helpers/spread */ "./node_modules/axios/lib/helpers/spread.js");
module.exports = axios;
// Allow use of default import syntax in TypeScript
module.exports.default = axios;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/Cancel.js":
/*!*************************************************!*\
  !*** ./node_modules/axios/lib/cancel/Cancel.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
    this.message = message;
}
Cancel.prototype.toString = function toString() {
    return 'Cancel' + (this.message ? ': ' + this.message : '');
};
Cancel.prototype.__CANCEL__ = true;
module.exports = Cancel;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/CancelToken.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/cancel/CancelToken.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var Cancel = __webpack_require__(/*! ./Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");
/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
    if (typeof executor !== 'function') {
        throw new TypeError('executor must be a function.');
    }
    var resolvePromise;
    this.promise = new Promise(function promiseExecutor(resolve) {
        resolvePromise = resolve;
    });
    var token = this;
    executor(function cancel(message) {
        if (token.reason) {
            // Cancellation has already been requested
            return;
        }
        token.reason = new Cancel(message);
        resolvePromise(token.reason);
    });
}
/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
    if (this.reason) {
        throw this.reason;
    }
};
/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
    var cancel;
    var token = new CancelToken(function executor(c) {
        cancel = c;
    });
    return {
        token: token,
        cancel: cancel
    };
};
module.exports = CancelToken;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/isCancel.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/cancel/isCancel.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

module.exports = function isCancel(value) {
    return !!(value && value.__CANCEL__);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/Axios.js":
/*!**********************************************!*\
  !*** ./node_modules/axios/lib/core/Axios.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var buildURL = __webpack_require__(/*! ../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var InterceptorManager = __webpack_require__(/*! ./InterceptorManager */ "./node_modules/axios/lib/core/InterceptorManager.js");
var dispatchRequest = __webpack_require__(/*! ./dispatchRequest */ "./node_modules/axios/lib/core/dispatchRequest.js");
var mergeConfig = __webpack_require__(/*! ./mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
    this.defaults = instanceConfig;
    this.interceptors = {
        request: new InterceptorManager(),
        response: new InterceptorManager()
    };
}
/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
    /*eslint no-param-reassign:0*/
    // Allow for axios('example/url'[, config]) a la fetch API
    if (typeof config === 'string') {
        config = arguments[1] || {};
        config.url = arguments[0];
    }
    else {
        config = config || {};
    }
    config = mergeConfig(this.defaults, config);
    // Set config.method
    if (config.method) {
        config.method = config.method.toLowerCase();
    }
    else if (this.defaults.method) {
        config.method = this.defaults.method.toLowerCase();
    }
    else {
        config.method = 'get';
    }
    // Hook up interceptors middleware
    var chain = [dispatchRequest, undefined];
    var promise = Promise.resolve(config);
    this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
        chain.unshift(interceptor.fulfilled, interceptor.rejected);
    });
    this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
        chain.push(interceptor.fulfilled, interceptor.rejected);
    });
    while (chain.length) {
        promise = promise.then(chain.shift(), chain.shift());
    }
    return promise;
};
Axios.prototype.getUri = function getUri(config) {
    config = mergeConfig(this.defaults, config);
    return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
};
// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
    /*eslint func-names:0*/
    Axios.prototype[method] = function (url, config) {
        return this.request(utils.merge(config || {}, {
            method: method,
            url: url
        }));
    };
});
utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
    /*eslint func-names:0*/
    Axios.prototype[method] = function (url, data, config) {
        return this.request(utils.merge(config || {}, {
            method: method,
            url: url,
            data: data
        }));
    };
});
module.exports = Axios;


/***/ }),

/***/ "./node_modules/axios/lib/core/InterceptorManager.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/core/InterceptorManager.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
function InterceptorManager() {
    this.handlers = [];
}
/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected) {
    this.handlers.push({
        fulfilled: fulfilled,
        rejected: rejected
    });
    return this.handlers.length - 1;
};
/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
    if (this.handlers[id]) {
        this.handlers[id] = null;
    }
};
/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
    utils.forEach(this.handlers, function forEachHandler(h) {
        if (h !== null) {
            fn(h);
        }
    });
};
module.exports = InterceptorManager;


/***/ }),

/***/ "./node_modules/axios/lib/core/buildFullPath.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/buildFullPath.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var isAbsoluteURL = __webpack_require__(/*! ../helpers/isAbsoluteURL */ "./node_modules/axios/lib/helpers/isAbsoluteURL.js");
var combineURLs = __webpack_require__(/*! ../helpers/combineURLs */ "./node_modules/axios/lib/helpers/combineURLs.js");
/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 * @returns {string} The combined full path
 */
module.exports = function buildFullPath(baseURL, requestedURL) {
    if (baseURL && !isAbsoluteURL(requestedURL)) {
        return combineURLs(baseURL, requestedURL);
    }
    return requestedURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/createError.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/createError.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var enhanceError = __webpack_require__(/*! ./enhanceError */ "./node_modules/axios/lib/core/enhanceError.js");
/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
    var error = new Error(message);
    return enhanceError(error, config, code, request, response);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/dispatchRequest.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/core/dispatchRequest.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var transformData = __webpack_require__(/*! ./transformData */ "./node_modules/axios/lib/core/transformData.js");
var isCancel = __webpack_require__(/*! ../cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");
var defaults = __webpack_require__(/*! ../defaults */ "./node_modules/axios/lib/defaults.js");
/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
    if (config.cancelToken) {
        config.cancelToken.throwIfRequested();
    }
}
/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
    throwIfCancellationRequested(config);
    // Ensure headers exist
    config.headers = config.headers || {};
    // Transform request data
    config.data = transformData(config.data, config.headers, config.transformRequest);
    // Flatten headers
    config.headers = utils.merge(config.headers.common || {}, config.headers[config.method] || {}, config.headers);
    utils.forEach(['delete', 'get', 'head', 'post', 'put', 'patch', 'common'], function cleanHeaderConfig(method) {
        delete config.headers[method];
    });
    var adapter = config.adapter || defaults.adapter;
    return adapter(config).then(function onAdapterResolution(response) {
        throwIfCancellationRequested(config);
        // Transform response data
        response.data = transformData(response.data, response.headers, config.transformResponse);
        return response;
    }, function onAdapterRejection(reason) {
        if (!isCancel(reason)) {
            throwIfCancellationRequested(config);
            // Transform response data
            if (reason && reason.response) {
                reason.response.data = transformData(reason.response.data, reason.response.headers, config.transformResponse);
            }
        }
        return Promise.reject(reason);
    });
};


/***/ }),

/***/ "./node_modules/axios/lib/core/enhanceError.js":
/*!*****************************************************!*\
  !*** ./node_modules/axios/lib/core/enhanceError.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
    error.config = config;
    if (code) {
        error.code = code;
    }
    error.request = request;
    error.response = response;
    error.isAxiosError = true;
    error.toJSON = function () {
        return {
            // Standard
            message: this.message,
            name: this.name,
            // Microsoft
            description: this.description,
            number: this.number,
            // Mozilla
            fileName: this.fileName,
            lineNumber: this.lineNumber,
            columnNumber: this.columnNumber,
            stack: this.stack,
            // Axios
            config: this.config,
            code: this.code
        };
    };
    return error;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/mergeConfig.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/mergeConfig.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");
/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 * @returns {Object} New object resulting from merging config2 to config1
 */
module.exports = function mergeConfig(config1, config2) {
    // eslint-disable-next-line no-param-reassign
    config2 = config2 || {};
    var config = {};
    var valueFromConfig2Keys = ['url', 'method', 'params', 'data'];
    var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy'];
    var defaultToConfig2Keys = [
        'baseURL', 'url', 'transformRequest', 'transformResponse', 'paramsSerializer',
        'timeout', 'withCredentials', 'adapter', 'responseType', 'xsrfCookieName',
        'xsrfHeaderName', 'onUploadProgress', 'onDownloadProgress',
        'maxContentLength', 'validateStatus', 'maxRedirects', 'httpAgent',
        'httpsAgent', 'cancelToken', 'socketPath'
    ];
    utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
        if (typeof config2[prop] !== 'undefined') {
            config[prop] = config2[prop];
        }
    });
    utils.forEach(mergeDeepPropertiesKeys, function mergeDeepProperties(prop) {
        if (utils.isObject(config2[prop])) {
            config[prop] = utils.deepMerge(config1[prop], config2[prop]);
        }
        else if (typeof config2[prop] !== 'undefined') {
            config[prop] = config2[prop];
        }
        else if (utils.isObject(config1[prop])) {
            config[prop] = utils.deepMerge(config1[prop]);
        }
        else if (typeof config1[prop] !== 'undefined') {
            config[prop] = config1[prop];
        }
    });
    utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
        if (typeof config2[prop] !== 'undefined') {
            config[prop] = config2[prop];
        }
        else if (typeof config1[prop] !== 'undefined') {
            config[prop] = config1[prop];
        }
    });
    var axiosKeys = valueFromConfig2Keys
        .concat(mergeDeepPropertiesKeys)
        .concat(defaultToConfig2Keys);
    var otherKeys = Object
        .keys(config2)
        .filter(function filterAxiosKeys(key) {
        return axiosKeys.indexOf(key) === -1;
    });
    utils.forEach(otherKeys, function otherKeysDefaultToConfig2(prop) {
        if (typeof config2[prop] !== 'undefined') {
            config[prop] = config2[prop];
        }
        else if (typeof config1[prop] !== 'undefined') {
            config[prop] = config1[prop];
        }
    });
    return config;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/settle.js":
/*!***********************************************!*\
  !*** ./node_modules/axios/lib/core/settle.js ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var createError = __webpack_require__(/*! ./createError */ "./node_modules/axios/lib/core/createError.js");
/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
    var validateStatus = response.config.validateStatus;
    if (!validateStatus || validateStatus(response.status)) {
        resolve(response);
    }
    else {
        reject(createError('Request failed with status code ' + response.status, response.config, null, response.request, response));
    }
};


/***/ }),

/***/ "./node_modules/axios/lib/core/transformData.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/transformData.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
    /*eslint no-param-reassign:0*/
    utils.forEach(fns, function transform(fn) {
        data = fn(data, headers);
    });
    return data;
};


/***/ }),

/***/ "./node_modules/axios/lib/defaults.js":
/*!********************************************!*\
  !*** ./node_modules/axios/lib/defaults.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {
var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var normalizeHeaderName = __webpack_require__(/*! ./helpers/normalizeHeaderName */ "./node_modules/axios/lib/helpers/normalizeHeaderName.js");
var DEFAULT_CONTENT_TYPE = {
    'Content-Type': 'application/x-www-form-urlencoded'
};
function setContentTypeIfUnset(headers, value) {
    if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
        headers['Content-Type'] = value;
    }
}
function getDefaultAdapter() {
    var adapter;
    if (typeof XMLHttpRequest !== 'undefined') {
        // For browsers use XHR adapter
        adapter = __webpack_require__(/*! ./adapters/xhr */ "./node_modules/axios/lib/adapters/xhr.js");
    }
    else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
        // For node use HTTP adapter
        adapter = __webpack_require__(/*! ./adapters/http */ "./node_modules/axios/lib/adapters/xhr.js");
    }
    return adapter;
}
var defaults = {
    adapter: getDefaultAdapter(),
    transformRequest: [function transformRequest(data, headers) {
            normalizeHeaderName(headers, 'Accept');
            normalizeHeaderName(headers, 'Content-Type');
            if (utils.isFormData(data) ||
                utils.isArrayBuffer(data) ||
                utils.isBuffer(data) ||
                utils.isStream(data) ||
                utils.isFile(data) ||
                utils.isBlob(data)) {
                return data;
            }
            if (utils.isArrayBufferView(data)) {
                return data.buffer;
            }
            if (utils.isURLSearchParams(data)) {
                setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
                return data.toString();
            }
            if (utils.isObject(data)) {
                setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
                return JSON.stringify(data);
            }
            return data;
        }],
    transformResponse: [function transformResponse(data) {
            /*eslint no-param-reassign:0*/
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                }
                catch (e) { /* Ignore */ }
            }
            return data;
        }],
    /**
     * A timeout in milliseconds to abort a request. If set to 0 (default) a
     * timeout is not created.
     */
    timeout: 0,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
    maxContentLength: -1,
    validateStatus: function validateStatus(status) {
        return status >= 200 && status < 300;
    }
};
defaults.headers = {
    common: {
        'Accept': 'application/json, text/plain, */*'
    }
};
utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
    defaults.headers[method] = {};
});
utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
    defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});
module.exports = defaults;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../process/browser.js */ "./node_modules/process/browser.js")))

/***/ }),

/***/ "./node_modules/axios/lib/helpers/bind.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/helpers/bind.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

module.exports = function bind(fn, thisArg) {
    return function wrap() {
        var args = new Array(arguments.length);
        for (var i = 0; i < args.length; i++) {
            args[i] = arguments[i];
        }
        return fn.apply(thisArg, args);
    };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/buildURL.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/helpers/buildURL.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
function encode(val) {
    return encodeURIComponent(val).
        replace(/%40/gi, '@').
        replace(/%3A/gi, ':').
        replace(/%24/g, '$').
        replace(/%2C/gi, ',').
        replace(/%20/g, '+').
        replace(/%5B/gi, '[').
        replace(/%5D/gi, ']');
}
/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
    /*eslint no-param-reassign:0*/
    if (!params) {
        return url;
    }
    var serializedParams;
    if (paramsSerializer) {
        serializedParams = paramsSerializer(params);
    }
    else if (utils.isURLSearchParams(params)) {
        serializedParams = params.toString();
    }
    else {
        var parts = [];
        utils.forEach(params, function serialize(val, key) {
            if (val === null || typeof val === 'undefined') {
                return;
            }
            if (utils.isArray(val)) {
                key = key + '[]';
            }
            else {
                val = [val];
            }
            utils.forEach(val, function parseValue(v) {
                if (utils.isDate(v)) {
                    v = v.toISOString();
                }
                else if (utils.isObject(v)) {
                    v = JSON.stringify(v);
                }
                parts.push(encode(key) + '=' + encode(v));
            });
        });
        serializedParams = parts.join('&');
    }
    if (serializedParams) {
        var hashmarkIndex = url.indexOf('#');
        if (hashmarkIndex !== -1) {
            url = url.slice(0, hashmarkIndex);
        }
        url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
    }
    return url;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/combineURLs.js":
/*!*******************************************************!*\
  !*** ./node_modules/axios/lib/helpers/combineURLs.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
    return relativeURL
        ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
        : baseURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/cookies.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/helpers/cookies.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
module.exports = (utils.isStandardBrowserEnv() ?
    // Standard browser envs support document.cookie
    (function standardBrowserEnv() {
        return {
            write: function write(name, value, expires, path, domain, secure) {
                var cookie = [];
                cookie.push(name + '=' + encodeURIComponent(value));
                if (utils.isNumber(expires)) {
                    cookie.push('expires=' + new Date(expires).toGMTString());
                }
                if (utils.isString(path)) {
                    cookie.push('path=' + path);
                }
                if (utils.isString(domain)) {
                    cookie.push('domain=' + domain);
                }
                if (secure === true) {
                    cookie.push('secure');
                }
                document.cookie = cookie.join('; ');
            },
            read: function read(name) {
                var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
                return (match ? decodeURIComponent(match[3]) : null);
            },
            remove: function remove(name) {
                this.write(name, '', Date.now() - 86400000);
            }
        };
    })() :
    // Non standard browser env (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
        return {
            write: function write() { },
            read: function read() { return null; },
            remove: function remove() { }
        };
    })());


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAbsoluteURL.js":
/*!*********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAbsoluteURL.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
    // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
    // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
    // by any combination of letters, digits, plus, period, or hyphen.
    return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isURLSameOrigin.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isURLSameOrigin.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
module.exports = (utils.isStandardBrowserEnv() ?
    // Standard browser envs have full support of the APIs needed to test
    // whether the request URL is of the same origin as current location.
    (function standardBrowserEnv() {
        var msie = /(msie|trident)/i.test(navigator.userAgent);
        var urlParsingNode = document.createElement('a');
        var originURL;
        /**
      * Parse a URL to discover it's components
      *
      * @param {String} url The URL to be parsed
      * @returns {Object}
      */
        function resolveURL(url) {
            var href = url;
            if (msie) {
                // IE needs attribute set twice to normalize properties
                urlParsingNode.setAttribute('href', href);
                href = urlParsingNode.href;
            }
            urlParsingNode.setAttribute('href', href);
            // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
            return {
                href: urlParsingNode.href,
                protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
                host: urlParsingNode.host,
                search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
                hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
                hostname: urlParsingNode.hostname,
                port: urlParsingNode.port,
                pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
                    urlParsingNode.pathname :
                    '/' + urlParsingNode.pathname
            };
        }
        originURL = resolveURL(window.location.href);
        /**
      * Determine if a URL shares the same origin as the current location
      *
      * @param {String} requestURL The URL to test
      * @returns {boolean} True if URL shares the same origin, otherwise false
      */
        return function isURLSameOrigin(requestURL) {
            var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
            return (parsed.protocol === originURL.protocol &&
                parsed.host === originURL.host);
        };
    })() :
    // Non standard browser envs (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
        return function isURLSameOrigin() {
            return true;
        };
    })());


/***/ }),

/***/ "./node_modules/axios/lib/helpers/normalizeHeaderName.js":
/*!***************************************************************!*\
  !*** ./node_modules/axios/lib/helpers/normalizeHeaderName.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");
module.exports = function normalizeHeaderName(headers, normalizedName) {
    utils.forEach(headers, function processHeader(value, name) {
        if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
            headers[normalizedName] = value;
            delete headers[name];
        }
    });
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/parseHeaders.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/parseHeaders.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
    'age', 'authorization', 'content-length', 'content-type', 'etag',
    'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
    'last-modified', 'location', 'max-forwards', 'proxy-authorization',
    'referer', 'retry-after', 'user-agent'
];
/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
    var parsed = {};
    var key;
    var val;
    var i;
    if (!headers) {
        return parsed;
    }
    utils.forEach(headers.split('\n'), function parser(line) {
        i = line.indexOf(':');
        key = utils.trim(line.substr(0, i)).toLowerCase();
        val = utils.trim(line.substr(i + 1));
        if (key) {
            if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
                return;
            }
            if (key === 'set-cookie') {
                parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
            }
            else {
                parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
            }
        }
    });
    return parsed;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/spread.js":
/*!**************************************************!*\
  !*** ./node_modules/axios/lib/helpers/spread.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
    return function wrap(arr) {
        return callback.apply(null, arr);
    };
};


/***/ }),

/***/ "./node_modules/axios/lib/utils.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/utils.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");
/*global toString:true*/
// utils is a library of generic helper functions non-specific to axios
var toString = Object.prototype.toString;
/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
    return toString.call(val) === '[object Array]';
}
/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
    return typeof val === 'undefined';
}
/**
 * Determine if a value is a Buffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
    return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
        && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
}
/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
    return toString.call(val) === '[object ArrayBuffer]';
}
/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
    return (typeof FormData !== 'undefined') && (val instanceof FormData);
}
/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
    var result;
    if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
        result = ArrayBuffer.isView(val);
    }
    else {
        result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
    }
    return result;
}
/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
    return typeof val === 'string';
}
/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
    return typeof val === 'number';
}
/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
    return val !== null && typeof val === 'object';
}
/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
    return toString.call(val) === '[object Date]';
}
/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
    return toString.call(val) === '[object File]';
}
/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
    return toString.call(val) === '[object Blob]';
}
/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
    return toString.call(val) === '[object Function]';
}
/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
    return isObject(val) && isFunction(val.pipe);
}
/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
    return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}
/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
    return str.replace(/^\s*/, '').replace(/\s*$/, '');
}
/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 * nativescript
 *  navigator.product -> 'NativeScript' or 'NS'
 */
function isStandardBrowserEnv() {
    if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
        navigator.product === 'NativeScript' ||
        navigator.product === 'NS')) {
        return false;
    }
    return (typeof window !== 'undefined' &&
        typeof document !== 'undefined');
}
/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
    // Don't bother if no value provided
    if (obj === null || typeof obj === 'undefined') {
        return;
    }
    // Force an array if not already something iterable
    if (typeof obj !== 'object') {
        /*eslint no-param-reassign:0*/
        obj = [obj];
    }
    if (isArray(obj)) {
        // Iterate over array values
        for (var i = 0, l = obj.length; i < l; i++) {
            fn.call(null, obj[i], i, obj);
        }
    }
    else {
        // Iterate over object keys
        for (var key in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, key)) {
                fn.call(null, obj[key], key, obj);
            }
        }
    }
}
/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge( /* obj1, obj2, obj3, ... */) {
    var result = {};
    function assignValue(val, key) {
        if (typeof result[key] === 'object' && typeof val === 'object') {
            result[key] = merge(result[key], val);
        }
        else {
            result[key] = val;
        }
    }
    for (var i = 0, l = arguments.length; i < l; i++) {
        forEach(arguments[i], assignValue);
    }
    return result;
}
/**
 * Function equal to merge with the difference being that no reference
 * to original objects is kept.
 *
 * @see merge
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function deepMerge( /* obj1, obj2, obj3, ... */) {
    var result = {};
    function assignValue(val, key) {
        if (typeof result[key] === 'object' && typeof val === 'object') {
            result[key] = deepMerge(result[key], val);
        }
        else if (typeof val === 'object') {
            result[key] = deepMerge({}, val);
        }
        else {
            result[key] = val;
        }
    }
    for (var i = 0, l = arguments.length; i < l; i++) {
        forEach(arguments[i], assignValue);
    }
    return result;
}
/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
    forEach(b, function assignValue(val, key) {
        if (thisArg && typeof val === 'function') {
            a[key] = bind(val, thisArg);
        }
        else {
            a[key] = val;
        }
    });
    return a;
}
module.exports = {
    isArray: isArray,
    isArrayBuffer: isArrayBuffer,
    isBuffer: isBuffer,
    isFormData: isFormData,
    isArrayBufferView: isArrayBufferView,
    isString: isString,
    isNumber: isNumber,
    isObject: isObject,
    isUndefined: isUndefined,
    isDate: isDate,
    isFile: isFile,
    isBlob: isBlob,
    isFunction: isFunction,
    isStream: isStream,
    isURLSearchParams: isURLSearchParams,
    isStandardBrowserEnv: isStandardBrowserEnv,
    forEach: forEach,
    merge: merge,
    deepMerge: deepMerge,
    extend: extend,
    trim: trim
};


/***/ }),

/***/ "./node_modules/nanoassert/index.js":
/*!******************************************!*\
  !*** ./node_modules/nanoassert/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

assert.notEqual = notEqual;
assert.notOk = notOk;
assert.equal = equal;
assert.ok = assert;
module.exports = assert;
function equal(a, b, m) {
    assert(a == b, m); // eslint-disable-line eqeqeq
}
function notEqual(a, b, m) {
    assert(a != b, m); // eslint-disable-line eqeqeq
}
function notOk(t, m) {
    assert(!t, m);
}
function assert(t, m) {
    if (!t)
        throw new Error(m || 'AssertionError');
}


/***/ }),

/***/ "./node_modules/nanobus/index.js":
/*!***************************************!*\
  !*** ./node_modules/nanobus/index.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var splice = __webpack_require__(/*! remove-array-items */ "./node_modules/remove-array-items/index.js");
var nanotiming = __webpack_require__(/*! nanotiming */ "./node_modules/nanotiming/browser.js");
var assert = __webpack_require__(/*! assert */ "./node_modules/nanoassert/index.js");
module.exports = Nanobus;
function Nanobus(name) {
    if (!(this instanceof Nanobus))
        return new Nanobus(name);
    this._name = name || 'nanobus';
    this._starListeners = [];
    this._listeners = {};
}
Nanobus.prototype.emit = function (eventName) {
    assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.emit: eventName should be type string or symbol');
    var data = [];
    for (var i = 1, len = arguments.length; i < len; i++) {
        data.push(arguments[i]);
    }
    var emitTiming = nanotiming(this._name + "('" + eventName.toString() + "')");
    var listeners = this._listeners[eventName];
    if (listeners && listeners.length > 0) {
        this._emit(this._listeners[eventName], data);
    }
    if (this._starListeners.length > 0) {
        this._emit(this._starListeners, eventName, data, emitTiming.uuid);
    }
    emitTiming();
    return this;
};
Nanobus.prototype.on = Nanobus.prototype.addListener = function (eventName, listener) {
    assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.on: eventName should be type string or symbol');
    assert.equal(typeof listener, 'function', 'nanobus.on: listener should be type function');
    if (eventName === '*') {
        this._starListeners.push(listener);
    }
    else {
        if (!this._listeners[eventName])
            this._listeners[eventName] = [];
        this._listeners[eventName].push(listener);
    }
    return this;
};
Nanobus.prototype.prependListener = function (eventName, listener) {
    assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.prependListener: eventName should be type string or symbol');
    assert.equal(typeof listener, 'function', 'nanobus.prependListener: listener should be type function');
    if (eventName === '*') {
        this._starListeners.unshift(listener);
    }
    else {
        if (!this._listeners[eventName])
            this._listeners[eventName] = [];
        this._listeners[eventName].unshift(listener);
    }
    return this;
};
Nanobus.prototype.once = function (eventName, listener) {
    assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.once: eventName should be type string or symbol');
    assert.equal(typeof listener, 'function', 'nanobus.once: listener should be type function');
    var self = this;
    this.on(eventName, once);
    function once() {
        listener.apply(self, arguments);
        self.removeListener(eventName, once);
    }
    return this;
};
Nanobus.prototype.prependOnceListener = function (eventName, listener) {
    assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.prependOnceListener: eventName should be type string or symbol');
    assert.equal(typeof listener, 'function', 'nanobus.prependOnceListener: listener should be type function');
    var self = this;
    this.prependListener(eventName, once);
    function once() {
        listener.apply(self, arguments);
        self.removeListener(eventName, once);
    }
    return this;
};
Nanobus.prototype.removeListener = function (eventName, listener) {
    assert.ok(typeof eventName === 'string' || typeof eventName === 'symbol', 'nanobus.removeListener: eventName should be type string or symbol');
    assert.equal(typeof listener, 'function', 'nanobus.removeListener: listener should be type function');
    if (eventName === '*') {
        this._starListeners = this._starListeners.slice();
        return remove(this._starListeners, listener);
    }
    else {
        if (typeof this._listeners[eventName] !== 'undefined') {
            this._listeners[eventName] = this._listeners[eventName].slice();
        }
        return remove(this._listeners[eventName], listener);
    }
    function remove(arr, listener) {
        if (!arr)
            return;
        var index = arr.indexOf(listener);
        if (index !== -1) {
            splice(arr, index, 1);
            return true;
        }
    }
};
Nanobus.prototype.removeAllListeners = function (eventName) {
    if (eventName) {
        if (eventName === '*') {
            this._starListeners = [];
        }
        else {
            this._listeners[eventName] = [];
        }
    }
    else {
        this._starListeners = [];
        this._listeners = {};
    }
    return this;
};
Nanobus.prototype.listeners = function (eventName) {
    var listeners = eventName !== '*'
        ? this._listeners[eventName]
        : this._starListeners;
    var ret = [];
    if (listeners) {
        var ilength = listeners.length;
        for (var i = 0; i < ilength; i++)
            ret.push(listeners[i]);
    }
    return ret;
};
Nanobus.prototype._emit = function (arr, eventName, data, uuid) {
    if (typeof arr === 'undefined')
        return;
    if (arr.length === 0)
        return;
    if (data === undefined) {
        data = eventName;
        eventName = null;
    }
    if (eventName) {
        if (uuid !== undefined) {
            data = [eventName].concat(data, uuid);
        }
        else {
            data = [eventName].concat(data);
        }
    }
    var length = arr.length;
    for (var i = 0; i < length; i++) {
        var listener = arr[i];
        listener.apply(listener, data);
    }
};


/***/ }),

/***/ "./node_modules/nanoscheduler/index.js":
/*!*********************************************!*\
  !*** ./node_modules/nanoscheduler/index.js ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var assert = __webpack_require__(/*! assert */ "./node_modules/nanoassert/index.js");
var hasWindow = typeof window !== 'undefined';
function createScheduler() {
    var scheduler;
    if (hasWindow) {
        if (!window._nanoScheduler)
            window._nanoScheduler = new NanoScheduler(true);
        scheduler = window._nanoScheduler;
    }
    else {
        scheduler = new NanoScheduler();
    }
    return scheduler;
}
function NanoScheduler(hasWindow) {
    this.hasWindow = hasWindow;
    this.hasIdle = this.hasWindow && window.requestIdleCallback;
    this.method = this.hasIdle ? window.requestIdleCallback.bind(window) : this.setTimeout;
    this.scheduled = false;
    this.queue = [];
}
NanoScheduler.prototype.push = function (cb) {
    assert.equal(typeof cb, 'function', 'nanoscheduler.push: cb should be type function');
    this.queue.push(cb);
    this.schedule();
};
NanoScheduler.prototype.schedule = function () {
    if (this.scheduled)
        return;
    this.scheduled = true;
    var self = this;
    this.method(function (idleDeadline) {
        var cb;
        while (self.queue.length && idleDeadline.timeRemaining() > 0) {
            cb = self.queue.shift();
            cb(idleDeadline);
        }
        self.scheduled = false;
        if (self.queue.length)
            self.schedule();
    });
};
NanoScheduler.prototype.setTimeout = function (cb) {
    setTimeout(cb, 0, {
        timeRemaining: function () {
            return 1;
        }
    });
};
module.exports = createScheduler;


/***/ }),

/***/ "./node_modules/nanotiming/browser.js":
/*!********************************************!*\
  !*** ./node_modules/nanotiming/browser.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var scheduler = __webpack_require__(/*! nanoscheduler */ "./node_modules/nanoscheduler/index.js")();
var assert = __webpack_require__(/*! assert */ "./node_modules/nanoassert/index.js");
var perf;
nanotiming.disabled = true;
try {
    perf = window.performance;
    nanotiming.disabled = window.localStorage.DISABLE_NANOTIMING === 'true' || !perf.mark;
}
catch (e) { }
module.exports = nanotiming;
function nanotiming(name) {
    assert.equal(typeof name, 'string', 'nanotiming: name should be type string');
    if (nanotiming.disabled)
        return noop;
    var uuid = (perf.now() * 10000).toFixed() % Number.MAX_SAFE_INTEGER;
    var startName = 'start-' + uuid + '-' + name;
    perf.mark(startName);
    function end(cb) {
        var endName = 'end-' + uuid + '-' + name;
        perf.mark(endName);
        scheduler.push(function () {
            var err = null;
            try {
                var measureName = name + ' [' + uuid + ']';
                perf.measure(measureName, startName, endName);
                perf.clearMarks(startName);
                perf.clearMarks(endName);
            }
            catch (e) {
                err = e;
            }
            if (cb)
                cb(err, name);
        });
    }
    end.uuid = uuid;
    return end;
}
function noop(cb) {
    if (cb) {
        scheduler.push(function () {
            cb(new Error('nanotiming: performance API unavailable'));
        });
    }
}


/***/ }),

/***/ "./node_modules/process/browser.js":
/*!*****************************************!*\
  !*** ./node_modules/process/browser.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};
// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.
var cachedSetTimeout;
var cachedClearTimeout;
function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout() {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        }
        else {
            cachedSetTimeout = defaultSetTimout;
        }
    }
    catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        }
        else {
            cachedClearTimeout = defaultClearTimeout;
        }
    }
    catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
}());
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    }
    catch (e) {
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        }
        catch (e) {
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }
}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    }
    catch (e) {
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        }
        catch (e) {
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }
}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;
function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    }
    else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}
function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;
    var len = queue.length;
    while (len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}
process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};
// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};
function noop() { }
process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;
process.listeners = function (name) { return []; };
process.binding = function (name) {
    throw new Error('process.binding is not supported');
};
process.cwd = function () { return '/'; };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function () { return 0; };


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
module.exports = function removeItems(arr, startIdx, removeCount) {
    var i, length = arr.length;
    if (startIdx >= length || removeCount === 0) {
        return;
    }
    removeCount = (startIdx + removeCount > length ? length - startIdx : removeCount);
    var len = length - removeCount;
    for (i = startIdx; i < len; ++i) {
        arr[i] = arr[i + removeCount];
    }
    arr.length = len;
};


/***/ }),

/***/ "./node_modules/webpack/buildin/global.js":
/*!***********************************!*\
  !*** (webpack)/buildin/global.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports) {

var g;
// This works in non-strict mode
g = (function () {
    return this;
})();
try {
    // This works if eval is allowed (see CSP)
    g = g || new Function("return this")();
}
catch (e) {
    // This works if the window reference is available
    if (typeof window === "object")
        g = window;
}
// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}
module.exports = g;


/***/ }),

/***/ "./search/js/helpers/date.js":
/*!***********************************!*\
  !*** ./search/js/helpers/date.js ***!
  \***********************************/
/*! exports provided: formatDate */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "formatDate", function() { return formatDate; });
function formatDate(format, date) {
    // format 2017-12-31
    var year = date.substr(0, 4);
    var month = date.substr(5, 2);
    var day = date.substr(8, 2);
    return jQuery.datepicker.formatDate(format, new Date(year, month - 1, day));
}


/***/ }),

/***/ "./search/js/helpers/document.js":
/*!***************************************!*\
  !*** ./search/js/helpers/document.js ***!
  \***************************************/
/*! exports provided: getOffset */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getOffset", function() { return getOffset; });
function getOffset(el) {
    var rect = el.getBoundingClientRect(), scrollLeft = window.pageXOffset || document.documentElement.scrollLeft, scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    return {
        top: Math.round(rect.top + scrollTop),
        left: Math.round(rect.left + scrollLeft)
    };
}


/***/ }),

/***/ "./search/js/helpers/strings.js":
/*!**************************************!*\
  !*** ./search/js/helpers/strings.js ***!
  \**************************************/
/*! exports provided: toPixel */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "toPixel", function() { return toPixel; });
function toPixel(number) {
    return number + 'px';
}


/***/ }),

/***/ "./search/js/helpers/url.js":
/*!**********************************!*\
  !*** ./search/js/helpers/url.js ***!
  \**********************************/
/*! exports provided: removeParameterFromUrl */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "removeParameterFromUrl", function() { return removeParameterFromUrl; });
function removeParameterFromUrl(url, parameter) {
    return url
        .replace(new RegExp('[?&]' + parameter + '=[^&#]*(#.*)?$'), '$1')
        .replace(new RegExp('([?&])' + parameter + '=[^&]*&'), '$1');
}


/***/ }),

/***/ "./search/js/modules/ac-query-builder.js":
/*!***********************************************!*\
  !*** ./search/js/modules/ac-query-builder.js ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ac_search_query__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ac-search-query */ "./search/js/modules/ac-search-query.js");
/* harmony import */ var _filter_types__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./filter-types */ "./search/js/modules/filter-types.js");
/* harmony import */ var _placement__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./placement */ "./search/js/modules/placement.js");
/* harmony import */ var _rule__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./rule */ "./search/js/modules/rule.js");
/* harmony import */ var _types_default__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../types/default */ "./search/js/types/default.js");
/* harmony import */ var _ac_templates__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./ac-templates */ "./search/js/modules/ac-templates.js");
/* harmony import */ var _operators__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./operators */ "./search/js/modules/operators.js");







var acSelect2FormatState = function (state) {
    var text = state.text;
    if (state.element) {
        var label = jQuery(state.element).data('label');
        text = jQuery("<span>" + label + "</span>");
    }
    return text;
};
var ACQueryBuilder = /** @class */ (function () {
    /**
     * @param element
     * @param query
     */
    function ACQueryBuilder(element, query) {
        this.el = element;
        this.$el = jQuery(element);
        this.$form = jQuery(AC.table_id).parents('form:first');
        this.filtertypes = _filter_types__WEBPACK_IMPORTED_MODULE_1__["default"];
        this.query = new _ac_search_query__WEBPACK_IMPORTED_MODULE_0__["default"](query);
        this.initialState = '';
        this.setDefaults();
    }
    ACQueryBuilder.prototype.setDefaults = function () {
        this.defaults = {
            types: {
                abstract: _types_default__WEBPACK_IMPORTED_MODULE_4__["default"]
            },
            operators: {
                nullable: ['is_null', 'is_not_null', 'is_empty', 'is_not_empty', 'date_future', 'date_past', 'date_today']
            }
        };
    };
    ACQueryBuilder.prototype.init = function () {
        var self = this;
        // Fire events
        jQuery(document).trigger('ACSearch.registerFilterTypes');
        self.$el.trigger('ACSearch.beforeInit');
        // Start init
        self.query.init();
        if (self.query.hasFilters()) {
            try {
                self.buildQueryBuilder(self.query.getFilters(), self.query.getRules());
            }
            catch (e) {
                this.$el.html('');
                self.buildQueryBuilder(self.query.getFilters(), []);
            }
            self.initEvents();
        }
        new _placement__WEBPACK_IMPORTED_MODULE_2__["default"]().place();
    };
    ACQueryBuilder.prototype.buildQueryBuilder = function (filters, rules) {
        this.$el.queryBuilder({
            operators: _operators__WEBPACK_IMPORTED_MODULE_6__["getCustomOperators"](),
            allow_empty: true,
            filters: filters,
            conditions: ['AND'],
            allow_groups: false,
            select_placeholder: ac_search.i18n.select,
            inputs_separator: '<span class="ac-s__separator">-</span>',
            templates: {
                rule: _ac_templates__WEBPACK_IMPORTED_MODULE_5__["TemplateRule"],
                group: _ac_templates__WEBPACK_IMPORTED_MODULE_5__["TemplateGroup"]
            },
            rules: rules
        });
    };
    ACQueryBuilder.prototype.initEvents = function () {
        var self = this;
        self.initialState = JSON.stringify(this.getRules());
        /**
         * Init all Rules for the first time
         */
        this.$el.find('.rule-container').each(function () {
            var $rule = jQuery(this);
            var rule = new _rule__WEBPACK_IMPORTED_MODULE_3__["default"]($rule, self.query);
            self._bindRuleFilterType(rule);
            self._bindRuleOperator(rule);
            self._bindRuleValue(rule);
            rule.$el.addClass('initial');
            rule.compact();
        });
        if (this.$el.find('.rule-container').length === 0) {
            this.$el.find('.rules-group-body').hide();
        }
        // Triggers after the rule is populated with form elements
        this.$el.on('afterApplyRuleFlags.queryBuilder', function (e, rule) {
            new _rule__WEBPACK_IMPORTED_MODULE_3__["default"](rule.$el).compact();
        });
        this.$el.on('click', '[data-add]', function () {
            self.$el.addClass('init');
            self.$el.find('.rules-group-body').css('display', 'inline-block');
        });
        this.$el.on('beforeAddRule.queryBuilder', function (e) {
            if (!self.$el.hasClass('init')) {
                return;
            }
            if (self.validateForm()) {
                self.compactRules();
            }
            if (self.hasOpenRules()) {
                e.preventDefault();
            }
        });
        this.$el.on('click', '.rule-container [data-confirm]', function () {
            if (!self.validateForm()) {
                return;
            }
            jQuery(this).trigger('confirmRule.acQueryBuilder', [new _rule__WEBPACK_IMPORTED_MODULE_3__["default"](jQuery(this).closest('.rule-container'))]);
        });
        this.$el.on('confirmRule.acQueryBuilder', '.rule-container', function (e, rule) {
            rule.compact();
            self.checkChangeState();
        });
        this.$el.on('afterDeleteRule.queryBuilder', function () {
            self.checkChangeState();
        });
        this.$el.on('click', '.rule-container.compact', function () {
            new _rule__WEBPACK_IMPORTED_MODULE_3__["default"](jQuery(this)).open();
        });
        this.$form.on('submit', function () {
            if (!self.$el.queryBuilder('validate')) {
                self.$form.trigger('ACSearch.validationFailed');
                return false;
            }
            var rules = self.getRules();
            self.disableFields();
            if (rules.rules.length === 0) {
                return;
            }
            var textarea = jQuery('<textarea name="ac-rules">');
            textarea.val(JSON.stringify(rules)).hide();
            self.$el.append(textarea);
            self._sanitizeFieldNames();
        });
        this.$el.on('afterCreateRuleFilters.queryBuilder', function (e, $rule) {
            self._bindRuleFilterType(new _rule__WEBPACK_IMPORTED_MODULE_3__["default"]($rule.$el, self.query));
        });
        this.$el.on('afterCreateRuleOperators.queryBuilder', function (e, $rule) {
            var rule = new _rule__WEBPACK_IMPORTED_MODULE_3__["default"]($rule.$el, self.query);
            self._bindRuleOperator(rule);
        });
        this.$el.on('afterCreateRuleInput.queryBuilder', function (e, $rule) {
            var rule = new _rule__WEBPACK_IMPORTED_MODULE_3__["default"]($rule.$el, self.query);
            self._bindRuleValue(rule);
        });
    };
    /** {Rule} rule */
    ACQueryBuilder.prototype._bindRuleFilterType = function (rule) {
        var self = this;
        var $select = rule.$el.find('.rule-filter-container select');
        rule.$el.find('.rule-operator-container').hide();
        rule.$el.find('.rule-value-container').hide();
        $select.find('option').each(function () {
            var $option = jQuery(this);
            var filter = self.query.getFilter($option.val());
            $option.data('label', filter.label);
        });
        $select.find('option:first').val('').text('');
        $select.select2({
            width: 200,
            theme: 'acs2',
            minimumResultsForSearch: 10,
            placeholder: ac_search.i18n.select,
            templateResult: acSelect2FormatState,
            templateSelection: acSelect2FormatState,
            escapeMarkup: function (markup) {
                if (typeof markup === 'object') {
                    return markup.html();
                }
                return markup;
            },
        });
        // Select option if there is only one filter available
        if (!$select.val() && $select.find('option').length === 2) {
            var value = $select.find('option:last').val();
            $select.val(value).trigger('change').next('.select2').addClass('single-value');
        }
    };
    /** {Rule} rule */
    ACQueryBuilder.prototype._bindRuleOperator = function (rule) {
        var filter = rule.getFilter();
        var $operator_container = rule.$el.find('.rule-operator-container');
        $operator_container.show();
        rule.$el.find('.rule-operator-container select option').each(function () {
            var $option = jQuery(this);
            $option.text(filter.operator_labels[$option.val()]);
        });
        this.filtertypes.getTypes().forEach(function (type) {
            type.transformRuleOperator(rule);
        });
        var $select = rule.$el.find('.rule-operator-container select');
        $select.select2({ minimumResultsForSearch: -1, width: 150, theme: 'acs2' });
        if ($select.find('option').length === 1) {
            rule.$el.find('.rule-operator-container').addClass('single-value');
            $select.prop('disabled', true).select2('destroy');
            $operator_container.append("<span class=\"single-value__label\">" + $select.find('option:selected').text() + "</span>");
        }
    };
    /** {Rule} rule */
    ACQueryBuilder.prototype._bindRuleValue = function (rule) {
        var $container = rule.$el.find('.rule-value-container');
        if ($container.find('.form-control').length > 1) {
            $container.addClass('range');
            $container.find('.form-control:first').addClass('first');
            $container.find('.form-control:last').addClass('last');
        }
        this.filtertypes.getTypes().forEach(function (type) {
            type.renderInput(rule);
        });
    };
    /**
     * Changes the input field names so it can be used on the backend
     */
    ACQueryBuilder.prototype._sanitizeFieldNames = function () {
        this.$el.find('li.rule-container').each(function () {
            var $rule = jQuery(this);
            $rule.find('select[name], input[name]').each(function () {
                var $input = jQuery(this);
                var name = $input.attr('name');
                name = name.replace('rule_', 'r');
                name = name.replace(/_/g, '-');
                $input.attr('name', name);
            });
        });
    };
    /**
     * Disable all querybuilder fields so they won't be posted in the form
     */
    ACQueryBuilder.prototype.disableFields = function () {
        return this.$el.find('input, select').prop('disabled', true);
    };
    /**
     * Enable all querybuilder fields so they will be posted in the form
     */
    ACQueryBuilder.prototype.enableFields = function () {
        return this.$el.find('input, select').prop('disabled', false);
    };
    ACQueryBuilder.prototype.getRules = function () {
        var rules = this.$el.queryBuilder('getRules');
        var rule_container = this.$el.find('.rule-container');
        if (!rules) {
            return;
        }
        for (var i = 0; i < rules.rules.length; i++) {
            var $rule = jQuery(rule_container[i]);
            if ($rule.data('formatted_value')) {
                rules.rules[i].formatted_value = $rule.data('formatted_value');
            }
        }
        return rules;
    };
    ACQueryBuilder.prototype.compactRules = function () {
        this.$el.find('.rule-container').each(function () {
            new _rule__WEBPACK_IMPORTED_MODULE_3__["default"](jQuery(this)).compact();
        });
    };
    ACQueryBuilder.prototype.addRule = function () {
        jQuery('.ac-button__add-filter').trigger('click');
    };
    /**
     * Checks is there are any rules that are closed
     */
    ACQueryBuilder.prototype.hasOpenRules = function () {
        return (this.$el.find('.rule-container:not(.compact)').length);
    };
    /**
     * @returns bool
     */
    ACQueryBuilder.prototype.validateForm = function () {
        return this.$el.queryBuilder('validate');
    };
    ACQueryBuilder.prototype.checkChangeState = function () {
        var newState = JSON.stringify(this.getRules());
        var $button = jQuery('.ac-search-button');
        if (this.getRules().rules.length === 0) {
            $button.addClass('-no-filters');
        }
        else {
            $button.removeClass('-no-filters');
        }
        if (newState === this.initialState) {
            $button.removeClass('button-primary');
        }
        else {
            $button.addClass('button-primary');
        }
        document.dispatchEvent(new CustomEvent('AC_Search_State_Change'));
    };
    return ACQueryBuilder;
}());
/* harmony default export */ __webpack_exports__["default"] = (ACQueryBuilder);


/***/ }),

/***/ "./search/js/modules/ac-search-query.js":
/*!**********************************************!*\
  !*** ./search/js/modules/ac-search-query.js ***!
  \**********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _filter_types_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./filter-types.js */ "./search/js/modules/filter-types.js");

var ACSearchQuery = /** @class */ (function () {
    function ACSearchQuery(query) {
        this.rules = query.rules;
        this.filters = query.filters;
        this.i18n = query.i18n;
        this.query_string = query.query_string;
    }
    ACSearchQuery.prototype.init = function () {
        this.transformFilters(this.filters);
        this.checkRules();
    };
    ACSearchQuery.prototype.checkRules = function () {
        var self = this;
        if (!this.rules) {
            return;
        }
        this.rules.rules.forEach(function (rule, index, object) {
            if (!self.getFilter(rule.id)) {
                object.splice(index, 1);
            }
        });
    };
    ACSearchQuery.prototype.hasFilters = function () {
        return this.getFilters().length !== 0;
    };
    ACSearchQuery.prototype.getFilters = function () {
        return this.filters;
    };
    ACSearchQuery.prototype.getFilter = function (id) {
        var filters = this.getFilters();
        for (var i = 0; i < filters.length; i++) {
            var filter = filters[i];
            if (id === filter.id) {
                return filter;
            }
        }
        return false;
    };
    ACSearchQuery.prototype.transformFilters = function (filters) {
        var result = [];
        filters.forEach(function (filter) {
            _filter_types_js__WEBPACK_IMPORTED_MODULE_0__["default"].getTypes().forEach(function (type) {
                filter = type.transformFilter(filter);
            });
            result.push(filter);
        });
        return result;
    };
    ACSearchQuery.prototype.getRules = function () {
        if (!this.rules) {
            return [];
        }
        return this.rules;
    };
    ACSearchQuery.prototype.getRule = function (index) {
        var rules = this.getRules().rules;
        if (!rules) {
            return false;
        }
        if (!index in rules) {
            return false;
        }
        return rules[index];
    };
    return ACSearchQuery;
}());
/* harmony default export */ __webpack_exports__["default"] = (ACSearchQuery);


/***/ }),

/***/ "./search/js/modules/ac-templates.js":
/*!*******************************************!*\
  !*** ./search/js/modules/ac-templates.js ***!
  \*******************************************/
/*! exports provided: TemplateRule, TemplateGroup */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TemplateRule", function() { return TemplateRule; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "TemplateGroup", function() { return TemplateGroup; });
var TemplateRule = "\n<li id=\"{{= it.rule_id }}\" class=\"rule-container\">\n  {{? it.settings.display_errors }}\n    <div class=\"error-container\"><i class=\"{{= it.icons.error }}\"></i></div>\n  {{?}}\n  <div class=\"rule-filter-container\"></div>\n  <div class=\"rule-operator-container\"></div>\n  <div class=\"rule-value-container\"></div>\n  <div class=\"rule-header\">\n      <button type=\"button\" data-confirm=\"rule\">\n        <i class=\"dashicons dashicons-yes\"></i>\n      </button>\n      <button type=\"button\" data-delete=\"rule\">\n        <i class=\"dashicons dashicons-no-alt\"></i>\n      </button>\n  </div>\n</li>";
var TemplateGroup = "\n<div id=\"{{= it.group_id }}\" class=\"rules-group-container\">\n  <div class=\"rules-group-header\">\n    <div class=\"group-actions\">\n\t\t<div class=\"ac-button-group\">    \n\t      <button type=\"button\" class=\"button ac-button__add-filter\" data-add=\"rule\">\n\t        {{=ac_search.i18n.add_filter}}\n\t      </button>\n\t      <button type=\"button\" class=\"button ac-button__segments\"></button>\n      \t</div>\n    </div>\n    {{? it.settings.display_errors }}\n      <div class=\"error-container\"><i class=\"{{= it.icons.error }}\"></i></div>\n    {{?}}\n  </div>\n  <div class=rules-group-body>\n    <ul class=rules-list></ul>\n  </div>\n</div>";


/***/ }),

/***/ "./search/js/modules/filter-types.js":
/*!*******************************************!*\
  !*** ./search/js/modules/filter-types.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _types_date__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../types/date */ "./search/js/types/date.js");
/* harmony import */ var _types_integer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./../types/integer */ "./search/js/types/integer.js");
/* harmony import */ var _types_ajax__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./../types/ajax */ "./search/js/types/ajax.js");
/* harmony import */ var _types_select2_preload__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./../types/select2_preload */ "./search/js/types/select2_preload.js");
/* harmony import */ var _types_select__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./../types/select */ "./search/js/types/select.js");





var ACFilterTypes = /** @class */ (function () {
    function ACFilterTypes() {
    }
    ACFilterTypes.addType = function ($class) {
        if (!this._types) {
            this._types = new Map();
        }
        this._types.set($class.filter_type, $class);
    };
    ACFilterTypes.getType = function ($type) {
        if (this._types.has($type)) {
            return this._types.get(type);
        }
        return false;
    };
    ACFilterTypes.getTypes = function () {
        if (!this._types) {
            return new Map();
        }
        return this._types;
    };
    return ACFilterTypes;
}());
// Register default types
ACFilterTypes.addType(_types_date__WEBPACK_IMPORTED_MODULE_0__["default"]);
ACFilterTypes.addType(_types_integer__WEBPACK_IMPORTED_MODULE_1__["default"]);
ACFilterTypes.addType(_types_ajax__WEBPACK_IMPORTED_MODULE_2__["default"]);
ACFilterTypes.addType(_types_select__WEBPACK_IMPORTED_MODULE_4__["default"]);
ACFilterTypes.addType(_types_select2_preload__WEBPACK_IMPORTED_MODULE_3__["default"]);
/* harmony default export */ __webpack_exports__["default"] = (ACFilterTypes);


/***/ }),

/***/ "./search/js/modules/operators.js":
/*!****************************************!*\
  !*** ./search/js/modules/operators.js ***!
  \****************************************/
/*! exports provided: getCustomOperators */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getCustomOperators", function() { return getCustomOperators; });
var getCustomOperators = function () {
    return jQuery.fn.queryBuilder.constructor.DEFAULTS.operators.concat([
        { type: 'date_past', nb_inputs: 0, multiple: false, apply_to: ['string'] },
        { type: 'date_future', nb_inputs: 0, multiple: false, apply_to: ['string'] },
        { type: 'date_today', nb_inputs: 0, multiple: false, apply_to: ['string'] },
        { type: 'lt_days_ago', nb_inputs: 1, multiple: false, apply_to: ['number'] },
        { type: 'gt_days_ago', nb_inputs: 1, multiple: false, apply_to: ['number'] },
        { type: 'within_days', nb_inputs: 1, multiple: false, apply_to: ['number'] }
    ]);
};


/***/ }),

/***/ "./search/js/modules/placement.js":
/*!****************************************!*\
  !*** ./search/js/modules/placement.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Placement = /** @class */ (function () {
    function Placement() {
    }
    Placement.prototype.getListScreenType = function () {
        var meta_type = AC.meta_type;
        var screen_type = meta_type;
        if (meta_type === 'post') {
            if (AC.list_screen === 'wp-media') {
                screen_type = 'media';
            }
        }
        return screen_type;
    };
    Placement.prototype.place = function () {
        switch (this.getListScreenType()) {
            case 'media':
                new MediaOverview();
                break;
            case 'term':
                new TermOverview();
                break;
            case 'user':
                new UserOverview();
                break;
            default:
                new PostOverview();
        }
        jQuery('body').addClass('ac-search-enabled');
    };
    return Placement;
}());
/* harmony default export */ __webpack_exports__["default"] = (Placement);
var MediaOverview = /** @class */ (function () {
    function MediaOverview() {
        var $search_container = jQuery('<div class="ac-search"></div>');
        var $media_filters = jQuery('.wp-filter');
        $media_filters.addClass('search-active');
        $search_container.append(jQuery('#ac-s'));
        $search_container.append($media_filters.find('select'));
        $search_container.append(jQuery('#post-query-submit'));
        $media_filters.addClass('ac-search-active').append($search_container);
    }
    return MediaOverview;
}());
var PostOverview = /** @class */ (function () {
    function PostOverview() {
        jQuery('.tablenav.top .actions').each(function () {
            var $container = jQuery(this);
            jQuery('body').addClass('ac-search-post');
            if (!$container.hasClass('bulkactions')) {
                $container.removeClass('alignleft').addClass('ac-search').prependTo('.tablenav.top');
                $container.find('#ac-s').prependTo($container);
            }
        });
    }
    return PostOverview;
}());
var TermOverview = /** @class */ (function () {
    function TermOverview() {
        var $search_container = jQuery('<div class="ac-search"></div>');
        var $add = jQuery('.ac-button__add-filter');
        var $button = jQuery('input[name=acp_filter_action]');
        var $filter_container = jQuery('.acp_tax_filters');
        var filter_count = jQuery('.acp-filter').length;
        $search_container.append(jQuery('#ac-s')).insertBefore('.tablenav:first .bulkactions').append($button);
        $filter_container.insertBefore($button);
        if (AdminColumns.Search.getRules().rules.length === 0 && filter_count === 0) {
            $button.addClass('-no-filters');
        }
        $button.on('click', function () {
            jQuery(this).parents('form').attr('method', 'get').submit();
        });
        $add.on('click', function () {
            $button.removeClass('-no-filters');
        });
        document.addEventListener('AC_Search_State_Change', function () {
            if (filter_count > 0) {
                $button.removeClass('-no-filters');
            }
        });
    }
    return TermOverview;
}());
var UserOverview = /** @class */ (function () {
    function UserOverview() {
        var $search_container = jQuery('<div class="ac-search"></div>');
        var $add = jQuery('.ac-button__add-filter');
        var $button = jQuery('[name=acp_filter_action]');
        var filter_count = jQuery('.acp-filter').length;
        $search_container.append(jQuery('#ac-s')).append(jQuery('[class*=acp-filter], .alignleft.actions .acp-range')).append($button);
        if (AdminColumns.Search.getRules().rules.length === 0 && filter_count === 0) {
            $button.addClass('-no-filters');
        }
        jQuery('.tablenav:eq(0)').prepend($search_container);
        $add.on('click', function () {
            $button.removeClass('-no-filters');
        });
        document.addEventListener('AC_Search_State_Change', function () {
            if (filter_count > 0) {
                $button.removeClass('-no-filters');
            }
        });
    }
    return UserOverview;
}());


/***/ }),

/***/ "./search/js/modules/rule.js":
/*!***********************************!*\
  !*** ./search/js/modules/rule.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Rule = /** @class */ (function () {
    function Rule($el, query) {
        this.$el = $el;
        this.query = query;
        if (!this.query) {
            this.query = AdminColumns.Search.query;
        }
    }
    Rule.prototype.getID = function () {
        return this.$el.attr('id');
    };
    Rule.prototype.getIndex = function () {
        var id = this.getID();
        return id.replace('ac-s_rule_', '');
    };
    Rule.prototype.getRule = function () {
        return this.query.getRule(this.getIndex());
    };
    Rule.prototype.getFilterID = function () {
        return this.$el.find('.rule-filter-container select').val();
    };
    Rule.prototype.getFilter = function () {
        var id = this.getFilterID();
        if (!id) {
            return false;
        }
        return this.query.getFilter(id);
    };
    Rule.prototype.getOperator = function () {
        return this.$el.find('.rule-operator-container select').val();
    };
    Rule.prototype.open = function () {
        var $el = this.$el;
        $el.removeClass('compact');
        $el.find('.rule-filter-container, .rule-operator-container, .rule-value-container, .rule-header [data-confirm]').show();
        if (AdminColumns.Search.defaults.operators.nullable.includes(this.getOperator())) {
            $el.find('.rule-value-container').hide();
        }
    };
    Rule.prototype.compact = function () {
        var $el = this.$el;
        this.updateDisplay($el);
        $el.addClass('compact');
        $el.find('.rule-filter-container, .rule-operator-container, .rule-value-container, .rule-header [data-confirm]').hide();
    };
    /**
     * Updates the human readable display for a collapsed filter
     *
     * @param $el
     */
    Rule.prototype.updateDisplay = function ($el) {
        var self = this;
        if (0 === $el.find('.rule-display').length) {
            $el.prepend('<div class="rule-display"></div>');
        }
        var $filter = $el.find('.rule-filter-container select option:selected');
        var $operator = $el.find('.rule-operator-container select option:selected');
        var $value_input = $el.find('.rule-value-container input[name], .rule-value-container select[name]');
        var value = '';
        if (1 === $value_input.length) {
            value = self.alterDisplayValue($value_input.val());
            if ($value_input.is('select')) {
                value = $value_input.find('option:selected').text();
            }
        }
        else {
            var values_1 = [];
            jQuery.each($value_input, function (k, value) {
                values_1.push(self.alterDisplayValue(jQuery(value).val()));
            });
            value = values_1.join(' - ');
        }
        // No value if nullable
        if (AdminColumns.Search.defaults.operators.nullable.includes($operator.val())) {
            value = '';
        }
        // Sanitize and don't allow HTML tags to prevent XSS issues
        value = jQuery("<div>" + value + "</div>").text();
        var string = "<span class=\"rule-display__filter\">" + $filter.data('label') + "</span>\n\t\t\t\t\t\t<span class=\"rule-display__operator\">" + $operator.text() + "</span>\n\t\t\t\t\t\t<span class=\"rule-display__value\">" + value + "</span>";
        $el.find('.rule-display').html(string);
    };
    Rule.prototype.alterDisplayValue = function (value) {
        var filter_type = AdminColumns.Search.filtertypes.getTypes().get(this.getFilter().filter_type);
        if (filter_type && filter_type.hasOwnProperty('ruleDisplayValue')) {
            value = filter_type.ruleDisplayValue(value, this);
        }
        return value;
    };
    return Rule;
}());
/* harmony default export */ __webpack_exports__["default"] = (Rule);


/***/ }),

/***/ "./search/js/modules/segments.ts":
/*!***************************************!*\
  !*** ./search/js/modules/segments.ts ***!
  \***************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _segments_modal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./segments/modal */ "./search/js/modules/segments/modal.ts");
/* harmony import */ var _helpers_url__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../helpers/url */ "./search/js/helpers/url.js");
/* harmony import */ var _segments_container__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./segments/container */ "./search/js/modules/segments/container.ts");
/* harmony import */ var _segments_toggle_button__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./segments/toggle-button */ "./search/js/modules/segments/toggle-button.ts");
/* harmony import */ var _segments_ajax__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./segments/ajax */ "./search/js/modules/segments/ajax.ts");
/* harmony import */ var _segments_instructions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./segments/instructions */ "./search/js/modules/segments/instructions.ts");
/* harmony import */ var _segments_query_params__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./segments/query-params */ "./search/js/modules/segments/query-params.ts");
/* harmony import */ var _segments_helpers__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./segments/helpers */ "./search/js/modules/segments/helpers.ts");








var canManageSegments = function () {
    return ac_search.capabilities.user_can_manage_shared_segments;
};
var Segments = /** @class */ (function () {
    function Segments(table, search, sorting, whitelistedUrlParams) {
        // Dependencies
        this.Table = table;
        this.Search = search;
        this.Sorting = sorting;
        this.WhitelistedUrlParams = whitelistedUrlParams;
        this.ToggleButton = new _segments_toggle_button__WEBPACK_IMPORTED_MODULE_3__["default"](document.querySelector('.ac-button__segments'));
        this.Container = new _segments_container__WEBPACK_IMPORTED_MODULE_2__["default"](document.querySelector('.ac-segments'), canManageSegments());
        this.Modal = new _segments_modal__WEBPACK_IMPORTED_MODULE_0__["default"](document.querySelector('#ac-modal-create-segment'), this);
        // Initiation
        AdminColumns.Modals.register(this.Modal, 'create_segment');
        this.initEvents();
    }
    Segments.prototype.getCurrentState = function () {
        return {
            rules: JSON.stringify(this.Search.getRules()),
            sorting: JSON.stringify(this.Sorting)
        };
    };
    Segments.prototype.unsetCurrent = function () {
        var url = window.location.href;
        this.Container.setCurrent('');
        history.replaceState({}, '', Object(_helpers_url__WEBPACK_IMPORTED_MODULE_1__["removeParameterFromUrl"])(url, 'ac-segment'));
    };
    Segments.prototype.checkButtonStatus = function () {
        var button = document.querySelector('.ac-segments__create .button');
        var rules = JSON.parse(this.getCurrentState().rules);
        if (rules.rules.length === 0) {
            button.setAttribute('disabled', 'disabled');
        }
        else {
            button.removeAttribute('disabled');
        }
    };
    Segments.prototype.initEvents = function () {
        var _this = this;
        this.ToggleButton.events.on('open', function () {
            _this.Container.show();
            _this.Container.moveToElement(_this.ToggleButton.element);
        });
        this.ToggleButton.events.on('close', function () {
            _this.Container.hide();
        });
        this.Container.el.addEventListener('click', function (e) { return e.stopPropagation(); });
        document.addEventListener('click', function () {
            if (_this.ToggleButton.isOpen())
                _this.ToggleButton.triggerClose();
        });
        this.Modal.events.on('save', function (data) {
            var queryStringParams = new _segments_query_params__WEBPACK_IMPORTED_MODULE_6__["default"](_this.Search);
            var global = !!data.get('global');
            _this.Modal.setLoading();
            Object(_segments_ajax__WEBPACK_IMPORTED_MODULE_4__["createSegment"])(data.get('name').toString(), global, queryStringParams.get().toString(), _this.WhitelistedUrlParams.getWhitelistedParams(), AC.ajax_nonce, AC.layout)
                .then(function (response) {
                _this.Modal.setLoading(false);
                if (response.data.success) {
                    var segment = Object(_segments_helpers__WEBPACK_IMPORTED_MODULE_7__["mapResponseSegmentToSegment"])(response.data.data.segment);
                    Segments.pushState(segment.getResponseItem());
                    _this.Container.refreshSegments(segment.getId().toString());
                    _this.Modal.finish();
                }
                else {
                    _this.Modal.displayError(response.data.data.error);
                }
            });
        });
        new _segments_instructions__WEBPACK_IMPORTED_MODULE_5__["default"]('.ac-segments__instructions', '#ac-segments-instructions');
    };
    Segments.pushState = function (segment) {
        history.replaceState({}, segment.name, segment.url);
    };
    return Segments;
}());
/* harmony default export */ __webpack_exports__["default"] = (Segments);


/***/ }),

/***/ "./search/js/modules/segments/ajax.ts":
/*!********************************************!*\
  !*** ./search/js/modules/segments/ajax.ts ***!
  \********************************************/
/*! exports provided: deleteSegment, retrieveSegments, createSegment */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "deleteSegment", function() { return deleteSegment; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "retrieveSegments", function() { return retrieveSegments; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createSegment", function() { return createSegment; });
var action = 'acp_search_segment_request';
var axios = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
var deleteSegment = function (id) {
    var data = {
        _ajax_nonce: AC.ajax_nonce,
        action: action,
        list_screen: AC.list_screen,
        layout: AC.layout,
        id: id,
        method: 'delete',
    };
    return axios.post(ajaxurl, mapDataToFormData(data));
};
var retrieveSegments = function () {
    var data = {
        _ajax_nonce: AC.ajax_nonce,
        action: action,
        list_screen: AC.list_screen,
        layout: AC.layout,
        method: 'read',
    };
    return axios.post(ajaxurl, mapDataToFormData(data));
};
var shareSegment = function (name, nonce, layout) {
    var data = {
        _ajax_nonce: nonce,
        action: action,
        layout: layout,
        name: name,
        method: 'share',
    };
    return axios.post(ajaxurl, mapDataToFormData(data));
};
var createSegment = function (name, global, querystring, whitelisted, nonce, layout_id) {
    var data = {
        _ajax_nonce: nonce,
        action: action,
        layout: layout_id,
        method: 'create',
        name: name,
        global: global ? 1 : 0
    };
    var formData = mapDataToFormData(data);
    var whitelisted_qs = new URLSearchParams();
    whitelisted.forEach(function (k) {
        whitelisted_qs.append(k, '');
    });
    formData.append('query_string', querystring);
    formData.append('whitelisted_query_string', whitelisted_qs.toString());
    return axios.post(ajaxurl, formData);
};
var mapDataToFormData = function (data, formData) {
    if (formData === void 0) { formData = null; }
    if (!formData) {
        formData = new FormData();
    }
    Object.keys(data).forEach(function (key) {
        formData.append(key, data[key]);
    });
    return formData;
};



/***/ }),

/***/ "./search/js/modules/segments/container.ts":
/*!*************************************************!*\
  !*** ./search/js/modules/segments/container.ts ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _helpers_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../helpers/strings */ "./search/js/helpers/strings.js");
/* harmony import */ var _helpers_document__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../helpers/document */ "./search/js/helpers/document.js");
/* harmony import */ var _ajax__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ajax */ "./search/js/modules/segments/ajax.ts");
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./helpers */ "./search/js/modules/segments/helpers.ts");




var SegmentContainer = /** @class */ (function () {
    function SegmentContainer(el, isAdmin) {
        this.el = el;
        this.current = null;
        this.isAdmin = isAdmin;
        this.addNewButton = el.querySelector('.ac-segments__create button');
        this.init();
        this.refreshSegments(this.el.dataset.initial);
    }
    SegmentContainer.prototype.init = function () {
        this.addNewButton.addEventListener('click', function () {
            AdminColumns.Modals.get('create_segment').open();
        });
    };
    SegmentContainer.prototype.checkPublicSectionState = function () {
        var publicSection = this.el.querySelector('.ac-segments__list.-global');
        if (!publicSection) {
            return;
        }
        if (this.el.querySelectorAll('.ac-segments__list.-global .ac-segments__list__items > *').length > 0) {
            publicSection.classList.add('-visible');
        }
        else {
            publicSection.classList.remove('-visible');
        }
    };
    SegmentContainer.prototype.show = function () {
        this.el.classList.add('-active');
    };
    SegmentContainer.prototype.hide = function () {
        this.el.classList.remove('-active');
    };
    SegmentContainer.prototype.moveToElement = function (el) {
        var offset = Object(_helpers_document__WEBPACK_IMPORTED_MODULE_1__["getOffset"])(el);
        this.el.style.left = Object(_helpers_strings__WEBPACK_IMPORTED_MODULE_0__["toPixel"])(offset.left + Math.round(el.offsetWidth / 2));
        this.el.style.top = Object(_helpers_strings__WEBPACK_IMPORTED_MODULE_0__["toPixel"])(offset.top);
    };
    SegmentContainer.prototype.removeSegment = function (id) {
        var row = this.el.querySelector(".ac-segments__list [data-id=\"" + id + "\"]");
        if (row) {
            row.remove();
            this.checkPublicSectionState();
        }
    };
    SegmentContainer.prototype.clearSegments = function () {
        this.el.querySelectorAll(".ac-segments__list__items").forEach(function (el) { return el.innerHTML = ''; });
    };
    SegmentContainer.prototype.addSegment = function (segment, isAdmin) {
        if (isAdmin === void 0) { isAdmin = false; }
        var element = document.createElement("div");
        var selector = segment.isGlobal() ? '.ac-segments__list.-global' : '.ac-segments__list.-personal';
        element.classList.add('ac-segment');
        element.dataset.id = segment.getId().toString();
        element.dataset.name = segment.getName();
        element.innerHTML = "<a class=\"ac-segment__label\" href=\"" + segment.getUrl() + "\">" + segment.getName() + "</a>";
        if (!segment.isGlobal() || isAdmin) {
            element.innerHTML += "<button class=\"ac-segment__action -delete\" data-action=\"delete\"><span class=\"dashicons dashicons-no-alt\"></span></button>";
        }
        this.el.querySelector(selector).querySelector('.ac-segments__list__items').insertAdjacentElement('beforeend', element);
        this.initSegmentEvents(element);
        this.checkPublicSectionState();
        return element;
    };
    SegmentContainer.prototype.setCurrent = function (id) {
        this.current = id;
        var segments = document.querySelectorAll('.ac-segments__list .ac-segment');
        for (var i = 0; i < segments.length; i++) {
            var segment_element = segments[i];
            segment_element.classList.remove('-current');
            if (segment_element.dataset.id === this.current) {
                segment_element.classList.add('-current');
            }
        }
    };
    SegmentContainer.prototype.initSegmentEvents = function (segment) {
        var _this = this;
        var id = segment.dataset.id;
        if (!segment) {
            return;
        }
        segment.querySelectorAll('[data-action=delete]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                Object(_ajax__WEBPACK_IMPORTED_MODULE_2__["deleteSegment"])(id).then(function (r) {
                    if (r.data.success) {
                        _this.removeSegment(id);
                    }
                });
            });
        });
    };
    SegmentContainer.prototype.refreshSegments = function (current) {
        var _this = this;
        if (current === void 0) { current = null; }
        Object(_ajax__WEBPACK_IMPORTED_MODULE_2__["retrieveSegments"])().then(function (response) {
            _this.clearSegments();
            response.data.data.forEach(function (s) {
                var segment = Object(_helpers__WEBPACK_IMPORTED_MODULE_3__["mapResponseSegmentToSegment"])(s);
                if (segment) {
                    _this.addSegment(segment, _this.isAdmin);
                }
            });
            if (AdminColumns.hasOwnProperty('Tooltips')) {
                AdminColumns.Tooltips.init();
            }
            if (current) {
                _this.setCurrent(current);
            }
        });
        this.checkPublicSectionState();
    };
    return SegmentContainer;
}());
/* harmony default export */ __webpack_exports__["default"] = (SegmentContainer);


/***/ }),

/***/ "./search/js/modules/segments/helpers.ts":
/*!***********************************************!*\
  !*** ./search/js/modules/segments/helpers.ts ***!
  \***********************************************/
/*! exports provided: mapResponseSegmentToSegment */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "mapResponseSegmentToSegment", function() { return mapResponseSegmentToSegment; });
/* harmony import */ var _segment__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./segment */ "./search/js/modules/segments/segment.ts");

var mapResponseSegmentToSegment = function (item) {
    return new _segment__WEBPACK_IMPORTED_MODULE_0__["default"](item.id, item.name, item.url, item.global == 1);
};


/***/ }),

/***/ "./search/js/modules/segments/instructions.ts":
/*!****************************************************!*\
  !*** ./search/js/modules/segments/instructions.ts ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Instructions = /** @class */ (function () {
    function Instructions(element_selector, info_selector) {
        // create pointer
        var el = jQuery(element_selector);
        var info = jQuery(info_selector);
        var timeout = null;
        el.pointer({
            content: info.html(),
            position: {
                edge: 'left',
                align: 'middle'
            },
            pointerClass: 'ac-wp-pointer wp-pointer wp-pointer-left noclick -nodismiss',
            pointerWidth: 250,
        });
        el.hover(function () {
            el.pointer('open');
            clearTimeout(timeout);
        }, function () {
            timeout = setTimeout(function () {
                el.pointer('close');
            }, 500);
        });
    }
    return Instructions;
}());
/* harmony default export */ __webpack_exports__["default"] = (Instructions);


/***/ }),

/***/ "./search/js/modules/segments/modal.ts":
/*!*********************************************!*\
  !*** ./search/js/modules/segments/modal.ts ***!
  \*********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _admin_columns_src_js_modules_modal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../../admin-columns/src/js/modules/modal */ "../admin-columns/src/js/modules/modal.ts");
var __extends = (undefined && undefined.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();

var nanobus = __webpack_require__(/*! nanobus */ "./node_modules/nanobus/index.js");
var SegmentModal = /** @class */ (function (_super) {
    __extends(SegmentModal, _super);
    function SegmentModal(el, segments) {
        var _this = _super.call(this, el) || this;
        _this.segments = segments;
        _this.form = document.querySelector('form#frm_create_segment');
        _this.formelements = _this.form.elements;
        _this.events = nanobus();
        _this.initFormEvents();
        return _this;
    }
    SegmentModal.prototype.initFormEvents = function () {
        var _this = this;
        this.form.addEventListener('submit', function (e) {
            e.preventDefault();
            _this.save();
        });
        this.el.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        this.form.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                _this.form.dispatchEvent(new Event('submit'));
            }
        });
    };
    SegmentModal.prototype.reset = function () {
        this.getNameElement().value = '';
    };
    SegmentModal.prototype.onOpen = function () {
        var _this = this;
        this.reset();
        setTimeout(function () {
            _this.getNameElement().focus();
        }, 100);
    };
    SegmentModal.prototype.finish = function () {
        this.reset();
        this.close();
    };
    SegmentModal.prototype.getNameElement = function () {
        return this.formelements.namedItem('name');
    };
    SegmentModal.prototype.save = function () {
        this.events.emit('save', new FormData(this.form));
    };
    SegmentModal.prototype.setLoading = function (on) {
        if (on === void 0) { on = true; }
        var loading = this.el.querySelector('.ac-modal__loading');
        (on)
            ? loading.style.display = 'inline-block'
            : loading.style.display = 'none';
    };
    SegmentModal.prototype.displayError = function (msg) {
        this.el.querySelector('.ac-modal__error').textContent = msg;
    };
    return SegmentModal;
}(_admin_columns_src_js_modules_modal__WEBPACK_IMPORTED_MODULE_0__["default"]));
/* harmony default export */ __webpack_exports__["default"] = (SegmentModal);


/***/ }),

/***/ "./search/js/modules/segments/query-params.ts":
/*!****************************************************!*\
  !*** ./search/js/modules/segments/query-params.ts ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var QueryParams = /** @class */ (function () {
    function QueryParams(search) {
        this.query = new URLSearchParams(window.location.search);
        this.search = search;
        this.handleSorting();
        this.handleSearch();
    }
    QueryParams.prototype.handleSorting = function () {
        if (!this.query.get('orderby') && typeof ACP_Sorting !== 'undefined') {
            this.query.set('orderby', ACP_Sorting.orderby);
        }
        if (!this.query.get('order') && typeof ACP_Sorting !== 'undefined') {
            this.query.set('order', ACP_Sorting.orderby);
        }
    };
    QueryParams.prototype.handleSearch = function () {
        this.query.set('ac-rules', JSON.stringify(this.search.getRules()));
    };
    QueryParams.prototype.get = function () {
        return this.query;
    };
    return QueryParams;
}());
/* harmony default export */ __webpack_exports__["default"] = (QueryParams);


/***/ }),

/***/ "./search/js/modules/segments/segment.ts":
/*!***********************************************!*\
  !*** ./search/js/modules/segments/segment.ts ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var Segment = /** @class */ (function () {
    function Segment(id, name, url, global) {
        this.id = id;
        this.name = name;
        this.url = url;
        this.global = global;
    }
    Segment.prototype.getId = function () {
        return this.id;
    };
    Segment.prototype.isGlobal = function () {
        return this.global;
    };
    Segment.prototype.getName = function () {
        return this.name;
    };
    Segment.prototype.getUrl = function () {
        return this.url;
    };
    Segment.prototype.getResponseItem = function () {
        return {
            id: this.getId(),
            name: this.getName(),
            url: this.getUrl(),
            global: this.isGlobal() ? 1 : 0
        };
    };
    return Segment;
}());
/* harmony default export */ __webpack_exports__["default"] = (Segment);


/***/ }),

/***/ "./search/js/modules/segments/toggle-button.ts":
/*!*****************************************************!*\
  !*** ./search/js/modules/segments/toggle-button.ts ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var nanobus = __webpack_require__(/*! nanobus */ "./node_modules/nanobus/index.js");
var ToggleButton = /** @class */ (function () {
    function ToggleButton(element) {
        this.element = element;
        this.events = nanobus();
        this.init();
    }
    ToggleButton.prototype.init = function () {
        var _this = this;
        this.element.addEventListener('click', function (e) {
            e.stopPropagation();
            if (_this.isOpen()) {
                _this.triggerClose();
            }
            else {
                _this.triggerOpen();
            }
        });
    };
    ToggleButton.prototype.isOpen = function () {
        return this.element.classList.contains('-open');
    };
    ToggleButton.prototype.triggerOpen = function () {
        this.element.classList.add('-open');
        this.events.emit('open');
    };
    ToggleButton.prototype.triggerClose = function () {
        this.element.classList.remove('-open');
        this.events.emit('close');
    };
    return ToggleButton;
}());
/* harmony default export */ __webpack_exports__["default"] = (ToggleButton);


/***/ }),

/***/ "./search/js/modules/segments/whitelisted-url-params.ts":
/*!**************************************************************!*\
  !*** ./search/js/modules/segments/whitelisted-url-params.ts ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var WhitelistedUrlParams = /** @class */ (function () {
    function WhitelistedUrlParams(element) {
        this.element = element;
        this.queryParams = new URLSearchParams(window.location.search);
    }
    WhitelistedUrlParams.prototype.hasUrlParameter = function (key) {
        return null !== this.queryParams.get(key);
    };
    /**
     * Return filters that are not managed by Admin Columns but are active
     */
    WhitelistedUrlParams.prototype.getWhitelistedParams = function () {
        var _this = this;
        var params = [
            'ac-rules', 'orderby', 'order', 's', 'post_status', 'comment_status'
        ];
        var allowedTagNames = ['SELECT', 'INPUT'];
        var siblings = Array.prototype.filter.call(this.element.parentNode.children, function (sibling) {
            return allowedTagNames.includes(sibling.tagName) && sibling !== _this.element;
        });
        siblings.forEach(function (element) {
            var name = element.getAttribute('name');
            if (_this.hasUrlParameter(name)) {
                params.push(name);
            }
        });
        // Specific ACP logic
        this.element.parentNode.querySelectorAll('.acp-range input').forEach(function (input) {
            var name = input.getAttribute('name');
            if (_this.hasUrlParameter(name)) {
                params.push(name);
            }
        });
        return params;
    };
    return WhitelistedUrlParams;
}());
/* harmony default export */ __webpack_exports__["default"] = (WhitelistedUrlParams);


/***/ }),

/***/ "./search/js/table.js":
/*!****************************!*\
  !*** ./search/js/table.js ***!
  \****************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* WEBPACK VAR INJECTION */(function(global) {/* harmony import */ var _modules_ac_query_builder_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/ac-query-builder.js */ "./search/js/modules/ac-query-builder.js");
/* harmony import */ var _modules_segments_ts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/segments.ts */ "./search/js/modules/segments.ts");
/* harmony import */ var _modules_segments_whitelisted_url_params__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/segments/whitelisted-url-params */ "./search/js/modules/segments/whitelisted-url-params.ts");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_3__);




global.AdminColumns = typeof AdminColumns !== "undefined" ? AdminColumns : {};
jquery__WEBPACK_IMPORTED_MODULE_3___default()(document).ready(function () {
    if (!AC.layout) {
        return;
    }
    if (jquery__WEBPACK_IMPORTED_MODULE_3___default()('#ac-s').length && ac_search.filters.length) {
        AdminColumns.Search = new _modules_ac_query_builder_js__WEBPACK_IMPORTED_MODULE_0__["default"]('#ac-s', ac_search);
        AdminColumns.Search.init();
        if (document.querySelector('.ac-segments')) {
            AdminColumns.Segments = new _modules_segments_ts__WEBPACK_IMPORTED_MODULE_1__["default"](AdminColumns.Table, AdminColumns.Search, ACP_Sorting, new _modules_segments_whitelisted_url_params__WEBPACK_IMPORTED_MODULE_2__["default"](document.querySelector('#ac-s')));
        }
        jquery__WEBPACK_IMPORTED_MODULE_3___default()('[name="acp_filter_action"], #post-query-submit').addClass('ac-search-button');
    }
    // If Search is disabled, segments can't work
    if (!document.getElementById('ac-s') && AdminColumns.Table && AdminColumns.Table.Actions) {
        jquery__WEBPACK_IMPORTED_MODULE_3___default()('.ac-table-button.-segments').remove();
        AdminColumns.Table.Actions.refresh();
    }
    jquery__WEBPACK_IMPORTED_MODULE_3___default()(document).on('wheel', '.ui-datepicker-year', function (e) {
        e.preventDefault();
        if (e.originalEvent.deltaY > 1) {
            jquery__WEBPACK_IMPORTED_MODULE_3___default()(this).find('option:selected').next().prop('selected', true).trigger('change');
        }
        if (e.originalEvent.deltaY < -1) {
            jquery__WEBPACK_IMPORTED_MODULE_3___default()(this).find('option:selected').prev().prop('selected', true).trigger('change');
        }
    });
});

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../node_modules/webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./search/js/types/ajax.js":
/*!*********************************!*\
  !*** ./search/js/types/ajax.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _default__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./default */ "./search/js/types/default.js");

/* harmony default export */ __webpack_exports__["default"] = (Object.assign({}, _default__WEBPACK_IMPORTED_MODULE_0__["default"], {
    filter_type: 'select2_ajax',
    renderInput: function (rule) {
        if (rule.getFilter().filter_type !== this.filter_type) {
            return;
        }
        var rule_request = rule.getRule();
        var filter = rule.getFilter();
        var $select = rule.$el.find('.rule-value-container select');
        var $rule_container = $select.closest('.rule-container');
        if (rule_request && rule_request.formatted_value) {
            $select.append(jQuery('<option/>', {
                value: rule_request.value,
                text: rule_request.formatted_value,
                selected: true
            })).trigger('change');
            $rule_container.data('formatted_value', rule_request.formatted_value);
            AdminColumns.Search.validateForm();
        }
        $select.ac_select2({
            width: 200,
            theme: 'acs2',
            escapeMarkup: function (markup) {
                return jQuery("<div>" + markup + "</div>").text();
            },
            ajax: {
                method: 'post',
                url: ajaxurl,
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        action: 'acp_search_comparison_request',
                        method: 'get_options',
                        layout: AC.layout,
                        searchterm: params.term,
                        page: params.page ? params.page : 1,
                        column: filter.id,
                        list_screen: AC.list_screen,
                        item_id: 47,
                        _ajax_nonce: AC.ajax_nonce
                    };
                },
                processResults: function (response) {
                    return response.data;
                },
            }
        });
        $select.on('select2:select', function () {
            $rule_container.data('formatted_value', $select.find('option:selected').text());
            $rule_container.find('[data-confirm]').trigger('click').hide();
            jQuery(this).closest('[data-confirm]').trigger('click').hide();
        });
    },
    transformFilter: function (filter) {
        if (filter.use_ajax && filter.use_pagination) {
            filter.filter_type = this.filter_type;
            filter.input = 'select';
        }
        return filter;
    }
}));


/***/ }),

/***/ "./search/js/types/date.js":
/*!*********************************!*\
  !*** ./search/js/types/date.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _default__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./default */ "./search/js/types/default.js");
/* harmony import */ var _helpers_date__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../helpers/date */ "./search/js/helpers/date.js");


var i18n = {
    DAYS_AGO: ac_search.i18n.days_ago,
    DAYS: ac_search.i18n.days,
};
/* harmony default export */ __webpack_exports__["default"] = (Object.assign({}, _default__WEBPACK_IMPORTED_MODULE_0__["default"], {
    filter_type: 'date',
    renderDatePickerInputs: function (rule) {
        var $el1 = rule.$el.find('.rule-value-container input:first');
        var $el2 = rule.$el.find('.rule-value-container input:eq(1)');
        $el1.css({ position: 'relative', 'z-index': 1000 }).on('keydown', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 9) {
                e.preventDefault();
            }
        }).attr('autocomplete', 'off').datepicker({
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            yearRange: "c-60:c+10",
            beforeShow: function () {
                jQuery('body').addClass('ac-jqui');
                jQuery('#ui-datepicker-div .ui-datepicker-year').hide();
            },
            onClose: function (selectedDate) {
                $el2.datepicker("option", "minDate", selectedDate).focus();
            }
        });
        $el2.css({ position: 'relative', 'z-index': 1000 }).attr('autocomplete', 'off').datepicker({
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            yearRange: "c-60:c+10",
            beforeShow: function () {
                jQuery('body').addClass('ac-jqui');
            },
            onClose: function (selectedDate) {
                $el1.datepicker("option", "maxDate", selectedDate);
            }
        });
        this.setAfterLabel(rule, '');
    },
    getAfterLabel: function (rule) {
        return rule.$el.find('.rule-value-container .rule-value-after-label').html();
    },
    setAfterLabel: function (rule, label) {
        if (!rule.$el) {
            return;
        }
        if (rule.$el.find('.rule-value-container .rule-value-after-label').length === 0) {
            rule.$el.find('.rule-value-container').append("<span class=\"rule-value-after-label\"></span>");
        }
        rule.$el.find('.rule-value-container .rule-value-after-label').html(label);
    },
    renderNumericInputs: function (rule) {
        rule.$el.find('.rule-value-container input').attr('type', 'number').datepicker('destroy');
    },
    /**
     * @param {Rule} rule
     */
    renderInput: function (rule) {
        var _this = this;
        if (this.filter_type !== rule.getFilter().filter_type) {
            return;
        }
        var $el2 = rule.$el.find('.rule-value-container input:eq(1)');
        if ($el2.is(':visible')) {
            rule.$el.find('.rule-value-container').addClass('between').find('.ac-s__separator').text('-');
        }
        var operatorSelect = rule.$el.find('.rule-operator-container select');
        operatorSelect.on('change', function () {
            _this.determineRenderInputs(rule);
        });
        this.determineRenderInputs(rule);
        rule.$el.addClass("rule-container--date");
    },
    determineAfterLabel: function (rule) {
        switch (rule.getOperator()) {
            case 'lt_days_ago':
            case 'gt_days_ago':
                this.setAfterLabel(rule, i18n.DAYS_AGO);
                break;
            case 'within_days':
                this.setAfterLabel(rule, i18n.DAYS);
                break;
            default:
                this.setAfterLabel(rule, '');
        }
    },
    determineRenderInputs: function (rule) {
        switch (rule.getOperator()) {
            case 'lt_days_ago':
            case 'gt_days_ago':
            case 'within_days':
                this.renderNumericInputs(rule);
                break;
            default:
                this.renderDatePickerInputs(rule);
        }
        this.determineAfterLabel(rule);
    },
    transformFilter: function (filter) {
        if (filter.type === 'date' || filter.type === 'datetime') {
            filter.filter_type = this.filter_type;
        }
        return filter;
    },
    ruleDisplayValue: function (value, rule) {
        if (!value) {
            return value;
        }
        switch (rule.getOperator()) {
            case 'lt_days_ago':
            case 'gt_days_ago':
            case 'within_days':
                return value + ' ' + this.getAfterLabel(rule);
            default:
                return Object(_helpers_date__WEBPACK_IMPORTED_MODULE_1__["formatDate"])('d M yy', value);
        }
    }
}));


/***/ }),

/***/ "./search/js/types/default.js":
/*!************************************!*\
  !*** ./search/js/types/default.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
var defaultType = {
    filter_type: '',
    /**
     * @param {Rule} rule
     * */
    renderInput: function (rule) { },
    transformFilter: function (filter) {
        return filter;
    },
    /** @param {Rule} rule */
    transformRuleOperator: function (rule) { },
    ruleDisplayValue: function (value, rule) {
        return value;
    }
};
/* harmony default export */ __webpack_exports__["default"] = (defaultType);


/***/ }),

/***/ "./search/js/types/integer.js":
/*!************************************!*\
  !*** ./search/js/types/integer.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _default__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./default */ "./search/js/types/default.js");

/* harmony default export */ __webpack_exports__["default"] = (Object.assign({}, _default__WEBPACK_IMPORTED_MODULE_0__["default"], {
    filter_type: 'integer',
    /** @param {Rule} rule */
    renderInput: function (rule) {
        var $el1 = rule.$el.find('.rule-value-container input:first');
        var $el2 = rule.$el.find('.rule-value-container input:eq(1)');
        $el1.on('blur change', function () {
            var minvalue = parseInt(jQuery(this).val());
            var maxvalue = parseInt($el2.val());
            if (maxvalue < minvalue) {
                $el2.val(minvalue);
            }
        });
        $el2.on('change', function () {
            var maxvalue = parseInt(jQuery(this).val());
            var minvalue = parseInt($el1.val());
            if (minvalue > maxvalue) {
                $el1.val(maxvalue);
            }
        });
    },
    transformFilter: function (filter) {
        if (filter.type === 'integer') {
            filter.filter_type = this.filter_type;
        }
        if (filter.type === 'double') {
            filter.filter_type = this.filter_type;
            filter.validation = {
                step: 0.01
            };
        }
        return filter;
    }
}));


/***/ }),

/***/ "./search/js/types/select.js":
/*!***********************************!*\
  !*** ./search/js/types/select.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _default__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./default */ "./search/js/types/default.js");

/* harmony default export */ __webpack_exports__["default"] = (Object.assign({}, _default__WEBPACK_IMPORTED_MODULE_0__["default"], {
    filter_type: 'select',
    /** @param {Rule} rule */
    renderInput: function (rule) {
        var filter = rule.getFilter();
        if (filter.input !== 'select' || filter.use_ajax) {
            return;
        }
        var settings = {
            width: 150,
            theme: 'acs2',
        };
        if (Object.keys(filter.values).length < 10) {
            settings.minimumResultsForSearch = -1;
        }
        rule.$el.find('.rule-value-container select').select2(settings);
    },
    transformFilter: function (filter) {
        if (typeof filter.values === 'object') {
            filter.input = 'select';
        }
        return filter;
    }
}));


/***/ }),

/***/ "./search/js/types/select2_preload.js":
/*!********************************************!*\
  !*** ./search/js/types/select2_preload.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _default__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./default */ "./search/js/types/default.js");

/* harmony default export */ __webpack_exports__["default"] = (Object.assign({}, _default__WEBPACK_IMPORTED_MODULE_0__["default"], {
    filter_type: 'select2_preload',
    renderInput: function (rule) {
        if (rule.getFilter().filter_type !== this.filter_type) {
            return;
        }
        var self = this;
        var filter = rule.getFilter();
        var rule_request = rule.getRule();
        var $select = rule.$el.find('.rule-value-container select');
        $select.prop('disabled', true).after('<span class="spinner"></span>');
        if (filter.ajax_options) {
            this.renderSelect2(rule, filter.ajax_options);
        }
        else {
            this.getPreloadedOptions(filter).done(function (response) {
                filter.ajax_options = response.data.results;
                self.renderSelect2(rule, response.data.results);
            });
        }
        if (rule_request) {
            $select.append(jQuery('<option/>', {
                value: rule_request.value,
                text: rule_request.formatted_value,
                selected: true
            })).trigger('change');
            AdminColumns.Search.validateForm();
        }
    },
    renderSelect2: function (rule, options) {
        var $select = rule.$el.find('.rule-value-container select');
        var $rule_container = $select.closest('.rule-container');
        $select.prop('disabled', false);
        $rule_container.find('.spinner').remove();
        $select.ac_select2({
            width: 200,
            theme: 'acs2',
            data: options,
            minimumResultsForSearch: 10
        }).trigger('change');
        $rule_container.data('formatted_value', $select.find('option:selected').text());
        $select.on('select2:select', function () {
            $rule_container.data('formatted_value', $select.find('option:selected').text());
            $rule_container.find('[data-confirm]').trigger('click').hide();
            jQuery(this).closest('[data-confirm]').trigger('click').hide();
        });
    },
    getPreloadedOptions: function (filter) {
        return jQuery.ajax({
            url: ajaxurl,
            dataType: 'json',
            method: 'post',
            data: {
                action: 'acp_search_comparison_request',
                method: 'get_options',
                layout: AC.layout,
                column: filter.id,
                list_screen: AC.list_screen,
                item_id: 47,
                _ajax_nonce: AC.ajax_nonce
            }
        });
    },
    transformFilter: function (filter) {
        if (filter.use_ajax && !filter.use_pagination) {
            filter.filter_type = this.filter_type;
            filter.input = 'select';
        }
        return filter;
    }
}));


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ })

/******/ });
//# sourceMappingURL=table.bundle.js.map