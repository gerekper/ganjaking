<?php

/**
 * Update Data to 7.17
 *  - Remove customer data for failed, cancelled and refunded orders
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$order_ids = get_posts(array(
	'post_type'     => 'shop_order',
	'post_status'   => array('wc-cancelled', 'wc-failed', 'wc-refunded'),
	'meta_query'    => array(
							array(
								'key'   => '_fue_recorded',
								'value' => 1
							)
						),
	'nopaging'      => true,
	'fields'        => 'ids'
));

if ( !$order_ids || empty( $order_ids ) ) {
	return;
}

foreach ( $order_ids as $order_id ) {
	Follow_Up_Emails::instance()->fue_wc->update_customer_data( $order_id, '', 'cancelled' );
}