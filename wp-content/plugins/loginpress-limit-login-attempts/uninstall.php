<?php

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * Run some custom tasks on plugin uninstall
 *
 * @since 1.0.1
 */
function loginpress_limit_login_uninstall() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		global $wpdb;

			// Get this so we can switch back to it later
			$current_blog = $wpdb->blogid;
			// Get all blogs in the network and delete table for each blog
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			$blog_settings   = get_option( 'loginpress_limit_login_attempts' );
			$is_delete_table = isset( $blog_settings['delete_data'] ) ? $blog_settings['delete_data'] : '';

			if ( 'on' == $is_delete_table ) {
				// Delete table for blog id.
				drop_loginpress_limit_login_attempts_details_table();
			}
		}
			switch_to_blog( $current_blog );
			return;

	} else {
		$settings        = get_option( 'loginpress_limit_login_attempts' );
		$is_delete_table = isset( $settings['delete_data'] ) ? $settings['delete_data'] : '';
		if ( 'on' == $is_delete_table ) {
			// Normal deactivation delete table.
			drop_loginpress_limit_login_attempts_details_table();
		}
	}

}

loginpress_limit_login_uninstall();

/**
 * Delete table
 *
 * @since 1.0.1
 */
function drop_loginpress_limit_login_attempts_details_table() {

	global $wpdb;
	// Table name.
	$table_name = "{$wpdb->prefix}loginpress_limit_login_details";
	// Drop table if exist.
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

}
