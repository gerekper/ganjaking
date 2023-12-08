/**
 * Start testimonial carousel widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTCarousel = function( $scope, $ ) {

		var $tCarousel = $scope.find( '.bdt-testimonial-carousel' );
            
        if ( ! $tCarousel.length ) {
            return;
        }

		var $tCarouselContainer = $tCarousel.find('.swiper-carousel'),
			$settings 		 = $tCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($tCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($tCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.default', widgetTCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.bdt-twyla', widgetTCarousel );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-testimonial-carousel.bdt-vyxo', widgetTCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End testimonial carousel widget script
 */

