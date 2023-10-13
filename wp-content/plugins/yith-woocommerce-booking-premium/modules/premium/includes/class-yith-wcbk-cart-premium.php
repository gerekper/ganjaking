<?php
/**
 * Class YITH_WCBK_Cart_Premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cart_Premium' ) ) {
	/**
	 * YITH_WCBK_Cart_Premium class.
	 */
	class YITH_WCBK_Cart_Premium extends YITH_WCBK_Cart {

		/**
		 * The constructor.
		 */
		protected function __construct() {
			parent::__construct();

			if ( 'yes' === get_option( 'yith-wcbk-redirect-to-checkout-after-booking', 'no' ) ) {
				add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'checkout_redirect_after_add_to_cart' ), 10, 2 );
				add_filter( 'wc_add_to_cart_message_html', array( $this, 'empty_added_to_cart_message' ), 10, 2 );
			}

			if ( 'yes' === get_option( 'yith-wcbk-show-booking-of-in-cart-and-checkout', 'no' ) ) {
				add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 10, 2 );
			}
		}

		/**
		 * Redirect to the "Checkout" page after adding the product to the cart.
		 *
		 * @param string           $url     The redirect URL.
		 * @param WC_Product|false $product The added-to-cart product.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function checkout_redirect_after_add_to_cart( $url, $product = false ) {
			if ( $product && is_a( $product, 'WC_Product' ) && yith_wcbk_is_booking_product( $product ) ) {
				$url = wc_get_checkout_url();
			}

			return $url;
		}

		/**
		 * Empty added-to-cart message to prevent showing it for Booking products if "Redirect users to checkout" option is enabled.
		 *
		 * @param string $message  The HTML message.
		 * @param array  $products Key-value array of product ID-quantity.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function empty_added_to_cart_message( $message, $products ) {
			if ( count( $products ) === 1 ) {
				$product_id = current( array_keys( $products ) );
				if ( yith_wcbk_is_booking_product( $product_id ) ) {
					$message = '';
				}
			}

			return $message;
		}

		/**
		 * Filter cart item name
		 *
		 * @param string $name      The product name shown in cart.
		 * @param array  $cart_item The cart item.
		 *
		 * @return string
		 */
		public function cart_item_name( $name, $cart_item ) {
			/**
			 * The Booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$product = $cart_item['data'];

			if ( is_a( $product, 'WC_Product' ) && yith_wcbk_is_booking_product( $product ) ) {
				$name = yith_wcbk_product_booking_of_name( $name );
			}

			return $name;
		}

	}
}
