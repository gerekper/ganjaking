jQuery(function ($) {


    $('#the-list').on('click', '.do_wc_recommender_build_recommendation', function () {

        $('#blockMessage').show();

        var id = $(this).attr('rel');

        var $row = $('#record_' + id);
        var $cell = $('.recommendations-cell', $row);

        $('#blockMessage span').html('Loading Potential Recommendations');
        $cell.block({
            message: $('#blockMessage'),
            overlayCSS: {
                background: '#F6F6BC',
                opacity: 0.6
            }
        });

        var data = {
            action: 'wc_recommender_begin_build_recommendation',
            post_id: id,
            security: wc_recommender_params.build_recommendations_security
        };


        var products_to_process;
        var lastResponse;

        $.post(wc_recommender_params.ajax_url, data, function (response) {

            products_to_process = response.data.products;

            if (products_to_process.length) {
                process_product_recommendations(0);
            } else {
                $('#blockMessage span').html('Loading Potential Recommendations');
                $cell.unblock(
                    {
                        'onUnblock': function () {

                        }
                    }
                );
            }
        });

        function process_product_recommendations(index) {

            if (products_to_process && index < products_to_process.length) {

                var current = index + 1;
                $('#blockMessage span').html('Processing ' + current + ' of ' + products_to_process.length + ' Potential Recommendations');

                var data = {
                    action: 'wc_recommender_build_recommendation',
                    post_id: id,
                    related_product_id: products_to_process[index],
                    security: wc_recommender_params.build_recommendations_security
                };

                $.post(wc_recommender_params.ajax_url, data, function (response) {

                    $('.viewed_similar', $row).find('.value').eq(0).text(response.data.viewed_similar);
                    $('.ordered_similar', $row).find('.value').eq(0).text(response.data.ordered_similar);
                    $('.purchased_together', $row).find('.value').eq(0).text(response.data.purchased_together);

                    lastResponse = response;
                    index++;
                    process_product_recommendations(index);
                });

            } else if (lastResponse) {
                $('.viewed_similar', $row).find('.value').eq(0).text(lastResponse.data.viewed_similar);
                $('.ordered_similar', $row).find('.value').eq(0).text(lastResponse.data.ordered_similar);
                $('.purchased_together', $row).find('.value').eq(0).text(lastResponse.data.purchased_together);

                $cell.unblock(
                    {
                        'onUnblock': function () {

                        }
                    }
                );
            } else {
                $('#blockMessage').hide();
                $cell.unblock(
                    {
                        'onUnblock': function () {

                        }
                    }
                );
            }

        }

        $('#bulkActionCancel').on('click', function (e) {
            e.preventDefault();
            products_to_process = null;
        });


    });


});


(function ($) {

    var cancel = false;
    var start_count = wc_recommender_params.start_count;

    function rebuild(start, count) {

        var data = {
            action: 'wc_recommender_rebuild_recommendations',
            rebuild_type: $('#rebuild-recommendations-type').val(),
            start: start,
            count: count,
        };

        $.post(wc_recommender_params.ajax_url, data, function (response) {

            if (!response.data.done) {
                update_ui(response.data);
                rebuild(response.data.start, response.data.count);
            } else {
                reset_ui();
            }

        });

    }

    function update_ui(data) {


        $('#total').text(data.total);
        $('#next_start').text(data.start);
        $('#through').text(parseInt(data.start) + parseInt(data.countremaining));
        $('#remaining').text(data.timeremaining);
    }

    function reset_ui(data) {
        $('#wc-recommender-status').hide();
        $('#wc-recommender-complete').show();
        $('#wc-recommender-start').show();


        $('#total').text('...');
        $('#next_start').text('0');
        $('#through').text(start_count);
        $('#remaining').text('...');
    }

    $(document).ready(function () {

        $('#rebuild-recommendations').click(function () {

            $('#wc-recommender-status').show();
            $('#wc-recommender-complete').hide();
            $('#wc-recommender-start').hide();

            update_ui({
                total: '...',
                start: 0,
                count: wc_recommender_params.start_count,
                countremaining: wc_recommender_params.start_count,
                timeremaining: '...'
            });

            var data = {
                action: 'wc_recommender_rebuild_recommendations',
                rebuild_type: $('#rebuild-recommendations-type').val(),
                start: 0,
                count: start_count,
                security: wc_recommender_params.build_recommendations_security
            };

            $.post(wc_recommender_params.ajax_url, data, function (response) {

                if (!response.data.done) {
                    update_ui(response.data);
                    rebuild(response.data.start, response.data.count);
                } else {
                    reset_ui();
                }

            });
        });
    });

})(jQuery);


(function ($) {

    var cancel = false;


    function reset_ui(data) {
        $('#wc-recommender-status').hide();
        $('#wc-recommender-complete').show();
        $('#wc-recommender-start').show();
    }

    $(document).ready(function () {

        $('#install-stats').click(function () {

            $('#wc-recommender-status').show();
            $('#wc-recommender-complete').hide();
            $('#wc-recommender-start').hide();

            var data = {
                action: 'wc_recommender_install_stats',
                security: wc_recommender_params.build_recommendations_security
            };

            $.post(wc_recommender_params.ajax_url, data, function (response) {
                if (!response.data.done) {
                    alert('Something went wrong');
                } else {
                    reset_ui();
                }

            });
        });
    });

})(jQuery);


(function ($) {

    $(document).ready(function () {


        $('#wc-recommender-cron-jobs').on('click', 'a.do_execute_cron_job', function () {
            var $cell = $(this).parent();

            $cell.block({
                message: null,
                overlayCSS: {
                    background: '#F6F6BC',
                    opacity: 0.6
                }
            });


            var data = {
                action: 'wc_recommender_execute_cron_job',
                security: wc_recommender_params.execute_cron_job_security
            };

            $.post(wc_recommender_params.ajax_url, data, function (response) {


                console.log(response.data.status);

                $cell.unblock();
            });
        });

    });

})(jQuery);
