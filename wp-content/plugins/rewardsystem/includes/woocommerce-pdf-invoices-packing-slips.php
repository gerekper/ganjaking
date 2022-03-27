<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSWCPdfPackingslips' ) ) {

	class RSWCPdfPackingslips {

		public static function init() {
			add_action( 'wpo_wcpdf_after_order_details' , array( __CLASS__ , 'display_earned_redeemed_message' ) , 10 , 2 ) ;
		}

		public static function display_earned_redeemed_message( $template, $order ) {
			if ( 'no' == get_option( 'rs_product_purchase_activated' ) ) {
				return ;
			}

			//Getting Order details
			$order_object = srp_order_obj( $order ) ;                        
			$order_id     = isset($order_object[ 'order_id' ]) ?$order_object[ 'order_id' ]:0 ;
						
			$earned_and_redeemed_point = get_earned_redeemed_points_message( $order_id , true ) ;
			if (!srp_check_is_array($earned_and_redeemed_point)) {
				return;
			}
						
			$earned_point              = implode( ',' , array_keys( $earned_and_redeemed_point ) ) ;
			$redeemed_point            = implode( ',' , array_values( $earned_and_redeemed_point ) ) ;

			//Getting Earned/Redeeming messages in PDF
			$replacemsgforearnedpoints = 0 != $earned_point ? str_replace( '[earnedpoints]' , round_off_type( $earned_point ) , get_option( 'rs_msg_for_earned_points' ) ) : '' ;
			$replacemsgforredeempoints = 0 != $redeemed_point ? str_replace( '[redeempoints]' , round_off_type( $redeemed_point) , get_option( 'rs_msg_for_redeem_points' ) ) : '' ;

			//Displaying Earned/Redeeming messages in PDF
			if ( 'yes' == get_option( 'rs_enable_msg_for_earned_points' ) ) {
				if ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) ) {
					echo wp_kses_post('<h3>' . $replacemsgforearnedpoints . '</h3><br>' );
					echo wp_kses_post('<h3>' . $replacemsgforredeempoints . '</h3>' );
				} else {
					echo wp_kses_post('<h3>' . $replacemsgforearnedpoints . '</h3>') ;
				}
			} else {
				if ( 'yes' == get_option( 'rs_enable_msg_for_redeem_points' ) ) {
					echo wp_kses_post('<h3>' . $replacemsgforredeempoints . '</h3>' );
				}
			}
		}

	}

}

RSWCPdfPackingslips::init() ;
