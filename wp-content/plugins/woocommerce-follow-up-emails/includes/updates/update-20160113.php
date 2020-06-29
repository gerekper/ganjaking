<?php
/**
 * Update Data to 20160113
 *  - Update the lifetime purchase total for all customers
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
set_time_limit( 0 );
global $wpdb;

$wpdb->query("DROP INDEX order_id ON {$wpdb->prefix}followup_customer_orders");
$wpdb->query("ALTER IGNORE TABLE {$wpdb->prefix}followup_customer_orders ADD UNIQUE INDEX order_id (order_id)");

$customer_ids = $wpdb->get_col("SELECT DISTINCT id FROM {$wpdb->prefix}followup_customers");

foreach ( $customer_ids as $id ) {
	$num_orders = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}followup_customer_orders WHERE followup_customer_id = $id");
	$wpdb->update(
		$wpdb->prefix .'followup_customers',
		array('total_orders' => $num_orders),
		array('id' => $id)
	);
}