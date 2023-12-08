/**
 * Start advanced divider widget script
 */

;(function($, elementor) {

    'use strict';

    // Accordion
    var widgetAdvancedDivider = function($scope, $) {

        var $avdDivider = $scope.find('.bdt-ep-advanced-divider'),
            $settings 	= $avdDivider.data('settings');

          
        if (!$avdDivider.length) {
            return;
        }

        if ($settings.animation === true) {
            elementorFrontend.waypoint($avdDivider, function() {
                var $divider = $(this).find('img');
                bdtUIkit.svg( $divider, {
                    strokeAnimation : true,
                });
            }, {
                offset: 'bottom-in-view',
                triggerOnce: (!$settings.loop)
            } );
        } else {
            var $divider = $($avdDivider).find('img');
            bdtUIkit.svg( $divider );
        }


    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-divider.default', widgetAdvancedDivider);
    });

}(jQuery, window.elementorFrontend));

/**
 * End advanced divider widget script
 */

