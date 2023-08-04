<?php
/**
 * Plugin Name: WooCommerce Freshdesk
 * Plugin URI: https://woocommerce.com/products/woocommerce-freshdesk/
 * Description: A Freshdesk integration plugin for WooCommerce.
 * Version: 1.3.0
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.3
 * Text Domain: woocommerce-freshdesk
 * Domain Path: languages/
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.9
 * Woo: 395305:31cb841311e1657f69861c452d788726
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-freshdesk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Freshdesk\Autoloader::init() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_FRESHDESK_FILE' ) ) {
	define( 'WC_FRESHDESK_FILE', __FILE__ );
}

if ( ! class_exists( 'WC_Freshdesk' ) ) :

	/**
	 * WooCommerce Freshdesk main class.
	 */
	class WC_Freshdesk extends \Themesquad\WC_Freshdesk\Plugin {

		/**
		 * Integration id.
		 *
		 * @var string
		 */
		protected static $integration_id = 'freshdesk';

		/**
		 * Initialize the plugin.
		 */
		protected function __construct() {
			parent::__construct();

			// Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {
				$this->includes();

				// Register the integration.
				add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

		/**
		 * Return an instance of this class.
		 *
		 * @deprecated 1.2.0
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			wc_deprecated_function( __FUNCTION__, '1.2.0', 'WC_Freshdesk::instance()' );

			return self::instance();
		}

		/**
		 * Includes.
		 *
		 * @return void
		 */
		private function includes() {
			include_once __DIR__ . '/includes/class-wc-freshdesk-integration.php';
			include_once __DIR__ . '/includes/abstracts/abstract-wc-freshdesk-abstract-integration.php';
			include_once __DIR__ . '/includes/class-wc-freshdesk-forum-category.php';
			include_once __DIR__ . '/includes/class-wc-freshdesk-solutions-category.php';
			include_once __DIR__ . '/includes/class-wc-freshdesk-tickets.php';
			include_once __DIR__ . '/includes/class-wc-freshdesk-shortcodes.php';
			include_once __DIR__ . '/includes/class-wc-freshdesk-privacy.php';
			include_once __DIR__ . '/includes/class-wc-freshdesk-ajax.php';
		}

		/**
		 * Return the integration id/slug.
		 *
		 * @return string Integration slug variable.
		 */
		public static function get_integration_id() {
			return self::$integration_id;
		}

		/**
		 * Return the WooCommerce logger API.
		 *
		 * @return WC_Logger
		 */
		public static function get_logger() {
			return new WC_Logger();
		}

		/**
		 * Add a new integration to WooCommerce.
		 *
		 * @param  array $integrations WooCommerce integrations.
		 *
		 * @return array               Freshdesk integration.
		 */
		public function add_integration( $integrations ) {
			$integrations[] = 'WC_Freshdesk_Integration';

			return $integrations;
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			/* translators: %s: WooCommerce link */
			echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Freshdesk requires %s to be installed and active.', 'woocommerce-freshdesk' ), '<a href="https://woocommerce.com" target="_blank">' . esc_html__( 'WooCommerce', 'woocommerce-freshdesk' ) . '</a>' ) . '</p></div>';
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Freshdesk', 'instance' ) );

endif;
