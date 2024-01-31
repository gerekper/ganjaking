<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_Delivery_Date_WC_Distance_Rate_Shipping' ) ){
    
    class YITH_Delivery_Date_WC_Distance_Rate_Shipping{
        
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


		        if( strpos( $option_name, 'legacy_'  )!==false ){
		        	$option_name = str_replace( 'legacy_', '', $option_name );
		        }


		        $shipping_option = get_option( 'woocommerce_'.$option_name.'_settings' );

	        }

	        return $shipping_option;
        }



    }
}

new YITH_Delivery_Date_WC_Distance_Rate_Shipping();