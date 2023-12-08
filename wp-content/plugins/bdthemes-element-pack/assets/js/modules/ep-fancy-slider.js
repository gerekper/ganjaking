/**
 * Start fancy slider widget script
 */

(function ($, elementor) {

    'use strict';

    var widgetFancySlider = function ($scope, $) {

        var $slider = $scope.find('.bdt-ep-fancy-slider');

        if (!$slider.length) {
            return;
        }

        var $sliderContainer = $slider.find('.swiper-carousel'),
            $settings = $slider.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($sliderContainer, $settings);

            if ($settings.pauseOnHover) {
                $($sliderContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-fancy-slider.default', widgetFancySlider);
    });

}(jQuery, window.elementorFrontend));

/**
 * End fancy slider widget script
 */