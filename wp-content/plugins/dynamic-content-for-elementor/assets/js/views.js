(function ($) {
	var WidgetElements_ViewsHandler = function ($scope, $) {

		var id_scope = $scope.attr('data-id');
		var elementSettings = dceGetElementSettings($scope);
		let swiper_class = elementorFrontend.config.experimentalFeatures.e_swiper_latest ? '.swiper' : '.swiper-container';
		var elementSwiper = $scope.find(swiper_class);
		var speed = elementSettings.transition_speed;
		var disableOnInteraction = Boolean( elementSettings.pause_on_interaction ) || false;
		var loop = false;
		if ( elementSettings.dce_views_style_format !== 'slideshow' ) {
			return;
		}
		if ( 'yes' === elementSettings.infinite) {
			loop = true;
		}

		var id_post = $scope.attr('data-post-id');

		var viewsSwiperOptions = {
			autoHeight: true,
			speed: speed,
			loop: loop,
		};

		// Responsive Parameters
		viewsSwiperOptions.breakpoints = dynamicooo.makeSwiperBreakpoints({
			slidesPerView: {
				elementor_key: 'slides_to_show',
				default_value: 'auto'
			},
			slidesPerGroup: {
				elementor_key: 'slides_to_scroll',
				default_value: 1
			},
			spaceBetween: {
				elementor_key: 'spaceBetween',
				default_value: 0,
			},
		}, elementSettings);

		// Navigation
		if (elementSettings.navigation != 'none') {

			if ( elementSettings.navigation == 'both' || elementSettings.navigation == 'arrows' ) {
				viewsSwiperOptions = $.extend(viewsSwiperOptions, {
					navigation: {
						nextEl: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .elementor-swiper-button-next' : '.elementor-swiper-button-next',
						prevEl: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .elementor-swiper-button-prev' : '.elementor-swiper-button-prev',
					},
				});
			}

			if ( elementSettings.navigation == 'both' || elementSettings.navigation == 'dots' ) {
				viewsSwiperOptions = $.extend(viewsSwiperOptions, {
					pagination: {
						el: id_post ? '.elementor-element-' + id_scope + '[data-post-id="' + id_post + '"] .swiper-pagination' : '.swiper-pagination',
						type: 'bullets',
						clickable: true,
					},
				});
			}
		}

		// Autoplay
		if ( elementSettings.autoplay ) {
			viewsSwiperOptions = $.extend(viewsSwiperOptions, {
				autoplay: {
					autoplay: true,
					delay: elementSettings.autoplay_speed,
					disableOnInteraction: disableOnInteraction,
				}
			});
		}

		const asyncSwiper = elementorFrontend.utils.swiper;

		new asyncSwiper( elementSwiper, viewsSwiperOptions ).then( ( newSwiperInstance ) => {
			viewsSwiper = newSwiperInstance;
		} ).catch( error => console.log(error) );

		// Pause on hover
		if ( elementSettings.autoplay && elementSettings.pause_on_hover ) {
			$(elementSwiper).on({
				mouseenter: function () {
					viewsSwiper.autoplay.stop();
				},
				mouseleave: function () {
					viewsSwiper.autoplay.start();
				}
			});
		}

	};

	// Make sure you run this code under Elementor..
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/dce-views.default', WidgetElements_ViewsHandler);
	});
})(jQuery);
