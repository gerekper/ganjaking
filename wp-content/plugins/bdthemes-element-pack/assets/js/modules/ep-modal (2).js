/**
 * Start modal widget script
 */
(function ($, elementor) {

    'use strict';

    var widgetModal = function ($scope, $) {

        var $modal = $scope.find('.bdt-modal');

        if (!$modal.length) {
            return;
        }

        $.each($modal, function (index, val) {

            var $this = $(this),
                $settings = $this.data('settings'),
                modalShowed = false,
                modalID = $settings.id,
                displayTimes = $settings.displayTimes,
                scrollDirection = $settings.scrollDirection,
                scrollSelector = $settings.scrollSelector,
                scrollOffset = $settings.scrollOffset,
                splashInactivity = $settings.splashInactivity,
                closeBtnDelayShow = $settings.closeBtnDelayShow,
                delayTime = $settings.delayTime,
                splashDelay = $settings.splashDelay,
                widgetId = $settings.widgetId,
                layout = $settings.layout;
            var editMode = Boolean(elementorFrontend.isEditMode()),
                inactiveTime;

            var modal = {
                setLocalize: function () {
                    
                    // clear cache if user logged in
                        if ($('body').hasClass('logged-in') || editMode) {
                            if ($settings.cacheOnAdmin !== true) {
                                localStorage.removeItem(widgetId + '_expiresIn');
                                localStorage.removeItem(widgetId);
                                return;
                            }
                        }
                    
                    this.clearLocalize();
                    var widgetID = widgetId,
                        localVal = 0,
                        // hours = 4;
                        hours = $settings.displayTimesExpire;

                    var expires = (hours * 60 * 60);
                    var now = Date.now();
                    var schedule = now + expires * 1000;

                    if (localStorage.getItem(widgetID) === null) {
                        localStorage.setItem(widgetID, localVal);
                        localStorage.setItem(widgetID + '_expiresIn', schedule);
                    }
                    if (localStorage.getItem(widgetID) !== null) {
                        var count = parseInt(localStorage.getItem(widgetID));
                        count++;
                        localStorage.setItem(widgetID, count);
                        // this.clearLocalize();
                    }
                },
                clearLocalize: function () {
                    var localizeExpiry = parseInt(localStorage.getItem(widgetId + '_expiresIn'));
                    var now = Date.now(); //millisecs since epoch time, lets deal only with integer
                    var schedule = now;
                    if (schedule >= localizeExpiry) {
                        localStorage.removeItem(widgetId + '_expiresIn');
                        localStorage.removeItem(widgetId);
                    }
                },
                modalFire: function () {
                    if (layout == 'splash' || layout == 'exit' || layout == 'on_scroll') {
                        modal.setLocalize();
                        var firedNotify = parseInt(localStorage.getItem(widgetId));
                        if ((displayTimes !== false) && (firedNotify > displayTimes)) {
                            return;
                        }
                    }
                    bdtUIkit.modal($this).show();
                },
                closeBtnDelayShow: function () {
                    var $modal = $('#' + modalID);
                    $modal.find('#bdt-modal-close-button').hide(0);
                    $modal.on("shown", function () {
                            $('#bdt-modal-close-button').hide(0).fadeIn(delayTime);
                        })
                        .on("hide", function () {
                            $modal.find('#bdt-modal-close-button').hide(0);
                        });
                },
                customTrigger: function () {
                    var init = this;
                    $(modalID).on('click', function (event) {
                        event.preventDefault();
                        init.modalFire();
                    });
                },
                scrollDetect: function (fn) {
                    let last_scroll_position = 0;
                    let ticking = false;
                    window.addEventListener("scroll", function () {
                        let prev_scroll_position = last_scroll_position;
                        last_scroll_position = window.scrollY;
                        if (!ticking) {
                            window.requestAnimationFrame(function () {
                                fn(last_scroll_position, prev_scroll_position);
                                ticking = false;
                            });
                            ticking = true;
                        }
                    });
                },
                modalFireOnSelector: function () {
                    var init = this;
                    if (scrollDirection) {
                        $(window).on('scroll', function () {
                            var hT = $(scrollSelector).offset().top,
                                hH = $(scrollSelector).outerHeight(),
                                wH = $(window).height(),
                                wS = $(this).scrollTop();
                            var firedId = widgetId + '-fired';
                            if (wS > (hT + hH - wH)) {
                                if (!$(scrollSelector).hasClass(firedId)) {
                                    init.modalFire();
                                }
                                $(scrollSelector).addClass(firedId); // tricks added
                            }
                        });
                    }
                },
                onScroll: function () {
                    var init = this;
                    this.scrollDetect((scrollPos, previousScrollPos) => {
                        var wintop = $(window).scrollTop(),
                            docheight = $(document).height(),
                            winheight = $(window).height();
                        var scrolltrigger = scrollOffset / 100;
                        var firedId = widgetId + '-fired';
                        if ((previousScrollPos > scrollPos) && scrollDirection == 'up') {
                            if (!$('body').hasClass(firedId)) {
                                init.modalFire();
                                $('body').addClass(firedId);
                            }
                        } else if ((previousScrollPos < scrollPos) && scrollDirection == 'down' && previousScrollPos !== 0) {
                            if ((wintop / (docheight - winheight)) > scrolltrigger) {
                                if (!$('body').hasClass(firedId)) {
                                    init.modalFire();
                                    $('body').addClass(firedId);
                                }
                            }
                        } else if ((previousScrollPos < scrollPos) && scrollDirection == 'selector' && previousScrollPos !== 0) {
                            init.modalFireOnSelector();
                        }
                    });
                },
                exitPopup: function () {
                    var init = this;
                    document.addEventListener('mouseleave', function (event) {
                        if (
                            event.clientY <= 0 ||
                            event.clientX <= 0 ||
                            (event.clientX >= window.innerWidth || event.clientY >= window.innerHeight)
                        ) {
                            if (!editMode){
                                init.modalFire();
                            }
                        }
                    });
                },
                resetTimer: function () {
                    clearTimeout(inactiveTime);
                    inactiveTime = setTimeout(this.modalFire, splashInactivity); // time is in milliseconds
                },
                splashInactivity: function () {
                    window.onload = this.resetTimer();
                    window.onmousemove = this.resetTimer();
                    window.onmousedown = this.resetTimer(); // catches touchscreen presses as well      
                    window.ontouchstart = this.resetTimer(); // catches touchscreen swipes as well 
                    window.onclick = this.resetTimer(); // catches touchpad clicks as well
                    window.onkeydown = this.resetTimer();
                    window.addEventListener('scroll', this.resetTimer(), true); // improved; see comments
                },
                splashTiming: function () {
                    var init = this;
                    setTimeout(function () {
                        init.modalFire();
                    }, splashDelay);
                },
                splashInit: function () {
                    if (!splashInactivity) {
                        this.splashTiming();
                        return;
                    }
                    this.splashInactivity();
                },
                default: function () {
                    $(modalID).on('click', function (event) {
                        event.preventDefault();
                        this.modalFire();
                    });
                },
                init: function () {
                    var init = this;

                    if (layout == 'default') {
                        init.default();
                    }
                    if (layout == 'splash') {
                        init.splashInit();
                    }
                    if (layout == 'exit') {
                        init.exitPopup();
                    }
                    if (layout == 'on_scroll') {
                        init.onScroll();
                    }
                    if (layout == 'custom') {
                        init.customTrigger();
                    }
                    if (closeBtnDelayShow) {
                        init.closeBtnDelayShow();
                    }
                }
            };

            // kick off the modal
            modal.init();
            

            // append section as content

            if ($settings !== undefined && editMode === false && $settings.custom_section != false) {
                $($settings.modal_id).addClass('elementor elementor-' + $settings.pageID)
                var $modalContent = $('.elementor-section' + $settings.custom_section);
                var $modalBody = $($settings.modal_id).find('.bdt-modal-body');
                $($modalContent).appendTo($modalBody);
            }

        });
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-modal.default', widgetModal);
    });

}(jQuery, window.elementorFrontend));

/**
 * End modal widget script
 */
