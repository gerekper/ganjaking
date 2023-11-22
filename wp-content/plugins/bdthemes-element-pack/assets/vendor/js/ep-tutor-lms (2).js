/**
 * Start tutor lms widget script
 */

( function( $, elementor ) {

	'use strict';

	var widgetTutorCarousel = function( $scope, $ ) {

		var $tutorCarousel = $scope.find( '.bdt-tutor-lms-course-carousel' );
            
        if ( ! $tutorCarousel.length ) {
            return;
        }

		var $tutorCarouselContainer = $tutorCarousel.find('.swiper-carousel'),
			$settings 		 = $tutorCarousel.data('settings');

		// Access swiper class
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        
        async function initSwiper() {

			var swiper = await new Swiper($tutorCarouselContainer, $settings);

			if ($settings.pauseOnHover) {
				 $($tutorCarouselContainer).hover(function() {
					(this).swiper.autoplay.stop();
				}, function() {
					(this).swiper.autoplay.start();
				});
			}
		};
	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-tutor-lms-course-carousel.default', widgetTutorCarousel );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * End tutor lms widget script
 */

/**
 * Start tutor lms grid widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetTutorLMSGrid = function ($scope, $) {

		var $tutorLMS = $scope.find('.bdt-tutor-lms-course-grid'),
			$settings = $tutorLMS.data('settings');

		if (!$tutorLMS.length) {
			return;
		}

		if ($settings.tiltShow == true) {
			var elements = document.querySelectorAll($settings.id + " .bdt-tutor-course-item");
			VanillaTilt.init(elements);
		}

	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/bdt-tutor-lms-course-grid.default', widgetTutorLMSGrid);
	});

}(jQuery, window.elementorFrontend));

/**
 * End tutor lms grid widget script
 */
