; (function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base, epSVGImage;
        epSVGImage = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {

                };
            },
            onElementChange: debounce(function (prop) {
                if (prop.indexOf('svg_image_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('svg_image_' + key);
            },

            run: function () {
                gsap.registerPlugin(DrawSVGPlugin, ScrollTrigger);
                var options = this.getDefaultSettings(),
                    widgetID = this.$element.data('id'),
                    element = this.findElement('.elementor-widget-container').get(0),
                    scrollTrigger = null;

                if (jQuery(this.$element).hasClass('elementor-section')) {
                    element = this.$element.get(0);
                }
                var $container = this.$element.find(".bdt-svg-image");
                if (!$container.length) {
                    return;
                }

                if ('yes' !== this.settings('draw')) {
                    return;
                }

                var shapes = $container.find("path, circle, rect, square, ellipse, polyline, line, polygon");
                const drawerType = this.settings('drawer_type');
                options.repeat = (this.settings('repeat') === 'yes') ? -1 : 0;
                options.yoyo = (this.settings('yoyo') === 'yes') ? true : false;

                if ('automatic' === drawerType) {
                    var reverseAnimation = this.settings('anim_rev') ? 'pause play reverse' : 'none',
                        triggerAnimation = 'custom' !== this.settings('animate_trigger') ? this.settings('animate_trigger') : this.settings('animate_offset.size') + "%";
                    options.scrollTrigger = {
                        trigger: '.elementor-element-' + widgetID,
                        toggleActions: "play " + reverseAnimation,
                        start: "top " + triggerAnimation
                    }
                    var timeLine = gsap.timeline(options);
                }

                if ('hover' === drawerType) {
                    var timeLine = gsap.timeline(options);
                    timeLine.pause();
                    $container.find("svg").hover(
                        function () {
                            timeLine.play();
                        },
                        function () {
                            timeLine.pause();
                        });


                }
                if ('viewport' === drawerType) {
                    var timeLine = gsap.timeline({
                        repeat: 1,
                        yoyo: true
                    });
                    scrollTrigger = (this.settings('animate_offset.size') / 100);
                    var controller = new ScrollMagic.Controller(),
                        scene = new ScrollMagic.Scene({
                            triggerElement: '.elementor-element-' + widgetID,
                            triggerHook: scrollTrigger,
                            duration: 0.6 * 1000
                        })
                    scene.setTween(timeLine).addTo(controller);
                }
                if ('viewport' === drawerType) {
                    timeLine.fromTo(shapes, { drawSVG: "0%" }, { duration: 200, drawSVG: "100%", stagger: 0.1 });
                } else {
                    timeLine.fromTo(shapes, { drawSVG: this.settings('animation_start_point.size') + "%" }, { duration: (this.settings('animation_duration.size') / 100), drawSVG: this.settings('animation_end_point.size') + "%", stagger: 0.1 });
                }
            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-svg-image.default',
            function ($scope) {
                elementorFrontend.elementsHandler.addHandler(epSVGImage, {
                    $element: $scope
                });
            }
        );
    });

})(jQuery, window.elementorFrontend);