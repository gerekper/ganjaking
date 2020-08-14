<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}

if( !class_exists( 'YITH_Delivery_Date_WC_FedEx' ) ){
    
    class YITH_Delivery_Date_WC_FedEx{
        
        public function __construct()
        {
           add_filter( 'ywcdd_get_shipping_method_option', array( $this, 'get_fedex_option_name' ), 10 ,2 );
           add_action( 'admin_enqueue_scripts', array( $this, 'hide_custom_fields' ),99 );

        }

        public function get_fedex_option_name( $shipping_option, $option_name ) {


	        if ( empty( $shipping_option ) ) {
		        $delimiter = ':';

		        if( version_compare( WC()->version,'2.6.0','>=' ) ){
			        $delimiter = '_';
		        }

		        $option = explode( $delimiter, $option_name );


		        if( !empty( $option[0] ) && 'fedex' == strtolower( $option[0] ) ){
			        $shipping_option = get_option( 'woocommerce_fedex_settings' );
		        }


	        }

	        return $shipping_option;
        }

        public function hide_custom_fields(){


        	if( !empty( $_GET['instance_id'] ) && ( !empty( $_GET['tab'] ) && 'shipping' == $_GET['tab'] ) ){

        		$script = "jQuery(document).ready(function($){
        		        var fedex_table = $(document).find('table.fedex_services');
        		        
        		        if( fedex_table.length ){
        		            
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

new YITH_Delivery_Date_WC_FedEx();