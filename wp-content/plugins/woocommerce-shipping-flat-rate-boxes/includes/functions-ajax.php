<?php
/**
 * Ajax functions collection.
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delete box via ajax function
 */
add_action( 'wp_ajax_woocommerce_flat_rate_box_delete', 'woocommerce_flat_rate_box_delete' );

/**
 * Delete flat rate box.
 *
 * @return void
 */
function woocommerce_flat_rate_box_delete() {
	check_ajax_referer( 'delete-box', 'security' );

	if ( ! empty( $_POST['box_id'] ) ) {
		$box_ids = is_array( $_POST['box_id'] ) ? array_map( 'intval', wp_unslash( $_POST['box_id'] ) ) : array( intval( $_POST['box_id'] ) );

		global $wpdb;
		$placeholders = implode( ', ', array_fill( 0, count( $box_ids ), '%d' ) );

		$wpdb->query(
		// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Repeated arguments.
			$wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_shipping_flat_rate_boxes WHERE box_id IN ({$placeholders})", ...$box_ids )
		// phpcs:enable
		);
	}

	die();
}
