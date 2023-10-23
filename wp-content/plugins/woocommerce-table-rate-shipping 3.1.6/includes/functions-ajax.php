<?php
/**
 * Ajax Functions collection.
 *
 * @package WooCommerce_Table_Rat_Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delete rate via ajax function
 */
add_action( 'wp_ajax_woocommerce_table_rate_delete', 'woocommerce_table_rate_delete' );

/**
 * Delete table rate.
 *
 * @return void
 */
function woocommerce_table_rate_delete() {
	check_ajax_referer( 'delete-rate', 'security' );

	if ( ! empty( $_POST['rate_id'] ) ) {
		$rate_ids = is_array( $_POST['rate_id'] ) ? array_map( 'intval', wp_unslash( $_POST['rate_id'] ) ) : array( intval( $_POST['rate_id'] ) );

		global $wpdb;

		$placeholders = implode( ', ', array_fill( 0, count( $rate_ids ), '%d' ) );

		$wpdb->query(
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Repeated arguments.
			$wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_shipping_table_rates WHERE rate_id IN ({$placeholders})", ...$rate_ids )
		// phpcs:enable
		);
	}

	die();
}
