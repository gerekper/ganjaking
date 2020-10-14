<?php

/**
 * Uninstall SearchWP completely
 */

global $wpdb;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
	exit;
}

include_once dirname( __FILE__ ) . '/searchwp-metrics-i18n.php';
include_once dirname( __FILE__ ) . '/searchwp-metrics.php';
include_once dirname( __FILE__ ) . '/includes/SearchWP_Metrics.php';

$searchwp_metrics = new SearchWP_Metrics();
$searchwp_metrics->init();

/**
 * LAST CHANCE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 */
$clear_data_on_uninstall = $searchwp_metrics->get_boolean_option( 'clear_data_on_uninstall' );

if ( empty( $clear_data_on_uninstall ) ) {
	return;
}

function searchwp_metrics_remove_all_data() {
	global $wpdb;

	$metrics   = new \SearchWP_Metrics();
	$utilities = new \SearchWP_Metrics\Utilities();
	$settings  = new \SearchWP_Metrics\Settings();

	// Purge all of the data, including postmeta
	$utilities->clear_metrics_data( true );

	// Drop the tables
	foreach ( $metrics->get_db_tables() as $table ) {
		$table = $metrics->get_db_prefix() . $table;

		$wpdb->query( "DROP TABLE {$table}" );
	}

	// Clear out all stored options
	foreach ( $settings->get_option_names() as $option ) {
		delete_option( $option );
	}
}

if ( is_multisite() ) {
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $blogs ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			searchwp_metrics_remove_all_data();
			restore_current_blog();
		}
	}
} else {
	searchwp_metrics_remove_all_data();
}
