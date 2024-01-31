;( function( $ ) {

	$( 'body .component' ).on( 'wc-composite-component-loaded', function () {
		$(this)
			.find( '.cart:not( .cart_group )' )
			.each(function () {
				$( 'body' ).trigger( 'wc-mmq-init-validation', [ $(this) ] );
			});
	});
} ) ( jQuery );
