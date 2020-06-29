/*----------------------------------------------------------------------------*\
	CAROUSEL PRODUCTS CATEGORIES SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {

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
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_carousel_categories = window.InlineShortcodeView.extend( {
			events: {
				'click > .vc_controls .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_control-btn-clone': 'clone',
				'mousemove': 'checkControlsPosition',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon': 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon': 'nextSlide'
			},
			rendered: function() {
				var $carousel_wc_categories = this.$el.find( '.mpc-wc-carousel-categories' ),
					$navigation = $carousel_wc_categories.siblings( '.mpc-navigation' );

				$carousel_wc_categories.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $carousel_wc_categories, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $carousel_wc_categories, $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $carousel_wc_categories, $navigation ] );

				setTimeout( function() {
					delay_init( $carousel_wc_categories );
				}, 250 );

				window.InlineShortcodeView_mpc_wc_carousel_categories.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-wc-carousel-categories' ).mpcslick( 'unslick' );

				window.InlineShortcodeView_mpc_wc_carousel_categories.__super__.beforeUpdate.call( this );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-wc-carousel-categories' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-wc-carousel-categories' ).mpcslick( 'slickNext' );
			}
		} );
	}

	var $carousels_wc_categories = $( '.mpc-wc-carousel-categories' );

	$carousels_wc_categories.each( function() {
		var $carousel_wc_categories = $( this );

		$carousel_wc_categories.one( 'mpc.init', function() {
			delay_init( $carousel_wc_categories );
		} );
	});
} )( jQuery );
