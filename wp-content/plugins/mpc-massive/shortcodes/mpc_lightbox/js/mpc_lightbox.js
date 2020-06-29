/*----------------------------------------------------------------------------*\
	LIGHTBOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $lightbox ) {
		mpc_init_lightbox( $lightbox, false );

		$lightbox.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_lightbox = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $lightbox = this.$el.find( '.mpc-lightbox' );

				$lightbox.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.font-loaded', [ $lightbox ] );
				$body.trigger( 'mpc.inited', [ $lightbox ] );

				init_shortcode( $lightbox );

				window.InlineShortcodeView_mpc_lightbox.__super__.rendered.call( this );
			}
		} );
	}

	var $lightboxs = $( '.mpc-lightbox' );

	$lightboxs.each( function() {
		var $lightbox = $( this );

		$lightbox.one( 'mpc.init', function () {
			init_shortcode( $lightbox );
		} );
	} );
} )( jQuery );
