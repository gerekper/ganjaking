;(function ($, elementor) {
    'use strict';

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            ScrollingEffect;

        ScrollingEffect = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    media   : false,
                    easing  : 1,
                    viewport: 1,
                };
            },

            onElementChange: debounce(function (prop) {
                if ( prop.indexOf('ep_parallax_effects') !== -1 ) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_parallax_effects_' + key);
            },

            run: function () {
                var options   = this.getDefaultSettings(),
                    element   = this.findElement('.elementor-widget-container').get(0);

                if ( jQuery(this.$element).hasClass('elementor-section') ) {
                    element = this.$element.get(0);
                }

                if ( this.settings('y') ) {
                    if (this.settings('y_custom_show')) {
                        options.y = this.settings('y_custom_value');
                    } else {
                        if ( this.settings('y_start.size') || this.settings('y_end.size') ) {
                            options.y = [this.settings('y_start.size') || 0, this.settings('y_end.size') || 0];

                        }
                    }
                }

                if ( this.settings('x') ) {
                    if (this.settings('x_custom_show')) {
                        options.x = this.settings('x_custom_value');
                    } else {
                        if ( this.settings('x_start.size') || this.settings('x_end.size') ) {
                            options.x = [this.settings('x_start.size'), this.settings('x_end.size')];
                        }
                    }
                }

                if ( this.settings('opacity_toggole') ) {
                    if (this.settings('opacity_custom_show')) {
                        options.opacity = this.settings('opacity_custom_value');
                    } else {
                        if ( 'htov' === this.settings('opacity') ) {
                            options.opacity = [0, 1];
                        } else if ( 'vtoh' === this.settings('opacity') ) {
                            options.opacity = [1, 0];
                        }
                    }
                }

                if ( this.settings('blur') ) {
                    if ( this.settings('blur_start.size') || this.settings('blur_end.size') ) {
                        options.blur = [this.settings('blur_start.size') || 0, this.settings('blur_end.size') || 0];
                    }
                }

                if ( this.settings('rotate') ) {
                    if ( this.settings('rotate_start.size') || this.settings('rotate_end.size') ) {
                        options.rotate = [this.settings('rotate_start.size') || 0, this.settings('rotate_end.size') || 0];
                    }
                }

                if ( this.settings('scale') ) {
                    if ( this.settings('scale_start.size') || this.settings('scale_end.size') ) {
                        options.scale = [this.settings('scale_start.size') || 1, this.settings('scale_end.size') || 1];
                    }
                }

                if ( this.settings('hue') ) {
                    if ( this.settings('hue_value.size') ) {
                        options.hue = this.settings('hue_value.size');
                    }
                }

                if ( this.settings('sepia') ) {
                    if ( this.settings('sepia_value.size') ) {
                        options.sepia = this.settings('sepia_value.size');
                    }
                }

                if ( this.settings('viewport') ) {
                    if ( this.settings('viewport_start') ) {
                        options.start = this.settings('viewport_start');
                    }
                }

                if ( this.settings('viewport') ) {
                    if ( this.settings('viewport_end') ) {
                        options.end = this.settings('viewport_end');
                    }
                }

                if ( this.settings('media_query') ) {
                    if ( this.settings('media_query') ) {
                        options.media = this.settings('media_query');
                    }
                }

                if ( this.settings('easing') ) {
                    if ( this.settings('easing_value.size') ) {
                        options.easing = this.settings('easing_value.size');
                    }
                }

                if ( this.settings('target') ) {
                    if ( this.settings('target') === 'section' ) {
                        options.target = '.elementor-section.elementor-element-' + jQuery(element).closest('section').data('id');
                    }
                }


                if ( this.settings('show') ) {
                    if (
                        this.settings('y') ||
                        this.settings('x') ||
                        this.settings('opacity') ||
                        this.settings('blur') ||
                        this.settings('rotate') ||
                        this.settings('scale') ||
                        this.settings('hue') ||
                        this.settings('sepia') ||
                        this.settings('viewport') ||
                        this.settings('media_query') ||
                        this.settings('easing') ||
                        this.settings('target')
                    ) {
                        bdtUIkit.parallax(element, options);
                    }
                }

            }
        });

        //console.log($(this.$element).hasClass("elementor-section"));

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollingEffect, { $element: $scope });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(ScrollingEffect, { $element: $scope });
        });
    });
}(jQuery, window.elementorFrontend));