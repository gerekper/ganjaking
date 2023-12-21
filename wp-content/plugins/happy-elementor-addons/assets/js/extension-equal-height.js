"use strict";

;
(function ($) {
  'use strict';

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
      EqualHeightHandler;
    EqualHeightHandler = ModuleHandler.extend({
      CACHED_ELEMENTS: [],
      isEqhEnabled: function isEqhEnabled() {
        return this.getElementSettings('_ha_eqh_enable') === 'yes' && $.fn.matchHeight;
      },
      isDisabledOnDevice: function isDisabledOnDevice() {
        var windowWidth = $window.outerWidth(),
          mobileWidth = elementorFrontendConfig.breakpoints.md,
          tabletWidth = elementorFrontendConfig.breakpoints.lg;
        if ('yes' == this.getElementSettings('_ha_eqh_disable_on_mobile') && windowWidth < mobileWidth) {
          return true;
        }
        if ('yes' == this.getElementSettings('_ha_eqh_disable_on_tablet') && windowWidth >= mobileWidth && windowWidth < tabletWidth) {
          return true;
        }
        return false;
      },
      getEqhTo: function getEqhTo() {
        return this.getElementSettings('_ha_eqh_to') || 'widget';
      },
      getEqhWidgets: function getEqhWidgets() {
        return this.getElementSettings('_ha_eqh_widget') || [];
      },
      getTargetElements: function getTargetElements() {
        var _this = this;
        return this.getEqhWidgets().map(function (widget) {
          if (false && _this.$element.data("element_type") === "container") {
            var $key = 0;
            var $widgets = {};
            var $container = _this.$element;
            var cls = '.elementor-widget-' + widget + ' .elementor-widget-container';
            if ($container.find(' > .e-con-inner > div[data-element_type="container"] > ' + cls).length) {
              $widgets = $container.find(' > .e-con-inner > div[data-element_type="container"] > ' + cls);
            }
            if ($container.find(' > div[data-element_type="container"] > ' + cls).length) {
              if ($widgets.length) {
                var _$key = $widgets.length;
                $container.find(' > div[data-element_type="container"] > ' + cls).each(function () {
                  var id = $(this).parent().data('id');
                  if (!$widgets.hasOwnProperty(_$key)) {
                    $widgets[_$key] = $(this)[0];
                  }
                  _$key += 1;
                });
                $widgets.length = _$key;
              } else {
                $widgets = $container.find(' > div[data-element_type="container"] > ' + cls);
              }
            }
            if ($container.find(' > .e-con-inner > div[data-element_type="container"] > .e-con-inner > ' + cls).length) {
              if ($widgets.length) {
                var _$key2 = $widgets.length;
                $container.find(' > .e-con-inner > div[data-element_type="container"] > .e-con-inner > ' + cls).each(function () {
                  var id = $(this).parent().data('id');
                  if (!$widgets.hasOwnProperty(_$key2)) {
                    $widgets[_$key2] = $(this)[0];
                  }
                  _$key2 += 1;
                });
                $widgets.length = _$key2;
              } else {
                $widgets = $container.find(' > .e-con-inner > div[data-element_type="container"] > .e-con-inner > ' + cls);
              }
            }
            if ($container.find(' > div[data-element_type="container"] > .e-con-inner > ' + cls).length) {
              if ($widgets.length) {
                var _$key3 = $widgets.length;
                $container.find(' > div[data-element_type="container"] > .e-con-inner > ' + cls).each(function () {
                  var id = $(this).parent().data('id');
                  if (!$widgets.hasOwnProperty(_$key3)) {
                    $widgets[_$key3] = $(this)[0];
                  }
                  _$key3 += 1;
                });
                $widgets.length = _$key3;
              } else {
                $widgets = $container.find(' > div[data-element_type="container"] > .e-con-inner > ' + cls);
              }
            }
            if ($container.find(' > .e-con-inner > ' + cls).length) {
              if ($widgets.length) {
                var _$key4 = $widgets.length;
                $container.find(' > .e-con-inner > ' + cls).each(function () {
                  var id = $(this).parent().data('id');
                  if (!$widgets.hasOwnProperty(_$key4)) {
                    $widgets[_$key4] = $(this)[0];
                  }
                  _$key4 += 1;
                });
                $widgets.length = _$key4;
              } else {
                $widgets = $container.find(' > .e-con-inner > ' + cls);
              }
            }
            if ($container.find(' > ' + cls).length) {
              if ($widgets.length) {
                var _$key5 = $widgets.length;
                $container.find(' > ' + cls).each(function () {
                  var id = $(this).parent().data('id');
                  if (!$widgets.hasOwnProperty(_$key5)) {
                    $widgets[_$key5] = $(this)[0];
                  }
                  _$key5 += 1;
                });
                $widgets.length = _$key5;
              } else {
                $widgets = $container.find(' > ' + cls);
              }
            }
            return $widgets;
          }
          return _this.$element.find('.elementor-widget-' + widget + ' .elementor-widget-container');
        });
      },
      bindEvents: function bindEvents() {
        if (this.isEqhEnabled()) {
          this.run();
          $window.on('resize orientationchange', debounce(this.run.bind(this), 500));
        }
      },
      onElementChange: debounce(function (prop, ele) {
        if (prop.indexOf('_ha_eqh') === -1) {
          return;
        }
        this.unbindMatchHeight(true);
        this.run();
      }, 100),
      unbindMatchHeight: function unbindMatchHeight(isCachedOnly) {
        if (isCachedOnly) {
          this.CACHED_ELEMENTS.forEach(function ($el) {
            $el.matchHeight({
              remove: true
            });
          });
          this.CACHED_ELEMENTS = [];
        } else {
          this.getTargetElements().forEach(function ($el) {
            $el && $el.matchHeight({
              remove: true
            });
          });
        }
      },
      run: function run() {
        var _this = this;
        if (this.isDisabledOnDevice()) {
          this.unbindMatchHeight();
        } else {
          this.getTargetElements().forEach(function ($el) {
            if ($el.length) {
              $el.matchHeight({
                byRow: false
              });
              _this.CACHED_ELEMENTS.push($el);
            }
          });
        }
      }
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
      elementorFrontend.elementsHandler.addHandler(EqualHeightHandler, {
        $element: $scope
      });
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
      elementorFrontend.elementsHandler.addHandler(EqualHeightHandler, {
        $element: $scope
      });
    });
  });
})(jQuery);