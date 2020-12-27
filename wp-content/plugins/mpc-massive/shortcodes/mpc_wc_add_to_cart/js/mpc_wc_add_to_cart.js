/*----------------------------------------------------------------------------*\
	ADD TO CART SHORTCODE
\*----------------------------------------------------------------------------*/
( function( $ ) {
	"use strict";

	function adjust_width( $button ) {
		var	$parent = $button.parent( '.mpc-wc-add_to_cart-wrap' ),
			   $title_hover = $button.find( '.mpc-atc__title-hover' ),
			   _css = { left: 0, top: 0 }, _css_hover = {};

		_css.width = $button.width();
		_css_hover.width = $title_hover.outerWidth( true );
		_css.height = $button.height();
		_css_hover.height = $title_hover.outerHeight( true );

		if( _css.width + 10 > _css_hover.width && _css_hover.width < _css.width - 10 ) { // Tolerance to fix skipping content
			_css_hover.width = _css.width;
		}
		if( _css.height + 10 > _css_hover.height && _css_hover.height < _css.height - 10 ) { // Tolerance to fix skipping content
			_css_hover.height = _css.height;
		}

		if( typeof _css_hover.width !== typeof undefined || typeof _css_hover.height !== typeof undefined ) {
			$button.css( _css );
			$parent.css( _css );

			$button.attr( 'data-css', JSON.stringify( _css ) )
				.attr( 'data-css-hover', JSON.stringify( _css_hover ) );

			$parent.on( 'mouseenter', function() {
				var $this = $( this ).children( '.mpc-wc-add_to_cart' ),
					_default = $this.data( 'css' ),
					_hover  = $this.data( 'css-hover' );

				_hover.left = ( _default.width - _hover.width ) * 0.5;
				_hover.top = ( _default.height - _hover.height ) * 0.5;

				$this.css( _hover );
			} ).on( 'mouseleave', function() {
				var $this = $( this ).children( '.mpc-wc-add_to_cart' );

				$this.css( $this.data( 'css' ) );
			});
		}
	}

	function add_to_cart_call( _ev, $button ) {
		var cart_data = $button.data( 'cart' ),
		    $notices = $button.find( '.mpc-atc__notices' );

		if( cart_data && !$button.is( '.mpc-disabled' ) ) {
			_ev.preventDefault();

			$button.addClass( 'mpc-disabled' );
			$button.attr( 'data-notice', 'show:loader' );

			$.post(
				_mpc_vars.ajax_url,
				{
					action: 'mpc_wc_add_to_cart',
					product_id: cart_data.product_id,
					variation_id: cart_data.variation_id,
					dataType: 'json'
				},
				function( _response ) {
					if( _response ) {
						$button.attr( 'data-notice', 'show:success' );

						$( document.body ).trigger( 'added_to_cart', [ _response.fragments, _response.cart_hash, null ] );
					} else {
						$button.attr( 'data-notice', 'show:error' );

						setTimeout( function() {
							$button.removeClass( 'mpc-disabled' )
									.removeAttr( 'data-notice' );
						}, 2000 );
					}
				}
			);
		} else if( $button.is( '.mpc-disabled' ) && $notices.attr( 'data-notice' ) != '' ) {
			$button.removeClass( 'mpc-disabled' )
					.removeAttr( 'data-notice' );
		}
	}

	function init_shortcode( $button ) {
		if( $button.is( '.mpc-auto-size' ) && !$button.is( '.mpc-display--block' ) ) {
			adjust_width( $button );
		}

		$button.trigger( 'mpc.inited' );
	}

	if ( typeof window.InlineShortcodeView != 'undefined' ) {
		var $body = $( 'body' );

		window.InlineShortcodeView_mpc_wc_add_to_cart = window.InlineShortcodeView.extend( {
			rendered: function() {
				var $button = this.$el.find( '.mpc-button' );

				$button.addClass( 'mpc-waypoint--init' );

				$body.trigger( 'mpc.icon-loaded', [ $button ] );
				$body.trigger( 'mpc.font-loaded', [ $button ] );
				$body.trigger( 'mpc.inited', [ $button ] );

				init_shortcode( $button );

				window.InlineShortcodeView_mpc_wc_add_to_cart.__super__.rendered.call( this );
			}
		} );
	}

	var $buttons = $( '.mpc-wc-add_to_cart' );

	$buttons.each( function() {
		var $button = $( this );

		$button.one( 'mpc.init', function () {
			init_shortcode( $button );
		} );
	} );

	$( document ).on( 'click', '.mpc-wc-add_to_cart', function( _ev ){
		add_to_cart_call( _ev, $( this ) );
	} );
} )( jQuery );
