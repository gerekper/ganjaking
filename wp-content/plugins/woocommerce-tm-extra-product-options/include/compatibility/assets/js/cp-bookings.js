( function( document, $ ) {
	'use strict';

	function tcAdjustUupdatePriceFormatedPrice( price ) {
		return price.toString().replace( '.00', ',-' );
	}

	$( document ).ready( function() {
		if ( ! $.epoAPI ) {
			return;
		}

		$.epoAPI.addFilter( 'tc_adjust_update_price_formated_price', tcAdjustUupdatePriceFormatedPrice, 10, 1 );
		$.epoAPI.addFilter( 'tc_adjust_update_price_original_price', tcAdjustUupdatePriceFormatedPrice, 10, 1 );
	} );
}( document, window.jQuery ) );
