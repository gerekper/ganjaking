/*----------------------------------------------------------------------------*\
	PRICING BOX SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function init_carousel( $wrapper, $slider ) {
		$slider.mpcslick( {
			prevArrow: '[data-mpcslider="' + $wrapper.attr( 'id' ) + '"] .mpcslick-prev',
			nextArrow: '[data-mpcslider="' + $wrapper.attr( 'id' ) + '"] .mpcslick-next',
			responsive: [
				{
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3
					}
				},
				{
					breakpoint: 768,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 480,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}
			]
		} );
	}

	function init_shortcode( $carousel ) {
		var $carousel_init = $carousel.find( '.mpc-pricing-box__wrapper' );

		if( $carousel.is( '.mpc-init--slick' ) ) {
			init_carousel( $carousel, $carousel_init );
		}

		$carousel.trigger( 'mpc.inited' );
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

	var $pricing_boxes = $( '.mpc-pricing-box' );

	$pricing_boxes.each( function() {
		var $pricing_box = $( this );

		$pricing_box.one( 'mpc.init', function() {
			delay_init( $pricing_box );
		});
	});

	/* FrontEnd Editor */
	function init_frontend( $pricing_box ) {
		var $columns = $pricing_box.find( '.mpc-pricing-column' );

		$columns.each( function() {
			var $this = $( this );

			$this.parents( '.vc_mpc_pricing_column' ).addClass( 'mpc-pricing-column' );
			$this.removeClass( 'mpc-pricing-column' );
		} );
	}

	if ( typeof window.InlineShortcodeViewContainer != 'undefined' ) {
		window.InlineShortcodeView_mpc_pricing_box = window.InlineShortcodeViewContainer.extend( {
			events: {
				'click > .vc_controls .vc_element .vc_control-btn-delete': 'destroy',
				'click > .vc_controls .vc_element .vc_control-btn-edit': 'edit',
				'click > .vc_controls .vc_element .vc_control-btn-clone': 'clone',
				'click > .vc_controls .vc_element .vc_control-btn-prepend': 'prependElement',
				'click > .vc_controls .vc_control-btn-append': 'appendElement',
				'click > .vc_empty-element': 'appendElement',
				'mouseenter': 'resetActive',
				'mouseleave': 'holdActive',
				'click > .mpc-carousel__wrapper .mpcslick-prev .mpc-nav__icon' : 'prevSlide',
				'click > .mpc-carousel__wrapper .mpcslick-next .mpc-nav__icon' : 'nextSlide'
			},
			rendered: function() {
				var $pricing_box = this.$el.find( '.mpc-pricing-box' ),
				    $navigation = $pricing_box.siblings( '.mpc-navigation' );

				$pricing_box.addClass( 'mpc-waypoint--init' );

				_mpc_vars.$body.trigger( 'mpc.icon-loaded', [ $pricing_box ] );
				_mpc_vars.$body.trigger( 'mpc.font-loaded', [ $pricing_box ] );
				_mpc_vars.$body.trigger( 'mpc.navigation-loaded', [ $navigation ] );
				_mpc_vars.$body.trigger( 'mpc.inited', [ $pricing_box, $navigation ] );

				setTimeout( function() {
					init_shortcode( $pricing_box );
					init_frontend( $pricing_box );
				}, 250 );

				window.InlineShortcodeView_mpc_pricing_box.__super__.rendered.call( this );
			},
			beforeUpdate: function() {
				this.$el.find( '.mpc-pricing-box__wrapper' ).mpcslick( 'unslick' );
			},
			prevSlide: function() {
				this.$el.find( '.mpc-pricing-box__wrapper' ).mpcslick( 'slickPrev' );
			},
			nextSlide: function() {
				this.$el.find( '.mpc-pricing-box__wrapper' ).mpcslick( 'slickNext' );
			}
		} );
	}
} )( jQuery );
