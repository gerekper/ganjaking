// Create Base64 Object
var Base64 = { _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) { var t = ""; var n, r, i, s, o, u, a; var f = 0; e = Base64._utf8_encode(e); while (f < e.length) { n = e.charCodeAt(f++); r = e.charCodeAt(f++); i = e.charCodeAt(f++); s = n >> 2; o = (n & 3) << 4 | r >> 4; u = (r & 15) << 2 | i >> 6; a = i & 63; if (isNaN(r)) { u = a = 64 } else if (isNaN(i)) { a = 64 } t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a) } return t }, decode: function (e) { var t = ""; var n, r, i; var s, o, u, a; var f = 0; e = e.replace(/[^A-Za-z0-9\+\/\=]/g, ""); while (f < e.length) { s = this._keyStr.indexOf(e.charAt(f++)); o = this._keyStr.indexOf(e.charAt(f++)); u = this._keyStr.indexOf(e.charAt(f++)); a = this._keyStr.indexOf(e.charAt(f++)); n = s << 2 | o >> 4; r = (o & 15) << 4 | u >> 2; i = (u & 3) << 6 | a; t = t + String.fromCharCode(n); if (u != 64) { t = t + String.fromCharCode(r) } if (a != 64) { t = t + String.fromCharCode(i) } } t = Base64._utf8_decode(t); return t }, _utf8_encode: function (e) { e = e.replace(/\r\n/g, "\n"); var t = ""; for (var n = 0; n < e.length; n++) { var r = e.charCodeAt(n); if (r < 128) { t += String.fromCharCode(r) } else if (r > 127 && r < 2048) { t += String.fromCharCode(r >> 6 | 192); t += String.fromCharCode(r & 63 | 128) } else { t += String.fromCharCode(r >> 12 | 224); t += String.fromCharCode(r >> 6 & 63 | 128); t += String.fromCharCode(r & 63 | 128) } } return t }, _utf8_decode: function (e) { var t = ""; var n = 0; var r = c1 = c2 = 0; while (n < e.length) { r = e.charCodeAt(n); if (r < 128) { t += String.fromCharCode(r); n++ } else if (r > 191 && r < 224) { c2 = e.charCodeAt(n + 1); t += String.fromCharCode((r & 31) << 6 | c2 & 63); n += 2 } else { c2 = e.charCodeAt(n + 1); c3 = e.charCodeAt(n + 2); t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63); n += 3 } } return t } }

var a2w_reload_page_after_ajax = false;
var a2w_update_action_lock = false;

function a2w_get_product_proc(params, callback) {
    if (typeof a2w_get_product === "function") {
        a2w_get_product(params, callback);
    } else {
        callback('error', false, 'Please install and activate the ali2woo chrome extension in your browser: <a href="' + a2w_wc_pl_script.chrome_url + '">Get Chrome Extension</a>');
    }
}

(function ($, window, document, undefined) {
    $(function () {

        $(document).on("click", ".a2w-product-info", function () {
            var id = $(this).attr('id').split('-')[1];
            $.a2w_show(id);
            return false;
        });

        $.a2w_show = function (id) {
            $('<div id="a2w-dialog' + id + '"></div>').dialog({
                dialogClass: 'a2w-dialog',
                width: "400px",
                modal: true,
                title: "Aliexpress Info (ID: " + id + ")",
                open: function () {
                    $('#a2w-dialog' + id).html(a2w_wc_pl_script.lang.please_wait_data_loads);
                    var data = { 'action': 'a2w_product_info', 'id': id };

                    $.post(ajaxurl, data, function (response) {

                        var json = jQuery.parseJSON(response);


                        if (json.state === 'error') {

                            console.log(json);

                        } else {
                            $('#a2w-dialog' + json.data.id).html(json.data.content.join('<br/>'));
                        }

                    });


                },
                closeText: "",
                close: function (event, ui) {
                    $("#a2w-dialog" + id).remove();
                },
                buttons: {
                    Ok: function () {
                        $(this).dialog("close");
                    }
                }
            });

            return false;

        };

        function a2w_js_update_product(products_to_update, state, on_load_calback) {
            if (products_to_update.length > 0) {
                var data = products_to_update.shift();

                const post_import = function (post_data = {}) {
                    jQuery.post(ajaxurl, post_data).done(function (response) {
                        var json = jQuery.parseJSON(response);
                        if (json.state !== 'ok') {
                            console.log(json);
                        }

                        if (json.state === 'error') {
                            state.update_error_cnt += data.ids.length;
                        } else {
                            state.update_error_cnt += json.update_state.error;
                            state.update_cnt += json.update_state.ok;
                        }

                        if (on_load_calback) {
                            on_load_calback(json.state, state);
                        }

                        a2w_js_update_product(products_to_update, state, on_load_calback);
                    }).fail(function (xhr, status, error) {
                        console.log(error);
                        state.update_error_cnt += data.ids.length;

                        if (on_load_calback) {
                            on_load_calback('error', state);
                        }

                        a2w_js_update_product(products_to_update, state, on_load_calback);
                    });
                }


                if (a2w_wc_pl_script.chrome_ext_import && data.action == 'a2w_sync_products') {
                    var external_id = jQuery('#a2w-' + data.ids[0]).attr('data-external-id');
                    a2w_get_product_proc({ id: external_id, locale: a2w_wc_pl_script.locale, curr: a2w_wc_pl_script.currency }, function (apd_state, apd, msg) {
                        if (apd_state !== 'error') {
                            const apd_items = [{ id: external_id, apd }]
                            post_import({ ...data, apd_items: apd_items })
                        } else {
                            console.log('Error! a2w_get_product: ', msg);
                            state.update_error_cnt += data.ids.length;
                            if (on_load_calback) {
                                on_load_calback('error', state);
                            }
                            a2w_js_update_product(products_to_update, state, on_load_calback);
                        }
                    });
                } else {
                    post_import(data)
                }
            }
        }

        $("#doaction, #doaction2").click(function (e) {
            if (a2w_update_action_lock) {
                e.preventDefault();
                return;
            }
            var check_action = ($(this).attr('id') == 'doaction') ? $('#bulk-action-selector-top').val() : $('#bulk-action-selector-bottom').val();
            if (!a2w_update_action_lock && ('a2w_product_update_manual' === check_action || 'a2w_product_update_reviews_manual' === check_action)) {
                e.preventDefault();
                a2w_update_action_lock = true;
                var update_per_request = 1;

                var products_to_update = [];

                var cnt = 0;
                var total_ids = 0;
                var data = { 'action': ('a2w_product_update_manual' === check_action) ? 'a2w_sync_products' : 'a2w_sync_products_reviews', 'ids': [] }
                $('input:checkbox[name="post[]"]:checked').each(function () {
                    total_ids++;
                    cnt++;
                    data.ids.push($(this).val());
                    if (cnt === update_per_request) {
                        products_to_update.push(data);
                        cnt = 0;
                        data = { 'action': ('a2w_product_update_manual' === check_action) ? 'a2w_sync_products' : 'a2w_sync_products_reviews', 'ids': [] };
                    }
                });

                if (data.ids.length > 0) {
                    products_to_update.push(data);
                }

                if (total_ids > 0) {
                    a2w_show_block($('.wp-list-table.posts'), a2w_sprintf(a2w_wc_pl_script.lang.process_update_d_of_d, 0, total_ids));

                    a2w_sprintf(a2w_wc_pl_script.lang.process_update_d_of_d, 0, total_ids)
                    var on_load = function (response_state, state) {
                        if ((state.update_cnt + state.update_error_cnt) === state.num_to_update) {
                            a2w_update_block($(".wp-list-table.posts"), a2w_sprintf(a2w_wc_pl_script.lang.complete_result_updated_d_erros_d, state.update_cnt, state.update_error_cnt));
                            a2w_update_action_lock = false;
                            location.reload();
                        } else {
                            a2w_update_block($(".wp-list-table.posts"), a2w_sprintf(a2w_wc_pl_script.lang.process_update_d_of_d_erros_d, state.update_cnt, state.num_to_update, state.update_error_cnt));
                        }
                    };

                    var state = { num_to_update: total_ids, update_cnt: 0, update_error_cnt: 0 };
                    a2w_js_update_product(products_to_update, state, on_load);
                }
            }
        });
    });

})(jQuery, window, document);

function a2w_show_block(element, message) {
    jQuery(element).css('position', 'relative');
    jQuery(element).css('zoom', '1');
    jQuery(element).append('<div class="blockUI blockOverlay" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255); opacity: 0.6; cursor: wait; position: absolute;"></div>');
    jQuery(element).append('<div class="blockUI blockMsg blockElement" style="z-index: 1011; position: absolute; padding: 0px; margin: 0px;margin-top: -1em;line-height: 2em; top: 50%; left: calc(50% + 2em); text-align: center; color: rgb(0, 0, 0); font-weight: bold;background-color: rgb(255, 255, 255); cursor: wait;">' + message + '</div>');
}

function a2w_update_block(element, message) {
    jQuery(element).find('.blockUI.blockMsg').html(message);
}

function a2w_hide_block(element) {
    jQuery(element).css('position', '');
    jQuery(element).css('zoom', '');
    jQuery(element).find('.blockUI').remove();
}
