/*----------------------------------------------------------------------------*\
	CALLOUT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $callout ) {
		$callout.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_callout = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $callout = this.$el.find( '.mpc-callout' );

				$callout.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $callout ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $callout ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $callout ] );

				init_shortcode( $callout );

				window.InlineShortcodeView_mpc_callout.__super__.rendered.call( this );
			},
		} );
	}

	var $callouts = $( '.mpc-callout' );

	$callouts.each( function() {
		var $callout = $( this );

		$callout.one( 'mpc.init', function() {
			init_shortcode( $callout );
		} );
	} );
} )( jQuery );
