<?php

/**
 * Remove all SearchWP Related data
 */

global $wpdb;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function searchwp_related_maybe_nuke() {
	global $wpdb;

	$is_multisite = is_multisite() && function_exists( 'get_current_site' ) ? get_current_site() : null;

	if ( apply_filters( 'searchwp_related_nuke_on_delete', false, $is_multisite ) ) {
		$wpdb->delete( $wpdb->prefix . 'postmeta', array(
			'meta_key' => 'searchwp_related'
		) );
		$wpdb->delete( $wpdb->prefix . 'postmeta', array(
			'meta_key' => 'searchwp_related_skip'
		) );

		delete_option( 'searchwp_related' );
	}
}

if ( is_multisite() ) {
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $blogs ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			searchwp_related_maybe_nuke();
			restore_current_blog();
		}
	}
} else {
	searchwp_related_maybe_nuke();
}
