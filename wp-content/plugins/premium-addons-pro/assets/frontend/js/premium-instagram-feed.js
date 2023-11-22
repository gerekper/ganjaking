(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var instaCounter = 0,
            PremiumInstaFeedHandler = function ($scope, $) {
                instaCounter++;

                var $instaElem = $scope.find(".premium-instafeed-container"),
                    $loading = $instaElem.find(".premium-loading-feed"),
                    settings = $instaElem.data("settings"),
                    carousel = $instaElem.data("carousel");

                if (!settings)
                    return;

                var feed = new Instafeed({
                    api: settings.api,
                    target: settings.id,
                    feed: settings.feed,
                    get: "user",
                    tagName: settings.tags,
                    sortBy: settings.sort,
                    limit: settings.limit,
                    videos: settings.videos,
                    words: settings.words,
                    overlay: settings.overlay,
                    filter: settings.filter,
                    templateData: {
                        likes: settings.likes,
                        comments: settings.comments,
                        description: settings.description,
                        link: settings.link,
                        share: settings.share
                    },
                    afterLoad: function () {

                        //Remove loading spinner
                        $loading.removeClass("premium-show-loading");

                        setTimeout(function () {
                            $($instaElem).find(".premium-insta-feed-wrap a[data-rel^='prettyPhoto']")
                                .prettyPhoto({
                                    theme: settings.theme,
                                    hook: "data-rel",
                                    opacity: 0.7,
                                    show_title: false,
                                    deeplinking: false,
                                    overlay_gallery: false,
                                    custom_markup: "",
                                    default_width: 900,
                                    default_height: 506,
                                    social_tools: ""
                                });


                            $instaElem.imagesLoaded(function () {

                                if (carousel) {
                                    instaCarouselHandler();
                                } else if (settings.masonry) {
                                    instagramMasonryGrid();
                                }

                                $scope.find(".elementor-invisible").removeClass("elementor-invisible");

                            });

                        }, 100);

                    }
                });

                try {
                    feed.run();
                } catch (err) {
                    console.log(err);
                }



                function instagramMasonryGrid() {
                    $instaElem.isotope({
                        itemSelector: ".premium-insta-feed",
                        percentPosition: true,
                        layoutMode: "masonry",
                        animationOptions: {
                            duration: 750,
                            easing: "linear",
                            queue: false
                        }
                    });

                }

                function instaCarouselHandler() {

                    var autoPlay = $instaElem.data("play"),
                        speed = $instaElem.data("speed"),
                        rtl = $instaElem.data("rtl"),
                        colsNumber = $instaElem.data("col"),
                        colsNumberTablet = $instaElem.data("col-tab"),
                        colsNumberMobile = $instaElem.data("col-mobile"),
                        prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                    $instaElem.find(".premium-insta-grid").slick({
                        infinite: true,
                        slidesToShow: colsNumber,
                        slidesToScroll: colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: colsNumberTablet,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: colsNumberMobile,
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

                //Handle Instagram Videos
                if (settings.videos) {
                    $instaElem.on('click', '.premium-insta-video-wrap', function () {
                        var $instaVideo = $(this).find("video");
                        $instaVideo.get(0).play();
                        $instaVideo.css("visibility", "visible");
                    });
                }
            };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-instagram-feed.default', PremiumInstaFeedHandler);
    });
})(jQuery);