<?php
if( !defined( 'ABSPATH' ) ){
	exit;

}

if( !class_exists( 'YWCRBP_YITH_Subscription_Module' ) ){

	class YWCRBP_YITH_Subscription_Module{

		protected static  $_instance;
		public function __construct() {

			add_filter( 'ywcrbp_get_your_price_html', array( $this, 'ywcrbp_change_your_price_html' ), 10 ,2 );
		}


		/**
		 * @param string $price
		 * @param WC_Product $product
		 *
		 * @return string
		 */
		public function ywcrbp_change_your_price_html( $price, $product ){

			if(    YITH_WC_Subscription()->is_subscription( $product ) ){

				$price = YITH_WC_Subscription()->change_price_html( $price, $product );
			}

			return $price ;
		}


		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}
	}
}

YWCRBP_YITH_Subscription_Module::get_instance();