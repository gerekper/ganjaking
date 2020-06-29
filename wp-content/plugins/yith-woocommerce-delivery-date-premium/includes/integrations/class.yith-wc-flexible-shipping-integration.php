<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_Delivery_Date_WC_Flexible_Shipping' ) ){
    
    class YITH_Delivery_Date_WC_Flexible_Shipping{
        
        public function __construct()
        {
           add_filter( 'ywcdd_get_shipping_method_option', array( $this, 'get_flexible_shipping_option_name' ), 10 ,2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'hide_custom_fields' ),99 );
        }

        public function get_flexible_shipping_option_name( $shipping_option, $option_name ) {

	        if ( empty( $shipping_option ) ) {
		        $delimiter = ':';

		        if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
			        $delimiter = '_';
		        }

		        $option = explode( $delimiter, $option_name );

		        $option_name = isset( $option[2] ) ? 'flexible_shipping' . $delimiter . $option[2] : $option_name;
		        $shipping_option = get_option( 'woocommerce_'.$option_name.'_settings' );

	        }

	        return $shipping_option;
        }

        public function hide_custom_fields(){

            if(  !empty( $_GET['instance_id'] )  && ( !empty( $_GET['tab'] ) && 'shipping' == $_GET['tab'] ) ){

                $script = "jQuery(document).ready(function($){
        		        var flexible_shipping_table = $(document).find('table.flexible_shipping_method_rules');
        		      
        		        if( flexible_shipping_table.length ){
        		            
        		            var processing_option = $(document).find('select.ywcdd_processing_method'),
        		                set_mandatory = $(document).find('.ywcdd_set_mandatory' );
        		                
        		                if( processing_option.length ){
        		                
        		                    processing_option.closest('tr').remove();
        		                }
        		                
        		                if( set_mandatory.length){
        		                    set_mandatory.closest('tr').remove();
        		                }
        		        }
        		        
        		    });";


                wp_add_inline_script('woocommerce_admin', $script );
            }
        }



    }
}

new YITH_Delivery_Date_WC_Flexible_Shipping();