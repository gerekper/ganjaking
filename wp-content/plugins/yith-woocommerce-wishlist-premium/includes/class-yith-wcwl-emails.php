<?php
/**
 * Emails class
 *
 * @package YITH\GiftCards\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_WCWL_Emails' ) ) {
	/**
	 * YITH_WCWL_Emails class
	 *
	 * @since   1.0.0
	 * @author  YITH
	 */
	class YITH_WCWL_Emails {

		/**
		 * Plugin emails array
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $emails = array();

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCWL_Emails
		 * @since 1.0.0
		 */
		public static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author YITH
		 */
		public function __construct() {}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Emails class
 *
 * @return YITH_WCWL_Emails|YITH_WCWL_Emails_Premium|YITH_WCWL_Emails_Extended
 * @since 2.0.0
 */
function YITH_WCWL_Emails() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	if ( defined( 'YITH_WCWL_PREMIUM' ) ) {
		$instance = YITH_WCWL_Emails_Premium::get_instance();
	} elseif ( defined( 'YITH_WCWL_EXTENDED' ) ) {
		$instance = YITH_WCWL_Emails_Extended::get_instance();
	} else {
		$instance = YITH_WCWL_Emails::get_instance();
	}

	return $instance;
}
