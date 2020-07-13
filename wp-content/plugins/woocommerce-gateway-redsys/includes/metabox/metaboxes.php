<?php

/*
* Copyright: (C) 2013 - 2020 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Copyright: (C) 2013 - 2020 José Conti
*/
function add_redsys_meta_box() {
	if (  WCRed()->is_redsys_order( get_the_ID() ) ) {
		
		$date = WCRed()->get_order_date( get_the_ID() );
		$hour = WCRed()->get_order_hour( get_the_ID() );
		$auth = WCRed()->get_order_auth( get_the_ID() );
		
		echo '<h4>' . esc_html__('Payment Details', 'woocommerce-redsys') . '</h4>';
		echo '<p><strong>' . esc_html__( 'Paid with', 'woocommerce-redsys' ) . ': </strong><br />' . WCRed()->get_gateway( get_the_ID() )  . '</p>';
		if ( $date ) {
			echo '<p><strong>' . esc_html__( 'Redsys Date', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $date ) . '</p>';
		}
		
		if ( $hour ) {
			echo '<p><strong>' . esc_html__( 'Redsys Hour', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $hour ) . '</p>';
		}
		
		if ( $auth ) {
			echo '<p><strong>' . esc_html__( 'Redsys Authorisation Code', 'woocommerce-redsys' ) . ': </strong><br />' . esc_html( $auth ) . '</p>';
		}
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'add_redsys_meta_box' );
