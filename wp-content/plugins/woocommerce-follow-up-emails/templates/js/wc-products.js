jQuery(document).ready(function($) {

    // Create a new list
    $( '#woocommerce-product-data' ).on( 'click', '.add-new-fue-list', function() {
        var list = $("#new_fue_list");

        if ( list.val().length == 0 ) {
            return;
        }

        $("#follow_ups_product_data").block({
            message: null,
            overlayCSS: {background: '#fff url(' + FUE_Products.ajax_loader + ') no-repeat center', opacity: 0.6}
        });

        $.post( ajaxurl, { action: 'fue_create_list', name: list.val(), security: FUE_Products.add_new_fue_list_nonce }, function(resp) {
            inject_new_list( resp.id, list.val() );
            $("#follow_ups_product_data").unblock();
            list.val("");
        });
    } );

    function inject_new_list( slug, name ) {
        var html = '<label>\
            <input type="checkbox" name="fue_lists[]" value="'+ slug +'" checked />\
        '+ name +'\
        </label>\
        <br/>';

        $("p.fue_lists_field").append(html);
    }

});
