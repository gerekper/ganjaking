<?php
/**
 * Plugin Name: WooCommerce Shipping Per Product v2
 * Plugin URI: https://woocommerce.com/products/per-product-shipping/
 * Description: Per product shipping allows you to define different shipping costs for products, based on customer location. These costs can be added to other shipping methods, or used as a standalone shipping method.
 * Version: 2.5.5
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-shipping-per-product
 * Requires at least: 3.3
 * Tested up to: 6.3
 * WC requires at least: 3.0
 * WC tested up to: 8.1
 *
 * Copyright: © 2023 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 18590:ba16bebba1d74992efc398d575bf269e
 *
 * @package WC_Shipping_Per_Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PER_PRODUCT_SHIPPING_VERSION', '2.5.5' ); // WRCS: DEFINED_VERSION.

if ( ! defined( 'PER_PRODUCT_SHIPPING_FILE' ) ) {
	define( 'PER_PRODUCT_SHIPPING_FILE', __FILE__ );
}

if ( ! defined( 'PER_PRODUCT_SHIPPING_ABSPATH' ) ) {
	define( 'PER_PRODUCT_SHIPPING_ABSPATH', dirname( PER_PRODUCT_SHIPPING_FILE ) . '/' );
}

register_activation_hook( __FILE__, 'woocommerce_shipping_per_product_install' );

/**
 * Installer.
 */
function woocommerce_shipping_per_product_install() {
	include_once 'installer.php';
}

/**
 * WooCommerce fallback notice.
 *
 * @since 2.3.8
 */
function woocommerce_shipping_per_product_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Per Product Shipping requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-shipping-per-product' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

// Require the main Shipping Per Product class.
if ( ! class_exists( 'WC_Shipping_Per_Product_Init' ) ) {
	require_once dirname( PER_PRODUCT_SHIPPING_FILE ) . '/includes/class-wc-shipping-per-product-init.php';
}

add_action( 'plugins_loaded', 'woocommerce_shipping_per_product_init' );

/**
 * Function that initializes the extension.
 *
 * @return void
 * @since 2.3.8
 */
function woocommerce_shipping_per_product_init() {
	load_plugin_textdomain( 'woocommerce-shipping-per-product', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipping_per_product_missing_wc_notice' );

		return;
	}

	WC_Shipping_Per_Product_Init::instance();
}
