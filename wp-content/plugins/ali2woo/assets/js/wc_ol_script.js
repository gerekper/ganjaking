var a2w_reload_page_after_ajax = false;
jQuery(function ($) {
    var get_id_from_link_anchor = function(el) {
        var jq_el = $(el);

        if (typeof jq_el.attr('id') == "undefined" && jq_el.attr('href').substr(0, 1) == "#") {
            var id = jq_el.attr('href').substr(1);
            //var id = jq_el.attr('href').substr(1).split('-');
        } else var id = jq_el.attr('id').split('-')[1];

        return id;
    }

    $(document).on("click", ".a2w-order-info", function() {

        var id = get_id_from_link_anchor(this);

        $.a2w_show_order(id);
        return false;
    });

    $("#doaction, #doaction2").click(function(event) {
        const check_action = ($(this).attr('id') === 'doaction') ?
            $('#bulk-action-selector-top').val() :
            $('#bulk-action-selector-bottom').val();

        if ('a2w_order_sync_bulk' === check_action) {
            event.preventDefault();
            const ids = [];
            $('input:checkbox[name="post[]"]:checked').each(function () {
                ids.push($(this).val());
            });
            if (ids.length < 1) {
                //for HPOS screen
                $('input:checkbox[name="id[]"]:checked').each(function () {
                    ids.push($(this).val());
                });
            }

            a2w_show_block(a2w_script_data.lang.order_sync);
            a2w_show_tip(a2w_script_data.lang.please_wait);

            const orders_data = ids.map(
                (order_id) => ({'action': 'a2w_sync_order_info', order_id})
            );

            const on_order_sync = (s) => {
                if (state.cnt + state.error_cnt === state.total) {
                    a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.complete_result_sync_d_erros_d, state.cnt, state.error_cnt), false, false, false, true);
                } else {
                    a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.process_sync_d_of_d_erros_d, state.cnt, state.total, state.error_cnt), false, false, false, true);
                }
                $('.hover_a2w_fulfillment .close').off('click').click(function () {
                    a2w_hide_block();
                });
            }

            var state = {total: orders_data.length, cnt: 0, error_cnt: 0};
            a2w_js_sync_order(orders_data, state, on_order_sync);
        }
    });

    $(document).on("click", ".a2w-aliexpress-sync", function(e) {
        e.preventDefault();

        a2w_show_block(a2w_script_data.lang.order_sync);
        a2w_show_tip(a2w_script_data.lang.please_wait);

        var item_sync_btn = $(this);
        item_sync_btn.prop("disabled", true);

        var ext_id = get_id_from_link_anchor(this);

        var item_info_btn = $(this).siblings('.a2w-order-info')[0],
            id = get_id_from_link_anchor( item_info_btn );
        
        const orders_data = [ { 'action': 'a2w_sync_order_info', order_id: id } ];

        const on_order_sync = (s) => {
            if(state.cnt + state.error_cnt === state.total) {
                a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.complete_result_sync_d_erros_d, state.cnt, state.error_cnt), false, false, false, true);
            } else {
                a2w_show_tip(a2w_sprintf(a2w_ali_orderfulfill_js.lang.process_sync_d_of_d_erros_d, state.cnt, state.total, state.error_cnt), false, false, false, true);
            }
            $('.hover_a2w_fulfillment .close').off('click').click(function() {
                a2w_hide_block();
            });
        }
        
        var state = { total: orders_data.length, cnt: 0, error_cnt: 0 };
            a2w_js_sync_order(orders_data, state, on_order_sync);
    });

    $.a2w_show_order = function(id) {
        $('<div id="a2w-dialog' + id + '"></div>').dialog({
            dialogClass: 'a2w-dialog',
            modal: true,
            width: "400px",
            title: a2w_script_data.lang.aliexpress_info + ": " + id,
            open: function() {
                $('#a2w-dialog' + id).html(a2w_script_data.lang.please_wait_data_loads);
                var data = { 'action': 'a2w_order_info', 'id': id };

                $.post(ajaxurl, data, function(response) {

                    var json = jQuery.parseJSON(response);


                    if (json.state === 'error') {

                        console.log(json);

                    } else {
                        $('#a2w-dialog' + json.data.id).html(json.data.content.join('<br/>'));
                    }

                });


            },
            closeText: "",
            close: function(event, ui) {
                $("#a2w-dialog" + id).remove();
            },
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });

        return false;

    };

    var sync_btn = $('#a2w_bulk_order_sync_manual');

    sync_btn.on('click', function() {
        sync_btn.prop("disabled", true);
        sync_btn.val(a2w_ali_orderfulfill_js.lang.please_wait_data_loads);

        a2w_get_fulfilled_orders((data) => {            
            const groupedOrders = data.reduce((acc,d) => {
                if(!acc[d.order_id]) acc[d.order_id] = [];
                acc[d.order_id].push(d.order_id)
                return acc;
            }, {});
            const orders_data = Object.keys(groupedOrders)
                .map((order_id)=>({ 'action': 'a2w_sync_order_info', order_id }));

            const on_order_sync = (s) => {
                if(state.cnt + state.error_cnt === state.total) {
                    sync_btn.val(a2w_sprintf(a2w_ali_orderfulfill_js.lang.complete_result_sync_d_erros_d, state.cnt, state.error_cnt));
                } else {
                    sync_btn.val(a2w_sprintf(a2w_ali_orderfulfill_js.lang.process_sync_d_of_d_erros_d, state.cnt, state.total, state.error_cnt));
                }
            }

            var state = { total: orders_data.length, cnt: 0, error_cnt: 0 };
            a2w_js_sync_order(orders_data, state, on_order_sync);
        })
    });

    function a2w_js_sync_order(orders_to_sync, state, on_load_calback) {
        if (orders_to_sync.length > 0) {
            var data = orders_to_sync.shift();

            jQuery.post(ajaxurl, data).done(function (response) {
                var json = jQuery.parseJSON(response);
                if (json.state !== 'ok') {
                    console.log(json);
                }
                if (json.state === 'error') {
                    state.error_cnt++;

                } else {
                    state.cnt++;
                }

                if (on_load_calback) {
                    on_load_calback(state);
                }

                a2w_js_sync_order(orders_to_sync, state, on_load_calback);
            }).fail(function (xhr, status, error) {
                console.log(error);
                state.error_cnt++;

                if (on_load_calback) {
                    on_load_calback(state);
                }

                a2w_js_sync_order(orders_to_sync, state, on_load_calback);
            });
        }
    }

    var a2w_get_next_tracking_code = function(data, i, status_code, callback_func) {
        a2w_get_order_tracking_code(data[i].ext_order_id, function(response) {

            // do not continue if status code is 403
            // if ((response.status_code == 200 || response.status_code == 404) && i < data.length)   {

            var ext_order_id = data[i].ext_order_id; //compatibility with old chrome extension

            if (typeof response.ext_order_id !== 'undefined') {
                ext_order_id = response.ext_order_id;
            }

            //fixed the chrome extnesion bug sending the html together with codes
            var normalized_codes = [];
            for (var code in response.tracking_codes) {
                result = response.tracking_codes[code].match(/>?([A-Z,0-9]+)<?/gm);
                normalized_codes.push(result[0]);
            }

            callback_func(i, ext_order_id, normalized_codes, response.carrier_name, response.carrier_url, response.tracking_status, response.status_code);

            if (i + 1 < data.length) {
                return a2w_get_next_tracking_code(data, i + 1, response.status_code, callback_func);
            }
        })

        return true;
    };

    var a2w_get_fulfilled_orders = function(callback_func) {
        var data = { 'action': 'a2w_get_fulfilled_orders' };

        jQuery.post(ajaxurl, data).done(function(response) {
            var json = jQuery.parseJSON(response);

            if (json.state !== 'ok') {
                console.log(json);
            }

            if (json.state === 'error') {
                //do smth
            } else {

                callback_func(json.data);

            }

        }).fail(function(xhr, status, error) {});
    }

    var a2w_save_tracking_code = function(id, ext_id, tracking_codes, carrier_name, carrier_url, tracking_status, func) {
        var data = { 'action': 'a2w_save_tracking_code', 'id': id, 'ext_id': ext_id, 'tracking_codes': tracking_codes, 'carrier_name': carrier_name, 'carrier_url': carrier_url, 'tracking_status': tracking_status };
        jQuery.post(ajaxurl, data).done(function(response) {
            var json = jQuery.parseJSON(response);
            func(json);

        }).fail(function(xhr, status, error) {
            func(null);
        });
    }
});
