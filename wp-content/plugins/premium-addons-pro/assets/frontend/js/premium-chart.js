(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumChartHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        chartElem: '.premium-chart-container',
                        chartCanvas: '.premium-chart-canvas',
                    }
                }

            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $chartElem: this.$element.find(selectors.chartElem),
                    $chartCanvas: this.$element.find(selectors.chartCanvas),
                }

            },

            bindEvents: function () {
                //Fix conflict with tabs widget.
                var _this = this,
                    $closestTab = this.elements.$chartElem.closest(".premium-tabs-content-section"),
                    closestTabID = this.elements.$chartElem.closest(".premium-tabs").attr('id'),
                    isHScrollWidget = this.elements.$chartElem.closest(".premium-hscroll-temp");

                //Don't forget to check first tab.
                if (!$closestTab.length && !isHScrollWidget.length) {
                    this.run();
                } else if (isHScrollWidget.length) {

                    var isRendered = false,
                        parentSectionWidth = isHScrollWidget.outerWidth();

                    $(window).on("scroll", function () {

                        if (!isRendered && $(window).scrollTop() >= isHScrollWidget.data("scroll-offset") - (parentSectionWidth / 2)) {
                            _this.run();
                            isRendered = true;
                        }

                    });

                } else {

                    var tabIndex = $closestTab.index();

                    //For the active tab on page load.
                    setTimeout(function () {
                        if ($closestTab.is(':visible')) {
                            _this.run();
                        }
                    }, 300);

                    $(document).on('click', "#" + closestTabID + " li[data-list-index='" + tabIndex + "']", function () {
                        //Make sure we are targeting a visible chart that was not rendered before.
                        // if (_this.elements.$chartElem.is(':visible') && !_this.elements.$chartElem.hasClass("chart-rendered")) {
                        if (!_this.elements.$chartElem.hasClass("chart-rendered")) {
                            _this.run();
                        }
                    });
                }

            },

            chartInstance: null,
            columnsData: null,
            run: function () {

                var settings = this.getElementSettings(),
                    $chartElem = this.elements.$chartElem;

                $chartElem.addClass("chart-rendered");

                this.columnsData = $chartElem.data("chart");

                var $checkModal = $chartElem.closest(".premium-modal-box-modal");

                if ($checkModal.length || "load" === settings.render_event) {

                    this.getChartData();

                } else {
                    var _this = this;
                    new Waypoint({
                        element: this.elements.$chartCanvas,
                        offset: Waypoint.viewportHeight() - 250,
                        triggerOnce: true,
                        handler: function () {
                            _this.getChartData();
                            this.destroy();
                        }
                    });
                }

            },

            getSingleOptions: function () {

                var settings = this.getElementSettings();

                return {
                    scale: {
                        ticks: {
                            beginAtZero: settings.y_axis_begin,
                            stepSize: settings.step_size,
                            suggestedMax: settings.y_axis_max,
                            callback: function (tick) {
                                var locale = settings.format_locale || false;
                                return tick.toLocaleString(locale);
                            }
                        }
                    }
                };

            },

            getMultiOptions: function () {

                var settings = this.getElementSettings(),
                    type = settings.type;

                return {
                    scales: {
                        xAxes: [{
                            barPercentage: ('bar' === type && settings.x_column_width.size) ? settings.x_column_width.size : 0.9,
                            display: ("pie" === type || "doughnut" === type) ? false : true,
                            gridLines: {
                                display: settings.x_axis_grid,
                                color: settings.x_axis_grid_color,
                                lineWidth: settings.x_axis_grid_width.size,
                                drawBorder: true
                            },
                            scaleLabel: {
                                display: settings.x_axis_label_switch,
                                labelString: settings.x_axis_label,
                                fontColor: settings.x_axis_label_color,
                                fontSize: settings.x_axis_label_size
                            },
                            ticks: {
                                fontSize: settings.x_axis_labels_size || 12,
                                fontColor: settings.x_axis_labels_color || '#54595f',
                                stepSize: settings.step_size,
                                maxRotation: settings.x_axis_label_rotation || 0,
                                minRotation: settings.x_axis_label_rotation || 0,
                                beginAtZero: settings.x_axis_begin,
                                callback: function (tick) {
                                    var locale = settings.format_locale || false;
                                    return tick.toLocaleString(locale);
                                }
                            }
                        }],
                        yAxes: [{
                            display: ("pie" === type || "doughnut" === type) ? false : true,
                            type: 'horizontalBar' !== type ? settings.data_type : 'category',
                            gridLines: {
                                display: settings.y_axis_grid,
                                color: settings.y_axis_grid_color,
                                lineWidth: settings.y_axis_grid_width.size,
                            },
                            scaleLabel: {
                                display: settings.y_axis_label_switch,
                                labelString: settings.y_axis_label,
                                fontColor: settings.y_axis_label_color,
                                fontSize: settings.y_axis_label_size
                            },
                            ticks: {
                                suggestedMin: settings.y_axis_min,
                                suggestedMax: settings.y_axis_max,
                                fontSize: settings.y_axis_labels_size || 12,
                                fontColor: settings.y_axis_labels_color || '#54595f',
                                beginAtZero: settings.y_axis_begin,
                                stepSize: settings.step_size,
                                callback: function (tick) {
                                    var locale = settings.format_locale || false;
                                    return tick.toLocaleString(locale);
                                }
                            }
                        }]
                    }
                };

            },

            getGlobalOptions: function (ctx) {

                var settings = this.getElementSettings(),
                    type = settings.type,
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    eventsArray = ["mousemove", "mouseout", "click", "touchstart", "touchmove"],
                    printVal = settings.value_on_chart,
                    event = ("pie" === type || "doughnut" === type) && printVal ? false : eventsArray;

                settings.legPos = settings.legend_position;
                if ("desktop" !== currentDevice) {
                    if (settings.legend_hide)
                        settings.legend_display = false;

                    settings.legPos = settings['legend_position_' + currentDevice];

                }

                return {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: "polarArea" === type ? 6 : 0
                        }
                    },
                    events: event,
                    animation: {
                        duration: settings.duration || 500,
                        easing: settings.start_animation,
                        onComplete: function () {
                            if (!event) {
                                this.defaultFontSize = 16;
                                ctx.font =
                                    '15px "Helvetica Neue", "Helvetica", "Arial", sans-serif';

                                ctx.fillStyle = "#000";

                                ctx.textAlign = "center";
                                ctx.textBaseline = "bottom";

                                this.data.datasets.forEach(function (dataset) {
                                    for (var i = 0; i < dataset.data.length; i++) {
                                        var model =
                                            dataset._meta[Object.keys(dataset._meta)[0]].data[i]
                                                ._model,
                                            total =
                                                dataset._meta[Object.keys(dataset._meta)[0]].total,
                                            mid_radius =
                                                model.innerRadius +
                                                (model.outerRadius - model.innerRadius) / 2,
                                            start_angle = model.startAngle,
                                            end_angle = model.endAngle,
                                            mid_angle = start_angle + (end_angle - start_angle) / 2;

                                        var x = mid_radius * Math.cos(mid_angle);
                                        var y = mid_radius * Math.sin(mid_angle);

                                        ctx.fillStyle = settings.y_axis_labels_color;

                                        var percent =
                                            String(Math.round((dataset.data[i] / total) * 100)) + "%";

                                        ctx.fillText(percent, model.x + x, model.y + y + 15);
                                    }
                                });
                            }
                        }
                    },
                    tooltips: {
                        enabled: settings.tool_tips,
                        mode: settings.tool_tips_mode,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var prefixString = "";
                                if ("pie" == type || "doughnut" == type || "polarArea" == type) {
                                    prefixString = data.labels[tooltipItem.index] + ": ";
                                }

                                var dataset = data.datasets[tooltipItem.datasetIndex];

                                var total = dataset.data.reduce(function (previousValue, currentValue) {
                                    return parseFloat(previousValue) + parseFloat(currentValue);
                                });

                                var currentValue = dataset.data[tooltipItem.index];

                                if (!settings.tool_tips_percent) {
                                    var locale = settings.format_locale || false;
                                    currentValue = parseFloat(currentValue).toLocaleString(locale);
                                }

                                var percentage = ((currentValue / total) * 100).toPrecision(3);

                                return (
                                    prefixString +
                                    (settings.tool_tips_percent ?
                                        percentage + "%" :
                                        currentValue)
                                );
                            }
                        }
                    },
                    legend: {
                        display: settings.legend_display,
                        position: settings.legPos,
                        reverse: settings.legend_reverse,
                        labels: {
                            usePointStyle: settings.legend_circle,
                            boxWidth: parseInt(settings.legend_item_width),
                            fontColor: settings.legend_text_color || '#54595f',
                            fontSize: parseInt(settings.legend_text_size)
                        }
                    }

                };

            },

            renderChart: function () {

                var widgetID = this.getID(),
                    ctx = document.getElementById('premium-chart-canvas-' + widgetID).getContext("2d"),
                    globalOptions = this.getGlobalOptions(ctx),
                    settings = this.getElementSettings(),
                    columnsData = this.columnsData,
                    xLabels = settings.x_axis_labels || '',
                    type = settings.type,
                    data = {
                        labels: 'custom' === settings.data_source ? xLabels.split(",") : [],
                        datasets: []
                    };

                this.chartInstance = new Chart(ctx, {
                    type: type,
                    data: data,
                    options: Object.assign(globalOptions, ("radar" !== type && "polarArea" !== type) ? this.getMultiOptions() : this.getSingleOptions())
                });

                if ('custom' === settings.data_source) {
                    var _this = this;
                    columnsData.forEach(function (element) {

                        if ("pie" !== type && "doughnut" !== type && "polarArea" !== type) {
                            if ("object" === typeof element.backgroundColor) {

                                //We need to make sure add gradient colors or not.
                                if ("empty" !== element.backgroundColor[element.backgroundColor.length - 1]) {
                                    var gradient = ctx.createLinearGradient(0, 0, 0, 600),
                                        secondColor = element.backgroundColor[1] ?
                                            element.backgroundColor[1] :
                                            element.backgroundColor[0];

                                    gradient.addColorStop(0, element.backgroundColor[0]);
                                    gradient.addColorStop(1, secondColor);
                                    element.backgroundColor = gradient;
                                    element.hoverBackgroundColor = gradient;
                                }

                            }
                        }

                        data.datasets.push(element);

                        _this.chartInstance.update();
                    });

                    $('#premium-chart-canvas-' + widgetID).on("click", function (evt) {
                        var activePoint = _this.chartInstance.getElementAtEvent(evt);
                        if (activePoint[0]) {
                            var URL =
                                _this.chartInstance.data.datasets[activePoint[0]._datasetIndex].links[
                                activePoint[0]._index
                                ];
                            if (URL != null && URL != "") {
                                window.open(URL, settings.y_axis_urls_target ? '_blank' : '_top');
                            }
                        }
                    });

                }

            },

            getChartData: function () {

                var dataSource = this.getElementSettings('data_source'),
                    columnsData = this.columnsData,
                    _this = this;

                if ('custom' === dataSource) {
                    this.renderChart();
                } else {

                    var $chartElem = this.elements.$chartElem;

                    $chartElem.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                    if (columnsData.url) {
                        $.ajax({
                            url: columnsData.url,
                            type: "GET",
                            success: function (res) {
                                $chartElem.find(".premium-loading-feed").remove();
                                _this.renderCSVChart(res);
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }

                }

            },

            renderCSVChart: function (res) {

                var widgetID = this.getID(),
                    ctx = document.getElementById('premium-chart-canvas-' + widgetID).getContext("2d"),
                    _this = this,
                    rowsData = res.split(/\r?\n|\r/),
                    columnsData = this.columnsData,
                    labels = (rowsData.shift()).split(columnsData.separator),
                    globalOptions = this.getGlobalOptions(ctx),
                    settings = this.getElementSettings(),
                    type = settings.type,
                    data = {
                        labels: labels,
                        datasets: []
                    };


                this.chartInstance = new Chart(ctx, {
                    type: type,
                    data: data,
                    options: Object.assign(globalOptions, ("radar" !== type && "polarArea" !== type) ? this.getMultiOptions() : this.getSingleOptions())
                });

                rowsData.forEach(function (row, index) {
                    if (row.length !== 0) {
                        var colData = {};

                        colData.data = row.split(columnsData.separator);
                        //add properties only if repeater element exists
                        if (columnsData.props[index]) {
                            colData.borderColor = columnsData.props[index].borderColor;
                            colData.borderWidth = columnsData.props[index].borderWidth;
                            colData.backgroundColor = columnsData.props[index].backgroundColor;
                            colData.hoverBackgroundColor = columnsData.props[index].hoverBackgroundColor;
                            colData.label = columnsData.props[index].title;
                        }

                        data.datasets.push(colData);
                        _this.chartInstance.update();

                    }
                });

            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-chart', PremiumChartHandler);
    });
})(jQuery);