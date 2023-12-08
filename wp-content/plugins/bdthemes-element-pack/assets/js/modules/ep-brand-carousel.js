/**
 * Start twitter carousel widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetBrandCarousel = function ($scope, $) {

		var $brandCarousel = $scope.find('.bdt-ep-brand-carousel');

		if (!$brandCarousel.length) {
			return;
		}

		var $brandCarouselContainer = $brandCarousel.find('.swiper-carousel'),
			$settings = $brandCarousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($brandCarouselContainer, $settings); // this is an example
			if ($settings.pauseOnHover) {
				$($brandCarouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-brand-carousel.default', widgetBrandCarousel);
	});

}(jQuery, window.elementorFrontend));

/**
 * End twitter carousel widget script
 */

