(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var premiumGradientHandler = function ($scope, $) {

            if (!$scope.hasClass("premium-gradient-yes"))
                return;

            var target = $scope,
                sectionId = target.data("id"),
                settings = {},
                tempTarget = target.find('#premium-animated-gradient-' + sectionId),
                editMode = elementorFrontend.isEditMode() && tempTarget.length > 0,
                targetID = editMode ? tempTarget : target,
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

        elementorFrontend.hooks.addAction("frontend/element_ready/section", premiumGradientHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/container", premiumGradientHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/column", premiumGradientHandler);

    });


})(jQuery);
