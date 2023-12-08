/**
 * Start twitter carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetProductCarousel = function( $scope, $ ) {

		var $ProductCarousel = $scope.find( '.bdt-ep-product-carousel' );
				
        if ( ! $ProductCarousel.length ) {
            return;
        }

		var $ProductCarouselContainer = $ProductCarousel.find('.swiper-carousel'),
			$settings 		 = $ProductCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($ProductCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($ProductCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-product-carousel.default', widgetProductCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter carousel widget script
 */

