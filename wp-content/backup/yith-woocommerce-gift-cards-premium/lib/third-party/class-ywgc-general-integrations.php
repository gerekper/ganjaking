<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWGC_General_Integrations' ) ) {

	/**
	 *
	 * @class   YWGC_General_Integrations
	 *
	 * @since   1.0.0
	 * @author Lorenzo Giuffrida
	 */
	class YWGC_General_Integrations {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

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

		public function __construct() {
			/**
			 * YITH Quick View. Let the YITH Gift Cards be enqueued in page other than the single product page
			 */
			add_filter( 'yith_ywgc_do_eneuque_frontend_scripts', array(
				$this,
				'enqueue_script_wide'
			) );
		}

		public function enqueue_script_wide() {
			return defined( 'YITH_WCQV_PREMIUM' ) && ( is_shop() || is_product_category() || is_product_tag() );
		}
	}
}

YWGC_General_Integrations::get_instance();