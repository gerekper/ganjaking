(function ($, elementor) {

    'use strict';

    $(window).on('elementor/frontend/init', function ($) {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            Confetti;

        Confetti = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    resize: true,
                    useWorker: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_widget_cf_') !== -1) {
                    //  this.instance.reset();
                    this.run();

                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_widget_cf_' + key);
            },
            randomInRange: function (min, max) {
                return Math.random() * (max - min) + min;
            },
            run: function () {
                var options = this.getDefaultSettings(),
                    $element = this.$element;

                if (this.settings('confetti') !== 'yes') {
                    return;
                }

                if (this.settings('z_index')) {
                    options.zIndex = this.settings('z_index');
                }
                if (this.settings('particle_count.size')) {
                    options.particleCount = this.settings('particle_count.size') || 100;
                }
                if (this.settings('start_velocity.size')) {
                    options.startVelocity = this.settings('start_velocity.size') || 45;
                }

                if (this.settings('spread.size')) {
                    options.spread = this.settings('spread.size') || 70;
                }
                if (this.settings('colors')) {
                    var colors = this.settings('colors');
                    options.colors = colors.split(',');
                }
                if (this.settings('shapes')) {
                    var shapes = this.settings('shapes');
                    options.shapes = shapes.split(/,|\|/);
                }

                if ('emoji' == this.settings('shape_type') && this.settings('shapes_emoji')) {
                    var shapes = this.settings('shapes_emoji');
                    let __shapes = [];

                    let shapesArray = shapes.split(/,|\|/);
                    shapesArray.forEach(function (shape, i) {
                        __shapes[i] = confetti.shapeFromText({
                            text: shape,
                        });
                    });
                    options.shapes = __shapes;
                }

                if ('svg' == this.settings('shape_type') && this.settings('shapes_svg')) {
                    let shapes = this.settings('shapes_svg');
                    let __path = [];
                    let shapesArray = shapes.split('|');

                    shapesArray.forEach(function (shape, i) {
                        __path[i] = confetti.shapeFromPath({
                            path: shape,
                            matrix: [0.03597122302158273, 0, 0, 0.03597122302158273, -4.856115107913669, -5.071942446043165]
                        });
                    });

                    options.shapes = __path;
                }


                if (this.settings('scalar.size')) {
                    options.scalar = this.settings('scalar.size') || 1;
                }

                if (this.settings('origin')) {
                    if (this.settings('origin_x.size') || this.settings('origin_y.size')) {
                        options.origin = {
                            x: this.settings('origin_x.size') || 0.5,
                            y: this.settings('origin_y.size') || 0.6
                        }
                    }
                }

                if (this.settings('angle.size')) {
                    options.angle = this.settings('angle.size') || 90;
                }

                var this_instance = this;
                var instanceConfetti = {
                    executeConfetti: function () {
                        if (this_instance.settings('type') == 'random') {
                            options.angle = this_instance.randomInRange(55, this_instance.settings('angle.size') || 90);
                            options.spread = this_instance.randomInRange(50, this_instance.settings('spread.size') || 70);
                            options.particleCount = this_instance.randomInRange(55, this_instance.settings('particle_count.size') || 100);
                        }
                        if (this_instance.settings('type') == 'fireworks') {
                            var duration = this_instance.settings('fireworks_duration.size') || 1500;
                            var animationEnd = Date.now() + duration;
                            var defaults = {
                                startVelocity: this_instance.settings('start_velocity.size') || 30,
                                spread: this_instance.settings('spread.size') || 360,
                                shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle', 'circle', 'square'],
                                ticks: 60,
                                zIndex: this_instance.settings('z_index') || 0
                            };

                            var interval = setInterval(function () {
                                var timeLeft = animationEnd - Date.now();

                                if (timeLeft <= 0) {
                                    return clearInterval(interval);
                                }

                                var particleCount = 50 * (timeLeft / duration);
                                // since particles fall down, start a bit higher than random
                                confetti(Object.assign({}, defaults, {
                                    particleCount,
                                    origin: {
                                        x: this_instance.randomInRange(0.1, 0.3),
                                        y: Math.random() - 0.2
                                    }
                                }));
                                confetti(Object.assign({}, defaults, {
                                    particleCount,
                                    origin: {
                                        x: this_instance.randomInRange(0.7, 0.9),
                                        y: Math.random() - 0.2
                                    }
                                }));
                            }, 250);
                        }

                        if (this_instance.settings('type') == 'school-pride') {
                            var duration = this_instance.settings('fireworks_duration.size') || 1500;
                            var end = Date.now() + (duration);

                            (function frame() {
                                confetti({
                                    particleCount: this_instance.settings('particle_count.size') || 2,
                                    angle: this_instance.settings('angle.size') || 60,
                                    spread: this_instance.settings('spread.size') || 55,
                                    shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle', 'circle', 'square'],
                                    origin: {
                                        x: 0
                                    },
                                    colors: colors.split(',')
                                });
                                confetti({
                                    particleCount: this_instance.settings('particle_count.size') || 2,
                                    angle: (this_instance.settings('angle.size') || 60) * 2, //120
                                    spread: this_instance.settings('spread.size') || 55,
                                    shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle', 'circle', 'square'],
                                    origin: {
                                        x: 1
                                    },
                                    colors: colors.split(',')
                                });

                                if (Date.now() < end) {
                                    requestAnimationFrame(frame);
                                }
                            }());
                        }

                        if (this_instance.settings('type') == 'snow') {
                            var duration = this_instance.settings('fireworks_duration.size') || 1500;
                            /**
                             * Infinite Animation Time
                             * Yes & Not in Editor
                             */
                            if ('yes' == this_instance.settings('anim_infinite') && false == Boolean(elementorFrontend.isEditMode())) {
                                duration = 24 * 60 * 60 * 1000;
                            }
                            var animationEnd = Date.now() + duration;
                            var skew = 1;

                            (function frame() {
                                var timeLeft = animationEnd - Date.now();
                                var ticks = Math.max(200, 500 * (timeLeft / duration));
                                skew = Math.max(0.8, skew - 0.001);

                                confetti({
                                    particleCount: this_instance.settings('particle_count.size') || 1,
                                    startVelocity: this_instance.settings('start_velocity.size') || 0,
                                    ticks: ticks,
                                    origin: {
                                        x: Math.random(),
                                        // since particles fall down, skew start toward the top
                                        y: (Math.random() * skew) - 0.2
                                    },
                                    colors: colors.split(','),
                                    shapes: this_instance.settings('shapes') ? shapes.split(',') : ['circle'],
                                    gravity: this_instance.randomInRange(0.4, 0.6),
                                    scalar: this_instance.randomInRange(0.4, 1),
                                    drift: this_instance.randomInRange(-0.4, 0.4)
                                });

                                if (timeLeft > 0) {
                                    requestAnimationFrame(frame);
                                }
                            }());

                            setInterval(function () {
                                // instanceConfetti.executeConfetti();
                            }, 5000);
                        }

                        if ((this_instance.settings('type') == 'basic') ||
                            (this_instance.settings('type') == 'random')) {
                            this_instance.instance = confetti(options);

                        }
                    }
                };

                if (this.settings('confetti') == 'yes') {

                    if ((this.settings('trigger_type') == 'click')) {
                        jQuery(this.settings('trigger_selector')).on('click', function () {
                            instanceConfetti.executeConfetti();
                            //  $(this).unbind('mouseenter mouseleave');
                        });
                    } else if (this.settings('trigger_type') == 'mouseenter') {
                        jQuery(this.settings('trigger_selector')).on('mouseenter', function () {
                            instanceConfetti.executeConfetti();
                            //  $(this).unbind('mouseenter mouseleave');
                        });
                    } else if (this.settings('trigger_type') == 'ajax-success') {
                        jQuery(document).ajaxComplete(function (event, jqxhr, settings) {
                            instanceConfetti.executeConfetti();
                        });
                    } else if (this.settings('trigger_type') == 'delay') {
                        setTimeout(function () {
                            instanceConfetti.executeConfetti();
                        }, this.settings('trigger_delay.size') ? this.settings('trigger_delay.size') : 1000);
                    } else if (this.settings('trigger_type') == 'onview') {
                        elementorFrontend.waypoint($element, function () {
                            instanceConfetti.executeConfetti();
                        }, {
                            // offset: 'bottom-in-view',
                            offset: '80%'
                        });
                    } else {
                        instanceConfetti.executeConfetti();
                    }

                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Confetti, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Confetti, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(Confetti, {
                $element: $scope
            });
        });

    });

}(jQuery, window.elementorFrontend));