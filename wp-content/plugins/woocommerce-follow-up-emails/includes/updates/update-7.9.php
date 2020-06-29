<?php

/**
 * Update Data to 7.9
 *  - move subscription lists to a separate table
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

// create the lists
$lists = $wpdb->get_col("SELECT DISTINCT email_list FROM {$wpdb->prefix}followup_subscribers");

foreach ( $lists as $list ) {
	$count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*)
		FROM {$wpdb->prefix}followup_subscriber_lists
		WHERE list_name = %s",
		$list
	));

	if ( $count == 0  ) {
		//$wpdb->insert( $wpdb->prefix .'subscribers_list')
	}

}