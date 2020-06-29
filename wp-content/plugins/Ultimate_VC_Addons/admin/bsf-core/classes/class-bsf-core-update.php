<?php
/**
 * BSF Core Update
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        http://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! class_exists( 'BSF_Core_Update' ) ) {

	/**
	 * BSF_Core_Update initial setup
	 *
	 * @since 1.0.0
	 */
	class BSF_Core_Update {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 *  Constructor
		 */
		public function __construct() {
			// Theme Updates.
			add_action( 'admin_init', __CLASS__ . '::init', 0 );
		}

		/**
		 * Implement theme update logic.
		 *
		 * @since 1.0.0
		 */
		public static function init() {
			do_action( 'astra_update_before' );

			// Get auto saved version number.
			$saved_version = get_option( 'bsf-updater-version', false );

			// If equals then return.
			if ( version_compare( $saved_version, BSF_UPDATER_VERSION, '=' ) ) {
				return;
			}

			// // Update auto saved version number.
			update_option( 'bsf-updater-version', BSF_UPDATER_VERSION );

			do_action( 'astra_update_after' );
		}

	}

}

/**
 * Kicking this off by calling 'get_instance()' method
 */
BSF_Core_Update::get_instance();
