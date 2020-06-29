<?php

/**
 * Update Data to 7.18
 *  - Force the  WC order importer to run again to fix the total purchase price inconsistencies for customers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_order_items" );
$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_customers" );
$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_order_categories" );
$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_customer_orders" );
$wpdb->query( "DELETE FROM {$wpdb->prefix}followup_customer_notes" );

$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key = '_fue_recorded'");

delete_option( 'fue_order_imported' );
delete_option( 'fue_needs_update' );

update_option( 'fue_db_version', '7.18' );

wp_redirect( admin_url('admin.php?page=followup-emails&tab=order_import') );
exit;