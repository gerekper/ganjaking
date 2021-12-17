<?php

/**
 * Description of A2W_ShippingMethod.
 * woocommerce_shipping_init
 * @author Mikhail
 * 
 * @include_action: plugins_loaded
 */
    
    if ( A2W_Woocommerce::is_woocommerce_installed() ) :
        if ( ! class_exists( 'A2W_ShippingMethod' ) ) :
        
            class A2W_ShippingMethod extends WC_Shipping_Method {
                
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct($instance_id = 0) {

                    $this->id                 = 'a2w';

                    $this->method_title       = 'ali2woo';
            
                    $this->method_description = 'ali2woo';
         
                    
                    //todo: here we can check our option
                    $this->enabled = 'yes';
                    
                
                }                       

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping( $package = array() ) {    
                }
     
            }
            
        endif;


        add_action('a2w_install', 'a2w_reset_wc_shipping_method_count');

        function a2w_reset_wc_shipping_method_count(){
            delete_transient('wc_shipping_method_count_legacy');
            delete_transient('wc_shipping_method_count');
        }

        if ( a2w_get_setting('aliship_frontend') ) :

            //set woocommerce_enable_shipping_calc to "yes"
            //to show the shipping calculator in the cart
            update_option( 'woocommerce_enable_shipping_calc', 'yes', 'no');

            add_filter( 'woocommerce_shipping_methods', 'add_a2w_shipping_method' );

            function add_a2w_shipping_method( $methods ) {
               
                //we need at least one shipping method to show shipping calculator in the cart
                $methods[] = 'A2W_ShippingMethod';
                return $methods;
            }

        endif;
         
    endif;
