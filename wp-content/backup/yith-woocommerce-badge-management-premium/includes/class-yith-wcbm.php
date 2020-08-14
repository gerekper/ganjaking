<?php
/**
 * Main class
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH WooCommerce Badge Management
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM' ) ) {
	/**
	 * YITH_WCBM class
	 */
	class YITH_WCBM {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM
		 */
		protected static $instance;

		/**
		 * Admin object
		 *
		 * @var YITH_WCBM_Admin|YITH_WCBM_Admin_Premium
		 */
		public $admin;

		/**
		 * Frontend object
		 *
		 * @var YITH_WCBM_Frontend|YITH_WCBM_Frontend_Premium
		 */
		public $frontend;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCBM|YITH_WCBM_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			YITH_WCBM_Post_Types::init();

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( is_admin() && ( ! isset( $_REQUEST['action'] ) || ( isset( $_REQUEST['action'] ) && 'yith_load_product_quick_view' !== $_REQUEST['action'] ) ) ) {
				$this->admin = yith_wcbm_admin();
			}

			$is_ajax_request = defined( 'DOING_AJAX' ) && DOING_AJAX;
			if ( ! is_admin() || $is_ajax_request ) {
				$this->frontend = yith_wcbm_frontend();
			}

			yith_wcbm_compatibility();
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
	}
}

/**
 * Unique access to instance of YITH_WCBM class
 *
 * @return YITH_WCBM|YITH_WCBM_Premium
 * @since 1.0.0
 */
function yith_wcbm() {
	return YITH_WCBM::get_instance();
}
