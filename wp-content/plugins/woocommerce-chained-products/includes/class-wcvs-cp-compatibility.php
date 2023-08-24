<?php
/**
 * Compatibility file for WooCommerce Variation Swatches and Photos
 *
 * @since       2.9.3
 * @version     1.0.2
 * @package     WooCommerce Chained Products
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WCVS_CP_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce Variation Swatches and Photos
	 */
	class WCVS_CP_Compatibility {

		/**
		 * Variable to hold instance of WCVS_CP_Compatibility
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-variation-swatches-and-photos/woocommerce-swatches.php' ) ) {
				add_filter( 'woocommerce_hide_invisible_variations', array( $this, 'hide_chained_variations' ), 10, 3 );
				add_filter( 'woocommerce_variation_is_visible', array( $this, 'is_variation_visible' ), 10, 4 );
			}

		}

		/**
		 * Get single instance of WCVS_CP_Compatibility
		 *
		 * @return WCVS_CP_Compatibility Singleton object of WCVS_CP_Compatibility
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
		public function __call( $function_name = '', $arguments = array() ) {

			global $wc_cp;

			if ( ! is_callable( array( $wc_cp, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $wc_cp, $function_name ), $arguments );
			} else {
				return call_user_func( array( $wc_cp, $function_name ) );
			}
		}

		/**
		 * Hide parent chained variation product if child product is out of stock
		 *
		 * @param bool                 $hide flag to show or hide.
		 * @param int                  $product_id main product id.
		 * @param WC_Product_Variation $variation variation product.
		 * @return bool $hide flag to show or hide.
		 */
		public function hide_chained_variations( $hide, $product_id, $variation ) {

			if ( ! $hide && $variation instanceof WC_Product_Variation ) {
				$availability = $variation->get_availability();
				if ( 'out-of-stock' === $availability['class'] ) {
					$hide = true;
				}
			}

			return $hide;

		}

		/**
		 * Hide parent chained variation product if child product is out of stock
		 *
		 * @param bool                 $visible flag to show or hide.
		 * @param int                  $variation_id variation product id.
		 * @param int                  $parent_id main product id.
		 * @param WC_Product_Variation $variation variation product.
		 * @return bool $visible flag to show or hide.
		 */
		public function is_variation_visible( $visible, $variation_id, $parent_id, $variation ) {

			if ( $visible && $variation instanceof WC_Product_Variation ) {

				$availability = $variation->get_availability();
				if ( 'out-of-stock' === $availability['class'] ) {
					$visible = false;
				}
			}

			return $visible;

		}

	}

}

WCVS_CP_Compatibility::get_instance();
