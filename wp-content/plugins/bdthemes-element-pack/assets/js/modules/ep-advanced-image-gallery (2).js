/**
 * Start bdt advanced image gallery widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetAdvancedImageGallery = function ($scope, $) {

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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-image-gallery.default', widgetAdvancedImageGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-advanced-image-gallery.bdt-carousel', widgetAdvancedImageGallery);
    });

}(jQuery, window.elementorFrontend));

/**
 * End bdt advanced image gallery widget script
 */ 