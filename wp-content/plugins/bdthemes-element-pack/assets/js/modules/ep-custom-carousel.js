/**
 * Start custom carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetCustomCarousel = function( $scope, $ ) {

		var $carousel = $scope.find( '.bdt-ep-custom-carousel' );
				
        if ( ! $carousel.length ) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-carousel'),
			$settings 		 = $carousel.data('settings');

		const Swiper = elementorFrontend.utils.swiper;
		initSwiper();
		async function initSwiper() {
			var swiper = await new Swiper($carouselContainer, $settings);

			if ($settings.pauseOnHover) {
				$($carouselContainer).hover(function () {
					(this).swiper.autoplay.stop();
				}, function () {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-custom-carousel.default', widgetCustomCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-custom-carousel.bdt-custom-content', widgetCustomCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End custom carousel widget script
 */

