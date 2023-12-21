"use strict";

;
(function ($) {
  var $window = $(window),
    debounce = function debounce(func, wait, immediate) {
      var timeout;
      return function () {
        var context = this,
          args = arguments;
        var later = function later() {
          timeout = null;
          if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
      };
    };
  $window.on('elementor/frontend/init', function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      FloatingFxHandler;
    FloatingFxHandler = ModuleHandler.extend({
      bindEvents: function bindEvents() {
        this.run();
      },
      getDefaultSettings: function getDefaultSettings() {
        return {
          direction: 'alternate',
          easing: 'easeInOutSine',
          loop: true,
          targets: this.findElement('.elementor-widget-container').get(0)
        };
      },
      onElementChange: debounce(function (prop) {
        if (prop.indexOf('ha_floating') !== -1) {
          this.anime && this.anime.restart();
          this.run();
        }
      }, 400),
      getFxVal: function getFxVal(key) {
        return this.getElementSettings('ha_floating_fx_' + key);
      },
      run: function run() {
        var config = this.getDefaultSettings();
        if (this.getFxVal('translate_toggle')) {
          if (this.getFxVal('translate_x.size') || this.getFxVal('translate_x.sizes.to')) {
            config.translateX = {
              value: [this.getFxVal('translate_x.sizes.from') || 0, this.getFxVal('translate_x.size') || this.getFxVal('translate_x.sizes.to')],
              duration: this.getFxVal('translate_duration.size'),
              delay: this.getFxVal('translate_delay.size') || 0
            };
          }
          if (this.getFxVal('translate_y.size') || this.getFxVal('translate_y.sizes.to')) {
            config.translateY = {
              value: [this.getFxVal('translate_y.sizes.from') || 0, this.getFxVal('translate_y.size') || this.getFxVal('translate_y.sizes.to')],
              duration: this.getFxVal('translate_duration.size'),
              delay: this.getFxVal('translate_delay.size') || 0
            };
          }
        }
        if (this.getFxVal('rotate_toggle')) {
          if (this.getFxVal('rotate_x.size') || this.getFxVal('rotate_x.sizes.to')) {
            config.rotateX = {
              value: [this.getFxVal('rotate_x.sizes.from') || 0, this.getFxVal('rotate_x.size') || this.getFxVal('rotate_x.sizes.to')],
              duration: this.getFxVal('rotate_duration.size'),
              delay: this.getFxVal('rotate_delay.size') || 0
            };
          }
          if (this.getFxVal('rotate_y.size') || this.getFxVal('rotate_y.sizes.to')) {
            config.rotateY = {
              value: [this.getFxVal('rotate_y.sizes.from') || 0, this.getFxVal('rotate_y.size') || this.getFxVal('rotate_y.sizes.to')],
              duration: this.getFxVal('rotate_duration.size'),
              delay: this.getFxVal('rotate_delay.size') || 0
            };
          }
          if (this.getFxVal('rotate_z.size') || this.getFxVal('rotate_z.sizes.to')) {
            config.rotateZ = {
              value: [this.getFxVal('rotate_z.sizes.from') || 0, this.getFxVal('rotate_z.size') || this.getFxVal('rotate_z.sizes.to')],
              duration: this.getFxVal('rotate_duration.size'),
              delay: this.getFxVal('rotate_delay.size') || 0
            };
          }
        }
        if (this.getFxVal('scale_toggle')) {
          if (this.getFxVal('scale_x.size') || this.getFxVal('scale_x.sizes.to')) {
            config.scaleX = {
              value: [this.getFxVal('scale_x.sizes.from') || 0, this.getFxVal('scale_x.size') || this.getFxVal('scale_x.sizes.to')],
              duration: this.getFxVal('scale_duration.size'),
              delay: this.getFxVal('scale_delay.size') || 0
            };
          }
          if (this.getFxVal('scale_y.size') || this.getFxVal('scale_y.sizes.to')) {
            config.scaleY = {
              value: [this.getFxVal('scale_y.sizes.from') || 0, this.getFxVal('scale_y.size') || this.getFxVal('scale_y.sizes.to')],
              duration: this.getFxVal('scale_duration.size'),
              delay: this.getFxVal('scale_delay.size') || 0
            };
          }
        }
        if (this.getFxVal('translate_toggle') || this.getFxVal('rotate_toggle') || this.getFxVal('scale_toggle')) {
          this.findElement('.elementor-widget-container').css('will-change', 'transform');
          this.anime = window.anime && window.anime(config);
        }
      }
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
      elementorFrontend.elementsHandler.addHandler(FloatingFxHandler, {
        $element: $scope
      });
    });
  });
})(jQuery);