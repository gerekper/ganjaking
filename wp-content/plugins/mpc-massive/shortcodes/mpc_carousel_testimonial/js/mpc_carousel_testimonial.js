/*----------------------------------------------------------------------------*\
	CAROUSEL TESTIMONIAL SHORTCODE
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
		var _selector = ( window.vc_mode == 'admin_frontend_editor' ) ? '[data-tag="mpc_testimonial"]' : 'div';

		$carousel.children( '.mpc-init' ).removeClass( 'mpc-init' );

		if( $carousel.is( '.mpc-carousel--gap' ) ) {
			$carousel.find( '.mpc-testimonial' ).each( function() {
				var $testimonial = $( this );

				if ( ! $testimonial.parent().is( '.mpc-gap' ) ) {
					$testimonial.wrap( '<div class="mpc-gap" />' );
				}
			} );
		}

		$carousel.mpcslick( {
			slide: _selector,
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			adaptiveHeight: true,
			responsive: _mpc_vars.carousel_breakpoints( $carousel, 1 ),
			rtl: _mpc_vars.rtl.global()
		} );

		$carousel.on( 'mouseleave', function () {
			setTimeout( function () {
				var _slick = $carousel.mpcslick( 'getSlick' );

				if ( _slick.options.autoplay ) {
					$carousel.mpcslick( 'play' );
				}
			}, 250 );
		} );

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_testimonial = window.InlineShortcodeViewContainer.extend( {
			events: {
				'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
				'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
				'click > .vc_controls .vc_control-btn-append': 'appendElement',
				'click > .vc_empty-element': 'appendElement',
				'mouseenter': 'resetActive',
				'mouseleave': 'holdActive',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_testimonial = this.$el.find( '.mpc-carousel-testimonial' ),
				    $navigation = $carousel_testimonial.siblings( '.mpc-navigation' );

				$carousel_testimonial.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_testimonial, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_testimonial );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_testimonial.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-testimonial' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_testimonial.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-testimonial' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-testimonial' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_testimonial = $( '.mpc-carousel-testimonial' );

	$carousels_testimonial.each( function() {
		var $carousel_testimonial = $( this );

		$carousel_testimonial.one( 'mpc.init', function() {
			delay_init( $carousel_testimonial );
		} );
	});
} )( jQuery );
