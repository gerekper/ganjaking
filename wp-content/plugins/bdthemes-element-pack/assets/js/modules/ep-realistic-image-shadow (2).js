;
(function ($, elementor) {
    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base,
            RealisticShadow;

        RealisticShadow = ModuleHandler.extend({

            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    allowHTML: true,
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('element_pack_ris_') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('element_pack_ris_' + key);
            },

            run: function () {
                var options = this.getDefaultSettings();
                var widgetID = this.$element.data('id');
                var widgetContainer = $('.elementor-element-' + widgetID + ' .elementor-widget-container');
                var obj = this;

                if ('yes' !== this.settings('enable')) {
                    return;
                }

                if (this.settings('selector')) {
                    widgetContainer = $('.elementor-element-' + widgetID).find(this.settings('selector'));
                }

                var $image = widgetContainer.find('img');

                $image.each(function () {
                    var $this = $(this);
                    if (!$this.hasClass('element-pack-ris-image')) {
                        var $duplicateImage = $this.clone();
                        $duplicateImage.addClass('element-pack-ris-image');
                        

                        // Remove any existing 'element-pack-ris-image' elements except the first one
                        var $existingImages = $($this).parent().find('.element-pack-ris-image');
                        if ($existingImages.length > 1) {
                            $existingImages.not(':first').remove();
                        }

                        if ($existingImages.length < 1) {
                            $($this).parent().append($duplicateImage);
                        }

                        // Add class to parent
                        widgetContainer.addClass('bdt-realistic-image-shadow');

                        if (obj.settings('on_hover') === 'yes') {
                            widgetContainer.addClass('bdt-hover');
                        }
                    }
                });

            }
        });

        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
            elementorFrontend.elementsHandler.addHandler(RealisticShadow, {
                $element: $scope
            });
        });
    });
})(jQuery, window.elementorFrontend);