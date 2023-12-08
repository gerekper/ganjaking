/**
 * Start logo carousel widget script
 */

(function($, elementor) {

    'use strict';

    var widgetLogoCarousel = function($scope, $) {

        var $logocarousel = $scope.find('.bdt-logo-carousel-wrapper');

        if (!$logocarousel.length) {
            return;
        }

        var $tooltip = $logocarousel.find('> .bdt-tippy-tooltip'),
            widgetID = $scope.data('id'); 

        $tooltip.each(function(index) {
            tippy(this, {
                allowHTML: true,
                theme: 'bdt-tippy-' + widgetID
            });
        });

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-logo-carousel.default', widgetLogoCarousel);
    });

}(jQuery, window.elementorFrontend));

/**
 * End logo carousel widget script
 */

