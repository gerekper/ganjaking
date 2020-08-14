<?php
if( !defined( 'ABSPATH') ){
	exit;
}
if( !class_exists( 'YITH_Delivery_Date_Tree_Table_Rate_Shipping' ) ) {

	class YITH_Delivery_Date_Tree_Table_Rate_Shipping {

		public function __construct() {

			add_filter( 'ywcdd_get_shipping_method_option', array( $this, 'get_table_rate_option_name' ), 10, 2 );
		}


		/**
		 * @param array $shipping_option
		 * @param string $option_name
		 */
		public  function  get_table_rate_option_name( $shipping_option, $option_name ){

			if( empty( $shipping_option ) ){

				if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
					$delimiter = '_';
				}else{
					$delimiter = ':';
				}


				$option = explode( $delimiter, $option_name );

				$option_name = isset( $option[3] ) ? 'tree_table_rate' . $delimiter . $option[3] : $option_name;

				$shipping_option = get_option( 'woocommerce_'.$option_name.'_settings' );
			}

			return $shipping_option;
		}
	}
}

new YITH_Delivery_Date_Tree_Table_Rate_Shipping();