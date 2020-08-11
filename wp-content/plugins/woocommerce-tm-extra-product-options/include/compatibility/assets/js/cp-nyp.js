( function( window, document, $ ) {
	'use strict';

	$( document ).ready( function() {
		$( window ).on( 'epoEventHandlers', function( event, dataObject ) {
			var currentCart = dataObject.currentCart;
			var totalsHolderContainer = dataObject.totalsHolderContainer;
			var totalsHolder = dataObject.totalsHolder;

			if ( event && dataObject && dataObject.epo ) {
				// Name your price compatibility
				currentCart.off( 'woocommerce-nyp-update.cpf' ).on( 'woocommerce-nyp-update.cpf', function() {
					var nyp = currentCart.find( '.nyp' );
					var new_product_price = nyp.data( 'price' );

					if ( nyp.length > 0 ) {
						totalsHolderContainer.find( '.cpf-product-price' ).val( new_product_price );
						totalsHolder.data( 'price', new_product_price );
						currentCart.trigger( {
							type: 'tm-epo-update',
							norules: 2
						} );
					}
				} );

				$( 'body' )
					.off( 'woocommerce-nyp-updated.cpf' )
					.on( 'woocommerce-nyp-updated.cpf', function() {
						currentCart.trigger( 'woocommerce-nyp-update.cpf' );
					} );

				currentCart.trigger( 'woocommerce-nyp-update.cpf' );
			}
		} );
	} );
}( window, document, window.jQuery ) );
