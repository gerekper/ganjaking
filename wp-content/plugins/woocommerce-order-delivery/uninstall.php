<?php
/**
 * WooCommerce Order Delivery Uninstall
 *
 * Deletes the plugin options.
 *
 * @package WC_OD
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * Plugin uninstall script.
 *
 * @since 1.0.0
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 */
function wc_od_uninstall() {
	global $wpdb;

	// Deletes the plugin options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wc_od_%';" );
}
wc_od_uninstall();
