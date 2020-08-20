/*----------------------------------------------------------------------------*\
	RIBBON SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $ribbon ) {
		$ribbon.trigger( 'mpc.inited' );
	}

	var $ribbons = $( '.mpc-ribbon' );

	$ribbons.each( function() {
		var $ribbon = $( this );

		$ribbon.one( 'mpc.init', function () {
			init_shortcode( $ribbon );
		} );
	} );
} )( jQuery );
