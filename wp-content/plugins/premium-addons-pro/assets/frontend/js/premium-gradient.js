(function ($) {

    if ($('.premium-gradient-yes').length) {

        var isEdit = "undefined" !== typeof elementorFrontend ? elementorFrontend.isEditMode() : false;

        if (isEdit) {
            premiumGradientHandler(window.current_scope);
        } else if (window.scopes_array) {
            Object.values(window.scopes_array).forEach(
                function ($scope) {
                    premiumGradientHandler($scope);
                }
            );
        }

    }

    function premiumGradientHandler($scope) {
        var target = $scope,
            sectionId = target.data("id"),
            settings = {},
            editMode = elementorFrontend.isEditMode(),
            targetID = editMode ? target.find('#premium-animated-gradient-' + sectionId) : target,
            waveEffect = target.hasClass('premium-gradient-wave-yes') ? true : false;

        generateSettings(targetID);

        if (!settings) {
            return false;
        }

        generateGradient();

        function generateSettings(target) {

            var generalSettings = target.data('gradient');

            if (!generalSettings) {
                return false;
            }

            settings.colorData = [];
            settings.angle = generalSettings.angle;

            $.each(generalSettings.colors, function (index, color) {
                settings.colorData.push(color);
            });

            if (0 !== Object.keys(settings).length) {
                return settings;
            }

        }

        function generateGradient() {
            var gradientStyle = "linear-gradient(" + settings.angle + "deg,";

            $.each(
                settings.colorData,
                function (index, layout) {

                    if ('undefined' !== typeof layout["__globals__"] && '' !== layout["__globals__"]["premium_gradient_colors"]) {

                        var colorPart = layout["__globals__"]["premium_gradient_colors"].split("="),
                            color = colorPart.pop();

                        gradientStyle += "var(--e-global-color-" + color + "),";
                    } else if (null !== layout["premium_gradient_colors"]) {
                        gradientStyle += layout["premium_gradient_colors"] + ",";
                    }

                }
            );

            gradientStyle += ")";

            gradientStyle = gradientStyle.replace(",)", ")");

            if (waveEffect) {
                target.find('.premium-wave-gradient-' + sectionId).remove();
                target.append('<div class="premium-wave-gradient premium-wave-gradient-' + sectionId + '"></div>');
                target = target.find('.premium-wave-gradient-' + sectionId);
            }

            target.css("background", gradientStyle);

        }
    };

})(jQuery);
