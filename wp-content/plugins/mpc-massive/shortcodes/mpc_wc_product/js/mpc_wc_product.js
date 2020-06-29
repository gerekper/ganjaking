/*----------------------------------------------------------------------------*\
	WC PRODUCT SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $product ) {
		mpc_init_lightbox( $product, false );

		$product.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_product = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $product = this.$el.find( '.mpc-wc-product' );

				$product.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $product ] );
				$body.trigger( 'mpc.font-loaded', [ $product ] );
				$body.trigger( 'mpc.inited', [ $product ] );

				init_shortcode( $product );

				window.InlineShortcodeView_mpc_wc_product.__super__.rendered.call( this );
			}
		} );
	}

	var $products = $( '.mpc-wc-product' );

	$products.each( function() {
		var $product = $( this );

		$product.one( 'mpc.init', function () {
			init_shortcode( $product );
		} );
	} );
} )( jQuery );
