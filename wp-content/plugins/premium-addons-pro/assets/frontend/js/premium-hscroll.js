(function ($) {

    //Fix issues with lock screen on mobile devices.
    ScrollTrigger.config({
        limitCallbacks: true,
        ignoreMobileResize: true
    });

    $(window).on('elementor/frontend/init', function () {
        var PremiumHorizontalScrollHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {
                return {
                    selectors: {
                        hScrollElem: '.premium-hscroll-wrap',
                        sectionWrap: '.premium-hscroll-sections-wrap',
                        hscrollTemp: '.premium-hscroll-temp',
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $hScrollElem: this.$element.find(selectors.hScrollElem),
                    };

                elements.$sectionWrap = elements.$hScrollElem.find(selectors.sectionWrap);
                elements.$hscrollTemp = elements.$hScrollElem.find(selectors.hscrollTemp);

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $scope = this.$element,
                    $hScrollElem = this.elements.$hScrollElem,
                    hScrollSettings = $hScrollElem.data("settings"),
                    instance = null,
                    templates = this.getElementSettings('section_repeater');

                if (!templates.length) return;

                templates.forEach(function (template) {

                    if ("id" === template.template_type && "" !== template.section_id) {
                        if ($("#" + template.section_id).length == 0) {
                            $hScrollElem.html(
                                '<div class="premium-error-notice"><span>Section with ID <b>' +
                                template.section_id +
                                "</b> does not exist on this page. Please make sure that section ID is properly set from section settings -> Advanced tab -> CSS ID.<span></div>"
                            );
                            return;
                        }
                    }
                });

                instance = new premiumHorizontalScroll($scope, hScrollSettings, this.getElementSettings());
                instance.checkDisableOnOption();

            },

        });

        window.premiumHorizontalScroll = function ($scope, settings, controlSettings) {

            var self = this,
                $elem = $scope.find('.premium-hscroll-wrap'),
                id = settings.id,
                count = controlSettings.section_repeater.length,
                editMode = elementorFrontend.isEditMode(),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                progressOffset = 300,
                currentActiveArr = [],
                currentActive = 0,
                prevActive = -1,
                loop = controlSettings.loop,
                entrance = controlSettings.entrance_animation,
                entranceOnce = controlSettings.trigger_animation_once,
                snapScroll = 'snap' === controlSettings.scroll_effect,
                isScrolling = false,
                scene = null,
                offset = null,
                timeline = null,
                rtlMode = controlSettings.rtl_mode,
                dimensions = null,
                isActive = false,
                state = null;

            $elem.find(".premium-hscroll-temp").each(function (index, template) {

                var hideOn = $(template).data('hide');

                if (-1 < hideOn.indexOf(currentDevice)) {
                    hideSection(template, index);
                }

            });

            function hideSection(template, index) {

                if (0 !== count) {
                    count--;
                    $(template).remove();

                    $elem.find('.premium-hscroll-bg-layer').eq(index).remove();
                    $elem.find('.premium-hscroll-total-slides').html(count > 9 ? count : ('0' + count));
                    $elem.find('.premium-hscroll-nav-item[data-slide="section_' + id + index + '"]').remove();
                }

                if (0 === count) {
                    $elem.find('.premium-hscroll-arrow, .premium-hscroll-nav, .premium-hscroll-pagination').remove();
                }

                if (settings.opacity) {
                    $elem.find(".premium-hscroll-temp:first").removeClass("premium-hscroll-hide");
                }

            }

            var $slides = $elem.find(".premium-hscroll-temp");

            if (settings.opacity)
                var targetIndex = 0;

            if (rtlMode)
                targetIndex = count - 1;


            if (-1 !== currentDevice.indexOf('tablet') && -1 !== currentDevice.indexOf('mobile')) {
                if (snapScroll && settings.disableSnap) {
                    snapScroll = false;
                    entrance = false;
                }
                if (['tablet', 'tablet_extra'].includes(currentDevice)) {
                    progressOffset = 100;
                } else if (['mobile', 'mobile_extra'].includes(currentDevice)) {
                    progressOffset = 50;
                }
            } else if (snapScroll) {
                progressOffset = 30;
            }

            var $nav = $(".premium-hscroll-nav-item", $elem),
                $arrows = $(".premium-hscroll-wrap-icon", $elem);

            self.checkDisableOnOption = function () {

                var disableOn = controlSettings.disable_on;

                if (disableOn.includes(elementorFrontend.getCurrentDeviceMode())) {

                    $elem.find('.premium-hscroll-arrow, .premium-hscroll-progress, .premium-hscroll-nav, .premium-hscroll-pagination').remove();

                    $elem.find(".premium-hscroll-temp").each(function (index, slide) {
                        $(slide).removeClass('premium-hscroll-temp');
                    });

                    $elem.find('.premium-hscroll-sections-wrap').removeClass('premium-hscroll-sections-wrap');

                    return;
                }

                self.init();

            }

            self.init = function () {

                if (!count) return;

                self.setLayout();

                self.setSectionsData();

                self.handleAnimations();

                self.setScene();

                if (!loop) self.checkActive();

                $nav.on("click.premiumHorizontalScroll", self.onNavDotClick);

                $arrows.on("click.premiumHorizontalScroll", self.onNavArrowClick);

                self.checkRemoteAnchors();

                self.checkLocalAnchors();

                $(document).on('elementor/popup/show', function () {
                    self.checkLocalAnchors();
                });


                if (snapScroll)
                    document.addEventListener("wheel", self.onScroll, { passive: false });


                //Keyboard Scrolling.
                document.addEventListener("keydown", self.onKeyboardPress);


                if (snapScroll && document.body.scrollHeight > 3000) {

                    // var windowOuterHeight = $(window).outerHeight();

                    //After page reload, check if the spacing between page scroll and offset top of Hscroll is lower than 150. If so, then return.
                    // if (offset - windowOuterHeight < 150)
                    //     return;

                    if (0 === currentActive) {
                        elementorFrontend.waypoint(
                            $elem,
                            function (direction) {
                                if ("down" === direction) {
                                    self.scrollToSlide(0, 'waypoint');
                                }
                            }, {
                            offset: 150,
                            triggerOnce: false
                        }
                        );
                    }

                }
            };

            self.checkLocalAnchors = function () {

                $("a").on("click", function () {

                    var href = $(this).attr("href");

                    if (href) {

                        href = href.replace('#/', '');

                        self.checkAnchors(href);
                    }

                });

            }

            self.checkRemoteAnchors = function () {

                var url = new URL(window.location.href);

                if (!url)
                    return;

                var slideID = url.searchParams.get("slide");

                if (slideID)
                    self.checkAnchors(slideID);

            };

            self.checkAnchors = function (href) {

                var $slide = $elem.find(".premium-hscroll-temp[data-section='" + href + "']");

                if (!$slide.length)
                    return;

                var slideIndex = $slide.index();

                self.scrollToSlide(slideIndex, "anchors");

            };

            self.onKeyboardPress = function (e) {

                //If Keyboard scrolling is disabled, then use the default browser scrolling.
                if (!settings.keyboard) {
                    // e.preventDefault();
                    return;
                }

                self.getState();

                if ("BEFORE" === state) {
                    return;
                } else {
                    var downKeyCodes = [40, 34],
                        upKeyCodes = [38, 33];

                    if ("AFTER" === state) {
                        if (-1 !== $.inArray(e.keyCode, upKeyCodes)) {
                            var lastScrollOffset = self.getScrollOffset(
                                $slides.eq(count - 1)
                            );

                            if (
                                e.pageY - lastScrollOffset <= 300 &&
                                e.pageY - lastScrollOffset > 100
                            ) {

                                self.preventDefault(event);
                                self.scrollToSlide(count - 1);


                            } else if (e.pageY - lastScrollOffset < 100) {

                                self.preventDefault(event);
                                self.scrollToSlide(count - 2);
                            }

                            return;
                        }
                    } else {

                        if (-1 !== $.inArray(e.keyCode, downKeyCodes)) {
                            if (isScrolling) {
                                self.preventDefault(event);
                                return;
                            }

                            self.goToNext();
                        }


                        if (-1 !== $.inArray(e.keyCode, upKeyCodes)) {
                            if (isScrolling) {
                                self.preventDefault(event);
                                return;
                            }

                            self.goToPrev("keyboard");
                        }
                    }
                }

            };

            self.getResponsiveControlValue = function (ID) {

                var value = controlSettings[ID];

                if ('desktop' !== currentDevice) {
                    value = controlSettings[ID + '_' + currentDevice];
                }

                if ('scroll_speed' === ID) {

                    value = !value ? 1 : value;

                } else {
                    value = !value || parseFloat(('' === value.size || undefined === value) ? self.getControlDefaultVal(ID) : value.size);
                }

                return value;

            };

            self.getControlDefaultVal = function (ID) {

                return ['distance', 'trigger_offset'].includes(ID) ? 0 : 1;
            };

            self.setScene = function () {

                var scrollSpeed = self.getResponsiveControlValue('scroll_speed');

                //                 if (['desktop', 'laptop', 'widescreen'].includes(currentDevice)) {
                scrollSpeed = scrollSpeed * 100 + "%";
                //                 } else {
                //                     scrollSpeed = scrollSpeed * $elem.outerHeight();
                //                 }

                //To fix trigger position when there are more than one column in the parent section.
                $scope.closest('.elementor-column').css('align-self', 'flex-start');

                timeline = gsap.timeline({
                    id: 'timeline' + id,
                    onUpdate: function () {
                        self.onProgress();
                    },
                    scrollTrigger: {
                        trigger: '#premium-hscroll-wrap-' + id,
                        pin: true,
                        start: 'top top',
                        end: scrollSpeed,
                        scrub: controlSettings.scrub ? 1.3 : 0,
                        onToggle: function () {
                            isActive = !isActive;
                        },
                    },
                });

                //Make sure spacer '.premium-hscroll-spacer' is set to content-box
                setTimeout(function () {
                    self.setHorizontalSlider();
                }, 200);

            };

            self.getDimensions = function () {

                var firstWidth = $slides.eq(0).innerWidth(),
                    distance = firstWidth * (count - 1),
                    progressWidth = firstWidth * count;

                var slidesInViewPort = self.getResponsiveControlValue('slides'),
                    distanceBeyond = self.getResponsiveControlValue('distance');

                distance = distance - (1 - 1 / slidesInViewPort) * $elem.outerWidth();

                distance = distanceBeyond + distance;

                return {
                    distance: distance,
                    progressBar: progressWidth
                };

            };

            self.setHorizontalSlider = function (progress) {

                dimensions = self.getDimensions();

                var fromOrTo = rtlMode ? 'from' : 'to';

                if ('tablet' === currentDevice && self.checkIpad() && !rtlMode) {
                    timeline.to("#premium-hscroll-scroller-wrap-" + id, 1, { left: rtlMode ? "0px" : -dimensions.distance, ease: Power0.easeNone }, 0)
                } else {
                    timeline[fromOrTo]("#premium-hscroll-scroller-wrap-" + id, 1, { x: -dimensions.distance, ease: Power0.easeNone }, 0);
                }

                timeline.to("#premium-hscroll-progress-line-" + id, 1, { width: dimensions.progressBar + "px", ease: Power0.easeNone }, 0);

                if ($scope.hasClass('custom-scroll-bar')) {

                    $elem.append('<div class="horizontal-content-scroller"><span></span></div>');

                    var progressWrap = $(".horizontal-content-scroller").outerWidth(),
                        progressBarSpan = $(".horizontal-content-scroller span").outerWidth();

                    var progressBarTransform = progressWrap - progressBarSpan;

                    timeline.to('.horizontal-content-scroller span', 1, { x: progressBarTransform }, 0);
                }

                if ('undefined' !== typeof progress) {
                    scene.progress(0);
                    scene.update(true);
                }

            }

            self.checkIpad = function () {
                return /Macintosh/.test(navigator.userAgent) && 'ontouchend' in document;
            };

            //Remove Fit to Screen to be replaced with min-height: 100vh
            self.setLayout = function () {
                $elem.closest("section.elementor-section-height-full").removeClass("elementor-section-height-full");
            };

            self.setSectionsData = function () {

                var slidesInViewPort = self.getResponsiveControlValue('slides'),
                    slideWidth = 100 / slidesInViewPort;

                $elem.find(".premium-hscroll-slider").css("width", count * slideWidth + "%");

                $elem.find(".premium-hscroll-temp").css("width", 100 / count + "%");

                // Will change to scroll_speed.
                var scrollSpeed = self.getResponsiveControlValue('scroll_speed');

                var width = parseFloat(
                    $elem.find(".premium-hscroll-sections-wrap").width() / count),
                    winHeight = window.innerHeight * scrollSpeed + self.getResponsiveControlValue('trigger_offset');

                $slides.each(function (index, template) {

                    var sectionType = 'template' === controlSettings.section_repeater[index].template_type;

                    if ($(template).data("section") && !sectionType) {
                        var id = $(template).data("section");

                        self.getSectionContent(id);
                    }

                    var position = index * width;

                    $(template).attr("data-position", position);
                });

                offset = $elem.offset().top;

                $slides.each(function (index, template) {

                    var scrollOffset = (index * (winHeight)) / (count - 1);

                    if (!['widescreen', 'desktop', 'laptop'].includes(currentDevice) && 0 != index) {

                        // check if iOS.
                        var ios = /iP(hone|ad|od)/i.test(navigator.userAgent) && !window.MSStream,
                            ios = ios || self.checkIpad();

                        if (ios) { // iOS device.

                            var allowedBrowser = /(Chrome|CriOS|OPiOS|FxiOS)/.test(navigator.userAgent);

                            if (!allowedBrowser) {
                                var isFireFox = '' === navigator.vendor;
                                allowedBrowser = allowedBrowser || isFireFox;
                            }

                            var isSafari = /WebKit/i.test(navigator.userAgent) && !allowedBrowser;

                            if ('mobile' === currentDevice) {

                                scrollOffset = self.getTouchScrollOffset(index, isSafari ? 80 : 100, scrollSpeed, count);

                            } else {

                                scrollOffset = self.getTouchScrollOffset(index, isSafari ? 30 : 80, scrollSpeed, count);

                            }

                        } else { // Android.
                            scrollOffset = self.getTouchScrollOffset(index, 60, scrollSpeed, count);
                        }
                    }

                    $(template).attr("data-scroll-offset", offset + scrollOffset);
                });
            };

            /**
             * Calculate scroll offset value for touch devices with the address bar height
             * and trigger offset option taken into account.
             *
             * @param {int} addressBar approximate height of address/nav bar on touch devices.
             */
            self.getTouchScrollOffset = function (index, addressBar, scrollSpeed, count) {

                var triggerOffset = self.getResponsiveControlValue('trigger_offset');

                return (index * (($(window).innerHeight() + addressBar + triggerOffset) * scrollSpeed)) / (count - 1);
            };

            self.onScroll = function (event) {
                if (isScrolling && null !== event) self.preventDefault(event);

                var delta = self.getDirection(event),
                    direction = 0 > delta ? "down" : "up";

                self.getState();

                if ("up" === direction && "AFTER" === state && document.body.scrollHeight > 3000) {
                    var lastScrollOffset = self.getScrollOffset(
                        $slides.eq(count - 1)
                    );

                    if (
                        window.pageYOffset - lastScrollOffset <= 300 &&
                        window.pageYOffset - lastScrollOffset > 100
                    )
                        self.scrollToSlide(count - 1, 'waypoint');
                }

                if (isActive) {

                    if ("down" === direction) {
                        if (!isScrolling && count - 1 !== currentActive) {
                            self.goToNext();
                        }
                    } else if ("up" === direction) {
                        if (!isScrolling && 0 !== currentActive) self.goToPrev();
                    }

                    if (
                        (0 !== currentActive && "up" === direction) || ("down" === direction && count - 1 !== currentActive)
                    ) {
                        self.preventDefault(event);
                    }
                }
            };

            self.getDirection = function (e) {
                e = window.event || e;
                var t = Math.max(
                    -1,
                    Math.min(1, e.wheelDelta || -e.deltaY || -e.detail)
                );
                return t;
            };

            self.refresh = function () {

                setTimeout(function () {
                    var sceneProgress = scene.progress();
                    self.setHorizontalSlider(sceneProgress);
                }, 200);

            };

            self.getState = function () {

                if ($('.one-hscroll').length < 1) {

                    switch (true) {
                        case timeline.scrollTrigger.progress < 0.01:
                            state = 'BEFORE';
                            break;

                        case timeline.scrollTrigger.progress > 0.99:
                            state = 'AFTER';
                            break;

                        case timeline.scrollTrigger.progress > 0.01 && timeline.scrollTrigger.progress < 0.99:
                            state = 'DURING';
                            break;
                    }

                } else {
                    state = 'DURING';
                }

            };

            self.onProgress = function () {

                var progressFillWidth = $elem.find(".premium-hscroll-progress-line").outerWidth(),
                    elemWidth = $elem.outerWidth();

                $slides.each(function (index) {

                    var scrollOffset = $slides.eq(index - 1).data("scroll-offset"),
                        scrollPosition = $(this).data("position");

                    if (settings.opacity && targetIndex !== index) {

                        if (window.pageYOffset >= scrollOffset + elemWidth / 8) {
                            $(this).removeClass("premium-hscroll-hide");
                        } else {
                            $(this).addClass("premium-hscroll-hide");
                        }

                    }

                    if (progressFillWidth >= scrollPosition - progressOffset) {

                        if (entrance && !isScrolling)
                            self.triggerAnimations();

                        if (-1 === currentActiveArr.indexOf(index)) {

                            currentActiveArr.push(index);

                            currentActive = index;
                            self.onSlideChange();
                        }

                    } else {

                        if (-1 !== currentActiveArr.indexOf(index)) {
                            currentActiveArr.pop();

                            currentActive = currentActiveArr[currentActiveArr.length - 1];
                            self.onSlideChange();
                        }

                    }
                });
            };

            self.onSlideChange = function () {

                prevActive = currentActive;

                self.addBackgroundLayer();

                if (settings.pagination && !snapScroll) {

                    var text = currentActive + 1 > 9 ? "" : "0";
                    $elem
                        .find(".premium-hscroll-current-slide")
                        .text(text + (currentActive + 1));
                }

                $nav.removeClass("active");

                $elem
                    .find(".premium-hscroll-nav-item")
                    .eq(currentActive)
                    .addClass("active");

                self.checkActive();

                if (entrance && !isScrolling)
                    self.restartAnimations(currentActive);
            };

            self.addBackgroundLayer = function () {

                if ($elem.find(".premium-hscroll-bg-layer").eq(currentActive).length > 0) {

                    $elem.find(".premium-hscroll-layer-active").removeClass("premium-hscroll-layer-active");

                    $elem.find(".premium-hscroll-bg-layer").eq(currentActive).addClass("premium-hscroll-layer-active");

                }



            };

            self.getSectionContent = function (sectionID) {
                if (!$("#" + sectionID).length)
                    return;

                var htmlContent = $("#" + sectionID);

                if (!editMode) {
                    $("#premium-hscroll-scroller-wrap-" + id)
                        .find('div[data-section="' + sectionID + '"]')
                        .append(htmlContent);
                } else {
                    $slides.find(".elementor-element-overlay")
                        .remove();
                    $("#premium-hscroll-scroller-wrap-" + id)
                        .find('div[data-section="' + sectionID + '"]')
                        .append(htmlContent.clone(true));
                }
            };

            self.checkActive = function () {
                if (!$arrows.length) return;

                if (loop) {
                    if (-1 === currentActive) {
                        currentActive = count - 1;
                    } else if (count === currentActive) {
                        currentActive = 0;
                    }
                } else {
                    if (0 === currentActive) {
                        $elem
                            .find(".premium-hscroll-arrow-left")
                            .addClass("premium-hscroll-arrow-hidden");
                    } else {
                        $elem
                            .find(".premium-hscroll-arrow-left")
                            .removeClass("premium-hscroll-arrow-hidden");
                    }

                    if (count - 1 === currentActive) {
                        $elem
                            .find(".premium-hscroll-arrow-right")
                            .addClass("premium-hscroll-arrow-hidden");
                    } else {
                        $elem
                            .find(".premium-hscroll-arrow-right")
                            .removeClass("premium-hscroll-arrow-hidden");
                    }
                }

            };

            self.onNavDotClick = function () {
                if (isScrolling) return;

                var $item = $(this),
                    index = $item.index();

                if (index === prevActive && isActive) return;

                currentActive = index;

                self.scrollToSlide(index);
            };

            self.onNavArrowClick = function (e) {
                if (isScrolling) return;

                if ($(e.target).closest(".premium-hscroll-arrow-left").length) {
                    self.goToPrev();
                } else if ($(e.target).closest(".premium-hscroll-arrow-right").length) {
                    self.goToNext();
                }

            };

            self.goToNext = function () {
                if (isScrolling) return;

                currentActive++;

                if (loop) {
                    if (-1 === currentActive) {
                        currentActive = count - 1;
                    } else if (count === currentActive) {
                        currentActive = 0;
                    }
                }

                self.scrollToSlide(currentActive);
            };

            self.goToPrev = function (trigger) {

                if (isScrolling || ("keyboard" === trigger && currentActive === 0))
                    return;

                currentActive--;

                if (loop) {
                    if (-1 === currentActive) {
                        currentActive = count - 1;
                    } else if (count === currentActive) {
                        currentActive = 0;
                    }
                }

                self.scrollToSlide(currentActive);
            };

            self.scrollToSlide = function (slideIndex, scrollSrc) {

                var targetOffset = self.getScrollOffset($slides.eq(slideIndex));

                if (!scrollSrc) {
                    if (isScrolling) return;
                } else if ('waypoint' === scrollSrc) {
                    targetOffset = targetOffset + (0 == slideIndex ? 2 : -1);
                }

                if (0 > currentActive || count - 1 < currentActive) return;

                isScrolling = true;

                prevActive = slideIndex;

                var spacerHeight = $("#premium-hscroll-spacer-" + id).outerHeight();

                if (!snapScroll) {

                    gsap.to(window, 1.5, {
                        scrollTo: targetOffset - spacerHeight,
                        ease: Power3.easeOut,
                        onComplete: self.afterSlideChange
                    });

                } else {

                    $("html, body").stop().clearQueue().animate({
                        scrollTop: targetOffset
                    }, 1000, function () {
                        var currentX = gsap.getProperty("#premium-hscroll-scroller-wrap-" + id, 'x'),
                            leftOffset = $slides.eq(slideIndex)[0].getBoundingClientRect().x;

                        setTimeout(function () {
                            gsap.set("#premium-hscroll-scroller-wrap-" + id, {
                                x: currentX - leftOffset
                            });
                        }, 30);

                    });

                }

                if (settings.pagination && snapScroll)
                    $elem.find(".premium-hscroll-current-slide").removeClass("zoomIn animated");

                if (settings.pagination && snapScroll) {
                    setTimeout(function () {

                        if (
                            currentActive + 1 !=
                            $elem.find(".premium-hscroll-current-slide")
                                .text()
                        ) {
                            //Lead zero
                            var text = currentActive + 1 > 9 ? "" : "0";
                            $elem
                                .find(".premium-hscroll-current-slide")
                                .text(text + (currentActive + 1))
                                .addClass("zoomIn animated");
                        }
                    }, 1000);
                }

                if (entrance && !entranceOnce) {

                    setTimeout(function () {
                        self.setAnimations();
                    }, 1000);
                }

                if (snapScroll) {
                    setTimeout(function () {
                        isScrolling = false;
                    }, 1200);
                }
            };

            self.afterSlideChange = function () {
                isScrolling = false;
            };

            self.handleAnimations = function () {

                if (entranceOnce)
                    return;

                if (entrance) {

                    self.hideAnimations();

                    elementorFrontend.waypoint($elem, function () {
                        self.setAnimations();
                    });
                } else {
                    self.unsetAnimations();
                }
            };

            self.hideAnimations = function () {

                $slides.find(".elementor-invisible").addClass("premium-hscroll-elem-hidden");

            };

            self.unsetAnimations = function () {

                $slides.not(":eq(0)").find(".elementor-invisible").each(function (index, elem) {

                    $(elem).removeClass("elementor-invisible");

                    var dataSettings = $(elem).data("settings");

                    if (dataSettings) {

                        delete dataSettings._animation;

                        delete dataSettings.animation;

                        $(elem).attr("data-settings", JSON.stringify(dataSettings));

                    }

                });

            };

            self.setAnimations = function () {

                self.restartAnimations();

                self.triggerAnimations();
            };

            self.restartAnimations = function (slideIndex) {
                var $unactiveSlides = $slides.filter(function (index) {
                    return index !== slideIndex;
                });

                $unactiveSlides.find(".animated")
                    .each(function (index, elem) {
                        var settings = $(elem)
                            .data("settings");

                        if (undefined === settings) return;

                        var animation = settings._animation || settings.animation;

                        $(elem)
                            .removeClass("animated " + animation)
                            .addClass("elementor-invisible");
                    });
            };

            self.triggerAnimations = function () {

                $slides.eq(currentActive).find(".elementor-invisible, .premium-hscroll-elem-hidden").each(function (index, elem) {
                    var settings = $(elem)
                        .data("settings");

                    if (undefined === settings) return;

                    if (!settings._animation && !settings.animation) return;

                    var delay = settings._animation_delay ? settings._animation_delay : 0,
                        animation = settings._animation || settings.animation;

                    setTimeout(function () {
                        $(elem)
                            .removeClass("elementor-invisible premium-hscroll-elem-hidden")
                            .addClass(animation + " animated");
                    }, delay);
                });
            };

            self.getScrollOffset = function (item) {

                if (!$(item).length)
                    return;

                var slideOffset = $(item).data("scroll-offset");

                if ($("#upper-element").length > 0) {
                    slideOffset = slideOffset + $("#upper-element").closest(".premium-notbar-outer-container").outerHeight();
                    $(item).attr("data-scroll-offset", slideOffset);
                }

                return slideOffset;
            };

            self.preventDefault = function (event) {
                if (event.preventDefault) {
                    event.preventDefault();
                } else {
                    event.returnValue = false;
                }
            };
        };

        elementorFrontend.elementsHandler.attachHandler('premium-hscroll', PremiumHorizontalScrollHandler);

    });

})(jQuery);
