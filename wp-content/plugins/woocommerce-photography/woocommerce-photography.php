<?php
/**
 * Plugin Name: WooCommerce Photography
 * Plugin URI: https://woocommerce.com/
 * Description: Provide a user experience for photographers to offer batches of images for order.
 * Version: 1.0.25
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 *
 * Text Domain: woocommerce-photography
 * Domain Path: /languages
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 * Tested up to: 5.3
 *
 * @package  WC_Photography
 * @category Core
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 583602:ee76e8b9daf1d97ca4d3874cc9e35687
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.0.22
 */
function woocommerce_photography_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Photography requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-photography' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Install method.
 *
 */
function woocommerce_photography_install() {
	include_once 'includes/class-wc-photography-taxonomies.php';
	include_once 'includes/class-wc-photography-install.php';

	WC_Photography_Install::install();
}

register_activation_hook( __FILE__, 'woocommerce_photography_install' );

if ( ! class_exists( 'WC_Photography' ) ) :
	define( 'WC_PHOTOGRAPHY_VERSION', '1.0.25' ); // WRCS: DEFINED_VERSION.

	/**
	 * WooCommerce Photography main class.
	 */
	class WC_Photography {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Initialize the plugin.
		 */
		private function __construct() {
			$this->includes();

			if ( is_admin() ) {
				$this->admin_includes();

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			}
		}

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Get templates path.
		 *
		 * @return string
		 */
		public static function get_templates_path() {
			return plugin_dir_path( __FILE__ ) . 'templates/';
		}

		/**
		 * Get assets url.
		 *
		 * @return string
		 */
		public static function get_assets_url() {
			return plugins_url( 'assets/', __FILE__ );
		}

		/**
		 * Includes.
		 *
		 * @return void
		 */
		private function includes() {
			// Classes.
			include_once 'includes/class-wc-photography-taxonomies.php';
			include_once 'includes/class-wc-product-photography.php';
			include_once 'includes/class-wc-photography-frontend.php';
			include_once 'includes/class-wc-photography-products.php';
			include_once 'includes/class-wc-photography-ajax.php';
			include_once 'includes/class-wc-photography-emails.php';
			include_once 'includes/class-wc-photography-install.php';
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
			require_once dirname( __FILE__ ) . '/includes/admin/class-wc-photography-privacy.php';
		}

		/**
		 * Add relevant links to plugins page.
		 *
		 * @param  array $links
		 *
		 * @return array
		 */
		public function plugin_action_links( $links ) {
			$plugin_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=wc-photography-settings' ) . '">' . __( 'Settings', 'woocommerce-photography' ) . '</a>',
				'support'  => '<a href="https://woocommerce.com/my-account/create-a-ticket/">' . __( 'Support', 'woocommerce-photography' ) . '</a>',
				'docs'     => '<a href="https://docs.woocommerce.com/documentation/woocommerce-extensions/photography/">' . __( 'Docs', 'woocommerce-photography' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_photography_init' );

/**
 * Initializes the extension.
 *
 * @since 1.0.22
 * @return Object Instance of the extension.
 */
function woocommerce_photography_init() {
	load_plugin_textdomain( 'woocommerce-photography', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_photography_wc_notice' );
		return;
	}

	WC_Photography::get_instance();
}
