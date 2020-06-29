<?php
if( !defined( 'ABSPATH' ) ){
	exit;

}

if( !class_exists( 'YWCRBP_YITH_Event_Ticket_Module' ) ){

	class YWCRBP_YITH_Event_Ticket_Module{

		protected static  $_instance;
		public function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'include_scripts' ) );
			add_action( 'wp_ajax_show_role_based_info', array( $this, 'show_role_based_info' ) );
			add_action( 'wp_ajax_nopriv_show_role_based_info', array( $this, 'show_role_based_info' ) );

		}


		public function include_scripts(){

			if( is_product() ){

				$params = array(
					'ajax_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'  => array(
							'show_role_based_info' => 'show_role_based_info'
					)
				);

				wp_register_script( 'ywcrbp_event_ticket_module', YWCRBP_ASSETS_URL.'js/'.yit_load_js_file( 'ywcrbp_event_ticket.js' ) );

				wp_localize_script( 'ywcrbp_event_ticket_module', 'ywcrb_event_ticket', $params );
				wp_enqueue_script( 'ywcrbp_event_ticket_module' );
			}
		}


		public function show_role_based_info(){

			$price_html = '';
			if( !empty( $_REQUEST['product_id'] ) && !empty( $_REQUEST['price'] )  ) {

				$price = $_REQUEST['price'];
				$product_id = $_REQUEST['product_id'];
				$product = wc_get_product( $product_id );
				$price_html = YITH_Role_Based_Prices_Product()->get_total_discount_markup_formatted( $product );

			}
			wp_send_json( array( 'price_html'=> $price_html  ) );
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

YWCRBP_YITH_Event_Ticket_Module::get_instance();