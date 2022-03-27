<?php
/**
 * Plugin Name: WooCommerce Xero Integration
 * Plugin URI: https://woocommerce.com/products/xero/
 * Description: Integrates <a href="https://woocommerce.com/" target="_blank" >WooCommerce</a> with the <a href="http://www.xero.com" target="_blank">Xero</a> accounting software.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Version: 1.7.41
 * Text Domain: wc-xero
 * Domain Path: /languages/
 * Tested up to: 5.9
 * WC tested up to: 6.2
 * WC requires at least: 2.6
 *
 * Copyright 2019 WooCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * Woo: 18733:f0dd29d338d3c67cf6cee88eddf6869b
 *
 * @package WC_Xero
 */

/**
 * Absolute url to xero.
 */
if ( ! defined( 'WC_XERO_ABSURL' ) ) {
	define( 'WC_XERO_ABSURL', plugin_dir_url( __FILE__ ) . '/' );
}

define( 'WC_XERO_VERSION', '1.7.41' ); // WRCS: DEFINED_VERSION.

/**
 * Main plugin class.
 */
class WC_Xero {

	const VERSION = WC_XERO_VERSION;

	/**
	 * The constructor.
	 */
	public function __construct() {
		if ( class_exists( 'WooCommerce' ) ) {
			$this->setup();
		} else {
			add_action( 'admin_notices', array( $this, 'notice_wc_required' ) );
		}
	}

	/**
	 * Setup the class.
	 */
	public function setup() {

		// Setup the autoloader.
		$this->setup_autoloader();

		// Load textdomain.
		load_plugin_textdomain( 'wc-xero', false, dirname( plugin_basename( self::get_plugin_file() ) ) . '/languages' );

		// Setup Settings.
		$settings = new WC_XR_Settings();
		$settings->setup_hooks();

		// Setup order actions.
		$order_actions = new WC_XR_Order_Actions( $settings );
		$order_actions->setup_hooks();

		// Setup Invoice hooks.
		$invoice_manager = new WC_XR_Invoice_Manager( $settings );
		$invoice_manager->setup_hooks();

		// Setup Payment hooks.
		$payment_manager = new WC_XR_Payment_Manager( $settings );
		$payment_manager->setup_hooks();

		if ( class_exists( 'WC_Abstract_Privacy' ) ) {
			new WC_XR_Privacy();
		}

		// If subscriptions is active.
		if ( class_exists( 'WC_Subscriptions' ) ) {
			require_once plugin_dir_path( self::get_plugin_file() ) . '/includes/compat/woocommerce-subscriptions-compat.php';
			new Woocommerce_Subscriptions_Compat( $settings );
		}

		// Plugins Links.
		add_filter( 'plugin_action_links_' . plugin_basename( self::get_plugin_file() ), array( $this, 'plugin_links' ) );
	}

	/**
	 * Get the plugin file.
	 *
	 * @static
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public static function get_plugin_file() {
		return __FILE__;
	}

	/**
	 * A static method that will setup the autoloader
	 *
	 * @static
	 * @since  1.0.0
	 */
	private function setup_autoloader() {
		require_once plugin_dir_path( self::get_plugin_file() ) . '/includes/class-wc-xr-autoloader.php';

		// Core loader.
		$autoloader = new WC_XR_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'includes/' );
		spl_autoload_register( array( $autoloader, 'load' ) );
	}

	/**
	 * Admin error notifying user that WC is required.
	 */
	public function notice_wc_required() {
		/* translators: %s: WooCommerce link */
		echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Xero Integration requires %s to be installed and active.', 'wc-xero' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
	}

	/**
	 * Plugin page links
	 *
	 * @param array $links Plugin links.
	 *
	 * @return array Plugin links.
	 */
	public function plugin_links( $links ) {

		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=woocommerce_xero' ) . '">' . __( 'Settings', 'wc-xero' ) . '</a>',
			'<a href="https://woocommerce.com/support/">' . __( 'Support', 'wc-xero' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/xero/">' . __( 'Documentation', 'wc-xero' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

}

/**
 * Extension main function.
 */
function _woocommerce_xero_main() {
	new WC_Xero();
}

// Initialize plugin when plugins are loaded.
add_action( 'plugins_loaded', '_woocommerce_xero_main' );

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_woocommerce-xero', '__return_true' );
