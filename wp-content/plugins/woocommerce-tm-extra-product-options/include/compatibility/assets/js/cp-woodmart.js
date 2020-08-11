( function( window, document, $ ) {
	'use strict';

	$( document ).ready( function() {
		$( window ).on( 'tcEpoMaybeChangePriceHtml', function( event, dataObject ) {
			var tcAPI = $.tcAPI();
			if ( event && dataObject && dataObject.epo ) {
				$( '.woodmart-sticky-btn-cart .price' )
					.html( $.epoAPI.util.decodeHTML( $.epoAPI.template.html( tcAPI.templateEngine.tc_formatted_price, { price: dataObject.nativePrice } ) ) )
					.show();
			}
		} );

		$( '.woodmart-sticky-btn-cart .input-text.qty' )
			.off( 'change' )
			.on( 'change', function() {
				$( '.summary-inner .qty' ).val( $( this ).val() ).trigger( 'change' );
			} );
	} );
}( window, document, window.jQuery ) );
