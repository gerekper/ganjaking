function get_configurations( $postCarousel, $scope ){
	let items_tablet = getControlValue('items_tablet', $scope),
		items_mobile = getControlValue('items_mobile', $scope);

	let $autoplay =
		$postCarousel.data("autoplay") !== undefined
			? $postCarousel.data("autoplay")
			: 999999,
		$pagination =
			$postCarousel.data("pagination") !== undefined
				? $postCarousel.data("pagination")
				: ".swiper-pagination",
		$arrow_next =
			$postCarousel.data("arrow-next") !== undefined
				? $postCarousel.data("arrow-next")
				: ".swiper-button-next",
		$arrow_prev =
			$postCarousel.data("arrow-prev") !== undefined
				? $postCarousel.data("arrow-prev")
				: ".swiper-button-prev",
		$defaultItems =
			$postCarousel.data("items") !== undefined
				? $postCarousel.data("items")
				: 3,
		$items_tablet =
			items_tablet !== undefined
				? items_tablet
				: 3,
		$items_mobile =
			items_mobile !== undefined
				? items_mobile
				: 3,
		$defaultMargin =
			$postCarousel.data("margin") !== undefined
				? $postCarousel.data("margin")
				: 10,
		$margin_tablet =
			$postCarousel.data("margin-tablet") !== undefined
				? $postCarousel.data("margin-tablet")
				: 10,
		$margin_mobile =
			$postCarousel.data("margin-mobile") !== undefined
				? $postCarousel.data("margin-mobile")
				: 10,
		$effect =
			$postCarousel.data("effect") !== undefined
				? $postCarousel.data("effect")
				: "slide",
		$speed =
			$postCarousel.data("speed") !== undefined
				? $postCarousel.data("speed")
				: 400,
		$loop =
			$postCarousel.data("loop") !== undefined
				? $postCarousel.data("loop")
				: 0,
		$grab_cursor =
			$postCarousel.data("grab-cursor") !== undefined
				? $postCarousel.data("grab-cursor")
				: 0,
		$pause_on_hover =
			$postCarousel.data("pause-on-hover") !== undefined
				? $postCarousel.data("pause-on-hover")
				: "",
		$centeredSlides = $effect === "coverflow" ? true : false;

	let $carouselOptions = {
		pause_on_hover: $pause_on_hover,
		direction: "horizontal",
		speed: $speed,
		effect: $effect,
		centeredSlides: $centeredSlides,
		grabCursor: $grab_cursor,
		autoHeight: true,
		loop: $loop,
		autoplay: {
			delay: $autoplay
		},
		pagination: {
			el: $pagination,
			clickable: true
		},
		navigation: {
			nextEl: $arrow_next,
			prevEl: $arrow_prev
		}
	};

	if ($autoplay === 0) {
		$carouselOptions.autoplay = false
	}

	if($effect === 'slide' || $effect === 'coverflow') {
		if (typeof (localize.el_breakpoints) === 'string') {
			$carouselOptions.breakpoints = {
				1024: {
					slidesPerView: $defaultItems,
					spaceBetween: $defaultMargin
				},
				768: {
					slidesPerView: $items_tablet,
					spaceBetween: $margin_tablet
				},
				320: {
					slidesPerView: $items_mobile,
					spaceBetween: $margin_mobile
				}
			};
		} else {
			let el_breakpoints = {}, breakpoints = {}, bp_index = 0,
				desktopBreakPoint = localize.el_breakpoints.widescreen.is_enabled ? localize.el_breakpoints.widescreen.value - 1 : 4800;
			el_breakpoints[bp_index] = {
				breakpoint: 0,
				slidesPerView: 0,
				spaceBetween: 0
			}
			bp_index++;
			localize.el_breakpoints.desktop = {
				is_enabled: true,
				value: desktopBreakPoint
			}
			jQuery.each(['mobile', 'mobile_extra', 'tablet', 'tablet_extra', 'laptop', 'desktop', 'widescreen'], function (index, device) {
				let breakpoint = localize.el_breakpoints[device];
				if (breakpoint.is_enabled) {
					let _items = getControlValue('items_' + device, $scope),
						_margin = $postCarousel.data('margin-' + device),
						$margin = _margin !== undefined ? _margin : (device === 'desktop' ? $defaultMargin : 10),
						$items = _items !== undefined && _items !== "" ? _items : (device === 'desktop' ? $defaultItems : 3);

					el_breakpoints[bp_index] = {
						breakpoint: breakpoint.value,
						slidesPerView: $items,
						spaceBetween: $margin
					}

					bp_index++;
				}
			});

			jQuery.each(el_breakpoints, function (index, breakpoint) {
				let _index = parseInt(index);
				if (typeof el_breakpoints[_index + 1] !== 'undefined') {
					breakpoints[breakpoint.breakpoint] = {
						slidesPerView: el_breakpoints[_index + 1].slidesPerView,
						spaceBetween: el_breakpoints[_index + 1].spaceBetween
					}
				}
			});

			$carouselOptions.breakpoints = breakpoints;
		}
	}else {
		$carouselOptions.items = 1;
	}

	return $carouselOptions;
}

function autoPlayManager( element, options, event ) {
	if (options.autoplay.delay === 0) {
		event?.autoplay?.stop();
	}

	if (options.pause_on_hover && options.autoplay.delay !== 0) {
		element.on("mouseenter", function() {
			event?.autoplay?.pause();
		});
		element.on("mouseleave", function() {
			event?.autoplay?.run();
		});
	}
}
var PostCarouselHandler = function($scope, $) {
	var $postCarousel = $scope.find(".eael-post-carousel").eq(0),
		$carouselOptions = get_configurations( $postCarousel, $scope );

	swiperLoader($postCarousel, $carouselOptions).then((eaelPostCarousel)=>{
		autoPlayManager( $postCarousel, $carouselOptions, eaelPostCarousel );
	});

	var PostCarouselLoader = function (element) {
		let postCarousels = $(element).find('.eael-post-carousel');
		if (postCarousels.length) {
			postCarousels.each(function () {
				if ($(this)[0].swiper) {
					$(this)[0].swiper.destroy(true, true);
					let options = get_configurations( $(this), element );
					let $this = $(this);
					swiperLoader($(this)[0], options).then((event) => {
						autoPlayManager($this, options, event);
					});
				}
			});
		}
	}

	ea.hooks.addAction("ea-toggle-triggered", "ea", PostCarouselLoader);
	ea.hooks.addAction("ea-lightbox-triggered", "ea", PostCarouselLoader);
	ea.hooks.addAction("ea-advanced-tabs-triggered", "ea", PostCarouselLoader);
	ea.hooks.addAction("ea-advanced-accordion-triggered", "ea", PostCarouselLoader);
};

const swiperLoader = (swiperElement, swiperConfig) => {
	if ('undefined' === typeof Swiper || 'function' === typeof Swiper) {
		const asyncSwiper = elementorFrontend.utils.swiper;
		return new asyncSwiper(swiperElement, swiperConfig).then((newSwiperInstance) => {
			return newSwiperInstance;
		});
	} else {
		return swiperPromise(swiperElement, swiperConfig);
	}
}

const swiperPromise =  (swiperElement, swiperConfig) => {
	return new Promise((resolve, reject) => {
		const swiperInstance =  new Swiper( swiperElement, swiperConfig );
		resolve( swiperInstance );
	});
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

jQuery(window).on("elementor/frontend/init", function() {

	if (ea.elementStatusCheck('eaelPostSliderLoad')) {
		return false;
	}

	elementorFrontend.hooks.addAction(
		"frontend/element_ready/eael-post-carousel.default",
		PostCarouselHandler
	);
});
