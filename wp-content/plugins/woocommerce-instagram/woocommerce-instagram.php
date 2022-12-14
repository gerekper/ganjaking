<?php
/**
 * Plugin Name: WooCommerce Instagram
 * Plugin URI: https://woocommerce.com/products/woocommerce-instagram/
 * Description: Connect your store with Instagram. Upload your product catalog to Instagram and showcase how your customers are using them.
 * Version: 4.3.3
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.1
 * Text Domain: woocommerce-instagram
 * Domain Path: /languages/
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.2
 * Woo: 260061:ecaa2080668997daf396b8f8a50d891a
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Instagram
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'ecaa2080668997daf396b8f8a50d891a', '260061' );

/**
 * Plugin requirements.
 */
if ( ! class_exists( 'WC_Instagram_Requirements', false ) ) {
	require_once dirname( __FILE__ ) . '/includes/class-wc-instagram-requirements.php';
}

if ( ! WC_Instagram_Requirements::are_satisfied() ) {
	return;
}

// Define WC_INSTAGRAM_FILE constant.
if ( ! defined( 'WC_INSTAGRAM_FILE' ) ) {
	define( 'WC_INSTAGRAM_FILE', __FILE__ );
}

// Include the main plugin class.
if ( ! class_exists( 'WC_Instagram' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-instagram.php';
}

/**
 * Main instance of the plugin.
 *
 * Returns the main instance of the plugin to prevent the need to use globals.
 *
 * @since  2.0.0
 * @return WC_Instagram
 */
function wc_instagram() {
	return WC_Instagram::instance();
}

wc_instagram();
