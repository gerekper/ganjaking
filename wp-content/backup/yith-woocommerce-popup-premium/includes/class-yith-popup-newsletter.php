<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */


if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Popup_Newsletter' ) ) {
	/**
	 * YITH WooCommerce Popup main class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup_Newsletter {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Popup_Newsletter
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Array with accessible variables
		 */
		protected $_data = array();


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Popup_Newsletter
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

		}


		public function get_integration() {
			$integration_types = array(
				'custom' => __( 'Custom Form', 'yith-woocommerce-popup' ),
			);

			// let custom integration to appear in integration type select field
			$integration_types = apply_filters( 'yith-popup-newsletter-integration-type', $integration_types );

			return $integration_types;
		}

	}

	/**
	 * Unique access to instance of YITH_Popup class
	 *
	 * @return \YITH_Popup_Newsletter
	 */
	function YITH_Popup_Newsletter() {
		return YITH_Popup_Newsletter::get_instance();
	}

	YITH_Popup_Newsletter();
}

