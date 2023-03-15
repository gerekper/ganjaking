<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Plugin Name: WooCommerce Xero Integration
 * Plugin URI: https://woocommerce.com/products/xero/
 * Description: Integrates <a href="https://woocommerce.com/" target="_blank" >WooCommerce</a> with the <a href="http://www.xero.com" target="_blank">Xero</a> accounting software.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Version: 1.7.56
 * Text Domain: woocommerce-xero
 * Domain Path: /languages/
 * Requires at least: 5.7
 * Tested up to: 6.1
 * Requires PHP: 7.2
 * WC tested up to: 7.4
 * WC requires at least: 6.8
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

define( 'WC_XERO_VERSION', '1.7.56' ); // WRCS: DEFINED_VERSION.

// ActionScheduler group.
define( 'WC_XERO_AS_GROUP', 'wc_xero' );

/**
 * Rate limit HTTP response code.
 *
 * @see https://developer.xero.com/documentation/api/accounting/responsecodes
 */
define( 'WC_XERO_RATE_LIMIT_ERROR', 429 );

/**
 * Main plugin class.
 */
final class WC_Xero {

	const VERSION = WC_XERO_VERSION;

	/**
	 * Setup the class.
	 */
	public function setup() {
		$wc_xr_plugin_requirement = new WC_XR_PLUGIN_REQUIREMENT();

		if ( ! $wc_xr_plugin_requirement->is_woocommerce_active() ) {
			return;
		}

		if ( is_admin() && current_user_can( 'manage_woocommerce' ) ) {
			$wc_xr_plugin_requirement->is_ssl_active();
		}

		// Run data migrations.
		$wc_xr_encrypt_legacy_tokens_migration = new WC_XR_Encrypt_Legacy_Tokens_Migration();
		$wc_xr_encrypt_legacy_tokens_migration->setup_hook();

		// Load textdomain.
		load_plugin_textdomain( 'woocommerce-xero', false, dirname( plugin_basename( self::get_plugin_file() ) ) . '/languages' );

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
		if ( class_exists( 'WC_Subscriptions_Core_Plugin' ) || class_exists( 'WC_Subscriptions' ) ) {
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
	 * @since 1.7.52 Make function public.
	 * @since  1.0.0
	 */
	public function setup_autoloader() {
		require_once plugin_dir_path( self::get_plugin_file() ) . '/includes/class-wc-xr-autoloader.php';

		// Core loader.
		$autoloader = new WC_XR_Autoloader( plugin_dir_path( self::get_plugin_file() ) . 'includes/' );
		spl_autoload_register( array( $autoloader, 'load' ) );
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
			'<a href="' . admin_url( 'admin.php?page=woocommerce_xero' ) . '">' . __( 'Settings', 'woocommerce-xero' ) . '</a>',
			'<a href="https://woocommerce.com/support/">' . __( 'Support', 'woocommerce-xero' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/xero/">' . __( 'Documentation', 'woocommerce-xero' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

}

$wc_xero = new WC_Xero();
$wc_xero->setup_autoloader();

add_action( 'plugins_loaded', array( $wc_xero, 'setup' ) );

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_woocommerce-xero', '__return_true' );

/**
 * Declares compatibility for HPOS.
 *
 * @return void
 */
function woocommerce_xero_declare_hpos_compatibility() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'woocommerce_xero_declare_hpos_compatibility' );
