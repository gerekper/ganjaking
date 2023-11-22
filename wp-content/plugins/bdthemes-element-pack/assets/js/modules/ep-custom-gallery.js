/**
 * Start bdt custom gallery widget script
 */

(function($, elementor) {

    'use strict';

    var widgetCustomGallery = function($scope, $) {

        var $customGallery = $scope.find('.bdt-custom-gallery'),
            $settings 	= $customGallery.data('settings');
          
        if (!$customGallery.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.default', widgetCustomGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.bdt-abetis', widgetCustomGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-custom-gallery.bdt-fedara', widgetCustomGallery);
    });

}(jQuery, window.elementorFrontend));

/**
 * End bdt custom gallery widget script
 */

