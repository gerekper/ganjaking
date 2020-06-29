/*----------------------------------------------------------------------------*\
	CAROUSEL IMAGE SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function carousel_responsive( $carousel ) {
		if( _mpc_vars.breakpoints.custom( '(min-width: 1200px)' ) ) {
			if( $carousel.is( '.mpc-layout--classic' ) ) {
				$carousel.css( 'height', '' );
			} else {
				$carousel.css( 'height', $carousel.attr( 'data-height' ) );
			}

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
		    _height = 9999;

		if( $carousel.is( '.mpc-layout--fluid' ) && ! $carousel.is( '.mpc-force-height' ) ) {
			$slides.each( function() {
				var _slide_height = parseInt( $( this ).attr( 'data-height' ) );

				if( _slide_height < _height ) {
					_height = _slide_height;
				}
			});

			$carousel.css( { height: _height } ).attr( 'data-height', _height );
		}

		if( $carousel.is( '.mpc-layout--fluid' ) ) {
			var _data = $carousel.data( 'mpcslick' );

			_data.slidesToShow = $slides.length - 1;

			$carousel.attr( 'data-mpcslick', JSON.stringify( _data ) );
		}

		$carousel.on( 'init', function() {
			setTimeout( function(){
				mpc_init_lightbox( $carousel, true );
			}, 250 );
		});

		$carousel.mpcslick({
			prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
			responsive: _mpc_vars.carousel_breakpoints( $carousel, 2 ),
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

		carousel_responsive( $carousel );

		_mpc_vars.$window.on( 'mpc.resize', function() {
			if( !$carousel.is( '.slick-slider' ) ) {
				$carousel.mpcslick({
					prevArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-prev',
					nextArrow: '[data-mpcslider="' + $carousel.attr( 'id' ) + '"] .mpcslick-next',
					responsive: _mpc_vars.carousel_breakpoints( $carousel ),
					rtl: _mpc_vars.rtl.global()
				});
			}

			setTimeout( function() {
				carousel_responsive( $carousel );
			}, 250 );
		});

		$carousel.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		window.InlineShortcodeView_mpc_carousel_image = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_image = this.$el.find( '.mpc-carousel-image' ),
				    $navigation = $carousel_image.siblings( '.mpc-navigation' );

				$carousel_image.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_image, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_image, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_image, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_image );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_image.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-image' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_image.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-image' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-image' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_image = $( '.mpc-carousel-image' );

	$carousels_image.each( function() {
		var $carousel_image = $( this );

		$carousel_image.one( 'mpc.init', function() {
			delay_init( $carousel_image );
		} );
	});
} )( jQuery );
