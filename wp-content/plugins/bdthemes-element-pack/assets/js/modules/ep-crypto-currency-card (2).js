/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-ep-crypto-currency-card'),
            $settings = $cryptoWidget.data('settings'),
            editMode = Boolean(elementorFrontend.isEditMode());

        if (!$cryptoWidget.length) {
            return;
        }

        var $options = {
            'currency': $settings.currency,
            'limit': 100,
            'order': 'market_cap_desc',
        };

        if ($settings.currency) {
            $options.currency = $settings.currency
        }
        // $options.limit = $settings.limit
        // if ($settings.limit) {
        // }
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
                    per_page: 1, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },

            }).done(function (data) {
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-card-item');
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
                    $($cryptoWidget).find('.bdt-crypto-currency-card-item').removeClass('data-changed');
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
                $($cryptoWidget).empty();

                if (typeof result.data !== "undefined" && result.data.length > 0 && true === result.apiErrors) {
                    let output = `<div class="bdt-alert-danger" bdt-alert>
                                    <a class="bdt-alert-close" bdt-close></a>
                                    <p>${result.data}</p>
                                </div>`;
                    $($cryptoWidget).append(output);
                    return;
                }


                var count = 0;
                result.data.forEach(element => {
                    count++;
                    if (count > 1) {
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
                    var hourly_price_html = '';
                    var market_cap_rank_html = '';
                    var market_cap_html = '';
                    var total_volume_html = '';
                    var price_change_html = '';
                    if (true == $settings.showCurrencyImage) {
                        img_html = `<div class="bdt-ep-currency-image">
                        <img src="${element.image}" alt="${element.id}">
                    </div>`;
                    }
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<div class="bdt-ep-currency-short-name">
                        <span>${element.symbol}</span>
                    </div>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-name-wrap"><div class="bdt-ep-currency-name">
                        <span>${element.id}</span>
                    </div> ${symble_html}</div>`;
                    }
                    if (true == $settings.showCurrencyChangePrice) {
                        hourly_price_html = `<div class="bdt-percentage" title="1 Hour Data Change">${OneHourData}</div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<div class="bdt-width-1-1 bdt-width-1-2@s"><div class="bdt-ep-current-price">
                        <div class="bdt-price">${amount}</div>
                        ${hourly_price_html}
                    </div></div>`;
                    }
                    if (true == $settings.showMarketCapRank) {
                        market_cap_rank_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">Market Cap Rank: </span>
                        <span>#${element.market_cap_rank}</span>
                    </div>`;
                    }
                    if (true == $settings.showMarketCap) {
                        market_cap_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">Market Cap: </span>
                        <span>${element.market_cap}</span>
                    </div>`;
                    }
                    if (true == $settings.showTotalVolume) {
                        total_volume_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">Total Volume: </span>
                        <span>${element.total_volume}</span>
                    </div>`;
                    }
                    if (true == $settings.showPriceChange) {
                        price_change_html = `<div class="bdt-ep-ccc-atribute">
                        <span class="bdt-ep-item-text">24H Change(%): </span>
                        <span>${element.price_change_percentage_24h}</span>
                    </div>`;
                    }

                    var output = `<div class="bdt-grid" bdt-grid data-id="${element.id}">

                                    <div class="bdt-width-1-1 bdt-width-1-2@s">
                                        <div class="bdt-ep-currency">
                                            ${img_html}
                                            ${name_html}
                                        </div>
                                    </div>
                                    
                                    ${price_html}
                                    
                                    <div class="bdt-width-1-1 bdt-margin-small-top bdt-ep-ccc-atributes bdt-grid-margin bdt-first-column">

                                        ${market_cap_rank_html}

                                        ${market_cap_html}

                                        ${total_volume_html}

                                        ${price_change_html}

                                    </div>
                                
                                <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                            </div>`;

                    $($cryptoWidget).append(output);

                });
            }
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-card.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */