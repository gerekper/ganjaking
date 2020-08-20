/*----------------------------------------------------------------------------*\
	ICON SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $icon ) {
		$icon.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_icon = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $icon = this.$el.find( '.mpc-icon' );

				$icon.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $icon ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $icon ] );

				init_shortcode( $icon );

				window.InlineShortcodeView_mpc_icon.__super__.rendered.call( this );
			}
		} );
	}

	var $icons = $( '.mpc-icon' );

	$icons.each( function() {
		var $icon = $( this );

		$icon.one( 'mpc.init', function () {
			init_shortcode( $icon );
		} );
	} );
} )( jQuery );
