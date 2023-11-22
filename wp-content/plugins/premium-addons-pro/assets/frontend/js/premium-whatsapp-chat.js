(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var $whatsappElemHandler = function ($scope, $) {
            var $whatsappElem = $scope.find(".premium-whatsapp-container"),
                settings = $whatsappElem.data("settings"),
                currentDevice = elementorFrontend.getCurrentDeviceMode();

            if (settings.hideMobile) {
                if ("mobile" === currentDevice) {
                    $($whatsappElem).css("display", "none");
                }
            } else if (settings.hideTab) {
                if ("tablet" === currentDevice) {
                    $($whatsappElem).css("display", "none");
                }
            }

            if (settings.tooltips) {
                $whatsappElem.find(".premium-whatsapp-link").tooltipster({
                    functionInit: function (instance, helper) {
                        var content = $(helper.origin)
                            .find("#tooltip_content")
                            .detach();
                        instance.content(content);
                    },
                    functionReady: function () {
                        $(".tooltipster-box").addClass(
                            "tooltipster-box-" + settings.id
                        );
                    },
                    animation: settings.anim,
                    contentCloning: true,
                    trigger: "hover",
                    arrow: true,
                    contentAsHTML: true,
                    autoClose: false,
                    minIntersection: 16,
                    interactive: true,
                    delay: 0,
                    side: ["right", "left", "top", "bottom"]
                });
            }
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-whatsapp-chat.default', $whatsappElemHandler);
    });
})(jQuery);