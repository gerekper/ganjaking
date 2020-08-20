/*----------------------------------------------------------------------------*\
	CAROUSEL POSTS SHORTCODE
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
		window.InlineShortcodeView_mpc_carousel_posts = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_posts = this.$el.find( '.mpc-carousel-posts' ),
				    $navigation = $carousel_posts.siblings( '.mpc-navigation' );

				$carousel_posts.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_posts, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_posts, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_posts, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_posts );
				}, 250 );

				window.InlineShortcodeView_mpc_carousel_posts.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-carousel-posts' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_carousel_posts.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-carousel-posts' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-carousel-posts' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_posts = $( '.mpc-carousel-posts' );

	$carousels_posts.each( function() {
		var $carousel_posts = $( this );

		$carousel_posts.one( 'mpc.init', function() {
			delay_init( $carousel_posts );
		} );
	});
} )( jQuery );
