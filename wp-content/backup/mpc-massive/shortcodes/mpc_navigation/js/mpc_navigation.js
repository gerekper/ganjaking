/*----------------------------------------------------------------------------*\
	NAVIGATION SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	var $navigations = $( '.mpc-navigation' );

	function calculate_navigation( $navigation, $carousel ) {
		var $nav_items = $navigation.parents( '.mpc-carousel__wrapper' ).find( '.mpc-navigation' ),
		    _stretched_size = $carousel.width(),
		    _window_size = _mpc_vars.window_width;

		if( $carousel.is( '.mpc-carousel--stretched' ) ) {
			$nav_items.addClass( 'mpc-nav--stretched' );

			if( !$navigation.is( '.mpc-navigation--style_1' ) && !$navigation.is( '.mpc-navigation--style_2' ) ) {
				$nav_items.first().css( 'margin-left', -$carousel.offset().left );
				$nav_items.last().css( 'margin-right', -( _window_size - ( $carousel.offset().left + _stretched_size ) ) );
			}
		} else if( $navigation.is( '.mpc-navigation--style_6' ) ) {
			$nav_items.first().css( 'margin-left', -$carousel.offset().left );
			$nav_items.last().css( 'margin-right', -( _window_size - ( $carousel.offset().left + _stretched_size ) ) );
		} else if( $carousel.parents( '.mpc-row' ).attr( 'data-vc-stretch-content' ) == 'true' ) {
			$nav_items.addClass( 'mpc-nav--stretched' );
		}
	}

	function init_shortcode( $navigation ) {
		if( $navigation.is( '.mpc-inited' ) ) {
			return;
		}

		var $carousel  = $navigation.siblings( '[class^="mpc-carousel-"], .mpc-pricing-box' ),
		    $nav_items = $navigation.parents( '.mpc-carousel__wrapper' ).find( '.mpc-navigation' );

		_mpc_vars.$window.on( 'load', function() {
			calculate_navigation( $navigation, $carousel );
		});

		$nav_items.trigger( 'mpc.inited' );
	}

	$navigations.each( function() {
		var $navigation = $( this );

		$navigation.one( 'mpc.init', function () {
			init_shortcode( $navigation );
		} );
	} );

	_mpc_vars.$window.on( 'mpc.resize', function() {
		$.each( $navigations, function() {
			var $navigation = $( this ),
			    $carousel   = $navigation.siblings( '[class^="mpc-carousel-"], .mpc-pricing-box' );

			calculate_navigation( $navigation, $carousel );
		} );
	} );
} )( jQuery );
