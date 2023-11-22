(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumTwitterFeedHandler = function ($scope, $) {
            var $elem = $scope.find(".premium-twitter-feed-wrapper"),
                $loading = $elem.find(".premium-loading-feed"),
                settings = $elem.data("settings"),
                carousel = 'yes' === $elem.data("carousel");

            function get_tweets_data() {
                $elem
                    .find(".premium-social-feed-container")
                    .socialfeed({
                        twitter: {
                            accounts: settings.accounts,
                            limit: settings.limit || 2,
                            consumer_key: 'AgV213XdiJzwCvrdaDRsxnwti',
                            consumer_secret: 'qRfkwcdL4y9l18WFd0sDIEYUC34iJGmCKUzniS6YomO3crBOkU',
                            token: "776918558542561280-E0hfZKFOYweZQYLQmEcqdvy8RsjrYtg",
                            secret: "rVLihQdh90lhbzvVlMW5fZolaATLlBbUXOyANpBb6RDOe",
                            tweet_mode: "extended",
                            header: settings.header
                        },
                        length: settings.length || 130,
                        show_media: 'yes' === settings.showMedia,
                        readMore: settings.readMore,
                        template: settings.template,
                        callback: function () {
                            $loading.removeClass("premium-show-loading");
                            $elem.imagesLoaded(function () {
                                handleTwitterFeed();
                            });
                        }
                    });
            }

            function handleTwitterFeed() {
                var headerWrap = $elem.find('.premium-twitter-user-cover');

                if (carousel) {

                    var autoPlay = 'yes' === $elem.data("play"),
                        speed = $elem.data("speed") || 5000,
                        rtl = $elem.data("rtl"),
                        colsNumber = $elem.data("col"),
                        prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                    headerWrap.prependTo($elem);
                    $(headerWrap).not(':first').remove();

                    $elem.find(".premium-social-feed-container").slick({
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

                if (!carousel && settings.layout === "grid-layout" && !settings.even) {

                    var masonryContainer = $elem.find(".premium-social-feed-container");

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

                    headerWrap.prependTo($elem);
                    $(headerWrap).not(':first').remove();

                }
            }

            $.ajax({
                url: get_tweets_data(),
                beforeSend: function () {
                    $loading.addClass("premium-show-loading");
                },
                error: function () {
                    console.log("error getting data from Twitter");
                }
            });

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-twitter-feed.default', PremiumTwitterFeedHandler);
    });
})(jQuery);