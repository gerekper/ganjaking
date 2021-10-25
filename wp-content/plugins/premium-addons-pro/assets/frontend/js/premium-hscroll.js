(function ($) {
    /****** Premium Horizontal Scroll Handler ******/
    var PremiumHorizontalScrollHandler = function ($scope, $) {
        var $hScrollElem = $scope.find(".premium-hscroll-wrap"),
            hScrollSettings = $hScrollElem.data("settings"),
            instance = null,
            disableOn = hScrollSettings.disableOn;

        var templates = hScrollSettings.templates;

        if (!templates.length) return;

        templates.forEach(function (template) {

            if ("id" === template.template_type && "" !== template.section_id) {
                if (!$("#" + template.section_id)
                    .length) {
                    $hScrollElem.html(
                        '<div class="premium-error-notice"><span>Section with ID <b>' +
                        template.section_id +
                        "</b> does not exist on this page. Please make sure that section ID is properly set from section settings -> Advanced tab -> CSS ID.<span></div>"
                    );
                    return;
                }
            }
        });

        if (disableOn.includes(elementorFrontend.getCurrentDeviceMode())) {
            $hScrollElem.find('.premium-hscroll-arrow, .premium-hscroll-progress, .premium-hscroll-nav, .premium-hscroll-pagination, .premium-hscroll-fixed-content').remove();

            $hScrollElem.find(".premium-hscroll-temp").each(function (index, slide) {
                $(slide).removeClass('premium-hscroll-temp');
            });

            $hScrollElem.find('.premium-hscroll-sections-wrap').removeClass('premium-hscroll-sections-wrap');

            return;
        }

        instance = new premiumHorizontalScroll($hScrollElem, hScrollSettings);
        instance.init();
    };

    window.premiumHorizontalScroll = function ($elem, settings) {

        var self = this,
            id = settings.id,
            count = settings.templates.length,
            editMode = elementorFrontend.isEditMode(),
            currentDevice = elementorFrontend.getCurrentDeviceMode(),
            progressOffset = 300,
            currentActiveArr = [],
            currentActive = 0,
            prevActive = -1,
            loop = settings.loop,
            snapScroll = 'snap' === settings.snap,
            controller = false,
            isScrolling = false,
            scene = null,
            offset = null,
            horizontalSlide = null,
            rtlMode = settings.rtl,
            scrollEvent = null,
            dimensions = null;

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


        if ("desktop" !== currentDevice) {
            if (snapScroll && settings.disableSnap) {
                snapScroll = false;
                settings.enternace = false;
            }
            if ("tablet" === currentDevice) {
                progressOffset = 100;
            } else if ("mobile" === currentDevice) {
                progressOffset = 50;
            }
        } else if (snapScroll) {
            progressOffset = 30;
        }

        var $nav = $(".premium-hscroll-nav-item", $elem),
            $arrows = $(".premium-hscroll-wrap-icon", $elem);

        self.init = function () {

            if (!count) return;

            self.setLayout();

            self.setSectionsData();

            self.handleAnimations();

            self.setScene();

            if (!loop) self.checkActive();

            scene.on("progress", self.onProgress);

            $nav.on("click.premiumHorizontalScroll", self.onNavDotClick);

            $arrows.on("click.premiumHorizontalScroll", self.onNavArrowClick);

            self.checkRemoteAnchors();

            self.checkLocalAnchors();

            $(document).on('elementor/popup/show', function () {
                self.checkLocalAnchors();
            });

            $(window)
                .on("resize", self.refresh);

            if (snapScroll)
                document.addEventListener ?
                    document.addEventListener("wheel", self.onScroll, {
                        passive: false
                    }) :
                    document.attachEvent("onmousewheel", self.onScroll);

            if (settings.keyboard)
                document.addEventListener ?
                    document.addEventListener("keydown", self.onKeyboardPress) :
                    document.attachEvent("keydown", self.onKeyboardPress);

            if (snapScroll) {
                $(window)
                    .on("load", function () {
                        var windowOuterHeight = $(window).outerHeight();

                        if (offset - windowOuterHeight < 150)
                            return;

                        if (0 === currentActive) {
                            elementorFrontend.waypoint(
                                $elem,
                                function (direction) {
                                    if ("down" === direction) {
                                        self.scrollToSlide(0);
                                    }
                                }, {
                                offset: 150,
                                triggerOnce: false
                            }
                            );
                        }
                    });
            }
        };

        self.checkLocalAnchors = function () {

            $("a").on("click", function (event) {

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
            if ("BEFORE" === scene.state()) {
                return;
            } else {
                var downKeyCodes = [40, 34],
                    upKeyCodes = [38, 33];

                if ("AFTER" === scene.state()) {
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

            var value = settings[ID];

            if ("desktop" !== currentDevice) {
                value = settings[ID + "_" + currentDevice];
            }

            return value;

        };

        self.setScene = function () {

            controller = new ScrollMagic.Controller();

            horizontalSlide = new TimelineMax();

            self.setHorizontalSlider();

            var scrollSpeed = self.getResponsiveControlValue('speed');

            if ("desktop" === currentDevice) {
                scrollSpeed = scrollSpeed * 100 + "%";
            } else {
                scrollSpeed = scrollSpeed * $elem.outerHeight();
            }


            scene = new ScrollMagic.Scene({
                triggerElement: "#premium-hscroll-spacer-" + id,
                triggerHook: "onLeave",
                duration: scrollSpeed
            })
                .setPin("#premium-hscroll-wrap-" + id, {
                    pushFollowers: true
                })
                .setTween(horizontalSlide)
                .addTo(controller);

        };

        self.getDimensions = function () {

            var firstWidth = $slides.eq(0).innerWidth(),
                distance = firstWidth * (count - 1),
                progressWidth = firstWidth * count;

            var slidesInViewPort = self.getResponsiveControlValue('slides'),
                distanceBeyond = self.getResponsiveControlValue('distance');

            distance = distance - (1 - 1 / slidesInViewPort) * $elem.outerWidth();

            distance = distanceBeyond + distance;

            if (rtlMode)
                $("#premium-hscroll-scroller-wrap-" + id).css("transform", "translateX(" + -distance + "px)");

            var ease = Power2.easeOut;

            ease = Power0.easeNone;

            return {
                distance: distance,
                progressBar: progressWidth,
                ease: ease
            };

        };

        self.setHorizontalSlider = function (progress) {

            // horizontalSlide = new TimelineMax();

            dimensions = self.getDimensions();

            horizontalSlide
                .to("#premium-hscroll-scroller-wrap-" + id, 1, { x: rtlMode ? "0px" : -dimensions.distance, ease: dimensions.ease }, 0)
                .to("#premium-hscroll-progress-line-" + id, 1, { width: dimensions.progressBar + "px", ease: dimensions.ease }, 0);

            if ('undefined' !== typeof progress) {
                scene.progress(0);
                scene.update(true);
            }

        }

        self.setLayout = function () {
            $elem
                .closest("section.elementor-section-height-full")
                .removeClass("elementor-section-height-full");
        };

        self.setSectionsData = function () {

            var slidesInViewPort = self.getResponsiveControlValue('slides');

            var slideWidth = 100 / slidesInViewPort;

            $elem
                .find(".premium-hscroll-slider")
                .css("width", count * slideWidth + "%");

            $elem.find(".premium-hscroll-temp")
                .css("width", 100 / count + "%");

            var scrollSpeed = self.getResponsiveControlValue('speed');

            var width = parseFloat(
                $elem.find(".premium-hscroll-sections-wrap")
                    .width() / count
            ),
                winHeight = $(window)
                    .height() * scrollSpeed;

            $slides.each(function (index, template) {

                if ($(template)
                    .data("section")) {
                    var id = $(template)
                        .data("section");
                    self.getSectionContent(id);
                }

                var position = index * width;
                $(template)
                    .attr("data-position", position);
            });

            offset = $elem.offset()
                .top;

            $slides.each(function (index, template) {
                var scrollOffset = (index * winHeight) / (count - 1);

                $(template)
                    .attr("data-scroll-offset", offset + scrollOffset);
            });
        };

        self.onScroll = function (event) {
            if (isScrolling && null !== event) self.preventDefault(event);


            var delta = self.getDirection(event),
                state = scene.state(),
                direction = 0 > delta ? "down" : "up";

            if ("up" === direction && "AFTER" === scene.state()) {
                var lastScrollOffset = self.getScrollOffset(
                    $slides.eq(count - 1)
                );

                if (
                    window.pageYOffset - lastScrollOffset <= 300 &&
                    window.pageYOffset - lastScrollOffset > 100
                )
                    self.scrollToSlide(count - 1);
            }

            if ("DURING" === state) {
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

        self.setSnapScroll = function (event) {
            var direction = event.scrollDirection;

            if (
                (0 !== currentActive && "REVERSE" === direction) ||
                "FORWARD" === direction
            ) {
                if (null !== scrollEvent) self.preventDefault(scrollEvent);
            }

            var $nextArrow = $(".premium-hscroll-next", $elem),
                $prevArrow = $(".premium-hscroll-prev", $elem);

            if ("FORWARD" === direction) {
                if (!isScrolling && count - 1 !== currentActive) {
                    $nextArrow.trigger("click.premiumHorizontalScroll");
                }
            } else {
                if (!isScrolling && 0 !== currentActive)
                    $prevArrow.trigger("click.premiumHorizontalScroll");
            }
        };

        self.refresh = function () {

            // dimensions = self.getDimensions();

            // horizontalSlide
            //     .to("#premium-hscroll-scroller-wrap-" + id, 1, { x: "-980", ease: Power0.easeNone }, 0);


            setTimeout(function () {
                var sceneProgress = scene.progress();
                self.setHorizontalSlider(sceneProgress);
            }, 200);

            // self.setScene();
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

                    if (settings.enternace && !isScrolling)
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

            if (settings.enternace && !isScrolling)
                self.restartAnimations(currentActive);
        };

        self.addBackgroundLayer = function () {

            if ($elem.find(".premium-hscroll-bg-layer[data-layer='" + currentActive + "']").length) {
                $elem.find(".premium-hscroll-layer-active").removeClass("premium-hscroll-layer-active");

                $elem.find(".premium-hscroll-bg-layer[data-layer='" + currentActive + "']").addClass("premium-hscroll-layer-active");
            }

        };

        self.getSectionContent = function (sectionID) {
            if (!$("#" + sectionID)
                .length) return;

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

            if (index === prevActive && "DURING" === scene.state()) return;


            currentActive = index;

            self.scrollToSlide(index);
        };

        self.onNavArrowClick = function (e) {
            if (isScrolling) return;

            if ($(e.target).hasClass("premium-hscroll-prev") || $(e.target).find(".premium-hscroll-prev").length) {
                self.goToPrev();
            } else if ($(e.target).hasClass("premium-hscroll-next") || $(e.target).find(".premium-hscroll-next").length) {
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
            }


            if (0 > currentActive || count - 1 < currentActive) return;

            isScrolling = true;

            prevActive = slideIndex;

            var spacerHeight = $("#premium-hscroll-spacer-" + id).outerHeight();

            TweenMax.to(window, 1.5, {
                scrollTo: {
                    y: targetOffset - spacerHeight
                },
                ease: Power3.easeOut,
                onComplete: self.afterSlideChange
            });

            if (settings.pagination && snapScroll)
                $elem
                    .find(".premium-hscroll-current-slide")
                    .removeClass("zoomIn animated");

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

            if (settings.enternace) {
                setTimeout(function () {
                    self.setAnimations();
                }, 1000);
            }

            if (snapScroll) {
                setTimeout(function () {
                    isScrolling = false;
                }, 1500);
            }
        };

        self.afterSlideChange = function () {
            isScrolling = false;
        };

        self.handleAnimations = function () {
            if (settings.enternace) {

                self.hideAnimations();

                elementorFrontend.waypoint($elem, function () {
                    // self.setAnimations();
                });
            } else {
                self.unsetAnimations();
            }
        };

        self.hideAnimations = function () {

            $slides.find(".elementor-invisible").addClass("premium-hscroll-elem-hidden");

        };

        self.unsetAnimations = function () {
            $slides.find(".elementor-invisible")
                .each(function (index, elem) {
                    $(elem)
                        .removeClass("elementor-invisible");
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

            $slides
                .eq(currentActive)
                .find(".elementor-invisible")
                .each(function (index, elem) {
                    var settings = $(elem)
                        .data("settings");

                    if (undefined === settings) return;

                    if (!settings._animation && !settings.animation) return;

                    var delay = settings._animation_delay ?
                        settings._animation_delay :
                        0,
                        animation = settings._animation || settings.animation;

                    setTimeout(function () {
                        $(elem)
                            .removeClass("elementor-invisible premium-hscroll-elem-hidden")
                            .addClass(animation + " animated");
                    }, delay);
                });
        };

        self.getScrollOffset = function (item) {
            if (!$(item)
                .length) return;

            return $(item)
                .data("scroll-offset");
        };

        self.preventDefault = function (event) {
            if (event.preventDefault) {
                event.preventDefault();
            } else {
                event.returnValue = false;
            }
        };
    };

    $(window)
        .on("elementor/frontend/init", function () {
            elementorFrontend.hooks.addAction(
                "frontend/element_ready/premium-hscroll.default",
                PremiumHorizontalScrollHandler
            );
        });
})(jQuery);