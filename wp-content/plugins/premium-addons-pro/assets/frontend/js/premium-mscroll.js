(function ($) {

    //Force page to scroll from 0 to calculate scenes properly.
    window.scrollTo(0, 0);

    //Fix issues with lock screen on mobile devices.
    ScrollTrigger.config({
        limitCallbacks: true,
        ignoreMobileResize: true
    });

    var allOffsets = [];

    $(window).on('elementor/frontend/init', function () {

        var premiumMScrollHandler = function ($scope, $) {

            //This is used to change the scope to the parent section/container element on the editor page. To make sure to see the updates instantaneous.
            var elemType = $scope.data("element_type"),
                mScrollElems = $scope.find(".premium-mscroll-yes").length > 0 || $scope.hasClass("premium-mscroll-yes"),
                isColumnWithNoScroll = 'column' === elemType && !$scope.hasClass("premium-mscroll-yes"),
                isInnerSection = $scope.hasClass("elementor-inner-section"),
                isEditor = elementorFrontend.isEditMode();


            //If the current $scope has nothing to do with magic scroll, then return.
            if (!mScrollElems || isColumnWithNoScroll || isInnerSection)
                return;

            if ('section' === elemType || 'container' === elemType) {

                //We want to get the section/container scope only if it has Magic Scroll elements in it.
                //Also, if on the frontend, and there are nested containers, use the first one only.
                if (isEditor || $scope.parents(".e-con").length > 0)
                    return;


                //For the frontend only.
                window.lastContainer = $scope;
            } else {

                //We want to get the most parent container, not the nested ones.
                window.lastContainer = $scope.closest(".e-con[data-nesting-level=0]");

                //If Flex Container is disabled, or it's enabled, but the current parent is still a section.
                if (!elementorFrontend.config.experimentalFeatures.container || window.lastContainer.length < 1)
                    window.lastContainer = $scope.closest(".elementor-top-section");

            }

            //If on the editor mode, and lastContainer has not been defined yet, then return.
            if (!window.lastContainer && isEditor)
                return;


            var target = window.lastContainer,
                closestID = target.data("id").toString(),
                currentDevice = elementorFrontend.getCurrentDeviceMode();

            //If this is a horizontal scroll section, define a timeline.
            if ($scope.find(".premium-hscroll-temp").length > 0) {

                var horizontalScrollWidget = target.find(".elementor-widget-premium-hscroll"),
                    horizontalScrollID = horizontalScrollWidget.data('id'),
                    speed = horizontalScrollWidget.data("settings") ? horizontalScrollWidget.data("settings").scroll_speed : 4;

                gsap.timeline({
                    id: "timeline" + horizontalScrollID,
                    scrollTrigger: {
                        trigger: '.elementor-element-' + closestID,
                        start: "top top",
                        end: "+=" + speed * 1000,
                        pin: true,
                        scrub: true,
                    }
                });

                return;
            }

            //We want one timeline for each section/container, and we want to get the max duration and apply it on the scene.
            var settings = null,
                allDurations = [],
                lockScrub = null,
                isLockScreen = false,
                timeLine = gsap.timeline(),
                allSettings = [],
                startPoints = [],
                totalSvgShift = [],
                totalShiftAmount = 0,
                orderedAnims = 0,
                firstTransformAnim = null;

            //Don't use imagesloaded on horizontal scroll. It will cause timelines not to created in order because the time it takes to load the images.
            var closestHScroll = $scope.closest(".elementor-widget-premium-hscroll"),
                parallaxLayers = target.find(".premium-parallax-layer");

            allOffsets['off' + closestID] = $(target).offset().top;

            if (isEditor || closestHScroll.length > 0 || (closestHScroll.length < 1 && parallaxLayers.length < 1)) {

                if (target.hasClass('instant-mscroll')) {
                    createScene();
                } else {
                    elementorFrontend.waypoint(target, function () {

                        var checkPrevSticky = target.prev('.elementor-top-section, .e-con[data-nesting-level=0]').find('#sticky-duplicate').length > 0;

                        if (checkPrevSticky) {
                            target.prev('.elementor-top-section, .e-con[data-nesting-level=0]').on("paParallaxLoaded", function () {
                                setTimeout(function () { createScene() }, 200);
                            });
                        } else {
                            createScene();
                        }

                    });
                }

            } else {

                if (target.hasClass('instant-mscroll')) {
                    createScene();
                } else {
                    target.on("paParallaxLoaded", function () {
                        elementorFrontend.waypoint(target, function () { createScene(); });
                    });
                }

            }

            function createScene() {

                //Build the scene when it's visible on the viewport to prevent overloading.
                mScrollElems = target.find(".premium-mscroll-yes");

                //This will be used to order the animations.
                mScrollElems.each(function (index, elem) {

                    var elemID = $(elem).data("id"),
                        $elem = target.find('#premium-mscroll-' + elemID),
                        editMode = isEditor && $elem.length > 0,
                        $targetElem = editMode ? $elem : elem;

                    settings = {};
                    generateSettings($targetElem);

                    if (!settings) {
                        return false;
                    }

                    allSettings[index] = (settings);

                    if (settings.effects && 'lock' === settings.trigger) {

                        currentElementEffects = settings.effects.filter(function (effect) {
                            return 'yes' !== effect.premium_mscroll_sync_lock && 'yes' !== effect.premium_mscroll_sync;
                        });

                        startPoints.push({ order: settings.order, effects: currentElementEffects.length });
                    }

                });

                //Used to order animations and for Sync With Whole Scene option.
                var updatedPoints = [];
                startPoints.map(function (point, index) {

                    checkIndex = updatedPoints.findIndex(function (point) {
                        return point.order == startPoints[index].order;
                    });

                    if (-1 == checkIndex) {

                        pointsWithSameOrder = startPoints.filter(function (point) {
                            return point.order == startPoints[index].order;
                        });

                        highestEffects = Math.max.apply(Math, pointsWithSameOrder.map(function (point) { return point.effects; }))

                        highestPoint = startPoints.find(function (point) {
                            return startPoints[index].order == point.order && point.effects == highestEffects;
                        });

                        updatedPoints.push(highestPoint);
                    }

                });

                if (updatedPoints.length > 0)
                    orderedAnims = updatedPoints.reduce(function (accumulator, object) {
                        return accumulator + object.effects;
                    }, 0);

                mScrollElems.each(function (index, elem) {

                    var elemID = $(elem).data("id");

                    settings = allSettings[index];

                    if (!settings) {
                        return false;
                    }

                    $(target).find('.sticky-duplicate2').remove();

                    if ('automatic' === settings.trigger) {

                        killTimeline(elemID);

                        var animRev = settings.animRev ? 'reverse' : 'none',
                            newTimeLine = gsap.timeline({
                                id: "timeline" + elemID,
                                scrollTrigger: {
                                    trigger: '.elementor-element-' + closestID,
                                    toggleActions: 'play pause ' + animRev + ' none',
                                    start: (closestHScroll.length > 0 ? "left " : "top ") + settings.autoTrigger,
                                    containerAnimation: closestHScroll.length > 0 ? gsap.getById("timeline" + closestHScroll.data("id")) : null,
                                },
                                onUpdate: function () {
                                    //The timeline is removed after it ends. So, we don't want it to end on the editor page so we can use killTimeline()
                                    if (isEditor && this.progress() > 0.98)
                                        this.pause();
                                },
                                delay: settings.animDel
                            });

                        generateTimeLine(elem, newTimeLine);

                    } else if ('lock' === settings.trigger) {

                        target.addClass("premium-mscroll-lock");

                        killTimeline(elemID);

                        isLockScreen = true;

                        var animationStartPoint = 0;
                        if (1 < settings.order) {

                            var lastOrder = null;
                            //To get the right starting index, we need to sum all the lengths of the effects for the other animations before the curret one.
                            // We loop through all the elements, compare between the order to get the previous animations, sum their lengths. This will be the current starting index.
                            for (var i = 0; i < updatedPoints.length; i++) {

                                //Check if the current order is larger than the other orders.
                                if (updatedPoints[i].order < settings.order && lastOrder != updatedPoints[i].order) {

                                    animationStartPoint = animationStartPoint + updatedPoints[i].effects;
                                    lastOrder = updatedPoints[i].order;

                                }

                            }

                            //If there is a previous elmenent having multiple path SVG draw with paths not synced, we need to take all those paths into account.
                            var animSvgShift = 0;
                            for (var i = 0; i < totalSvgShift.length; i++) {

                                //Check if this order should be shifted because of previous Draw SVG effects.
                                if (totalSvgShift[i].start < settings.order) {
                                    animSvgShift = totalSvgShift[i].shiftAmount;

                                }
                            }
                            animationStartPoint = animationStartPoint + animSvgShift;

                        }

                        //The start index should be added to the +/- animation delay.
                        animationStartPoint = animationStartPoint + settings.lockDelay;

                        generateTimeLine(elem, timeLine, animationStartPoint);

                    } else {

                        var timeLineID = elemID;
                        if ('section' === settings.playRef) {
                            elemID = closestID;
                        }

                        if ('sticky' === settings.trigger) {
                            var stickyElem = 'scope' === settings.stickyTarget ? '.elementor-element-' + elemID : settings.stickyTargetSelector;

                            //If the target selector does not contain ID, then we will select it and add parents.
                            if ('custom' === settings.stickyTarget && -1 == stickyElem.indexOf('#'))
                                stickyElem = '.elementor-element-' + elemID + ' ' + settings.stickyTargetSelector;

                            //Return if sticky reference or sticky target is empty.
                            if ('custom' === settings.stickyTarget && settings.stickyTargetSelector.length < 2)
                                return;

                            if (settings.stickyRef.length < 2 || $(settings.stickyRef).length < 1)
                                return;

                            //Duplicate the sticky if needed. Demo page first section.
                            if (-1 !== stickyElem.indexOf('sticky-duplicate')) {

                                var topPosBefore = $(stickyElem).offset().top,
                                    $clonedStickyElem = $(stickyElem).clone().css({
                                        position: 'absolute',
                                        zIndex: 3,
                                        left: $(stickyElem).offset().left + 'px',
                                        width: $(stickyElem).width() + 'px',
                                        height: $(stickyElem).height() + 'px',
                                    }).attr('id', 'sticky-duplicate2').addClass("pa-invisible sticky-duplicate2");

                                $(settings.stickyRef).prepend($clonedStickyElem);

                                stickyElem = settings.stickyRef + ' #sticky-duplicate2';

                                var topPosAfter = allOffsets['off' + closestID] - topPosBefore;

                                $clonedStickyElem.css('top', - topPosAfter + 'px');

                            }


                        }

                        killTimeline(timeLineID);

                        var timeLineSettings = settings,
                            newTimeLine = gsap.timeline({
                                id: "timeline" + timeLineID,
                                scrollTrigger: {
                                    trigger: 'sticky' === settings.trigger ? settings.stickyRef : '.elementor-element-' + elemID,
                                    start: (closestHScroll.length > 0 ? "left " : "top ") + settings.playOffset + "%",
                                    containerAnimation: closestHScroll.length > 0 ? gsap.getById("timeline" + closestHScroll.data("id")) : null,
                                    end: function () {

                                        if ('sticky' === timeLineSettings.trigger) {
                                            var stickyEnd = handleSticky(stickyElem, timeLineSettings);

                                            return "bottom center+=" + stickyEnd;
                                        } else {
                                            return timeLineSettings.fullDuration * 100 + '%';
                                        }
                                    },
                                    pin: 'sticky' === settings.trigger ? stickyElem : false,
                                    scrub: ('' == settings.scrub || 'sticky' === timeLineSettings.trigger) ? !0 : settings.scrub,
                                    onToggle: function (line) {

                                        if ('sticky' === timeLineSettings.trigger) {

                                            $(line.trigger).toggleClass("parallax-no-trans");
                                            $(stickyElem).closest(".premium-parallax-layer").toggleClass("parallax-no-trans");

                                            //When back to start, show the duplicate and hide the original.
                                            if (line.progress < 0.5) {
                                                $("#sticky-duplicate").toggleClass("pa-invisible");

                                                if ("sticky-duplicate2" === $(stickyElem).attr("id"))
                                                    $(line.pin).toggleClass("pa-invisible");
                                            }

                                        }

                                    },
                                }
                            });

                        //Generate the animation, but in a new timeline.
                        generateTimeLine(elem, newTimeLine, true);

                    }

                    //Show the element after everything is initiated.
                    if ('sticky' !== settings.trigger || "sticky-duplicate2" !== $(stickyElem).attr("id"))
                        $(elem).removeClass("pa-invisible");

                });

                //Now, create one scene for each container/section, but only if we need to use magic scroll.
                killTimeline(closestID);

                if (closestHScroll.length < 1) {

                    if (isLockScreen) {
                        //Get the largest "Decrease Scroll Speed By" option value, and use it.
                        var maxDuration = Math.max.apply(null, allDurations);
                        settings.fullDuration = maxDuration;

                        var lockedtimeLine = gsap.timeline({
                            id: "timeline" + closestID,
                            scrollTrigger: {
                                trigger: '.elementor-element-' + closestID,
                                start: "top top",
                                pin: '.elementor-element-' + closestID,
                                end: settings.fullDuration * 100 + '%',
                                scrub: ('' == lockScrub || !lockScrub) ? !0 : lockScrub
                            },
                        });

                        lockedtimeLine.add(timeLine);

                    }

                } else {

                    //If this is a horizontal slide with magic scroll elements.
                    var hScrollTimeLine = gsap.getById("timeline" + closestHScroll.data("id")),
                        thisHScrollTemp = target.closest(".premium-hscroll-temp"),
                        thisHScrollTempIndex = thisHScrollTemp.index(),
                        nextHScrollTemp = thisHScrollTemp.next(".premium-hscroll-temp"),
                        prevHScrollTemp = thisHScrollTemp.prev(".premium-hscroll-temp"),
                        hScrollTempsLength = $(hScrollTimeLine.vars.scrollTrigger.trigger).find('.premium-hscroll-temp').length,
                        stepSize = 100 / hScrollTempsLength;

                    //Go to the current slide.
                    if (prevHScrollTemp.length > 0 && prevHScrollTemp.find(".premium-mscroll-yes").length < 1) {
                        hScrollTimeLine.to("#premium-hscroll-scroller-wrap-" + closestHScroll.data("id"), {
                            xPercent: -stepSize * thisHScrollTempIndex,
                            duration: thisHScrollTempIndex,
                            ease: 'none'
                        });
                    }

                    //Add the animation.
                    if (target.hasClass("premium-mscroll-lock"))
                        hScrollTimeLine.add(timeLine);

                    //Then, if the next slide has no magic scroll elements, then go to it.
                    if (nextHScrollTemp.length > 0 && nextHScrollTemp.find(".premium-mscroll-yes").length < 1) {
                        hScrollTimeLine.to("#premium-hscroll-scroller-wrap-" + closestHScroll.data("id"), {
                            xPercent: -(stepSize * (hScrollTempsLength - 1)),
                            duration: hScrollTempsLength - thisHScrollTempIndex,
                            ease: 'none'
                        });
                    }

                }

            }

            function handleResponsive() {

                var thisDevice = 'desktop';

                if (-1 !== currentDevice.indexOf("tablet")) {
                    thisDevice = "tablet";
                } else if (-1 !== currentDevice.indexOf("mobile")) {
                    thisDevice = "mobile";
                }

                return thisDevice;

            }

            function generateTimeLine(target, timeLine, startPoint) {

                if (!settings.effects || settings.effects.length < 1)
                    return;

                var scrollEffects = settings.effects,
                    isSynced = $(target).hasClass("premium-mscroll-sync-yes") ? 0 : "chain",
                    targetElem = target,
                    syncedAnimations = [],
                    totalShift = 0,
                    lastXPos = lastYPos = lastXRot = lastYRot = lastZRot = lastBlur = lastGScale = 0,
                    lastBright = 100,
                    animDuration = settings.animDuration;

                //Define Tweens.
                scrollEffects.forEach(function (effect, index) {

                    var ease = getEaseFunction(effect),
                        fromOrTo = 'yes' === effect.premium_mscroll_reverse ? 'from' : 'to',
                        disableOnDevice = false,
                        effectType = effect.premium_mscroll_type;

                    if (['rotate', 'scale', 'translate', 'skew'].includes(effectType)) {

                        //Set first transform animation only once.
                        if (!firstTransformAnim)
                            firstTransformAnim = effect;

                        //If run all animation together is enabled, different transform origins causes an issue.
                        var targetEffect = $(target).hasClass("premium-mscroll-sync-yes") ? firstTransformAnim : effect;

                        var transformOrigX = getResponsiveValue(targetEffect, 'premium_mscroll_origx'),
                            transformOrigY = getResponsiveValue(targetEffect, 'premium_mscroll_origy');

                        if ('custom' === transformOrigX) {
                            transformX = getResponsiveValue(targetEffect, 'premium_mscroll_origx_custom', 'slide');
                            transformOrigX = transformX.size + transformX.unit + ' ';

                        }

                        if ('custom' === transformOrigY) {
                            transformY = getResponsiveValue(targetEffect, 'premium_mscroll_origy_custom', 'slide');
                            transformOrigY = transformY.size + transformY.unit + ' ';

                        }

                        //If an empty value, then inherit from the target value of the last translate/rotate animation.
                        if ('translate' === effectType) {

                            var translateX = getResponsiveValue(effect, 'premium_mscroll_tr_x', 'slide');
                            if ("" !== translateX.size) {
                                lastXPos = translateX.size + translateX.unit;
                            }

                            var translateY = getResponsiveValue(effect, 'premium_mscroll_tr_y', 'slide');
                            if ("" !== translateY.size) {
                                lastYPos = translateY.size + translateY.unit;
                            }

                        } else if ('rotate' === effectType) {

                            var rotateX = getResponsiveValue(effect, 'premium_mscroll_rot_x', 'slide');
                            if ("" !== rotateX.size) {
                                lastXRot = rotateX.size;
                            }

                            var rotateY = getResponsiveValue(effect, 'premium_mscroll_rot_y', 'slide');
                            if ("" !== rotateY.size) {
                                lastYRot = rotateY.size;
                            }

                            var rotateZ = getResponsiveValue(effect, 'premium_mscroll_rot_z', 'slide');
                            if ("" !== rotateZ.size) {
                                lastZRot = rotateZ.size;
                            }
                        }

                    }

                    //Run all animations simultaneously.
                    if ("chain" !== isSynced) {
                        //set index to 0.
                        index = isSynced;
                    } else {

                        //We need to move the missing parts of the scene when animation is synced with the previous one, or when the animation is disabled on the current device.
                        index = index - syncedAnimations.length;

                        if (effect.premium_mscroll_disable_anim.includes(currentDevice)) {

                            //For each animation that is disabled on the current device, we need to add an element to the array that will be used to calculate the number of the shifts background.
                            disableOnDevice = true;
                            syncedAnimations.push(true);
                        }

                        if ("yes" === effect.premium_mscroll_sync) {
                            index = index - 1;
                            //For each animation that is synced with the previous one, we need to add an element to the array that will be used to calculate the number of the shifts background.
                            syncedAnimations.push(true);

                        }

                        //Used to add/subtract the delay between animation, will be added to the index.
                        if (effect.premium_mscroll_delay) {
                            var effectDelay = getResponsiveValue(effect, 'premium_mscroll_delay', 'slide').size;

                            if ("" !== effectDelay) {
                                totalShift = totalShift + effectDelay;
                            }
                        }

                        index = index + totalShift;

                    }

                    //Set the start point to the end of the previous animation.
                    if ('lock' === settings.trigger) {
                        index = index + startPoint;

                        animDuration = 1;

                        //We don't want to take the effects related to the same element in consideration.
                        if ("yes" === effect.premium_mscroll_sync_lock) {
                            animDuration = orderedAnims;
                            index = 0;

                        }

                    } else {
                        animDuration = settings.animDuration / scrollEffects.length;
                        index = index * animDuration;
                    }

                    //Disable on current device.
                    if (disableOnDevice)
                        return;

                    //Make sure every effect is reset to the outer container.
                    target = $(targetElem);

                    if ('custom' === effect.premium_mscroll_apply_on || 'video' === effectType) {


                        //We don't want to trigger anything when the first character is entered, or if no elements with that CSS selector exists.
                        if (effect.premium_mscroll_selector.length > 1 && $(effect.premium_mscroll_selector).length > 0) {
                            //If the selector contains an ID, then the user may wants to target an element outside the widget/column container.
                            if (-1 !== effect.premium_mscroll_selector.indexOf('#')) {
                                target = $(effect.premium_mscroll_selector);

                                if ('#sticky-duplicate' === effect.premium_mscroll_selector && 'sticky' === settings.trigger)
                                    target = $('.elementor-element-' + closestID + ' #sticky-duplicate2');

                            } else {
                                if ('video' === effectType) {
                                    target = $(".elementor-element-" + closestID).find(effect.premium_mscroll_selector);
                                } else {
                                    target = $(targetElem).find(effect.premium_mscroll_selector);
                                }

                            }
                        }

                        if ('video' !== effectType) {
                            TweenMax.set(target, { clearProps: "all" });
                        } else if ('VIDEO' !== target.prop("tagName")) {
                            target = target.find("video");
                        }

                        $(target).css('transition', 'none');

                    } else if ('carousel' === effectType) {

                        function handleCarousel() {

                            target = $(targetElem).find(".slick-track");

                            TweenMax.set(target, { clearProps: "transform" });

                            $(target).css('transition', 'none');

                        }

                        if (isEditor || (!isEditor && !window.carouselTrigger)) {
                            $(targetElem).on("paCarouselLoaded", function () {
                                handleCarousel();
                            });
                        } else {
                            handleCarousel();
                        }

                    } else if ('progress' === effectType) {
                        target = $(targetElem).find(".premium-progressbar-container");
                    } else if ('compare' === effectType) {
                        target = $(targetElem).find(".premium-images-compare-container");
                    }

                    if ($(target).hasClass("elementor-widget-premium-addon-icon-box")) {
                        $(target).css('transition', 'none');
                    }

                    if ('' !== settings.perspective) {
                        timeLine.set(target, {
                            transformPerspective: settings.perspective + "px",
                            perspectiveOrigin: "50% 50%"
                        });
                    }

                    switch (effectType) {
                        case 'scale':

                            if ('yes' !== effect.premium_mscroll_sc_screen) {
                                scaleX = getResponsiveValue(effect, 'premium_mscroll_sc_xvalue', 'slide').size;

                                scaleY = getResponsiveValue(effect, 'premium_mscroll_sc_yvalue', 'slide').size;

                                if ('to' === fromOrTo && 'sticky' === settings.trigger)
                                    $(target).attr("data-scale", scaleY > 1 ? -1 * scaleY : scaleY);

                                timeLine[fromOrTo](target, animDuration, {
                                    scaleX: scaleX,
                                    scaleY: scaleY,
                                    ease: ease,
                                    transformOrigin: transformOrigX + ' ' + transformOrigY,
                                    force3D: true
                                }, index);

                            } else {

                                var fitVars = Flip.fit(target[0], ".elementor-element-" + closestID, {
                                    getVars: true,
                                    ease: ease,
                                    duration: animDuration,
                                });

                                timeLine[fromOrTo](target, fitVars, index);

                            }

                            break;

                        case 'translate':

                            var xPosition = translateX.size ? translateX.size + translateX.unit : lastXPos,
                                yPosition = translateY.size ? translateY.size + translateY.unit : lastYPos;

                            timeLine[fromOrTo](target, animDuration, {
                                x: xPosition,
                                y: yPosition,
                                ease: ease,
                                transformOrigin: transformOrigY + ' ' + transformOrigX,
                                force3D: true
                            }, index);
                            break;

                        case 'carousel':

                            var xPosition = getResponsiveValue(effect, 'premium_mscroll_carousel_x', 'slide');

                            function scrollCarousel() {

                                timeLine[fromOrTo](target, animDuration, {
                                    x: xPosition.size + xPosition.unit,
                                    ease: ease,
                                }, index);

                            }

                            if (isEditor || (!isEditor && !window.carouselTrigger)) {
                                $(targetElem).on("paCarouselLoaded", function () {
                                    scrollCarousel();
                                });
                            } else {
                                scrollCarousel();
                            }

                            break;

                        case 'rotate':

                            timeLine[fromOrTo](target, animDuration, {
                                rotationX: rotateX.size || lastXRot,
                                rotationY: rotateY.size || lastYRot,
                                rotationZ: rotateZ.size || lastZRot,
                                ease: ease,
                                transformOrigin: transformOrigY + ' ' + transformOrigX,
                                force3D: true
                            }, index);
                            break;

                        case 'skew':

                            timeLine[fromOrTo](target, animDuration, {
                                skewX: getResponsiveValue(effect, 'premium_mscroll_sk_xvalue', 'slide').size,
                                skewY: getResponsiveValue(effect, 'premium_mscroll_sk_yvalue', 'slide').size,
                                ease: ease,
                                transformOrigin: transformOrigX + ' ' + transformOrigY,
                                force3D: true
                            }, index);
                            break;

                        case 'fadein':
                        case 'fadeout':

                            var fadeDir = getResponsiveValue(effect, 'premium_mscroll_fade_dir'),
                                isUpDir = 'up' === fadeDir || 'down' === fadeDir,
                                fadeStartX, fadeStartY, fadeEndX, fadeEndY;

                            if ('fadein' === effectType) {

                                fadeEndX = fadeEndY = 0;

                                fadeStartX = isUpDir ? 0 : 100;
                                fadeStartY = isUpDir ? 100 : 0;

                                if ('left' === fadeDir) {
                                    fadeStartX = -100;
                                } else if ('down' === fadeDir) {
                                    fadeStartY = -100;
                                }

                                timeLine.fromTo(target, animDuration, {
                                    x: fadeStartX,
                                    y: fadeStartY,
                                    opacity: 0,
                                    ease: ease,
                                }, {
                                    x: 0,
                                    y: 0,
                                    opacity: 1,
                                    ease: ease,
                                }, index);

                            } else {
                                fadeStartX = fadeStartY = 0;

                                fadeEndX = isUpDir ? 0 : -100;
                                fadeEndY = isUpDir ? -100 : 0;

                                if ('left' === fadeDir) {
                                    fadeEndX = 100;
                                } else if ('down' === fadeDir) {
                                    fadeEndY = 100;
                                }

                                timeLine.to(target, animDuration, {
                                    x: fadeEndX,
                                    y: fadeEndY,
                                    opacity: 0,
                                    ease: ease,
                                }, index);
                            }

                            break;

                        case 'custom':

                            var path = getCustomPath(effect, target);
                            timeLine[fromOrTo](target, animDuration, {
                                motionPath: {
                                    autoRotate: true,
                                    curviness: 1.25,
                                    path: path,
                                    align: path,
                                    alignOrigin: [0.5, 0.5],
                                },
                                ease: ease,
                            }, index);

                            break;
                        case 'opacity':

                            timeLine[fromOrTo](target, animDuration, {
                                autoAlpha: getResponsiveValue(effect, 'premium_mscroll_op_value', 'slide').size,
                                ease: ease,
                            }, index);

                            break;

                        case 'color':

                            var color = getResponsiveValue(effect, 'premium_mscroll_color');

                            timeLine[fromOrTo](target, animDuration, {
                                color: color,
                                fill: color,
                                ease: ease,
                            }, index);
                            break;

                        case 'backcolor':

                            var color = getResponsiveValue(effect, 'premium_mscroll_color');

                            timeLine[fromOrTo](target, animDuration, {
                                backgroundColor: color,
                                ease: ease,
                            }, index);
                            break;

                        case 'border':

                            var border = effect.premium_mscroll_border_width;

                            timeLine[fromOrTo](target, animDuration, {
                                borderTopWidth: border ? effect.premium_mscroll_border_width.top : 0,
                                borderRightWidth: border ? effect.premium_mscroll_border_width.right : 0,
                                borderBottomWidth: border ? effect.premium_mscroll_border_width.bottom : 0,
                                borderLeftWidth: border ? effect.premium_mscroll_border_width.left : 0,
                                borderStyle: effect.premium_mscroll_border_border,
                                borderColor: effect.premium_mscroll_border_color,
                                borderRadius: getBorderRadius(effect),
                                ease: ease,
                            }, index);

                            break;

                        case 'shadow':

                            var boxShadow = effect.premium_mscroll_sh_box_shadow,
                                shadowType = effect.premium_mscroll_sh_box_shadow_position;

                            timeLine[fromOrTo](target, animDuration, {
                                boxShadow: boxShadow.horizontal + 'px ' + boxShadow.vertical + 'px ' + boxShadow.blur + 'px ' + boxShadow.spread + 'px ' + boxShadow.color + ' ' + shadowType,
                                ease: ease,
                            }, index);

                            break;

                        case 'tshadow':

                            var textShadow = effect.premium_mscroll_tsh_text_shadow;

                            timeLine[fromOrTo](target, animDuration, {
                                textShadow: textShadow.horizontal + 'px ' + textShadow.vertical + 'px ' + textShadow.blur + 'px ' + textShadow.color,
                                ease: ease,
                            }, index);

                            break;

                        case 'padding':
                            timeLine[fromOrTo](target, animDuration, {
                                padding: getPaddingExp(effect),
                                ease: ease,
                            }, index);
                            break;

                        //The effects below are all added as CSS Filter. So, they are handled together.
                        case 'blur':
                        case 'gray':
                        case 'bright':

                            if ('blur' === effectType) {

                                //start the blur from the last scroll blur pixels.
                                var blurElement = { a: lastBlur },
                                    blurValue = getResponsiveValue(effect, 'premium_mscroll_blur', 'slide').size;

                                //This is used to start each animation from the last point.
                                lastBlur = blurValue;
                                //If the animation is reversed, then we need to set the starting value.

                                if ('from' === fromOrTo)
                                    applyFilter('set');

                                timeLine[fromOrTo](blurElement, animDuration, {
                                    a: blurValue,
                                    ease: ease,
                                    onUpdate: function () {
                                        applyFilter('blur', blurElement.a)
                                    },
                                }, index);


                            } else if ('gray' === effectType) {

                                //start the grayscale from the last scroll blur pixels.
                                var gSElement = { a: lastGScale },
                                    gScaleValue = getResponsiveValue(effect, 'premium_mscroll_gscale', 'slide').size;

                                if ('from' === fromOrTo) {
                                    lastGScale = gScaleValue;
                                    applyFilter('set');
                                }


                                timeLine[fromOrTo](gSElement, animDuration, {
                                    a: gScaleValue,
                                    ease: ease,
                                    onUpdate: function () {
                                        applyFilter('gray', gSElement.a)
                                    },
                                }, index);


                            } else {

                                //start the grayscale from the last scroll blur pixels.
                                var brElement = { a: lastBright },
                                    brValue = getResponsiveValue(effect, 'premium_mscroll_bright', 'slide').size;

                                if ('from' === fromOrTo) {
                                    lastBright = brValue;
                                    applyFilter('set');
                                }


                                timeLine[fromOrTo](brElement, animDuration, {
                                    a: brValue,
                                    ease: ease,
                                    onUpdate: function () {
                                        applyFilter('bright', brElement.a);
                                    },
                                }, index);

                            }

                            //here you pass the filter to the DOM element
                            var filterRule = '';
                            function applyFilter(action, val) {

                                switch (action) {
                                    case 'set':
                                        //The initial filter value
                                        filterRule = "blur(" + lastBlur + "px) grayscale(" + lastGScale + "%) brightness(" + lastBright + "%)";
                                        break;
                                    case 'blur':
                                        //If blur, get the new blur value, and apply the other filters from their last values.
                                        filterRule = "blur(" + val + "px) grayscale(" + lastGScale + "%) brightness(" + lastBright + "%)";
                                        lastBlur = val;
                                        break;
                                    case 'gray':
                                        filterRule = "blur(" + lastBlur + "px) grayscale(" + val + "%) brightness(" + lastBright + "%)";
                                        lastGScale = val;
                                        break;
                                    default:
                                        filterRule = "blur(" + lastBlur + "px) grayscale(" + lastGScale + "%) brightness(" + val + "%)";
                                        lastBright = val;
                                        break;

                                }

                                TweenMax.set(target, {
                                    filter: filterRule,
                                    webkitFilter: filterRule,
                                });
                            };

                            break;

                        case 'progress':

                            var barSettings = target.data("settings"),
                                length = barSettings.progress_length,
                                type = barSettings.type,
                                $progress = null,
                                $value = null;

                            $value = target.find(".premium-progressbar-right-label");
                            $value.text("0%");

                            target.removeClass("pa-invisible");

                            if ("line" === type) {

                                $progress = target.find(".premium-progressbar-bar");

                                gsap.set($progress, { clearProps: "width" });

                                timeLine.to($progress, animDuration, {
                                    width: length + "%",
                                    ease: ease,
                                    onUpdate: function () {
                                        $value.text(parseInt($progress[0].style.width.replace('%', '')) + '%');
                                    }
                                }, index);

                            } else if ("circle" === type) {

                                var progress = { frame: 0 };

                                $progress = target.find(".premium-progressbar-circle-left");

                                gsap.set($progress, { clearProps: "transform" });

                                var secondHalf = false;

                                timeLine.to(progress, animDuration, {
                                    frame: length,
                                    ease: ease,
                                    roundProps: "frame",
                                    onUpdate: function () {

                                        gsap.set($progress, { rotate: progress.frame * 3.6 });
                                        $value.text(progress.frame + '%');

                                        secondHalf = progress.frame * 3.6 > 180;

                                        target.find(".premium-progressbar-circle").css({
                                            '-webkit-clip-path': secondHalf ? 'inset(0)' : 'inset(0 0 0 50%)',
                                            'clip-path': secondHalf ? 'inset(0)' : 'inset(0 0 0 50%)',
                                        });

                                        target.find(".premium-progressbar-circle-right").css('visibility', secondHalf ? 'visible' : 'hidden');

                                    }
                                }, index);

                            } else if ("half-circle" === type) {

                                var progress = { frame: 0 };

                                $progress = target.find(".premium-progressbar-hf-circle-progress");

                                gsap.set($progress, { clearProps: "transform" });

                                $progress.css("transition", "none");

                                var degreesFactor = 1.8 * (elementorFrontend.config.is_rtl ? -1 : 1);

                                timeLine.to(progress, animDuration, {
                                    frame: length,
                                    ease: ease,
                                    roundProps: "frame",
                                    onUpdate: function () {

                                        gsap.set($progress, { rotate: progress.frame * degreesFactor });
                                        $value.text(progress.frame + '%');

                                    }
                                }, index);

                            }

                            break;

                        case 'compare':

                            var $imgCompare = target;
                            if ($imgCompare.hasClass("premium-compare-mscroll")) {

                                var widgetSettings = $imgCompare.data("settings"),
                                    compareStart = { step: widgetSettings.visibleRatio };

                                timeLine.to(compareStart, animDuration, {
                                    step: 'yes' === widgetSettings.reverse ? 0 : 1,
                                    ease: ease,
                                    onUpdate: function () {

                                        $imgCompare.trigger("updateRatio", compareStart.step);

                                    },
                                }, index);

                            }

                            break;

                        case 'video':

                            var currentAnimDuration = animDuration;

                            //The settimeout is for Elementor background videos to make sure they are rendered.
                            setTimeout(function () {

                                if ('VIDEO' !== target.prop("tagName"))
                                    target = target.find("video");

                                if (target.length > 0) {

                                    var $videoElem = target[0];

                                    $videoElem.load();
                                    $videoElem.pause();

                                    $videoElem.addEventListener('loadeddata', function () {

                                        target.css("pointer-events", "none").closest(".premium-video-box-container").addClass("playing");

                                        timeLine.to(target, currentAnimDuration, {
                                            currentTime: $videoElem.duration - 0.1,
                                            ease: ease,
                                        }, index);

                                    });
                                }

                            }, 200);

                            break;

                        case 'sequence':

                            var images = getSequenceImages(effect),
                                obj = { curImg: 0 },
                                lastIndex = null;

                            $(target).find("img").attr('srcset', '');
                            timeLine.to(obj, animDuration, {
                                curImg: images.length - 1,
                                roundProps: "curImg",
                                // immediateRender: true,
                                onUpdate: function () {

                                    //To change only when the image URL is changed.
                                    if (lastIndex != obj.curImg) {
                                        $(target).find("img").attr("src", images[obj.curImg]); // set the image source
                                    }

                                    lastIndex = obj.curImg;
                                },
                                ease: ease,
                            }, index);
                            break;

                        case 'class':

                            var cssClass = getResponsiveValue(effect, 'premium_mscroll_css_class');

                            //Make sure that the class is removed before adding a new one.
                            $(target).removeClass(cssClass);

                            timeLine.call(function () {
                                $(target).addClass(cssClass);
                            }, null, null, index);
                            break;

                        case 'font':

                            var fontSize = getResponsiveValue(effect, 'premium_mscroll_font', 'slide').size + "px";

                            timeLine.to(target, animDuration, {
                                fontSize: fontSize,
                                autoRound: false,
                                ease: ease,
                            }, index);
                            break;

                        case 'spacing':

                            var letterSpacing = getResponsiveValue(effect, 'premium_mscroll_letter_spacing', 'slide').size + "px";

                            timeLine.to(target, animDuration, {
                                letterSpacing: letterSpacing,
                                autoRound: false,
                                ease: ease,
                            }, index);
                            break;

                        case 'svg':

                            target = $(target).find("path, circle, rect, square, ellipse, polyline, polygon, line");

                            //We need to clear this because if there are multiple SVGs, then not clearing stroke-dasharray will make them not drawable in the editor
                            //Because each time this case is triggered, it will read paths with wrong stroke-dasharray
                            TweenMax.set(target, { clearProps: "stroke-dasharray" });

                            //SVG Draw is reversed, so we need to handle this.
                            fromOrTo = "to" === fromOrTo ? "from" : "to";

                            function prepareSVGpath() {

                                var lastPathIndex = 0,
                                    startOnEndPoint = 'from' === fromOrTo ? effect.premium_mscroll_draw_start.size : effect.premium_mscroll_draw_end.size;

                                target.each(function (pathIndex, path) {

                                    var $path = $(path);

                                    $path.attr("fill", "transparent");

                                    if ('yes' === effect.premium_mscroll_draw_sync)
                                        pathIndex = 0;

                                    pathIndex = pathIndex + index;

                                    lastPathIndex = pathIndex;

                                    timeLine[fromOrTo]($path, animDuration, {
                                        PaSvgDrawer: (startOnEndPoint || 0) + "% 0",
                                        ease: ease,
                                    }, pathIndex);

                                    if ('yes' === effect.premium_mscroll_svg_fill && 'yes' !== effect.premium_mscroll_fill_af_full) {

                                        timeLine.to($path, animDuration, {
                                            fill: effect.premium_mscroll_svg_color,
                                            ease: ease,
                                            onUpdate: function () {
                                                if (!$path.hasClass("no-transition"))
                                                    $path.addClass("no-transition");
                                            },
                                            onComplete: function () {
                                                $path.removeClass("no-transition");
                                            }
                                        }, pathIndex);
                                    }

                                });

                                if ('yes' === effect.premium_mscroll_svg_fill && 'yes' === effect.premium_mscroll_fill_af_full) {

                                    //If the paths will be drawn together, then start the filling after that.
                                    if (lastPathIndex == 0)
                                        lastPathIndex = 1;

                                    timeLine.to(target, animDuration, {
                                        fill: effect.premium_mscroll_svg_color,
                                        ease: ease,
                                        onUpdate: function () {
                                            if (!target.hasClass("no-transition"))
                                                target.addClass("no-transition");
                                        },
                                        onComplete: function () {
                                            target.removeClass("no-transition");
                                        }
                                    }, lastPathIndex);
                                }


                            }

                            prepareSVGpath();

                            break;

                    }

                });

                //Loop Count.
                if (settings.repeat > 0)
                    timeLine.repeat(settings.repeat - 1);

            }

            function generateSettings(target) {

                var generalSettings = $(target).data('mscroll');

                if (!generalSettings) {
                    return false;
                }

                var thisDevice = handleResponsive(),
                    deviceSettings = generalSettings[thisDevice];

                if ("disable" === deviceSettings.action) {
                    return;
                } else if ("height" === deviceSettings.action) {

                    var comparedHeight = "window" === deviceSettings.lock ? $(window).outerHeight() : deviceSettings.height;

                    if ('lock' === generalSettings.trigger && $(window.lastContainer).outerHeight() > comparedHeight)
                        generalSettings.trigger = 'play';
                }

                settings.trigger = generalSettings.trigger;

                if ('automatic' !== settings.trigger) {

                    if (-1 !== currentDevice.indexOf("mobile")) {
                        settings.scrub = (generalSettings.scrubMobile || generalSettings.scrub);
                    } else if (-1 !== currentDevice.indexOf("tablet")) {
                        settings.scrub = (generalSettings.scrubTablet || generalSettings.scrub);
                    } else {
                        settings.scrub = generalSettings.scrub;
                    }

                    if (settings.scrub)
                        lockScrub = settings.scrub;

                }

                if ('lock' === settings.trigger || 'play' === settings.trigger) {

                    if (-1 !== currentDevice.indexOf("mobile")) {
                        settings.fullDuration = (generalSettings.fullDurationMobile || generalSettings.fullDuration);
                        settings.order = generalSettings.orderMobile;
                    } else if (-1 !== currentDevice.indexOf("tablet")) {
                        settings.fullDuration = (generalSettings.fullDurationTablet || generalSettings.fullDuration);
                        settings.order = generalSettings.orderTablet;
                    } else {
                        settings.fullDuration = generalSettings.fullDuration;
                        settings.order = generalSettings.order;
                    }

                    //We want to push the value for lock viewport animations only to get the maximum duration and apply it.
                    if ('lock' === settings.trigger)
                        allDurations.push(settings.fullDuration);

                }

                if ('lock' === settings.trigger) {

                    settings.lockDelay = generalSettings.lockDelay || 0;

                    settings.animDuration = 1;
                } else {

                    //For automatic animation only.
                    settings.autoTrigger = generalSettings.autoTrigger;
                    settings.animRev = generalSettings.animRev;

                    settings.animDuration = generalSettings.autoDuration || 3;
                    settings.animDel = generalSettings.autoDel || 0;

                    //For viewport animation only.
                    settings.playOffset = generalSettings.playOffset;
                    settings.playRef = generalSettings.playRef;

                    //For sticky elements only.
                    if ('sticky' === settings.trigger) {

                        var stickySettings = {
                            stickyRef: generalSettings.stickyRef,

                            stickyTarget: generalSettings.stickyTarget,
                            stickyTargetSelector: generalSettings.stickyTargetSelector,

                            stickyStart: generalSettings.stickyStart,
                            stickyEnd: generalSettings.stickyEnd,

                            stickyDisable: generalSettings.stickyDisable
                        }

                        settings = Object.assign(settings, stickySettings);

                    }
                }

                settings.repeat = generalSettings.repeat || 0;
                settings.perspective = generalSettings.perspective || '';

                settings.effects = [];

                $.each(generalSettings.effects, function (index, effect) {

                    settings.effects.push(effect);


                    //Take number of paths in the SVG into consideration for the next animation start point.
                    if ('svg' === effect.premium_mscroll_type) {
                        var pathLength = $(target).find("path, circle, rect, square, ellipse, polyline, polygon, line").length;

                        if ('yes' !== effect.premium_mscroll_draw_sync && pathLength > 1) {
                            totalShiftAmount = totalShiftAmount + pathLength;
                            totalSvgShift.push({ start: settings.order, shiftAmount: totalShiftAmount });
                        }

                    }
                });

                if (0 !== Object.keys(settings).length) {
                    return settings;
                }

            }

            function handleSticky(stickyElem, settings) {

                var end = settings.stickyEnd,
                    $stickyElem = $(stickyElem);

                if ($stickyElem.length < 1)
                    return;

                $stickyElem.css({
                    minWidth: $stickyElem.outerWidth()
                });

                var stickyScale = $stickyElem.data("scale") ? $stickyElem.data("scale") : 1,
                    transformOriginY = $stickyElem.css("transform-origin").substr($stickyElem.css("transform-origin").indexOf(" ") + 1),
                    scaleOriginValue = 1 != stickyScale ? (stickyScale) * parseFloat(transformOriginY) : 0;

                if ('before' === settings.stickyEnd) {
                    end = ('IMG' === $stickyElem.prop("tagName")) ? $stickyElem[0].getBoundingClientRect().height : $stickyElem[0].offsetHeight;

                    if (end == 0)
                        end = $stickyElem[0].naturalHeight || $stickyElem[0].offsetHeight;
                } else {
                    end = 0;
                }

                if (settings.stickyStart === 'absolute')
                    $(stickyElem).offset({ top: $(settings.stickyRef).offset().top });

                var offsetDiff = $stickyElem.offset().top - $(settings.stickyRef).offset().top,
                    stickyHide = 'sticky-duplicate2' === $(stickyElem).attr("id");

                if (stickyHide || (!stickyHide && $(settings.stickyRef).outerHeight() < $stickyElem.outerHeight()))
                    offsetDiff = 0;


                var scrollHeight = end - scaleOriginValue + offsetDiff;

                if (-1 == stickyElem.indexOf('sticky-duplicate') && 50 != settings.playOffset) {
                    fixOffset = $(window).outerHeight() * ((settings.playOffset - 50) / 100);
                    scrollHeight = fixOffset + scrollHeight;
                }

                return scrollHeight;


            }

            function getEaseFunction(scroll) {

                var ease = null;

                switch (scroll.premium_mscroll_ease) {
                    case 'easein':
                        ease = Power0.easeNone;
                        break;
                    case 'easeout':
                        ease = Power0.easeOut;
                        break;
                    case 'easeinout':
                        ease = Power0.EaseInOut;
                        break;
                    case 'custom':
                        ease = eval(scroll.premium_mscroll_c_ease);
                        break;
                    default:
                        ease = Power0.easeNone;
                }

                return ease;

            }

            function getBorderRadius(scroll) {

                var borderRExp = 'yes' === scroll.premium_mscroll_border_ra ? scroll.premium_mscroll_border_ra_value : scroll.premium_mscroll_border_r.size + scroll.premium_mscroll_border_r.unit;

                return borderRExp;

            }

            function getPaddingExp(scroll) {

                var padding = getResponsiveValue(scroll, 'premium_mscroll_padding'),
                    unit = padding.unit;

                var paddingExp = padding.top + unit + " "
                    + padding.right + unit + " "
                    + padding.bottom + unit + " "
                    + padding.left + unit;

                return paddingExp;
            }

            function getCustomPath(scroll, elem) {

                var pathPoints = [];

                var customPath = null;

                if ('svg' === scroll.premium_mscroll_path_type) {


                    var svgID = "premium-mscroll-svg-" + scroll['_id'],
                        viewBoxMinX = getResponsiveValue(scroll, 'premium_mscroll_st_x', 'slide').size || 0,
                        viewBoxMinY = getResponsiveValue(scroll, 'premium_mscroll_st_y', 'slide').size || 0,
                        customPath = -1 !== scroll.premium_mscroll_svg_path.indexOf('svg') ? scroll.premium_mscroll_svg_path : '<svg><path d="' + scroll.premium_mscroll_svg_path + '"/></svg>';

                    //Remove any custom paths created for that element.
                    $(' .elementor-repeater-item-' + scroll['_id']).remove();

                    $('<div class="premium-mscroll-svg elementor-repeater-item-' + scroll['_id'] + '"id="' + svgID + '">' + customPath + '</div>').insertAfter(elem).find("path").attr({
                        id: svgID + '-path',
                        fill: "none"
                    });

                    var $el = $(' .elementor-repeater-item-' + scroll['_id']).find("svg"),

                        svgElemWidth = $el.outerWidth(),
                        svgElemHeight = $el.outerHeight(),
                        zoom = getResponsiveValue(scroll, 'premium_mscroll_st_zoom'),
                        viewBoxX = Math.ceil(svgElemWidth / zoom),
                        viewBoxY = Math.ceil(svgElemHeight / zoom),
                        viewBox = -1 * viewBoxMinX + ' ' + -1 * viewBoxMinY + ' ' + + viewBoxX + ' ' + viewBoxY;

                    $el.attr("viewBox", viewBox);

                    return '#' + svgID + '-path';
                } else {
                    customPath = getResponsiveValue(scroll, 'premium_mscroll_path_points').replace(/},/g, '}&');
                }


                customPath = customPath.split("&");
                customPath.map(function (point) {
                    //Make sure that the pair can be parsed to JSON.
                    point = point.replace(/{/g, '{"x":').replace(/,/g, ',"y":').replace(/:/g, ':"').replace(/%/g, '%').replace(/,/g, '",').replace(/}/g, '"}');
                    pathPoints.push(JSON.parse(point));
                });

                return pathPoints;

            }

            function getSequenceImages(scroll) {

                var gallery = scroll.premium_mscroll_gallery,
                    urlsArr = [];

                gallery.map(function (img) {
                    urlsArr.push(img.url);
                });

                return urlsArr;

            }

            function getResponsiveValue(scroll, animation, type) {

                var suffix = 'desktop' === currentDevice ? '' : '_' + currentDevice,
                    controlValue = null;

                if ('slide' !== type) {
                    suffix = !scroll[animation + suffix] ? '' : suffix;
                    controlValue = scroll[animation + suffix];
                } else {
                    if (scroll[animation + suffix]) {
                        suffix = '' == scroll[animation + suffix].size ? '' : suffix;
                    } else {
                        suffix = '';
                    }

                    controlValue = {
                        'size': scroll[animation + suffix].size,
                        'unit': scroll[animation + suffix].unit
                    };
                }

                return controlValue;

            }

        };

        function killTimeline(id) {

            if (elementorFrontend.isEditMode()) {

                var historyTimeLine = gsap.getById("timeline" + id);

                if (historyTimeLine) {
                    historyTimeLine.scrollTrigger.kill();
                    historyTimeLine.progress(0);
                    historyTimeLine.kill();
                }

            }

        }

        if (elementorFrontend.isEditMode()) {

            elementorFrontend.hooks.addAction("frontend/element_ready/global", premiumMScrollHandler);

            //Handle element removal.
            elementor.listenTo(elementor.channels.data, 'element:destroy', function (element) {

                var elementType = element.attributes.elType;

                //On remove section
                if (elementType !== 'widget' && elementType !== 'column') {
                    killTimeline(element.id);
                } else {

                    //On remove element, check if there are still lock screen elements in the section.
                    var mScrollSections = elementorFrontend.elements.$body.find(".premium-mscroll-lock");

                    setTimeout(function () {
                        mScrollSections.each(function (index, elem) {
                            if ($(elem).find(".premium-mscroll-yes").length < 1) {
                                killTimeline($(elem).data("id"));
                            }

                        });
                    }, 100);

                }


            });

        } else {
            elementorFrontend.hooks.addAction("frontend/element_ready/section", premiumMScrollHandler);
            elementorFrontend.hooks.addAction("frontend/element_ready/container", premiumMScrollHandler);
        }


    });


})(jQuery);
