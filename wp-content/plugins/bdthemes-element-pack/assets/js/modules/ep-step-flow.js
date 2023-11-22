/**
 * Start step flow widget script
 */

(function($, elementor) {

    'use strict';

    // Accordion
    var widgetStepFlow = function($scope, $) {

        var $avdDivider = $scope.find('.bdt-step-flow'),
            divider = $($avdDivider).find('.bdt-title-separator-wrapper > img');

        if (!$avdDivider.length) {
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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-step-flow.default', widgetStepFlow);
    });

}(jQuery, window.elementorFrontend));

/**
 * End step flow widget script
 */

