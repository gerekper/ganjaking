( function( $ ) {

	$( function() {
		$( '.global-quantity' ).show();

		$( 'body' ).on( 'change', '.global-quantity .quantity .qty', function() {
			var qty  = $( this ).val(),
				wrap = $( this ).closest( '.photography-products' );

			$( '.quantity .qty', wrap ).val( qty );
		});
	});

}( jQuery ) );
