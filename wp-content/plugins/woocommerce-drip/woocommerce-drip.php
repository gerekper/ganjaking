<?php
/**
 * Plugin Name: WooCommerce Drip
 * Plugin URI: https://woocommerce.com/products/woocommerce-drip/
 * Description: Integrate your WooCommerce store and customers with your Drip account.
 * Version: 1.2.22
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * License: GPL-2.0+
 * Text Domain: woocommerce-drip
 *
 * Copyright: © 2020 WooCommerce
 * Woo: 609085:cbafd0ee5daa6120a5902df2ecf6fe7b
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 * Tested up to: 5.5
 *
 * @package woocommerce-drip
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.21
 * @return string
 */
function woocommerce_drip_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Drip requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-drip' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

add_action( 'plugins_loaded', 'woocommerce_drip_init' );

function woocommerce_drip_init() {
	load_plugin_textdomain( 'woocommerce-drip', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_drip_missing_wc_notice' );
		return;
	}

	if ( ! class_exists( 'WC_Drip' ) ) {
		define( 'WC_DRIP_VERSION', '1.2.22' ); // WRCS: DEFINED_VERSION.

		/**
		 * WC_Drip Class
		 *
		 * @package  WooCommerce Drip
		 * @since    1.0.0
		 */
		class WC_Drip {
			/**
			 * Construct the plugin.
			 **/
			public function __construct() {
				$this->init();
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			}

			/**
			 * Initialize the plugin
			 **/
			public function init() {
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
			 * Plugin action links
			 */
			public function action_links( $links ) {
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=wcdrip' ) . '">Settings</a>',
					'<a href="https://docs.woocommerce.com/document/woocommerce-drip/">' . __( 'Documentation', 'woocommerce-drip' ) . '</a>',
				);

				return array_merge( $plugin_links, $links );
			}
		}

		new WC_Drip();
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
