/*----------------------------------------------------------------------------*\
	SINGLE POST SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function resize( $single_post ) {
		if( $single_post.is( '.mpc-layout--style_4' ) ) {
			var $post_content = $single_post.find( '.mpc-post__wrapper > .mpc-post__content' );

			$post_content.css( 'margin-bottom', parseInt( $post_content.outerHeight() * -0.5 ) );
		}
	}

	function init_shortcode( $single_post ) {
		mpc_init_lightbox( $single_post, false );

		resize( $single_post );

		$single_post.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_single_post = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $single_post = this.$el.find( '.mpc-single-post' );

				$single_post.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $single_post ] );
				$body.trigger( 'mpc.font-loaded', [ $single_post ] );
				$body.trigger( 'mpc.inited', [ $single_post ] );

				init_shortcode( $single_post );

				window.InlineShortcodeView_mpc_single_post.__super__.rendered.call( this );
			}
		} );
	}

	var $single_posts = $( '.mpc-single-post' );

	$single_posts.each( function() {
		var $single_post = $( this );

		$single_post.one( 'mpc.init', function () {
			init_shortcode( $single_post );
		} );

		$single_post.on( 'mpc.resize', function() {

		} );
	} );
} )( jQuery );
