<?php

defined( 'ABSPATH' ) or exit;

/*
 *  YITH Bot Detected
 */

if ( ! function_exists( 'yith_wcch_updated_version' ) ) {

	function yith_wcch_updated_version() {

		global $wpdb;

		$db_version = get_option( 'yith_wcch_db_version' );

		// 1.0.6
		if ( version_compare( $db_version, '1.0.6', '<') ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}yith_wcch_sessions ADD ip VARCHAR(250) after user_id" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}yith_wcch_sessions ADD referer VARCHAR(250) after url" );
			update_option( 'yith_wcch_db_version', '1.0.6' );
		}

	}

}