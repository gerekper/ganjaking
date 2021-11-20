<?php
/**
 * Plugin Name: WooCommerce Products Compare
 * Plugin URI: https://woocommerce.com/products/woocommerce-products-compare/
 * Description: Have your customers to compare similar products side by side.
 * Version: 1.0.25
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-products-compare
 * Domain Path: /languages
 * Tested up to: 5.8
 * WC tested up to: 5.9
 * WC requires at least: 2.6
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 853117:c3ba0a4a3199a0cc7a6112eb24414548
 *
 * @package WC_Products_Compare
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.0.19
 */
function woocommerce_products_compare_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Products Compare requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-products-compare' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Products_Compare' ) ) :

	define( 'WC_PRODUCTS_COMPARE_VERSION', '1.0.25' ); // WRCS: DEFINED_VERSION.

	/**
	 * Main class.
	 */
	class WC_Products_Compare {

		/**
		 * Init.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function __construct() {
			register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

			require_once dirname( __FILE__ ) . '/includes/class-wc-products-compare-frontend.php';

			add_action( 'widgets_init', array( $this, 'register_widget' ) );

			if ( is_admin() ) {
				require_once dirname( __FILE__ ) . '/includes/class-wc-products-compare-admin.php';
			}

			return true;
		}

		/**
		 * Run on deactivate
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function deactivate() {

			// Set the flag back to false so it can be reflushed on activate.
			update_option( 'wc_products_compare_endpoint_set', false );

			flush_rewrite_rules();

			return true;
		}

		/**
		 * Registers the widget
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function register_widget() {
			require_once dirname( __FILE__ ) . '/includes/class-wc-products-compare-widget.php';

			register_widget( 'WC_Products_Compare_Widget' );

			return true;
		}

		/**
		 * Checks to make sure item is a product
		 *
		 * @since 1.0.4
		 * @version 1.0.4
		 * @param object $product Product object.
		 * @return bool
		 */
		public static function is_product( $product ) {
			if ( $product && 'product' === get_post_type( $product->get_id() ) ) {
				return true;
			}

			return false;
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_products_compare_init' );

/**
 * Init function.
 *
 * @since 1.0.0
 * @since 1.0.19 Load text domain and check for WC exists.
 * @return bool
 */
function woocommerce_products_compare_init() {
	load_plugin_textdomain( 'woocommerce-products-compare', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_products_compare_missing_wc_notice' );
		return;
	}

	new WC_Products_Compare();
}
