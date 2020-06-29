/*----------------------------------------------------------------------------*\
 TIMELINE BASIC SHORTCODE
 \*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $item ) {
		//if( $item.is( '.mpc-animation' ) )
		$item.trigger( 'mpc.inited' );
	}

	var $timeline_items = $( '.mpc-timeline-item__wrap' );

	$timeline_items.each( function() {
		var $timeline_item = $( this );

		$timeline_item.one( 'mpc.init', function() {
			init_shortcode( $timeline_item );
		} );
	});

} )( jQuery );
