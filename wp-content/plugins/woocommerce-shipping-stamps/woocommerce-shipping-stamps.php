<?php
/**
 * Plugin Name: WooCommerce Stamps.com API integration
 * Plugin URI: https://woocommerce.com/products/woocommerce-shipping-stamps/
 * Description: Stamps.com API integration for label printing. Requires server SOAP support.
 * Version: 1.9.3
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: woocommerce-shipping-stamps
 * Domain Path: /languages
 *
 * Woo: 538435:b0e7af51937d3cdbd6779283d482b6e4
 * WC tested up to: 7.9
 * WC requires at least: 3.0
 * Tested up to: 6.2
 *
 * Copyright: © 2023 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Shipping_Stamps
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.3.17
 * @return void
 */
function woocommerce_shipping_stamps_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Stamps requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-shipping-stamps' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Stamps_Integration' ) ) :
	define( 'WC_STAMPS_INTEGRATION_VERSION', '1.9.3' ); // WRCS: DEFINED_VERSION.

	/**
	 * WC_Stamps_Integration class.
	 */
	class WC_Stamps_Integration {

		/**
		 * Constructor.
		 */
		public function __construct() {
			require_once( dirname( __FILE__ ) . '/includes/class-wc-stamps-privacy.php' );

			define( 'WC_STAMPS_INTEGRATION_FILE', __FILE__ );
			include_once( dirname( __FILE__ ) . '/includes/class-wc-stamps-settings.php' );

			$test_mode = defined( 'WC_STAMPS_TEST_MODE' ) && WC_STAMPS_TEST_MODE;
			if ( $test_mode ) {
				define( 'WC_STAMPS_INTEGRATION_WSDL_FILE', 'test-swsimv50.wsdl' );
				define( 'WC_STAMPS_INTEGRATION_AUTH_ENDPOINT', 'https://connect.woocommerce.com/auth/stampssandbox' );
			} else {
				define( 'WC_STAMPS_INTEGRATION_WSDL_FILE', 'swsimv50.wsdl' );
				define( 'WC_STAMPS_INTEGRATION_AUTH_ENDPOINT', 'https://connect.woocommerce.com/auth/stamps' );
			}

			include_once( 'includes/class-wc-stamps-api.php' );
			include_once( 'includes/class-wc-stamps-balance.php' );

			if ( is_admin() && current_user_can( 'manage_woocommerce' ) ) {
				include_once( 'includes/class-wc-stamps-order.php' );
				include_once( 'includes/class-wc-stamps-post-types.php' );
				include_once( 'includes/class-wc-stamps-labels.php' );
				include_once( 'includes/class-wc-stamps-label.php' );
				include_once( 'includes/class-wc-stamps-settings.php' );
			}

			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
			add_action( 'admin_init', array( $this, 'activation_check' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			add_filter( 'woocommerce_translations_updates_for_woocommerce_shipping_stamps', '__return_true' );
		}

		/**
		 * Declare High-Performance Order Storage (HPOS) compatibility
		 *
		 * @see https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
		 *
		 * @return void
		 */
		public function declare_hpos_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-shipping-stamps/woocommerce-shipping-stamps.php' );
			}
		}

		/**
		 * Check SOAP support on activation
		 */
		public function activation_check() {
			if ( ! class_exists( 'SoapClient' ) ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_die( 'Sorry, but you cannot run this plugin, it requires the <a href="http://php.net/manual/en/class.soapclient.php">SOAP</a> support on your server to function.' );
			}
		}

		/**
		 * Plugin action links.
		 *
		 * @since 1.3.3
		 * @version 1.3.3
		 *
		 * @param array $links Plugin action links.
		 *
		 * @return array Plugin action links.
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=stamps' ) . '">' . __( 'Settings', 'woocommerce-shipping-stamps' ) . '</a>',
				'<a href="http://docs.woocommerce.com/">' . __( 'Support', 'woocommerce-shipping-stamps' ) . '</a>',
				'<a href="https://docs.woocommerce.com/document/woocommerce-shipping-stamps/">' . __( 'Docs', 'woocommerce-shipping-stamps' ) . '</a>',
			);
			return array_merge( $plugin_links, $links );
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_shipping_stamps_init' );

/**
 * Initializes the extension.
 *
 * @since 1.3.17
 * @return void
 */
function woocommerce_shipping_stamps_init() {
	load_plugin_textdomain( 'woocommerce-shipping-stamps', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipping_stamps_missing_wc_notice' );
		return;
	}

	return wc_shipping_stamps();
}

/**
 * Return instance of WC_Stamps_Integration.
 *
 * @since 1.3.3
 * @version 1.3.3
 *
 * @return WC_Stamps_Integration.
 */
function wc_shipping_stamps() {
	static $plugin;

	if ( ! isset( $plugin ) ) {
		$plugin = new WC_Stamps_Integration();
	}

	return $plugin;
}
