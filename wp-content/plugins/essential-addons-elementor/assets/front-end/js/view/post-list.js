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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/post-list.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/post-list.js":
/*!**********************************!*\
  !*** ./src/js/view/post-list.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("var postListHandler = function postListHandler($scope, $) {\n  // category\n  ea.getToken();\n  var $post_cat_wrap = $('.post-categories', $scope),\n    $scroll_on_pagination = parseInt($post_cat_wrap.data('scroll-on-pagination')),\n    $scroll_on_pagination_offset = parseInt($post_cat_wrap.data('scroll-offset'));\n  $post_cat_wrap.on('click', 'a', function (e) {\n    e.preventDefault();\n    var $this = $(this);\n    // tab class\n    $('.post-categories a', $scope).removeClass('active');\n    $this.addClass('active');\n    // collect props\n    var $class = $post_cat_wrap.data('class'),\n      $widget_id = $post_cat_wrap.data(\"widget\"),\n      $page_id = $post_cat_wrap.data(\"page-id\"),\n      $nonce = $post_cat_wrap.data(\"nonce\"),\n      $args = $post_cat_wrap.data('args'),\n      $settings = $post_cat_wrap.data('settings'),\n      $page = 1,\n      $template_info = $post_cat_wrap.data('template'),\n      $taxonomy = {\n        taxonomy: $('.post-categories a.active', $scope).data('taxonomy'),\n        field: 'term_id',\n        terms: $('.post-categories a.active', $scope).data('id')\n      };\n\n    // ajax\n    $.ajax({\n      url: localize.ajaxurl,\n      type: 'POST',\n      data: {\n        action: 'load_more',\n        \"class\": $class,\n        args: $args,\n        taxonomy: $taxonomy,\n        settings: $settings,\n        template_info: $template_info,\n        page: $page,\n        page_id: $page_id,\n        widget_id: $widget_id,\n        nonce: localize.nonce\n      },\n      success: function success(response) {\n        var $content = $(response);\n        if ($content.hasClass('no-posts-found') || $content.length == 0) {\n          $('.eael-post-appender', $scope).empty().append($content);\n\n          // update nav\n          $('.btn-prev-post', $scope).prop('disabled', true);\n          $('.btn-next-post', $scope).prop('disabled', true);\n        } else {\n          $('.eael-post-appender', $scope).empty().append($content);\n\n          // update page\n          $('.post-list-pagination', $scope).data('page', 1);\n\n          // update nav\n          $('.btn-prev-post', $scope).prop('disabled', true);\n          $('.btn-next-post', $scope).prop('disabled', false);\n        }\n      },\n      error: function error(response) {\n        console.log(response);\n      }\n    });\n  });\n\n  // load more\n  var $pagination_wrap = $('.post-list-pagination', $scope);\n  $pagination_wrap.on('click', 'button', function (e) {\n    e.preventDefault();\n    e.stopPropagation();\n    e.stopImmediatePropagation();\n    // collect props\n    var $this = $(this),\n      $widget_id = $pagination_wrap.data(\"widget\"),\n      $page_id = $pagination_wrap.data(\"page-id\"),\n      $nonce = $pagination_wrap.data(\"nonce\"),\n      $class = $pagination_wrap.data('class'),\n      $args = $pagination_wrap.data('args'),\n      $settings = $pagination_wrap.data('settings'),\n      $page = $this.hasClass('btn-prev-post') ? parseInt($pagination_wrap.data('page')) - 1 : parseInt($pagination_wrap.data('page')) + 1,\n      $template_info = $pagination_wrap.data('template'),\n      $taxonomy = {\n        taxonomy: $('.post-categories a.active', $scope).data('taxonomy'),\n        field: 'term_id',\n        terms: $('.post-categories a.active', $scope).data('id')\n      };\n    if ($taxonomy.taxonomy === '' || $taxonomy.taxonomy === 'all' || $taxonomy.taxonomy === 'undefined') {\n      $taxonomy.taxonomy = 'all';\n    }\n    if ($page == 1 && $this.hasClass(\"btn-prev-post\")) {\n      $this.prop('disabled', true);\n    }\n    $this.prop('disabled', true);\n    if ($page <= 0) {\n      return;\n    }\n    $.ajax({\n      url: localize.ajaxurl,\n      type: 'post',\n      data: {\n        action: 'load_more',\n        \"class\": $class,\n        args: $args,\n        taxonomy: $taxonomy,\n        settings: $settings,\n        page: $page,\n        template_info: $template_info,\n        page_id: $page_id,\n        widget_id: $widget_id,\n        nonce: localize.nonce\n      },\n      success: function success(response) {\n        var $content = $(response);\n        if ($content.hasClass('no-posts-found') || $content.length == 0) {\n          // do nothing\n        } else {\n          $('.eael-post-appender', $scope).empty().append($content);\n          if ($page == 1 && $this.hasClass(\"btn-prev-post\")) {\n            $this.prop('disabled', true);\n          } else {\n            $('.post-list-pagination button', $scope).prop('disabled', false);\n          }\n          $pagination_wrap.data('page', $page);\n        }\n        if ($scroll_on_pagination && $('.eael-post-appender', $scope).length > 0) {\n          var $post_list_container = $('.eael-post-list-container', $scope);\n          if (!isElementInViewport($post_list_container)) {\n            $('html, body').animate({\n              scrollTop: $post_list_container.offset().top - $scroll_on_pagination_offset\n            }, 500);\n          }\n        }\n      },\n      error: function error(response) {\n        console.log(response);\n      }\n    });\n  });\n  function isElementInViewport(el) {\n    if (typeof jQuery === \"function\" && el instanceof jQuery) {\n      el = el[0];\n    }\n    var rect = el.getBoundingClientRect();\n    return rect.top >= 0 && rect.left >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /* or $(window).height() */\n    rect.right <= (window.innerWidth || document.documentElement.clientWidth) /* or $(window).width() */;\n  }\n};\n\njQuery(window).on('elementor/frontend/init', function () {\n  elementorFrontend.hooks.addAction('frontend/element_ready/eael-post-list.default', postListHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/post-list.js?");

/***/ })

/******/ });