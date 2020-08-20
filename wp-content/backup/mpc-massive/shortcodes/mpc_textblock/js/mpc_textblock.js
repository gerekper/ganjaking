/*----------------------------------------------------------------------------*\
	TEXTBLOCK SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $textblock ) {
		$textblock.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_textblock = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $textblock = this.$el.find( '.mpc-textblock' );

				$textblock.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $textblock ] );
				$body.trigger( 'mpc.font-loaded', [ $textblock ] );
				$body.trigger( 'mpc.inited', [ $textblock ] );

				init_shortcode( $textblock );

				window.InlineShortcodeView_mpc_textblock.__super__.rendered.call( this );
			}
		} );
	}

	var $textblocks = $( '.mpc-textblock' );

	$textblocks.each( function() {
		var $textblock = $( this );

		$textblock.one( 'mpc.init', function () {
			init_shortcode( $textblock );
		} );
	} );
} )( jQuery );
