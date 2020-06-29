/*----------------------------------------------------------------------------*\
	CAROUSEL SLIDER SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function carousel_responsive( $carousel ) {
		if( _mpc_vars.breakpoints.custom( '(min-width: 1200px)' ) ) {
			$carousel.css( 'height', '' );
			return;
		}

		var $slides = $carousel.find( 'img' ),
		    _max_height = $carousel.height(),
		    _current_slide = $carousel.mpcslick( 'slickCurrentSlide' );

		$.each( $slides, function() {
			var $slide = $( this ),
			    _ratio = ( $slide.attr( 'height' ) / $slide.attr( 'width' ) ) * 0.9;

			if( $carousel.width() < $slide.width() ) {
				_max_height = Math.min( parseInt( $carousel.width() * _ratio ), _max_height );
			}
		});

		$carousel.css( 'height', _max_height );
		$carousel.mpcslick( 'slickGoTo', _current_slide, true );
	}

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
		var $slides = $carousel.find( '.mpc-carousel__item-wrapper' ),
			_data   = $carousel.data( 'mpcslick' );

		$carousel.on( 'afterChange', function( ev, slick, currentSlide ) {
			$carousel.find( '.mpc-carousel__count' ).attr( 'data-current-slide', currentSlide + 1 );
		});

		_data.slidesToShow = $slides.length - 1;

		$carousel.attr( 'data-mpcslick', JSON.stringify( _data ) );

		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			slide: '.mpc-carousel__item-wrapper',
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev .mpc-nav__icon',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next .mpc-nav__icon',
			rtl: _mpc_vars.rtl.global()
		});

		$carousel.on( 'mouseleave', function() {
			var $this = $( this );

			setTimeout( function() {
				var _slick = $this.mpcslick( 'getSlick' );

				if( _slick.options.autoplay ) {
					$this.mpcslick( 'play' );
				}
			}, 250 );
		});

		carousel_responsive( $carousel );

		_mpc_vars.$window.on( 'resize', function() {
			carousel_responsive( $carousel );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_slider = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_slider = this.$el.find( '.mpc-carousel-slider' ),
					$navigation = $carousel_slider.siblings( '.mpc-navigation' );

				$carousel_slider.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_slider, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_slider, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_slider, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_slider );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_slider.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-slider' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_image.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-slider' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-slider' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_slider = $( '.mpc-carousel-slider' );

	$carousels_slider.each( function() {
		var $carousel_slider = $( this );

		$carousel_slider.one( 'mpc.init', function() {
			delay_init( $carousel_slider );
		} );
	} );
} )( jQuery );
