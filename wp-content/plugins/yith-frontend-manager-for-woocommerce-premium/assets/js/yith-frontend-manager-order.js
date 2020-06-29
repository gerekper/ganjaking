(function($) {

    $(window).load(function(){

        $( 'table.woocommerce_order_items tr td a.wc-order-item-name ').attr( 'href', '#woocommerce-order-items' );

        $( 'button.save-action' ).on( 'items_saved', function() {

            $( 'div.wc-order-data-row-toggle' ).not( 'div.wc-order-bulk-actions' ).slideUp();
            $( 'div.wc-order-bulk-actions' ).slideDown();
            $( 'div.wc-order-totals-items' ).slideDown();
            $( '#woocommerce-order-items' ).find( 'div.refund' ).hide();
            $( '.wc-order-edit-line-item .wc-order-edit-line-item-actions' ).show();

        });

        $( 'button.save-action' ).click( function() {
            $('.total .view .woocommerce-Price-amount.amount').html( $('.total .edit .wc_input_price').val() );
        });
    });

    $('.wc-order-item-name').on( 'click', function(e){
        e.preventDefault();

        var t = $(this),
            line_item_id = t.parents('tr').data('order_item_id'),
            data = {
            'action': 'yith_wcfm_get_product_url_from_line_item',
            'context' : 'frontend',
            'line_item_id': line_item_id,
            'order_id' : woocommerce_admin_meta_boxes.post_id
        };

        jQuery.post(
            yith_wcfm_orders.ajax_url,
            data,
            function(response) {
                if( response != false ){
                    location.href = response;
                }
        });
    });

    $('td.purchased').on('click', function(){
        $(this).find( 'div.items_list' ).toggle();
    });

    $( '#metakeyinput' ).addClass( 'hidden' ).prop( 'style', 'display:none;' );

    $( 'button.wc-reload' ).on( 'click', function(e){
        e.preventDefault();
        $('button.save_order ').trigger('click');
    });

})(jQuery);