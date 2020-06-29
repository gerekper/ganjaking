<?php
/**
 * Update Data to 20160203
 *  - Delete all action-scheduler entries without a queue item
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
set_time_limit( 0 );
global $wpdb;

delete_option( 'fue_needs_update' );
update_option( 'fue_db_version', '20160203' );
wp_redirect( admin_url('admin.php?page=followup-emails&tab=data_updater&act=clear_scheduler') );
exit;