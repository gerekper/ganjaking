<?php
/**
 * Update Data to 20160308
 *  - Migrate the followup history from the comments table to the new followup_followup_history table
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
set_time_limit( 0 );
global $wpdb;

delete_option( 'fue_needs_update' );
update_option( 'fue_db_version', '20160308' );
wp_redirect( admin_url('admin.php?page=followup-emails&tab=data_updater&act=migrate_logs') );
exit;