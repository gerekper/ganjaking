<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If uninstall not called from WordPress exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	global $wpdb;

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wc_pao_%'" );
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_product_addons%'" );
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_all_products'" );
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_priority'" );
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_product_addons_exclude_global'" );
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_product_addons_old'" );
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '%_product_addons_converted'" );
}
