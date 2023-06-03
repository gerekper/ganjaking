<?php
/**
 * Plugin Name: WooCommerce Currency Converter
 * Plugin URI: https://woocommerce.com/products/currency-converter-widget/
 * Description: Provides a currency selection widget for displaying product prices and totals in different currencies. Conversions are estimated based on data from the Open Exchange Rates API with no guarantee whatsoever of accuracy.
 * Version: 2.0.2
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Text Domain: woocommerce-currency-converter-widget
 * Domain Path: /languages
 * Requires PHP: 5.6
 * Requires at least: 4.9
 * Tested up to: 6.2
 *
 * WC requires at least: 3.7
 * WC tested up to: 7.7
 * Woo: 18651:0b2ec7cb103c9c102d37f8183924b271
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-currency-converter-widget
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Currency_Converter\Autoloader::init() ) {
	return;
}

// Plugin requirements.
\Themesquad\WC_Currency_Converter\Requirements::init();

if ( ! \Themesquad\WC_Currency_Converter\Requirements::are_satisfied() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_CURRENCY_CONVERTER_FILE' ) ) {
	define( 'WC_CURRENCY_CONVERTER_FILE', __FILE__ );
}

/**
 * Initialize plugin.
 */
function wc_currency_converter_init() {
	require_once __DIR__ . '/includes/class-wc-currency-converter.php';
	WC_Currency_Converter::instance();
}
wc_currency_converter_init();
