<?php
defined( 'YITH_WCMBS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMBS_Builders' ) ) {
	/**
	 * Builders class
	 * handle Builders
	 *
	 * @since 1.4.0
	 */
	class YITH_WCMBS_Builders {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Builders
		 */
		private static $instance;

		/**
		 * @var YITH_WCMBS_Elementor
		 */
		public $elementor;

		/**
		 * @var YITH_WCMBS_Gutenberg
		 */
		public $gutenberg;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Builders
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCMBS_Elementor constructor.
		 */
		private function __construct() {
			$this->load();
		}

		private function load() {
			require_once YITH_WCMBS_INCLUDES_PATH . '/builders/gutenberg/class.yith-wcmbs-gutenberg.php';
			require_once YITH_WCMBS_INCLUDES_PATH . '/builders/elementor/class.yith-wcmbs-elementor.php';

			$this->gutenberg = YITH_WCMBS_Gutenberg::get_instance();
			$this->elementor = YITH_WCMBS_Elementor::get_instance();
		}

	}
}