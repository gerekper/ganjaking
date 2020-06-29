<?php

/**
 * Update Data to 7.6
 *  - Reset the roles to attach more capabilities to the fue_manager role
 *  - Merge fue_customers with duplicate user_ids
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$installer = include( FUE_INC_DIR .'/class-fue-install.php' );
$installer->remove_roles();
$installer->create_role();

global $wpdb;

$results = $wpdb->get_results("SELECT user_id, COUNT(user_id) AS occ FROM {$wpdb->prefix}followup_customers WHERE user_id > 0 GROUP BY user_id HAVING occ > 1");

foreach ( $results as $row ) {
	$customers  = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}followup_customers WHERE user_id = %d", $row->user_id));
	$new_id             = 0;
	$total_purchases    = 0;
	$total_orders       = 0;

	foreach ( $customers as $customer ) {
		if ( $new_id === 0 ) {
			$new_id             = $customer->id;
			$total_purchases    = $customer->total_purchase_price;
			$total_orders       = $customer->total_orders;
		} else {
			$total_purchases    += $customer->total_purchase_price;
			$total_orders       += $customer->total_orders;

			$wpdb->update( $wpdb->prefix .'followup_customer_orders', array( 'followup_customer_id' => $new_id ), array( 'followup_customer_id' => $customer->id ) );
			$wpdb->delete( $wpdb->prefix .'followup_customers', array('id' => $customer->id) );
		}
	}

	$wpdb->update( $wpdb->prefix .'followup_customers', array(
		'total_purchase_price'  => $total_purchases,
		'total_orders'          => $total_orders
	), array( 'id' => $new_id ) );
}

