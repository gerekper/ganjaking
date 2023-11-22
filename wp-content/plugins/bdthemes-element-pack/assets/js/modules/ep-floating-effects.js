;(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base, FloatingEffect;

        FloatingEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    direction: 'alternate',
                    easing   : 'easeInOutSine',
                    loop     : true
                };
            },

            settings: function (key) {
                return this.getElementSettings('ep_floating_effects_' + key);
            },

            onElementChange: debounce(function (prop) {
                if ( prop.indexOf('ep_floating') !== -1 ) {
                    this.anime && this.anime.restart();
                    this.run();
                }
            }, 400),

            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.findElement('.elementor-widget-container').get(0);

                if ( this.settings('translate_toggle') ) {
                    if ( this.settings('translate_x.sizes.from').length !== 0 || this.settings('translate_x.sizes.to').length !== 0 ) {
                        options.translateX = {
                            value   : [this.settings('translate_x.sizes.from') || 0, this.settings('translate_x.sizes.to') || 0],
                            duration: this.settings('translate_duration.size'),
                            delay   : this.settings('translate_delay.size') || 0
                        };
                    }
                    // console.log(options);

                    if ( this.settings('translate_y.sizes.from').length !== 0 || this.settings('translate_y.sizes.to').length !== 0 ) {
                        options.translateY = {
                            value   : [this.settings('translate_y.sizes.from') || 0, this.settings('translate_y.sizes.to') || 0],
                            duration: this.settings('translate_duration.size'),
                            delay   : this.settings('translate_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('rotate_toggle') ) {
                    if ( this.settings('rotate_infinite') !== 'yes' ) {
                        if ( this.settings('rotate_x.sizes.from').length !== 0 || this.settings('rotate_x.sizes.to').length !== 0 ) {
                            options.rotateX = {
                                value   : [this.settings('rotate_x.sizes.from') || 0, this.settings('rotate_x.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay   : this.settings('rotate_delay.size') || 0
                            };
                        }
                        if ( this.settings('rotate_y.sizes.from').length !== 0 || this.settings('rotate_y.sizes.to').length !== 0 ) {
                            options.rotateY = {
                                value   : [this.settings('rotate_y.sizes.from') || 0, this.settings('rotate_y.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay   : this.settings('rotate_delay.size') || 0
                            };
                        }
                        if ( this.settings('rotate_z.sizes.from').length !== 0 || this.settings('rotate_z.sizes.to').length !== 0 ) {
                            options.rotateZ = {
                                value   : [this.settings('rotate_z.sizes.from') || 0, this.settings('rotate_z.sizes.to') || 0],
                                duration: this.settings('rotate_duration.size'),
                                delay   : this.settings('rotate_delay.size') || 0
                            };
                        }
                    }
                }

                if ( this.settings('scale_toggle') ) {
                    if ( this.settings('scale_x.sizes.from').length !== 0 || this.settings('scale_x.sizes.to').length !== 0 ) {
                        options.scaleX = {
                            value   : [this.settings('scale_x.sizes.from') || 0, this.settings('scale_x.sizes.to') || 0],
                            duration: this.settings('scale_duration.size'),
                            delay   : this.settings('scale_delay.size') || 0
                        };
                    }
                    if ( this.settings('scale_y.sizes.from').length !== 0 || this.settings('scale_y.sizes.to').length !== 0 ) {
                        options.scaleY = {
                            value   : [this.settings('scale_y.sizes.from') || 0, this.settings('scale_y.sizes.to') || 0],
                            duration: this.settings('scale_duration.size'),
                            delay   : this.settings('scale_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('skew_toggle') ) {
                    if ( this.settings('skew_x.sizes.from').length !== 0 || this.settings('skew_x.sizes.to').length !== 0 ) {
                        options.skewX = {
                            value   : [this.settings('skew_x.sizes.from') || 0, this.settings('skew_x.sizes.to') || 0],
                            duration: this.settings('skew_duration.size'),
                            delay   : this.settings('skew_delay.size') || 0
                        };
                    }
                    if ( this.settings('skew_y.sizes.from').length !== 0 || this.settings('skew_y.sizes.to').length !== 0 ) {
                        options.skewY = {
                            value   : [this.settings('skew_y.sizes.from') || 0, this.settings('skew_y.sizes.to') || 0],
                            duration: this.settings('skew_duration.size'),
                            delay   : this.settings('skew_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('border_radius_toggle') ) {
                    jQuery(element).css('overflow', 'hidden');
                    if ( this.settings('border_radius.sizes.from').length !== 0 || this.settings('border_radius.sizes.to').length !== 0 ) {
                        options.borderRadius = {
                            value   : [this.settings('border_radius.sizes.from') || 0, this.settings('border_radius.sizes.to') || 0],
                            duration: this.settings('border_radius_duration.size'),
                            delay   : this.settings('border_radius_delay.size') || 0
                        };
                    }
                }

                if ( this.settings('opacity_toggle') ) {
                    if ( this.settings('opacity_start.size').length !== 0 || this.settings('opacity_end.size').length !== 0 ) {
                        options.opacity = {
                            value   : [this.settings('opacity_start.size') || 1, this.settings('opacity_end.size') || 0],
                            duration: this.settings('opacity_duration.size'),
                            easing  : 'linear'
                        };
                    }
                }

                if ( this.settings('easing') ) {
                    options.easing = this.settings('easing');
                }


                if ( this.settings('show') ) {
                    options.targets = element;
                    if (
                        this.settings('translate_toggle') ||
                        this.settings('rotate_toggle') ||
                        this.settings('scale_toggle') ||
                        this.settings('skew_toggle') ||
                        this.settings('border_radius_toggle') ||
                        this.settings('opacity_toggle')
                    ) {
                        this.anime = window.anime && window.anime(options);
                    }
                }

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(FloatingEffect, {
                $element: $scope
            });
        });
    });
}(jQuery, window.elementorFrontend));