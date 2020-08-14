jQuery(document).ready(function ($) {
    var getUrlParameter = function (url, sParam) {
            var sPageURL = decodeURIComponent(url.substring(1)),
                sURLVariables = sPageURL.split(/[&|?]+/),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        },
        /**
         * Check if cookies are enabled
         *
         * @return bool
         * @since 2.0.0
         */

        is_cookie_enabled = function () {
            if (navigator.cookieEnabled) return true;

            // set and read cookie
            document.cookie = "cookietest=1";
            var ret = document.cookie.indexOf("cookietest=") != -1;

            // delete cookie
            document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";

            return ret;
        },
        /**
         * Shows new notices on the page.
         *
         * @param {Object} The Notice HTML Element in string or object form.
         */
        show_notice = function (html_element, $target) {
            if (!$target) {
                $target = $('.woocommerce-notices-wrapper:first') || $('.cart-empty').closest('.woocommerce') || $('.woocommerce-cart-form');
            }
            $target.prepend(html_element);
        },
        remove_from_save_for_later_list = function (item_id) {
            if (item_id) {

                if (yith_sfl_args.is_user_logged_in || is_cookie_enabled()) {
                    var data = {
                        'remove_from_savelist': item_id,
                        action: yith_sfl_args.actions.remove_from_savelist_action
                    };

                    $(document).find('#ywsfl_general_content').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: yith_sfl_args.ajax_url,
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            $(document).find('#ywsfl_general_content').html($(response.template).html());

                            show_notice(response.notice);
                        },
                        complete: function () {
                            $(document).find('#ywsfl_general_content').unblock();
                            $.scroll_to_notices($('[role="alert"]'));
                        }
                    });
                } else {
                    alert(yith_sfl_args.labels.cookie_disabled);
                }
            }
        };
    $(document).on('click', '.add_saveforlater', function (e) {

        e.preventDefault();

        var item_key = getUrlParameter($(this).attr('href'), 'save_for_later');

        var remove_btn = $(this).parents('tr').find('td.product-remove a');

        if (item_key) {

            if (yith_sfl_args.is_user_logged_in || is_cookie_enabled()) {

                var data = {
                    'save_for_later': item_key,
                    action: yith_sfl_args.actions.add_to_savelist_action
                };

                $.ajax({
                    type: 'GET',
                    url: yith_sfl_args.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {

                        remove_btn.click();
                        $(document).find('#ywsfl_general_content').html(response.template);
                    }
                });
            } else {
                alert(yith_sfl_args.labels.cookie_disabled);
            }
        }

    });
    $(document).on('click', '#ywsfl_container_list td.product-remove a.remove_from_savelist', function (e) {

        e.preventDefault();

        var item_id = $(this).data('item_id');
        remove_from_save_for_later_list(item_id);
    });


    $(document.body).on('added_to_cart', function (e, fragments, cart_hash, add_to_cart_btn) {

        var list_table = add_to_cart_btn.parents('#ywsfl_container_list');
        if (list_table.length) {

            var item_id = list_table.find( 'td.product-remove a').data('item_id');

            remove_from_save_for_later_list( item_id);
        }
    });
});