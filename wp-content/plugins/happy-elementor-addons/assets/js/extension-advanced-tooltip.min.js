"use strict";

;
(function ($) {
  'use strict';

  var $window = $(window);
  $window.on('elementor/frontend/init', function () {
    var gloablTooltipOptions = [];
    var AdvancedTooltip = elementorModules.frontend.handlers.Base.extend({
      onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        if (this.$element.hasClass('ha-advanced-tooltip-enable')) {
          this.$element.append("<span class='ha-advanced-tooltip-content'></span>");
          this.run();
        }
      },
      getReadySettings: function getReadySettings() {
        var settings = {
          trigger: this.getElementSettings('ha_advanced_tooltip_trigger'),
          content: this.getElementSettings('ha_advanced_tooltip_content'),
          animation: this.getElementSettings('ha_advanced_tooltip_animation'),
          duration: this.getElementSettings('ha_advanced_tooltip_duration') || 500,
          showArrow: this.getElementSettings('ha_advanced_tooltip_arrow') || false,
          position: this.getElementSettings('ha_advanced_tooltip_position')
        };
        return $.extend({}, settings);
      },
      onElementChange: function onElementChange(e) {
        if (this.$element.hasClass('ha-advanced-tooltip-enable')) {
          var style_controls = ['ha_advanced_tooltip_enable', 'ha_advanced_tooltip_content', 'ha_advanced_tooltip_position', 'ha_advanced_tooltip_arrow', 'ha_advanced_tooltip_duration', 'ha_advanced_tooltip_size', 'ha_advanced_tooltip_animation'];
          if (style_controls.includes(e)) {
            if (e == 'ha_advanced_tooltip_enable' && this.$element.find('.ha-advanced-tooltip-content').length <= 0) {
              this.$element.append("<span class='ha-advanced-tooltip-content'></span>");
              this.run();
            }
            gloablTooltipOptions = this.getReadySettings();
          }
        } else {
          this.$element.find('.ha-advanced-tooltip-content').remove();
        }
      },
      run: function run() {
        var $scope = this.$element;
        if (this.$element.hasClass("ha-advanced-tooltip-enable")) {
          gloablTooltipOptions = this.getReadySettings();
          var content = $scope.find('.ha-advanced-tooltip-content');
          content.html($.parseHTML(gloablTooltipOptions.content));
          content.css('animation-duration', gloablTooltipOptions.duration + 'ms');
          content.addClass(gloablTooltipOptions.animation);
          if (!gloablTooltipOptions.showArrow) {
            content.addClass('no-arrow');
          }
          if (gloablTooltipOptions.trigger == 'click') {
            this.$element.on('click', function () {
              if (content.hasClass('show')) {
                content.removeClass('show');
              } else {
                content.addClass('show');
              }
            });
          } else if (gloablTooltipOptions.trigger == 'hover') {
            this.$element.on('mouseenter', function () {
              content.addClass('show');
            });
            this.$element.on('mouseleave', function () {
              content.removeClass('show');
            });
          }
        }
      }
    });
    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
      elementorFrontend.elementsHandler.addHandler(AdvancedTooltip, {
        $element: $scope
      });
    });
  });
})(jQuery);