<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'YITH_WooCommerce_Sequential_Order_Number' ) ) {

	class YITH_WooCommerce_Sequential_Order_Number {

		/**Single instance of the class
		 * @var YITH_WooCommerce_Sequential_Order_Number
		 * #@since 1.0.0
		 */
		protected static $instance;

		public function __construct() {
			// Load Plugin Framework
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			if ( is_admin() ) {
				YITH_Sequential_Order_Admin();
			}

			YWSON_Manager();
		}


		/**Returns single instance of the class
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_WooCommerce_Sequential_Order_Number
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**load plugin_fw
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 *this method is deprecated, valid for old custom codes
		 * @author Salvatore Strano
		 *
		 * @param WC_Order $order
		 *
		 * @deprecated since 1.1.0 Use YWSON_Manager()->generate_sequential_order_number
		 */
		public function create_progressive_numeration_new( $order ) {
			_deprecated_function( __METHOD__, '1.1.0', 'YWSON_Manager()->generate_sequential_order_number( $order )' );
			YWSON_Manager()->generate_sequential_order_number( $order );
		}
	}
}

function YITH_Sequential_Order_Number() {
	return YITH_WooCommerce_Sequential_Order_Number::get_instance();
}

