/**
 * Start honeycombs widget script
 */

(function($, elementor) {
    'use strict';
    var widgetHoneycombs = function($scope, $) {
        var $honeycombsArea = $scope.find('.bdt-honeycombs-area'),
        $honeycombs = $honeycombsArea.find('.bdt-honeycombs');
        if (!$honeycombsArea.length) {
            return;
        }
        var $settings = $honeycombs.data('settings');

        $($honeycombs).honeycombs({
            combWidth: $settings.width,
            margin: $settings.margin,
            threshold: 3,
            widthTablet: $settings.width_tablet,
            widthMobile : $settings.width_mobile,
            viewportLg : $settings.viewport_lg,
            viewportMd : $settings.viewport_md
        });

        //loaded class for better showing
        $($honeycombs).addClass('honeycombs-loaded');



    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-honeycombs.default', widgetHoneycombs);
    });
}(jQuery, window.elementorFrontend));

/**
 * End honeycombs widget script
 */

