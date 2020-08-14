<?php
/**
 * Uninstall plugin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Color and Label Variations Premium
 * @version 1.0.0
 */

// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete option from options table
delete_option( 'yith_wccl_db_version' );

//remove custom table
$table_name = $wpdb->prefix . 'yith_wccl_meta';
$sql = "DROP TABLE $table_name";
$wpdb->query( $sql );

//change to standard select type custom attributes
$table = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
$update = "UPDATE `$table` SET `attribute_type` = 'select' WHERE `attribute_type` NOT LIKE 'text'";
$wpdb->query( $update );