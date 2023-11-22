/**
 * Start faq widget script
 */

(function($, elementor) {
    'use strict';
    var widgetPostGallery = function($scope, $) {
        var $faqWrapper = $scope.find('.bdt-faq-wrapper'),
            $faqFilter = $faqWrapper.find('.bdt-ep-grid-filters-wrapper');
        if (!$faqFilter.length) {
            return;
        }
        var $settings = $faqFilter.data('hash-settings');
        var activeHash = $settings.activeHash;
        var hashTopOffset = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;

        function hashHandler($faqFilter, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($faqFilter).find('[bdt-filter-control="[data-filter*=\'bdtf-' + window.location.hash.substring(1) + '\']"]').length) {
                    var hashTarget = $('[bdt-filter-control="[data-filter*=\'bdtf-' + window.location.hash.substring(1) + '\']"]').closest($faqFilter).attr('id');
                    $('html, body').animate({
                        easing: 'slow',
                        scrollTop: $('#' + hashTarget).offset().top - hashTopOffset
                    }, hashScrollspyTime, function() {
                        //#code
                    }).promise().then(function() {
                        $('[bdt-filter-control="[data-filter*=\'bdtf-' + window.location.hash.substring(1) + '\']"]').trigger("click");
                    });
                }
            }
        }

        if ($settings.activeHash == 'yes') {
            $(window).on('load', function() {
                hashHandler($faqFilter, hashScrollspyTime = 1500, hashTopOffset);
            });
            $($faqFilter).find('.bdt-ep-grid-filter').off('click').on('click', function(event) {
                window.location.hash = ($.trim($(this).context.innerText.toLowerCase())).replace(/\s+/g, '-');
                // hashHandler( $faqFilter, hashScrollspyTime, hashTopOffset);
            });
            $(window).on('hashchange', function(e) {
                hashHandler($faqFilter, hashScrollspyTime, hashTopOffset);
            });
        }
    };
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-faq.default', widgetPostGallery);
    });
}(jQuery, window.elementorFrontend));

/**
 * End faq widget script
 */

