;
(function ($, elementor) {
    'use strict';
    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base,
            BackgroundExpand;

        BackgroundExpand = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    direction: 'alternate',
                };
            },

            settings: function (key) {
                return this.getElementSettings('ep_bg_expand_' + key);
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_bg_expand_') !== -1) {
                    this.run();
                }
            }, 400),

            run: function () {
                var options = this.getDefaultSettings(),
                    element = this.$element.get(0);

                if ('yes' !== this.settings('enable')) {
                    return;
                }

                if (this.settings('selector')) {
                    element = this.settings('selector');
                }

                function initClass(e) {
                    $(element).addClass(e);
                }

                function terminateClass(e) {
                    $(element).removeClass(e);
                }

                var tl = gsap.timeline({
                    scrollTrigger: {
                        // markers      : true,
                        trigger      : $(element),
                        start        : "top center",
                        end          : '100% bottom',
                        toggleActions: "restart none none reverse",
                        onEnter      : () => initClass("bdt-bx-active"),
                        onEnterBack  : () => terminateClass("bdt-bx-active"),
                    }
                });

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/section', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(BackgroundExpand, {
                $element: $scope
            });
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/container', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(BackgroundExpand, {
                $element: $scope
            });
        });

    });
}(jQuery, window.elementorFrontend));