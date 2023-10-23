function wc_trs_display_abort_text( datas ) {
	var wc_info     = jQuery( '.woocommerce-info' );
	var wc_chk_info = jQuery( 'form.woocommerce-checkout .woocommerce-info' );
	var has_trs     = false;

	wc_chk_info.each( function ( idx, elem ) {
		var current_elem = jQuery( elem );

		// Remove the element if abort text has been found previously.
		if ( 'yes' === current_elem.data('wc_trs') ) {
			if ( true === has_trs ) {
				current_elem.remove();
			}

			has_trs = true;
		}
	} );

	if ( false === has_trs ) {
		return;
	}

	wc_info.each( function ( idx, elem ) {
		var current_elem = jQuery( elem );
		if ( 'yes' === current_elem.data('wc_trs') && 1 > current_elem.parents( 'form.woocommerce-checkout' ).length ) {
			current_elem.remove();
		}
	} );
}

jQuery( document.body ).on( 'updated_checkout checkout_error', function( datas ) {	
	wc_trs_display_abort_text();
} );
