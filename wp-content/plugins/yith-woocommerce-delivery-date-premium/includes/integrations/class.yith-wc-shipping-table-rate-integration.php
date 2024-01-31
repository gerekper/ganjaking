<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_Delivery_Date_WC_Shipping_Table_Rate' ) ){

    class YITH_Delivery_Date_WC_Shipping_Table_Rate{

        public function __construct()
        {
           add_filter( 'ywcdd_get_shipping_method_option', array( $this, 'get_table_rate_option_name' ), 10 ,2 );

        }

        public function get_table_rate_option_name( $shipping_option, $option_name ) {

	        if ( empty( $shipping_option ) ) {
		        $delimiter = ':';

		        if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
			        $delimiter = '_';
		        }

		        $option = explode( $delimiter, $option_name );

		        $option_name = isset( $option[2] ) ? 'table_rate' . $delimiter . $option[2] : $option_name;

		        $shipping_option = get_option( 'woocommerce_'.$option_name.'_settings' );

	        }

	        return $shipping_option;
        }



    }
}

new YITH_Delivery_Date_WC_Shipping_Table_Rate();
