<?php
/**
 * Handle Page Builders.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Builders' ) ) {
	/**
	 * Builders class
	 * handle Page Builders
	 *
	 * @since 3.0.0
	 */
	class YITH_WCBK_Builders {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The Gutenberg class instance.
		 *
		 * @var YITH_WCBK_Gutenberg
		 */
		public $gutenberg;

		/**
		 * YITH_WCBK_Builders constructor.
		 */
		private function __construct() {
			$this->load();
		}

		/**
		 * Load classes.
		 */
		private function load() {
			require_once YITH_WCBK_INCLUDES_PATH . '/builders/gutenberg/class-yith-wcbk-gutenberg.php';

			$this->gutenberg = YITH_WCBK_Gutenberg::get_instance();
		}
	}
}
