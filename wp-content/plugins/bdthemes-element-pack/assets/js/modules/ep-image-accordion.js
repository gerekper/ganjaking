/**
 * Start image accordion widget script
 */

(function($, elementor) {

    'use strict';

    var widgetImageAccordion = function($scope, $) {

        var $imageAccordion = $scope.find('.bdt-ep-image-accordion'),
            $settings = $imageAccordion.data('settings');

        var accordionItem = $imageAccordion.find('.bdt-ep-image-accordion-item');
        var totalItems = $imageAccordion.children().length;

        if (($settings.activeItem == true) && ($settings.activeItemNumber <= totalItems)) {
            $imageAccordion.find('.bdt-ep-image-accordion-item').removeClass('active');
            $imageAccordion.children().eq($settings.activeItemNumber - 1).addClass('active');
        }


        $(accordionItem).on($settings.mouse_event, function() {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });

        if ($settings.activeItem != true) {
            $("body").on($settings.mouse_event, function(e) {
                if (e.target.$imageAccordion == "bdt-ep-image-accordion" || $(e.target).closest(".bdt-ep-image-accordion").length) {} else {
                    $imageAccordion.find('.bdt-ep-image-accordion-item').removeClass('active');
                }
            });
        }
    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-image-accordion.default', widgetImageAccordion);
    });

}(jQuery, window.elementorFrontend));

/**
 * End image accordion widget script
 */