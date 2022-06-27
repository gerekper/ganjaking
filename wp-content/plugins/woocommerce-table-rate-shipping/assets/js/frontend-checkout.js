jQuery( document.body ).on( 'updated_checkout', function( datas ) {	
	var wc_info = jQuery( '.woocommerce-notices-wrapper .woocommerce-info' );

	wc_info.each( function ( idx, elem ) {
		if ( jQuery( elem ).data('wc_trs') === 'yes' ) {
			jQuery( elem ).remove();
		}
	} );
} );
