/*----------------------------------------------------------------------------*\
	PRICING LEGEND SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $pricing ) {
		$pricing.trigger( 'mpc.inited' );
	}

	var $pricing_legend = $( '.mpc-pricing-legend' );

	$pricing_legend.each( function() {
		var $pricing = $( this );

		$pricing.one( 'mpc.init', function() {
			init_shortcode( $pricing );
		});
	});
} )( jQuery );
