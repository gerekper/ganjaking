/**
 * Start Post Gallery widget script
 */

(function ($, elementor) {
    'use strict';

    var widgetPostGallery = function ($scope, $) {
        var $postGalleryWrapper = $scope.find('.bdt-post-gallery-wrapper'),
            $bdtPostGallery = $scope.find('.bdt-post-gallery'),
            $settings = $bdtPostGallery.data('settings'),
            $postFilter = $postGalleryWrapper.find('.bdt-ep-grid-filters-wrapper');

        if (!$postGalleryWrapper.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

        if (!$postFilter.length) {
            return;
        }
        var $settings = $postFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($postFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($postFilter).find('[data-bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[data-bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').closest($postFilter).attr('id');

                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function () {
                        //#code
                    }).promise().then(function () {
                        bdtUIkit.filter($postGalleryWrapper, {
                            target: $settings.id,
                            selActive: document.querySelector('[data-bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]'),
                        });
                    });
                }
            }
        }

        if ($settings.activeHash == true) {
            $(window).on('load', function () {
                hashHandler($postFilter, hashScrollspyTime = 1500, hashTopOffset);
            });

            $($postFilter).find('.bdt-ep-grid-filter').off('click').on('click', function (event) {
                window.location.hash = $(this).find('a').text().replace(/\s+/g, '-').toLowerCase();
            });
        }
    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.default', widgetPostGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.bdt-abetis', widgetPostGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.bdt-fedara', widgetPostGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-post-gallery.bdt-trosia', widgetPostGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End Post Gallery widget script
 */