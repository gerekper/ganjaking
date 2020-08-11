( function( window, document, $ ) {
	'use strict';

	$( document ).ready( function() {
		// Order page
		$( '#woocommerce-order-items' ).on( 'click.tc', 'a.tm-delete-order-item', function( e ) {
			var item = $( this ).closest( 'tr.item, tr.fee, tr.shipping' );
			var itemId;
			var key;

			e.preventDefault();

			itemId = $( "<input type='hidden' class='tm_meta_serialized' name='tm_item_id' />" ).val( item.attr( 'data-tm_item_id' ) );
			key = $( "<input type='hidden' class='tm_meta_serialized' name='tm_key' />" ).val( item.attr( 'data-tm_key_id' ) );
			item.prepend( itemId ).prepend( key );
			$( '.button.calculate-action' ).trigger( 'click' );
		} );
	} );
}( window, document, window.jQuery ) );
