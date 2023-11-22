(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumDividerHandler = function ($scope, $) {
            var $divider = $scope.find(".premium-separator-container"),
                sepSettings = $divider.data("settings"),
                leftBackground = null,
                rightBackground = null;

            if ("custom" === sepSettings) {
                leftBackground = $divider
                    .find(".premium-separator-left-side")
                    .data("background");

                $divider
                    .find(".premium-separator-left-side hr")
                    .css("border-image", "url( " + leftBackground + " ) 20% round");

                rightBackground = $divider
                    .find(".premium-separator-right-side")
                    .data("background");

                $divider
                    .find(".premium-separator-right-side hr")
                    .css("border-image", "url( " + rightBackground + " ) 20% round");
            }

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-divider.default', PremiumDividerHandler);
    });
})(jQuery);