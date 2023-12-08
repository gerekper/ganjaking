/**
 * Start remote thumbs widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemoteThumbs = function ($scope, $) {
        var $remoteThumbs = $scope.find('.bdt-remote-thumbs'),
            $settings = $remoteThumbs.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remoteThumbs.length) {
            return;
        }

        if (!$settings.remoteId) {
            // return;
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

                $($settings.id).find('.bdt-item:eq(' + realIndex + ')').addClass('bdt-active');

                $($settings.id).find('.bdt-item').on("click", function () {
                    var index = $(this).data('index');

                    if ($settings.loopStatus) {
                        swiperInstance.slideToLoop(index);
                    } else {
                        swiperInstance.slideTo(index);
                    }

                    $($settings.id).find('.bdt-item').removeClass('bdt-active');
                    $($settings.id).find('.bdt-item:eq(' + index + ')').addClass('bdt-active');
                    $($settings.id).addClass('wait--');

                });

                swiperInstance.on('slideChangeTransitionEnd', function (e) {
                    if ($($settings.id).hasClass('wait--')) {
                        $($settings.id).removeClass('wait--');
                        return;
                    } else {
                        $($settings.id).find('.bdt-item').removeClass('bdt-active');
                        $($settings.id).find('.bdt-item:eq(' + swiperInstance.realIndex + ')').addClass('bdt-active');
                        // console.log('*** mySwiper.activeIndex', swiperInstance.realIndex);
                    }

                });

            }, 2500);

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-thumbs.default', widgetRemoteThumbs);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote thumbs widget script
 */