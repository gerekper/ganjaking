/*----------------------------------------------------------------------------*\
	MPC_ALIGN PARAM
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $aligns = $( '.mpc-vc-align' );

	$aligns.each( function() {
		var $align  = $( this ),
			$input  = $align.siblings( '.mpc-value' ),
			$radios = $align.find( '.mpc-align-radio' );

		$align.on( 'change', '.mpc-align-radio', function() {
			$input.val( $( this ).val() );
		} );

		$input.on( 'change', function() {
			$radios.filter( '[value="' + $input.val() + '"]' ).click();
		} );
	} );
} )( jQuery );
