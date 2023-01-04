<?php
/**
 * Plugin Name: WooCommerce Free Gift Coupons
 * Plugin URI: http://www.woocommerce.com/products/free-gift-coupons/
 * Description: Add a free product to the cart when a coupon is entered
 * Version: 3.4.0
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com
 * Woo: 414577:e1c4570bcc412b338635734be0536062
 * Requires at least: 4.4
 * Tested up to: 6.1.0
 * WC requires at least: 3.1.0
 * WC tested up to: 7.3.0
 *
 * Text Domain: wc_free_gift_coupons
 * Domain Path: /languages/
 *
 * @package WooCommerce Free Gift Coupons
 * @package Core
 *
 * Copyright: © 2012 Kathy Darling.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Make sure it doesn't create twice.
if ( ! function_exists( 'wc_free_gift_coupons_init') ) {
	/**
	 * Boot up the plugin
	 *
	 * @since   1.2.0
	 */
	function wc_free_gift_coupons_init() {

		// Required WooCommerce version.
		$required_woo = '3.1.0';

		// Required PHP version.
		$required_php = '5.6.20';

		// Check we're running the required version of WC.
		if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, $required_woo, '<' ) ) {
			/* translators: %1$s: Opening link tag, %2$s: Closing link tag, %3$s: Required version of WooCommerce */
			$notice = sprintf( __( '<strong>WooCommerce Free Gift Coupons is inactive.</strong> The %1$sWooCommerce plugin%2$s must be active and at least version %3$s for Free Gift Coupons to function. Please upgrade or activate WooCommerce.', 'wc_free_gift_coupons' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', $required_woo );
			include_once  'includes/admin/class-wc-free-gift-coupons-admin-notices.php' ;
			WC_Free_Gift_Coupons_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		// PHP version check.
		if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), $required_php, '<' ) ) {
			/* translators: %1$s: Opening link tag, %2$s: Closing link tag, %3$s: Required version of PHP */
			$notice = sprintf( __( 'WooCommerce Free Gift Coupons requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'wc_free_gift_coupons' ), $required_php, 'https://docs.woocommerce.com/document/how-to-update-your-php-version/' );
			include_once  'includes/admin/class-wc-free-gift-coupons-admin-notices.php' ;
			WC_Free_Gift_Coupons_Admin_Notices::add_notice( $notice, 'error' );
			return false;
		}

		if ( ! defined( 'WC_FGC_PLUGIN_NAME' ) ) {
			define( 'WC_FGC_PLUGIN_NAME', plugin_basename( __FILE__ ) );
		}

		if ( ! defined( 'WC_FGC_PLUGIN_FILE' ) ) {
			define( 'WC_FGC_PLUGIN_FILE', __FILE__ );
		}
	
		require_once  'includes/legacy/class-wc-free-gift-coupons-legacy.php' ;
		require_once  'includes/class-wc-free-gift-coupons.php' ;
		WC_Free_Gift_Coupons::init();
	}
}

add_action( 'plugins_loaded', 'wc_free_gift_coupons_init' );
