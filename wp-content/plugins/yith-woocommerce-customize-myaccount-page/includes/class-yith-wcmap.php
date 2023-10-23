<?php
/**
 * Main class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP', false ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP
		 */
		protected static $instance;

		/**
		 * Items class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Items
		 */
		public $items = null;

		/**
		 * Admin class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Admin
		 */
		public $admin = null;

		/**
		 * Frontend class instance
		 *
		 * @since 1.0.0
		 * @var YITH_WCMAP_Frontend
		 */
		public $frontend = null;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return YITH_WCMAP
		 */
		public static function get_instance() {
			if ( is_null( static::$instance ) ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), '1.0.0' );
		}

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		protected function __construct() {
			// Do install process.
			YITH_WCMAP_Install::install();
			$this->load_classes();
		}

		/**
		 * Load required classes
		 *
		 * @since 3.0.0
		 * @return void
		 */
		protected function load_classes() {
			$this->items = new YITH_WCMAP_Items();
			// Class admin.
			if ( $this->is_admin() ) {
				$class       = $this->get_admin_class();
				$this->admin = class_exists( $class ) ? new $class() : null;
			} else { // Class frontend.
				$class          = $this->get_frontend_class();
				$this->frontend = class_exists( $class ) ? new $class() : null;
			}
		}

		/**
		 * Get admin class instance
		 *
		 * @since 3.12.0
		 * @return null|YITH_WCMAP_Admin
		 */
		public function get_admin() {
			return $this->admin;
		}

		/**
		 * Get admin class
		 *
		 * @since 3.12.0
		 * @return string
		 */
		protected function get_admin_class() {
			return 'YITH_WCMAP_Admin';
		}

		/**
		 * Get frontend class instance
		 *
		 * @since 3.12.0
		 * @return null|YITH_WCMAP_Frontend
		 */
		public function get_frontend() {
			return $this->frontend;
		}

		/**
		 * Get frontend class
		 *
		 * @since 3.12.0
		 * @return string
		 */
		protected function get_frontend_class() {
			return 'YITH_WCMAP_Frontend';
		}

		/**
		 * Check if is admin or not and load the correct class
		 *
		 * @since  1.1.2
		 * @return bool
		 */
		public function is_admin() {
			$check_ajax    = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$check_context = isset( $_REQUEST['context'] ) && 'frontend' === sanitize_text_field( wp_unslash( $_REQUEST['context'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$is_admin      = is_admin() && ! ( $check_ajax && $check_context );

			/**
			 * APPLY_FILTERS: yith_wcmap_is_admin_request
			 *
			 * Filter whether the current request has been made for an admin page.
			 *
			 * @param bool $is_admin Whether the current request has been made for an admin page or not.
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcmap_is_admin_request', $is_admin );
		}
	}
}
