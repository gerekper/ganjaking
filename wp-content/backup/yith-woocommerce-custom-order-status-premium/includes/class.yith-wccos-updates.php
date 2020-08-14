<?php
! defined( 'YITH_WCCOS' ) && exit; // Exit if accessed directly

require_once 'functions.yith-wccos-updates.php';
if ( ! class_exists( 'YITH_WCCOS_Updates' ) ) {
	/**
	 * Class YITH_WCCOS
	 *
	 * @since 1.1.11
	 */
	class YITH_WCCOS_Updates {
		/** @var YITH_WCCOS_Updates */
		private static $_instance;

		/** @var array */
		private $_update_callbacks;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCCOS_Updates
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_WCCOS_Updates constructor.
		 */
		private function __construct() {
			$this->_update_callbacks = array(
				'1.1.11' => array( 'yith_wccos_update_1_1_11_sendmail_to_recipients' ),
			);

			$this->maybe_update();
		}

		public function maybe_update() {
			if ( $this->needs_db_update() ) {
				$this->update();
			}
		}

		public function needs_db_update() {
			$current_db_version = get_option( 'yith_wccos_db_version', '' );
			$updates            = $this->get_update_callbacks();

			return version_compare( $current_db_version, max( array_keys( $updates ) ), '<' );
		}

		public function get_update_callbacks() {
			return $this->_update_callbacks;
		}

		private function update() {
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