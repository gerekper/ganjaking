;(function($, elementor){
    'use strict';

    $(window).on('elementor/frontend/init', function () {

        var ModuleHandler = elementorModules.frontend.handlers.Base, BarCode;


        BarCode = ModuleHandler.extend({
            bindEvents: function () {
                this.run();
            },

            getDefaultSettings: function () {
                return {
                    format: 'code128'
                };
            },

            onElementChange: debounce(function (prop) {
                if (prop.indexOf('ep_barcode') !== -1) {
                    this.run();
                }
            }, 400),

            settings: function (key) {
                return this.getElementSettings('ep_barcode_' + key);
            },

            run: function () {

                var options = this.getDefaultSettings();
                var element = this.findElement('.elementor-widget-container').get(0);
                if (jQuery(this.$element).hasClass('elementor-section')) {
                    element = this.$element.get(0);
                }
                var $container = this.$element.find(".bdt-ep-barcode");
                if (!$container.length) {
                    return;
                }

                var content = this.settings('content');
                options.displayValue = (this.settings('show_label') === 'yes');
                options.format = this.settings('format');
                options.text = (this.settings('label_text')) ? this.settings('label_text') : '';
                options.width = (this.settings('width.size')) ? this.settings('width.size') : 2;
                options.height = (this.settings('height.size')) ? this.settings('height.size') : 40;
                options.fontOptions = (this.settings('font_width')) ? this.settings('font_width') : 'normal';
                options.textAlign = (this.settings('label_alignment')) ? this.settings('label_alignment') : 'center';
                options.textPosition = (this.settings('label_position')) ? this.settings('label_position') : 'bottom';
                options.textMargin = (this.settings('label_spacing.size')) ? this.settings('label_spacing.size') : 2;
                options.margin = 0;

                JsBarcode('#bdt-ep-barcode-' + this.$element.data('id'), content, options);

            }
        });


        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-barcode.default',
            function ($scope) {
                elementorFrontend.elementsHandler.addHandler(BarCode, {
                    $element: $scope
                });
            }
        );
    });

})(jQuery, window.elementorFrontend);