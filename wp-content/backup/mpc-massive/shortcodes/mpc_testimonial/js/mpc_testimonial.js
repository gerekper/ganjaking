/*----------------------------------------------------------------------------*\
	TESTIMONIAL SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_shortcode( $testimonial ) {
		$testimonial.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_testimonial = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $testimonial = this.$el.find( '.mpc-testimonial' );

				$testimonial.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $testimonial ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $testimonial ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $testimonial ] );

				init_shortcode( $testimonial );

				window.InlineShortcodeView_mpc_testimonial.__super__.rendered.call( this );
				this.afterRender();
			},
			afterRender: function() {
				var _parent = vc.shortcodes.findWhere( { id: this.model.get( 'parent_id' ) } );

				setTimeout( function() {
					_parent.trigger( 'mpc:forceRender' );
				}, 250 );
			}
		} );
	}

	var $testimonials = $( '.mpc-testimonial' );

	$testimonials.each( function() {
		var $testimonial = $( this );

		$testimonial.one( 'mpc.init', function () {
			init_shortcode( $testimonial );
		} );
	} );
} )( jQuery );
