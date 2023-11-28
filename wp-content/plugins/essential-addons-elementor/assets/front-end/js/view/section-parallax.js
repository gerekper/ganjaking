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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/section-parallax.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/section-parallax.js":
/*!*****************************************!*\
  !*** ./src/js/view/section-parallax.js ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var EaelParallaxHandler = function EaelParallaxHandler($scope, $) {\n  var target = $scope,\n    sectionId = target.data(\"id\"),\n    settings = false,\n    editMode = elementorFrontend.isEditMode();\n  if (editMode) {\n    settings = generateEditorSettings(sectionId);\n  }\n  if (!editMode || !settings) {\n    return false;\n  }\n  if (settings[0] == \"yes\") {\n    if (\"multi\" != settings[1] && \"automove\" != settings[1]) {\n      generateJarallax();\n    } else if (\"automove\" == settings[1]) {\n      generateAutoMoveBackground();\n    } else {\n      generateMultiLayers();\n    }\n  }\n  function generateEditorSettings(targetId) {\n    var editorElements = null,\n      sectionData = {},\n      sectionMultiData = {},\n      settings = [];\n    if (!window.elementor.hasOwnProperty(\"elements\")) {\n      return false;\n    }\n    editorElements = window.elementor.elements;\n    if (!editorElements.models) {\n      return false;\n    }\n    $.each(editorElements.models, function (index, elem) {\n      if (targetId == elem.id) {\n        sectionData = elem.attributes.settings.attributes;\n      } else if (elem.id == target.closest(\".elementor-top-section\").data(\"id\")) {\n        $.each(elem.attributes.elements.models, function (index, col) {\n          $.each(col.attributes.elements.models, function (index, subSec) {\n            sectionData = subSec.attributes.settings.attributes;\n          });\n        });\n      }\n    });\n    if (!sectionData.hasOwnProperty(\"eael_parallax_type\")) {\n      return false;\n    }\n    if (\"\" == sectionData[\"eael_parallax_type\"]) {\n      return false;\n    }\n    if (\"multi\" != sectionData[\"eael_parallax_type\"] && \"automove\" != sectionData[\"eael_parallax_type\"]) {\n      settings.push(sectionData[\"eael_parallax_switcher\"]);\n      settings.push(sectionData[\"eael_parallax_type\"]);\n      settings.push(sectionData[\"eael_parallax_speed\"]);\n      settings.push(\"yes\" == sectionData[\"eael_parallax_android_support\"] ? 0 : 1);\n      settings.push(\"yes\" == sectionData[\"eael_parallax_ios_support\"] ? 0 : 1);\n      settings.push(sectionData[\"eael_parallax_background_size\"]);\n      settings.push(sectionData[\"eael_parallax_background_pos\"]);\n    } else if (\"automove\" == sectionData[\"eael_parallax_type\"]) {\n      settings.push(sectionData[\"eael_parallax_switcher\"]);\n      settings.push(sectionData[\"eael_parallax_type\"]);\n      settings.push(sectionData[\"eael_auto_speed\"]);\n      settings.push(sectionData[\"eael_parallax_auto_type\"]);\n    } else {\n      if (!sectionData.hasOwnProperty(\"eael_parallax_layers_list\")) {\n        return false;\n      }\n      sectionMultiData = sectionData[\"eael_parallax_layers_list\"].models;\n      if (0 == sectionMultiData.length) {\n        return false;\n      }\n      settings.push(sectionData[\"eael_parallax_switcher\"]);\n      settings.push(sectionData[\"eael_parallax_type\"]);\n      settings.push(\"yes\" == sectionData[\"eael_parallax_layer_invert\"] ? 1 : 0);\n      $.each(sectionMultiData, function (index, obj) {\n        settings.push(obj.attributes);\n      });\n    }\n    if (0 !== settings.length) {\n      return settings;\n    }\n    return false;\n  }\n  function responsiveParallax(android, ios) {\n    switch ( true || false) {\n      case android && ios:\n        return /iPad|iPhone|iPod|Android/;\n        break;\n      case android && !ios:\n        return /Android/;\n        break;\n      case !android && ios:\n        return /iPad|iPhone|iPod/;\n        break;\n      case !android && !ios:\n        return null;\n    }\n  }\n  function generateJarallax() {\n    setTimeout(function () {\n      target.jarallax({\n        type: settings[1],\n        speed: settings[2],\n        disableParallax: responsiveParallax(1 == settings[3], 1 == settings[4]),\n        keepImg: true\n      });\n    }, 500);\n  }\n  function generateAutoMoveBackground() {\n    var speed = parseInt(settings[2]);\n    target.css(\"background-position\", \"0px 0px\");\n    if (settings[3] == 11) {\n      var position = parseInt(target.css(\"background-position-x\"));\n      setInterval(function () {\n        position = position + speed;\n        target.css(\"backgroundPosition\", position + \"px 0\");\n      }, 70);\n    } else if (settings[3] == \"right\") {\n      var position = parseInt(target.css(\"background-position-x\"));\n      setInterval(function () {\n        position = position - speed;\n        target.css(\"backgroundPosition\", position + \"px 0\");\n      }, 70);\n    } else if (settings[3] == \"top\") {\n      var position = parseInt(target.css(\"background-position-y\"));\n      setInterval(function () {\n        position = position + speed;\n        target.css(\"backgroundPosition\", \"0 \" + position + \"px\");\n      }, 70);\n    } else if (settings[3] == \"bottom\") {\n      var position = parseInt(target.css(\"background-position-y\"));\n      setInterval(function () {\n        position = position - speed;\n        target.css(\"backgroundPosition\", \"0 \" + position + \"px\");\n      }, 70);\n    }\n  }\n  function generateMultiLayers() {\n    var counter = 0,\n      mouseParallax = \"\",\n      mouseRate = \"\";\n    $.each(settings, function (index, layout) {\n      if (2 < index) {\n        if (null != layout[\"eael_parallax_layer_image\"][\"url\"] && \"\" != layout[\"eael_parallax_layer_image\"][\"url\"]) {\n          if (\"yes\" == layout[\"eael_parallax_layer_mouse\"] && \"\" != layout[\"eael_parallax_layer_rate\"]) {\n            mouseParallax = ' data-parallax=\"true\" ';\n            mouseRate = ' data-rate=\"' + layout[\"eael_parallax_layer_rate\"] + '\" ';\n          } else {\n            mouseParallax = ' data-parallax=\"false\" ';\n          }\n          var backgroundImage = layout[\"eael_parallax_layer_image\"][\"url\"],\n            $html = $('<div id=\"eael-parallax-layer-' + counter + '\"' + mouseParallax + mouseRate + ' class=\"eael-parallax-layer\"></div>').prependTo(target).css({\n              \"z-index\": layout[\"eael_parallax_layer_z_index\"],\n              \"background-image\": \"url(\" + backgroundImage + \")\",\n              \"background-size\": layout[\"eael_parallax_layer_back_size\"],\n              \"background-position-x\": layout[\"eael_parallax_layer_hor_pos\"] + \"%\",\n              \"background-position-y\": layout[\"eael_parallax_layer_ver_pos\"] + \"%\"\n            });\n          counter++;\n        }\n      }\n    });\n    target.mousemove(function (e) {\n      $(this).find('.eael-parallax-layer[data-parallax=\"true\"]').each(function (index, element) {\n        $(this).parallax($(this).data(\"rate\"), e);\n      });\n    });\n  }\n};\njQuery(window).on(\"elementor/frontend/init\", function () {\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/section\", EaelParallaxHandler);\n  elementorFrontend.hooks.addAction(\"frontend/element_ready/container\", EaelParallaxHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/section-parallax.js?");

/***/ })

/******/ });