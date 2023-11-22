(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumReviewHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        premiumRevElem: '.premium-fb-rev-container',
                        revsContainer: '.premium-fb-rev-reviews',
                        dotsContainer: '.premium-fb-dots-container',
                        dotsElem: '.slick-dots',
                        revPage: '.premium-fb-rev-page',
                        nextPage: 'premium-fb-page-next-yes',
                        emptyDots: '.premium-fb-empty-dots',
                        reviewWrap: '.premium-fb-rev-review-wrap'
                    },
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors'),
                    elements = {
                        $premiumRevElem: this.$element.find(selectors.premiumRevElem),
                        $revsContainer: this.$element.find(selectors.revsContainer),
                        $reviewWrap: this.$element.find(selectors.reviewWrap),
                    };

                elements.$revPage = elements.$premiumRevElem.find(selectors.revPage);

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var carousel = this.getElementSettings('reviews_carousel'),
                    revStyle = this.getElementSettings('reviews_style'),
                    $revsContainer = this.elements.$revsContainer,
                    $premiumRevElem = this.elements.$premiumRevElem,
                    slickSettings = this.getSlickSettings(),
                    selectors = this.getSettings('selectors');

                if (carousel) {

                    var isInfinite = this.getElementSettings('infinite_autoplay');

                    if ("even" === revStyle && isInfinite) {

                        var $reviewWrap = this.elements.$reviewWrap,
                            heights = new Array();

                        $reviewWrap.each(function (index, rev) {

                            var height = $(rev).outerHeight();

                            console.log(height);

                            heights.push(height);
                        });

                        var maxHeight = Math.max.apply(null, heights);

                        $reviewWrap.css("height", maxHeight + "px");

                    }

                    $revsContainer.slick(slickSettings.settings);
                }

                if ((slickSettings.general.dots && this.$element.hasClass(selectors.nextPage)) || (slickSettings.general.dots && slickSettings.general.arrows)) {

                    $('<div class="premium-fb-dots-container"></div>').appendTo($premiumRevElem);

                    var $dotsContainer = $premiumRevElem.find(selectors.dotsContainer),
                        $dotsElem = $revsContainer.find(selectors.dotsElem);

                    $('<div class="premium-fb-empty-dots"></div>').appendTo($dotsContainer);

                    $($dotsElem).appendTo($dotsContainer);

                    if (this.$element.hasClass(selectors.nextPage)) {
                        var pageWidth = this.elements.$revPage.outerWidth();

                        $dotsContainer.find(selectors.emptyDots).css('width', pageWidth + 'px');
                    }
                }

                if ("masonry" === revStyle && 1 !== slickSettings.general.colsNumber && !carousel) {
                    $revsContainer.isotope(this.getIsotopeSettings());
                }

            },

            getSlickSettings: function () {

                var settings = this.getElementSettings(),
                    slickCols = this.getSlickCols(),
                    generalSettings = {
                        autoPlay: 'yes' === settings.carousel_play ? true : false,
                        infinite: 'yes' === settings.infinite_autoplay ? true : false,
                        colsNumber: slickCols.colsNumber,
                        colsNumberTablet: slickCols.colsNumberTablet,
                        colsNumberMobile: slickCols.colsNumberMobile,
                        speed: settings.carousel_autoplay_speed || 5000,
                        dots: ['all', 'dots'].includes(settings.carousel_navigation) ? true : false,
                        arrows: ['all', 'arrows'].includes(settings.carousel_navigation) ? true : false,
                        prevArrow: '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow: '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>'
                    };

                generalSettings.rows = generalSettings.infinite ? settings.rows : 0;

                return {
                    general: generalSettings,
                    settings: {
                        infinite: true,
                        slidesToShow: generalSettings.colsNumber,
                        slidesToScroll: generalSettings.infinite ? 1 : generalSettings.colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: generalSettings.infinite ? 1 : generalSettings.colsNumberTablet,
                                slidesToScroll: 1,
                                autoplaySpeed: generalSettings.speed,
                                speed: 300,
                                centerMode: generalSettings.infinite ? true : false,
                                centerPadding: '30px',
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: generalSettings.infinite ? 1 : generalSettings.colsNumberMobile,
                                slidesToScroll: 1,
                                autoplaySpeed: generalSettings.speed,
                                speed: 300,
                                centerMode: generalSettings.infinite ? true : false,
                                centerPadding: '30px',
                            }
                        }
                        ],
                        useTransform: true,
                        autoplay: generalSettings.infinite ? true : generalSettings.autoPlay,
                        speed: generalSettings.infinite ? generalSettings.speed : 300,
                        autoplaySpeed: generalSettings.infinite ? 0 : generalSettings.speed,
                        rows: generalSettings.rows,
                        rtl: elementorFrontend.config.is_rtl,
                        arrows: generalSettings.arrows,
                        nextArrow: generalSettings.nextArrow,
                        prevArrow: generalSettings.prevArrow,
                        draggable: true,
                        pauseOnHover: generalSettings.infinite ? false : true,
                        dots: generalSettings.dots,
                        cssEase: generalSettings.infinite ? "linear" : "ease",
                        customPaging: function () {
                            return '<i class="fas fa-circle"></i>';
                        },
                    }
                }

            },

            getSlickCols: function () {
                var slickCols = this.getElementSettings(),
                    colsNumber = slickCols.reviews_columns,
                    colsNumberTablet = slickCols.reviews_columns_tablet || colsNumber,
                    colsNumberMobile = slickCols.reviews_columns_mobile || colsNumber;

                return {
                    colsNumber: parseInt(100 / colsNumber.substr(0, colsNumber.indexOf('%'))),
                    colsNumberTablet: parseInt(100 / colsNumberTablet.substr(0, colsNumberTablet.indexOf('%'))),
                    colsNumberMobile: parseInt(100 / colsNumberMobile.substr(0, colsNumberMobile.indexOf('%'))),
                }

            },

            getIsotopeSettings: function () {
                return {
                    itemSelector: ".premium-fb-rev-review-wrap",
                    percentPosition: true,
                    layoutMode: "masonry",
                    animationOptions: {
                        duration: 750,
                        easing: "linear",
                        queue: false
                    }
                }
            },

        });

        elementorFrontend.elementsHandler.attachHandler('premium-facebook-reviews', PremiumReviewHandler);
        elementorFrontend.elementsHandler.attachHandler('premium-google-reviews', PremiumReviewHandler);
        elementorFrontend.elementsHandler.attachHandler('premium-yelp-reviews', PremiumReviewHandler);

    });
})(jQuery);