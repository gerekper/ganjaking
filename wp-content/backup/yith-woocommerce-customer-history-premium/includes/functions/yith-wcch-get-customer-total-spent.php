<?php

defined( 'ABSPATH' ) or exit;

/*
 *  YITH Get customer total spent
 */

if ( ! function_exists( 'yith_ch_get_customer_total_spent' ) ) {

    function yith_ch_get_customer_total_spent( $user_id ) {

    	$total_spent = 0;

    	$customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $user_id,
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys( wc_get_order_statuses() ),
            // 'post_parent' => '0',
        ) );

        foreach ( $customer_orders as $order ) {
			
			$order = new WC_Order( $order->ID );
			$total_spent += $order->get_total();
            $total_spent -= $order->get_total_refunded();

		}

		return $total_spent;

    }

}