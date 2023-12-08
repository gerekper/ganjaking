/**
 * Start twitter slider widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTwitterSlider = function( $scope, $ ) {

		var $twitterSlider = $scope.find( '.bdt-twitter-slider' );
				
        if ( ! $twitterSlider.length ) {
            return;
        }

		var $twitterSliderContainer = $twitterSlider.find('.swiper-carousel'),
			$settings 		 = $twitterSlider.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($twitterSliderContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($twitterSliderContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-twitter-slider.default', widgetTwitterSlider );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End twitter slider widget script
 */

