<?php
	
/*
* Copyright: (C) 2013 - 2020 José Conti
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function redsys_show_recipe_auth( $text, $order ) {

	if ( ! empty( $order ) ) {
		$order_id = $order->get_id();
		if ( WCRed()->is_paid( $order_id ) && WCRed()->is_redsys_order( $order_id ) ) {
			$numero_autorizacion = get_post_meta( $order_id, '_authorisation_code_redsys', true );
			$textthabks          = __( 'Thanks for your purchase, the authorization number at Redsys is ', 'woocommerce-redsys') . $numero_autorizacion;
			return $text . '<br />' . $textthabks;
		} else {
			return $text;
		}
	} else {
		return $text;
	}
}
add_filter( 'woocommerce_thankyou_order_received_text', 'redsys_show_recipe_auth', 20, 2 );
