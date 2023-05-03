<?php
/**
 * Compatibility file for WooCommerce Points and Rewards
 *
 * @author      StoreApps
 * @since       7.8.0
 * @version     1.0.0
 *
 * @package     woocommerce-smart-coupons/includes/compat/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_PNR_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce Points and Rewards
	 */
	class WC_SC_PNR_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_PNR_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'wp_loaded', array( $this, 'hooks_for_compatibility' ) );
		}

		/**
		 * Add compatibility related functionality
		 */
		public function hooks_for_compatibility() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php' ) ) {
				add_action( 'wc_sc_before_auto_apply_coupons', array( $this, 'before_auto_apply_coupons' ) );
			}
		}

		/**
		 * Get single instance of WC_SC_PNR_Compatibility
		 *
		 * @return WC_SC_PNR_Compatibility Singleton object of WC_SC_PNR_Compatibility
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
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
		 * Function to execute before auto apply coupons
		 *
		 * @param array $args Additional arguments.
		 */
		public function before_auto_apply_coupons( $args = array() ) {
			if ( empty( $args['current_filter'] ) || 'woocommerce_account_content' !== $args['current_filter'] ) {
				return;
			}
			if ( ! has_filter( 'wc_points_rewards_should_render_earn_points_message', '__return_false' ) ) {
				add_filter( 'wc_points_rewards_should_render_earn_points_message', '__return_false', 100 );
			}
			if ( ! has_filter( 'wc_points_rewards_should_render_redeem_points_message', '__return_false' ) ) {
				add_filter( 'wc_points_rewards_should_render_redeem_points_message', '__return_false', 100 );
			}
		}

	}

}

WC_SC_PNR_Compatibility::get_instance();
