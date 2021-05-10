<?php
/**
 * Frontend Class for Simple Products.
 *
 * @package WooCommerce Waitlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Simple' ) ) {
	/**
	 * Loads up the waitlist for simple products
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Simple {
		/**
		 * Current product ID
		 *
		 * @var int
		 */
		public static $product_id;
		/**
		 * Load up hooks if product is out of stock
		 *
		 * @param WC_Product $product Current product object.
		 */
		public function init( WC_Product $product ) {
			self::$product_id = $product->get_id();
			add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'output_waitlist_elements' ), 35 );
		}
		/**
		 * Output waitlist elements via waitlist-single template
		 */
		public static function output_waitlist_elements() {
			echo wcwl_get_waitlist_fields( self::$product_id );
		}
	}
}
