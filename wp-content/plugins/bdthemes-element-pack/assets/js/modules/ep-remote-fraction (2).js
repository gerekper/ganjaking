/**
 * Start remote fraction widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteFraction = function ($scope, $) {
        var $remoteFraction = $scope.find('.bdt-remote-fraction'),
            $settings = $remoteFraction.data('settings'),
            $pad = $settings.pad,
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteFraction.length) {
            return;
        }

        if (!$settings.remoteId) {
            // return;
            // try to auto detect
            var $parentSection = $scope.closest('.elementor-section');

            $settings['remoteId'] = $parentSection;
        }

        if ($($settings.remoteId).find('.swiper-container, .swiper').length <= 0) {
            if (editMode == true) {
                $($settings.id + '-notice').removeClass('bdt-hidden');
            }
            return;
        }

        $($settings.id + '-notice').addClass('bdt-hidden');

        $(document).ready(function () {
            setTimeout(() => {
                const swiperInstance = $($settings.remoteId).find('.swiper-container, .swiper')[0].swiper;

                var $slideActive = $($settings.remoteId).find('.swiper-slide-active');
                var realIndex = $slideActive.data('swiper-slide-index')
                if (typeof realIndex === 'undefined') {
                    realIndex = $slideActive.index();
                }

                var $totalSlides = $($settings.remoteId).find('.swiper-slide:not(.swiper-slide-duplicate)').length;
                $totalSlides = $totalSlides + '';
                realIndex = ((realIndex + 1) + '');
                
                $($settings.id).find('.bdt-current').text(realIndex.padStart($pad, "0"));
                $($settings.id).find('.bdt-total').text($totalSlides.padStart($pad, "0"));

                swiperInstance.on('slideChangeTransitionEnd', function (e) {
                    let item = swiperInstance.realIndex + 1 + '';
                    $($settings.id).find('.bdt-current').text(item.padStart($pad, "0"));
                });

            }, 2500);

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-fraction.default', widgetRemoteFraction);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote fraction widget script
 */