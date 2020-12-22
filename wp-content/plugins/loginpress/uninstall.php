<?php
/**
* Uninstall LoginPress.
*
* @package loginpress
* @author WPBrigade
* @since 1.1.9
*/

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$loginpress_setting      = get_option( 'loginpress_setting' );
$loginpress_uninstall 	 = isset( $loginpress_setting['loginpress_uninstall'] ) ? $loginpress_setting['loginpress_uninstall'] : 'off';
if ( 'on' != $loginpress_uninstall ) {
	return;
}

// Load the LoginPress file.
require_once 'loginpress.php';

// Array of Plugin's Option.
$loginpress_uninstall_options = array(
	'loginpress_customization',
	'loginpress_setting',
	'loginpress_addon_active_time',
	'loginpress_addon_dismiss_1',
	'loginpress_review_dismiss',
	'loginpress_active_time',
	'_loginpress_optin',
	'loginpress_friday_sale_active_time',
	'loginpress_friday_sale_dismiss',
);

if ( ! is_multisite() ) {

	// Delete all plugin Options.
	foreach ( $loginpress_uninstall_options as $option ) {
		if ( get_option( $option ) ) {
			delete_option( $option );
		}
	}

} else {

	global $wpdb;
	$loginpress_blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	foreach ( $loginpress_blog_ids as $blog_id ) {

		switch_to_blog( $blog_id );

		// Pull the LoginPress page from options.
		$loginpress             = new LoginPress();
		$loginpress_page        = $loginpress->get_loginpress_page();
		$loginpress_page_id     = $loginpress_page->ID;

		wp_trash_post( $loginpress_page_id );

		// Delete all plugin Options.
		foreach ( $loginpress_uninstall_options as $option ) {
			if ( get_option( $option ) ) {
				delete_option( $option );
			}
		}

		restore_current_blog();
	}
}


// Clear any cached data that has been removed.
// wp_cache_flush();
