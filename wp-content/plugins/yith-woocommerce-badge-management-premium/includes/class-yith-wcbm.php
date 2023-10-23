<?php
/**
 * Main class
 *
 * @package YITH\BadgeManagement\Classes
 * @author  YITH <plugins@yithemes.com>
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
			add_action( 'current_screen', array( $this, 'maybe_load_frontend' ) );
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_cpt_admin_class' ), 20 );

			// Declare WooCommerce supported features.
			add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

			YITH_WCBM_Post_Types::init();
			yith_wcbm_badges();

			yith_wcbm_install_class();

			if ( is_admin() && ( ! isset( $_REQUEST['action'] ) || ( isset( $_REQUEST['action'] ) && 'yith_load_product_quick_view' !== $_REQUEST['action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$this->admin = yith_wcbm_admin();
			}

			$is_ajax_request = defined( 'DOING_AJAX' ) && DOING_AJAX;
			if ( ! is_admin() || $is_ajax_request ) {
				$this->frontend = yith_wcbm_frontend();
			}

			yith_wcbm_compatibility();
		}

		/**
		 * Load frontend class if gutenberg is used
		 */
		public function maybe_load_frontend() {
			if ( ! $this->frontend ) {
				$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
				if ( $screen && $screen->is_block_editor() ) {
					$this->frontend = yith_wcbm_frontend();
					$this->frontend->enqueue_scripts();
				}
			}
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
		 * Load CPT Admin Class
		 */
		public function load_cpt_admin_class() {
			require_once YITH_WCBM_DIR . '/includes/class-yith-wcbm-badge-post-type-admin.php';
		}

		/**
		 * Declare support for WooCommerce features.
		 *
		 * @since 2.18.0
		 */
		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) && defined( 'YITH_WCBM_FREE_INIT' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', YITH_WCBM_FREE_INIT );
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
