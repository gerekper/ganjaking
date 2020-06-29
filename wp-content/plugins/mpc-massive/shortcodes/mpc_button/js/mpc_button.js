/*----------------------------------------------------------------------------*\
	BUTTON SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $button ) {
		$button.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_button = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $button = this.$el.find( '.mpc-button' ),
					$set    = $button.closest( '.vc_element' );

				$button.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $set ] );

				init_shortcode( $button );

				_mpc_vars.$document.trigger( 'mpc.init-tooltip', [ $button.siblings( '.mpc-tooltip' ) ] );

				window.InlineShortcodeView_mpc_button.__super__.rendered.call( this );
			}
		} );
	}

	var $buttons = $( '.mpc-button' );

	$buttons.each( function() {
		var $button = $( this );

		$button.one( 'mpc.init', function () {
			init_shortcode( $button );
		} );
	} );
} )( jQuery );
