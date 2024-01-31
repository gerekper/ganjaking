;( function( $ ) {

	$( document ).on( "mfpOpen", function() {
		$( ".composite_form .composite_data" ).each( function() {
			$( this ).wc_composite_form();
		} );
	} );
} ) ( jQuery );
