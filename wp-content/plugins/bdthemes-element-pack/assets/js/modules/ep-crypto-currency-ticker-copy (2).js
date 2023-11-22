/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-ticker'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

            if (!$cryptoWidget.length) {
                return;
            }

        // Crypto Data
        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        if ($settings.limit) {
            $options.limit = $settings.limit
        }
        if ($settings.order) {
            $options.order = $settings.order
        }

        let currency_selected, cryptoDataSettingsValue = $options;

        function getData() {
            $.ajax({
                type: "GET",
                // dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto_data',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-ticker-item');
                /**
                 * @idPriceColumnArray is holding data from current items
                 */

                let idPriceColumnArray = [];
                for (let i = 0; i < itemData.length; i++) {
                    idPriceColumnArray.push({
                        // id: itemData[i]["id"],
                        id: $(itemData[i]).data('id'),
                        current_price: parseFloat($(itemData[i]).find('.bdt-price-text').text()),
                    });
                }

                // console.log(idPriceColumnArray);

                /**
                 * @crypDataParse holding data from crypto live data server
                 */
                let cryptDataParse = JSON.parse(data);
                /**
                 * changes array
                 */
                let changesIdArray = [];
                /**
                 * now have to compare this two array of object
                 */
                for (let i = 0; i < idPriceColumnArray.length; i++) {
                    $.map(cryptDataParse, function (elem, index) {
                        if (elem.id === idPriceColumnArray[i].id) {
                            // console.log(elem.current_price);
                            if (elem.current_price !== idPriceColumnArray[i].current_price) {
                                changesIdArray.push({
                                    id: idPriceColumnArray[i].id,
                                    current_price: elem.current_price,
                                    old_price: idPriceColumnArray[i].current_price,
                                });
                            }
                        }
                    });
                }

                // console.log(changesIdArray);

                if (changesIdArray.length !== 0) {
                    changesIdArray.forEach(element => {
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').addClass('data-changed');
                        $($cryptoWidget).find('[data-id="' + element.id + '"]').find('.bdt-price-text').text(element.current_price);

                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;
                        $($cryptoWidget).find('[data-id="' + element.id + '"] .price-int').text(amount);

                    });
                }


                setTimeout(function () {
                    $($cryptoWidget).find('.bdt-crypto-currency-ticker-item').removeClass('data-changed');
                    return getData();
                }, 10000);
            });
        }

        /**
         * number format
         */
        function numFormatter(num) {
            if (num > 999 && num < 1000000) {
                return (num / 1000).toFixed(2) + 'K'; // convert to K for number from > 1000 < 1 million 
            } else if (num > 1000000000) {
                return (num / 1000000000).toFixed(2) + 'B'; // convert to M for number from > 1 million 
            } else if (num > 1000000) {
                return (num / 1000000).toFixed(2) + 'M'; // convert to M for number from > 1 million 
            } else if (num < 900) {
                return num; // if value < 1000, nothing to do
            }
        }
        /**
         * defalt onload call will be here
         */
        if (cryptoDataSettingsValue !== undefined && cryptoDataSettingsValue.currency !== undefined) {
            currency_selected = cryptoDataSettingsValue.currency;
        } else {
            currency_selected = "usd"; // default currency settings here
        }

        $.ajax({
            type: "GET",
            dataType: "json",
            url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
            data: {
                currency: currency_selected,
                per_page: cryptoDataSettingsValue.limit, //limit
                order: cryptoDataSettingsValue.order,
                ids: $settings.ids
            },
            success: function (result) {
                $($cryptoWidget).find('ul').empty();
                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > cryptoDataSettingsValue.limit) {
                        return;
                    }
                    // console.log(element.id);
                    let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                    // let formatAmount = numFormatter(element.current_price);
                    let amount = returnCurrencySymbol(upperCaseCurrncyCode) + element.current_price;

                    let data = element.price_change_percentage_1h;
                    let OneHourData = (Number(data) === data && data % 1 !== 0) ? data.toFixed(2) + "%" : data + "%";

                    var img_html = '';
                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_change_percentage_1h_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-crypto-currency-ticker-img">
                        <img src="${element.image}" alt="${element.id}">
                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<h3 class="bdt-crypto-currency-ticker-title">
                        ${element.id} ${symble_html}
                    </h3>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<span class="bdt-crypto-currency-ticker-price">${amount}</span>`;
                    }
                    if (true == $settings.showPriceChangePercentage) {
                        price_change_percentage_1h_html = `<div class="bdt-crypto-currency-ticker-percentage">
                        <svg width="25" height="22" viewBox="0 0 20 20" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M6.646 11.646a.5.5 0 01.708 0L10 14.293l2.646-2.647a.5.5 0 01.708.708l-3 3a.5.5 0 01-.708 0l-3-3a.5.5 0 010-.708z"
                                clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                                d="M10 4.5a.5.5 0 01.5.5v9a.5.5 0 01-1 0V5a.5.5 0 01.5-.5z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>${OneHourData}</span>
                    </div>`;
                    }

                    var output = `<li class="bdt-crypto-currency-ticker-item" data-id="${element.id}">

                                <div class="bdt-crypto-currency-ticker-inner-item">
                                    ${img_html}
                                    <div class="bdt-crypto-currency-ticker-content">
                                        ${name_html}
                                        ${price_html}
                                        ${price_change_percentage_1h_html}
                                    </div>
                                </div>

                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                             </li>`;

                    $($cryptoWidget).find('ul').append(output);

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }








        //ticker js
        $.epNewsTicker = function (element, options) {

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

            ticker._label = ticker._element.children(".bdt-crypto-currency-ticker-label"),
                ticker._news = ticker._element.children(".bdt-crypto-currency-ticker-inner"),
                ticker._ul = ticker._news.children("ul"),
                ticker._li = ticker._ul.children("li.bdt-crypto-currency-ticker-item"),
                ticker._controls = ticker._element.children(".bdt-crypto-currency-ticker-controls"),
                ticker._prev = ticker._controls.find(".bdt-crypto-currency-ticker-prev").parent(),
                ticker._action = ticker._controls.find(".bdt-crypto-currency-ticker-action").parent(),
                ticker._next = ticker._controls.find(".bdt-crypto-currency-ticker-next").parent();

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
                    ticker._action.find('span').removeClass('bdt-crypto-currency-ticker-pause').addClass('bdt-crypto-currency-ticker-play');
                else
                    ticker._action.find('span').removeClass('bdt-crypto-currency-ticker-play').addClass('bdt-crypto-currency-ticker-pause');


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
                        if (ticker._action.find('span').hasClass('bdt-crypto-currency-ticker-pause')) {
                            ticker._action.find('span').removeClass('bdt-crypto-currency-ticker-pause').addClass('bdt-crypto-currency-ticker-play');
                            ticker.stop();
                        } else {
                            ticker.settings.autoPlay = true;
                            ticker._action.find('span').removeClass('bdt-crypto-currency-ticker-play').addClass('bdt-crypto-currency-ticker-pause');
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

        $.fn.epNewsTicker = function (options) {

            return this.each(function () {
                if (undefined == $(this).data('epNewsTicker')) {
                    var ticker = new $.epNewsTicker(this, options);
                    $(this).data('epNewsTicker', ticker);
                }
            });

        }


        setTimeout(function () {
            $($cryptoWidget).epNewsTicker({
                "effect": 'scroll',
                "autoPlay": true,
                "interval": 5000,
                "pauseOnHover": true,
                "scrollSpeed": 1,
                // "direction"    :  false
            });
        }, 4000);



    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-ticker.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */