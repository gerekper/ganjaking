(function ($) {

    $(window).on('DOMContentLoaded', function () {

        window.paCustomCursorHandler = function (eleType, $scope, settings) {

            var self = this,
                isActivated = true,
                pageSettings = {},
                cursorSettings = settings;

            this.isAddonEnabled = false;

            self.init = function () {

                this.isEditMode = elementorFrontend.isEditMode();

                // in case the addons is disabled on page load >> we listen to its switcher to re-init the addon when it's enabled.
                if (this.isEditMode) {
                    this.initAddonSwListener();
                }

                self.initCustomCursor();
            };

            self.initCustomCursor = function () {

                self.initPageSettings();

                if (!Object.keys(pageSettings).length) {
                    return;
                }

                self.isPgCursorEnabled();

                if (!this.isAddonEnabled) {
                    return;
                }

                self.generateCursorSettings();

                self.generateCursor();

                if (this.isEditMode) { // init controls callback only in the editor.
                    self.initControlsCallbacks();
                }
            };

            /** Gets all page settings. We need to make sure that the controls we want its value have frontend_available => true */
            self.initPageSettings = function () {

                var settings = self.isEditMode ? elementor.settings.page.model.attributes : elementorFrontend.config.settings.page;

                pageSettings = Object.assign({}, settings); // make sure the settings is an object.
            };

            /** Checks if the addon's switcher is enabled and not disabled on the current device mode. */
            self.isPgCursorEnabled = function () {

                var isAddonEnabled = 'yes' === pageSettings.premium_global_cursor_switcher ? true : false;

                if (isAddonEnabled) {

                    var isTouchDevice = ['tablet', 'tablet-extra', 'mobile', 'mobile-extra'].includes(elementorFrontend.getCurrentDeviceMode()) ? true : false,
                        mobileDisabled = isTouchDevice && 'yes' === pageSettings.pa_disable_cursor ? true : false;

                    if (self.isEditMode || !mobileDisabled) {
                        self.isAddonEnabled = true;
                    }

                } else {
                    self.isAddonEnabled = false;
                }
            };

            /** Extracts the addons settings. */
            self.generateCursorSettings = function () {

                var settings = pageSettings,
                    cursorType = settings.pa_cursor_type;

                cursorSettings.elemId = self.getPageId();

                cursorSettings.cursorType = cursorType;

                cursorSettings.delay = ['ftext', 'fimage'].includes(cursorType) && '' !== settings.pa_cursor_trans.size ? settings.pa_cursor_trans.size : 0.01;

                cursorSettings.pulse = ['icon', 'image'].includes(cursorType) && 'yes' === settings.pa_cursor_pulse ? ' premium-pulse-yes ' : '';

                cursorSettings.buzz = ['icon', 'image'].includes(cursorType) && 'yes' === settings.pa_cursor_buzz ? ' premium-buzz-yes ' : '';

                cursorSettings.elementSettings = self.getElementSettings(settings);

                cursorSettings.cursorDot = ['ftext', 'fimage'].includes(cursorType) && 'yes' === settings.pa_cursor_dot ? ' premium-cursor-dot ' : '';

                cursorSettings.magnet = 'yes' === settings.pa_magnet;
            };

            // TODO: change the function name.
            /** Gets cursor type-related settings */
            self.getElementSettings = function (settings) {

                var cursorType = settings.pa_cursor_type,
                    elementSettings = {};

                if ('icon' === cursorType) {
                    elementSettings = settings.pa_cursor_icon;

                } else if ('image' === cursorType || 'fimage' === cursorType) {
                    elementSettings.url = settings.pa_cursor_img.url;

                    if ('fimage' === cursorType) {
                        elementSettings.xpos = settings.pa_cursor_xpos.size;
                        elementSettings.ypos = settings.pa_cursor_ypos.size;
                    }

                } else if ('ftext' === cursorType) {
                    elementSettings.text = settings.pa_cursor_ftext;
                    elementSettings.xpos = settings.pa_cursor_xpos.size;
                    elementSettings.ypos = settings.pa_cursor_ypos.size;

                } else if ('lottie' === cursorType) {
                    elementSettings.url = settings.pa_cursor_lottie_url;
                    elementSettings.loop = settings.pa_cursor_loop;
                    elementSettings.reverse = settings.pa_cursor_reverse;
                }

                return elementSettings;
            };

            self.generateCursor = function () {

                var settings = cursorSettings,
                    uniqueClass = 'premium-global-cursor-' + settings.elemId,
                    cursorHtml = self.getCursorHtml(uniqueClass);

                self.addCursor(uniqueClass, cursorHtml);

                var hasSvgIcon = 'icon' === settings.cursorType && 'svg' === settings.elementSettings.library;

                if (hasSvgIcon) {
                    self.renderSvgIcon(settings.elementSettings.value.url, settings.elemId);
                }

                if ('lottie' === settings.cursorType) {
                    var $lottieItem = $('.premium-global-cursor-' + settings.elemId).find('.premium-lottie-animation'),
                        lottieInstance = new premiumLottieAnimations($lottieItem);

                    lottieInstance.init();
                }

                self.generateCursorMotion(settings, uniqueClass);
            };

            self.generateCursorMotion = function (settings, uniqueClass) {

                // cursor props.
                var types = ['icon', 'image', 'lottie'],
                    eleInfo = settings.eleInfo,
                    props = {
                        extraTop: 0,
                        extraLeft: 0,
                        elem: uniqueClass,
                        delay: settings.delay,
                        width: $scope.find('.premium-global-cursor-' + settings.elemId).outerWidth(),
                        height: $scope.find('.premium-global-cursor-' + settings.elemId).outerHeight()
                    };


                if (settings.magnet && $scope.find('.premium-image-hotspots-container').length > 0) {
                    self.handleMagnetEffect(props);
                }

                if (!types.includes(settings.cursorType)) {
                    props.extraLeft = (settings.elementSettings.xpos / 100) * props.width;
                    props.extraTop = (settings.elementSettings.ypos / 100) * props.height;
                    props.width = 0;

                } else {
                    // We need to make sure the arrow is centered.
                    props.extraLeft = 0.5 * props.width;
                    props.extraTop = 0.5 * props.height;
                }

                $scope.off('mousemove');

                $scope.mousemove(function (e) {

                    $scope.css('cursor', (['fimage', 'ftext'].includes(settings.cursorType) && '' == settings.cursorDot) ? 'default' : 'none');

                    if ('page' !== eleType) {

                        if ('section' !== eleType) {
                            eleInfo.$section.addClass('premium-cursor-not-active');

                            if ('widget' === eleType) {
                                eleInfo.$col.addClass('premium-cursor-not-active');
                            }
                        }

                        if (eleInfo.isInnerSection) {
                            eleInfo.$parentCol.addClass('premium-cursor-not-active');
                            eleInfo.$parentSec.addClass('premium-cursor-not-active');
                        }

                        // Handle site/page cursor.
                        $('.elementor-page').addClass('premium-cursor-not-active');
                    }

                    // if (['image', 'fimage'].includes(settings.cursorType)) {
                    //     $('.' + uniqueClass).css('display', 'flex');
                    // } else {
                    //     $('.' + uniqueClass).show();
                    // }

                    $('.' + uniqueClass).css('display', 'flex');

                    if (isActivated)
                        self.followMouse(e, props);

                }).mouseout(function () {

                    if ('page' !== eleType) {

                        if ('section' !== eleType) {

                            eleInfo.$section.removeClass('premium-cursor-not-active');

                            if ('widget' === eleType) {
                                eleInfo.$col.removeClass('premium-cursor-not-active');
                            }
                        }

                        if (eleInfo.isInnerSection) {
                            eleInfo.$parentCol.removeClass('premium-cursor-not-active');
                            eleInfo.$parentSec.removeClass('premium-cursor-not-active');
                        }

                        $('.elementor-page').removeClass('premium-cursor-not-active');
                    }

                }).mouseleave(function () {
                    $('.' + uniqueClass).hide();
                });
            };

            self.handleMagnetEffect = function (props, grow) {

                var $hotspots = $scope.find('.premium-image-hotspots-main-icons'),
                    originalWidth = props.width,
                    originalHeight = props.height,
                    grow = settings.magnet_grow;

                if (['image', 'fimage'].includes(settings.cursorType) && grow === '')
                    grow = 1;

                $hotspots.addClass('magnet-icon').append('<div class="magnet-spot"/>');

                $hotspots.find('.magnet-spot').mouseenter(function () {

                    var $this = $(this).closest('.premium-image-hotspots-main-icons');

                    isActivated = false;

                    var spotPosition = $this[0].getBoundingClientRect();

                    $scope.addClass('magnet-effect');

                    if (['image', 'fimage'].includes(settings.cursorType)) {

                        gsap.to('.' + props.elem, 0.3, {
                            width: $this.outerWidth() + grow,
                            height: $this.outerHeight() + grow,
                        });

                    }

                    gsap.to('.' + props.elem, 0.3, {
                        top: spotPosition.top - ((grow ? grow : originalHeight) / 2) + (grow ? 0 : $this.outerHeight() / 2),
                        left: Math.ceil(spotPosition.left - ((grow ? grow : originalWidth) / 2) + (grow ? 0 : $this.outerWidth() / 2))
                    });

                    gsap.to('.' + props.elem + ' .eicon-circle', 0.3, {
                        autoAlpha: 0
                    });

                }).mouseleave(function () {

                    isActivated = true;

                    $scope.removeClass('magnet-effect');

                    gsap.to('.' + props.elem, 0.3, {
                        width: originalWidth,
                        height: originalHeight,
                    });

                    gsap.to('.' + props.elem + ' .eicon-circle', 0.3, {
                        autoAlpha: 1
                    });

                });

            };

            self.followMouse = function (e, props) {

                TweenMax.to('.' + props.elem, settings.magnet ? 0.3 : props.delay, {
                    css: {
                        left: e.clientX + props.extraLeft - props.width,
                        top: e.clientY + props.extraTop - props.width,
                    },
                    ease: Power1.easeOut,
                });
            };

            self.renderSvgIcon = function (url, id) {

                var parser = new DOMParser();

                fetch(url)
                    .then(
                        function (response) {
                            if (200 !== response.status) {
                                console.log('Looks like there was a problem loading your svg. Status Code: ' +
                                    response.status);
                                return;
                            }

                            response.text().then(function (text) {
                                var parsed = parser.parseFromString(text, 'text/html'),
                                    svg = parsed.querySelector('svg');

                                $(svg).attr('class', 'premium-cursor-icon-svg');

                                $scope.find('.premium-global-cursor-' + id).html($(parsed).find('svg'));
                            });
                        }
                    );
            };

            self.getCursorHtml = function (uniqueClass) {

                var settings = cursorSettings,
                    cursorHtml = '<div class="premium-global-cursor ' + uniqueClass + settings.pulse + settings.buzz + '">';

                if ('' !== settings.cursorDot) {
                    cursorHtml += '<i class="eicon-circle"></i>';
                }

                if ('icon' === settings.cursorType) {
                    if ('svg' !== settings.elementSettings.library) {
                        cursorHtml += '<i class=" premium-cursor-icon-fa ' + settings.elementSettings.value + '"></i>';
                    }

                } else if ('image' === settings.cursorType || 'fimage' === settings.cursorType) {
                    cursorHtml += '<img class="premium-cursor-img" src="' + settings.elementSettings.url + '" alt="' + settings.elementSettings.alt + '">';

                } else if ('ftext' === settings.cursorType) {
                    cursorHtml += '<p class="premium-cursor-follow-text">' + settings.elementSettings.text + '</p>';

                } else {
                    cursorHtml += '<div class="premium-lottie-animation premium-cursor-lottie-icon" data-lottie-url="' + settings.elementSettings.url + '" data-lottie-loop="' + settings.elementSettings.loop + '" data-lottie-reverse="' + settings.elementSettings.reverse + '" ></div>';
                }

                cursorHtml += '</div>';

                return cursorHtml;
            };

            self.addCursor = function (uniqueClass, cursor) {

                self.addCursorClasses();

                $scope.find('.' + uniqueClass).remove();

                $scope.prepend(cursor);
            }

            self.addCursorClasses = function () {

                // remove the old type-class( premium-cursor-{type} ) before adding a new one.
                $scope.removeClass(function (index, selector) {
                    return (selector.match(new RegExp("(^|\\s)premium-cursor-\\S+", 'g')) || []).join(' ');
                });

                $scope.addClass('premium-gCursor-yes premium-cursor-' + cursorSettings.cursorType + cursorSettings.cursorDot);
            };

            self.initAddonSwListener = function () {

                elementor.settings.page.addChangeCallback('premium_global_cursor_switcher', self.onAddonSwitcherChange);
            };

            /** Addon's switcher call back function, re-init the cursor on enabling, Destroy it on disabling. */
            self.onAddonSwitcherChange = function (newVal) {

                if ('yes' === newVal) {
                    self.initCustomCursor();

                } else {
                    self.destroy();
                }
            };

            /**
             * This basically acts like the ( render_type => template ) for controls.
             * we'll only add it for controls that does affect the cursor structure.
             */
            self.initControlsCallbacks = function () {

                var renderControls = [
                    'pa_cursor_type',
                    'pa_cursor_pulse',
                    'pa_cursor_buzz',
                    'pa_cursor_xpos',
                    'pa_cursor_ypos',
                    'pa_cursor_icon',
                    'pa_cursor_img',
                    'pa_cursor_ftext',
                    'pa_cursor_trans',
                    'pa_cursor_lottie_url',
                    'pa_cursor_loop',
                    'pa_cursor_reverse'
                ];

                renderControls.forEach(function (control) {
                    elementor.settings.page.addChangeCallback(control, self.onControlChange);
                });
            };

            /** Fires on elementor control change. */
            self.onControlChange = function () {

                self.refreshCursor();
            };

            self.getPageId = function () {

                return elementorFrontend.config.post.id;
            };

            self.refreshCursor = function () {

                self.initPageSettings();

                self.generateCursorSettings();

                self.generateCursor();
            };

            self.destroy = function () {

                $scope.removeClass('premium-gCursor-yes premium-cursor-' + cursorSettings.cursorType);

                $('.premium-global-cursor-' + cursorSettings.elemId).remove();
            };
        };

        /**
         * Check site cursor.
         */
        // this should be delayed a bit ?
        $pageCursorEnabled = $('body').hasClass('premium-gCursor-yes');

        // only check for site cursor if the page cursor is disabled.
        if (!$pageCursorEnabled) {

            $siteCursorEnabled = $('.premium-site-cursor').length ? true : false;
            $mobileDisabled = $('.premium-site-cursor').data('pa_mobile_disabled');

            if ($siteCursorEnabled && !$mobileDisabled) {

                $siteCursorSettings = $('.premium-site-cursor').data('premium_site_cursor');

                var cursorInstance = new paCustomCursorHandler('page', $('body'), $siteCursorSettings);

                cursorInstance.generateCursor();
            }
        }
    });

    $(window).on('elementor/frontend/init', function () {

        // we add paCustomCursorHandler on content load but only initialize it on elementor/init.
        if (undefined != window.paCustomCursorHandler) {
            var cursorObj = new paCustomCursorHandler('page', $('.elementor-page'), {});
            cursorObj.init();
        }
    });

})(jQuery);