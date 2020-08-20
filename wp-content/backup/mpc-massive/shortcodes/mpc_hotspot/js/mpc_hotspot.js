/*----------------------------------------------------------------------------*\
	HOTSPOT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $hotspot ) {
		var $siblings = $hotspot.siblings( '.mpc-hotspot' );

		$hotspot.on( 'mouseenter mouseover', function() {
			$siblings.removeClass( 'mpc-active' );
			$hotspot.addClass( 'mpc-active' );
		} );

		$hotspot.trigger( 'mpc.inited' );
		$hotspot.find( '.mpc-hotspot__icon' ).trigger( 'mpc.inited' );
	}

	function init_frontend( $hotspot ) {
		var $vc_handler = $hotspot.parents( '.vc_mpc_hotspot' ),
			_position = $hotspot.data( 'position' );

		$vc_handler.css( {
			'top': _position[ 1 ] + '%',
			'left': _position[ 0 ] + '%'
		} );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_hotspot = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $hotspot = this.$el.find( '.mpc-hotspot' ),
				    $tooltip = $hotspot.find( '.mpc-tooltip' );

				$hotspot.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $hotspot ] );
				$body.trigger( 'mpc.font-loaded', [ $hotspot ] );
				$body.trigger( 'mpc.inited', [ $hotspot, $tooltip ] );

				init_shortcode( $hotspot );
				init_frontend( $hotspot  );

				window.InlineShortcodeView_mpc_hotspot.__super__.rendered.call( this );
			}
		} );
	}

	var $hotspots = $( '.mpc-hotspot' );

	$hotspots.each( function() {
		var $hotspot = $( this );

		$hotspot.one( 'mpc.init', function () {
			init_shortcode( $hotspot );
		} );
	} );
} )( jQuery );
