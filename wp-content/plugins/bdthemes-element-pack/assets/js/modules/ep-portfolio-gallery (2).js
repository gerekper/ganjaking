/**
 * Start portfolio gallery widget script
 */

(function ($, elementor) {
    'use strict';
    // PortfolioGallery
    var widgetPortfolioGallery = function ($scope, $) {
        var $portfolioGalleryWrapper = $scope.find('.bdt-portfolio-gallery-wrapper'),
            $settings = $portfolioGalleryWrapper.data('settings'),
            $portfolioFilter = $portfolioGalleryWrapper.find('.bdt-ep-grid-filters-wrapper');

        if (!$portfolioGalleryWrapper.length) {
            return;
        }

        if ($settings.tiltShow == true) {
            var elements = document.querySelectorAll($settings.id + " [data-tilt]");
            VanillaTilt.init(elements);
        }

        if (!$portfolioFilter.length) {
            return;
        }
        var $settings = $portfolioFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($portfolioFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($portfolioFilter).find('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').closest($portfolioFilter).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function () {
                        //#code
                    }).promise().then(function () {
                        $('[bdt-filter-control="[data-filter*=\'' + window.location.hash.substring(1) + '\']"]').trigger("click");
                    });
                }
            }
        }
        if ($settings.activeHash == 'yes') {
            $(window).on('load', function () {
                hashHandler($portfolioFilter, hashScrollspyTime = 1500, hashTopOffset);
            });
            $($portfolioFilter).find('.bdt-ep-grid-filter').off('click').on('click', function (event) {
                window.location.hash = ($.trim($(this).context.innerText.toLowerCase())).replace(/\s+/g, '-');
                // hashHandler( $portfolioFilter, hashScrollspyTime, hashTopOffset);
            });
            $(window).on('hashchange', function (e) {
                hashHandler($portfolioFilter, hashScrollspyTime, hashTopOffset);
            });
        }


        //filter item count
        var categories = {},
            category;

        var arr = [];
        var totalItem = 0;
        $($portfolioGalleryWrapper).find(".bdt-portfolio-gallery div[data-filter]").each(function (i, el) {
            category = $(el).data("filter");
            let list = category.split(/\s+/);
            $(list).each(function (i, el) {
                arr.push(el);
            });
            totalItem = totalItem + 1;
        });

        var counts = {};
        arr.forEach(function (x) {
            counts[x] = (counts[x] || 0) + 1;
        });
        //print total item result
        $($portfolioGalleryWrapper).find('.bdt-all-count').text(totalItem);
        // print results
        for (var key in counts) {
            $($portfolioGalleryWrapper).find('[data-bdt-target=' + key + '] .bdt-count').text(counts[key]);
        }


    };
    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.default', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-abetis', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-fedara', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-trosia', widgetPortfolioGallery);
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-portfolio-gallery.bdt-janes', widgetPortfolioGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End portfolio gallery widget script
 */