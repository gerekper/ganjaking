/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-ticker'),
            $settings = $cryptoWidget.data('settings'),
            $tickerSettings = $cryptoWidget.data('ticker-settings'),
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
            }, 7000);
        }

        $(document).ready(function () {
            setTimeout(function () {
                $($cryptoWidget).epNewsTicker($tickerSettings);
            }, 7000);
        });

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-ticker.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */