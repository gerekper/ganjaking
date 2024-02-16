(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumAlertBoxHandler = function ($scope, $) {
            var $barElem = $scope.find(".premium-notbar-outer-container"),
                settings = $barElem.data("settings"),
                _this = $($barElem);

            if (_this.length > 0) {

                //If animation is set, we need to keep it hidden until we trigger the animation.
                if (!settings.entranceAnimation) {
                    $barElem.removeClass('elementor-invisible');
                }

                if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                    if (settings.cookies) {
                        if (notificationReadCookie("premiumNotBar-" + settings.id)) {
                            $barElem.css("display", "none");
                        }
                    }
                }

                function notificationSetCookie(cookieName, cookieValue) {
                    var today = new Date(),
                        expire = new Date();

                    expire.setTime(today.getTime() + 3600000 * settings.interval);

                    document.cookie = cookieName + "=" + encodeURI(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
                }

                function notificationReadCookie(cookieName) {
                    var theCookie = " " + document.cookie;

                    var ind = theCookie.indexOf(" " + cookieName + "=");

                    if (ind == -1) ind = theCookie.indexOf(";" + cookieName + "=");

                    if (ind == -1 || cookieName == "") return "";

                    var ind1 = theCookie.indexOf(";", ind + 1);

                    if (ind1 == -1) ind1 = theCookie.length;

                    return unescape(theCookie.substring(ind + cookieName.length + 2, ind1));
                }

                if (settings.location === "top" && settings.position === "premium-notbar-relative") {

                    $($barElem).detach();

                    $($barElem).addClass('premium-notbar-top');

                    $("body").prepend(_this);

                    if (settings.type === 'notification') {

                        if ($("body").find('.premium-notbar-notification-top-' + settings.id).length > 0)
                            return;

                        else
                            $($barElem).addClass('premium-notbar-notification-top-' + settings.id);
                    }
                }

                if (settings.location === "top" && settings.type === 'alert') {

                    $("body").find('.premium-notbar-notification-top-' + settings.id).remove();
                }

                if (settings.location !== "top") {
                    // $("body").find(".premium-notbar-top").remove();
                }

                if ('yes' === settings.customPos) {
                    if (settings.layout === "boxed" || settings.type === 'alert') {

                        var barWidth = $barElem.find(".premium-notbar").parent().width();
                        $barElem.find(".premium-notbar").css("width", barWidth);

                        $(window).on("resize", function () {
                            barWidth = $barElem.find(".premium-notbar").parent().width();
                            $barElem.find(".premium-notbar").css("width", barWidth);
                        });
                    }
                }

                triggerAnimations();

                function triggerAnimations() {

                    if (settings.entranceAnimation) {
                        $barElem.removeClass('elementor-invisible');
                        $barElem.find('.premium-notbar').addClass('animated ' + settings.entranceAnimation);
                    }

                }

                $barElem.find(".premium-notbar-close").on("click", function () {

                    //Handle cookies behavior.
                    if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                        if (settings.cookies) {
                            if (!notificationReadCookie("premiumNotBar-" + settings.id)) {
                                notificationSetCookie("premiumNotBar-" + settings.id, true);
                            }
                        }
                    }

                    if (settings.closeAction === 'hide') {

                        if ('top' === settings.location) {

                            if (settings.position === "premium-notbar-fixed") {
                                $barElem.find('.premium-notbar').addClass('notbar-hidden-top');
                            } else {
                                $barElem.css('overflow', 'hidden');
                                $barElem.animate({
                                    height: "0"
                                }, 300);
                            }

                        } else if ('bottom' === settings.location) {

                            $barElem.find('.premium-notbar').addClass('notbar-hidden-bottom');

                        } else {

                            $barElem.addClass('notbar-hidden');
                        }

                    } else {

                        var $elementToRemove = null;
                        switch (settings.elementToRemove) {
                            case 'widget':
                                $elementToRemove = $scope;
                                break;

                            case 'column':
                                $elementToRemove = $scope.closest('.e-con.e-child');
                                break;

                            default:
                                $elementToRemove = $scope.closest('.e-con.e-parent');
                        }

                        if (elementorFrontend.isEditMode())
                            return;

                        $elementToRemove.css('overflow', 'hidden');
                        $elementToRemove.animate({
                            height: 0,
                            padding: 0,
                            margin: 0,
                            borderWidth: 0
                        }, 300);

                    }
                });

            }
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-notbar.default', PremiumAlertBoxHandler);
    });
})(jQuery);