jQuery(document).ready(function ($) {

    var current_index = 0;
    $(document).on('click', '#yith_survey_convert', function (e) {

        e.preventDefault();
        var data = {

                action: survey_conversion_args.actions.convert_surveys,
                security: $('#yith_survey_conversion_nonce').val()

            },
            button = $(this);




        var order_ids = JSON.parse(survey_conversion_args.order_ids),
            order_total = parseInt( order_ids.length ),
            progressbar = $('#yith_survey_convert_progressbar'),
            progressLabel = progressbar.find('.yith_survey_progessbar_label'),
            tr = progressbar.parents('tr'),
            no_error = true;

        if (order_total > 0) {
            button.attr('disabled', 'disabled');
            tr.show();
            progressbar.progressbar();

            progressbar.progressbar("value", 0);
            progressLabel.html( 0+'/'+order_total );
            function import_votes(index) {

                if( index < order_total ) {
                    data.order_id = order_ids[index];
                    $.ajax({
                        type: 'POST',
                        url: survey_conversion_args.ajax_url,
                        data: data,
                        success: function (response) {

                            if (typeof response.result === 'undefined' || 'error' === response.result) {
                                no_error = false;
                            }
                            progressbar.progressbar("value", (( index+1)/order_total )* 100);
                            progressLabel.html( (index+1)+'/'+order_total );

                            import_votes(index + 1);

                        }
                    });
                }else{
                    window.location.href = window.location + '&import_completed=1';

                }
            }

            import_votes(0);

        }else{
            window.location.href = window.location + '&import_completed=1';
        }

    });

});