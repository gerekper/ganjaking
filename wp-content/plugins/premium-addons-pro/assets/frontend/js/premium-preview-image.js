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

        var PremiumPreviewWindowHandler = function ($scope, $) {
            var $prevWinElem = $scope.find(".premium-preview-image-wrap"),
                settings = $prevWinElem.data("settings"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                minWidth = null,
                maxWidth = null;

            if (-1 !== currentDevice.indexOf("mobile")) {
                minWidth = settings.minWidthMobs;
                maxWidth = settings.maxWidthMobs;
                //We need to make sure that content will not go out of screen.
                settings.side = ['top', 'bottom'];
            } else if (-1 !== currentDevice.indexOf("tablet")) {
                minWidth = settings.minWidthTabs;
                maxWidth = settings.maxWidthTabs;
            } else {
                minWidth = settings.minWidth;
                maxWidth = settings.maxWidth;
            }

            if (settings.responsive) {

                var previewImageOffset = $prevWinElem.offset().left;

                if (previewImageOffset < settings.minWidth) {
                    var difference = settings.minWidth - previewImageOffset;

                    settings.minWidth = settings.minWidth - difference;
                }
            }


            var $figure = $prevWinElem.find('.premium-preview-image-figure'),
                floatData = $figure.data();

            if (floatData.float) {

                if ($scope.hasClass("pa-previmg-disable-fe-yes")) {
                    if (window.paCheckSafari)
                        return;
                }

                var animeSettings = {
                    targets: $figure[0],
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

            // if interactive is enabled and delay = 0 >> make out-delay more than zero to enable interactive.
            var delay = 0 === settings.delay && settings.active ? [0, 0.1] : settings.delay;

            $prevWinElem.find(".premium-preview-image-inner-trig-img").tooltipster({
                functionBefore: function () {
                    if (settings.hideMobiles && ['mobile', 'mobile_extra'].includes(currentDevice)) {
                        return false;
                    }
                },
                functionInit: function (instance, helper) {
                    var content = $(helper.origin).find("#tooltip_content").detach();
                    instance.content(content);
                },
                functionReady: function () {
                    $(".tooltipster-box").addClass("tooltipster-box-" + settings.id);

                    //prevent class overlapping.
                    var premElements = $('.tooltipster-box-' + settings.id),
                        length = premElements.length;

                    if (premElements.length > 1) {
                        delete premElements[length - 1];
                        premElements.removeClass('tooltipster-box-' + settings.id);
                    }

                },
                contentCloning: true,
                plugins: ['sideTip'],
                animation: settings.anim,
                animationDuration: settings.animDur,
                delay: delay,
                updateAnimation: null,
                trigger: "custom",
                triggerOpen: {
                    tap: true,
                    mouseenter: true
                },
                triggerClose: {
                    tap: true,
                    mouseleave: true
                },
                arrow: settings.arrow,
                contentAsHTML: true,
                autoClose: false,
                maxWidth: maxWidth,
                minWidth: minWidth,
                distance: settings.distance,
                interactive: settings.active,
                minIntersection: 16,
                side: settings.side
            });

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-preview-image.default', PremiumPreviewWindowHandler);
    });
})(jQuery);