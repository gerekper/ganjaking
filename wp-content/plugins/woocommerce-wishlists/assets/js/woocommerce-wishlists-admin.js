jQuery(document).ready(function ($) {

    $('input.check-column').click(function () {
        $(this).closest('table').find(':checkbox').attr('checked', this.checked);
    });


    $('#wc_wishlists_items').on('click', '.do_wl_bulk_action', function () {


        var action = $(this).closest('.wl_bulk_actions').find('select').val();
        var selected_rows = $('#woocommerce-wishlist-items').find('.check-column input:checked');
        var item_ids = [];

        $(selected_rows).each(function () {

            var $item = $(this).closest('tr.item, tr.fee');

            item_ids.push($item.attr('data-order_item_id'));

        });

        if (item_ids.length == 0) {
            alert(woocommerce_wishlist_writepanel_params.i18n_select_items);
            return;
        }

        if (action == 'delete') {

            var answer = confirm(woocommerce_wishlist_writepanel_params.remove_item_notice);

            if (answer) {

                $('table.woocommerce_order_items').block({
                    message: null,
                    overlayCSS: {
                        background: '#F6F6BC',
                        opacity: 0.6
                    }
                });

                var data = {
                    wlid: woocommerce_wishlist_writepanel_params.post_id,
                    wishlist_item_ids: item_ids,
                    action: 'woocommerce_remove_wishlist_item',
                    security: woocommerce_wishlist_writepanel_params.wishlist_item_nonce
                };

                $.ajax({
                    url: woocommerce_wishlist_writepanel_params.ajax_url,
                    data: data,
                    type: 'POST',
                    success: function (response) {
                        $(selected_rows).each(function () {
                            $(this).closest('tr.item, tr.fee').remove();
                        });
                        $('table.woocommerce_order_items').unblock();
                    }
                });

            }
        }

        return false;
    });


});

