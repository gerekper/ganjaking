<?php
/**
 * Update Compatibility
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAEL_Update' ) ) :

	/**
	 * UAEL Update initial setup
	 *
	 * @since 1.21.0
	 */
	class UAEL_Update {

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
			add_action( 'admin_init', __CLASS__ . '::init' );
		}

		/**
		 * Init
		 *
		 * @since 1.21.0
		 * @return void
		 */
		public static function init() {

			do_action( 'uael_update_before' );

			// Get auto saved version number.
			$saved_version = get_option( 'uael-version', false );

			// Update auto saved version number.
			if ( ! $saved_version ) {
				update_option( 'uael-version', UAEL_VER );
				return;
			}

			// If equals then return.
			if ( version_compare( $saved_version, UAEL_VER, '=' ) ) {
				return;
			}

			UAEL_Helper::create_specific_stylesheet();

			// Update auto saved version number.
			update_option( 'uael-version', UAEL_VER );

			do_action( 'uael_update_after' );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	UAEL_Update::get_instance();

endif;
