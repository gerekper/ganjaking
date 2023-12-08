(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RippleEffects;

        RippleEffects = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },
            getDefaultSettings: function () {
                return {
                    // debug: true,
                    multi: true,
                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_ripple_') !== -1) {
                    this.run();
                }
            }, 400),
            settings: function (key) {
                return this.getElementSettings('ep_ripple_' + key);
            },
            run: function () {
                if (this.settings('enable') !== 'yes') {
                    return;
                }

                var $element = this.$element,
                    options = this.getDefaultSettings(),
                    $widgetId = 'ep-' + this.getID(),
                    $widgetClassSelect = '.elementor-element-' + this.getID(),
                    $selector = '';

                if (this.settings('selector') === 'widgets') {
                    $selector = $widgetClassSelect + ' .elementor-widget-container';
                }
                if (this.settings('selector') === 'images') {
                    $selector = $widgetClassSelect + ' img';
                }
                if (this.settings('selector') === 'buttons') {
                    $selector = $widgetClassSelect + ' a';
                }
                if (this.settings('selector') === 'both') {
                    $selector = $widgetClassSelect + ' a,' + $widgetClassSelect + ' img';
                }
                if (this.settings('selector') === 'custom' && this.settings('custom_selector')) {
                    $selector = $widgetClassSelect + ' ' + this.settings('custom_selector');
                }

                if ('' === $selector ) {
                    return;
                }

                $(document).on('click', '[href="#"]', function (e) { e.preventDefault(); });
                if (this.settings('on')) {
                    options.on = this.settings('on');
                }
                if (this.settings('easing')) {
                    options.easing = this.settings('easing');
                }
                if (this.settings('duration.size')) {
                    options.duration = this.settings('duration.size');
                }
                if (this.settings('opacity.size')) {
                    options.opacity = this.settings('opacity.size');
                }
                if (this.settings('color')) {
                    options.color = this.settings('color');
                }

                document.querySelectorAll($selector).forEach(function (el) {
                    if ('IMG' == el.tagName) {
                        var $image = $(el);
                        $image.wrap('<div id="bdt-ripple-effect-img-wrapper-' + $widgetId + '"></div>');
                        window.rippler = $.ripple('#bdt-ripple-effect-img-wrapper-' + $widgetId, options);
                    } else {
                        window.rippler = $.ripple($selector, options);
                    }
                });
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RippleEffects, {
                $element: $scope
            });
        });
    });

}(jQuery, window.elementorFrontend));