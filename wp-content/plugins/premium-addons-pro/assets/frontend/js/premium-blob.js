(function ($) {

    window.premiumParallaxEffects = function (element, settings) {

        var self = this,
            $el = $(element),
            scrolls = $el.data("scrolls"),
            elementSettings = settings,
            elType = elementSettings.elType;

        //Check if Horizontal Scroll Widget
        var isHScrollWidget = $el.closest(".premium-hscroll-temp").length;

        self.elementRules = {};

        self.init = function () {

            if (scrolls || 'SECTION' === elType) {

                if (!elementSettings.effects.length) {
                    return;
                }
                self.setDefaults();
                elementorFrontend.elements.$window.on('scroll load', self.initScroll);
            } else {
                elementorFrontend.elements.$window.off('scroll load', self.initScroll);
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

            if ('rotate' === action) {
                elementSettings.defaults.unit = 'deg';
            } else {
                elementSettings.defaults.unit = 'px';
            }

            self.updateElement('transform', action, self.getStep(percents, data) + elementSettings.defaults.unit);

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

        self.initScroll = function () {

            if (elementSettings.effects.includes('translateY')) {

                self.initVScroll();

            }

            if (elementSettings.effects.includes('translateX')) {

                self.initHScroll();

            }

        };

        self.initVScroll = function () {

            var percents = self.getPercents();

            self.transform('translateY', percents, elementSettings.vscroll);

        };

        self.initHScroll = function () {

            var percents = self.getPercents();

            self.transform('translateX', percents, elementSettings.hscroll);

        };

        self.getDimensions = function () {

            var elementOffset = $el.offset();

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

        self.getEffectMovePoint = function (percents, effect, range) {

            var point = 0;

            if (percents < range.start) {
                if ("down" === effect) {
                    point = 0;
                } else {
                    point = 100;
                }
            } else if (percents < range.end) {

                point = self.getPointFromPercents((range.end - range.start), (percents - range.start));

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

        self.getEffectValueFromMovePoint = function (level, movePoint) {

            return level * movePoint / 100;

        };

        self.getPointFromPercents = function (movableRange, percents) {

            var movePoint = percents / movableRange * 100;

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

            var cssVarKey = '--' + key;

            element.style.setProperty(cssVarKey, value);

        };

        self.updateElementRule = function (rule) {

            var cssValue = '';

            $.each(self.elementRules[rule], function (variableKey) {
                cssValue += variableKey + '(var(--' + variableKey + '))';
            });

            $el.css(rule, cssValue);

        };

    };

    window.PremiumBlobGenerator = PremiumBlobGenerator = {

        scope: null,
        settings: {},

        init: function ($scope) {
            this.scope = $scope;

            var target = $scope,
                sectionId = target.data("id"),
                tempTarget = $scope.find('#premium-blob-gen-' + sectionId),
                editMode = elementorFrontend.isEditMode() && tempTarget.length > 0;

            this.settings = editMode ? tempTarget.data('blob') : $scope.data('blob');

            if (!this.settings) {
                return false;
            }

            this.generateBlob(editMode);
            this.applyParallax();

            if (editMode) {

                var freeHandSettings = {
                    repeater: 'premium_blob_repeater',
                    item: '.premium-blob-layer',
                    hor: 'premium_blob_hor_offset',
                    ver: 'premium_blob_ver_offset',
                    width: 'premium_blob_size',
                    tab: 'section_premium_blob',
                    offset: 0,
                    widgets: ["drag"]
                }, instance = null;

                instance = new premiumEditorBehavior(target, freeHandSettings);
                instance.init();
            }

        },

        generateBlob: function (editMode) {

            var _this = this,
                blobs = this.settings,
                blobHtml = '';

            if (editMode) {
                window.PremiumWidgetsEditor.updateBlobs(blobs, this.scope);
            }

            $.each(blobs, function (index, blob) {

                if (blob.devices.includes(elementorFrontend.getCurrentDeviceMode())) {
                    return true;
                }

                var blobSource = blob.source,
                    blobShadow = blob.shadow ? ' premium-blob-shadow ' : '';

                if ('custom' === blob.type) {
                    var path = BlobGenerator.svgPath({ seed: blob.source.seed, size: blob.size, extraPoints: blob.extraPoints, randomness: blob.randomness }).trim();
                    blobSource = editMode ? window.PremiumWidgetsEditor.drawBlob(blob, path) : blob.source.html;
                }

                blobHtml = '<div class="premium-blob-layer premium-blob-' + blob.id + blobShadow + ' elementor-repeater-item-' + blob.id + '">' + blobSource + '</div>';

                _this.scope.find('.premium-blob-' + blob.id).remove();

                $(blobHtml).prependTo(_this.scope).css({
                    'position': 'absolute',
                    'overflow': 'hidden',
                });

                if ('pre' === blob.type) {

                    if (0 !== $('.premium-blob-' + blob.id + ' svg pattern').length) {

                        $('.premium-blob-' + blob.id + ' svg pattern').attr('id', 'pattern' + blob.id);

                        $('.premium-blob-' + blob.id + ' svg > path').attr('fill', 'url(#pattern' + blob.id + ')');

                    } else if (0 !== $('.premium-blob-' + blob.id + ' svg linearGradient').length) {

                        $('.premium-blob-' + blob.id + ' svg linearGradient').attr('id', 'gradient' + blob.id);

                        $attr = 'none' === $('.premium-blob-' + blob.id + ' svg > path').attr('fill') ? 'stroke' : 'fill';

                        $('.premium-blob-' + blob.id + ' svg > path').attr($attr, 'url(#gradient' + blob.id + ')');

                    }
                } else {
                    //reset viewbox to match blob size if "custom" & 'frontend'
                    if (!editMode) {
                        $('.premium-blob-' + blob.id + ' svg').attr('viewBox', '0 0 ' + blob.size + ' ' + blob.size);
                    }
                }

                if (blob.animate && 0 < blob.extraPoints && 0 < blob.randomness) {

                    setTimeout(function () { //to make sure blobGenerator is defined.

                        // reset the original path first
                        var newPath = BlobGenerator.svgPath({ seed: blob.source.seed, size: blob.size, extraPoints: blob.extraPoints, randomness: blob.randomness }).trim();
                        $('.premium-blob-' + blob.id + ' svg > path').attr('d', newPath);

                        var animeSettings = {
                            targets: '.premium-blob-' + blob.id + ' svg > path',
                            loop: true,
                            direction: 'alternate',
                            easing: 'linear',
                            duration: parseFloat(blob.animeDur) * 1000,
                            d: [{ value: BlobGenerator.svgPath({ seed: Math.random(), size: blob.size, extraPoints: blob.extraPoints, randomness: blob.randomness }).trim() },
                            { value: BlobGenerator.svgPath({ seed: Math.random(), size: blob.size, extraPoints: blob.extraPoints, randomness: blob.randomness }).trim() }
                            ],
                        };

                        anime(animeSettings);
                    }, 100);

                }

                if (blob.parallax) {

                    if ('yes' === blob.parallaxSetting.vscroll) {

                        _this.scope.find('.elementor-repeater-item-' + blob.id).attr({
                            'data-parallax-scroll': 'yes',
                            'data-parallax-vscroll': 'yes',
                            'data-parallax-speed': blob.parallaxSetting.speed,
                            'data-parallax-start': blob.parallaxSetting.start,
                            'data-parallax-end': blob.parallaxSetting.end,
                            'data-parallax-direction': blob.parallaxSetting.direction
                        });
                    }

                    if ('yes' === blob.parallaxSetting.hscroll) {
                        _this.scope.find('.elementor-repeater-item-' + blob.id).attr({
                            'data-parallax-scroll': 'yes',
                            'data-parallax-hscroll': 'yes',
                            'data-parallax-hscroll_speed': blob.parallaxSetting.speed_h,
                            'data-parallax-hscroll_start': blob.parallaxSetting.start_h,
                            'data-parallax-hscroll_end': blob.parallaxSetting.end_h,
                            'data-parallax-hscroll_direction': blob.parallaxSetting.direction_h
                        });
                    }
                }

            });
        },

        applyParallax: function () {

            this.scope.find('.premium-blob-layer').each(function (index, layer) {
                var data = $(layer).data();

                if ('yes' === data.parallaxScroll) {

                    var effects = [],
                        vScrollSettings = {},
                        hScrollSettings = {},
                        settings = {},
                        instance = null;

                    if ('yes' === data.parallaxVscroll) {
                        effects.push('translateY');

                        vScrollSettings = {
                            speed: data.parallaxSpeed,
                            direction: data.parallaxDirection,
                            range: {
                                start: data.parallaxStart,
                                end: data.parallaxEnd
                            }
                        };
                    }

                    if ('yes' === data.parallaxHscroll) {
                        effects.push('translateX');

                        hScrollSettings = {
                            speed: data.parallaxHscroll_speed,
                            direction: data.parallaxHscroll_direction,
                            range: {
                                start: data.parallaxHscroll_start,
                                end: data.parallaxHscroll_end
                            }
                        };
                    }

                    settings = {
                        elType: 'SECTION',
                        vscroll: vScrollSettings,
                        hscroll: hScrollSettings,
                        effects: effects
                    };

                    instance = new premiumParallaxEffects(layer, settings);
                    instance.init();
                }
            });
        }
    };

    var PremiumBlobHandler = function ($scope) {

        if (!$scope.hasClass("premium-blob-yes"))
            return;

        window.PremiumBlobGenerator.init($scope);

    }

    $(window).on('elementor/frontend/init', function () {

        elementorFrontend.hooks.addAction("frontend/element_ready/section", PremiumBlobHandler);
        elementorFrontend.hooks.addAction("frontend/element_ready/container", PremiumBlobHandler);

    });

}(jQuery));