/**
 * Provides support for variable products for WooCommerce earlier than 2.4.0
 */
jQuery(document).ready(function($) {

    function msrp_get_variation_data_from_array(all_variations, variation_id) {

        var matching = [];

        for (var i = 0; i < all_variations.length; i++) {

            var this_variation = all_variations[i];
            var this_variation_id = this_variation.variation_id;

            if ( this_variation_id == variation_id ) {
                matching = this_variation;
                break;
            }
        }

        return matching;
    }

    function show_msrp ( e, msrp_html ) {

        var msrp_container = jQuery(e).find('div.single_variation .woocommerce_msrp');

        if ( msrp_container.length < 1 )
            jQuery(e).find('div.single_variation').prepend('<div class="woocommerce_msrp">' + woocommerce_msrp.msrp_description + ': <span class="woocommerce_msrp_price">' + msrp_html + '</span></div>');
        else
            msrp_container.find('span.woocommerce_msrp_price').html(msrp_html);

    }

    function hide_msrp (e) {

        var msrp_container = jQuery(e).find('div.single_variation .woocommerce_msrp');

        if ( msrp_container.length > 0 )
            msrp_container.remove();

    }



    jQuery(document).on( 'show_variation', '.variations_form', function() {

        var variation_id = jQuery(this).find('input[name=variation_id]').val();
        var product_id   = jQuery(this).attr('data-product_id');

        var all_variations		= $variation_form.data( 'product_variations' )

		// Fallback to window property if not set - backwards compat
		if ( ! all_variations )
			all_variations = window[ "product_variations" ][ product_id ];
		if ( ! all_variations )
			all_variations = window[ "product_variations" ];
		if ( ! all_variations )
			all_variations = window[ "product_variations_" + product_id ];

		variation = msrp_get_variation_data_from_array( all_variations, variation_id );

        var msrp = variation.msrp;
        var msrp_html = variation.msrp_html;
        var non_msrp_price = variation.non_msrp_price;

        if ( variation.msrp > 0 ) {

            if ( woocommerce_msrp.msrp_status == 'always' ) {

                show_msrp ( this, msrp_html );

            }  else if ( woocommerce_msrp.msrp_status == 'different' ) {

                if ( msrp != non_msrp_price ) {

                    show_msrp ( this, msrp_html );

                } else {

                    hide_msrp ( this );

                }

            }

        }

    });

});
