<?php
/**
* Uninstall LoginPress - Social Login.
*
* @package loginpress
* @author WPBrigade
* @since 1.0.5
*/

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}


/**
* Run special routine on uninstall
* @since 1.0.5
*/
function loginpress_social_uninstall( ) {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		global $wpdb;

			// Get this so we can switch back to it later.
			$current_blog = $wpdb->blogid;
			// Get all blogs in the network and delete table for each blog.
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			$blog_settings        = get_option( 'loginpress_social_logins' );
			$is_delete_user_table = isset( $blog_settings['delete_user_data'] ) ? $blog_settings['delete_user_data'] : '';

			if ( 'on' == $is_delete_user_table ) {
				drop_loginpress_social_login_details_table();  // User table for blog ID.
			}
		}
			switch_to_blog( $current_blog );
			return;

	} else {
    $settings             = get_option( 'loginpress_social_logins' );
		$is_delete_user_table = isset( $settings['delete_user_data'] ) ? $settings['delete_user_data'] : '';
		if ( 'on' == $is_delete_user_table ) {
			drop_loginpress_social_login_details_table(); // normal deactivaton  delete table
		}
	}
}

loginpress_social_uninstall();
/**
 * Delete user table.
 * @since 1.0.5
 */
	function drop_loginpress_social_login_details_table() {

	global $wpdb;
	// tbale name
	$table_name = "{$wpdb->prefix}loginpress_social_login_details";
	// drop table if exist
	$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
