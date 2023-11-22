(function ($) {

    $(window).on('elementor/frontend/init', function () {
        // Tabs Handler
        var PremiumTabsHandler = elementorModules.frontend.handlers.Base.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        premiumTabsElem: '.premium-tabs',
                        navList: '.premium-tabs-nav-list',
                        navListItem: '.premium-tabs-nav-list-item',
                        contentWrap: '.premium-content-wrap',
                        currentTab: '.premium-tabs-nav-list-item.tab-current',
                        currentClass: 'tab-current',
                        tabContent: '.premium-accordion-tab-content'
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $premiumTabsElem: this.$element.find(selectors.premiumTabsElem),
                        $navList: this.$element.find(selectors.navList).first(),
                        $contentWrap: this.$element.find(selectors.contentWrap),
                        $navListItem: this.$element.find(selectors.navListItem),
                    };

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var _this = this,
                    $premiumTabsElem = this.elements.$premiumTabsElem,
                    $navList = this.elements.$navList,
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    elementSettings = this.getElementSettings(),
                    selectors = this.getSettings('selectors'),
                    navigation = [];

                //Fix conflict issue when shortcodes are added in tabs content.
                if ('object' === typeof elementSettings.premium_tabs_repeater) {
                    elementSettings.premium_tabs_repeater.forEach(function (item) {
                        navigation.push(item.custom_tab_navigation);
                    });
                }

                this.settings = {
                    id: '#premium-tabs-' + this.$element.data('id'),
                    start: parseInt(elementSettings.default_tab_index),
                    autoChange: elementSettings.autochange,
                    delay: elementSettings.autochange_delay,
                    list: this.elements.$navListItem,
                    carousel: elementSettings.carousel_tabs,
                    accordion: elementSettings.accordion_tabs,
                    tabColor: elementSettings.premium_tab_background_color,
                    activeTabColor: elementSettings.premium_tab_active_background_color
                };

                //apply outline on the tabs
                if (this.settings.activeTabColor) {
                    if ('00' === this.settings.activeTabColor.slice(-2)) {

                        var tabsType = elementSettings.premium_tab_type,
                            borderSide = 'horizontal' === tabsType ? "top" : elementorFrontend.config.is_rtl ? "right" : "left";

                        borderSide += "-color";

                        $premiumTabsElem.find(".premium-tab-arrow").css("border-" + borderSide, "#fff");
                    }
                }

                if (this.settings.carousel) {
                    if (-1 === elementSettings.carousel_tabs_devices.indexOf(currentDevice)) {
                        this.settings.carousel = false;
                        $premiumTabsElem.removeClass("elementor-invisible");
                    }


                    //Make sure slick is initialized before showing tabs.
                    $navList.on('init', function () {
                        $premiumTabsElem.removeClass("elementor-invisible");
                    });

                    elementorFrontend.waypoint(
                        $navList,
                        function () {
                            $navList.slick(_this.getSlickSettings());
                        }
                    );


                    $navList.on('click', selectors.navListItem, function () {
                        var tabIndex = $(this).data("slick-index");
                        $navList.slick('slickGoTo', tabIndex);
                    });

                    //activate tab only if carousel tabs number is odd.
                    $navList.on("afterChange", function () {
                        var numberOfTabs = _this.getCarouselTabs();

                        if (1 === numberOfTabs % 2)
                            $navList.find(".premium-tabs-nav-list-item.slick-current").trigger('click');
                    });

                    //Fix arrow pointer not showing on small screens.
                    if ('style1' === elementSettings.premium_tab_style_selected) {

                        setTimeout(function () {
                            var navListHeight = $navList.find(".slick-list").eq(0).outerHeight();
                            $navList.find(".slick-list").outerHeight(navListHeight + 10);
                        }, 2500);

                    }

                }

                if (this.settings.accordion && elementSettings.accordion_tabs_devices.includes(currentDevice)) {
                    this.$element.find('.premium-tabs, .premium-tabs-nav-list').addClass('premium-accordion-tabs');
                    this.elements.$contentWrap.css('display', 'none');
                } else {
                    new CBPFWTabs($premiumTabsElem, this.settings);
                }

                $(document).ready(function () {

                    if (_this.settings.accordion && elementSettings.accordion_tabs_devices.includes(currentDevice)) {

                        $premiumTabsElem.find(".premium-content-wrap").remove();
                        _this.changeToAccodrion();

                    } else {
                        //Check this as it conflicts with Elementor swiper.
                        // if ($premiumTabsElem.find(".swiper-container").length < 1) {
                        //The comment removed because it will conflict if the swiper is a media carousel and the images are anchors.
                        $premiumTabsElem.find(".premium-accordion-tab-content").remove();
                        // }

                    }

                    navigation.map(function (item, index) {
                        if (item) {
                            $(item).on("click", function () {

                                if (!_this.settings.carousel) {
                                    var $tabToActivate = $navList.find("li.premium-tabs-nav-list-item").eq(index);

                                    $tabToActivate.trigger('click');
                                } else {
                                    var activeTabHref = _this.elements.$contentWrap.find("section").eq(index).attr("id");

                                    $("a[href=#" + activeTabHref + "]").eq(1).trigger('click');

                                    _this.$element.find(".slick-current").trigger('click');
                                }

                            });
                        }
                    });
                });

            },

            getCarouselTabs: function () {
                var elementSettings = this.getElementSettings(),
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    numberofTabs = 5;

                switch (true) {
                    case currentDevice.includes('mobile'):
                        numberofTabs = elementSettings.tabs_number_mobile;
                        break;
                    case currentDevice.includes('tablet'):
                        numberofTabs = elementSettings.tabs_number_tablet;
                        break;
                    default:
                        numberofTabs = elementSettings.tabs_number;
                        break;
                }

                return numberofTabs;

            },

            getSlickSettings: function () {

                var elementSettings = this.getElementSettings(),
                    prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                    nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>',
                    slides_tab = elementSettings.tabs_number_tablet,
                    slides_mob = elementSettings.tabs_number_mobile,
                    spacing_tab = elementSettings.slides_spacing_tablet,
                    spacing_mob = elementSettings.slides_spacing_mobile;

                return {
                    infinite: true,
                    autoplay: false,
                    rows: 0,
                    dots: false,
                    useTransform: true,
                    centerMode: true,
                    draggable: false,
                    slidesToShow: elementSettings.tabs_number || 5,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: slides_tab,
                            centerPadding: spacing_tab + "px",
                            nextArrow: elementSettings.carousel_arrows_tablet ? nextArrow : '',
                            prevArrow: elementSettings.carousel_arrows_tablet ? prevArrow : '',
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: slides_mob,
                            centerPadding: spacing_mob + "px",
                            nextArrow: elementSettings.carousel_arrows_mobile ? nextArrow : '',
                            prevArrow: elementSettings.carousel_arrows_mobile ? prevArrow : '',
                        }
                    }
                    ],
                    rtl: elementorFrontend.config.is_rtl,
                    nextArrow: elementSettings.carousel_arrows ? nextArrow : '',
                    prevArrow: elementSettings.carousel_arrows ? prevArrow : '',
                    centerPadding: elementSettings.slides_spacing + "px",
                }

            },

            changeToAccodrion: function () {

                var $premiumTabsElem = this.elements.$premiumTabsElem,
                    _this = this,
                    $navListItem = this.elements.$navListItem,
                    elementSettings = this.getElementSettings(),
                    selectors = this.getSettings('selectors'),
                    accDur = elementSettings.accordion_tabs_anim_duration,
                    scrollAfter = elementSettings.accordion_animation;

                if (-1 !== this.settings.start) {
                    $premiumTabsElem.find(".premium-tabs-nav-list-item:eq(" + this.settings.start + ")").addClass("tab-current");
                }

                var contentId = $premiumTabsElem.find(selectors.currentTab).data('content-id');

                $premiumTabsElem.find('.premium-accordion-tab-content:not(' + contentId + ')').slideUp(accDur, 'swing');

                $navListItem.click(function () {

                    var $clickedTab = $(this);

                    if ($clickedTab.hasClass(selectors.currentClass)) {

                        $clickedTab.toggleClass(selectors.currentClass);

                        _this.$element.find($clickedTab.data('content-id')).slideUp(accDur, 'swing');

                    } else {

                        $navListItem.removeClass(selectors.currentClass);

                        $clickedTab.toggleClass(selectors.currentClass);

                        _this.$element.find(selectors.tabContent).slideUp(accDur, 'swing');

                        _this.$element.find($clickedTab.data('content-id')).slideDown(accDur, 'swing');
                    }

                    if (scrollAfter) {
                        setTimeout(function () {
                            $('html, body').animate({
                                scrollTop: $clickedTab.offset().top - 100
                            }, 1000);
                        }, accDur === 'slow' ? 700 : 400);
                    }

                });

            }

        });

        window.CBPFWTabs = function (t, settings) {

            var self = this,
                id = settings.id,
                i = settings.start,
                isClicked = false,
                canNavigate = true;

            self.el = t;

            self.options = {
                start: i
            };

            self.extend = function (t, s) {
                for (var i in s) s.hasOwnProperty(i) && (t[i] = s[i]);
                return t;
            };

            self._init = function () {

                self.tabs = $(id).find(".premium-tabs-nav").first().find("li.premium-tabs-nav-list-item");
                self.items = $(id).find(".premium-content-wrap").first().find("> section");
                self.carouselTabs = $(id).find('.premium-tabs-nav-list').first();

                self.current = -1;

                //No tabs will be active by default.
                if (-1 !== self.options.start) {
                    self._show();
                }

                self._initEvents();

                if (settings.autoChange) {
                    self.runAutoNavigation();
                }

                if (settings.carousel) {
                    self.carouselTabs.on('beforeChange', function () {
                        canNavigate = false;
                    });

                    self.carouselTabs.on('afterChange', function () {
                        canNavigate = true;
                    });

                }
            };

            self._initEvents = function () {

                self.tabs.each(function (index, tab) {

                    var listIndex = $(tab).data("list-index");

                    $(tab).find("a.premium-tab-link").on("click", function (s) {
                        s.preventDefault();
                    });

                    $(tab).on("click", function (s) {

                        //If a tab is clicked by the user, not auto navigation
                        if (s.originalEvent)
                            isClicked = true;

                        s.preventDefault();

                        //If tab is clicked twice
                        if ($(tab).hasClass("tab-current"))
                            return;

                        self._show(listIndex, tab);
                    });
                });
            };

            self._show = function (tabIndex, tab) {

                if (!canNavigate)
                    return;

                if (self.current >= 0) {
                    self.tabs.removeClass("tab-current");
                    self.items.removeClass("content-current");
                }

                self.current = tabIndex;

                //Activate the first tab if no tab is clicked yet
                if (void 0 == tabIndex) {
                    self.current = self.options.start >= 0 && self.options.start < self.items.length ? self.options.start : 0;
                    tab = settings.carousel ? self.tabs.filter(".slick-center:not(.slick-cloned)") : self.tabs[self.current];
                }

                self.tabs.removeClass("premium-zero-height");

                if (settings.carousel) {

                    setTimeout(function () {
                        var $currentActivetab = self.tabs.filter(".slick-center:not(.slick-cloned)");
                        $currentActivetab.addClass("tab-current");

                        //Hide separator for arrow pointer if active background is set
                        if (settings.tabColor !== settings.activeTabColor && 'undefined' != typeof settings.activeTabColor) {
                            $currentActivetab.addClass("premium-zero-height");
                            $currentActivetab.prev().addClass("premium-zero-height");
                        }

                    }, 100);
                } else {
                    $(tab).addClass("tab-current");

                    //Hide separator for arrow pointer if active background is set
                    if (settings.tabColor !== settings.activeTabColor && 'undefined' != typeof settings.activeTabColor) {

                        $(tab).prevAll('.premium-tabs-nav-list-item').first().addClass("premium-zero-height");
                        $(tab).addClass("premium-zero-height");
                    }

                }

                var $activeContent = self.items.eq(self.current);

                $activeContent.addClass("content-current");

                if ($('.premium-mscroll-yes').length < 1 && $('.premium-hscroll-outer-wrap').length < 1)
                    window.dispatchEvent(new Event('resize'));

                //Fix Media Grid height issue.
                if ($activeContent.find(".premium-gallery-container").length > 0) {
                    var $mediaGrid = $activeContent.find(".premium-gallery-container"),
                        layout = $mediaGrid.data("settings").img_size;
                    setTimeout(function () {

                        // if (0 === $mediaGrid.outerHeight()) {
                        if ('metro' === layout) {
                            $mediaGrid.trigger('resize');
                        } else {
                            $mediaGrid.isotope("layout");
                        }
                        // }

                    }, 100);

                }


                self.items.find(".slick-slider").slick('pause').slick('freezeAnimation');

                if ($activeContent.find(".slick-slider").length > 0) {

                    setTimeout(function () {

                        $activeContent.find(".slick-slider").slick("play").slick("resumeAnimation");

                        $activeContent.find(".slick-slider").slick('setPosition').slick('setPosition');
                    }, 100);

                }

                //Make sure videos are paused
                if (self.items.find("video").length > 0) {
                    self.items.not(".content-current").find("video").each(function (index, elem) {
                        $(elem).get(0).pause();
                    });
                }

                if (self.items.find("iframe").length > 0) {
                    self.items.not(".content-current").find("iframe").each(function (index, elem) {

                        var source = $(elem).parent().attr("data-src");

                        $(elem).attr("src", source);
                    });
                }

            };

            self.runAutoNavigation = function () {

                var $navListItem = settings.list,
                    index = settings.start > 0 ? settings.start : 0;

                var autoChangeInterval = setInterval(function () {

                    //If user clicks a tab, then stop auto navigation.
                    if (isClicked) {
                        clearInterval(autoChangeInterval);
                        return;
                    }

                    index++;

                    if (index > $navListItem.length - 1)
                        index = 0;

                    $navListItem.eq(index).trigger('click');
                }, settings.delay * 1000);

            };

            self.options = self.extend({}, self.options);
            self.extend(self.options, i);
            self._init();

        };

        elementorFrontend.elementsHandler.attachHandler('premium-addon-tabs', PremiumTabsHandler);
    });
})(jQuery);