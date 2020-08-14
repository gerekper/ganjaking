(function ($) {
    jQuery('#woocommerce-product-data').find('.product_data .hidden').each(function(){jQuery(this).removeClass('hidden');});
    jQuery('#product-type').trigger('change');

    // Save attributes and update variations.
    $( '#variable_product_options' ).on( 'reload', function() {
        var this_page = window.location.toString();
        this_page = this_page + '?product_id=' +  woocommerce_admin_meta_boxes.post_id;

        $( '#variable_product_options' ).load( this_page + ' #variable_product_options_inner' );
    });

    $( '#metakeyinput' ).addClass( 'hidden' ).prop( 'style', 'display:none;' );
})(jQuery);
