<?php
/**
 * Plugin Name: WooCommerce Slack
 * Plugin URI: https://woocommerce.com/products/woocommerce-slack/
 * Description: Easily send notifications to your different Slack channels whenever a WooCommerce event happens!
 * Version: 1.3.0
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires PHP: 5.6
 * Requires at least: 4.7
 * Tested up to: 6.1
 * Domain: woocommerce-slack
 * Domain Path: /languages
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.3
 * Woo: 609199:5d6bda97bdd686290db0d68143723878
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Slack
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Slack\Autoloader::init() ) {
	return;
}

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

// Define plugin file constant.
if ( ! defined( 'WC_SLACK_FILE' ) ) {
	define( 'WC_SLACK_FILE', __FILE__ );
}

if ( ! class_exists( 'WC_Slack' ) ) {

	/**
	 * WC_Slack Class
	 *
	 * @package  WooCommerce Slack
	 * @since    1.0.0
	 */
	class WC_Slack extends Themesquad\WC_Slack\Plugin {

		/**
		 * Construct the plugin
		 **/
		protected function __construct() {
			parent::__construct();

			require_once WC_SLACK_PATH . 'includes/class-wcslack.php'; // Deprecated.

			$options = get_option( 'woocommerce_wcslack_settings', true );
			if ( ! empty( $options['api_key'] ) ) {
				require_once WC_SLACK_PATH . 'includes/class-wcslack-settings-legacy.php';
			} else {
				require_once WC_SLACK_PATH . 'includes/class-wcslack-settings.php';
			}
			require_once WC_SLACK_PATH . 'includes/class-wcslack-slack.php';
			require_once WC_SLACK_PATH . 'includes/class-wcslack-events.php';
			require_once WC_SLACK_PATH . 'includes/class-wcslack-privacy.php';

			add_action( 'init', array( 'WC_Slack_Events', 'get_instance' ) );
			add_action( 'init', array( 'WC_Slack_API', 'get_instance' ) );

			add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
		}

		/**
		 * This function was used to show plugin links in admin panel.
		 *
		 * @deprecated 1.3.0
		 *
		 * @param array $links Links.
		 * @return array
		 */
		public function plugin_links( $links ) {
			wc_deprecated_function( __FUNCTION__, '1.3.0' );

			return $links;
		}

		/**
		 * Add Integration Settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $integrations Array of integration instances.
		 * @return array
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
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_slack_missing_wc_notice' );
		return;
	}
	WC_Slack::instance();
}
