(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumScrollHandler = function ($scope, $) {
            var premiumScrollElem = $scope.find(".premium-multiscroll-wrap"),
                premiumScrollSettings = premiumScrollElem.data("settings"),
                id = premiumScrollSettings["id"];

            function loadMultiScroll() {
                $("#premium-scroll-nav-menu-" + id).removeClass(
                    "premium-scroll-responsive"
                );

                $("#premium-multiscroll-" + id).multiscroll({
                    verticalCentered: true,
                    menu: "#premium-scroll-nav-menu-" + id,
                    sectionsColor: [],
                    keyboardScrolling: premiumScrollSettings["keyboard"],
                    navigation: premiumScrollSettings["dots"],
                    navigationPosition: premiumScrollSettings["dotsPos"],
                    navigationVPosition: premiumScrollSettings["dotsVPos"],
                    navigationTooltips: premiumScrollSettings["dotsText"],
                    navigationColor: "#000",
                    loopBottom: premiumScrollSettings["btmLoop"],
                    loopTop: premiumScrollSettings["topLoop"],
                    css3: true,
                    paddingTop: 0,
                    paddingBottom: 0,
                    normalScrollElements: null,
                    touchSensitivity: 5,
                    leftSelector: ".premium-multiscroll-left-" + id,
                    rightSelector: ".premium-multiscroll-right-" + id,
                    sectionSelector: ".premium-multiscroll-temp-" + id,
                    anchors: premiumScrollSettings["anchors"],
                    fit: premiumScrollSettings["fit"],
                    cellHeight: premiumScrollSettings["cellHeight"],
                    id: id,
                    leftWidth: premiumScrollSettings["leftWidth"],
                    rightWidth: premiumScrollSettings["rightWidth"]
                });
            }
            var leftTemps = $(premiumScrollElem).find(".premium-multiscroll-left-temp"),
                rightTemps = $(premiumScrollElem).find(".premium-multiscroll-right-temp"),
                hideTabs = premiumScrollSettings["hideTabs"],
                hideMobs = premiumScrollSettings["hideMobs"],
                deviceType = $("body").data("elementor-device-mode"),
                navArray = leftTemps.data("navigation"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                count = leftTemps.length;

            function reOrderTemplates() {
                $(premiumScrollElem)
                    .parents(".elementor-top-section")
                    .removeClass("elementor-section-height-full");
                $.each(rightTemps, function (index) {
                    if (premiumScrollSettings["rtl"]) {
                        $(leftTemps[index]).insertAfter(rightTemps[index]);
                    } else {
                        $(rightTemps[index]).insertAfter(leftTemps[index]);
                    }
                });
                $(premiumScrollElem)
                    .find(".premium-multiscroll-inner")
                    .removeClass("premium-scroll-fit")
                    .css("min-height", premiumScrollSettings["cellHeight"] + "px");
            }

            switch (true) {
                case hideTabs && hideMobs:
                    if (!deviceType.includes("tablet") && !deviceType.includes("mobile")) {
                        loadMultiScroll();
                    } else {
                        reOrderTemplates();
                    }
                    break;
                case hideTabs && !hideMobs:
                    if (!deviceType.includes("tablet")) {
                        loadMultiScroll();
                    } else {
                        reOrderTemplates();
                    }
                    break;
                case !hideTabs && hideMobs:
                    if (!deviceType.includes("mobile")) {
                        loadMultiScroll();
                    } else {
                        reOrderTemplates();
                    }
                    break;
                case !hideTabs && !hideMobs:
                    loadMultiScroll();
                    break;
            }

            function hideTemplate(template) {

                if (0 !== count) {
                    count--;
                    $(template).addClass('premium-multiscroll-hide');
                }
            }

            leftTemps.each(function (index, template) {

                var hideOn = $(template).data('hide');

                if (-1 < hideOn.indexOf(currentDevice)) {

                    hideTemplate(template);
                }
            });

            rightTemps.each(function (index, template) {

                var hideOn = $(template).data('hide');

                if (-1 < hideOn.indexOf(currentDevice)) {

                    hideTemplate(template);
                }
            });

            $(document).ready(function () {

                navArray.map(function (item, index) {
                    if (item) {

                        $(item).on("click", function () {

                            $("#premium-multiscroll-" + id).multiscroll.moveTo(index);

                        })
                    }

                });

            })

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-multi-scroll.default', PremiumScrollHandler);
    });
})(jQuery);