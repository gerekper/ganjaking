/*----------------------------------------------------------------------------*\
	IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $image ) {
		mpc_init_lightbox( $image, false );

		$image.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_image = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $image  = this.$el.find( '.mpc-image' ),
					$set    = $image.closest( '.vc_element' );

				$image.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $set ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $set ] );

				setTimeout( function() {
					init_shortcode( $image );
				}, 250 );

				window.InlineShortcodeView_mpc_image.__super__.rendered.call( this );
			}
		} );
	}

	var $images = $( '.mpc-image' );

	$images.each( function() {
		var $image = $( this );

		$image.one( 'mpc.init', function() {
			init_shortcode( $image );
		} );
	});
} )( jQuery );
