<?php

/**
 * Update Data to 7.13
 *  - Clear emails with non-existent order IDs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$order_ids = $wpdb->get_col("SELECT q.order_id FROM wp_followup_email_orders q LEFT JOIN wp_posts p ON p.ID = q.order_id WHERE q.order_id > 0 AND p.ID IS NULL");

foreach ( $order_ids as $order_id ) {
	FUE_Addon_WooCommerce_Scheduler::order_deleted( $order_id );
}