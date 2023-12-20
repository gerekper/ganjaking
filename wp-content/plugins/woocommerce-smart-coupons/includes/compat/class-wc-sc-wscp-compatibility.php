<?php
/**
 * Compatibility file for WooCommerce Side Cart Premium
 *
 * @author      StoreApps
 * @since       8.11.0
 * @version     1.0.0
 *
 * @package     woocommerce-smart-coupons/includes/compat/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_WSCP_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce Side Cart Premium
	 */
	class WC_SC_WSCP_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_WSCP_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Get single instance of WC_SC_WSCP_Compatibility
		 *
		 * @return WC_SC_WSCP_Compatibility Singleton object of WC_SC_WSCP_Compatibility
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action( 'wp_loaded', array( $this, 'hooks_for_compatibility' ) );
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}
		}

		/**
		 * Add compatibility related functionality
		 */
		public function hooks_for_compatibility() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-side-cart-premium/xoo-wsc-main.php' ) ) {

				$apply_before_tax = get_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );
				if ( ! class_exists( 'WC_SC_Apply_Before_Tax' ) && 'yes' === $apply_before_tax ) {
					include_once plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'includes/class-wc-sc-apply-before-tax.php';
				}

				if ( class_exists( 'WC_SC_Apply_Before_Tax' ) ) {
					$wc_sc_apply_before_tax_instance = WC_SC_Apply_Before_Tax::get_instance();
					add_action( 'woocommerce_ajax_added_to_cart', array( $wc_sc_apply_before_tax_instance, 'cart_calculate_discount_amount' ), 10 );
				}
				if ( class_exists( 'WC_SC_URL_Coupon' ) ) {
					$wc_sc_url_coupon_instance = WC_SC_URL_Coupon::get_instance();
					add_action( 'woocommerce_ajax_added_to_cart', array( $wc_sc_url_coupon_instance, 'apply_coupon_from_session' ) );
				}
			}
		}

	}

}

WC_SC_WSCP_Compatibility::get_instance();
