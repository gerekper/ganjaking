<?php

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/*
 * Only remove ALL product and page data if WC_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'WC_REMOVE_ALL_DATA' ) && true === WC_REMOVE_ALL_DATA ) {
	global $wpdb;

	$tables = array();

	$results = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}followup_%'", 'ARRAY_N' );

	foreach ( $results as $row ) {
	    $tables[] = $row[0];
	}

	foreach ( $tables as $tbl ) {
	    $wpdb->query( "DROP TABLE `$tbl`" );
	}

	// Delete the pages.
	wp_delete_post( get_option( 'fue_followup_unsubscribe_page_id' ), true );
	wp_delete_post( get_option( 'fue_followup_my_subscriptions_page_id' ), true );

	delete_option( 'woocommerce_followup_unsubscribe_page_id' );
	delete_option( 'woocommerce_followup_db_version' );

	$installer = include( 'includes/class-fue-install.php' );
	$installer->remove_roles();

	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'fue_%'" );

	// Delete posts + data
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'follow_up_emails'" );

	// Delete email_history entries (comments)
	$wpdb->query( "DELETE FROM {$wpdb->comments} WHERE comment_type = 'email_history'" );

	// Delete action scheduler data
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_title IN ('fue_send_summary', 'sfn_send_usage_report', 'sfn_followup_emails');" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
}
