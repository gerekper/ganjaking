/**
 * Start news ticker widget script
 */

(function ($) {
    "use strict";
    $.epNewsTickerOld = function (element, options) {

        var defaults = {
            effect: 'fade',
            direction: 'ltr',
            autoPlay: false,
            interval: 4000,
            scrollSpeed: 2,
            pauseOnHover: false,
            position: 'auto',
            zIndex: 99999
        }

        var ticker = this;
        ticker.settings = {};
        ticker._element = $(element);

        ticker._label = ticker._element.children(".bdt-news-ticker-label"),
            ticker._news = ticker._element.children(".bdt-news-ticker-content"),
            ticker._ul = ticker._news.children("ul"),
            ticker._li = ticker._ul.children("li"),
            ticker._controls = ticker._element.children(".bdt-news-ticker-controls"),
            ticker._prev = ticker._controls.find(".bdt-news-ticker-prev").parent(),
            ticker._action = ticker._controls.find(".bdt-news-ticker-action").parent(),
            ticker._next = ticker._controls.find(".bdt-news-ticker-next").parent();

        ticker._pause = false;
        ticker._controlsIsActive = true;
        ticker._totalNews = ticker._ul.children("li").length;
        ticker._activeNews = 0;
        ticker._interval = false;
        ticker._frameId = null;

        /****************************************************/
        /**PRIVATE METHODS***********************************/
        /****************************************************/

        var setContainerWidth = function () {
            if (ticker._label.length > 0) {
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": ticker._label.outerWidth()
                    });
                else
                    ticker._news.css({
                        "left": ticker._label.outerWidth()
                    });
            }

            if (ticker._controls.length > 0) {
                var controlsWidth = ticker._controls.outerWidth();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "left": controlsWidth
                    });
                else
                    ticker._news.css({
                        "right": controlsWidth
                    });
            }

            if (ticker.settings.effect === 'scroll') {
                var totalW = 0;
                ticker._li.each(function () {
                    totalW += $(this).outerWidth();
                });
                totalW += 50;
                ticker._ul.css({
                    'width': totalW
                });
            }
        }


        var startScrollAnimationLTR = function () {
            var _ulPosition = parseFloat(ticker._ul.css('marginLeft'));
            _ulPosition -= ticker.settings.scrollSpeed / 2;
            ticker._ul.css({
                'marginLeft': _ulPosition
            });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth()) {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginLeft': 0
                });
            }
            if (ticker._pause === false) {
                ticker._frameId = requestAnimationFrame(startScrollAnimationLTR);
                (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationLTR, 16);
            }
        }

        var startScrollAnimationRTL = function () {
            var _ulPosition = parseFloat(ticker._ul.css('marginRight'));
            _ulPosition -= ticker.settings.scrollSpeed / 2;
            ticker._ul.css({
                'marginRight': _ulPosition
            });

            if (_ulPosition <= -ticker._ul.find('li:first-child').outerWidth()) {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginRight': 0
                });
            }
            if (ticker._pause === false)
                ticker._frameId = requestAnimationFrame(startScrollAnimationRTL);
            (window.requestAnimationFrame && ticker._frameId) || setTimeout(startScrollAnimationRTL, 16);
        }

        var scrollPlaying = function () {
            if (ticker.settings.direction === 'rtl') {
                if (ticker._ul.width() > ticker._news.width())
                    startScrollAnimationRTL();
                else
                    ticker._ul.css({
                        'marginRight': 0
                    });
            } else
            if (ticker._ul.width() > ticker._news.width())
                startScrollAnimationLTR();
            else
                ticker._ul.css({
                    'marginLeft': 0
                });
        }

        var scrollGoNextLTR = function () {
            ticker._ul.stop().animate({
                marginLeft: -ticker._ul.find('li:first-child').outerWidth()
            }, 300, function () {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginLeft': 0
                });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoNextRTL = function () {
            ticker._ul.stop().animate({
                marginRight: -ticker._ul.find('li:first-child').outerWidth()
            }, 300, function () {
                ticker._ul.find('li:first-child').insertAfter(ticker._ul.find('li:last-child'));
                ticker._ul.css({
                    'marginRight': 0
                });
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevLTR = function () {
            var _ulPosition = parseInt(ticker._ul.css('marginLeft'), 10);
            if (_ulPosition >= 0) {
                ticker._ul.css({
                    'margin-left': -ticker._ul.find('li:last-child').outerWidth()
                });
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginLeft: 0
            }, 300, function () {
                ticker._controlsIsActive = true;
            });
        }

        var scrollGoPrevRTL = function () {
            var _ulPosition = parseInt(ticker._ul.css('marginRight'), 10);
            if (_ulPosition >= 0) {
                ticker._ul.css({
                    'margin-right': -ticker._ul.find('li:last-child').outerWidth()
                });
                ticker._ul.find('li:last-child').insertBefore(ticker._ul.find('li:first-child'));
            }

            ticker._ul.stop().animate({
                marginRight: 0
            }, 300, function () {
                ticker._controlsIsActive = true;
            });
        }

        var scrollNext = function () {
            if (ticker.settings.direction === 'rtl')
                scrollGoNextRTL();
            else
                scrollGoNextLTR();
        }

        var scrollPrev = function () {
            if (ticker.settings.direction === 'rtl')
                scrollGoPrevRTL();
            else
                scrollGoPrevLTR();
        }

        var effectTypography = function () {
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).width(30).show();
            ticker._ul.find('li').eq(ticker._activeNews).animate({
                width: '100%',
                opacity: 1
            }, 1500);
        }

        var effectFade = function () {
            ticker._ul.find('li').hide();
            ticker._ul.find('li').eq(ticker._activeNews).fadeIn();
        }

        var effectSlideDown = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'top': 30,
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'top': -30,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'top': 30,
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': -30,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideUp = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'top': -30,
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'top': 30,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'top': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'top': -30,
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'top': 30,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'top': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideRight = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'left': '50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'left': -50,
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'left': '50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': -50,
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity': 1
                }, 300);
            }
        }

        var effectSlideLeft = function () {
            if (ticker._totalNews <= 1) {
                ticker._ul.find('li').animate({
                    'left': '-50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).css({
                        'left': '50%',
                        'opacity': 0,
                        'display': 'block'
                    })
                    $(this).animate({
                        'left': 0,
                        'opacity': 1
                    }, 300);
                });
            } else {
                ticker._ul.find('li:visible').animate({
                    'left': '-50%',
                    'opacity': 0
                }, 300, function () {
                    $(this).hide();
                });

                ticker._ul.find('li').eq(ticker._activeNews).css({
                    'left': '50%',
                    'opacity': 0
                }).show();

                ticker._ul.find('li').eq(ticker._activeNews).animate({
                    'left': 0,
                    'opacity': 1
                }, 300);
            }
        }


        var showThis = function () {
            ticker._controlsIsActive = true;

            switch (ticker.settings.effect) {
                case 'typography':
                    effectTypography();
                    break;
                case 'fade':
                    effectFade();
                    break;
                case 'slide-down':
                    effectSlideDown();
                    break;
                case 'slide-up':
                    effectSlideUp();
                    break;
                case 'slide-right':
                    effectSlideRight();
                    break;
                case 'slide-left':
                    effectSlideLeft();
                    break;
                default:
                    ticker._ul.find('li').hide();
                    ticker._ul.find('li').eq(ticker._activeNews).show();
            }

        }

        var nextHandler = function () {
            switch (ticker.settings.effect) {
                case 'scroll':
                    scrollNext();
                    break;
                default:
                    ticker._activeNews++;
                    if (ticker._activeNews >= ticker._totalNews)
                        ticker._activeNews = 0;

                    showThis();

            }
        }

        var prevHandler = function () {
            switch (ticker.settings.effect) {
                case 'scroll':
                    scrollPrev();
                    break;
                default:
                    ticker._activeNews--;
                    if (ticker._activeNews < 0)
                        ticker._activeNews = ticker._totalNews - 1;

                    showThis();
            }
        }

        var playHandler = function () {
            ticker._pause = false;
            if (ticker.settings.autoPlay) {
                switch (ticker.settings.effect) {
                    case 'scroll':
                        scrollPlaying();
                        break;
                    default:
                        ticker.pause();
                        ticker._interval = setInterval(function () {
                            ticker.next();
                        }, ticker.settings.interval);
                }
            }
        }

        var resizeEvent = function () {
            if (ticker._element.width() < 480) {
                ticker._label.hide();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": 0
                    });
                else
                    ticker._news.css({
                        "left": 0
                    });
            } else {
                ticker._label.show();
                if (ticker.settings.direction == 'rtl')
                    ticker._news.css({
                        "right": ticker._label.outerWidth()
                    });
                else
                    ticker._news.css({
                        "left": ticker._label.outerWidth()
                    });
            }
        }

        /****************************************************/
        /**PUBLIC METHODS************************************/
        /****************************************************/
        ticker.init = function () {
            ticker.settings = $.extend({}, defaults, options);

            //ticker._element.append('<div class="bdt-breaking-loading"></div>');
            //window.onload = function(){

            //ticker._element.find('.bdt-breaking-loading').hide();

            //adding effect type class
            ticker._element.addClass('bdt-effect-' + ticker.settings.effect + ' bdt-direction-' + ticker.settings.direction);

            setContainerWidth();

            if (ticker.settings.effect != 'scroll')
                showThis();

            playHandler();

            //set playing status class
            if (!ticker.settings.autoPlay)
                ticker._action.find('span').removeClass('bdt-news-ticker-pause').addClass('bdt-news-ticker-play');
            else
                ticker._action.find('span').removeClass('bdt-news-ticker-play').addClass('bdt-news-ticker-pause');


            ticker._element.on('mouseleave', function (e) {
                var activePosition = $(document.elementFromPoint(e.clientX, e.clientY)).parents('.bdt-breaking-news')[0];
                if ($(this)[0] === activePosition) {
                    return;
                }


                if (ticker.settings.pauseOnHover === true) {
                    if (ticker.settings.autoPlay === true)
                        ticker.play();
                } else {
                    if (ticker.settings.autoPlay === true && ticker._pause === true)
                        ticker.play();
                }

            });

            ticker._element.on('mouseenter', function () {
                if (ticker.settings.pauseOnHover === true)
                    ticker.pause();
            });

            ticker._next.on('click', function () {
                if (ticker._controlsIsActive) {
                    ticker._controlsIsActive = false;
                    ticker.pause();
                    ticker.next();
                }
            });

            ticker._prev.on('click', function () {
                if (ticker._controlsIsActive) {
                    ticker._controlsIsActive = false;
                    ticker.pause();
                    ticker.prev();
                }
            });

            ticker._action.on('click', function () {
                if (ticker._controlsIsActive) {
                    if (ticker._action.find('span').hasClass('bdt-news-ticker-pause')) {
                        ticker._action.find('span').removeClass('bdt-news-ticker-pause').addClass('bdt-news-ticker-play');
                        ticker.stop();
                    } else {
                        ticker.settings.autoPlay = true;
                        ticker._action.find('span').removeClass('bdt-news-ticker-play').addClass('bdt-news-ticker-pause');
                        //ticker._pause = false;
                    }
                }
            });

            resizeEvent();
            //}

            $(window).on('resize', function () {
                resizeEvent();
                ticker.pause();
                ticker.play();
            });

        }

        ticker.pause = function () {
            ticker._pause = true;
            clearInterval(ticker._interval);
            cancelAnimationFrame(ticker._frameId);
        }

        ticker.stop = function () {
            ticker._pause = true;
            ticker.settings.autoPlay = false;
        }

        ticker.play = function () {
            playHandler();
        }

        ticker.next = function () {
            nextHandler();
        }

        ticker.prev = function () {
            prevHandler();
        }
        /****************************************************/
        /****************************************************/
        /****************************************************/
        ticker.init();

    }

    $.fn.epNewsTickerOld = function (options) {

        return this.each(function () {
            if (undefined == $(this).data('epNewsTickerOld')) {
                var ticker = new $.epNewsTickerOld(this, options);
                $(this).data('epNewsTickerOld', ticker);
            }
        });

    }

})(jQuery);



(function ($, elementor) {

    'use strict';

    var widgetNewsTicker = function ($scope, $) {

        var $newsTicker = $scope.find('.bdt-news-ticker'),
            $settings = $newsTicker.data('settings');

        if (!$newsTicker.length) {
            return;
        }

        $($newsTicker).epNewsTickerOld($settings);

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-news-ticker.default', widgetNewsTicker);
    });

}(jQuery, window.elementorFrontend));

/**
 * End news ticker widget script
 */
