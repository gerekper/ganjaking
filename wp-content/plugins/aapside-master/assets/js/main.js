function isTouchDevice() {
    return 'ontouchstart' in window // works on most browsers
        || 'onmsgesturechange' in window; // works on ie10
}
function isMobileDevice() {
    return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
};

(function ($) {
    "use strict";

    /*---------------------------------------------------
      * Initialize all widget js in elementor init hook
      ---------------------------------------------------*/
    $(window).on('elementor/frontend/init', function ($scope) {

        elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope, $) {
            activeTestimonialSliderThree();
            activeTestimonialSliderTwo();
            activeScreenshortSlider();
            activeBrandSlider();
            activeScreenshortSliderTwo();
            activeTeamMemberSlider();
            activeTestimonialSlider();
            activeTestimonialFourSlider();
            activeBlogSliderOne();
            countdownInit();
            activeIsotopeFilter();

            if (!elementorFrontend.isEditMode()) {
                activeAnimationExtend($scope);
                $(window).on('scroll', function () {
                    if ($(window).width() > 991) {
                        stickyMenu();
                    }
                });
            }
        });
    });

    /*-------------------------------------------
               Portfolio Filter
   --------------------------------------------*/
    function activeIsotopeFilter() {

        var postFilter = $('.appside-isotope-init');
        if (postFilter.length < 1) {
            return;
        }

        $.each(postFilter, function (index, value) {
            var el = $(this);
            var parentClass = $(this).parent().attr('class');
            var $selector = $('#' + el.attr('id'));

            $($selector).imagesLoaded(function () {
                var festivarMasonry = $($selector).isotope({
                    itemSelector: '.appside-masonry-item',
                    percentPosition: true,
                    masonry: {
                        columnWidth: 0,
                        gutter: 0
                    }
                });
                $(document).on('click', '.' + parentClass + ' .appside-isotope-nav ul li', function () {
                    var filterValue = $(this).attr('data-filter');
                    festivarMasonry.isotope({
                        filter: filterValue
                    });
                });
            });
        });

        /*----------------------------
            portfolio menu active
         ----------------------------*/
        $(document).on('click', '.appside-isotope-nav ul li', function () {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });
    }

    /*--------------------------
      sticky menu activation
    ---------------------------*/
    function stickyMenu() {
        var st = $(this).scrollTop();
        var mainMenuTop = $('.navbar-area');
        if ($(window).scrollTop() > 1000) {
            mainMenuTop.addClass('nav-fixed');
        } else {
            mainMenuTop.removeClass('nav-fixed ');
        }
    }

    /*---------------------------------
    *
    * -------------------------------*/
    function activeAnimationExtend($scope) {
        if (elementorFrontend.isEditMode()) {
            var elementSettings = {};
            var modelCID = $scope.data('model-cid');

            var settings = elementorFrontend.config.elements.data[modelCID];
            if (typeof settings != 'undefined') {
                var type = settings.attributes.widgetType || settings.attributes.elType,
                    settingsKeys = elementorFrontend.config.elements.keys[type];

                if (!settingsKeys) {
                    settingsKeys = elementorFrontend.config.elements.keys[type] = [];

                    $.each(settings.controls, function (name, control) {
                        if (control.frontend_available) {
                            settingsKeys.push(name);
                        }
                    });
                }

                $.each(settings.getActiveControls(), function (controlKey) {
                    if (-1 !== settingsKeys.indexOf(controlKey)) {
                        elementSettings[controlKey] = settings.attributes[controlKey];
                    }
                });

                var widgetExt = elementSettings;
            }
        } else {
            //Get widget settings data
            var widgetExtObj = $scope.attr('data-settings');

            if (typeof widgetExtObj != 'undefined') {
                var widgetExt = JSON.parse(widgetExtObj);
            }
        }

        if (typeof widgetExt != 'undefined') {
            //Begin scroll animation extensions
            if (typeof widgetExt.appside_sec_extends_is_scrollme != 'undefined' && widgetExt.appside_sec_extends_is_scrollme == 'true') {
                var scrollArgs = {};

                if (typeof widgetExt.appside_sec_extends_scrollme_scalex.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_scalex.size != 1) {
                    scrollArgs['scaleX'] = widgetExt.appside_sec_extends_scrollme_scalex.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_scaley.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_scaley.size != 1) {
                    scrollArgs['scaleY'] = widgetExt.appside_sec_extends_scrollme_scaley.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_scalez.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_scalez.size != 1) {
                    scrollArgs['scaleZ'] = widgetExt.appside_sec_extends_scrollme_scalez.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_rotatex.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_rotatex.size != 0) {
                    scrollArgs['rotateX'] = widgetExt.appside_sec_extends_scrollme_rotatex.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_rotatey.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_rotatey.size != 0) {
                    scrollArgs['rotateY'] = widgetExt.appside_sec_extends_scrollme_rotatey.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_rotatez.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_rotatez.size != 0) {
                    scrollArgs['rotateY'] = widgetExt.appside_sec_extends_scrollme_rotatez.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_translatex.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_translatex.size != 0) {
                    scrollArgs['x'] = widgetExt.appside_sec_extends_scrollme_translatex.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_translatey.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_translatey.size != 0) {
                    scrollArgs['y'] = widgetExt.appside_sec_extends_scrollme_translatey.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_translatez.size != 'undefined' && widgetExt.appside_sec_extends_scrollme_translatez.size != 0) {
                    scrollArgs['z'] = widgetExt.appside_sec_extends_scrollme_translatez.size;
                }

                if (typeof widgetExt.appside_sec_extends_scrollme_smoothness.size != 'undefined') {
                    scrollArgs['smoothness'] = widgetExt.appside_sec_extends_scrollme_smoothness.size;
                }

                $scope.attr('data-parallax', JSON.stringify(scrollArgs));

                if (typeof widgetExt.appside_sec_extends_scrollme_disable != 'undefined') {
                    if (widgetExt.appside_sec_extends_scrollme_disable == 'mobile') {
                        if (parseInt($(window).width()) < 501) {
                            $scope.addClass('noanimation');
                        }
                    }

                    if (widgetExt.appside_sec_extends_scrollme_disable == 'tablet') {
                        if (parseInt($(window).width()) < 769) {
                            $scope.addClass('noanimation');
                        }
                    }

                    $(window).resize(function () {
                        if (widgetExt.appside_sec_extends_scrollme_disable == 'mobile') {
                            if (isMobileDevice() || parseInt($(window).width()) < 501) {
                                $scope.addClass('noanimation');
                            } else {
                                $scope.removeClass('noanimation');
                            }
                        }

                        if (widgetExt.appside_sec_extends_scrollme_disable == 'tablet') {
                            if (parseInt($(window).width()) < 769) {
                                $scope.addClass('noanimation');
                            } else {
                                $scope.removeClass('noanimation');
                            }
                        }
                    });
                }
            }
            //End scroll animation extensions

            //Begin entrance animation extensions
            if (typeof widgetExt.appside_sec_extends_is_smoove != 'undefined' && widgetExt.appside_sec_extends_is_smoove == 'true') {
                $scope.addClass('init-smoove');

                $scope.smoove({
                    min_width: parseInt(widgetExt.appside_sec_extends_smoove_disable),

                    scaleX: widgetExt.appside_sec_extends_smoove_scalex.size,
                    scaleY: widgetExt.appside_sec_extends_smoove_scaley.size,

                    rotateX: parseInt(widgetExt.appside_sec_extends_smoove_rotatex.size) + 'deg',
                    rotateY: parseInt(widgetExt.appside_sec_extends_smoove_rotatey.size) + 'deg',
                    rotateZ: parseInt(widgetExt.appside_sec_extends_smoove_rotatez.size) + 'deg',

                    moveX: parseInt(widgetExt.appside_sec_extends_smoove_translatex.size) + 'px',
                    moveY: parseInt(widgetExt.appside_sec_extends_smoove_translatey.size) + 'px',
                    moveZ: parseInt(widgetExt.appside_sec_extends_smoove_translatez.size) + 'px',

                    skewX: parseInt(widgetExt.appside_sec_extends_smoove_skewx.size) + 'deg',
                    skewY: parseInt(widgetExt.appside_sec_extends_smoove_skewy.size) + 'deg',

                    perspective: parseInt(widgetExt.appside_sec_extends_smoove_perspective.size),

                    offset: '-10%',
                });

                if (typeof widgetExt.appside_sec_extends_smoove_duration != 'undefined') {
                    $scope.css('transition-duration', parseInt(widgetExt.appside_sec_extends_smoove_duration) + 'ms');
                }

                var width = $(window).width();
                if (widgetExt.appside_sec_extends_smoove_disable >= width) {
                    if (!$scope.hasClass('smooved')) {
                        $scope.addClass('no-smooved');
                    }

                    return false;
                }
            }
            //End entrance animation extensions


            //Begin mouse parallax extensions
            if (typeof widgetExt.appside_sec_extends_is_parallax_mouse != 'undefined' && widgetExt.appside_sec_extends_is_parallax_mouse == 'true') {
                var elementID = $scope.attr('data-id');
                $scope.find('.elementor-widget-container').attr('data-depth', parseFloat(widgetExt.appside_sec_extends_is_parallax_mouse_depth.size));
                $scope.attr('ID', 'parallax-' + elementID);

                var parentElement = document.getElementById('parallax-' + elementID);
                var parallax = new Parallax(parentElement, {
                    relativeInput: true
                });

                if (elementorFrontend.isEditMode()) {
                    if ($scope.width() == 0) {
                        $scope.css('width', '100%');
                    }

                    if ($scope.height() == 0) {
                        $scope.css('height', '100%');
                    }
                }
            }
            //End mouse parallax extensions


            //Begin infinite animation extensions
            if (typeof widgetExt.appside_sec_extends_is_infinite != 'undefined' && widgetExt.appside_sec_extends_is_infinite == 'true') {
                var animationClass = '';
                var keyframeName = '';
                var animationCSS = '';

                if (typeof widgetExt.appside_sec_extends_infinite_animation != 'undefined') {
                    animationClass = widgetExt.appside_sec_extends_infinite_animation;

                    switch (animationClass) {
                        case 'if_swing1':
                            keyframeName = 'appsideSwing';
                            break;

                        case 'if_swing2':
                            keyframeName = 'appsideSwing2';
                            break;

                        case 'if_wave':
                            keyframeName = 'appsideWave';
                            break;

                        case 'if_tilt':
                            keyframeName = 'appsideTilt';
                            break;

                        case 'if_bounce':
                            keyframeName = 'appsideBounce';
                            break;

                        case 'if_scale':
                            keyframeName = 'appsideScale';
                            break;

                        case 'if_spin':
                            keyframeName = 'appsideSpin';
                            break;
                    }

                    animationCSS += keyframeName + ' ';
                }
                if (typeof widgetExt.appside_sec_extends_infinite_duration != 'undefined') {
                    animationCSS += widgetExt.appside_sec_extends_infinite_duration + 's ';
                }

                animationCSS += 'infinite alternate ';

                if (typeof widgetExt.appside_sec_extends_infinite_easing != 'undefined') {
                    animationCSS += 'cubic-bezier(' + widgetExt.appside_sec_extends_infinite_easing + ')';
                }
                $scope.css({
                    'animation': animationCSS,
                });
                $scope.addClass(animationClass);
            }
            //End infinite animation extensions
        }
    }

    /*----------------------------------
        Brand Slider Widget
    --------------------------------*/
    function activeBlogSliderOne() {
        if ($('.appside-blog-carousel-01').length < 1) {
            return;
        }
        var brandCarouselOne = $('.appside-blog-carousel-01');

        $.each(brandCarouselOne, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                414: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 2
                },
                768: {
                    items: 2
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            };

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    /*----------------------------------
        Brand Slider Widget
    --------------------------------*/
    function activeBrandSlider() {
        if ($('.brands-carousel').length < 1) {
            return;
        }
        var brandCarouselOne = $('.brands-carousel');

        $.each(brandCarouselOne, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                360: {
                    items: 2
                },
                414: {
                    items: 2
                },
                460: {
                    items: 2
                },
                599: {
                    items: 3
                },
                768: {
                    items: 3
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            };

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    /*----------------------------------
        Screenshort Slider Widget
    --------------------------------*/
    function activeScreenshortSlider() {
        if ($('.screenshort-carousel').length < 1) {
            return;
        }
        var ScreenshortCarouselOne = $('.screenshort-carousel');

        $.each(ScreenshortCarouselOne, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 2
                },
                768: {
                    items: 2
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            };

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    function activeScreenshortSliderTwo() {
        if ($('.screenshort-carousel-02').length < 1) {
            return;
        }
        var ScreenshortCarouselTwo = $('.screenshort-carousel-02');

        $.each(ScreenshortCarouselTwo, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 2
                },
                768: {
                    items: 2
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            }

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    function activeTestimonialSliderTwo() {
        if ($('.appside-testimonial-carousel-02').length < 1) {
            return;
        }
        var testimonialCarouselTwo = $('.appside-testimonial-carousel-02');

        $.each(testimonialCarouselTwo, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1,
                    nav: false,
                    center: false,
                    stagePadding: 10
                },
                414: {
                    items: 1,
                    nav: false,
                    center: false,
                    stagePadding: 10
                },
                767: {
                    items: 2,
                    nav: false,
                    center: false,
                    stagePadding: 10
                },
                768: {
                    items: 2,
                    nav: false,
                    center: false,
                    stagePadding: 10
                },
                960: {
                    items: items,
                    nav: false,
                    center: false
                },
                1200: {
                    items: items,
                    nav: false,
                    stagePadding: 10
                },
                1920: {
                    items: items
                }
            }

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInitWidthStagePadding($selector, sliderSettings, responsive);

        });
    }

    /*----------------------------------
        team member Slider Widget
    --------------------------------*/
    function activeTeamMemberSlider() {
        if ($('.team-carousel').length < 1) {
            return;
        }
        var TeamMemberCarouselOne = $('.team-carousel');

        $.each(TeamMemberCarouselOne, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 2
                },
                768: {
                    items: 3
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            }

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    /*----------------------------------
        testimonial Slider Widget
    --------------------------------*/
    function activeTestimonialSlider() {
        if ($('.testimonial-carousel').length < 1) {
            return;
        }
        var activeTestimonialSlider = $('.testimonial-carousel');

        $.each(activeTestimonialSlider, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 1
                },
                768: {
                    items: 1
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            }

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    /*----------------------------------
        testimonial Slider Four Widget
    --------------------------------*/
    function activeTestimonialFourSlider() {
        if ($('.appside-testimonial-carousel-04').length < 1) {
            return;
        }
        var activeTestimonialSlider = $('.appside-testimonial-carousel-04');

        $.each(activeTestimonialSlider, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 1
                },
                768: {
                    items: 1
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            }

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    /*----------------------------------
        testimonial Slider Widget
    --------------------------------*/
    function activeTestimonialSliderThree() {
        if ($('.appside-testimonial-carousel-03').length < 1) {
            return;
        }
        var TestimonialSliderthree = $('.appside-testimonial-carousel-03');

        $.each(TestimonialSliderthree, function (index, value) {
            let el = $(this);
            let $selector = $('#' + el.attr('id'));
            let loop = el.data('loop');
            let items = el.data('items');
            let autoplay = el.data('autoplay');
            let margin = el.data('margin');
            let dots = false;
            let nav = false;
            let autoplaytimeout = el.data('autoplaytimeout');
            let responsive = {
                0: {
                    items: 1
                },
                460: {
                    items: 1
                },
                599: {
                    items: 1
                },
                768: {
                    items: 1
                },
                960: {
                    items: items
                },
                1200: {
                    items: items
                },
                1920: {
                    items: items
                }
            }

            var sliderSettings = {
                "items": items,
                "loop": loop,
                "dots": dots,
                "margin": margin,
                "autoplay": autoplay,
                "autoPlayTimeout": autoplaytimeout,
                "nav": nav,
                "navtext": ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],

            };

            wowCarouselInit($selector, sliderSettings, responsive, 'fadeIn', 'fadeOut');

        });
    }

    //owl init function
    function wowCarouselInit($selector, sliderSettings, responsive, animateIn = false, animateOut = false) {
        $($selector).owlCarousel({
            loop: sliderSettings.loop,
            autoplay: sliderSettings.autoplay, //true if you want enable autoplay
            autoPlayTimeout: sliderSettings.autoPlayTimeout,
            margin: sliderSettings.margin,
            dots: sliderSettings.dots,
            nav: sliderSettings.nav,
            navText: sliderSettings.navtext,
            animateIn: animateIn,
            animateOut: animateOut,
            responsive: responsive,
            smartSpeed: 2000
        });
    }

    function wowCarouselInitWidthStagePadding($selector, sliderSettings, responsive, animateIn = false, animateOut = false) {
        $($selector).owlCarousel({
            loop: sliderSettings.loop,
            autoplay: sliderSettings.autoplay, //true if you want enable autoplay
            autoPlayTimeout: sliderSettings.autoPlayTimeout,
            margin: sliderSettings.margin,
            dots: sliderSettings.dots,
            nav: sliderSettings.nav,
            navText: sliderSettings.navtext,
            animateIn: animateIn,
            animateOut: animateOut,
            responsive: responsive,
            center: true,
            stagePadding: 100,
            smartSpeed: 2000
        });
    }

    /**-----------------------------
     *  countdown
     * ---------------------------*/
    function countdownInit(){
        var mycountdown = $(".mycountdown");
        if (mycountdown.length > 0) {
            var countdownTime = mycountdown.data('countdown');
            mycountdown.countdown(countdownTime, function (event) {
                $('.month').text(
                    event.strftime('%m')
                );
                $('.days').text(
                    event.strftime('%n')
                );
                $('.hours').text(
                    event.strftime('%H')
                );
                $('.mins').text(
                    event.strftime('%M')
                );
                $('.secs').text(
                    event.strftime('%S')
                );
            });
        }
    }


    $(document).ready(function () {

        /*------------------------------
          counter section activation
        -------------------------------*/
        var counternumber = $('.count-num');
        counternumber.counterUp({
            delay: 20,
            time: 1000
        });

        /*---------------------------------
        * Magnific Popup
        * --------------------------------*/
        $('.video-play-btn,.play-video-btn,.video-btn-one .icon a').magnificPopup({
            type: 'video'
        });

    });

})(jQuery);