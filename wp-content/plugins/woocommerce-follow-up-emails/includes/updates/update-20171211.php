<?php
/**
 * Update Data to 20171211
 *  - Alter schema of wp_followup_coupon_logs.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
set_time_limit( 0 );
global $wpdb;

// The purpose of this file is to trigger the dbDelta call on FUE install.

delete_option( 'fue_needs_update' );
update_option( 'fue_db_version', '20171211' );

