<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_WRBP_Compatibilities' ) ) {

	class YITH_WRBP_Compatibilities {

		protected static $_instance;


		public function __construct() {


            require_once( 'class.yith-ywrbp-AeliaCS-module.php' );

			if ( defined( 'YITH_WPV_PREMIUM' ) ) {

				require_once( 'class.compatibility-role-based-prices-multivendor.php' );
			}

			if( defined( 'YITH_YWSBS_PREMIUM' ) ){
				require_once( 'class.ywrbp-subscription-module.php' );
			}

			if( defined( 'YITH_WCEVTI_PREMIUM' ) ){

				require_once( 'class.yith-ywrbp-event-tickets-module.php' );
			}

			if( defined( 'YITH_YWDPD_PREMIUM' )){
				require_once('class.ywrbp-dynamic-pricing.php');
			}


		}

		/**
		 * Returns single instance of the class
		 * @author YITHEMES
		 * @return \YITH_WRBP_Compatibilities
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
/**
 * @return YITH_WRBP_Compatibilities
 */
function YITH_WRBP_Compatibilities() {
	return YITH_WRBP_Compatibilities::get_instance();
}

YITH_WRBP_Compatibilities();