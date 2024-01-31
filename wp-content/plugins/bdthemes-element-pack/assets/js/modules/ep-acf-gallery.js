/**
 * Start ACF Gallery widget script ( Duplicate of Advanced Image Gallery widget script )
 */

(function ($, elementor) {

    'use strict';

    var widgetAcfGallery = function ($scope, $) {

        var $advancedImageGallery = $scope.find('.bdt-ep-advanced-image-gallery'),
            $settings = $advancedImageGallery.data('settings');

        if (!$advancedImageGallery.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-acf-gallery.default', widgetAcfGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-acf-gallery.bdt-carousel', widgetAcfGallery);
    });

}(jQuery, window.elementorFrontend));

/**
 * End ACF Gallery widget script
 */ 