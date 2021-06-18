jQuery( function () {
	function woocommerce_gpf_currency_update() {
		const currencyable_feeds = [ 'google', 'bing', 'googlelocalproductinventory' ];
		var feed_type = jQuery( '#feed_type' ).val();
		if ( currencyable_feeds.includes( feed_type ) ) {
			jQuery( '#gpf_currency_container' ).show();
			jQuery( 'select#gpf_currency' ).attr( 'name', 'currency' );

		} else {
			jQuery( '#gpf_currency_container' ).hide();
			jQuery( 'select#gpf_currency' ).attr( 'name', '' );
		}
	}

	jQuery( document ).on( 'change', '#feed_type', woocommerce_gpf_currency_update );
	woocommerce_gpf_currency_update();
} );
