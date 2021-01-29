<?php
/**
 * Plugin Name: WooCommerce Currency Converter
 * Plugin URI: https://woocommerce.com/products/currency-converter-widget/
 * Description: Adds a currency selection widget - when the user chooses a currency, the stores prices are displayed in the chosen currency dynamically. This does not affect the currency in which you take payment. Conversions are estimated based on data from the Open Source Exchange Rates API with no guarantee whatsoever of accuracy.
 * Version: 1.6.24
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: woocommerce-currency-converter-widget
 * Domain Path: /languages
 * Requires at least: 3.1
 * Tested up to: 5.5
 * WC tested up to: 4.5
 * WC requires at least: 2.6
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 18651:0b2ec7cb103c9c102d37f8183924b271
 *
 * @package woocommerce-currency-converter-widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_CURRENCY_CONVERTER_VERSION', '1.6.24' ); // WRCS: DEFINED_VERSION.

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_currency_converter_init' );

/**
 * Initialize plugin.
 */
function wc_currency_converter_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_currency_converter_woocommerce_deactivated' );
		return;
	}

	if ( ! class_exists( 'WC_Currency_Converter' ) ) {
		require_once __DIR__ . '/includes/class-wc-currency-converter.php';
		new WC_Currency_Converter();
	}
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_currency_converter_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Currency Converter requires %s to be installed and active.', 'woocommerce-currency-converter-widget' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
