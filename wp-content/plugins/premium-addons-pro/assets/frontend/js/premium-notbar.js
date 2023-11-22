(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumAlertBoxHandler = function ($scope, $) {
            var $barElem = $scope.find(".premium-notbar-outer-container"),
                settings = $barElem.data("settings"),
                _this = $($barElem),
                link = settings.link,
                currentDevice = elementorFrontend.getCurrentDeviceMode();

            if (_this.length > 0) {
                if (settings.responsive) {
                    if (settings.hideMobs) {
                        if ('mobile' === currentDevice) {
                            $barElem.css("display", "none");
                        }
                    }

                    if (settings.hideTabs) {
                        if ('tablet' === currentDevice) {
                            $barElem.css("display", "none");
                        }
                    }
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

                    $("body").prepend(_this);
                }

                if (settings.layout === "boxed") {
                    var not_width = $barElem
                        .find(".premium-notbar")
                        .parent()
                        .width();

                    $barElem.find(".premium-notbar").css("width", not_width);

                    $(window).on("resize", function () {
                        var not_width = $barElem
                            .find(".premium-notbar")
                            .parent()
                            .width();

                        $barElem.find(".premium-notbar").css("width", not_width);
                    });
                }

                if (!link) {
                    $barElem.find(".premium-notbar-close").on("click", function () {

                        $barElem.find(".premium-notbar-background-overlay").remove();

                        if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                            if (settings.cookies) {
                                if (!notificationReadCookie("premiumNotBar-" + settings.id)) {
                                    notificationSetCookie("premiumNotBar-" + settings.id, true);
                                }
                            }
                        }

                        if ($(this).hasClass("premium-notbar-top") || $(this).hasClass("premium-notbar-edit-top")) {
                            if (settings.position === "premium-notbar-fixed") {
                                $(this)
                                    .parentsUntil(".premium-notbar-outer-container")
                                    .css("top", "-1000px");
                            } else {
                                $($barElem).animate({
                                    height: "0"
                                }, 300);
                            }
                        } else if ($(this).hasClass("premium-notbar-bottom")) {
                            $(this)
                                .parentsUntil(".premium-notbar-outer-container")
                                .css("bottom", "-1000px");
                        } else {
                            $(this)
                                .parentsUntil(".premium-notbar-outer-container")
                                .css({
                                    visibility: "hidden",
                                    opacity: "0"
                                });
                        }
                    });
                }
            }
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-notbar.default', PremiumAlertBoxHandler);
    });
})(jQuery);