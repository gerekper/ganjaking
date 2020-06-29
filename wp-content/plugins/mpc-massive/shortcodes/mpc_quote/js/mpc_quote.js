/*----------------------------------------------------------------------------*\
	QUOTE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $quote ) {
		$quote.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_quote = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $quote = this.$el.find( '.mpc-quote' );

				$quote.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $quote ] );
				$body.trigger( 'mpc.font-loaded', [ $quote ] );
				$body.trigger( 'mpc.inited', [ $quote ] );

				init_shortcode( $quote );

				window.InlineShortcodeView_mpc_quote.__super__.rendered.call( this );
			}
		} );
	}

	var $quotes = $( '.mpc-quote' );

	$quotes.each( function() {
		var $quote = $( this );

		$quote.one( 'mpc.init', function () {
			init_shortcode( $quote );
		} );
	} );
} )( jQuery );
