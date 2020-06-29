<?php
/**
 * Plugin Name: WooCommerce Drip
 * Plugin URI: https://woocommerce.com/products/woocommerce-drip/
 * Description: Integrate your WooCommerce store and customers with your Drip account.
 * Version: 1.2.20
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * License: GPL-2.0+
 * Text Domain: woocommerce-drip
 *
 * Copyright: © 2020 WooCommerce
 * Woo: 609085:cbafd0ee5daa6120a5902df2ecf6fe7b
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 * Tested up to: 5.3
 *
 * @package woocommerce-drip
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WC_Drip Class
 *
 * @package  WooCommerce Drip
 * @since    1.0.0
 */

if ( ! class_exists( 'WC_Drip' ) ) {

	define( 'WC_DRIP_VERSION', '1.2.20' ); // WRCS: DEFINED_VERSION.

	class WC_Drip {

		/**
		 * Construct the plugin.
		 **/
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}


		/**
		 * Initialize the plugin
		 **/
		public function init() {
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

				// Brace Yourself
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcdrip.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcdrip-settings.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcdrip-privacy.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcdrip-events.php' );
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcdrip-subscribe.php' );

				// Drip API PHP Library Class
				require_once( plugin_dir_path( __FILE__ ) . 'includes/lib/Drip_API.class.php' );

				// WC Plugin Compatability Class (https://github.com/skyverge/wc-plugin-compatibility)
				include( plugin_dir_path( __FILE__ ) . 'includes/lib/class-wcdrip-wc-plugin-compatibility.php' );

				// Vroom.. Vroom..
				add_action( 'init', array( 'WC_Drip_Init', 'get_instance' ) );
				add_action( 'init', array( 'WC_Drip_Events', 'get_instance' ) );
				add_action( 'init', array( 'WC_Drip_Subscriptions', 'get_instance' ) );

				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );

			} else {

				add_action( 'admin_notices', array( $this, 'woocoommerce_deactivated' ) );

			}

		}


		/**
		 * Add Integration Settings
		 *
		 * @package  WooCommerce Drip
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function add_integration( $integrations ) {

			$integrations[] = 'WC_Drip_Settings';
			return $integrations;

		}


		/**
		 * WooCommerce Deactivated Notice
		 *
		 * @package  WooCommerce Drip
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function woocoommerce_deactivated() {

			echo '<div class="error"><p>' . sprintf( __( 'WooCommerce Drip requires %s to be installed and active.', 'woocommerce-drip' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';

		}

	}

}

$WC_Drip = new WC_Drip( __FILE__ );


/**
 * Plugin Settings Links etc.
 *
 * @package  WooCommerce Drip
 * @author   Bryce <bryce@bryce.se>
 * @since    1.0.0
 */

$plugin = plugin_basename( __FILE__ );
add_filter( 'plugin_action_links_' . $plugin, 'wcdrip_plugin_links' );

// Add settings link on plugin page
if ( ! function_exists( 'wcdrip_plugin_links' ) ) {
	function wcdrip_plugin_links( $links ) {

		$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=wcdrip' ) . '">Settings</a>';
		$settings_link .= ' | <a href="http://docs.woothemes.com/document/woocommerce-drip" target="_blank">Docs</a>';
		array_unshift( $links, $settings_link );
		return $links;

	}
}

if ( ! function_exists( 'wcdrip_log' ) ) {

	/**
	 * Log a message via WC_Logger.
	 *
	 * @since 1.3.0
	 *
	 * @param string $message Message to log
	 */
	function wcdrip_log( $message ) {

		static $wcdrip_logger;

		$settings = wcdrip_get_settings();
		if ( ! class_exists( 'WC_Logger' ) || ! isset( $settings['logging_enabled'] ) ) {
			return false;
		}
		if ( 'yes' !== $settings['logging_enabled'] ) {
			return false;
		}

		if ( ! isset( $wcdrip_logger ) ) {
			$wcdrip_logger = new WC_Logger();
		}

		$wcdrip_logger->add( 'woocommerce-drip', $message );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( $message );
		}
	}

}

if ( ! function_exists( 'wcdrip_get_settings' ) ) {

	/**
	 * Get WP Drip settings.
	 *
	 * @since 1.3.0
	 *
	 * @return array WC Drip settings
	 */
	function wcdrip_get_settings() {
		$settings = get_option( 'woocommerce_wcdrip_settings', array() );

		foreach ( $settings as $k => $v  ) {
			$nk = str_replace( '-', '_', $k );

			if ( ! isset( $settings[ $nk ] ) ) {
				$settings[ $nk ] = $v;
				unset( $settings[ $k ] );
			}
		}

		return $settings;
	}

}
