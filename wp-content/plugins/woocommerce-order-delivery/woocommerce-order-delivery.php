<?php
/**
 * Plugin Name: WooCommerce Order Delivery
 * Plugin URI: https://woocommerce.com/products/woocommerce-order-delivery/
 * Description: Choose a delivery date during checkout for the order.
 * Version: 1.8.5
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires at least: 4.4
 * Tested up to: 5.6
 * WC requires at least: 3.0
 * WC tested up to: 4.9
 * Woo: 976514:beaa91b8098712860ec7335d3dca61c0
 *
 * Text Domain: woocommerce-order-delivery
 * Domain Path: /languages/
 *
 * Copyright: © 2015-2021 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'beaa91b8098712860ec7335d3dca61c0', '976514' );

/**
 * Check if WooCommerce is active and the minimum requirements are satisfied.
 */
if ( ! is_woocommerce_active() || version_compare( get_option( 'woocommerce_db_version' ), '3.0', '<' ) ) {
	add_action( 'admin_notices', 'wc_od_requirements_notice' );
	return;
}

/**
 * Displays an admin notice when the minimum requirements are not satisfied.
 *
 * @since 1.4.1
 */
function wc_od_requirements_notice() {
	if ( current_user_can( 'activate_plugins' ) ) :
		if ( is_woocommerce_active() ) :
			/* translators: %s: WooCommerce version */
			$message = sprintf( __( '<strong>WooCommerce Order Delivery</strong> requires WooCommerce %s or higher.', 'woocommerce-order-delivery' ), '3.0' );
		else :
			$message = __( '<strong>WooCommerce Order Delivery</strong> requires WooCommerce to be activated to work.', 'woocommerce-order-delivery' );
		endif;

		printf( '<div class="error"><p>%s</p></div>', wp_kses_post( $message ) );
	endif;
}

/**
 * Singleton pattern
 */
if ( ! class_exists( 'WC_OD_Singleton' ) ) {
	require_once 'includes/class-wc-od-singleton.php';
}

if ( ! class_exists( 'WC_Order_Delivery' ) ) {

	/**
	 * Class WC_Order_Delivery
	 */
	final class WC_Order_Delivery extends WC_OD_Singleton {

		/**
		 * The plugin version.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $version = '1.8.5';


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		protected function __construct() {
			parent::__construct();

			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Define constants.
		 *
		 * @since 1.1.0
		 */
		public function define_constants() {
			$this->define( 'WC_OD_VERSION', $this->version );
			$this->define( 'WC_OD_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'WC_OD_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WC_OD_BASENAME', plugin_basename( __FILE__ ) );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @since 1.1.0
		 *
		 * @param string      $name  The constant name.
		 * @param string|bool $value The constant value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Auto-load in-accessible properties on demand.
		 *
		 * NOTE: Keep backward compatibility with some deprecated properties on this class.
		 *
		 * @since 1.1.0
		 *
		 * @param mixed $key The property name.
		 * @return mixed The property value.
		 */
		public function __get( $key ) {
			switch ( $key ) {
				case 'dir_path':
					wc_deprecated_argument( 'WC_Order_Delivery->dir_path', '1.1.0', 'This property is deprecated and will be removed in future releases. Use the constant WC_OD_PATH instead.' );
					return WC_OD_PATH;

				case 'dir_url':
					wc_deprecated_argument( 'WC_Order_Delivery->dir_url', '1.1.0', 'This property is deprecated and will be removed in future releases. Use the constant WC_OD_URL instead.' );
					return WC_OD_URL;

				case 'date_format':
					wc_deprecated_argument( 'WC_Order_Delivery->date_format', '1.1.0', 'This property is deprecated and will be removed in future releases. Use the function wc_od_get_date_format() instead.' );
					return wc_od_get_date_format( 'php' );

				case 'date_format_js':
					wc_deprecated_argument( 'WC_Order_Delivery->date_format', '1.1.0', 'This property is deprecated and will be removed in future releases. Use the function wc_od_get_date_format() instead.' );
					return wc_od_get_date_format( 'js' );

				case 'prefix':
					wc_deprecated_argument( 'WC_Order_Delivery->prefix', '1.1.0', 'This property is deprecated and will be removed in future releases.' );
					return 'wc_od_';
			}
		}

		/**
		 * Includes the necessary files.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			/**
			 * Class autoloader.
			 */
			include_once 'includes/class-wc-od-autoloader.php';

			/**
			 * Abstract classes.
			 */
			include_once 'includes/abstracts/abstract-class-wc-od-data.php';
			include_once 'includes/abstracts/abstract-class-wc-od-shipping-methods-data.php';

			/**
			 * Core classes.
			 */
			include_once 'includes/wc-od-functions.php';
			include_once 'includes/class-wc-od-install.php';
			include_once 'includes/class-wc-od-emails.php';

			if ( is_admin() ) {
				include_once 'includes/admin/class-wc-od-admin.php';
			}

			if ( WC_OD_Utils::is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
				include_once 'includes/subscriptions/class-wc-od-subscriptions.php';
			}
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.1.0
		 */
		private function init_hooks() {
			// Install.
			register_activation_hook( __FILE__, array( 'WC_OD_Install', 'install' ) );

			// Init.
			add_action( 'plugins_loaded', array( $this, 'init' ) );

			// Data stores.
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );
		}

		/**
		 * Init plugin.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			// Load text domain.
			load_plugin_textdomain( 'woocommerce-order-delivery', false, dirname( WC_OD_BASENAME ) . '/languages' );

			// Load checkout.
			$this->checkout();

			// Load order details.
			$this->order_details();

			// Load cache.
			$this->cache();
		}

		/**
		 * Register data stores.
		 *
		 * @since 1.8.0
		 *
		 * @param array $stores Data stores.
		 * @return array
		 */
		public function register_data_stores( $stores ) {
			$stores['delivery_range'] = 'WC_OD_Data_Store_Delivery_Range';

			return $stores;
		}

		/**
		 * Displays an admin notice when the WooCommerce plugin is not active.
		 *
		 * @since 1.0.0
		 * @deprecated 1.4.1
		 */
		public function woocommerce_not_active() {
			wc_deprecated_function( __METHOD__, '1.4.1', 'This method is deprecated and will be removed in future releases.' );
		}

		/**
		 * Adds custom links to the plugins page.
		 *
		 * @since 1.0.0
		 * @deprecated 1.2.0
		 *
		 * @param array $links The plugin links.
		 * @return array The filtered plugin links.
		 */
		public function action_links( $links ) {
			wc_deprecated_function( __METHOD__, '1.2.0', 'This method is deprecated and will be removed in future releases. Use the method WC_OD_Install::plugin_action_links instead.' );

			return $links;
		}

		/**
		 * Get Settings Class.
		 *
		 * @since 1.0.0
		 *
		 * @return WC_OD_Settings
		 */
		public function settings() {
			return WC_OD_Settings::instance();
		}

		/**
		 * Get Checkout Class.
		 *
		 * @since 1.0.0
		 *
		 * @return WC_OD_Checkout
		 */
		public function checkout() {
			return WC_OD_Checkout::instance();
		}

		/**
		 * Get Order_Details Class.
		 *
		 * @since 1.0.0
		 *
		 * @return WC_OD_Order_Details
		 */
		public function order_details() {
			return WC_OD_Order_Details::instance();
		}

		public function cache() {
			return WC_OD_Delivery_Cache::instance();
		}
	}

	/**
	 * The main function for returning the plugin instance and avoiding
	 * the need to declare the global variable.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_Order_Delivery The *Singleton* instance.
	 */
	function WC_OD() {
		return WC_Order_Delivery::instance();
	}

	WC_OD();
}
