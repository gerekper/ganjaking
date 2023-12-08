/**
 * Start twitter carousel widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetFbFeedCarousel = function ($scope, $) {

		var $fbCarousel = $scope.find('.bdt-facebook-feed-carousel');

		if (!$fbCarousel.length) {
			return;
		}

		var $fbCarouselContainer = $fbCarousel.find('.swiper-carousel'),
			$settings = $fbCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($fbCarouselContainer, $settings); // this is an example
			if ($settings.pauseOnHover) {
				$($fbCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-facebook-feed-carousel.default', widgetFbFeedCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End twitter carousel widget script
 */

