(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumFacebookHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {
                return {
                    selectors: {
                        elementWrap: '.premium-social-feed-element-wrap',
                        feedWrapper: '.premium-facebook-feed-wrapper',
                        loader: '.premium-loading-feed',

                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $elementWrap: this.$element.find(selectors.elementWrap),
                        $feedWrapper: this.$element.find(selectors.feedWrapper),
                        $loader: this.$element.find(selectors.loader),
                    };

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $loader = this.elements.$loader;

                this.elements.$elementWrap.remove();

                $.ajax({
                    url: this.get_facebook_data(),
                    beforeSend: function () {
                        $loader.addClass("premium-show-loading");
                    },
                    error: function () {
                        console.log("error getting data from Facebook");
                    }
                });

            },

            get_facebook_data: function () {

                var _this = this,
                    $paFbElem = this.elements.$feedWrapper,
                    $loader = this.elements.$loader,
                    settings = this.getElementSettings(),
                    id = this.$element.data('id'),
                    widgetSettings = $paFbElem.data('settings');

                $paFbElem
                    .find(".premium-social-feed-container")
                    .socialfeed({
                        facebook: {
                            accounts: ['!' + settings.account_id],
                            limit: settings.post_number || 2,
                            access_token: settings.access_token,
                            feedObject: PaFbFeed[id]
                        },
                        length: settings.content_length || 130,
                        show_media: 'yes' === settings.posts_media,
                        readMore: settings.read_text,
                        template: widgetSettings.template,
                        adminPosts: settings.admin_posts,
                        callback: function () {
                            $loader.removeClass("premium-show-loading");
                            $paFbElem.imagesLoaded(function () {
                                _this.handleFacebookFeed();
                            });
                        }
                    });
            },

            //new function for handling carousel option
            handleFacebookFeed: function () {

                var $paFbElem = this.elements.$feedWrapper,
                    settings = this.getElementSettings(),
                    widgetSettings = $paFbElem.data('settings');

                if ('yes' === settings.feed_carousel) {

                    var autoPlay = 'yes' === settings.carousel_play,
                        speed = settings.carousel_autoplay_speed || 5000,
                        rtl = elementorFrontend.config.is_rtl,
                        colsNumber = $paFbElem.data("col"),
                        prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                    $paFbElem.find(".premium-social-feed-container").slick({
                        infinite: true,
                        slidesToShow: colsNumber,
                        slidesToScroll: colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                        ],
                        autoplay: autoPlay,
                        autoplaySpeed: speed,
                        rows: 0,
                        rtl: rtl ? true : false,
                        nextArrow: nextArrow,
                        prevArrow: prevArrow,
                        draggable: true,
                        pauseOnHover: true
                    });
                }

                if ('yes' != settings.feed_carousel && widgetSettings.layout === "grid-layout" && !widgetSettings.even) {

                    var masonryContainer = $paFbElem.find(".premium-social-feed-container");

                    masonryContainer.isotope({
                        itemSelector: ".premium-social-feed-element-wrap",
                        percentPosition: true,
                        layoutMode: "masonry",
                        animationOptions: {
                            duration: 750,
                            easing: "linear",
                            queue: false
                        }
                    });
                }
            }

        });


        elementorFrontend.elementsHandler.attachHandler('premium-facebook-feed', PremiumFacebookHandler);
    });
})(jQuery);
