/**
 * Start interactive tabs widget script
 */

 (function($, elementor) {

    'use strict';

    var widgetInteractiveTabs = function($scope, $) {

        var $slider = $scope.find('.bdt-interactive-tabs-content'),
            $tabs   = $scope.find('.bdt-interactive-tabs');

        if (!$slider.length) {
            return;
        }

        var $sliderContainer = $slider.find('.swiper-carousel'),
            $settings = $slider.data('settings'),
            $swiperId = $($settings.id).find('.swiper-carousel');

            const Swiper = elementorFrontend.utils.swiper;
            initSwiper();
            async function initSwiper() {
                var swiper = await new Swiper($swiperId, $settings);
                if ($settings.pauseOnHover) {
                    $($sliderContainer).hover(function () {
                        (this).swiper.autoplay.stop();
                    }, function () {
                        (this).swiper.autoplay.start();
                    });
                }
                // start video stop
                var stopVideos = function () {
                    var videos = document.querySelectorAll($settings.id + ' .bdt-interactive-tabs-iframe');
                    Array.prototype.forEach.call(videos, function (video) {
                        var src = video.src;
                        video.src = src;
                    });
                };
                // end video stop

                // $tabs.find('.bdt-interactive-tabs-item').eq(swiper.realIndex).addClass('bdt-active');
                $tabs.find('.bdt-interactive-tabs-item:first').addClass('bdt-active');
                console.log(swiper.realIndex);
                swiper.on('slideChange', function () {
                    $tabs.find('.bdt-interactive-tabs-item').removeClass('bdt-active');
                    $tabs.find('.bdt-interactive-tabs-item').eq(swiper.realIndex).addClass('bdt-active');

                    stopVideos();

                });

                $tabs.find('.bdt-interactive-tabs-wrap .bdt-interactive-tabs-item[data-slide]').on('click', function (e) {
                    e.preventDefault();
                    var slideno = $(this).data('slide');
                    swiper.slideTo(slideno + 1);
                });
            };
    };
    
    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-interactive-tabs.default', widgetInteractiveTabs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End interactive tabs widget script
 */

