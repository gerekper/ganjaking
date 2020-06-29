/**
 * Created by Your Inspiration on 29/04/2015.
 */
jQuery(document).ready(function ($) {

    /**IMPORT , SEE TAB TOOLS**/

    $('#ywson_import_order_numbers').on('click', function (e) {
        e.preventDefault();

        var data = {

                action: yith_son_params.actions.import_old_order_number,
                security: $('#ywson_nonce').val()

            },
            button = $(this),
            block_params = {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                },
                ignoreIfBlocked: true
            };

        $('.ywson_imported').remove();
        button.attr('disabled', 'disabled');

        $.ajax({
            type: 'POST',
            url: yith_son_params.ajax_url,
            data: data,
            success: function (response) {

                button.attr('disabled', false);
                button.after('<span class="ywson_imported">'+response.message+'</span>');
            }
        });


    });

});

