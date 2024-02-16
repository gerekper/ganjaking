function get_configurations($testimonialSlider, $scope) {
	let items_tablet = getControlValue('items_tablet', $scope),
		items_mobile = getControlValue('items_mobile', $scope),
	$pagination =
			$testimonialSlider.data('pagination') !== undefined
				? $testimonialSlider.data('pagination')
				: '.swiper-pagination',
		$arrow_next =
			$testimonialSlider.data('arrow-next') !== undefined
				? $testimonialSlider.data('arrow-next')
				: '.swiper-button-next',
		$arrow_prev =
			$testimonialSlider.data('arrow-prev') !== undefined
				? $testimonialSlider.data('arrow-prev')
				: '.swiper-button-prev',
		$items =
			$testimonialSlider.data('items') !== undefined
				? $testimonialSlider.data('items')
				: 3,
		$items_tablet =
			items_tablet !== undefined
				? items_tablet
				: 3,
		$items_mobile =
			items_mobile !== undefined
				? items_mobile
				: 3,
		$slideItems =
			$testimonialSlider.data('slide-items') !== undefined
				? $testimonialSlider.data('slide-items')
				: 1,
		$slideItems_tablet =
			$testimonialSlider.data('slide-items-tablet') !== undefined
				? $testimonialSlider.data('slide-items-tablet')
				: 1,
		$slideItems_mobile =
			$testimonialSlider.data('slide-items-mobile') !== undefined
				? $testimonialSlider.data('slide-items-mobile')
				: 1,
		$margin =
			$testimonialSlider.data('margin') !== undefined
				? $testimonialSlider.data('margin')
				: 10,
		$margin_tablet =
			$testimonialSlider.data('margin-tablet') !== undefined
				? $testimonialSlider.data('margin-tablet')
				: 10,
		$margin_mobile =
			$testimonialSlider.data('margin-mobile') !== undefined
				? $testimonialSlider.data('margin-mobile')
				: 10,
		$effect =
			$testimonialSlider.data('effect') !== undefined
				? $testimonialSlider.data('effect')
				: 'slide',
		$speed =
			$testimonialSlider.data('speed') !== undefined
				? $testimonialSlider.data('speed')
				: 400,
		$autoplay =
			$testimonialSlider.data('autoplay_speed') !== undefined
				? $testimonialSlider.data('autoplay_speed')
				: 999999,
		$loop =
			$testimonialSlider.data('loop') !== undefined
				? $testimonialSlider.data('loop')
				: 0,
		$grab_cursor =
			$testimonialSlider.data('grab-cursor') !== undefined
				? $testimonialSlider.data('grab-cursor')
				: 0,
		$centeredSlides = $effect == 'coverflow' ? true : false,
		$pause_on_hover =
			$testimonialSlider.data('pause-on-hover') !== undefined
				? $testimonialSlider.data('pause-on-hover')
				: '';

	var $testimonialSliderOptions = {
		pause_on_hover: $pause_on_hover,
		direction: 'horizontal',
		speed: $speed,
		effect: $effect,
		centeredSlides: $centeredSlides,
		grabCursor: $grab_cursor,
		autoHeight: true,
		loop: $loop,
		autoplay: {
			delay: $autoplay,
			disableOnInteraction: false
		},
		pagination: {
			el: $pagination,
			clickable: true,
		},
		navigation: {
			nextEl: $arrow_next,
			prevEl: $arrow_prev,
		},
	}

	if ($effect === 'slide' || $effect === 'coverflow') {
		$testimonialSliderOptions.breakpoints = {
			1024: {
				slidesPerView: $items,
				spaceBetween: $margin,
				slidesPerGroup: $slideItems,
			},
			768: {
				slidesPerView: $items_tablet,
				spaceBetween: $margin_tablet,
				slidesPerGroup: $slideItems_tablet,
			},
			320: {
				slidesPerView: $items_mobile,
				spaceBetween: $margin_mobile,
				slidesPerGroup: $slideItems_mobile,
			},
		}
	} else {
		$testimonialSliderOptions.items = 1
	}

	if ($effect === 'fade') {
		$testimonialSliderOptions.fadeEffect = {
			crossFade: true,
		};
	}

	return $testimonialSliderOptions;
}

function autoPlayManager( element, options, event ){
	if (options.autoplay.delay === 0) {
		element?.autoplay?.stop()
	}

	if (options.pause_on_hover && options.autoplay.delay !== 0) {
		element.on('mouseenter', function () {
			event?.autoplay?.stop();
		})
		element.on('mouseleave', function () {
			event?.autoplay?.start();
		})
	}
	event.update();
}

var TestimonialSliderHandler = function ($scope, $) {
	var $testimonialSlider = $scope.find('.eael-testimonial-slider-main').eq(0),
		$testimonialSliderOptions = get_configurations( $testimonialSlider, $scope )


	var $testimonialSliderObj = swiperLoader(
		$testimonialSlider,
		$testimonialSliderOptions
	)
	$testimonialSliderObj.then( ( $testimonialSliderObj ) => {
		autoPlayManager( $testimonialSlider, $testimonialSliderOptions, $testimonialSliderObj );

		//gallery pagination
		var $paginationGallerySelector = $scope
			.find('.eael-testimonial-slider .eael-testimonial-gallary-pagination')
			.eq(0)
		if ($paginationGallerySelector.length > 0) {
			swiperLoader($paginationGallerySelector, {
				spaceBetween: 20,
				centeredSlides: true,
				touchRatio: 0.2,
				slideToClickedSlide: true,
				loop: true,
				slidesPerGroup: 1,
				loopedSlides: $items,
				slidesPerView: 3,
			}).then(( $paginationGallerySlider) => {
				$testimonialSliderObj.controller.control = $paginationGallerySlider
				$paginationGallerySlider.controller.control = $testimonialSliderObj
			})
		}
	} );

	$(document).on("click", ".eael-testimonial-read-more-btn", function (e) {
    e.preventDefault();
    var parent = $(this).closest(".eael-testimonial-item-inner");
    $(".eael-testimonial-text-excerpt", parent).toggle();
    $(".eael-testimonial-text-full-text", parent).toggle();
    $('.swiper-wrapper').height('auto');
		window.dispatchEvent(new Event("resize"));
  });

  var TestimonialSlider = function (element) {
    let testimonialSliders = $(element).find(".eael-testimonial-slider-main");
    if (testimonialSliders.length) {
      testimonialSliders.each(function () {
        if ($(this)[0].swiper) {
          $(this)[0].swiper.destroy(true, true);
            let options = get_configurations($(this), element);
          let $this = $(this);
          swiperLoader($(this)[0], options).then((event) => {
            autoPlayManager($this, options, event);
          });
        }
      });
    }
  };

  ea.hooks.addAction("ea-toggle-triggered", "ea", TestimonialSlider);
  ea.hooks.addAction("ea-lightbox-triggered", "ea", TestimonialSlider);
  ea.hooks.addAction("ea-advanced-tabs-triggered", "ea", TestimonialSlider);
  ea.hooks.addAction("ea-advanced-accordion-triggered", "ea", TestimonialSlider );

}

/**
 * getControlValue
 *
 * Return Elementor control value in frontend,
 * But before uses this method you have to ensure that,
 * "frontend_available = true" in elementor control
 *
 * @since 5.0.1
 * @param name
 * @param $scope
 * @returns {*}
 */
const getControlValue = (name, $scope) => {
	if (ea.isEditMode) {
		return elementorFrontend.config.elements?.data[$scope[0]?.dataset.modelCid]?.attributes[name]?.size;
	} else {
		$scope = jQuery($scope);
		return $scope?.data('settings')?.[name]?.size;
	}
}

const swiperLoader = (swiperElement, swiperConfig) => {
	if ( 'undefined' === typeof Swiper || 'function' === typeof Swiper ) {
		const asyncSwiper = elementorFrontend.utils.swiper;
		return new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
			return  newSwiperInstance;
		} );
	} else {
		return swiperPromise( swiperElement, swiperConfig );
	}
}

const swiperPromise =  (swiperElement, swiperConfig) => {
	return new Promise((resolve, reject) => {
		const swiperInstance =  new Swiper( swiperElement, swiperConfig );
		resolve( swiperInstance );
	});
}

jQuery(window).on('elementor/frontend/init', function () {

	if (ea.elementStatusCheck('testimonialLoad')) {
		return false;
	}

	elementorFrontend.hooks.addAction(
		'frontend/element_ready/eael-testimonial-slider.default',
		TestimonialSliderHandler
	)
})
