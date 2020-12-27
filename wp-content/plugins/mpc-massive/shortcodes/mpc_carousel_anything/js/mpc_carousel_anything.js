/*----------------------------------------------------------------------------*\
 CAROUSEL ANYTHING SHORTCODE
 \*----------------------------------------------------------------------------*/
(function( $ ) {
	"use strict";

	function wrap_shortcode( $carousel ) {
		$carousel.children().each( function() {
			var $this = $( this );

			$this
				.addClass( 'mpc-init--fast' )
				.wrap( '<div class="mpc-carousel__item-wrapper" />' );

			setTimeout( function() {
				$this.trigger( 'mpc.init-fast' );
			}, 20 );
		} );
	}

	function unwrap_shortcode( $carousel ) {
		$carousel.find( '.vc_element' ).each( function() {
			$( this ).unwrap().unwrap();
		} );
	}

	function get_initial( $carousel ) {
		return Math.random() * $carousel.children().length;
	}

	function delay_init( $carousel ) {
		if( $.fn.mpcslick && !$carousel.is( '.slick-initialized' ) ) {
			var _initial    = $carousel.data( 'slick-random' ) == 'true' ? get_initial( $carousel ) : $carousel.data( 'slick-initial' );

			$carousel.mpcslick( {
				prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
				nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
				adaptiveHeight: true,
				initialSlide: _initial,
				responsive: _mpc_vars.carousel_breakpoints( $carousel ),
				rtl: _mpc_vars.rtl.global()
			} );

			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		$carousel.trigger( 'mpc.inited' );
	}

	var $carousels_anything = $( '.mpc-carousel-anything' );

	$carousels_anything.each( function() {
		var $carousel_anything = $( this );

		wrap_shortcode( $carousel_anything );

		$carousel_anything.one( 'mpc.init', function() {
			delay_init( $carousel_anything );
		} );
	} );
})( jQuery );
