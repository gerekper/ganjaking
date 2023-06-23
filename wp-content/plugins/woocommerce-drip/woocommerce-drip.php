<?php
/**
 * Plugin Name: WooCommerce Drip
 * Plugin URI: https://woocommerce.com/products/woocommerce-drip/
 * Description: Integrate your WooCommerce store and customers with your Drip account.
 * Version: 1.3.0
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Text Domain: woocommerce-drip
 * Domain Path: /languages
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.2
 *
 * Woo: 609085:cbafd0ee5daa6120a5902df2ecf6fe7b
 * WC requires at least: 3.5
 * WC tested up to: 7.8
 *
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-drip
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Drip\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_DRIP_FILE' ) ) {
	define( 'WC_DRIP_FILE', __FILE__ );
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
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_drip_missing_wc_notice' );
		return;
	}

	if ( ! class_exists( 'WC_Drip' ) ) {
		/**
		 * WC_Drip Class
		 *
		 * @package  WooCommerce Drip
		 * @since    1.0.0
		 */
		class WC_Drip extends \Themesquad\WC_Drip\Plugin {
			/**
			 * Construct the plugin.
			 **/
			protected function __construct() {
				parent::__construct();

				$this->init();
			}

			/**
			 * Initialize the plugin
			 **/
			public function init() {
				// Brace Yourself
				require_once WC_DRIP_PATH . 'includes/class-wcdrip.php';
				require_once WC_DRIP_PATH . 'includes/class-wcdrip-settings.php';
				require_once WC_DRIP_PATH . 'includes/class-wcdrip-privacy.php';
				require_once WC_DRIP_PATH . 'includes/class-wcdrip-events.php';
				require_once WC_DRIP_PATH . 'includes/class-wcdrip-subscribe.php';

				// Drip API PHP Library Class
				require_once WC_DRIP_PATH . 'includes/lib/Drip_API.class.php';

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
		}

		WC_Drip::instance();
	}
}


if ( ! function_exists( 'wcdrip_log' ) ) {

	/**
	 * Log a message via WC_Logger.
	 *
	 * @since 1.2.0
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
	 * @since 1.2.0
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
