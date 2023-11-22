(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumColorTransitionHandler = elementorModules.frontend.handlers.Base.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        scrollElement: '.premium-scroll-background',
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $scrollElement: this.$element.find(selectors.scrollElement)
                    };

                return elements;
            },

            bindEvents: function () {

                var _this = this,
                    //Used to delay trigger if content will be shown on the page. For example, Black Friday bar.
                    delay = _this.$element.hasClass("delay-trigger") ? 500 : 0;

                setTimeout(function () {
                    _this.setWidgetSettings();
                    _this.run();
                }, delay)

            },

            setWidgetSettings: function () {

                var repeaterSettings = this.getRepeaterSettings(),
                    currentDevice = elementorFrontend.getCurrentDeviceMode();

                var layoutSettings = {
                    offset: null,
                    isNull: false,
                    isSolid: true,
                    elements: repeaterSettings.elements,
                    downColors: repeaterSettings.downColors,
                    upColors: repeaterSettings.upColors,
                    itemsIDs: repeaterSettings.itemsIDs,
                    downOffsets: repeaterSettings.downOffsets,
                    upOffsets: repeaterSettings.upOffsets,
                    id: this.$element.data('id'),
                    offset: 'offset' + ('desktop' === currentDevice ? '' : '_' + currentDevice)
                };

                layoutSettings.$firstElement = $('#' + layoutSettings.elements[0]);
                layoutSettings.$lastElement = $('#' + layoutSettings.elements[layoutSettings.elements.length - 1]);

                // we need to check if elements really exists before proceeding forward
                if (layoutSettings.$firstElement.length && layoutSettings.$lastElement.length) {

                    layoutSettings.firstElemOffset = layoutSettings.$firstElement.offset().top;
                    layoutSettings.lastElemOffset = layoutSettings.$lastElement.offset().top;
                    layoutSettings.lastElemeHeight = layoutSettings.$lastElement.outerHeight();
                    layoutSettings.lastElemeBot = layoutSettings.lastElemOffset + layoutSettings.lastElemeHeight;
                }

                this.settings = layoutSettings;

            },

            run: function () {

                var _this = this,
                    $window = $(window),
                    $scrollElement = this.elements.$scrollElement;

                //Widget Settings
                var elements = this.settings.elements,
                    downColors = this.settings.downColors,
                    downOffsets = this.settings.downOffsets,
                    upOffsets = this.settings.upOffsets,
                    itemsIDs = this.settings.itemsIDs;

                //Make sure all IDs refer to existing elements.
                for (var i = 0; i < elements.length; i++) {
                    if (!$('#' + elements[i]).length) {
                        $scrollElement.html('<div class="premium-error-notice">Please make sure that IDs added to the widget are valid</div>');
                        this.settings.isNull = true;
                        break;
                    }
                }

                if (this.settings.isNull)
                    return;

                //Change to desktop offset if empty.
                if (undefined == this.getElementSettings(this.settings.offset))
                    this.settings.offset = 'offset';

                $('<div id="premium-color-transition-' + this.settings.id + '" class="premium-color-transition"></div>').prependTo($('body'));

                $(document).ready(function () {
                    if ($('.premium-color-transition').length > 1)
                        $window.on('scroll', _this.checkVisible);
                });

                downColors.forEach(function (color) {
                    if (-1 !== color.indexOf('//'))
                        _this.settings.isSolid = false;
                });

                if (!this.getElementSettings('gradient'))
                    this.settings.isSolid = false;

                if (this.settings.isSolid) {

                    this.rowTransitionalColor = function ($row, firstColor, secondColor) {
                        "use strict";

                        var firstColor = _this.hexToRgb(firstColor),
                            secondColor = _this.hexToRgb(secondColor);

                        var scrollPos = 0,
                            currentRow = $row,
                            beginningColor = firstColor,
                            endingColor = secondColor,
                            percentScrolled, newRed, newGreen, newBlue, newColor;

                        $(document).scroll(function () {
                            var animationBeginPos = currentRow.offset().top,
                                endPart = currentRow.outerHeight() / 4,
                                animationEndPos = animationBeginPos + currentRow.outerHeight() - endPart;

                            scrollPos = $(this).scrollTop();

                            if (scrollPos >= animationBeginPos && scrollPos <= animationEndPos) {
                                percentScrolled = (scrollPos - animationBeginPos) / (currentRow.outerHeight() - endPart);
                                newRed = Math.abs(beginningColor.r + (endingColor.r - beginningColor.r) * percentScrolled);
                                newGreen = Math.abs(beginningColor.g + (endingColor.g - beginningColor.g) * percentScrolled);
                                newBlue = Math.abs(beginningColor.b + (endingColor.b - beginningColor.b) * percentScrolled);

                                newColor = "rgb(" + newRed + "," + newGreen + "," + newBlue + ")";

                                $('#premium-color-transition-' + _this.settings.id).css({
                                    backgroundColor: newColor
                                });

                            } else if (scrollPos > animationEndPos) {
                                $('#premium-color-transition-' + _this.settings.id).css({
                                    backgroundColor: endingColor
                                });
                            }
                        });

                    };

                    this.hexToRgb = function (hex) {

                        if (-1 !== hex.indexOf("rgb")) {
                            var rgb = (hex.substring(hex.indexOf("(") + 1)).split(",");
                            return {
                                r: parseInt(rgb[0]),
                                g: parseInt(rgb[1]),
                                b: parseInt(rgb[2])
                            };

                        } else {
                            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                            return result ? {
                                r: parseInt(result[1], 16),
                                g: parseInt(result[2], 16),
                                b: parseInt(result[3], 16)
                            } : null;
                        }
                    };

                    $('#premium-color-transition-' + this.settings.id).css({
                        backgroundColor: downColors[0]
                    });

                    var parent_node = $("#premium-color-transition-" + this.settings.id).closest(".elementor");

                    if (0 === parent_node.length)
                        parent_node = $(".elementor").first();

                    var i = 0,
                        arry_len = downColors.length,
                        isLooped = null;

                    $(".elementor > .elementor-element, .elementor-section-wrap > .elementor-element").each(function () {
                        if (arry_len <= i)
                            i = 0;

                        var firstColor = i,
                            secondColor = i + 1;

                        if (downColors[firstColor] !== '' && downColors[firstColor] != undefined) {
                            firstColor = downColors[firstColor];
                        }
                        if (downColors[secondColor] !== '' && downColors[secondColor] != undefined) {
                            isLooped = false;
                            secondColor = downColors[secondColor];
                        } else {
                            i = 0;
                            isLooped = true;
                            secondColor = i;
                            secondColor = downColors[secondColor];
                        }

                        _this.rowTransitionalColor($(this), firstColor, secondColor);
                        if (!isLooped)
                            i++;
                    });

                } else {

                    //Refresh all Waypoints instances.
                    Waypoint.refreshAll();

                    var currentActiveIndex = null;
                    elements.forEach(function (element, index) {

                        $('<div class="premium-color-transition-layer elementor-repeater-item-' + itemsIDs[index] + '" data-direction="down"></div>').prependTo($('#premium-color-transition-' + _this.settings.id));

                        $('<div class="premium-color-transition-layer elementor-repeater-item-' + itemsIDs[index] + '" data-direction="up"></div>').prependTo($('#premium-color-transition-' + _this.settings.id));

                        if (_this.visible($('#' + element), true)) {
                            $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').addClass('layer-active');
                            currentActiveIndex = index;
                        }

                        elementorFrontend.waypoint(
                            $('#' + element),
                            function (direction) {

                                if ('down' === direction) {

                                    var downBackground = _this.settings.downColors[index];

                                    if (_this.checkDifferentBackgrounds(downBackground, currentActiveIndex)) {
                                        $('.premium-color-transition-layer').removeClass('layer-active');
                                        $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').addClass('layer-active');
                                        currentActiveIndex = index;
                                    }

                                }
                            }, {
                            offset: downOffsets[index],
                            triggerOnce: false
                        }
                        );

                        elementorFrontend.waypoint(
                            $('#' + element),
                            function (direction) {
                                if ('up' === direction) {

                                    var upBackground = _this.settings.upColors[index];

                                    if (_this.checkDifferentBackgrounds(upBackground, currentActiveIndex)) {

                                        $('.premium-color-transition-layer').removeClass('layer-active');
                                        $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="up"]').addClass('layer-active');

                                        currentActiveIndex = index;
                                    }

                                }
                            }, {
                            offset: upOffsets[index],
                            triggerOnce: false
                        }
                        );

                    });

                }

            },

            // Compare between the color to be changed and the current color of active layer.
            // If equal, then no need to change the color.
            checkDifferentBackgrounds: function (background, active) {

                var currentActiveDir = $('#premium-color-transition-' + this.settings.id + ' .layer-active').data('direction'),
                    currentActiveBackground;

                if ('down' === currentActiveDir) {
                    currentActiveBackground = this.settings.downColors[active];
                } else {
                    currentActiveBackground = this.settings.upColors[active];
                }

                //If current active is null, then none of the sections are in the viewport. We must change background.
                return null != active ? -1 == currentActiveBackground.indexOf(background) : true;

            },

            getRepeaterSettings: function () {
                var repeater = this.getElementSettings('id_repeater'),
                    elements = [],
                    downColors = [],
                    itemsIDs = [],
                    upColors = [],
                    downOffsets = [],
                    upOffsets = [],
                    globalOffset = this.getElementSettings('offset') || 30;

                repeater.forEach(function (element, index) {

                    elements.push(element.section_id);
                    itemsIDs.push(element._id);

                    element.down_background = element.down_color;

                    if ('image' === element.scroll_down_type && '' !== element.down_image.url) {
                        element.down_background = element.down_image.url;
                    }

                    element.up_background = element.up_color;

                    if ('image' === element.scroll_up_type && '' !== element.up_image.url) {
                        element.up_background = element.up_image.url;
                    }

                    if ('' === element.up_background) {
                        element.up_background = element.down_background;
                    }

                    downColors.push(element.down_background);
                    upColors.push(element.up_background);

                    switch (element.scroll_down_offset) {
                        case '':
                            downOffsets.push(0 === index ? 'bottom-in-view' : globalOffset);
                            break;
                        case 'top-in-view':
                            downOffsets.push('0');
                            break;
                        case 'bottom-in-view':
                            downOffsets.push(element.scroll_down_offset);
                            break;
                        default:
                            downOffsets.push(element.scroll_down_custom_offset.size + element.scroll_down_custom_offset.unit);
                            break;
                    }

                    switch (element.scroll_up_offset) {
                        case '':
                            upOffsets.push("-" + globalOffset);
                            break;
                        case 'top-in-view':
                            upOffsets.push('0');
                            break;
                        case 'bottom-in-view':
                            upOffsets.push(element.scroll_up_offset);
                            break;
                        default:
                            upOffsets.push("-" + element.scroll_up_custom_offset.size + element.scroll_up_custom_offset.unit);
                            break;
                    }
                });

                return {
                    elements: elements,
                    downColors: downColors,
                    upColors: upColors,
                    itemsIDs: itemsIDs,
                    downOffsets: downOffsets,
                    upOffsets: upOffsets,
                };

            },

            checkVisible: function () {
                var settings = this.settings,
                    $window = $(window);

                if (undefined === settings.firstElemOffset || undefined === settings.lastElemOffset)
                    return;

                if ($window.scrollTop() >= settings.lastElemeBot - settings.lastElemeHeight / 4) {
                    var index = $('#premium-color-transition-' + settings.id).index();
                    if (0 !== index)
                        $('#premium-color-transition-' + settings.id).addClass('premium-bg-transition-hidden');

                }
                if (($window.scrollTop() >= settings.firstElemOffset) && ($window.scrollTop() < settings.lastElemOffset)) {
                    $('#premium-color-transition-' + settings.id).removeClass('premium-bg-transition-hidden');
                }
            },

            visible: function (selector, partial, hidden) {
                var s = selector.get(0),
                    $window = $(window),
                    vpHeight = $window.outerHeight(),
                    clientSize =
                        hidden === true ? s.offsetWidth * s.offsetHeight : true;

                if (typeof s.getBoundingClientRect === "function") {
                    var rec = s.getBoundingClientRect();
                    var tViz = rec.top >= 0 && rec.top < vpHeight,
                        bViz = rec.bottom > 0 && rec.bottom <= vpHeight,
                        vVisible = partial ? tViz || bViz : tViz && bViz,
                        vVisible =
                            rec.top < 0 && rec.bottom > vpHeight ? true : vVisible;
                    return clientSize && vVisible;
                } else {
                    var viewTop = 0,
                        viewBottom = viewTop + vpHeight,
                        position = $window.position(),
                        _top = position.top,
                        _bottom = _top + $window.height(),
                        compareTop = partial === true ? _bottom : _top,
                        compareBottom = partial === true ? _top : _bottom;
                    return (
                        !!clientSize &&
                        (compareBottom <= viewBottom && compareTop >= viewTop)
                    );
                }
            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-color-transition', PremiumColorTransitionHandler);
    });
})(jQuery);