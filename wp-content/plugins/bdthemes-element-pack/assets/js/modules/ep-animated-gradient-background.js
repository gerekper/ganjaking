; (function ($, elementor) {
  $(window).on("elementor/frontend/init", function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
      AnimatedGradientBackground;

    AnimatedGradientBackground = ModuleHandler.extend({
      bindEvents: function () {
        this.run();
      },

      getDefaultSettings: function () {
        return {
          allowHTML: true,
        };
      },
      onElementChange: debounce(function (prop) {
        if (prop.indexOf('element_pack_agbg_') !== -1) {
          this.run();
        }
      }, 400),

      settings: function (key) {
        return this.getElementSettings("element_pack_agbg_" + key);
      },

      run: function () {
        if (this.settings('show') !== 'yes') {
          return;
        }
        const sectionID = this.$element.data("id");
        const widgetContainer = document.querySelector(".elementor-element-" + sectionID);
        const checkClass = $(widgetContainer).find(".bdt-animated-gradient-background");

        if ($(checkClass).length < 1) {
          $(widgetContainer).prepend('<canvas id="canvas-basic-' + sectionID + '" class="bdt-animated-gradient-background"></canvas>');
        }

        const gradientID = $(widgetContainer).find(".bdt-animated-gradient-background").attr("id");

        let color_list = this.settings("color_list");
        let colors = [];
        color_list.forEach((color) => {
          colors.push([color.start_color, color.end_color]);
        });

        var direction = (this.settings("direction") !== undefined) ? this.settings('direction') : 'diagonal';
        var transitionSpeed = (this.settings("transitionSpeed") !== undefined) ? this.settings('transitionSpeed.size') : '5500';
        var granimInstance = new Granim({
          element: "#" + gradientID,
          direction: direction,
          isPausedWhenNotInView: true,
          states: {
            "default-state": {
              gradients: colors,
              transitionSpeed: transitionSpeed,
            },
          },
        });
      },
    });

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/section",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(AnimatedGradientBackground, {
          $element: $scope,
        });
      }
    );

    elementorFrontend.hooks.addAction(
      "frontend/element_ready/container",
      function ($scope) {
        elementorFrontend.elementsHandler.addHandler(AnimatedGradientBackground, {
          $element: $scope,
        });
      }
    );

  });
})(jQuery, window.elementorFrontend);