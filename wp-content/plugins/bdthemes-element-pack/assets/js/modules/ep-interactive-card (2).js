/**
 * Start interactive card widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetInteractiveCard = function ($scope, $) {
        var $i_card_main = $scope.find('.bdt-interactive-card');

        if ( !$i_card_main.length ) {
            return;
        }
        var $settings = $i_card_main.data('settings');

        if ( $($settings).length ) {
            var myWave = wavify(document.querySelector('#' + $settings.id), {
                height   : 60,
                bones    : $settings.wave_bones, //3
                amplitude: $settings.wave_amplitude, //40
                speed    : $settings.wave_speed //.25
            });

            setTimeout(function(){
                $($i_card_main).addClass('bdt-wavify-active');
            }, 1000);
        }
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-interactive-card.default', widgetInteractiveCard);
    });

}(jQuery, window.elementorFrontend));

/**
 * End interactive card widget script
 */

