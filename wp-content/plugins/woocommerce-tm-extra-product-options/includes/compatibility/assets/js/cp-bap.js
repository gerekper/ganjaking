( function( window, document, $ ) {
	'use strict';

	var tcAPI;

	function getQtyElement( currentCart ) {
		var qty = currentCart.find( tcAPI.qtySelector ).last();
		if ( qty.length === 0 ) {
			qty = currentCart.find( tcAPI.associateQtySelector ).last();
		}
		return qty;
	}

	// document ready
	$( function() {
		tcAPI = $.tcAPI ? $.tcAPI() : null;
		if ( ! tcAPI ) {
			return;
		}

		$( window ).on( 'epoEventHandlers', function( event, dataObject ) {
			var currentCart;
			var totalsHolderContainer;
			var totalsHolder;
			var qtyElement;
			var qty;

			if ( event && dataObject && dataObject.epo ) {
				currentCart = dataObject.currentCart;
				totalsHolderContainer = dataObject.totalsHolderContainer;
				totalsHolder = dataObject.totalsHolder;
				qtyElement = getQtyElement( currentCart );

				// We are forced to use the following trigger as the plugin doesn't offer its own
				currentCart.on( 'woocommerce-product-addons-update', function() {
					var v = $( this ).find( '#bkap_price_charged' ).val();
					qty = qtyElement.val();
					if ( qty > 0 ) {
						v = v / qty;
					}

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
