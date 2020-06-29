/*----------------------------------------------------------------------------*\
	PRODUCTS CATEGORY SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $products_category ) {
		$products_category.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_category = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $products_category = this.$el.find( '.mpc-wc-category' );

				$products_category.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $products_category ] );
				$body.trigger( 'mpc.font-loaded', [ $products_category ] );
				$body.trigger( 'mpc.inited', [ $products_category ] );

				init_shortcode( $products_category );

				window.InlineShortcodeView_mpc_wc_category.__super__.rendered.call( this );
			}
		} );
	}

	var $products_categories = $( '.mpc-wc-category' );

	$products_categories.each( function() {
		var $products_category = $( this );

		$products_category.one( 'mpc.init', function () {
			init_shortcode( $products_category );
		} );
	} );
} )( jQuery );
