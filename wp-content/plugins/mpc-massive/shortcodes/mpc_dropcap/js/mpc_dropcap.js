/*----------------------------------------------------------------------------*\
	DROPCAP SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $dropcap ) {
		$dropcap.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_dropcap = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $dropcap = this.$el.find( '.mpc-dropcap' );

				$dropcap.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $dropcap ] );
				$body.trigger( 'mpc.font-loaded', [ $dropcap ] );
				$body.trigger( 'mpc.inited', [ $dropcap ] );

				init_shortcode( $dropcap );

				window.InlineShortcodeView_mpc_dropcap.__super__.rendered.call( this );
			}
		} );
	}

	var $dropcaps = $( '.mpc-dropcap' );

	$dropcaps.each( function() {
		var $dropcap = $( this );

		$dropcap.one( 'mpc.init', function () {
			init_shortcode( $dropcap );
		} );
	} );
} )( jQuery );
