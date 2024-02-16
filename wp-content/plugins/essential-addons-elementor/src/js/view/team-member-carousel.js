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
		return $scope?.data('settings')?.[name]?.size;
	}
}
var TeamMemberCarouselHandler = function($scope, $) {
    var $carousel = $scope.find(".eael-tm-carousel").eq(0),
        $pagination =
            $carousel.data("pagination") !== undefined
                ? $carousel.data("pagination")
                : ".swiper-pagination",
        $arrow_next =
            $carousel.data("arrow-next") !== undefined
                ? $carousel.data("arrow-next")
                : ".swiper-button-next",
        $arrow_prev =
            $carousel.data("arrow-prev") !== undefined
                ? $carousel.data("arrow-prev")
                : ".swiper-button-prev",
        $defaultItems =
            $carousel.data("items") !== undefined ? $carousel.data("items") : 3,
        $defaultMargin =
            $carousel.data("margin") !== undefined
                ? $carousel.data("margin")
                : 10,
        $speed =
            $carousel.data("speed") !== undefined
                ? $carousel.data("speed")
                : 400,
        $autoplay =
            $carousel.data("autoplay") !== undefined
                ? $carousel.data("autoplay")
                : 999999,
        $loop =
            $carousel.data("loop") !== undefined ? $carousel.data("loop") : 0,
        $grab_cursor =
            $carousel.data("grab-cursor") !== undefined
                ? $carousel.data("grab-cursor")
                : 0,
        $data_id =
            $carousel.data("id") !== undefined ? $carousel.data("id") : "",
        $pause_on_hover =
            $carousel.data("pause-on-hover") !== undefined
                ? $carousel.data("pause-on-hover")
                : "";
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

	$.each(['mobile', 'mobile_extra', 'tablet', 'tablet_extra', 'laptop', 'desktop', 'widescreen'], function (index, device) {
		let breakpoint = localize.el_breakpoints[device];
		if (breakpoint.is_enabled) {
			let _items = $carousel.data('items-' + device),
				_margin = $carousel.data('margin-' + device);
			$margin = _margin !== undefined ? _margin : (device === 'desktop' ? $defaultMargin : 10);
			$items = _items !== undefined && _items !== "" ? _items : (device === 'desktop' ? $defaultItems : 3);
			el_breakpoints[bp_index] = {
				breakpoint: breakpoint.value,
				slidesPerView: $items,
				spaceBetween: $margin
			}
			bp_index++;
		}
	});

	$.each(el_breakpoints, function (index, breakpoint) {
		let _index = parseInt(index);
		if (typeof el_breakpoints[_index + 1] !== 'undefined') {
			breakpoints[breakpoint.breakpoint] = {
				slidesPerView: el_breakpoints[_index + 1].slidesPerView,
				spaceBetween: el_breakpoints[_index + 1].spaceBetween
			}
		}
	});

	var $slider_options = {
            direction: "horizontal",
            speed: $speed,
            grabCursor: $grab_cursor,
            loop: $loop,
            observer: true,
			observeParents: true,
            autoplay: {
                delay: $autoplay,
	            disableOnInteraction: false
            },
            pagination: {
                el: $pagination,
                clickable: true
            },
            navigation: {
                nextEl: $arrow_next,
                prevEl: $arrow_prev
            },
            breakpoints: breakpoints,
        };

    swiperLoader($carousel, $slider_options).then((TeamSlider)=>{
		if (0 == $autoplay) {
			TeamSlider.autoplay.stop();
		}

		if ($pause_on_hover && $autoplay !== 0) {
			$carousel.on("mouseenter", function() {
				TeamSlider.autoplay.stop();
			});
			$carousel.on("mouseleave", function() {
				TeamSlider.autoplay.start();
			});
		}

		TeamSlider.update();
	});


    var $tabContainer = $('.eael-advance-tabs'),
		nav = $tabContainer.find('.eael-tabs-nav li'),
		tabContent = $tabContainer.find('.eael-tabs-content > div');

	
	nav.on('click', function() {
		var currentContent = tabContent.eq($(this).index()),
			sliderExist = $(currentContent).find('.swiper-container-wrap.eael-team-member-carousel-wrap');
		if(sliderExist.length) {
			swiperLoader($carousel, $slider_options);
		}
	});
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

jQuery(window).on("elementor/frontend/init", function() {

	if (ea.elementStatusCheck('teamMemberSliderLoad')) {
		return false;
	}

    elementorFrontend.hooks.addAction(
        "frontend/element_ready/eael-team-member-carousel.default",
        TeamMemberCarouselHandler
    );
});
