(function ($) {

    if ('undefined' == typeof window.paCheckSafari) {
        window.paCheckSafari = checkSafariBrowser();

        function checkSafariBrowser() {

            var iOS = /iP(hone|ad|od)/i.test(navigator.userAgent) && !window.MSStream;

            if (iOS) {
                var allowedBrowser = /(Chrome|CriOS|OPiOS|FxiOS)/.test(navigator.userAgent);

                if (!allowedBrowser) {
                    var isFireFox = '' === navigator.vendor;
                    allowedBrowser = allowedBrowser || isFireFox;
                }

                var isSafari = /WebKit/i.test(navigator.userAgent) && !allowedBrowser;

            } else {
                var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            }

            if (isSafari) {
                return true;
            }

            return false;
        }
    }

    $(window).on('elementor/frontend/init', function () {

        var PremiumImageHotspotHandler = function ($scope, $) {

            var $hotspotsElem = $scope.find(".premium-image-hotspots-container"),
                hotspots = $hotspotsElem.find(".tooltip-wrapper"),
                settings = $hotspotsElem.data("settings"),
                isEdit = elementorFrontend.isEditMode(),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                triggerClick = null,
                triggerHover = null,
                triggerClose = true,
                touchDevices = ['tablet', 'mobile', 'tablet_extra', 'mobile_extra'];

            //Tooltips to be opened by default.
            var $openedByDefault = $hotspotsElem.find(".tooltip-wrapper[data-active=true]");

            //Always trigger on click on touch devices
            if (touchDevices.includes(currentDevice) || settings.trigger === "click") {
                triggerClick = true;
                triggerHover = false;
            } else if (settings.trigger === "hover") {
                triggerClick = false;
                triggerHover = true;
            }

            if ('' !== settings.iconHover) {
                $hotspotsElem.find('.premium-image-hotspots-main-icons > svg').addClass('elementor-animation-' + settings.iconHover);
            }

            var $floatElem = $hotspotsElem.find('.premium-image-hotspots-img-wrap'),
                floatData = $floatElem.data();

            if (floatData.float) {

                if ($scope.hasClass("pa-hotspots-disable-fe-yes")) {
                    if (window.paCheckSafari)
                        return;
                }

                var animeSettings = {
                    targets: $floatElem[0],
                    loop: true,
                    direction: 'alternate',
                    easing: 'easeInOutSine'
                };

                if (floatData.floatTranslate) {

                    animeSettings.translateX = {
                        duration: floatData.floatTranslateSpeed * 1000,
                        value: [floatData.floatxStart || 0, floatData.floatxEnd || 0]
                    };

                    animeSettings.translateY = {
                        duration: floatData.floatTranslateSpeed * 1000,
                        value: [floatData.floatyStart || 0, floatData.floatyEnd || 0]
                    };

                }

                if (floatData.floatRotate) {

                    animeSettings.rotateX = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotatexStart || 0, floatData.rotatexEnd || 0]
                    };

                    animeSettings.rotateY = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotateyStart || 0, floatData.rotateyEnd || 0]
                    };

                    animeSettings.rotateZ = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotatezStart || 0, floatData.rotatezEnd || 0]
                    };

                }

                if (floatData.floatOpacity) {
                    animeSettings.opacity = {
                        duration: floatData.floatOpacitySpeed * 1000,
                        value: floatData.floatOpacityValue || 0
                    };
                }

                anime(animeSettings);

            }

            if (settings.trigger === "click") {
                triggerClose = 'yes' === settings.triggerClose;
            }

            hotspots.tooltipster({
                functionBefore: function () {

                    $openedByDefault.map(function (index, elem) {
                        $(elem).tooltipster('instance').close();
                    });

                    if (!triggerClose) {

                        $('#tooltip_content .premium-world-clock__time-wrapper').addClass('premium-addons__v-hidden');
                        $('#tooltip_content .premium-weather__outer-wrapper').css({ visibility: 'hidden', opacity: 0 });

                        $hotspotsElem.find(".tooltip-wrapper").map(function (index, elem) {
                            $(elem).tooltipster('instance').close();
                        });
                    }

                    if (settings.hideMobiles && "mobile" === currentDevice)
                        return false;
                },
                functionInit: function (instance, helper) {

                    if (!helper)
                        return;

                    if (isEdit) {

                        var templateID = $(helper.origin).data('template-id');
                        if (undefined !== templateID && '' !== templateID) {
                            $.ajax({
                                type: 'GET',
                                url: PremiumProSettings.ajaxurl,
                                data: {
                                    action: 'get_elementor_template_content',
                                    templateID: templateID
                                }
                            }).success(function (response) {
                                var data;

                                try {
                                    data = JSON.parse(response).data;
                                } catch (error) {
                                    data = response.data;
                                }

                                if (undefined !== data.template_content) {
                                    instance.content(data.template_content);
                                }
                            });
                        }
                    }

                    var content = $(helper.origin).find("#tooltip_content").detach();
                    instance.content(content);

                    $(helper.origin).find(".premium-image-hotspots-tooltips-wrapper").remove();

                },
                functionReady: function (origin, tooltipObj) {

                    var $tooltipContent = $(tooltipObj.tooltip);

                    $tooltipContent.find('.premium-world-clock__time-wrapper').css('opacity', 1);
                    $tooltipContent.find('.premium-weather__outer-wrapper').css({ visibility: 'visible', opacity: 1 });

                    // render lottie animation.
                    if ( $tooltipContent.find('.premium-lottie-animation').length ) {
                        var instance = new premiumLottieAnimations($tooltipContent);
                        instance.init();
                    }

                    if ($(tooltipObj.origin).find('.magnet-spot').length > 0)
                        $tooltipContent.css('cursor', 'none');

                    if (!$(".tooltipster-base").hasClass('premium-tooltipster-base')) { // make sure 'premium-tooltipster-base' is added
                        $(".tooltipster-base").addClass('premium-tooltipster-base');
                    }

                    if (!$(".tooltipster-base").hasClass('premium-hotspots-tooltip')) { // make sure 'premium-hotspots-tooltip' is added
                        $(".tooltipster-base").addClass('premium-hotspots-tooltip');
                    }

                    $(".tooltipster-box").addClass("tooltipster-box-" + settings.id);
                    $(".tooltipster-arrow").addClass("tooltipster-arrow-" + settings.id);

                    //Used to refresh the tooltip position to fix issues when large tooltip padding is added
                    hotspots.tooltipster('reposition');

                },
                contentCloning: true,
                plugins: ["sideTip"],
                animation: settings.anim,
                animationDuration: settings.animDur,
                delay: [settings.delay, 0.001],
                trigger: "custom",
                triggerOpen: {
                    click: triggerClick,
                    tap: true,
                    mouseenter: triggerHover
                },
                triggerClose: {
                    click: triggerClose,
                    tap: true,
                    mouseleave: triggerHover
                },
                arrow: settings.arrow,
                contentAsHTML: true,
                autoClose: false,
                minWidth: settings.minWidth,
                maxWidth: settings.maxWidth,
                distance: settings.distance,
                interactive: settings.active,
                minIntersection: 16,
                side: settings.side,
                functionPosition: function (instance, helper, position) {

                    var customHorPos = $(helper.origin).data('tooltip-h'),
                        customVerPos = $(helper.origin).data('tooltip-v'),
                        widgetPos = $floatElem[0].getBoundingClientRect();

                    if (customHorPos) {
                        customHorPos = customHorPos / 100;
                        position.coord.left = widgetPos.x + (widgetPos.width * customHorPos)
                    }


                    if (customVerPos) {
                        customVerPos = customVerPos / 100;
                        position.coord.top = widgetPos.y + (widgetPos.height * customVerPos)
                    }

                    return position;
                }
            });

            $openedByDefault.map(function (index, elem) {

                $(elem).tooltipster('instance').open();
            });

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-image-hotspots.default', PremiumImageHotspotHandler);
    });
})(jQuery);