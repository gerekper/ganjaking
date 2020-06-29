/*----------------------------------------------------------------------------*\
	PRICING COLUMN SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $pricing ) {
		$pricing.trigger( 'mpc.inited' );
	}

	var $pricing_columns = $( '.mpc-pricing-column' );

	$pricing_columns.each( function() {
		var $pricing = $( this );

		$pricing.one( 'mpc.init', function() {
			init_shortcode( $pricing );
		});
	});
} )( jQuery );
