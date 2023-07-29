<?php
/**
 * Plugin Name: WooCommerce Chained Products
 * Plugin URI: https://woocommerce.com/products/chained-products/
 * Description: Easily create chained products, product bundles and combo packs and boost your sales.
 * Version: 3.0.0
 * Author: StoreApps
 * Author URI: https://www.storeapps.org/
 * Developer: StoreApps
 * Developer URI: https://www.storeapps.org/
 * Requires at least: 4.9.0
 * Tested up to: 6.2.2
 * WC requires at least: 3.0.0
 * WC tested up to: 7.9.0
 * Text Domain: woocommerce-chained-products
 * Domain Path: /languages/
 * Woo: 18687:cc6e246e495745db10f9f7fddc5aa907
 * Copyright (c) 2012-2023 WooCommerce, StoreApps All rights reserved.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-chained-products
 */

/**
 * Function to set transient on plugin activation
 */
function chained_product_activate() {
	if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) { // phpcs:ignore
		set_transient( '_chained_products_activation_redirect', 1, 30 );
	}
}

register_activation_hook( __FILE__, 'chained_product_activate' );

/**
 * Function to initiate Chained Products & its functionality
 */
function initialize_chained_products() {

	global $wc_cp;

	$active_plugins = (array) get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	if ( ( in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) ) {
		if ( ! defined( 'WC_CP_PLUGIN_DIRNAME' ) ) {
			define( 'WC_CP_PLUGIN_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
		}

		if ( ! defined( 'WC_CP_PLUGIN_FILE' ) ) {
			define( 'WC_CP_PLUGIN_FILE', __FILE__ );
		}

		if ( ! defined( 'WC_CP_PLUGIN_URL' ) ) {
			define( 'WC_CP_PLUGIN_URL', plugins_url( WC_CP_PLUGIN_DIRNAME ) );
		}

		include_once 'includes/class-wc-chained-products.php';

		$wc_cp = new WC_Chained_Products();
	} else {
		if ( is_admin() ) {
			?>
			<div class="notice notice-error">
				<p><?php echo esc_html__( 'WooCommerce Chained Products requires WooCommerce to be activated.', 'woocommerce-chained-products' ); ?></p>
			</div>
			<?php
		}
	}
}

add_action( 'plugins_loaded', 'initialize_chained_products' );

