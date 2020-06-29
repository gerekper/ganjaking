<?php
/**
 * Compatibility class with Dynamic Pricing and Discounts
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts
		 * @since 1.0.5
		 */
		protected static $_instance;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts
		 * @since 1.0.5
		 */
		public function __construct() {
			add_filter( 'yith_wcdp_process_cart_item_product_change', '__return_false', 10 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'process_cart_item_product_change' ), 100 );
			add_action( 'woocommerce_cart_loaded_from_session', array(
				$this,
				'process_cart_item_product_change'
			), 100 );
		}

		/**
		 * Execute deposit calculations after Dynamic discounts
		 *
		 * @return void
		 * @since 1.0.5
		 */
		public function process_cart_item_product_change() {
			$cart = WC()->cart;

			if ( ! $cart->is_empty() ) {
				foreach ( $cart->cart_contents as $cart_item_key => & $cart_item ) {
					if ( ! isset( $cart_item['deposit'] ) || ! $cart_item['deposit'] ) {
						continue;
					}

					/**
					 * @var $product \WC_Product
					 */
					$product = $cart_item['data'];

					$product_id = ( 'variation' != $product->get_type() ) ? yit_get_prop( $product, 'id' ) : $product->get_parent_id();

					$variation_id    = isset( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : false;
					$deposit_value   = apply_filters( 'yith_wcdp_deposit_value', YITH_WCDP_Premium()->get_deposit( $product_id, $product->get_price(), 'edit', false, $variation_id ), $product_id, $variation_id, $cart_item );
					$deposit_balance = apply_filters( 'yith_wcdp_deposit_balance', max( $product->get_price() - $deposit_value, 0 ), $product_id, $variation_id, $cart_item );
					yit_set_prop( $cart_item['data'], 'price', $deposit_value );
					yit_set_prop( $cart_item['data'], 'yith_wcdp_deposit', true );

					if ( apply_filters( 'yith_wcdp_virtual_on_deposit', true, null ) ) {
						yit_set_prop( $cart_item['data'], 'virtual', 'yes' );
					}

					$cart_item['deposit_value']   = $deposit_value;
					$cart_item['deposit_balance'] = $deposit_balance;

				}
			}
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts
		 * @since 1.0.5
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


	}
}

/**
 * Unique access to instance of YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts class
 *
 * @return \YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts
 * @since 1.0.0
 */
function YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts() {
	return YITH_WCDP_YITH_Dynamic_Pricing_And_Discounts::get_instance();
}

