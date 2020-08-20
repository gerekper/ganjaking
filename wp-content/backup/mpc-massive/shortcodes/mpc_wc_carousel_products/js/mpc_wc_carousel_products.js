/*----------------------------------------------------------------------------*\
	CAROUSEL PRODUCTS SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function delay_init( $carousel ) {
		if ( $.fn.mpcslick && ! $carousel.is( '.slick-initialized' ) ) {
			init_shortcode( $carousel );
		} else {
			setTimeout( function() {
				delay_init( $carousel );
			}, 50 );
		}
	}

	function init_shortcode( $carousel ) {
		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			responsive: _mpc_vars.carousel_breakpoints( $carousel ),
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			setTimeout( function() {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_wc_carousel_products = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $wc_carousel_products = this.$el.find( '.mpc-wc-carousel-products' ),
				    $navigation = $wc_carousel_products.siblings( '.mpc-navigation' );

				$wc_carousel_products.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $wc_carousel_products, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $wc_carousel_products, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $wc_carousel_products, $navigation ] );

				setTimeout( function() {
					delay_init( $wc_carousel_products );
				}, 250 );

				window.InlineShortcodeView_mpc_wc_carousel_products.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-wc-carousel-products' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_wc_carousel_products.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-wc-carousel-products' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-wc-carousel-products' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $wc_carousels_products = $( '.mpc-wc-carousel-products' );

	$wc_carousels_products.each( function() {
		var $wc_carousel_products = $( this );

		$wc_carousel_products.one( 'mpc.init', function() {
			delay_init( $wc_carousel_products );
		} );
	});
} )( jQuery );
