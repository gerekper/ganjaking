<?php
/**
 * Plugin Name: WooCommerce Mix and Match Products
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Allow customers to choose products in any combination to fill a "container" of a specific size.
 * Version: 2.4.4
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Woo: 853021:e59883891b7bcd535025486721e4c09f
 * 
 * GitHub Plugin URI: https://github.com/kathyisawesome/woocommerce-mix-and-match-products/
 * Primary Branch: trunk
 * Release Asset: true
 *
 * WC requires at least: 3.6.0
 * WC tested up to: 7.3.0
 * Requires at least: 4.7.0
 * Requires PHP: 7.2
 *
 * Text Domain: woocommerce-mix-and-match-products
 * Domain Path: /languages
 *
 * @package WooCommerce Mix and Match
 *
 * Copyright: © 2015-2023 Kathy Darling and Manos Psychogyiopoulos
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WC_MNM_PLUGIN_FILE' ) ) {
	define( 'WC_MNM_PLUGIN_FILE', __FILE__ );
}
// Required WooCommerce version.
if ( ! defined( 'WC_MNM_REQUIRED_WOO' ) ) {
	define( 'WC_MNM_REQUIRED_WOO', '3.6.0' );
}

// Make sure plugin doesn't load twice.
if ( ! function_exists( 'wc_mnm_init' ) ) {

	/**
	 * Load plugin class, if dependencies are met.
	 *
	 * @since 1.10.0
	 */
	function wc_mnm_init() {

		// WC version check.
		if ( ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, WC_MNM_REQUIRED_WOO, '<' ) ) ) {
			require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );
			$msg = sprintf(
				// Translators: %s minimum required WooCommerce version number.
				__( '<strong>WooCommerce Mix and Match is inactive.</strong> The WooCommerce plugin must be active and at least version %s for Mix and Match to function. Please upgrade or activate WooCommerce.', 'woocommerce-mix-and-match-products' ),
				WC_MNM_REQUIRED_WOO
			);

			WC_MNM_Admin_Notices::add_custom_notice( 'min-wc', $msg );

			return false;
		}

		// Dependencies are met so launch plugin.
		include_once dirname( __FILE__ ) . '/includes/class-wc-mix-and-match.php';
		WC_Mix_and_Match::instance();

	}
	add_action( 'plugins_loaded', 'wc_mnm_init', 5 );

}
