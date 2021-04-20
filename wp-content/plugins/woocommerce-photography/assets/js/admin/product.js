( function( $ ) {

	$( document ).ready( function() {
		$( '#general_product_data .pricing' ).addClass( 'show_if_photography' );
		$( '#general_product_data ._tax_class_field' ).closest( '.options_group' ).addClass( 'show_if_photography' );
		$( '#product-type' ).trigger( 'change' );
	} );

}( jQuery ) );
