/**
 * Start accordion widget script
 */

;(function($, elementor) {
    'use strict';
    var widgetAccordion = function($scope, $) {
        var $accrContainer = $scope.find('.bdt-ep-accordion-container'),
        $accordion = $accrContainer.find('.bdt-ep-accordion');
        if (!$accrContainer.length) {
            return;
        }
        var $settings         = $accordion.data('settings');
        var activeHash        = $settings.activeHash;
        var hashTopOffset     = $settings.hashTopOffset;
        var hashScrollspyTime = $settings.hashScrollspyTime;
        var activeScrollspy   = $settings.activeScrollspy;

        if (activeScrollspy === null || typeof activeScrollspy === 'undefined'){
            activeScrollspy = 'no';
        }

        function hashHandler($accordion, hashScrollspyTime, hashTopOffset) {
            if (window.location.hash) {
                if ($($accordion).find('[data-title="' + window.location.hash.substring(1) + '"]').length) {
                        var hashTarget = $('[data-title="' + window.location.hash.substring(1) + '"]')
                        .closest($accordion)
                        .attr('id');

                        if(activeScrollspy == 'yes'){
                            $('html, body').animate({
                                easing    : 'slow',
                                scrollTop : $('#'+hashTarget).offset().top - hashTopOffset
                            }, hashScrollspyTime, function() {
                                }).promise().then(function() {
                                    bdtUIkit.accordion($accordion).toggle($('[data-title="' + window.location.hash.substring(1) + '"]').data('accordion-index'), false);
                                });
                        } else {
                            bdtUIkit.accordion($accordion).toggle($('[data-title="' + window.location.hash.substring(1) + '"]').data('accordion-index'), true);
                        }

                }
            }
        }
    if (activeHash == 'yes') {
        $(window).on('load', function() {
            if(activeScrollspy == 'yes'){
                hashHandler($accordion, hashScrollspyTime, hashTopOffset);
            }else{
                bdtUIkit.accordion($accordion).toggle($('[data-title="' + window.location.hash.substring(1) + '"]').data('accordion-index'), false);
            }
        });
        $($accordion).find('.bdt-ep-accordion-title').off('click').on('click', function(event) {
            window.location.hash = ($.trim($(this).attr('data-title')));
            hashHandler($accordion, hashScrollspyTime = 1000, hashTopOffset);
        });
        $(window).on('hashchange', function(e) {
            hashHandler($accordion, hashScrollspyTime = 1000, hashTopOffset);
        });
    }

    };

    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-accordion.default', widgetAccordion);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */