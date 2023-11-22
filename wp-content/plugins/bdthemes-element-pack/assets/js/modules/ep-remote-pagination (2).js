/**
 * Start remote pagination widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetRemotePagination = function ($scope, $) {
        var $remotePagination = $scope.find('.bdt-remote-pagination'),
            $settings = $remotePagination.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$remotePagination.length) {
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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-remote-pagination.default', widgetRemotePagination);
    });

}(jQuery, window.elementorFrontend));

/**
 * End remote pagination widget script
 */