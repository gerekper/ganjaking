; (function ($, elementor) {
$(window).on('elementor/frontend/init', function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
        ThreedText;

    ThreedText = ModuleHandler.extend({

        bindEvents: function () {
            this.run();
        },

        getDefaultSettings: function () {
            return {
                depth: '30px',
                layers: 8,
            };
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('ep_threed_text_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings('ep_threed_text_' + key);
        },

        run: function () {
            var options = this.getDefaultSettings(),
                $element = this.findElement('.elementor-heading-title, .bdt-main-heading-inner'),
                $widgetId = 'ep-' + this.getID(),
                $widgetIdSelect = '#' + $widgetId;

            jQuery($element).attr('id', $widgetId);

            if (this.settings('depth.size')) {
                options.depth = this.settings('depth.size') + this.settings('depth.unit') || '30px';
            }
            if (this.settings('layers')) {
                options.layers = this.settings('layers') || 8;
            }
            if (this.settings('perspective.size')) {
                options.perspective = this.settings('perspective.size') + 'px' || '500px';
            }
            if (this.settings('fade')) {
                options.fade = !!this.settings('fade');
            }
            // if (this.settings('direction')) {
            //     options.direction = this.settings('direction') || 'forwards';
            // }
            if (this.settings('event')) {
                options.event = this.settings('event') || 'pointer';
            }
            if (this.settings('event_rotation') && this.settings('event') != 'none') {
                options.eventRotation = this.settings('event_rotation.size') + 'deg' || '35deg';
            }
            if (this.settings('event_direction') && this.settings('event') != 'none') {
                options.eventDirection = this.settings('event_direction') || 'default';
            }

            if (this.settings('active') == 'yes') {

                var $text = $($widgetIdSelect).html();
                $($widgetIdSelect).parent().append('<div class="ep-z-text-duplicate" style="display:none;">' + $text + '</div>');

                $text = $($widgetIdSelect).parent().find('.ep-z-text-duplicate:first').html();

                $($widgetIdSelect).find('.z-text').remove();

                var ztxt = new Ztextify($widgetIdSelect, options, $text);
            }

            if (this.settings('depth_color')) {
                var depthColor = this.settings('depth_color') || '#fafafa';
                $($widgetIdSelect).find('.z-layers .z-layer:not(:first-child)').css('color', depthColor);
            }

            // if (this.settings('bg_color')) {
            //     var bgColor = this.settings('bg_color') || 'rgba(96, 125, 139, .5)';
            //     $($widgetIdSelect).find('.z-text > .z-layers').css('background', bgColor);
            // }

        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(ThreedText, {
            $element: $scope
        });
    });

});
}) (jQuery, window.elementorFrontend);