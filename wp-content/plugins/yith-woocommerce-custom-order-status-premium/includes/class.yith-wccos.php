<?php
/**
 * Main class
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCCOS' ) ) {
	/**
	 * Main Class
	 *
	 * @author YITH <plugins@yithemes.com>
	 */
	class YITH_WCCOS {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCCOS
		 */
		protected static $instance;

		/**
		 * Admin class.
		 *
		 * @var YITH_WCCOS_Admin|YITH_WCCOS_Admin_Premium
		 */
		public $admin;

		/**
		 * Frontend class.
		 *
		 * @var YITH_WCCOS_Frontend|YITH_WCCOS_Frontend_Premium
		 */
		public $frontend;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCCOS
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 */
		protected function __construct() {
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			$this->admin    = yith_wccos_admin();
			$this->frontend = yith_wccos_frontend();

			yith_wccos_integrations();
			YITH_WCCOS_Updates::get_instance();

			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );

			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

		}


		/**
		 * Load Plugin Framework
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Add email classes.
		 *
		 * @param array $emails The emails.
		 *
		 * @return array
		 */
		public function add_email_classes( $emails ) {
			$emails['YITH_WCCOS_Email'] = include YITH_WCCOS_DIR . '/includes/class.yith-wccos-email.php';

			return $emails;
		}

		/**
		 * Register plugins for activation tab.
		 *
		 * @since 1.2.3
		 */
		public function register_plugin_for_activation() {
			if ( function_exists( 'YIT_Plugin_Licence' ) ) {
				YIT_Plugin_Licence()->register( YITH_WCCOS_INIT, YITH_WCCOS_SECRET_KEY, YITH_WCCOS_SLUG );
			}
		}

		/**
		 * Register plugins for update tab.
		 *
		 * @since 1.2.3
		 */
		public function register_plugin_for_updates() {
			if ( function_exists( 'YIT_Upgrade' ) ) {
				YIT_Upgrade()->register( YITH_WCCOS_SLUG, YITH_WCCOS_INIT );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCCOS class
 *
 * @return YITH_WCCOS
 */
function yith_wccos() {
	return YITH_WCCOS::get_instance();
}
