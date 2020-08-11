( function( window, document, $ ) {
	'use strict';

	$( document ).ready( function() {
		$( window ).on( 'epoEventHandlers', function( event, dataObject ) {
			var currentCart;
			var totalsHolder;

			if ( event && dataObject && dataObject.epo ) {
				currentCart = dataObject.currentCart;
				totalsHolder = dataObject.totalsHolder;

				$( '#fancy-product-designer-' + totalsHolder.parent().attr( 'data-product-id' ) )
					.off( 'priceChange.cpf' )
					.on( 'priceChange.cpf', function( evt, sp, tp ) {
						var v = tp;

						if ( totalsHolder.data( 'fpdprice' ) === undefined ) {
							totalsHolder.data( 'fpdprice', parseFloat( v ) );
						} else {
							totalsHolder.data( 'fpdprice', parseFloat( v ) );
						}

						if ( totalsHolder.data( 'tcprice' ) === undefined ) {
							totalsHolder.data( 'tcprice', parseFloat( totalsHolder.data( 'price' ) ) );
						} else {
							totalsHolder.data( 'price', parseFloat( totalsHolder.data( 'tcprice' ) ) );
						}

						v = parseFloat( totalsHolder.data( 'price' ) ) + parseFloat( v );
						totalsHolder.parent().find( '.cpf-product-price' ).val( v );

						totalsHolder.data( 'price', v );

						currentCart.trigger( {
							type: 'tm-epo-update',
							norules: 2
						} );
					} );
			}
		} );
	} );
}( window, document, window.jQuery ) );
