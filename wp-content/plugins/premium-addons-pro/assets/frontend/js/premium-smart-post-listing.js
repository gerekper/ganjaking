(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumSmartPostListingHandler = elementorModules.frontend.handlers.Base.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        user: '.fa-user',
                        activeCat: '.category.active',
                        loading: '.premium-loading-feed',
                        filterTabs: '.premium-smart-listing__filter-tabs li a',
                        activeElement: '.premium-smart-listing__filter-tabs li .active',
                        widgetWrapper: '.premium-smart-listing__wrapper',
                        postsOuterWrapper: '.premium-smart-listing__posts-outer-wrapper',
                        currentPage: '.premium-smart-listing__pagination-container .page-numbers.current',
                        post: '.premium-smart-listing__post-wrapper',
                        listingsWrapper: '.premium-smart-listing__posts-wrapper',
                        gridItem: '.premium-smart-listing__grid-item',
                        metaSeparators: '.premium-smart-listing__meta-separator',
                    }
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors'),
                    elements = {
                        $filterTabs: this.$element.find(selectors.filterTabs),
                        $widgetWrapper: this.$element.find(selectors.widgetWrapper),
                        $postsOuterWrapper: this.$element.find(selectors.postsOuterWrapper),
                        $post: this.$element.find(selectors.post),
                        $listingsWrapper: this.$element.find(selectors.listingsWrapper),
                    };

                return elements;
            },

            bindEvents: function () {
                this.setWidgetSettings();

                this.removeMetaSeparators();

                this.run();
            },
            setWidgetSettings: function() {
                var elementSettings = this.getElementSettings(),
                    _this  = this;

                this.settings = {
                    pageNumber: 1,
                    count: 2,
                    isLoaded: true,
                    reqType: '',
                    paginationType: elementSettings.pagination_type,
                    infinite: 'yes' === elementSettings.infinite_scroll ? true : false,
                    total: _this.elements.$post.data('total'),
                    carousel: 'yes' === elementSettings.carousel ? true : false,
                    scrollAfter: 'yes' === elementSettings.scroll_to_offset ? true : false,
                };

                this.settings.loadMoreBtn = ! this.settings.infinite && 'yes' === elementSettings.load_more_button ? true : false;

                if (this.settings.carousel) {
                    this.settings.slidesToScroll = elementSettings.slides_to_scroll;
                    this.settings.spacing = parseInt(elementSettings.carousel_spacing);
                    this.settings.autoPlay = 'yes' === elementSettings.carousel_play ? true : false;
                    this.settings.fade = 'yes' === elementSettings.carousel_fade ? true : false;
                    this.settings.center = 'yes' === elementSettings.carousel_center ? true : false;
                    this.settings.speed = '' !== elementSettings.carousel_speed ? parseInt(elementSettings.carousel_speed) : 300;
                    this.settings.arrows = 'yes' === elementSettings.carousel_arrows ? true : false;

                    if ( this.settings.autoPlay ) {
                        this.settings.autoplaySpeed = '' !== elementSettings.carousel_autoplay_speed ? parseInt(elementSettings.carousel_autoplay_speed) : 5000;
                    }
                }

            },
            run: function() {
                var elementSettings = this.getElementSettings(),
                    _this = this;

                if ( this.elements.$filterTabs.length > 1 ) {

                    this.initFilterTabs();

                    if ( 'yes' === elementSettings['wrap_tabs_sw'] ) {

                        _this.reconstructFilterTabs();

                        $(window).on('resize.paConstructFilters', function () {
                            _this.reconstructFilterTabs();
                        });
                    } else {
                        $(window).off('resize.paConstructFilters');
                    }
                }

                if ( 'custom' === elementSettings.pa_spl_skin ) {
                    this.hideExtraItems();
                }

                if (this.settings.carousel) {
                    this.elements.$listingsWrapper.slick(this.getSlickSettings());

                    if ( this.settings.arrows ) {

                        // add events to the arrows.
                        this.$element.find('.page-numbers').on('click.paCarouselNav', function(){

                            if ( $(this).hasClass('prev') ) {
                                _this.elements.$listingsWrapper.slick('slickPrev');
                            } else if ( $(this).hasClass('next') ) {
                                _this.elements.$listingsWrapper.slick('slickNext');
                            }
                        });
                    }
                    // $post.removeClass("premium-carousel-hidden");
                }

                if ( 'yes' === elementSettings.premium_blog_paging ) {
                    this.paginate();
                }

                if (this.settings.infinite && this.elements.$postsOuterWrapper.is(":visible")) {
                    this.getInfiniteScrollPosts();
                }

                if (this.settings.loadMoreBtn && this.elements.$postsOuterWrapper.is(":visible")) {
                    this.loadMorePosts();
                }
            },
            hideExtraItems: function() {
                /**
                 * Hide the extra containing columns since the section itself won't cause an issue,
                 * and removing the columns from the dom will results in style/design mishaps.
                 */
                var isContainer = elementorFrontend.config.experimentalFeatures.container,
                    $extraItems = this.$element.find('.premium-extra-item')
                    $parentCol = $extraItems.closest('.elementor-widget-premium-grid-item').parents('.e-con').first();

                $parentCol = !isContainer || $parentCol.length < 1 ? $extraItems.closest('.elementor-top-column') : $parentCol;

                $parentCol.css({
                    visibility: 'hidden',
                    opacity: 0
                });

            },
            initFilterTabs: function() {

                var _this = this,
                    selectors = this.getSettings('selectors');

                this.elements.$filterTabs.on('click.paFilterTabs', function(e) {

                    e.preventDefault();

                    _this.$element.find(selectors.activeElement).removeClass("active");

                    $(this).addClass("active");

                    // update active category & page number.
                    _this.settings.activeCategory = $(this).attr("data-filter");
                    _this.settings.pageNumber = 1;

                    _this.settings.reqType = 'filter';

                    if (_this.settings.infinite) {

                        _this.getPostsByAjax(false);
                        _this.settings.count = 2;
                        _this.getInfiniteScrollPosts();
                    } else {
                        //Make sure to reset pagination before sending our AJAX request
                        _this.getPostsByAjax(_this.settings.scrollAfter);
                    }

                });
            },
            paginate: function () {
                var _this = this,
                    $scope = this.$element,
                    selectors = this.getSettings('selectors');

                $scope.on('click', '.premium-smart-listing__pagination-container .page-numbers', function (e) {

                    e.preventDefault();

                    if ( 'default' === _this.settings.paginationType ) {
                        var currentPage = _this.settings.pageNumber;

                    } else {
                        if ($(this).hasClass("current")) return;
                        var currentPage = parseInt($scope.find(selectors.currentPage).html());
                    }

                    if ($(this).hasClass('next')) {
                        _this.settings.pageNumber = currentPage + 1;
                    } else if ($(this).hasClass('prev')) {
                        _this.settings.pageNumber = currentPage - 1;
                    } else {
                        _this.settings.pageNumber = $(this).html();
                    }

                    _this.getPostsByAjax(_this.settings.scrollAfter);
                })
            },
            getInfiniteScrollPosts: function () {
                var windowHeight = jQuery(window).outerHeight() / 1.25,
                    _this = this;

                $(window).scroll(function () {

                    if (_this.elements.$filterTabs.length > 1) {

                        var $post = _this.elements.$postsOuterWrapper.find(".premium-smart-listing__post-wrapper");
                        _this.settings.total = $post.data('total');
                    }

                    if (_this.settings.count <= _this.settings.total) {

                        if (($(window).scrollTop() + windowHeight) >= (_this.$element.find('.premium-smart-listing__post-wrapper:last').offset().top)) {
                            if (true == _this.settings.isLoaded) {
                                _this.settings.pageNumber = _this.settings.count;
                                _this.settings.reqType = 'infinite';
                                _this.getPostsByAjax(false);
                                _this.settings.count++;
                                _this.settings.isLoaded = false;
                            }

                        }
                    }
                });
            },
            loadMorePosts: function () {
                var _this = this;

                _this.$element.find('.premium-smart-listing__load-more-btn-wrapper a').on('click.PaLoadMore', function() {

                    if (_this.elements.$filterTabs.length > 1) {

                        var $post = _this.elements.$postsOuterWrapper.find(".premium-smart-listing__post-wrapper");
                        _this.settings.total = $post.data('total');
                    }

                    if (_this.settings.count <= _this.settings.total) {

                        if (true == _this.settings.isLoaded) {
                            _this.settings.pageNumber = _this.settings.count;
                            _this.settings.reqType = 'infinite';
                            _this.getPostsByAjax(false);
                            _this.settings.count++;
                            _this.settings.isLoaded = false;
                        }
                    }
                });
            },
            getPostsByAjax: function(shouldScroll) {

                //If filter tabs is not enabled, then always set category to all.
                if ('undefined' === typeof this.settings.activeCategory) {
                    this.settings.activeCategory = '*';
                }

                var _this = this,
                    selectors = this.getSettings('selectors');

                $.ajax({
                    url: PremiumProSettings.ajaxurl,
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        action: 'pa_get_posts',
                        page_id: _this.elements.$widgetWrapper.data('page'),
                        widget_id: _this.$element.data('id'),
                        page_number: _this.settings.pageNumber,
                        req_type: _this.settings.reqType,
                        category: _this.settings.activeCategory,
                        nonce: PremiumSettings.nonce,
                    },
                    beforeSend: function () {
                        _this.elements.$postsOuterWrapper.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                        if (shouldScroll) {
                            $('html, body').animate({
                                scrollTop: ((_this.elements.$postsOuterWrapper.offset().top) - 50)
                            }, 'slow');
                        }
                    },
                    success: function (res) {
                        if (!res.data)
                            return;

                        _this.$element.find(selectors.loading).remove();

                        var posts = res.data.posts,
                            paging = res.data.paging;

                        if (_this.settings.infinite || _this.settings.loadMoreBtn) {
                            _this.settings.isLoaded = true;

                            if (_this.elements.$filterTabs.length > 1 && _this.settings.pageNumber === 1) {
                                _this.elements.$postsOuterWrapper.html(posts);
                            } else {
                                if ( 'custom' === _this.getElementSettings().pa_spl_skin ) {
                                    _this.elements.$postsOuterWrapper.append(posts);
                                    _this.hideExtraItems();
                                } else {
                                    _this.$element.find('.premium-smart-listing__posts-wrapper').append(posts);
                                }
                            }
                            // check if there's no more posts -> hide the button.
                        } else {
                            _this.elements.$postsOuterWrapper.html(posts);
                            _this.$element.find(".premium-smart-listing__footer").html(paging);
                        }

                        _this.removeMetaSeparators();
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            },
            getSlickSettings: function () {

                var settings = this.settings,
                    elementSettings = this.getElementSettings(),
                    rows = elementSettings.carousel_rows ? elementSettings.carousel_rows : 1,
                    cols = elementSettings.listing_cols ? elementSettings.listing_cols : 1,
                    rowsTablet = elementSettings.carousel_rows_tablet ? elementSettings.carousel_rows_tablet : 1,
                    rowsMobile = elementSettings.carousel_rows_mobile ? elementSettings.carousel_rows_mobile : 1,
                    colsTablet = elementSettings.listing_cols_tablet ? elementSettings.listing_cols_tablet : 1;

                return {
                    infinite: true,
                    slidesToShow: cols,
                    slidesToScroll: parseInt( settings.slidesToScroll || cols ),
                    responsive: [
                        {
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: colsTablet,
                                slidesToScroll: 1,
                                rows: rowsTablet
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                rows: rowsMobile
                            }
                        }
                    ],
                    autoplay: settings.autoPlay,
                    rows: rows,
                    speed: settings.speed,
                    autoplaySpeed: settings.autoplaySpeed,
                    fade: settings.fade,
                    centerMode: settings.center,
                    centerPadding: settings.spacing + "px",
                    draggable: true,
                    arrows: false,
                };
            },
            removeMetaSeparators: function () {

                var selectors = this.getSettings('selectors'),
                    $gridItems = this.$element.find(selectors.gridItem);

                $gridItems.each(function (index, item) {

                    var $metaSeparators = $(item).find(selectors.metaSeparators),
                        $user = $(item).find(selectors.user);

                        if (1 === $metaSeparators.length) {

                            if (!$user.length) {
                                $(item).find(selectors.metaSeparators).remove();
                            }

                        } else {
                            if (!$user.length) {
                                $(item).find(selectors.metaSeparators).first().remove();
                            }
                        }
                });
            },
            reconstructFilterTabs: function() {

                var deviceTabs = getComputedStyle(this.$element.find('.premium-smart-listing__header-wrapper')[0]).getPropertyValue('--premium-spl-filters'),
                    $filterWrapper = this.$element.find('.premium-smart-listing__filter-tabs'),
                    $filterTabs = $filterWrapper.find('li:not(.premium-smart-listing__filter-tabs-menu-wrapper)'),
                    $wrappedMenu = $filterWrapper.find('.premium-smart-listing__filter-tabs-menu-wrapper ul'),
                    $visibleTabs = $filterWrapper.find('li:not(.premium-smart-listing__filter-tabs-menu-wrapper):not(.premium-smart-listing__wrapped-filter)'),
                    $wrappedTabs = $wrappedMenu.find('li');

                if ( deviceTabs > $visibleTabs.length ) {

                    $filterTabs.css('visiblity', 'hidden');

                    var itemsNo = deviceTabs - $visibleTabs.length;

                    for ( var i = 0; i < itemsNo; i++ ) {

                        var $clone = $($wrappedTabs[i]).removeClass('premium-smart-listing__wrapped-filter').clone();

                        $($wrappedTabs[i]).remove();

                        $filterWrapper.find('.premium-smart-listing__filter-tabs-menu-wrapper').before( $clone );
                    }

                } else if ( deviceTabs < $visibleTabs.length ) {

                    $filterTabs.css( 'visiblity', 'hidden');

                    var itemsNo =  $visibleTabs.length - deviceTabs;

                    for ( var i = 1; i <= itemsNo; i++ ) {

                        var $clone = $($visibleTabs[ $visibleTabs.length - i ]).addClass('premium-smart-listing__wrapped-filter').clone();

                        $( $visibleTabs[ $visibleTabs.length - i ]).remove();

                        $wrappedMenu.prepend( $clone );
                    }
                }

                this.checkWrappedFilters();

                $filterTabs.css( 'visiblity', 'visible');
            },
            checkWrappedFilters: function() {

                var wrappedTabs = this.$element.find('.premium-smart-listing__filter-tabs-menu-wrapper ul').children().length;

                if ( wrappedTabs > 0 ) {
                    this.$element.find('.premium-smart-listing__filter-tabs-menu-wrapper').show();
                } else {
                    this.$element.find('.premium-smart-listing__filter-tabs-menu-wrapper').hide();
                }
            }
        });

        elementorFrontend.elementsHandler.attachHandler('premium-smart-post-listing', PremiumSmartPostListingHandler);
    });

})(jQuery);