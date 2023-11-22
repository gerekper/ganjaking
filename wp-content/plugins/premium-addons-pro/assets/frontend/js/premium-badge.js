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

        var premiumGlobalBadgeHandler = function ($scope, $) {

            if (!$scope.hasClass('premium-gbadge-yes')) {
                return;
            }

            var elemType = $scope.data('element_type'),
                id = $scope.data("id"),
                settings = {};

            generateSettings(elemType, id);

            if (!settings) {
                return false;
            }

            $(window).trigger('resize');

            elementorFrontend.waypoint(
                $scope,
                function () {
                    generateGlobalbadge();
                }
            );


            function generateSettings(type, id) {

                var editMode = elementorFrontend.isEditMode(),
                    badgeSettings = {},
                    tempTarget = $scope.find('#premium-global-badge-' + id),
                    tempTarget2 = $scope.find('#premium-global-badge-temp-' + id),
                    tempExist = 0 !== tempTarget.length || 0 !== tempTarget2.length,
                    editMode = elementorFrontend.isEditMode() && tempExist;

                if (editMode) {
                    badgeSettings = tempTarget.data('gbadge');

                    if ('widget' === type && !badgeSettings) {
                        badgeSettings = tempTarget2.data('gbadge');
                    }
                } else {
                    badgeSettings = $scope.data('gbadge');
                }

                if (!badgeSettings) {
                    return false;
                }

                settings.text = badgeSettings.text;
                settings.icon = badgeSettings.icon;

                if (badgeSettings.icon) {
                    settings.iconType = badgeSettings.iconType;
                }

                if (badgeSettings.svgLayer) {
                    settings.svgLayer = badgeSettings.svgLayer;
                }

                if (badgeSettings.floating) {
                    settings.floating = badgeSettings.floating;
                }

                if (0 !== Object.keys(settings).length) {
                    return settings;
                }
            }

            function generateGlobalbadge() {

                var uniqueClass = 'premium-global-badge-' + id,
                    badgeHtml = '<div class="premium-global-badge ' + uniqueClass + '">' + getbadgeHtml(settings) + '</div>';

                if (settings.svgLayer) {
                    badgeHtml += '<div class="premium-gbadge-svg premium-gbadge-svg-' + id + '">' + settings.svgLayer + '</div>';
                }

                $scope.find("." + uniqueClass).remove();
                $scope.prepend(badgeHtml);

                if (settings.icon) {
                    if ('icon' === settings.iconType && 'svg' === settings.icon.library) {
                        handleSvgIcon(settings.icon.value.url, id);
                    }

                    if ('lottie' === settings.iconType) {
                        var $item = $scope.find('.premium-lottie-animation'),
                            instance = new premiumLottieAnimations($item);
                        instance.init();
                    }
                }

                if (settings.floating) {

                    if ($scope.hasClass("pa-badge-disable-fe-yes")) {
                        if (window.paCheckSafari)
                            return;
                    }

                    var animeTarget = !settings.svgLayer ? uniqueClass : uniqueClass + ' , .premium-gbadge-svg-' + id;
                    applyFloatingEffects(settings.floating, animeTarget);
                }
            }

            function getbadgeHtml(settings) {
                var badgeHtml = '<div class="premium-badge-container"> <span class="premium-badge-text">' + escapeHtml(settings.text) + '</span>';

                if (settings.icon) {
                    badgeHtml += '<span class="premium-badge-icon">';

                    if ('icon' === settings.iconType) {
                        if ('svg' !== settings.icon.library) {
                            badgeHtml += '<i class=" premium-badge-icon-fa ' + settings.icon.value + '"></i>';
                        }

                    } else if ('image' === settings.iconType) {
                        badgeHtml += '<img class="premium-badge-img" src="' + settings.icon.url + '" alt="' + settings.icon.alt + '">';

                    } else {
                        badgeHtml += '<div class="premium-lottie-animation premium-badge-lottie-icon" data-lottie-url="' + settings.icon.url + '" data-lottie-loop="' + settings.icon.loop + '" data-lottie-reverse="' + settings.icon.reverse + '" ></div>';
                    }
                    badgeHtml += '</span>';
                }

                return badgeHtml + '</div>';
            }

            function escapeHtml(unsafe) {
                var badgeTxt = $(document.createElement("DIV")).html(unsafe).text();
                return badgeTxt;
            }

            function handleSvgIcon(url, id) {

                var parser = new DOMParser();

                fetch(url)
                    .then(
                        function (response) {
                            if (200 !== response.status) {
                                console.log('Looks like there was a problem loading your svg. Status Code: ' +
                                    response.status);
                                return;
                            }

                            response.text().then(function (text) {
                                var parsed = parser.parseFromString(text, 'text/html'),
                                    svg = parsed.querySelector('svg');

                                $(svg).attr('class', 'premium-badge-icon-svg');
                                $scope.find('.premium-global-badge-' + id + ' .premium-badge-icon').html($(parsed).find('svg'));
                            });
                        }
                    );
            }

            function applyFloatingEffects(effects, target) {
                var animeSettings = {
                    targets: '.' + target,
                    loop: true,
                    direction: 'alternate',
                    easing: 'easeInOutSine'
                };

                if (effects.translate) {
                    var data = effects.translate,
                        x_translate = {
                            value: [data.x_param_from || 0, data.x_param_to || 0],
                            duration: data.speed,
                        },
                        y_translate = {
                            value: [data.y_param_from || 0, data.y_param_to || 0],
                            duration: data.speed,
                        };

                    animeSettings.translateX = x_translate;
                    animeSettings.translateY = y_translate;
                }

                if (effects.rotate) {
                    var data = effects.rotate,
                        x_rotate = {
                            duration: data.speed,
                            value: [data.x_param_from || 0, data.x_param_to || 0],
                        },
                        y_rotate = {
                            duration: data.speed,
                            value: [data.y_param_from || 0, data.y_param_to || 0],
                        },
                        z_rotate = {
                            duration: data.speed,
                            value: [data.z_param_from || 0, data.z_param_to || 0],
                        };

                    animeSettings.rotateX = x_rotate;
                    animeSettings.rotateY = y_rotate;
                    animeSettings.rotateZ = z_rotate;
                }

                if (effects.opacity) {
                    var data = effects.opacity;

                    animeSettings.opacity = {
                        value: [data.from || 0, data.to || 0],
                        duration: data.speed,
                    };
                }

                if (effects.filters) {
                    var data = effects.filters,
                        filterArr = [];

                    if (data.blur) {
                        var blurEffect = {
                            value: [data.blur.from || 0, data.blur.to || 0],
                            duration: data.blur.duration,
                            delay: data.blur.delay || 0
                        };

                        filterArr.push(blurEffect);
                    }

                    if (data.gscale) {
                        var gscaleEffect = {
                            value: [data.gscale.from || 0, data.gscale.to || 0],
                            duration: data.gscale.duration,
                            delay: data.gscale.delay || 0
                        };

                        filterArr.push(gscaleEffect);
                    }

                    animeSettings.filter = filterArr;
                }

                anime(animeSettings);
            }
        };

        elementorFrontend.hooks.addAction("frontend/element_ready/global", premiumGlobalBadgeHandler);
    });

})(jQuery);