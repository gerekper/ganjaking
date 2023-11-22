(function ($, elementor) {
  "use strict";

  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      SvgBlob;

    SvgBlob = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {};
      },

      settings: function (key) {
        return this.getElementSettings("svg_blob_" + key);
      },

      run: function () {
        var options = this.getDefaultSettings();
        var element = this.findElement(".elementor-widget-container").get(0);
        if (jQuery(this.$element).hasClass("elementor-section")) {
          element = this.$element.get(0);
        }
        var $container = this.$element.find(".bdt-svg-blob");
        if (!$container.length) {
          return;
        }
        const path = $container.data("settings");
        const firstSVG = $container.find("path")[0];
        options = {
          targets: firstSVG,
          d: [{ value: path || [] }],
          easing: 'easeOutQuad',
          direction: 'alternate',
          loop: this.settings('loop') === 'yes',
          duration:
            this.settings('duration.size') !== ''
              ? this.settings('duration.size')
              : 2000,
          delay:
            this.settings('delay.size') !== ''
              ? this.settings('delay.size')
              : 10,
          endDelay:
            this.settings('end_delay.size') !== ''
              ? this.settings('end_delay.size')
              : 10,
        };
        anime(options);
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/bdt-svg-blob.default",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(SvgBlob, {
          $element: $scope,
        });
      }
    );
  });
})(jQuery, window.elementorFrontend);
