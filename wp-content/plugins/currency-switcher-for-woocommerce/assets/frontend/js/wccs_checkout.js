jQuery(document).ready( function(){
    jQuery( document ).on( 'updated_checkout', function( event ) {
        if ( wccs_checkout != '' ) {
            if ( wccs_checkout.is_shop_currency && wccs_checkout.is_billing_currency ) {
                var url = wccs_checkout.admin_url;
                var country_code = jQuery('#billing_country').val();
                console.log( country_code );
                var data = {
                    action: wccs_checkout.action,
                    nonce: wccs_checkout.nonce,
                    billing_currency: country_code,
                };

                jQuery.post( url, data, function(response) {
                    response = JSON.parse( response );
                    if ( response.status == 'success' ) {
                        window.location.href = response.url;
                    }
                });
            }
        }
    });


    jQuery( document ).on(
		"click",
		"#place_order",
		function (e) {
            if ( wccs_checkout.shop_currency != '1' ) {
                e.preventDefault();
                var url = wccs_checkout.admin_url;
                var data = {
                    action: 'wccs_currency_to_default',
                    nonce: wccs_checkout.nonce,
                };

                jQuery.post( url, data, function(response) {
                    //response = JSON.parse( response );
                    console.log(response);
                    if ( response == 'success' ) {
                        jQuery("form.woocommerce-checkout").submit();
                    }
                });
            }
		}
	);
});