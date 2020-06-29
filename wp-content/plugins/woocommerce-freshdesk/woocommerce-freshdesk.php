<?php
/**
 * Plugin Name: WooCommerce Freshdesk
 * Plugin URI: https://woocommerce.com/products/woocommerce-freshdesk/
 * Description: A Freshdesk integration plugin for WooCommerce.
 * Version: 1.1.25
 * Author: Automattic
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-freshdesk
 * Domain Path: languages/
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 * Tested up to: 5.3
 *
 * @package woocommerce-freshdesk
 *
 * Woo: 395305:31cb841311e1657f69861c452d788726
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Freshdesk' ) ) :

	define( 'WC_FRESHDESK_VERSION', '1.1.25' ); // WRCS: DEFINED_VERSION.

	/**
	 * WooCommerce Freshdesk main class.
	 */
	class WC_Freshdesk {

		/**
		 * Integration id.
		 *
		 * @var string
		 */
		protected static $integration_id = 'freshdesk';

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
			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

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
		 * @return object A single instance of this class.
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
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
		 * Load the plugin text domain for translation.
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-freshdesk' );

			load_textdomain( 'woocommerce-freshdesk', trailingslashit( WP_LANG_DIR ) . 'woocommerce-freshdesk/woocommerce-freshdesk-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-freshdesk', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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

	add_action( 'plugins_loaded', array( 'WC_Freshdesk', 'get_instance' ) );

endif;
