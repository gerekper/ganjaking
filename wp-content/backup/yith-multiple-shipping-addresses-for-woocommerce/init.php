<?php
/*
Plugin Name: YITH Multiple Shipping Addresses for WooCommerce
Plugin URI: https://yithemes.com/themes/plugins/yith-multiple-addresses-shipping-for-woocommerce/
Description: <code><strong>YITH Multiple Shipping Addresses for WooCommerce</strong></code> allows your customer to select the delivery of individual products to completely different addresses. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce on <strong>YITH</strong></a>.
Version: 1.1.2
Author: YITH
Author URI: https://yithemes.com/
Text Domain: yith-multiple-shipping-addresses-for-woocommerce
Domain Path: /languages/
WC requires at least: 3.0.0
WC tested up to: 4.2
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* === DEFINE === */
! defined( 'YITH_WCMAS_VERSION' )          && define( 'YITH_WCMAS_VERSION', '1.1.2' );
! defined( 'YITH_WCMAS_INIT' )             && define( 'YITH_WCMAS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCMAS_SLUG' )             && define( 'YITH_WCMAS_SLUG', 'yith-multiple-shipping-addresses-for-woocommerce' );
! defined( 'YITH_WCMAS_SECRETKEY' )        && define( 'YITH_WCMAS_SECRETKEY', '12345' );
! defined( 'YITH_WCMAS_FILE' )             && define( 'YITH_WCMAS_FILE', __FILE__ );
! defined( 'YITH_WCMAS_PATH' )             && define( 'YITH_WCMAS_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCMAS_URL' )              && define( 'YITH_WCMAS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCMAS_ASSETS_URL' )       && define( 'YITH_WCMAS_ASSETS_URL', YITH_WCMAS_URL . 'assets/' );
! defined( 'YITH_WCMAS_ASSETS_JS_URL' )    && define( 'YITH_WCMAS_ASSETS_JS_URL', YITH_WCMAS_URL . 'assets/js/' );
! defined( 'YITH_WCMAS_TEMPLATE_PATH' )    && define( 'YITH_WCMAS_TEMPLATE_PATH', YITH_WCMAS_PATH . 'templates/' );
! defined( 'YITH_WCMAS_WC_TEMPLATE_PATH' ) && define( 'YITH_WCMAS_WC_TEMPLATE_PATH', YITH_WCMAS_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCMAS_OPTIONS_PATH' )     && define( 'YITH_WCMAS_OPTIONS_PATH', YITH_WCMAS_PATH . 'plugin-options' );
! defined( 'YITH_WCMAS_PREMIUM' )          && define( 'YITH_WCMAS_PREMIUM', '1' );

! defined( 'YITH_WCMAS_BILLING_ADDRESS_ID' )          && define( 'YITH_WCMAS_BILLING_ADDRESS_ID', 'billing_address' );
! defined( 'YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID' ) && define( 'YITH_WCMAS_DEFAULT_SHIPPING_ADDRESS_ID', 'default_shipping_address' );

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCMAS_PATH . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCMAS_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCMAS_PATH );

if ( ! function_exists( 'yith_wcmas_install_woocommerce_admin_notice' ) ) {

	function yith_wcmas_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'YITH Multiple Shipping Addresses for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_wcmas_init' ) ) {
	/**
	 * Start the plugin
	 */
	function yith_wcmas_init() {
		/**
		 * Load text domain
		 */
		load_plugin_textdomain( 'yith-multiple-shipping-addresses-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

		if ( ! function_exists( 'YITH_Multiple_Addresses_Shipping' ) ) {
			/**
			 * Unique access to instance of YITH_Multiple_Addresses_Shipping class
			 *
			 * @return YITH_Multiple_Addresses_Shipping
			 * @since 1.0.0
			 */
			function YITH_Multiple_Addresses_Shipping() {
				if ( defined( 'YITH_WCMAS_INIT' ) && file_exists( YITH_WCMAS_PATH . 'includes/class.yith-multiple-addresses-shipping.php'  ) ) {
					require_once( YITH_WCMAS_PATH . 'includes/class.yith-multiple-addresses-shipping.php' );
					return YITH_Multiple_Addresses_Shipping::instance();
				}
			}
		}
		// Let's start the game!
		YITH_Multiple_Addresses_Shipping();
	}
}

/* Start the plugin on plugins_loaded */
if ( ! function_exists( 'yith_wcmas_install' ) ) {
	/**
	 * Install the plugin
	 */
	function yith_wcmas_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_wcmas_install_woocommerce_admin_notice' );
		}
		else {
			do_action( 'yith_wcmas_init' );
		}
	}
}

add_action( 'plugins_loaded', 'yith_wcmas_install', 11 );
add_action( 'yith_wcmas_init', 'yith_wcmas_init' );


