<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @author      StoreApps
 * @since       4.0.0
 * @version     1.0
 *
 * @package     woocommerce-smart-coupons/includes/blocks
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_SC_Gutenberg_Coupon_Block' ) ) {

	/**
	 * Class for handling Smart Coupons Shortcode
	 */
	class WC_SC_Gutenberg_Coupon_Block {

		/**
		 * Variable to hold instance of WC_SC_Gutenberg_Coupon_Block
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		private function __construct() {

			global $wp_version;
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'gutenberg/gutenberg.php' ) || version_compare( $wp_version, '5.0', '>=' ) ) {
				add_action( 'init', array( $this, 'gutenberg_coupon_block_init' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'gutenberg_coupon_add_inline_css' ) );
			}

		}

		/**
		 * Get single instance of WC_SC_Gutenberg_Coupon_Block
		 *
		 * @return WC_SC_Gutenberg_Coupon_Block Singleton object of WC_SC_Gutenberg_Coupon_Block
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
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
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
		 * Registers all block assets so that they can be enqueued through Gutenberg in
		 * the corresponding context.
		 *
		 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
		 */
		public function gutenberg_coupon_block_init() {

			// Skip block registration if Gutenberg is not enabled/merged.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}
			$dir = dirname( __FILE__ );

			$index_js = 'sc-gutenberg-block.js';
			wp_register_script(
				'coupon-block-editor-js',
				plugins_url( $index_js, __FILE__ ),
				array(
					'wp-blocks',
					'wp-i18n',
					'wp-element',
					'wp-components',
				),
				filemtime( "$dir/$index_js" ),
				true
			);

			if ( shortcode_exists( 'smart_coupons' ) ) {
				register_block_type(
					'woocommerce-smart-coupons/coupon',
					array(
						'editor_script'   => 'coupon-block-editor-js',
						'attributes'      => array(
							'coupon_code' => array( 'type' => 'string' ),
						),
						'render_callback' => array( WC_SC_Shortcode::get_instance(), 'execute_smart_coupons_shortcode' ),
					)
				);
			}

		}

		/**
		 * Our componenet is referring to smart-coupon.css as main css file.
		 * We cannot add register/enqueue a new CSS file to render coupon design in Gutenberg.
		 * And hence we are adding necessary files via wp_add_inline_style.
		 */
		public function gutenberg_coupon_add_inline_css() {

			if ( ! wp_style_is( 'smart-coupon' ) ) {
				wp_enqueue_style( 'smart-coupon' );
			}

			$coupon_style_attributes       = WC_Smart_Coupons::get_instance()->get_coupon_style_attributes();
			$gutenberg_active_coupon_style = '
				.gb-active-coupon {
					' . $coupon_style_attributes . '
				}
			';

			// Add the above custom CSS via wp_add_inline_style.
			wp_add_inline_style( 'smart-coupon', $gutenberg_active_coupon_style );

		}

	}

}

WC_SC_Gutenberg_Coupon_Block::get_instance();
