<?php
/**
 * Plugin Name: WooCommerce Mix and Match Products
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Allow customers to choose products in any combination to fill a "container" of a specific size.
 * Version: 1.12.0
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Woo: 853021:e59883891b7bcd535025486721e4c09f
 * WC requires at least: 3.1.0
 * WC tested up to: 6.1.0
 *
 * Text Domain: woocommerce-mix-and-match-products
 * Domain Path: /languages
 *
 * @package WooCommerce Mix and Match
 *
 * Copyright: © 2015 Kathy Darling and Manos Psychogyiopoulos
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WC_MNM_PLUGIN_FILE' ) ) {
	define( 'WC_MNM_PLUGIN_FILE', __FILE__ );
}


/**
 * Load plugin class, if dependencies are met.
 *
 * @since 1.10.0
 */
function wc_mnm_init() {

	// Required WooCommerce version.
	$required_woo = '3.1.0';

	// Required PHP version.
	$required_php = '5.6.20';

	// WC version check.
	if ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, $required_woo, '<' ) ) {
		$notice = sprintf(
			// Translators: %1$s opening <a> tag for link. %2$s closing </a> tag. %3$s minimum required WooCommerce version number.
			__( '<strong>WooCommerce Mix and Match Products is inactive.</strong> The %1$sWooCommerce plugin%2$s must be active and at least version %3$s for Mix and Match to function. Please upgrade or activate WooCommerce.', 'woocommerce-mix-and-match-products' ),
			'<a href="http://wordpress.org/extend/plugins/woocommerce/">',
			'</a>',
			$required_woo
		);
		require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );
		WC_MNM_Admin_Notices::add_notice( $notice, 'error' );
		return false;
	}

	// PHP version check.
	if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), $required_php, '<' ) ) {
		$notice = sprintf(
			// Translators: %1$s link to documentation. %2$s minimum required PHP version number.
			__( 'WooCommerce Mix and Match Products requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-mix-and-match-products' ),
			$required_php,
			'https://docs.woocommerce.com/document/how-to-update-your-php-version/'
		);
		require_once( 'includes/admin/class-wc-mnm-admin-notices.php' );
		WC_MNM_Admin_Notices::add_notice( $notice, 'error' );
		return false;
	}

	// Dependencies are met so launch plugin.
	include_once dirname( __FILE__ ) . '/includes/class-wc-mix-and-match.php';
	WC_Mix_and_Match::instance();

}
add_action( 'plugins_loaded', 'wc_mnm_init', 5 );
