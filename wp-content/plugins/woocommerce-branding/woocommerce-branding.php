<?php
/**
 * Plugin Name: WooCommerce Branding
 * Plugin URI: http://woocommerce.com/products/branding/
 * Description: Rebrand WooCommerce using your own brand name, colour scheme, and icon.
 * Version: 1.0.26
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Requires at least: 3.1
 * Tested up to: 4.8
 * Woo: 19003:b57eb3de77456cf73ef6f7456a03ea83
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-branding
 */

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_branding_init' );

/**
 * Initialize plugin.
 */
function wc_branding_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_branding_woocommerce_deactivated' );
		return;
	}

	/**
	 * Localisation
	 */
	load_plugin_textdomain( 'wc_branding', false, dirname( plugin_basename( __FILE__ ) ) . '/' );

	require_once 'classes/class-wc-branding.php';

	$woocommerce_branding = new WC_Branding( __FILE__ );

}

register_activation_hook( __FILE__, 'activate_woocommerce_branding' );

/**
 * Activation function.
 */
function activate_woocommerce_branding() {
	// Ensure WC Extensions Flash is disabled.
	update_option( 'hide-wc-extensions-message', 1 );
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_branding_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Branding requires %s to be installed and active.', 'wc_branding' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
