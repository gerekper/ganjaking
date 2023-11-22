(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumFbChatHandler = function ($scope, $) {

            var premiumFbChat = $scope.find(".premium-fbchat-container"),
                premiumFbChatSettings = premiumFbChat.data("settings"),
                currentDevice = elementorFrontend.getCurrentDeviceMode();

            if (premiumFbChat.length > 0) {

                if ("mobile" === currentDevice && premiumFbChatSettings["hideMobile"]) {
                    return;
                }

                window.fbAsyncInit = function () {
                    FB.init({
                        appId: premiumFbChatSettings["appId"],
                        autoLogAppEvents: !0,
                        xfbml: !0,
                        version: "v2.12"
                    });
                };
                (function (a, b, c) {
                    var d = a.getElementsByTagName(b)[0];
                    a.getElementById(c) ||
                        ((a = a.createElement(b)),
                            (a.id = c),
                            (a.src =
                                "https://connect.facebook.net/" +
                                premiumFbChatSettings["lang"] +
                                "/sdk/xfbml.customerchat.js"),
                            d.parentNode.insertBefore(a, d));
                })(document, "script", "facebook-jssdk");


                $(".elementor-element-overlay .elementor-editor-element-remove").on(
                    "click",
                    function () {

                        var $this = $(this),
                            parentId = $this.parents("section.elementor-element");

                        if (parentId.find("#premium-fbchat-container").length) {
                            document.location.href = document.location.href;
                        }

                    }
                );
            }
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-facebook-chat.default', PremiumFbChatHandler);

    });
})(jQuery);