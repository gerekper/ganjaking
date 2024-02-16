function get_configurations($carousel){
    let $pagination =
        $carousel.data('pagination') !== undefined
            ? $carousel.data('pagination')
            : '.swiper-pagination',
        $arrow_next =
            $carousel.data('arrow-next') !== undefined
                ? $carousel.data('arrow-next')
                : '.swiper-button-next',
        $arrow_prev =
            $carousel.data('arrow-prev') !== undefined
                ? $carousel.data('arrow-prev')
                : '.swiper-button-prev',
        $items =
            $carousel.data('items') !== undefined ? $carousel.data('items') : 3,
        $items_tablet =
            $carousel.data('items-tablet') !== undefined
                ? $carousel.data('items-tablet')
                : 3,
        $items_mobile =
            $carousel.data('items-mobile') !== undefined
                ? $carousel.data('items-mobile')
                : 3,
        $margin =
            $carousel.data('margin') !== undefined
                ? $carousel.data('margin')
                : 10,
        $margin_tablet =
            $carousel.data('margin-tablet') !== ''
                ? $carousel.data('margin-tablet')
                : 10,
        $margin_mobile =
            $carousel.data('margin-mobile') !== ''
                ? $carousel.data('margin-mobile')
                : 10,
        $effect =
            $carousel.data('effect') !== undefined
                ? $carousel.data('effect')
                : 'slide',
        $speed =
            $carousel.data('speed') !== undefined
                ? $carousel.data('speed')
                : 400,
        $autoplay =
            $carousel.data('autoplay') !== undefined
                ? $carousel.data('autoplay')
                : 0,
        $loop =
            $carousel.data('loop') !== undefined ? $carousel.data('loop') : 0,
        $grab_cursor =
            $carousel.data('grab-cursor') !== undefined
                ? $carousel.data('grab-cursor')
                : 0,
        $centeredSlides = $effect === 'coverflow' ? true : false,
        $pause_on_hover =
            $carousel.data('pause-on-hover') !== undefined
                ? $carousel.data('pause-on-hover')
                : '',
        $twitterCarouselOptions = {
            pause_on_hover: $pause_on_hover,
            direction: 'horizontal',
            speed: $speed,
            effect: $effect,
            fadeEffect: { crossFade: true },
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

    if ($autoplay === 0) {
        $twitterCarouselOptions.autoplay = false
    }

    if ($effect === 'slide' || $effect === 'coverflow') {
        $twitterCarouselOptions.breakpoints = {
            1024: {
                slidesPerView: $items,
                spaceBetween: $margin,
            },
            768: {
                slidesPerView: $items_tablet,
                spaceBetween: $margin_tablet,
            },
            320: {
                slidesPerView: $items_mobile,
                spaceBetween: $margin_mobile,
            },
        }
    } else {
        $twitterCarouselOptions.items = 1
    }

    return $twitterCarouselOptions;
}

function autoPlayManager( element, options, event ){
    if (options.autoplay.delay === 0) {
        event?.autoplay?.stop()
    }

    if (options.pause_on_hover && options.autoplay.delay !== 0) {
        element.on('mouseenter', function () {
            event?.autoplay?.stop()
        })
        element.on('mouseleave', function () {
            event?.autoplay?.start()
        })
    }
}
var TwitterFeedCarouselHandler = function ($scope, $) {
    let $carousel = $('.eael-twitter-feed-carousel', $scope),
        $twitterCarouselOptions = get_configurations($carousel);

    swiperLoader($carousel, $twitterCarouselOptions).then((twitterCarousel) => {
        autoPlayManager($carousel, $twitterCarouselOptions, twitterCarousel);
    });

    var TwitterFeedCarouselLoader = function (element) {
        let productSliders = $(element).find('.eael-twitter-feed-carousel');
        if (productSliders.length) {
            productSliders.each(function () {
                let $this = $(this);
                if ($this[0].swiper) {
                    $this[0].swiper.destroy(true, true);
                    let options = get_configurations($this);
                    swiperLoader($(this)[0], options).then((event) => {
                        autoPlayManager($this, options, event);
                    });
                }
            });
        }
    }

    ea.hooks.addAction("ea-toggle-triggered", "ea", TwitterFeedCarouselLoader);
    ea.hooks.addAction("ea-lightbox-triggered", "ea", TwitterFeedCarouselLoader);
    ea.hooks.addAction("ea-advanced-tabs-triggered", "ea", TwitterFeedCarouselLoader);
    ea.hooks.addAction("ea-advanced-accordion-triggered", "ea", TwitterFeedCarouselLoader);


    if (isEditMode) {
        elementor.hooks.addAction("panel/open_editor/widget/eael-twitter-feed-carousel", ( panel, model, view ) => {
            panel.content.el.onclick = (event) => {

                if (event.target.dataset.event == "ea:cache:clear") {
                    let button = event.target;
                    button.innerHTML = "Clearing...";

                    jQuery.ajax({
                        url: localize.ajaxurl,
                        type: "post",
                        data: {
                            action: "eael_clear_widget_cache_data",
                            security: localize.nonce,
                            ac_name: model.attributes.settings.attributes.eael_twitter_feed_ac_name,
                            hastag: model.attributes.settings.attributes.eael_twitter_feed_hashtag_name,
                            c_key: model.attributes.settings.attributes.eael_twitter_feed_consumer_key,
                            c_secret: model.attributes.settings.attributes.eael_twitter_feed_consumer_secret,
                        },
                        success(response) {
                            if (response.success) {
                                button.innerHTML = "Clear";

                            } else {
                                button.innerHTML = "Failed";
                            }
                        },
                        error() {
                            button.innerHTML = "Failed";
                        },
                    });
                }
            }
        });

    }
}

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

jQuery(window).on('elementor/frontend/init', function () {

    if (ea.elementStatusCheck('twitterFeedLoad')) {
        return false;
    }

    elementorFrontend.hooks.addAction(
        'frontend/element_ready/eael-twitter-feed-carousel.default',
        TwitterFeedCarouselHandler
    )
})
