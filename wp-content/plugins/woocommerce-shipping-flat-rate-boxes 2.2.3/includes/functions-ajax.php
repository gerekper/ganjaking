<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delete box via ajax function
 */
add_action('wp_ajax_woocommerce_flat_rate_box_delete', 'woocommerce_flat_rate_box_delete');

function woocommerce_flat_rate_box_delete() {
	check_ajax_referer( 'delete-box', 'security' );

	if ( is_array( $_POST['box_id'] ) ) {
		$box_ids = array_map( 'intval', $_POST['box_id'] );
	} else {
		$box_ids = array( intval( $_POST['box_id'] ) );
	}

	if ( ! empty( $box_ids ) ) {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_shipping_flat_rate_boxes WHERE box_id IN (" . implode( ',', $box_ids ) . ")" );
	}

	die();
}
