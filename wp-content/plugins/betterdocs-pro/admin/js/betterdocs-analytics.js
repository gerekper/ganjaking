(function ($) {
    'use strict';

    $(document).ready(function () {
        const startDate = $('#betterdocs_start_date');
        const betterdocs = $('#betterdocs_docs');
        const endDate = $('#betterdocs_end_date');
        const comparisonFactor = $('#betterdocs_comparison_factor');
        const currentDateNow = Date.now();
        const query_vars = get_query_vars();

        if (betterdocs.length > 0) {
            betterdocs.select2();
        }
        if (comparisonFactor.length > 0) {
            comparisonFactor.select2();
        }

        if (startDate.length > 0) {
            startDate.datepicker({
                dateFormat: 'dd-mm-yy',
            });
            if (Object.keys(query_vars).indexOf('start_date') >= 0) {
                startDate.datepicker('setDate', query_vars['start_date']);
            } else {
                startDate.datepicker('setDate', new Date((currentDateNow - 604800000)));
            }
        }
        if (endDate.length > 0) {
            endDate.datepicker({
                dateFormat: 'dd-mm-yy',
            });
            if (Object.keys(query_vars).indexOf('end_date') >= 0) {
                endDate.datepicker('setDate', query_vars['end_date']);
            } else {
                endDate.datepicker('setDate', currentDateNow);
            }
        }

        const analyticsForm = $('#betterdocs-analytics-form');

        renderChart();
        
        analyticsForm.submit(function (e) {
            e.preventDefault();
            var betterdocs = $('#betterdocs_docs').val(),
                nxStartDate = $('#betterdocs_start_date').val(),
                nxEndDate = $('#betterdocs_end_date').val(),
                nxComparisonFactor = $('#betterdocs_comparison_factor').val();

            if ((nxStartDate == '' && nxEndDate != '') || (nxStartDate != '' && nxEndDate == '') || (nxStartDate == '' && nxEndDate == '')) {
                alert("Please select both start and end date");
                return false;
            }
            
            var params = '?page=betterdocs-analytics';

            if (nxStartDate !== '' && nxEndDate !== '') {
                params += '&start_date=' + nxStartDate + '&end_date=' + nxEndDate;
            }
            if (nxComparisonFactor) {
                params += '&comparison_factor=' + nxComparisonFactor;
            }

            if (betterdocs != null) {
                params += '&betterdocs=' + betterdocs;
            }
            
            window.history.pushState('/admin.php?page=betterdocs-analytics', 'Connects', params);

            renderChart();
        });
    });

    function renderChart() {
        var query_vars = get_query_vars();

        var comparison_factor = decodeURIComponent(query_vars['comparison_factor']);
        var betterdocs = decodeURIComponent(query_vars['betterdocs']);
        delete query_vars['comparison_factor'];
        delete query_vars['betterdocs'];
        if (comparison_factor != 'undefined') {
            query_vars['comparison_factor'] = comparison_factor;
        }
        if (betterdocs != 'undefined') {
            query_vars['betterdocs'] = betterdocs;
        }

        var nonce = $('#betterdocs-analytics-form #_wpnonce').val();
        var referer = $('#betterdocs-analytics-form #_wpnonce + input').val();

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'betterdocs_analytics_calc',
                query_vars: query_vars,
                nonce: nonce,
                referer: referer,
            },
            success: function (response) {
                response = JSON.parse(response);
                chart(response);
            },
        });
    }

    function chart(data) {
        var stepped_size = data.datasets.stepped_size;
        delete data.datasets.stepped_size;
        var config = {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: Object.values(data.datasets),
            },
            options: {
                maintainAspectRatio: !1,
                scaleShowHorizontalLines: !0,
                scaleShowVerticalLines: !1,
                bezierCurveTension: .3,
                responsive: true,
                spanGaps: false,
                tooltips: {
                    mode: 'nearest',
                    position: 'nearest',
                    intersect: false,
                },
                hover: {
                    position: 'nearest',
                    intersect: false
                },
                scales: {
                    xAxes: [{
                        display: true,
                    }],
                    yAxes: [{
                        display: true,
                        offsetGridLines: true,
                        ticks: {
                            stepSize: parseInt(Math.ceil(stepped_size) / 5),
                        },
                    }]
                }
            }
        };

        var ctx = document.getElementById('betterdocs_canvas').getContext('2d');

        if (window.bDocsChart !== undefined) {
            window.bDocsChart.data.labels = data.labels;
            window.bDocsChart.data.datasets = Object.values(data.datasets);
            window.bDocsChart.config.options.scales.yAxes[0].ticks.stepSize = parseInt(Math.ceil(stepped_size) / 5);
            window.bDocsChart.update();
            return;
        }

        window.bDocsChart = new Chart(ctx, config);
    }

    function get_query_vars(name = '') {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
            vars[key] = value;
        });

        if (name != '') {
            return vars[name];
        }

        return vars;
    }

})(jQuery);