<?php
/**
 * Update Data to 7.19.4
 *  - Update the lifetime purchase total for all customers
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
set_time_limit( 0 );
global $wpdb;
if ( !Follow_Up_Emails::instance()->is_woocommerce_installed() ) {
	return;
}
$wpdb->query("UPDATE {$wpdb->prefix}followup_customers SET total_purchase_price = 0");
delete_option( 'fue_needs_update' );
update_option( 'fue_db_version', '7.19.4' );
wp_redirect( admin_url('admin.php?page=followup-emails&tab=data_update&act=sum_orders') );
exit;