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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/view/section-particles.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/view/section-particles.js":
/*!******************************************!*\
  !*** ./src/js/view/section-particles.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _typeof(o) { \"@babel/helpers - typeof\"; return _typeof = \"function\" == typeof Symbol && \"symbol\" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && \"function\" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? \"symbol\" : typeof o; }, _typeof(o); }\nvar EaelParticlesHandler = function EaelParticlesHandler($scope, $) {\n  var sectionId = $scope.data('id'),\n    particle_switch = $scope.data('particle_enable'),\n    particle_switch_for_mobile = $scope.data('particle-mobile-disabled'),\n    mobile_device_width = 767,\n    global_data = [];\n\n  // Checking mobile device disable\n  if (particle_switch_for_mobile !== undefined && particle_switch_for_mobile && $(window).width() <= mobile_device_width) return;\n\n  // Checking if the section has enabled particles.\n  if (_typeof(particle_switch) == undefined || particle_switch != undefined && particle_switch == false) return;\n  var preset_theme = $scope.data('preset_theme'),\n    custom_style = $scope.data('custom_style'),\n    source = $scope.data('eael_ptheme_source'),\n    particle_opacity = $scope.data('particle_opacity'),\n    particle_speed = $scope.data('particle_speed'),\n    settings;\n\n  // Checking custo style json is not empty.\n  if (source == 'custom' && source == '') return;\n  $scope.addClass('eael-particles-section');\n  if (window.isEditMode) {\n    var editorElements = null,\n      particleArgs = {},\n      settings = {};\n    if (!window.elementor.hasOwnProperty('elements')) {\n      return false;\n    }\n    editorElements = window.elementor.elements;\n    if (!editorElements.models) {\n      return false;\n    }\n    $.each(editorElements.models, function (i, el) {\n      if (sectionId == el.id) {\n        particleArgs = el.attributes.settings.attributes;\n      } else if (el.id == $scope.closest('.elementor-top-section').data('id')) {\n        $.each(el.attributes.elements.models, function (i, col) {\n          $.each(col.attributes.elements.models, function (i, subSec) {\n            particleArgs = subSec.attributes.settings.attributes;\n          });\n        });\n      }\n    });\n    settings[\"switch\"] = particleArgs['eael_particle_switch'];\n    settings.themeSource = particleArgs['eael_particle_theme_from'];\n    global_data.opacity = particleArgs['eael_particle_opacity']['size'];\n    global_data.speed = particleArgs['eael_particle_speed']['size'];\n    if (settings.themeSource == 'presets') {\n      settings.selected_theme = localize.ParticleThemesData[particleArgs['eael_particle_preset_themes']];\n    }\n    if (settings.themeSource == 'custom' && '' !== particleArgs['eael_particles_custom_style']) {\n      settings.selected_theme = particleArgs['eael_particles_custom_style'];\n    }\n    if (0 !== settings.length) {\n      settings = settings;\n    }\n  } else {\n    var div = $('.eael-section-particles-' + sectionId);\n    div.each(function () {\n      source = $(this).data('eael_ptheme_source');\n      if (source == 'presets') {\n        themes = JSON.parse(localize.ParticleThemesData[preset_theme]);\n      } else {\n        themes = custom_style != '' ? custom_style : undefined;\n      }\n      var id = $(this).attr('id');\n      if (id == undefined) {\n        $(this).attr('id', 'eael-section-particles-' + sectionId);\n        id = $(this).attr('id');\n      }\n      themes.particles.opacity.value = particle_opacity;\n      themes.particles.move.speed = particle_speed;\n      particlesJS(id, themes);\n    });\n  }\n  if (!window.isEditMode || !settings) {\n    return false;\n  }\n  if (settings[\"switch\"] == 'yes') {\n    if (settings.themeSource === 'presets' || settings.themeSource === 'custom' && '' !== settings.selected_theme) {\n      if (typeof particlesJS !== 'undefined' && $.isFunction(particlesJS)) {\n        $scope.attr('id', 'eael-section-particles-' + sectionId);\n        var selected_theme = JSON.parse(settings.selected_theme);\n        selected_theme.particles.opacity.value = global_data.opacity;\n        selected_theme.particles.move.speed = global_data.speed;\n        particlesJS('eael-section-particles-' + sectionId, selected_theme);\n        $scope.children('canvas.particles-js-canvas-el').css({\n          position: 'absolute',\n          top: 0\n        });\n      }\n    }\n  } else {\n    $scope.removeClass('eael-particles-section');\n  }\n};\njQuery(window).on('elementor/frontend/init', function () {\n  elementorFrontend.hooks.addAction('frontend/element_ready/section', EaelParticlesHandler);\n  elementorFrontend.hooks.addAction('frontend/element_ready/container', EaelParticlesHandler);\n});\n\n//# sourceURL=webpack:///./src/js/view/section-particles.js?");

/***/ })

/******/ });