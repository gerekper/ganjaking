/**
 * Start advanced icon box widget script
 */

(function($, elementor) {

    'use strict';

    // Accordion
    var widgetAdvancedIconBox = function($scope, $) {

        var $avdDivider = $scope.find('.bdt-ep-advanced-icon-box'),
            divider = $($avdDivider).find('.bdt-ep-advanced-icon-box-separator-wrap > img');

        if (!$avdDivider.length && !divider.length) {
            return;
        }

        elementorFrontend.waypoint(divider, function() {
            bdtUIkit.svg(this, {
                strokeAnimation: true
            });
        }, {
            offset: 'bottom-in-view'
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-icon-box.default', widgetAdvancedIconBox);
    });

}(jQuery, window.elementorFrontend));

/**
 * End advanced icon box widget script
 */

