(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumIconBoxHandler = function ($scope, $) {

            $scope.find(".elementor-invisible").removeClass("elementor-invisible");

            var devices = ['widescreen', 'desktop', 'laptop', 'tablet', 'tablet_extra', 'mobile', 'mobile_extra'].filter(function (ele) { return ele != elementorFrontend.getCurrentDeviceMode(); });

            devices.map(function (device) {
                device = ('desktop' !== device) ? device + '-' : '';
                $scope.removeClass(function (index, selector) {
                    return (selector.match(new RegExp("(^|\\s)premium-" + device + "icon-box\\S+", 'g')) || []).join(' ');
                });
            });

            if ($scope.data("box-tilt")) {
                var reverse = $scope.data("box-tilt-reverse");

                UniversalTilt.init({
                    elements: $scope,
                    settings: {
                        reverse: reverse
                    },
                    callbacks: {
                        onMouseLeave: function (el) {
                            el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0)";
                        },
                        onDeviceMove: function (el) {
                            el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0.3)";
                        }
                    }
                });
            }

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-icon-box.default', PremiumIconBoxHandler);

    });
})(jQuery);