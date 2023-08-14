<?php

/**
 * Uninstall SearchWP completely
 */

global $wpdb;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once __DIR__ . '/index.php';

function searchwp_maybe_uninstall() {
	global $wpdb;

	if ( empty( apply_filters( 'searchwp\nuke_on_delete', get_option( SEARCHWP_PREFIX . 'nuke_on_delete' ) ) ) ) {
		return;
	}

	$indexer = new \SearchWP\Indexer();
	$index   = new \SearchWP\Index\Controller();

	$indexer->_uninstall();
	$index->_uninstall();

	// Delete all settings stored as options.
	foreach( \SearchWP\Settings::get_keys() as $key ) {
		\SearchWP\Settings::delete( $key );
	}

	// Delete misc data.
	delete_site_option( SEARCHWP_PREFIX . 'last_health_check' );

	// Delete all settings stored as usermeta.
	$wpdb->query( $wpdb->prepare( "
		DELETE FROM {$wpdb->usermeta}
		WHERE meta_key = %s
	", SEARCHWP_PREFIX . 'settings_view_config' ) );
}

if ( is_multisite() ) {
	$sites = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $sites ) {
		foreach ( $sites as $site ) {
			switch_to_blog( $site['blog_id'] );
			searchwp_maybe_uninstall();
			restore_current_blog();
		}
	}
} else {
	searchwp_maybe_uninstall();
}
