/**
 * Cart scripts.
 *
 * @package WC_Store_Credit/Assets/Js/Frontend
 * @since   4.2.0
 */

(function( $ ) {

	'use strict';

	$(function() {
		var wc_store_cart = {

			init: function() {
				this.bindEvents();
			},

			bindEvents: function() {
				var that = this;

				$( document.body )
					.on( 'click', '.show-store-credit-coupons', function( event ) {
						event.preventDefault();
						that.toogleCoupons();
					})
					.on( 'click', '.wc-store-credit-cart-coupon', function( event ) {
						event.preventDefault();
						that.applyCoupon( $( this ).data( 'couponCode' ) );
						that.toogleCoupons();
					})
					.on( 'wc_fragments_loaded wc_fragments_refreshed updated_checkout', function() {
						// Don't use the cached version of the cart fragment on page load.
						$( '.wc-store-credit-cart-coupons-container' ).addClass( 'refresh' );
					});
			},

			toogleCoupons: function() {
				$( '.wc-store-credit-cart-coupons' ).slideToggle( 400 );
			},

			applyCoupon: function( code ) {
				$( 'input[name="coupon_code"]' ).val( code );
				$( 'button[name="apply_coupon"]' ).trigger( 'click' );
			}
		};

		wc_store_cart.init();
	});
})( jQuery );
