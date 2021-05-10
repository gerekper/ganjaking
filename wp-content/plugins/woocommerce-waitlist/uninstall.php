<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // if uninstall not called from WordPress exit
}

require_once 'definitions.php';

if ( 'yes' === get_option( WCWL_SLUG . '_remove_all_data' ) ) {
	delete_metadata( 'post', null, WCWL_SLUG, null, true );
	delete_metadata( 'post', null, 'wcwl_waitlist_archive', null, true );
	delete_metadata( 'post', null, WCWL_SLUG . '_count', null, true );
	delete_metadata( 'post', null, '_' . WCWL_SLUG . '_count', null, true );
	delete_metadata( 'post', null, WCWL_SLUG . '_has_dates', null, true );
	delete_metadata( 'post', null, 'wcwl_options', null, true );
	delete_metadata( 'post', null, 'wcwl_stock_level', null, true );
	delete_metadata( 'post', null, 'wcwl_mailout_errors', null, true );
	delete_metadata( 'user', null, WCWL_SLUG, null, true );
	delete_metadata( 'user', null, '_' . WCWL_SLUG, null, true );
	delete_metadata( 'user', null, 'wcwl_languages', null, true );
	delete_option( WCWL_SLUG );
	delete_option( WCWL_SLUG . '_registration_needed' );
	delete_option( WCWL_SLUG . '_archive_on' );
	delete_option( WCWL_SLUG . '_remove_all_data' );
	delete_option( '_' . WCWL_SLUG . '_languages' );
	delete_option( '_' . WCWL_SLUG . '_counts_updated' );
	delete_option( '_' . WCWL_SLUG . '_metadata_updated' );
	delete_option( '_' . WCWL_SLUG . '_corrupt_data' );
	delete_option( '_' . WCWL_SLUG . '_version_2_warning' );
}
