<?php
/**
 * Plugin Name: WooCommerce Photography
 * Plugin URI: https://woocommerce.com/
 * Description: Provide a user experience for photographers to offer batches of images for order.
 * Version: 1.2.1
 * Author: Themesquad
 * Author URI: https://themesquad.com
 * Text Domain: woocommerce-photography
 * Domain Path: /languages
 * Requires PHP: 5.4
 * Requires at least: 4.7
 * Tested up to: 6.3
 *
 * WC requires at least: 3.4
 * WC tested up to: 8.0
 * Woo: 583602:ee76e8b9daf1d97ca4d3874cc9e35687
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Photography
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Photography\Autoloader::init() ) {
	return;
}

// Plugin requirements.
\Themesquad\WC_Photography\Requirements::init();

if ( ! \Themesquad\WC_Photography\Requirements::are_satisfied() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_PHOTOGRAPHY_FILE' ) ) {
	define( 'WC_PHOTOGRAPHY_FILE', __FILE__ );
}

if ( ! class_exists( 'WC_Photography' ) ) :

	/**
	 * WooCommerce Photography main class.
	 */
	class WC_Photography extends \Themesquad\WC_Photography\Plugin {

		/**
		 * Initialize the plugin.
		 */
		protected function __construct() {
			parent::__construct();

			$this->includes();

			if ( is_admin() ) {
				$this->admin_includes();
			}
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return WC_PHOTOGRAPHY_PATH . 'templates/';
		}

		/**
		 * Get assets url.
		 *
		 * @return string
		 */
		public static function get_assets_url() {
			return WC_PHOTOGRAPHY_URL . 'assets/';
		}

		/**
		 * Includes.
		 *
		 * @return void
		 */
		private function includes() {
			// Classes.
			include_once 'includes/class-wc-photography-taxonomies.php';
			include_once 'includes/class-wc-photography-install.php';
			include_once 'includes/class-wc-photography-frontend.php';
			include_once 'includes/class-wc-photography-products.php';
			include_once 'includes/class-wc-photography-ajax.php';
			include_once 'includes/class-wc-photography-emails.php';
			include_once 'includes/class-wc-photography-wc-compat.php';

			// Integration with Products Add-ons.
			if ( class_exists( 'WC_Product_Addons' ) ) {
				include_once 'includes/class-wc-photography-products-addons.php';
			}

			// Functions.
			include_once 'includes/wc-photography-template-functions.php';
			include_once 'includes/wc-photography-helpers.php';
		}

		/**
		 * Admin includes.
		 *
		 * @return void
		 */
		private function admin_includes() {
			include_once 'includes/admin/class-wc-photography-admin.php';
		}

		/**
		 * Add relevant links to plugins page.
		 *
		 * @param array $links Action links.
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			wc_deprecated_function( __FUNCTION__, '1.2.0' );
			return $links;
		}

		/**
		 * Returns an instance of this class.
		 *
		 * @deprecated 1.2.0
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			wc_deprecated_function( __FUNCTION__, '1.2.0', 'WC_Photography::instance()' );

			return self::instance();
		}
	}
endif;

/**
 * Initializes the extension.
 *
 * @since 1.0.22
 */
function woocommerce_photography_init() {
	WC_Photography::instance();
}

woocommerce_photography_init();
