(function ($, window, document, undefined) {
    $(function () {


        $('.a2w-content .a2w-convert-products').click(function () {
            const file = $('.a2w-content .convert-file').val()
            const products_to_convert = [];
            $.each(a2w_convert_product_ids, function (i, id) {
                products_to_convert.push({ 'action': 'a2w_convert_sm_product', id, file });
            });

            const on_load = function (response_state, response_message, state) {
                if ((state.ok + state.error) === state.total) {
                    $(".a2w-content .convert-log").html(a2w_sprintf(a2w_admin_tools_data.complete_result_updated_d_erros_d, state.ok, state.error));
                } else {
                    $(".a2w-content .convert-log").html(a2w_sprintf(a2w_admin_tools_data.process_update_d_of_d_erros_d, state.ok, state.total, state.error));
                }
            };

            const state = { total: products_to_convert.length, ok: 0, error: 0 };
            $(".a2w-content .convert-log").html(a2w_sprintf(a2w_admin_tools_data.process_update_d_of_d, 0, state.total));
            a2w_js_post_to_convert(products_to_convert, state, on_load);
        });


        function a2w_js_post_to_convert(products_to_convert, state, on_load_calback) {
            if (products_to_convert.length > 0) {
                var data = products_to_convert.shift();

                jQuery.post(ajaxurl, data).done(function (response) {
                    var json = jQuery.parseJSON(response);
                    if (json.state !== 'ok') {
                        console.log(json);
                    }

                    if (json.state === 'error') {
                        state.error++;
                    } else {
                        state.ok++;
                    }

                    if (on_load_calback) {
                        on_load_calback(json.state, json.message, state);
                    }

                    a2w_js_post_to_convert(products_to_convert, state, on_load_calback);
                }).fail(function (xhr, status, error) {
                    console.log(error);
                    state.error++;

                    if (on_load_calback) {
                        on_load_calback('error', 'request error', state);
                    }

                    a2w_js_post_to_convert(products_to_convert, state, on_load_calback);
                });

            }
        }


    });

})(jQuery, window, document);


