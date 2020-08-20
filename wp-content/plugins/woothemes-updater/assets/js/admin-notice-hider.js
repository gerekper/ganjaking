jQuery( window ).load( function ( e ) {
	// Send an AJAX request to set a site transient, once the "dismiss" button is clicked.
	jQuery( '#woothemes-helper-subscription-message .notice-dismiss' ).click( function ( e ) {
		data = { 'action':'woothemes_helper_dismiss_renew', 'woothemes_helper_dismiss_renew_nonce':woothemes_helper.dismiss_renew_nonce };
		jQuery.post( ajaxurl, data, function ( data ) {
			return data;
		});
	});

	// Send an AJAX request to set a site option, once the "dismiss" button is clicked on the activation notice.
	jQuery( '#woothemes-helper-product-activation-message .notice-dismiss' ).click( function ( e ) {
		data = { 'action':'woothemes_helper_dismiss_activation', 'woothemes_helper_dismiss_activation_nonce':woothemes_helper.dismiss_activation_nonce };
		jQuery.post( ajaxurl, data, function ( data ) {
			return data;
		});
	});
});