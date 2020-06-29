/*----------------------------------------------------------------------------*\
	DIVIDER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $divider ) {
		$divider.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_divider = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $divider = this.$el.find( '.mpc-divider' );

				$divider.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $divider ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $divider ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $divider ] );

				init_shortcode( $divider );

				window.InlineShortcodeView_mpc_divider.__super__.rendered.call( this );
			}
		} );
	}

	var $dividers = $( '.mpc-divider' );

	$dividers.each( function() {
		var $divider = $( this );

		$divider.one( 'mpc.init', function () {
			init_shortcode( $divider );
		} );
	} );
} )( jQuery );
