jQuery(document).ready( function(){
	jQuery( document ).on( 'click', '.subscription_renewal_early', function( event ) {
		
		event.preventDefault();		
		var current = jQuery(this);
		var url = jQuery(this).attr('href');

		jQuery('.wccs-currency-error').remove();

		if ( typeof WCSViewSubscription != '' ) {
			var data = {
				action: wccs_early_renewal_subscription.action,
				nonce: wccs_early_renewal_subscription.nonce,
				subscription_renewal_id : WCSViewSubscription.subscription_id
			}

			jQuery.post( wccs_early_renewal_subscription.admin_url, data, function ( response ) {

				var response = JSON.parse(response);
				//console.log( response );

				if ( 'failed' === response.status ) {
					if ( jQuery('.wccs-currency-error').length <= 0 ) {
						current.after(`<p class="wccs-currency-error woocommerce-error" style="margin-top: 10px">${response.msg}</p>`);
					}
				}

				if ( 'success' === response.status ) {					
					window.location.href = url;
				}

			});
		}
	});
});