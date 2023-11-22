/**
 * Start accordion widget script
 */

;
(function ($, elementor) {
    'use strict';
    var widgetCrypto = function ($scope, $) {
        var $cryptoWidget = $scope.find('.bdt-crypto-currency-table'),
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

        let table, currency_selected, cryptoDataSettingsValue = $options;

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
                var tableData = table.rows().data();
                /**
                 * @idPriceColumnArray is holding data from current datatable
                 */
                let idPriceColumnArray = [];
                for (let i = 0; i < tableData.length; i++) {
                    idPriceColumnArray.push({
                        id: tableData[i]["id"],
                        current_price: tableData[i]["current_price"],
                    });
                }
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

                changesIdArray.filter(function (valueChangesArray, index) {
                    let foundindex = 0;
                    var filteredData = table.column(1)
                        .data()
                        .filter(function (value, index) {
                            if (value === valueChangesArray.id) {
                                foundindex = index;
                                table.column(2)
                                    .nodes()
                                    .each(function (node, colIndex, dt) {
                                        if (colIndex === index) {
                                            table.cell(node)
                                                .data(valueChangesArray.current_price);
                                            let nodes = table.column(2).nodes();
                                            $(nodes[index]).addClass("focus-item");
                                        }
                                    });
                            }
                            return value === valueChangesArray.id ? true : false;
                        });
                });
                setTimeout(function () {
                    table.column(2)
                        .nodes()
                        .each(function () {
                            $(this).removeClass("focus-item");
                        });
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
        table = $($settings.tableId).DataTable({
            language : window.ElementPackConfig.data_table.language, // default language override for proper translation
            destroy: true, // fixed the Alert issue in Edit mode
            processing: true,
            serverSide: false,
            searching: $settings.searching,
            ordering: $settings.ordering,
            paging: $settings.paging,
            info: $settings.info,
            pageLength: $settings.pageLength,
            ajax: {
                type: "GET",
                dataType: "json",
                url: ElementPackConfig.ajaxurl + '?action=ep_crypto',
                data: {
                    currency: currency_selected,
                    per_page: cryptoDataSettingsValue.limit, //limit
                    order: cryptoDataSettingsValue.order,
                    ids: $settings.ids
                },
            },
            columns: [{
                    data: "market_cap_rank"
                },
                {
                    data: "id",
                    render: function (data, type, row, meta) {
                        return (
                            '<div class="bdt-coin"><div class="bdt-coin-image"><img src="' +
                            row["image"] +
                            '" alt="' +
                            row["id"] +
                            '"></div><div class="bdt-coin-title"><div class="bdt-coin-name">' +
                            row["id"] +
                            '</div><div class="bdt-coin-symbol">' +
                            row["symbol"] +
                            "</div></div></div>"
                        );
                    },
                },
                {
                    data: "current_price",
                    render: function (data, type, row, meta) {
                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let val = Number(data) === data && data % 1 !== 0 ? data.toFixed(2) : data; // this is just we are checking if this value is float. then we are just adjusting the two decimal point. otherwise returing the original value
                        //return returnCurrencySymbol(upperCaseCurrncyCode) + data;
                        return returnCurrencySymbol(upperCaseCurrncyCode) + val;
                    },
                },
                {
                    data: "price_change_percentage_24h",
                    render: function (data, type, row, meta) {
                        return Number(data) === data && data % 1 !== 0 ? data.toFixed(2) + "%" : data + "%";
                    },
                },
                {
                    data: "market_cap",
                    render: function (data, type, row, meta) {
                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let formatAmout = numFormatter(data);
                        //return returnCurrencySymbol(upperCaseCurrncyCode) + data;
                        return returnCurrencySymbol(upperCaseCurrncyCode) + formatAmout;
                    },
                },
                {
                    data: "total_volume",
                    render: function (data, type, row, meta) {
                        let upperCaseCurrncyCode = currency_selected.toString().toUpperCase();
                        let formatAmout = numFormatter(data);
                        //return returnCurrencySymbol(upperCaseCurrncyCode) + data;
                        return returnCurrencySymbol(upperCaseCurrncyCode) + formatAmout;
                    },
                },
                {
                    data: "circulating_supply",
                    render: function (data, type, row, mata) {
                        let formatAmout = numFormatter(data);
                        //return data.toFixed(2);
                        return formatAmout;
                    }
                },
                {
                    data: "last_seven_days_changes",
                    render: function (data, type, row, meta) {
                        return (
                            '<input type="hidden" class="hdnInputCanvas-' + row["id"] + '"  value="' + data + '"/>' + '<div class="chart-container" style="position: relative; height: 100%; width: 250px"><canvas id="canvas-' + row["id"] + '"></canvas></div>'
                        );
                    },
                },
            ],
            columnDefs: [{
                searchable: false,
                orderable: false,
                //targets: [0, 8],
                targets: [7],
            }, ],
            //order: [[1, "asc"]],
            order: [
                [0, "asc"]
            ],
            createdRow: function (row, data, index) {
                let getCanvasElement = $("td", row).eq(7);
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


                // gradient color chart
                

                const dataCharts = {
                    labels: labels,
                    datasets: [{
                        label: "",
                        backgroundColor: "rgba(30,135,240,0.2)",
                        borderColor: "#1e87f0",
                        fill: true,
                        lineTension: 0.4,
                        pointStyle: 'circle',
                        pointBackgroundColor: "transparent",
                        pointBorderWidth: 0,
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
                                enabled: true,
                                callbacks: {
                                    title: () => ''
                                }
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
                chart.canvas.parentNode.style.width = "100%";
                chart.canvas.parentNode.style.height = "60px";
                chart.canvas.style.width = "100%";
                chart.canvas.style.height = "60px";
            },
        });

        if (true !== editMode) {
            setTimeout(function () {
                getData();
            }, 5000);
        }

    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/bdt-crypto-currency-table.default', widgetCrypto);
    });

}(jQuery, window.elementorFrontend));

/**
 * End accordion widget script
 */