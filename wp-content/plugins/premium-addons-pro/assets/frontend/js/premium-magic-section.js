(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumMagicSectionHandler = function ($scope, $) {

            if ($(".premium-magic-section-body-inner").length < 1)
                $("body").wrapInner('<div class="premium-magic-section-body-inner" />');

            var $bodyInnerWrap = $("body .premium-magic-section-body-inner"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                $magicElem = $scope.find(".premium-magic-section-wrap"),
                premiumMagicSectionWrap = $scope.find(".premium-magic-section-container"),
                settings = $magicElem.data("settings"),
                offset,
                offsetAw,
                gutter,
                inIcon = settings["inIcon"],
                outIcon = settings["outIcon"];

            function getWraptoOrg() {
                $bodyInnerWrap.css({
                    top: 0,
                    left: 0,
                    right: 0
                });
            }

            getWraptoOrg();

            gutter = getGutter($magicElem);

            $magicElem.ready(function () {

                var $magicContent = $magicElem.find(".premium-magic-section-content-wrap");

                if ($magicContent.outerWidth() > $magicElem.outerWidth())
                    $magicElem
                        .find(".premium-magic-section-content-wrap-out")
                        .css("overflow-x", "scroll");

                if ($magicContent.outerHeight() > $magicElem.outerHeight())
                    $magicElem
                        .find(".premium-magic-section-content-wrap-out")
                        .css("overflow-y", "scroll");

                switch (settings.position) {
                    case "top":
                        offset = -1 * ($magicElem.outerHeight() - gutter);
                        $magicElem.css("top", offset);
                        break;
                    case "right":
                        offset = -1 * ($magicElem.outerWidth() - gutter);
                        $magicElem.css("right", offset);
                        break;
                    case "left":
                        offset = -1 * ($magicElem.outerWidth() - gutter);
                        $magicElem.css("left", offset);
                        break;
                }
            });

            function getGutter(elem) {

                var settings = $(elem).data("settings"),
                    gutter =
                        settings.position === "top" || settings.position === "bottom" ?
                            (settings.gutter / 100) * $(elem).outerHeight() :
                            (settings.gutter / 100) * $(elem).outerWidth();
                return gutter;

            }

            if (settings.responsive) {
                if (settings.hideMobs) {
                    if ('mobile' === currentDevice) {
                        premiumMagicSectionWrap.css("display", "none");

                        $(window).on("resize", function () {
                            premiumMagicSectionWrap.css("display", "none");
                        });
                    }
                }

                if (settings.hideTabs) {
                    if ('tablet' === currentDevice) {
                        premiumMagicSectionWrap.css("display", "none");

                        $(window).on("resize", function () {
                            premiumMagicSectionWrap.css("display", "none");
                        });
                    }
                }
            }

            $magicElem
                .find(".premium-magic-section-icon-wrap .premium-magic-section-btn")
                .on("click", function () {
                    var nearestMagicSection = $(this).closest(
                        ".premium-magic-section-wrap"
                    ),
                        magicSections = $("body")
                            .find("div.premium-magic-section-wrap")
                            .not(nearestMagicSection);
                    $.each(magicSections, function (index, elem) {
                        if ($(elem).hasClass("in")) {
                            var sectionPos = $(elem).data("settings")["position"],
                                style = $(elem).data("settings")["style"],
                                inIconAw = $(elem).data("settings")["inIcon"],
                                outIconAw = $(elem).data("settings")["outIcon"],
                                gutterAw = getGutter(elem);
                            if (style === "push") {
                                getWraptoOrg();
                            }
                            $(elem)
                                .find(".premium-magic-section-btn")
                                .removeClass(outIconAw)
                                .addClass(inIconAw);
                            $(elem).toggleClass("in out");
                            switch (sectionPos) {
                                case "top":
                                    offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                    $(elem).animate({
                                        top: offsetAw
                                    }, "fast", "linear");
                                    break;
                                case "bottom":
                                    offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                    $(elem).animate({
                                        bottom: offsetAw
                                    }, "fast", "linear");
                                    break;
                                case "left":
                                    offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                    $(elem).animate({
                                        left: offsetAw
                                    }, "fast", "linear");
                                    break;
                                case "right":
                                    offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                    $(elem).animate({
                                        right: offsetAw
                                    }, "fast", "linear");
                                    break;
                            }
                        }
                    });
                    if (nearestMagicSection.hasClass("out")) {
                        $(this)
                            .removeClass(inIcon)
                            .addClass(outIcon);
                    } else {
                        $(this)
                            .removeClass(outIcon)
                            .addClass(inIcon);
                    }
                    if (nearestMagicSection.hasClass("out")) {
                        nearestMagicSection
                            .parent()
                            .siblings(".premium-magic-section-overlay")
                            .addClass("active");
                    } else {
                        nearestMagicSection
                            .parent()
                            .siblings(".premium-magic-section-overlay")
                            .removeClass("active");
                    }
                    nearestMagicSection.toggleClass("in out");
                    switch (settings.position) {
                        case "top":
                            offset = -1 * ($magicElem.outerHeight() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    top: offset
                                }, "fast", "linear");
                                if (settings.style == "push") {
                                    $bodyInnerWrap.animate({
                                        top: 0
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            } else {
                                nearestMagicSection.animate({
                                    top: 0
                                }, "fast", "linear");
                                if (settings.style == "push") {
                                    $bodyInnerWrap.animate({
                                        top: -1 * offset
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            }
                            break;
                        case "bottom":
                            offset = -1 * ($magicElem.outerHeight() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    bottom: offset
                                }, "fast", "linear");
                            } else {
                                nearestMagicSection.animate({
                                    bottom: 0
                                }, "fast", "linear");
                            }
                            break;
                        case "right":
                            offset = -1 * ($magicElem.outerWidth() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    right: offset
                                }, "fast", "linear");
                                if (settings.style == "push") {
                                    $bodyInnerWrap.css("left", "auto").animate({
                                        right: 0
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            } else {
                                nearestMagicSection.animate({
                                    right: 0
                                }, "fast", "linear");
                                if (settings.style == "push") {
                                    $bodyInnerWrap.css("left", "auto").animate({
                                        right: -1 * offset
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            }
                            break;
                        case "left":
                            offset = -1 * ($magicElem.outerWidth() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    left: offset
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.css("right", "auto").animate({
                                        left: 0
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            } else {
                                nearestMagicSection.animate({
                                    left: 0
                                }, "fast", "linear");
                                if (settings.style == "push") {
                                    $bodyInnerWrap.css("right", "auto").animate({
                                        left: -1 * offset
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            }
                            break;
                    }
                });

            if (settings.clickOutside) {

                premiumMagicSectionWrap
                    .siblings(".premium-magic-section-overlay")
                    .on("click", function () {

                        $magicElem
                            .siblings(".premium-magic-section-button-trig")
                            .children(".premium-magic-section-btn")
                            .trigger("click");
                        $magicElem
                            .find(".premium-magic-section-icon-wrap")
                            .children(".premium-magic-section-btn")
                            .trigger("click");
                    });

                $("body").on("click", function (event) {
                    var trigButton =
                        "div.premium-magic-section-button-trig .premium-magic-section-btn",
                        trigIcon =
                            "div.premium-magic-section-icon-wrap .premium-magic-section-btn",
                        buttonContent = ".premium-magic-section-btn *",
                        magicSec = "div.premium-magic-section-content-wrap-out",
                        magicSecContent = "div.premium-magic-section-content-wrap-out *";
                    if (
                        !$(event.target).is($(buttonContent)) &&
                        !$(event.target).is($(trigButton)) &&
                        !$(event.target).is($(trigIcon)) &&
                        !$(event.target).is($(magicSec)) &&
                        !$(event.target).is($(magicSecContent))
                    ) {
                        if ($magicElem.hasClass("in")) {
                            $magicElem
                                .siblings(".premium-magic-section-button-trig")
                                .children(".premium-magic-section-btn")
                                .trigger("click");
                            $magicElem
                                .find(".premium-magic-section-icon-wrap")
                                .children(".premium-magic-section-btn")
                                .trigger("click");
                        }
                    }
                });
            }

            $magicElem
                .find(".premium-magic-section-close-wrap")
                .on("click", function () {
                    if ($magicElem.hasClass("in")) {
                        $(this)
                            .parent()
                            .siblings(".premium-magic-section-button-trig")
                            .children(".premium-magic-section-btn")
                            .trigger("click");
                        $(this)
                            .siblings(".premium-magic-section-icon-wrap")
                            .children(".premium-magic-section-btn")
                            .trigger("click");
                    }
                });

            $magicElem
                .siblings(".premium-magic-section-button-trig")
                .children(".premium-magic-section-btn")
                .on("click", function () {
                    var nearestMagicSection = $(this)
                        .closest(".premium-magic-section-button-trig")
                        .siblings(".premium-magic-section-wrap"),
                        magicSections = $("body")
                            .find("div.premium-magic-section-wrap")
                            .not(nearestMagicSection);
                    nearestMagicSection.toggleClass("in out");
                    $.each(magicSections, function (index, elem) {
                        if ($(elem).hasClass("in")) {
                            var sectionPos = $(elem).data("settings")["position"],
                                style = $(elem).data("settings")["style"],
                                inIconAw = $(elem).data("settings")["inIcon"],
                                outIconAw = $(elem).data("settings")["outIcon"],
                                gutterAw = getGutter(elem);

                            if (style === "push") {
                                getWraptoOrg();
                            }
                            $(elem)
                                .find(".premium-magic-section-btn")
                                .removeClass(outIconAw)
                                .addClass(inIconAw);
                            $(elem).toggleClass("in out");
                            switch (sectionPos) {
                                case "top":
                                    offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                    $(elem).animate({
                                        top: offsetAw
                                    }, "fast", "linear");
                                    break;
                                case "bottom":
                                    offsetAw = -1 * ($(elem).outerHeight() - gutterAw);
                                    $(elem).animate({
                                        bottom: offsetAw
                                    }, "fast", "linear");
                                    break;
                                case "left":
                                    offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                    $(elem).animate({
                                        left: offsetAw
                                    }, "fast", "linear");
                                    break;
                                case "right":
                                    offsetAw = -1 * ($(elem).outerWidth() - gutterAw);
                                    $(elem).animate({
                                        right: offsetAw
                                    }, "fast", "linear");
                                    break;
                            }
                        }
                    });
                    if (nearestMagicSection.hasClass("out")) {
                        nearestMagicSection
                            .parent()
                            .siblings(".premium-magic-section-overlay")
                            .removeClass("active");
                    } else {
                        nearestMagicSection
                            .parent()
                            .siblings(".premium-magic-section-overlay")
                            .addClass("active");
                    }
                    switch (settings["position"]) {
                        case "top":
                            offset = -1 * ($magicElem.outerHeight() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    top: offset
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.animate({
                                        top: 0
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            } else {
                                nearestMagicSection.animate({
                                    top: 0
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.animate({
                                        top: -1 * offset
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            }
                            break;
                        case "bottom":
                            offset = -1 * ($magicElem.outerHeight() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    bottom: offset
                                }, "fast", "linear");
                            } else {
                                nearestMagicSection.animate({
                                    bottom: 0
                                }, "fast", "linear");
                            }
                            break;
                        case "right":
                            offset = -1 * ($magicElem.outerWidth() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    right: offset
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.css("left", "auto").animate({
                                        right: 0
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            } else {
                                nearestMagicSection.animate({
                                    right: 0
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.css("left", "auto").animate({
                                        right: -1 * offset
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            }
                            break;
                        case "left":
                            offset = -1 * ($magicElem.outerWidth() - gutter);
                            if (nearestMagicSection.hasClass("out")) {
                                nearestMagicSection.animate({
                                    left: offset
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.css("right", "auto").animate({
                                        left: 0
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            } else {
                                nearestMagicSection.animate({
                                    left: 0
                                }, "fast", "linear");
                                if (settings["style"] == "push") {
                                    $bodyInnerWrap.css("right", "auto").animate({
                                        left: -1 * offset
                                    },
                                        "fast",
                                        "linear"
                                    );
                                }
                            }
                            break;
                    }
                });

            $magicElem.removeClass('magic-section-hide');
        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-magic-section.default', PremiumMagicSectionHandler);
    });
})(jQuery);