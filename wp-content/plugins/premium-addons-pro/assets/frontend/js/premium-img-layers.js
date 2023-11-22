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

        window.premiumImageLayersEffects = function (element, settings) {

            var self = this,
                $el = $(element),
                scrolls = $el.data("scrolls"),
                elementSettings = settings,
                elType = elementSettings.elType,
                elOffset = $el.offset();

            //Check if Horizontal Scroll Widget
            var isHScrollWidget = $el.closest(".premium-hscroll-temp").length;

            self.elementRules = {};

            self.init = function () {

                if (scrolls || 'SECTION' === elType) {

                    if (!elementSettings.effects.length > 0) {
                        return;
                    }
                    self.setDefaults();
                    self.initScroll('load');
                    elementorFrontend.elements.$window.on('scroll', self.initScroll);
                } else {

                    elementorFrontend.elements.$window.off('scroll', self.initScroll);
                    return;
                }

            };

            self.setDefaults = function () {

                elementSettings.defaults = {};
                elementSettings.defaults.axis = 'y';

            };

            self.transform = function (action, percents, data) {

                if ("down" === data.direction) {
                    percents = 100 - percents;
                }

                if (data.range) {
                    if (data.range.start > percents && !isHScrollWidget) {
                        percents = data.range.start;
                    }

                    if (data.range.end < percents && !isHScrollWidget) {
                        percents = data.range.end;
                    }
                }

                if ("rotate" === action) {
                    elementSettings.defaults.unit = "deg";
                } else {
                    elementSettings.defaults.unit = "px";
                }

                self.updateElement(
                    "transform",
                    action,
                    self.getStep(percents, data) + elementSettings.defaults.unit
                );

            };

            self.getPercents = function () {
                var dimensions = self.getDimensions();

                var startOffset = innerHeight;

                if (isHScrollWidget) startOffset = 0;

                (elementTopWindowPoint = dimensions.elementTop - pageYOffset),
                    (elementEntrancePoint = elementTopWindowPoint - startOffset);

                passedRangePercents =
                    (100 / dimensions.range) * (elementEntrancePoint * -1);

                return passedRangePercents;
            };

            self.initScroll = function (event) {

                if ("load" === event) {
                    $el.css("transition", "all 1s ease");
                } else {
                    $el.css("transition", "none");
                }

                if (elementSettings.effects.includes('translateY')) {

                    self.initVScroll();

                }

                if (elementSettings.effects.includes('translateX')) {

                    self.initHScroll();

                }

                if (elementSettings.effects.includes('opacity')) {

                    self.initOScroll();

                }

                if (elementSettings.effects.includes('blur')) {

                    self.initBScroll();

                }

                if (elementSettings.effects.includes('gray')) {

                    self.initGScroll();

                }

                if (elementSettings.effects.includes('rotate')) {

                    self.initRScroll();

                }

                if (elementSettings.effects.includes('scale')) {

                    self.initScaleScroll();

                }

            };

            self.initVScroll = function () {
                var percents = self.getPercents();

                self.transform("translateY", percents, elementSettings.vscroll);
            };

            self.initHScroll = function () {
                var percents = self.getPercents();

                self.transform("translateX", percents, elementSettings.hscroll);
            };

            self.getDimensions = function () {
                var elementOffset = elOffset;

                var dimensions = {
                    elementHeight: $el.outerHeight(),
                    elementWidth: $el.outerWidth(),
                    elementTop: elementOffset.top,
                    elementLeft: elementOffset.left
                };

                dimensions.range = dimensions.elementHeight + innerHeight;

                return dimensions;
            };

            self.getStep = function (percents, options) {
                return -(percents - 50) * options.speed;
            };

            self.initOScroll = function () {
                var percents = self.getPercents(),
                    data = elementSettings.oscroll,
                    movePoint = self.getEffectMovePoint(
                        percents,
                        data.fade,
                        data.range
                    ),
                    level = data.level / 10,
                    opacity =
                        1 -
                        level +
                        self.getEffectValueFromMovePoint(level, movePoint);

                $el.css("opacity", opacity);
            };

            self.initBScroll = function () {

                var percents = self.getPercents(),
                    data = elementSettings.bscroll,
                    movePoint = self.getEffectMovePoint(percents, data.blur, data.range),
                    blur = data.level - self.getEffectValueFromMovePoint(data.level, movePoint);

                self.updateElement('filter', 'blur', blur + 'px');

            };

            self.initGScroll = function () {

                var percents = self.getPercents(),
                    data = elementSettings.gscale,
                    movePoint = self.getEffectMovePoint(percents, data.gray, data.range),
                    grayScale = 10 * (data.level - self.getEffectValueFromMovePoint(data.level, movePoint));

                self.updateElement('filter', 'grayscale', grayScale + '%');

            };

            self.initRScroll = function () {
                var percents = self.getPercents();

                self.transform("rotate", percents, elementSettings.rscroll);
            };

            self.getEffectMovePoint = function (percents, effect, range) {
                var point = 0;

                if (percents < range.start) {
                    if ("down" === effect) {
                        point = 0;
                    } else {
                        point = 100;
                    }
                } else if (percents < range.end) {
                    point = self.getPointFromPercents(
                        range.end - range.start,
                        percents - range.start
                    );

                    if ("up" === effect) {
                        point = 100 - point;
                    }
                } else if ("up" === effect) {
                    point = 0;
                } else if ("down" === effect) {
                    point = 100;
                }

                return point;
            };

            self.initScaleScroll = function () {
                var percents = self.getPercents(),
                    data = elementSettings.scale,
                    movePoint = self.getEffectMovePoint(
                        percents,
                        data.direction,
                        data.range
                    );

                this.updateElement(
                    "transform",
                    "scale",
                    1 + (data.speed * movePoint) / 1000
                );
            };

            self.getEffectValueFromMovePoint = function (level, movePoint) {
                return (level * movePoint) / 100;
            };

            self.getPointFromPercents = function (movableRange, percents) {
                var movePoint = (percents / movableRange) * 100;

                return +movePoint.toFixed(2);
            };

            self.updateElement = function (propName, key, value) {
                if (!self.elementRules[propName]) {
                    self.elementRules[propName] = {};
                }

                if (!self.elementRules[propName][key]) {
                    self.elementRules[propName][key] = true;

                    self.updateElementRule(propName);
                }

                var cssVarKey = "--" + key;

                element.style.setProperty(cssVarKey, value);
            };

            self.updateElementRule = function (rule) {
                var cssValue = "";

                $.each(self.elementRules[rule], function (variableKey) {
                    cssValue += variableKey + "(var(--" + variableKey + "))";
                });

                $el.css(rule, cssValue);
            };

        };

        // Image Layers Handler
        var PremiumImageLayersHandler = function ($scope, $) {

            var $imgLayers = $scope.find(".premium-img-layers-wrapper"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                layers = $imgLayers.find(".premium-img-layers-list-item"),
                applyOn = $imgLayers.data("devices"),
                disableFEOnSafai = $scope.hasClass("pa-imglayers-disable-fe-yes");

            layers.each(function (index, layer) {
                var $layer = $(layer),
                    data = $layer.data(),
                    hideOn = data.layerHide,
                    isRemoved = false;

                if ('object' == typeof hideOn && hideOn.length > 0) {

                    hideOn.map(function (device) {

                        if ('desktop' === device && -1 == currentDevice.indexOf('mobile') && -1 == currentDevice.indexOf('tablet')) {
                            $layer.remove();
                            isRemoved = true;
                        } else if (-1 !== currentDevice.indexOf(device)) {
                            $layer.remove();
                            isRemoved = true;
                        }

                    });
                }

                if (isRemoved)
                    return;

                if (data.scrolls) {
                    if (-1 !== applyOn.indexOf(currentDevice)) {

                        var instance = null,
                            effects = [],
                            vScrollSettings = {},
                            hScrollSettings = {},
                            oScrollSettings = {},
                            bScrollSettings = {},
                            rScrollSettings = {},
                            scaleSettings = {},
                            grayScaleSettings = {},
                            settings = {};

                        if (data.scrolls) {

                            if (data.vscroll) {
                                effects.push('translateY');
                                vScrollSettings = {
                                    speed: data.vscrollSpeed,
                                    direction: data.vscrollDir,
                                    range: {
                                        start: data.vscrollStart,
                                        end: data.vscrollEnd
                                    }
                                };
                            }
                            if (data.hscroll) {
                                effects.push('translateX');
                                hScrollSettings = {
                                    speed: data.hscrollSpeed,
                                    direction: data.hscrollDir,
                                    range: {
                                        start: data.hscrollStart,
                                        end: data.hscrollEnd
                                    }
                                };
                            }
                            if (data.oscroll) {
                                effects.push('opacity');
                                oScrollSettings = {
                                    level: data.oscrollLevel,
                                    fade: data.oscrollEffect,
                                    range: {
                                        start: data.oscrollStart,
                                        end: data.oscrollEnd
                                    }
                                };
                            }
                            if (data.bscroll) {
                                effects.push('blur');
                                bScrollSettings = {
                                    level: data.bscrollLevel,
                                    blur: data.bscrollEffect,
                                    range: {
                                        start: data.bscrollStart,
                                        end: data.bscrollEnd
                                    }
                                };
                            }
                            if (data.rscroll) {
                                effects.push('rotate');
                                rScrollSettings = {
                                    speed: data.rscrollSpeed,
                                    direction: data.rscrollDir,
                                    range: {
                                        start: data.rscrollStart,
                                        end: data.rscrollEnd
                                    }
                                };
                            }
                            if (data.scale) {
                                effects.push('scale');
                                scaleSettings = {
                                    speed: data.scaleSpeed,
                                    direction: data.scaleDir,
                                    range: {
                                        start: data.scaleStart,
                                        end: data.scaleEnd
                                    }
                                };
                            }
                            if (data.gscale) {
                                effects.push('gray');
                                grayScaleSettings = {
                                    level: data.gscaleLevel,
                                    gray: data.gscaleEffect,
                                    range: {
                                        start: data.gscaleStart,
                                        end: data.gscaleEnd
                                    }
                                };
                            }

                        }

                        settings = {
                            elType: 'Widget',
                            vscroll: vScrollSettings,
                            hscroll: hScrollSettings,
                            oscroll: oScrollSettings,
                            bscroll: bScrollSettings,
                            rscroll: rScrollSettings,
                            scale: scaleSettings,
                            gscale: grayScaleSettings,
                            effects: effects
                        };

                        instance = new premiumImageLayersEffects(layer, settings);
                        instance.init();

                    }

                } else if (data.float) {

                    if (disableFEOnSafai) {
                        if (window.paCheckSafari)
                            return;
                    }

                    var floatXSettings = null,
                        floatYSettings = null,
                        floatRotateXSettings = null,
                        floatRotateYSettings = null,
                        floatRotateZSettings = null;

                    var animeSettings = {
                        targets: $layer[0],
                        loop: true,
                        direction: 'alternate',
                        easing: 'easeInOutSine'
                    };

                    if (data.floatTranslate) {

                        floatXSettings = {
                            duration: data.floatTranslateSpeed * 1000,
                            value: [data.floatxStart || 0, data.floatxEnd || 0]
                        };

                        animeSettings.translateX = floatXSettings;

                        floatYSettings = {
                            duration: data.floatTranslateSpeed * 1000,
                            value: [data.floatyStart || 0, data.floatyEnd || 0]
                        };

                        animeSettings.translateY = floatYSettings;

                    }

                    if (data.floatRotate) {

                        floatRotateXSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotatexStart || 0, data.rotatexEnd || 0]
                        };

                        animeSettings.rotateX = floatRotateXSettings;

                        floatRotateYSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotateyStart || 0, data.rotateyEnd || 0]
                        };

                        animeSettings.rotateY = floatRotateYSettings;

                        floatRotateZSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotatezStart || 0, data.rotatezEnd || 0]
                        };

                        animeSettings.rotateZ = floatRotateZSettings;

                    }

                    if (data.floatOpacity) {
                        animeSettings.opacity = {
                            duration: data.floatOpacitySpeed * 1000,
                            value: data.floatOpacityValue || 0
                        };
                    }


                    anime(animeSettings);
                }

                if ($layer.data("layer-animation") && " " != $layer.data("layer-animation")) {

                    new Waypoint({
                        element: $($imgLayers),
                        offset: Waypoint.viewportHeight() - 150,
                        handler: function () {

                            $layer.addClass("animated " + $layer.data("layer-animation"));

                            //Opacity should sync animation delay before setting it to 1.
                            var animationDelay = $layer.css("animation-delay") ? parseFloat($layer.css("animation-delay").replace("s", "")) : 0;

                            setTimeout(function () {
                                $layer.css("opacity", 1);
                            }, animationDelay * 1000);
                        }
                    });
                }

                if ($layer.hasClass('premium-mask-yes')) {
                    var html = '';
                    $layer.find('.premium-img-layers-text').text().split(' ').forEach(function (word) {
                        html += ' <span class="premium-mask-span">' + word + '</span>';
                    });

                    $layer.find('.premium-img-layers-text').text('').append(html);

                    elementorFrontend.waypoint($scope, function () {
                        $layer.find('.premium-img-layers-text').addClass('premium-mask-active');
                    }, {
                        offset: Waypoint.viewportHeight() - 150,
                        triggerOnce: true
                    });
                }

            });


            $imgLayers.find('.premium-img-layers-list-item[data-parallax="true"]').each(function () {

                var $this = $(this),
                    resistance = $(this).data("rate"),
                    reverse = -1;

                if ($this.data("mparallax-reverse"))
                    reverse = 1;

                $imgLayers.mousemove(function (e) {
                    TweenLite.to($this, 0.2, {
                        x: reverse * ((e.clientX - window.innerWidth / 2) / resistance),
                        y: reverse * ((e.clientY - window.innerHeight / 2) / resistance)
                    });
                });

                if ($this.data("mparallax-init")) {
                    $imgLayers.mouseleave(function () {
                        TweenLite.to($this, 0.4, {
                            x: 0,
                            y: 0
                        });
                    });
                }

            });

            var tilts = $imgLayers.find('.premium-img-layers-list-item[data-tilt="true"]');

            if (tilts.length > 0) {
                tilt = UniversalTilt.init({
                    elements: tilts,
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

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-img-layers-addon.default', PremiumImageLayersHandler);
    });
})(jQuery);