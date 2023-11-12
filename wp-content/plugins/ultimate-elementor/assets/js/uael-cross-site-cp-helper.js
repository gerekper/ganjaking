/**
 * Created by dagan on 07/04/2014.
 */
'use strict';
/* global console, XsUtils */
window.XsUtils = window.XsUtils || (function () {

  function extend(object, defaultObject) {
    var result = defaultObject || {};
    var key;
    for (key in object) {
      if (object.hasOwnProperty(key)) {
        result[key] = object[key];
      }
    }
    return result;
  }

  //public interface
  return {
    extend: extend
  };
})();

window.xsLocalStorage = window.xsLocalStorage || (function () {
  var MESSAGE_NAMESPACE = 'cross-domain-local-message-uae';
  var options = {
    iframeId: 'cross-domain-iframe-uae',
    iframeUrl: undefined,
    initCallback: function () {}
  };
  var requestId = -1;
  var iframe;
  var requests = {};
  var wasInit = false;
  var iframeReady = true;

  function applyCallback(data) {
    if (requests[data.id]) {
      requests[data.id](data);
      delete requests[data.id];
    }
  }

  function receiveMessage(event) {
    var data;
    try {
      data = JSON.parse(event.data);
    } catch (err) {
      //not our message, can ignore
    }
    if (data && data.namespace === MESSAGE_NAMESPACE) {
      if (data.id === 'iframe-ready') {
        iframeReady = true;
        options.initCallback();
      } else {
        applyCallback(data);
      }
    }
  }

  function buildMessage(action, key, value, callback) {
    requestId++;
    requests[requestId] = callback;
    var data = {
      namespace: MESSAGE_NAMESPACE,
      id: requestId,
      action: action,
      key: key,
      value: value
    };
    iframe.contentWindow.postMessage(JSON.stringify(data), '*');
  }

  function init(customOptions) {
    options = XsUtils.extend(customOptions, options);
    var temp = document.createElement('div');

    if (window.addEventListener) {
      window.addEventListener('message', receiveMessage, false);
    } else {
      window.attachEvent('onmessage', receiveMessage);
    }

    temp.innerHTML = '<iframe id="' + options.iframeId + '" src=' + options.iframeUrl + ' style="display: none;"></iframe>';
    document.body.appendChild(temp);
    iframe = document.getElementById(options.iframeId);
  }

  function isApiReady() {
    if (!wasInit) {
      return false;
    }
    if (!iframeReady) {
      return false;
    }
    return true;
  }

  function isDomReady() {
    return (document.readyState === 'complete');
  }

  return {
    //callback is optional for cases you use the api before window load.
    init: function (customOptions) {
      if (!customOptions.iframeUrl) {
        throw 'Please specify the iframe URL';
      }
      if (wasInit) {
        return;
      }
      wasInit = true;
      if (isDomReady()) {
        init(customOptions);
      } else {
        if (document.addEventListener) {
          // All browsers expect IE < 9
          document.addEventListener('readystatechange', function () {
            if (isDomReady()) {
              init(customOptions);
            }
          });
        } else {
          // IE < 9
          document.attachEvent('readystatechange', function () {
            if (isDomReady()) {
              init(customOptions);
            }
          });
        }
      }
    },
    setItem: function (key, value, callback) {
      if (!isApiReady()) {
        return;
      }
      buildMessage('set', key, value, callback);
    },

    getItem: function (key, callback) {
      if (!isApiReady()) {
        return;
      }
      buildMessage('get', key,  null, callback);
    },
    removeItem: function (key, callback) {
      if (!isApiReady()) {
        return;
      }
      buildMessage('remove', key,  null, callback);
    },
    key: function (index, callback) {
      if (!isApiReady()) {
        return;
      }
      buildMessage('key', index,  null, callback);
    },
    getSize: function(callback) {
      if(!isApiReady()) {
        return;
      }
      buildMessage('size', null, null, callback);
    },
    getLength: function(callback) {
      if(!isApiReady()) {
        return;
      }
      buildMessage('length', null, null, callback);
    },
    clear: function (callback) {
      if (!isApiReady()) {
        return;
      }
      buildMessage('clear', null,  null, callback);
    },
    wasInit: function () {
      return wasInit;
    }
  };
})();
