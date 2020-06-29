<?php
/**
 * Frontend Class for WC Bundles.
 *
 * @package WooCommerce Waitlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Bundle' ) ) {
	/**
	 * Loads up the waitlist for bundled products
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Bundle {

		/**
		 * Current product Bundle
		 *
		 * @var $product WC_Product_Bundle
		 */
		public static $product;

		/**
		 * Load up hooks if product is out of stock
		 *
		 * @param object $product WC_Product_Bundle.
		 *
		 * @todo hook in close to bundle button and hide until required when variations are involved (JS)
		 */
		public function init( $product ) {
			self::$product = $product;
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_bundle_script_data' ) );
		}

		/**
		 * Load required data to modify bundle HTML with JS
		 *
		 * @return void
		 */
		public function enqueue_bundle_script_data() {
			$data = array(
				'waitlist_html'     => wcwl_get_waitlist_fields( self::$product->get_id() ),
				'backorder_allowed' => WooCommerce_Waitlist_Plugin::enable_waitlist_for_backorder_products( self::$product->get_id() ),
			);
			wp_localize_script( 'wcwl_frontend', 'wcwl_bundle_data', $data );
		}
	}
}
