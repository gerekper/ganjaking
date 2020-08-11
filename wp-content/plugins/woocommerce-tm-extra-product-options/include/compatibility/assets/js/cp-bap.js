( function( window, document, $ ) {
	'use strict';

	$( document ).ready( function() {
		$( window ).on( 'epoEventHandlers', function( event, dataObject ) {
			var currentCart;
			var totalsHolderContainer;
			var totalsHolder;

			if ( event && dataObject && dataObject.epo ) {
				currentCart = dataObject.currentCart;
				totalsHolderContainer = dataObject.totalsHolderContainer;
				totalsHolder = dataObject.totalsHolder;

				// We are forced to use the following trigger as the plugin doesn't offer its own
				currentCart.on( 'woocommerce-product-addons-update', function() {
					var v = $( this ).find( '#bkap_price_charged' ).val();

					totalsHolderContainer.find( '.cpf-product-price' ).val( v );
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
