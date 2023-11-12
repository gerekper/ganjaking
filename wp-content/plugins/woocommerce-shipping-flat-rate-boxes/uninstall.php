<?php
/**
 * Uninstall
 *
 * Deletes the rates table
 *
 * @package woocommerce-shipping-flat-rate-boxes
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

// Tables.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}woocommerce_shipping_flat_rate_boxes" );
