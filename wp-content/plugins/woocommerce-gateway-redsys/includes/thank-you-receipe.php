<?php
	
/*
* Copyright: (C) 2013 - 2021 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function redsys_show_recipe_auth( $text, $order ) {

	if ( ! empty( $order ) ) {
		$order_id = $order->get_id();
		if ( WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id ) ) {
			$numero_autorizacion = WCRed()->get_order_auth( $order_id );
			$date                = WCRed()->get_order_date( $order_id );
			$hour                = WCRed()->get_order_hour( $order_id );
			$fuc                 = WCRed()->get_redsys_option( 'customer', 'redsys' );
			$commerce_name       = WCRed()->get_redsys_option( 'commercename', 'redsys' );
			$textthabks         .= __( 'Thanks for your purchase, the details of your transaction are: ', 'woocommerce-redsys') . '<br />';
			$textthabks         .= __( 'FUC: ', 'woocommerce-redsys') . $fuc . '<br />';
			$textthabks         .= __( 'Authorization Number: ', 'woocommerce-redsys') . $numero_autorizacion . '<br />';
			$textthabks         .= __( 'Commmerce Name: ', 'woocommerce-redsys') . $commerce_name . '<br />';
			$textthabks         .= __( 'Date: ', 'woocommerce-redsys') . $date . '<br />';
			$textthabks         .= __( 'Hour: ', 'woocommerce-redsys') . $hour . '<br />';
			return $text . '<br />' . $textthabks;
		} else {
			return $text;
		}
	} else {
		return $text;
	}
}
add_filter( 'woocommerce_thankyou_order_received_text', 'redsys_show_recipe_auth', 20, 2 );
