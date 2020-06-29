<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSRewardGatewayFrontend' ) ) {

    class RSRewardGatewayFrontend {

        public static function init() {
            add_action( 'woocommerce_checkout_update_order_meta' , array( __CLASS__ , 'update_cart_subtotal' ) , 10 , 2 ) ;
        }

        public static function update_cart_subtotal( $order_id , $data ) {
            $cart_subtotal = srp_cart_subtotal( true ) ;
            update_post_meta( $order_id , 'rs_cart_subtotal' , $cart_subtotal ) ;
        }

    }

    RSRewardGatewayFrontend::init() ;
}