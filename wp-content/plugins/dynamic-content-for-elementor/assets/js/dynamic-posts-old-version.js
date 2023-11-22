var isAdminBar = false,
isEditMode = false;

(function ($) {

    var WidgetElementsPostsDCEHandler = function ($scope, $) {
        var infScroll = null;

        var elementSettings = dceGetElementSettings($scope),
        id_scope = $scope.attr('data-id'),
        elementorElement = '.elementor-element-' + id_scope,
        is_history = Boolean( elementSettings.infiniteScroll_enable_history ) ? 'replace' : false;
        $block_acfposts = '.acfposts-grid',
        $objBlock_acfposts = $scope.find($block_acfposts);
        var id_post = $scope.closest('.elementor').attr('data-post-id');

        if ($objBlock_acfposts.data('style') == 'swiper' || $objBlock_acfposts.data('style') == 'carousel') {
            if (elementSettings.masking_enable) {
                $objBlock_acfposts.closest('.elementor-section').css('overflow', 'hidden');
            }
        }

        // WOW Animation
        if (elementSettings.enabled_wow) {
          var wow = new WOW(
            {
              boxClass: 'wow', // animated element css class (default is wow)
              animateClass: 'animated', // animation css class (default is animated)
              offset: 0, // distance to the element when triggering the animation (default is 0)
              mobile: true, // trigger animations on mobile devices (default is true)
              live: true, // act on asynchronously loaded content (default is true)
              callback: function (box) {
                  // the callback is fired every time an animation is started
                  // the argument that is passed in is the DOM node being animated
              },
              scrollContainer: null // optional scroll container selector, otherwise use window
            }
          );
          wow.init();
        }

        if ($objBlock_acfposts.data('style') == 'grid') {

            // Isotope
            $layoutMode = 'masonry';
            if ($objBlock_acfposts.data('fitrow'))
                $layoutMode = 'fitRows';
            var $grid_dce_posts = $objBlock_acfposts.isotope({
                itemSelector: '.dce-post-item',
                layoutMode: $layoutMode,
                sortBy: 'original-order',
                percentPosition: true,
                masonry: {
                    columnWidth: '.dce-post-item'
                }
            });
            // imagesLoaded
            $grid_dce_posts.imagesLoaded().progress(function () {
                $grid_dce_posts.isotope('layout');
            });
            $grid_dce_posts.imagesLoaded().always(function () {
                $grid_dce_posts.isotope('layout');
            });

            // Infinite Scroll
            var iso = $grid_dce_posts.data('isotope');

            $scope.find('.dce-filters .filters-item').on('click', 'a', function (e) {
                var filterValue = $(this).attr('data-filter');
                $(this).parent().siblings().removeClass('filter-active');
                $(this).parent().addClass('filter-active');

                $grid_dce_posts.isotope({filter: filterValue});

                // callto infinite scroll
                if (elementSettings.infiniteScroll_enable) {
                    if ($objBlock_acfposts.length) {
                        $objBlock_acfposts.infiniteScroll('loadNextPage');
                    }
                }

                return false;
            });


        } else if ($($scope).find($block_acfposts).data('style') == 'carousel') {


            var slidesToShow = elementSettings.slides_to_show || 3,
                    isSingleSlide = 1 === slidesToShow,
                    centro = true,
                    cicloInfinito = false;

            var slideNum = $scope.find('.dce-post-item').length;
            if (slideNum < Number(elementSettings.slides_to_show)) {
                centroDiapo = true;
                cicloInfinito = false;
                slideInitNum = Math.ceil(slideNum / 2);

            } else {
                centro = Boolean( elementSettings.carousel_center_enable );
                cicloInfinito = Boolean( elementSettings.carousel_infinite_enable );
            }
            var slickOptions = {
                dots: Boolean( elementSettings.carousel_dots_enable ),
                arrows: Boolean( elementSettings.carousel_arrow_enable ),
                prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
                nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
                infinite: cicloInfinito,
                autoplay: Boolean( elementSettings.carousel_autoplay_enable ),
                centerPadding: false,
                centerMode: Boolean(centro),
                speed: elementSettings.carousel_speed || 500,
                autoplaySpeed: elementSettings.carousel_autoplayspeed || 3000,
                slidesToShow: Number(elementSettings.slides_to_show) || 4,
                slidesToScroll: Number(elementSettings.slides_to_scroll) || 4,
                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: Number(elementSettings.slides_to_show_tablet) || 2,
                            slidesToScroll: Number(elementSettings.slides_to_scroll_tablet) || 2,
                            dots: Boolean(elementSettings.carousel_dots_enable_tablet ),
                            arrows: Boolean( elementSettings.carousel_arrow_enable_tablet ),
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: Number(elementSettings.slides_to_show_mobile) || 1,
                            slidesToScroll: Number(elementSettings.slides_to_scroll_mobile) || 1,
                            dots: Boolean(elementSettings.carousel_dots_enable_mobile ),
                            arrows: Boolean( elementSettings.carousel_arrow_enable_mobile ),
                        }
                    }
                ]
            };
            if (isSingleSlide) {
                slickOptions.fade = 'fade' === elementSettings.carousel_effect;
            } else {
                slickOptions.slidesToScroll = +elementSettings.slides_to_scroll;
            }
            // Carosello
            $objBlock_acfposts.slick(slickOptions);


        } else if ($($scope).find($block_acfposts).data('style') == 'swiper') {

            var elementSettings = dceGetElementSettings($scope);
			let swiper_class = elementorFrontend.config.experimentalFeatures.e_swiper_latest ? '.swiper' : '.swiper-container';

            var elementSwiper = $scope.find(swiper_class);
            var id_scope = $scope.attr('data-id');
            var centroDiapo = false;
            var cicloInfinito = false;
            var slideInitNum = 0;
            var slidesPerView = Number(elementSettings.slidesPerView);

            var slideNum = $scope.find('.dce-post-item').length;

            centerDiapo = Boolean( elementSettings.centeredSlides );
            cicloInfinito = Boolean( elementSettings.loop );

            var elementorBreakpoints = elementorFrontend.config.breakpoints;

            var spaceBetween = 0;
            if (elementSettings.spaceBetween) {
                spaceBetween = elementSettings.spaceBetween;
            }

            var swiperOptions = {
                // Optional parameters
                direction: 'horizontal',
                initialSlide: slideInitNum,
                speed: Number(elementSettings.speed_slider) || 300,
                autoHeight: Boolean( elementSettings.autoHeight ), //false, // Set to true and slider wrapper will adopt its height to the height of the currently active slide
                roundLengths: Boolean( elementSettings.roundLengths ), //false, // Set to true to round values of slides width and height to prevent blurry texts on usual resolution screens (if you have such)
                effect: elementSettings.effects || 'slide',
                slidesPerView: slidesPerView || 'auto',
                slidesPerGroup: Number(elementSettings.slidesPerGroup) || 1, // Set numbers of slides to define and enable group sliding. Useful to use with slidesPerView > 1
                spaceBetween: spaceBetween, // 30,
                slidesOffsetBefore: 0, //   Add (in px) additional slide offset in the beginning of the container (before all slides)
                slidesOffsetAfter: 0, //    Add (in px) additional slide offset in the end of the container (after all slides)
                slidesPerColumn: Number(elementSettings.slidesColumn) || 1, // 1, // Number of slides per column, for multirow layout
                slidesPerColumnFill: 'row', // Could be 'column' or 'row'. Defines how slides should fill rows, by column or by row
                centerInsufficientSlides: true,
                watchOverflow: true,
                centeredSlides: centroDiapo,
                grabCursor: Boolean( elementSettings.grabCursor ), //true,
                freeMode: Boolean( elementSettings.freeMode ),
                freeModeMomentum: Boolean( elementSettings.freeModeMomentum ),
                freeModeMomentumRatio: Number(elementSettings.freeModeMomentumRatio) || 1,
                freeModeMomentumVelocityRatio: Number(elementSettings.freeModeMomentumVelocityRatio) || 1,
                freeModeMomentumBounce: Boolean( elementSettings.freeModeMomentumBounce ),
                freeModeMomentumBounceRatio: Number(elementSettings.speed) || 1,
                freeModeMinimumVelocity: Number(elementSettings.speed) || 0.02,
                freeModeSticky: Boolean( elementSettings.freeModeSticky ),
                loop: cicloInfinito,
                navigation: {
                    nextEl: id_post ? '.dce-elementor-post-' + id_post + ' .elementor-element-' + id_scope + ' .next-' + id_scope : '.next-' + id_scope,
                    prevEl: id_post ? '.dce-elementor-post-' + id_post + ' .elementor-element-' + id_scope + ' .prev-' + id_scope : '.prev-' + id_scope,
                },
                pagination: {
                    el: id_post ? '.dce-elementor-post-' + id_post + ' .elementor-element-' + id_scope + ' .pagination-' + id_scope : '.pagination-' + id_scope,
                    clickable: true,
                    type: String(elementSettings.pagination_type) || 'bullets',
                    dynamicBullets: true,
                    renderFraction: function (currentClass, totalClass) {
                      return '<span class="' + currentClass + '"></span>' +
                             '<span class="separator">' + String(elementSettings.fraction_separator) + '</span>' +
                             '<span class="' + totalClass + '"></span>';
                      },
                },
                mousewheel: Boolean( elementSettings.mousewheelControl ),
                keyboard: {
                  enabled: Boolean( elementSettings.keyboardControl ),
                },
                on: {
                    init: function () {
                      $('body').attr('data-carousel-'+id_scope, this.realIndex);
                    },
                    slideChange: function (e) {
                      $('body').attr('data-carousel-'+id_scope, this.realIndex);
                    },
                  }
            };
            if (elementSettings.useAutoplay) {

                //default
                swiperOptions = $.extend(swiperOptions, {autoplay: true});

                var autoplayDelay = Number(elementSettings.autoplay);
                if ( !autoplayDelay ) {
                    autoplayDelay = 3000;
                }else{
                    autoplayDelay = Number(elementSettings.autoplay);
                }
                swiperOptions = $.extend(swiperOptions, {autoplay: {delay: autoplayDelay, disableOnInteraction: Boolean(elementSettings.autoplayDisableOnInteraction), stopOnLastSlide: Boolean(elementSettings.autoplayStopOnLast) }});

            }

            // Responsive Params
			swiperOptions.breakpoints = dynamicooo.makeSwiperBreakpoints({
				slidesPerView: {
					elementor_key: 'slidesPerView',
					default_value: 'auto'
				},
				slidesPerGroup: {
					elementor_key: 'slidesPerGroup',
					default_value: 1
				},
				spaceBetween: {
					elementor_key: 'spaceBetween',
					default_value: 0,
				},
				slidesPerColumn: {
					elementor_key: 'slidesColumn',
					default_value: 1,
				},
			}, elementSettings);

			
			const asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper( elementSwiper, swiperOptions ).then( ( newSwiperInstance ) => {
				mySwiper = newSwiperInstance;
			} ).catch( error => console.log(error) );
           
            // if autoplay and pause on hover are enabled
            if (elementSettings.useAutoplay && elementSettings.autoplayStopOnHover) {
                $(elementSwiper).on({
                    mouseenter: function () {
                        mySwiper.autoplay.stop();
                    },
                    mouseleave: function () {
                        mySwiper.autoplay.start();
                    }
                });
            }

        }
        // InfiniteScroll
        if ($objBlock_acfposts.data('style') == 'grid' || $objBlock_acfposts.data('style') == 'flexgrid' || $objBlock_acfposts.data('style') == 'simple') {
            if (elementSettings.infiniteScroll_enable) {
                //
                if (jQuery(elementorElement + ' .pagination__next').length) {
                    var infiniteScroll_options = {
                        // Infinite Scroll options...
                        path: elementorElement + ' .pagination__next',

                        history: is_history,
                        //history: 'push',

                        append: elementorElement + ' .dce-post-item',
                        outlayer: iso,

                        status: elementorElement + ' .page-load-status',
                        hideNav: elementorElement + '.pagination',

                        // disable loading on scroll
                        scrollThreshold: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,
                        loadOnScroll: 'scroll' === elementSettings.infiniteScroll_trigger ? true : false,

                        onInit: function () {
                            this.on('load', function () {
                            });
                        }
                    };
                    if (elementSettings.infiniteScroll_trigger == 'button') {
                        // load pages on button click
                        infiniteScroll_options['button'] = elementorElement + ' .view-more-button';
                    }
                    infScroll = $objBlock_acfposts.infiniteScroll(infiniteScroll_options);

                    // fix for infinitescroll + masonry
                    var nElements = jQuery(elementorElement + ' .dce-post-item:visible').length; // initial length
                    $objBlock_acfposts.on( 'append.infiniteScroll', function( event, response, path, items ) {
                        setTimeout(function(){
                            var nElementsVisible = jQuery(elementorElement + ' .dce-post-item:visible').length;
                            if (nElementsVisible <= nElements) {
                                // force another load
                                $objBlock_acfposts.infiniteScroll('loadNextPage');
                            }
                        }, 1000);

						// Reinit Template
						if ( elementorFrontend) {
							if ( elementorFrontend.elementsHandler.runReadyTrigger ) {
								var widgets = $('.elementor-widget-dce-dyncontel-acfposts').find('.elementor-widget');
								widgets.each(function (i) {
									elementorFrontend.elementsHandler.runReadyTrigger(jQuery(this));
								});
							}
						}

						// Add inline CSS for background url
						var allArticles = document.querySelectorAll(".elementor-widget-dce-dyncontel-acfposts .elementor-section, .elementor-widget-dce-dyncontel-acfposts .elementor-column");
						allArticles.forEach(function(article) {
							dce.addCssForBackground( article );
						});

                    });
                }
            }
        }

       // Vertical Timeline - by CodyHouse.co
        function VerticalTimeline(element) {
            this.element = element;
            this.blocks = this.element.getElementsByClassName("js-cd-block");
            this.images = this.element.getElementsByClassName("js-cd-img");
            this.contents = this.element.getElementsByClassName("js-cd-content");
            this.offset = 0.8;
            this.hideBlocks();
        }

        VerticalTimeline.prototype.hideBlocks = function () {
            //hide timeline blocks which are outside the viewport
            if (!"classList" in document.documentElement) {
                return;
            }
            var self = this;
            for (var i = 0; i < this.blocks.length; i++) {
                (function (i) {
                    if (self.blocks[i].getBoundingClientRect().top > window.innerHeight * self.offset) {
                        if (self.images[i]) {
                            self.images[i].classList.add("cd-is-hidden");
                        }
                        if (self.contents[i]) {
                            self.contents[i].classList.add("cd-is-hidden");
                        }
                    }
                })(i);
            }
        };

        VerticalTimeline.prototype.showBlocks = function () {
            if (!"classList" in document.documentElement) {
                return;
            }
            var self = this;
            if (self.contents.length) {
                for (var i = 0; i < this.blocks.length; i++) {
                    (function (i) {
                        if (self.contents[i].classList.contains("cd-is-hidden") && self.blocks[i].getBoundingClientRect().top <= window.innerHeight * self.offset) {
                            // add bounce-in animation
                            self.images[i].classList.add("cd-timeline__img--bounce-in");
                            self.contents[i].classList.add("cd-timeline__content--bounce-in");
                            self.images[i].classList.remove("cd-is-hidden");
                            self.contents[i].classList.remove("cd-is-hidden");
                        }
                    })(i);
                }
            }
        };

        // ----- Inizializzo la timeline -----
        var verticalTimelines = document.getElementsByClassName("js-cd-timeline"),
                verticalTimelinesArray = [],
                scrolling = false;
        if (verticalTimelines.length > 0) {
            for (var i = 0; i < verticalTimelines.length; i++) {
                (function (i) {
                    verticalTimelinesArray.push(new VerticalTimeline(verticalTimelines[i]));
                })(i);
            }
            jQuery('.wrap-p .modal-p').on("scroll", function (event) {
                if (!scrolling) {
                    scrolling = true;
                    (!window.requestAnimationFrame) ? setTimeout(checkTimelineScroll, 250) : window.requestAnimationFrame(checkTimelineScroll);
                }
            });
            //show timeline blocks on scrolling
            window.addEventListener("scroll", function (event) {
                if (!scrolling) {
                    scrolling = true;
                    (!window.requestAnimationFrame) ? setTimeout(checkTimelineScroll, 250) : window.requestAnimationFrame(checkTimelineScroll);
                }
            });
        }

        function checkTimelineScroll() {
            verticalTimelinesArray.forEach(function (timeline) {
                timeline.showBlocks();
            });
            scrolling = false;
        }

        if($scope.find('.dce-hover-effect').length){

            $scope.find('.dce-post-item').each(function(i,el){
                $(el).on("mouseenter touchstart", function() {
                    $(this).find('.dce-hover-effect-content').removeClass('dce-close').addClass('dce-open');
                });
                $(el).on("mouseleave touchend", function() {
                    $(this).find('.dce-hover-effect-content').removeClass('dce-open').addClass('dce-close');
                });
            });
        }
    };

    $(window).on('elementor/frontend/init', function () {
        if (elementorFrontend.isEditMode()) {
            isEditMode = true;
        }

        if ($('body').is('.admin-bar')) {
            isAdminBar = true;
        }
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-acfposts.default', WidgetElementsPostsDCEHandler);
    });

})(jQuery);
