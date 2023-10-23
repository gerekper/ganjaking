<?php
/**
 * Main class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Extended' ) ) {
	/**
	 * YITH WooCommerce Ajax Navigation
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Extended extends YITH_WCAN {

		/**
		 * Constructor
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function __construct() {
			// Require Premium Files.
			add_filter( 'yith_wcan_required_files', array( $this, 'require_additional_files' ) );

			// Add premium filters type.
			add_filter( 'yith_wcan_supported_filter_designs', array( $this, 'supported_designs' ) );

			parent::__construct();
		}

		/**
		 * Init plugin, by creating main objects
		 *
		 * @return void
		 * @since  1.4
		 */
		public function init() {
			// do startup operations.
			YITH_WCAN_Install_Extended::init();

			// init general classes.
			YITH_WCAN_Presets();
			YITH_WCAN_Sessions();
			YITH_WCAN_Cron();

			// init shortcodes.
			YITH_WCAN_Shortcodes::init();

			// init widgets.
			YITH_WCAN_Widgets::init();

			// init specific classes.
			if ( is_admin() ) {
				$this->admin = new YITH_WCAN_Admin_Extended();
			} else {
				$this->frontend = new YITH_WCAN_Frontend_Extended();
			}
		}

		/**
		 * Add require premium files
		 *
		 * @param array $files Files to include.
		 *
		 * @return array Filtered array of files to include
		 * @since 1.3.2
		 */
		public function require_additional_files( $files ) {
			$files[] = 'class-yith-wcan-install-extended.php';
			$files[] = 'class-yith-wcan-cron.php';
			$files[] = 'class-yith-wcan-session.php';
			$files[] = 'class-yith-wcan-sessions.php';
			$files[] = 'class-yith-wcan-session-factory.php';
			$files[] = 'class-yith-wcan-query-extended.php';
			$files[] = 'class-yith-wcan-admin-extended.php';
			$files[] = 'class-yith-wcan-frontend-extended.php';
			$files[] = 'data-stores/class-yith-wcan-session-data-store.php';

			return $files;
		}

		/**
		 * Add additional filter designs
		 *
		 * @param array $supported_designs Array of supported designs.
		 * @return array Filtered array of supported designs.
		 */
		public function supported_designs( $supported_designs ) {
			$supported_designs = yith_wcan_merge_in_array(
				$supported_designs,
				array(
					'radio' => _x( 'Radio', '[Admin] Filter edit form', 'yith-woocommerce-ajax-navigation' ),
				),
				'checkbox'
			);

			return $supported_designs;
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_WCAN|YITH_WCAN_Premium Main instance
		 */
		public static function instance() {
			if ( class_exists( 'YITH_WCAN_Premium' ) ) {
				return YITH_WCAN_Premium::instance();
			} else {
				if ( is_null( self::$instance ) ) {
					self::$instance = new self();
				}

				return self::$instance;
			}
		}
	}
}

if ( ! function_exists( 'YITH_WCAN_Extended' ) ) {
	/**
	 * Return single instance for YITH_WCAN_Extended class
	 *
	 * @return YITH_WCAN_Extended
	 * @since 4.0.0
	 */
	function YITH_WCAN_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
		return YITH_WCAN_Extended::instance();
	}
}