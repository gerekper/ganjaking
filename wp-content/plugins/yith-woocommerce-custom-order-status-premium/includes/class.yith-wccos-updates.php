<?php
/**
 * Updates class.
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCCOS_Updates' ) ) {
	/**
	 * Class YITH_WCCOS_Updates
	 *
	 * @since 1.1.11
	 */
	class YITH_WCCOS_Updates {
		/**
		 * Single instance of the class.
		 *
		 * @var YITH_WCCOS_Updates
		 */
		private static $instance;

		/**
		 * Call-back functions.
		 *
		 * @var array
		 */
		private $update_callbacks;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCCOS_Updates
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCCOS_Updates constructor.
		 */
		private function __construct() {
			$this->update_callbacks = array(
				'1.1.11' => array( 'yith_wccos_update_1_1_11_sendmail_to_recipients' ),
			);

			$this->maybe_update();
		}

		/**
		 * Maybe update.
		 */
		public function maybe_update() {
			if ( $this->needs_db_update() ) {
				$this->update();
			}
		}

		/**
		 * Needs update?
		 *
		 * @return bool|int
		 */
		public function needs_db_update() {
			$current_db_version = get_option( 'yith_wccos_db_version', '' );
			$updates            = $this->get_update_callbacks();

			return version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
		}

		/**
		 * Get update callback functions.
		 *
		 * @return array
		 */
		public function get_update_callbacks() {
			return $this->update_callbacks;
		}

		/**
		 * Update.
		 */
		private function update() {
			require_once 'functions.yith-wccos-updates.php';

			$current_db_version = get_option( 'yith_wccos_db_version', '' );
			$updates            = $this->get_update_callbacks();

			foreach ( $updates as $version => $update_callbacks ) {
				if ( version_compare( $current_db_version, $version, '<' ) ) {
					foreach ( $update_callbacks as $update_callback ) {
						if ( function_exists( $update_callback ) ) {
							call_user_func( $update_callback );
						}
					}
				}
			}

			update_option( 'yith_wccos_db_version', max( array_keys( $updates ) ) );
		}
	}
}
