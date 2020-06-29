/*----------------------------------------------------------------------------*\
	INTERACTIVE IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $interactive_image ) {
		$interactive_image.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_interactive_image = window.InlineShortcodeViewContainer.extend( {
			rendered: function() {
				var $interactive_image = this.$el.find( '.mpc-interactive_image' );

				$interactive_image.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $interactive_image ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $interactive_image ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $interactive_image ] );

				init_shortcode( $interactive_image );

				window.InlineShortcodeView_mpc_interactive_image.__super__.rendered.call( this );
			}
		} );
	}

	var $interactive_images = $( '.mpc-interactive_image' );

	$interactive_images.each( function() {
		var $interactive_image = $( this );

		$interactive_image.one( 'mpc.init', function () {
			init_shortcode( $interactive_image );
		} );
	} );
} )( jQuery );
