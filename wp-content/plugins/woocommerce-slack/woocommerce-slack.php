<?php
/**
 * Plugin Name: WooCommerce Slack
 * Plugin URI: https://woocommerce.com/products/woocommerce-slack/
 * Description: Easily send notifications to your different Slack channels whenever a WooCommerce event happens!
 * Version: 1.2.10
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Tested up to: 6.0
 * Domain: woocommerce-slack
 * Domain Path: /languages
 *
 * WC requires at least: 2.6
 * WC tested up to: 7.0
 * Woo: 609199:5d6bda97bdd686290db0d68143723878
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Slack
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.2
 * @return void
 */
function woocommerce_slack_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Slack requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-slack' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * WC_Slack Class
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.0.0
 */

if ( ! class_exists( 'WC_Slack' ) ) {

	define( 'WC_SLACK_VERSION', '1.2.10' ); // WRCS: DEFINED_VERSION.

	class WC_Slack {

		/**
		 * Construct the plugin
		 **/

		public function __construct() {
			$plugin = plugin_basename( __FILE__ );
			add_filter( 'plugin_action_links_' . $plugin, array( $this, 'plugin_links' ) );

			// Brace Yourself
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack.php' );
			$options = get_option( 'woocommerce_wcslack_settings', true );
			if ( ! empty( $options['api_key'] ) ) {
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-settings-legacy.php' );
			} else {
				require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-settings.php' );
			}
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-slack.php' );
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-events.php' );
			require_once( plugin_dir_path( __FILE__ ) . 'includes/class-wcslack-privacy.php' );

			// Vroom.. Vroom..
			WC_Slack_Init::get_instance();
			add_action( 'init', array( 'WC_Slack_Events', 'get_instance' ) );
			add_action( 'init', array( 'WC_Slack_API', 'get_instance' ) );

			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		}

		public function plugin_links( $links ) {

			$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=wcslack' ) . '">' . __( 'Settings', 'woocommerce-slack' ) . '</a>';
			$settings_link .= ' | <a href="http://docs.woocommerce.com/document/woocommerce-slack" target="_blank">' . __( 'Docs', 'woocommerce-slack' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 * Add Integration Settings
		 *
		 * @package  WooCommerce Slack
		 * @author   Bryce <bryce@bryce.se>
		 * @since    1.0.0
		 */

		public function add_integration( $integrations ) {

			$integrations[] = 'WC_Slack_Settings';
			return $integrations;

		}
	}
}

add_action( 'plugins_loaded', 'woocommerce_slack_init' );

/**
 * Initializes the extension.
 *
 * @since 1.2.2
 * @return void
 */
function woocommerce_slack_init() {
	load_plugin_textdomain( 'woocommerce-slack', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_slack_missing_wc_notice' );
		return;
	}

	new WC_Slack();
}
