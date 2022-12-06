<?php

/**
 * Update int values to string in array meta.
 *
 * @return bool
 */
function yith_wcmbs_update_140_int_to_string_array_meta() {
	global $wpdb;

	$empty_array  = serialize( array() );
	$limit        = 50;
	$search_query = $wpdb->prepare( "SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_linked-plans', '_yith_wcmbs_restrict_access_plan') AND meta_value != '' AND meta_value != %s AND meta_value NOT LIKE %s LIMIT %d", $empty_array, '%;s:%', $limit );
	$metas        = $wpdb->get_results( $search_query );

	if ( ! $metas ) {
		// Stop the execution, since there are no more meta to update
		return false;
	}

	foreach ( $metas as $meta ) {
		$value = maybe_unserialize( $meta->meta_value );
		$value = serialize( array_map( 'strval', $value ) );

		$query = $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_id = %d", $value, $meta->meta_id );
		$wpdb->query( $query );
	}

	// Next execution!
	return true;
}

/**
 * Update version 1.4.0
 */
function yith_wcmbs_update_140_db_version() {
	YITH_WCMBS_Install::update_db_version( '1.4.0' );
}

/**
 * Clear previously scheduled events, so they'll be re-created by using the correct time-zone (to be executed at midnight of the size time-zone).
 */
function yith_wcmbs_update_1_18_0_clear_scheduled_events() {
	wp_clear_scheduled_hook( 'yith_wcmbs_check_expiring_membership' );
	wp_clear_scheduled_hook( 'yith_wcmbs_check_expired_membership' );
	wp_clear_scheduled_hook( 'yith_wcmbs_check_credits_in_membership' );
	wp_clear_scheduled_hook( 'yith_wcmbs_delete_transients_cron' );
}

/**
 * Update version 1.18.0
 */
function yith_wcmbs_update_1_18_0_db_version() {
	YITH_WCMBS_Install::update_db_version( '1.18.0' );
}
