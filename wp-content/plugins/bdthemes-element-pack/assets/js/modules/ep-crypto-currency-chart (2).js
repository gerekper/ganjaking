/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-chart'),
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
                let itemData = $($cryptoWidget).find('.bdt-crypto-currency-chart-item');
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
                    $($cryptoWidget).find('.bdt-crypto-currency-chart-item').removeClass('data-changed');
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

                    var name_html = '';
                    var symble_html = '';
                    var price_html = '';
                    var price_change_percentage_1h_html = '';
                    if (true == $settings.showCurrencyShortName) {
                        symble_html = `<span>(${element.symbol})</span>`;
                    }
                    if (true == $settings.showCurrencyName) {
                        name_html = `<div class="bdt-crypto-currency-chart-title"><h4>${element.id} ${symble_html}</h4></div>`;
                    }
                    if (true == $settings.showCurrencyCurrentPrice) {
                        price_html = `<div class="bdt-crypto-currency-chart-price-l">
                        <span class="price-int">${amount}</span>
                    </div>`;
                    }
                    if (true == $settings.showPriceChangePercentage) {
                        price_change_percentage_1h_html = `<div class="bdt-crypto-currency-chart-change">
                        <span class="bdt-crypto-currency-chart-list-change up" title="1 Hour Data Change">${OneHourData}</span>
                    </div>`;
                    }

                    var output = `<div class="bdt-crypto-currency-chart-item" data-id="${element.id}">
                                    <div class="bdt-crypto-currency-chart-head-content">
                                        <div class="bdt-crypto-currency-chart-head-inner-content">
                                            ${name_html}
                                            ${price_change_percentage_1h_html}
                                            
                                        </div>
                                        <div class="bdt-crypto-currency-chart-bottom-inner-content">
                                            ${price_html}
                                        </div>
                                    </div>
                                    <div class="bdt-crypto-currency-chart-chart">
                                        <input type="hidden" class="hdnInputCanvas-${element.id}"  value="${element.last_seven_days_changes}"/><div class="chart-container" style="position: relative;"><canvas id="canvas-${element.id}"></canvas></div>
                                    </div>
                                    <span class="bdt-price-text bdt-hidden">${element.current_price}</span>
                                </div>`;

                    $($cryptoWidget).append(output);

                    let getCanvasElement = $($cryptoWidget).find('[data-id="' + element.id + '"]');
                    //let canvas_id = $(getCanvasElement).find("canvas").attr("id");
                    let getHiddenData = $(getCanvasElement).find("input").val();
                    let splitData = getHiddenData.split(",");
                    /***
                     * here we are just getting last 20 values value
                     */
                    if (splitData && splitData.length > 15) {
                        splitData = splitData.slice(0, 14);
                    }
                    /**
                     * end of splice code. this we can remove if any further code found
                     */
                    const dom_canvas_element = $(getCanvasElement).find("canvas");
                    const labels = [],
                        dataPointvalue = [];
                    splitData.forEach((element, index) => {
                        labels.push(index);
                        dataPointvalue.push(Number(element));
                    });

                    const dataCharts = {
                        labels: labels,
                        datasets: [{
                            label: "",
                            // backgroundColor: $settings.backgroundColor || "#777",
                            // borderColor: $settings.borderColor || "#777",
                            backgroundColor: "rgba(30,135,240,0.2)",
                            borderColor: "#1e87f0",
                            fill: true,
                            lineTension: 0.4,
                            pointStyle: 'circle',
                            pointBackgroundColor: "#1e87f0",
                            pointBorderWidth: 1,
                            borderWidth: 2,
                            data: dataPointvalue,
                        }, ],
                    };
                    const config = {
                        type: "line",
                        data: dataCharts,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        display: false,
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                },
                                y: {
                                    ticks: {
                                        display: false,
                                    },
                                    grid: {
                                        display: false,
                                        drawBorder: false,
                                        drawOnChartArea: false,
                                        drawTicks: false,
                                    },
                                },
                            },
                        },
                    };
                    const chart = new Chart(dom_canvas_element, config);
                    // chart.canvas.parentNode.style.width = "100%";
                    // chart.canvas.parentNode.style.height = "80px";

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
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-chart.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */