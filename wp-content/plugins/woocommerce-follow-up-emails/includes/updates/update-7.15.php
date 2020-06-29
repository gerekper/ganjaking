<?php

/**
 * Update Data to 7.15
 *  - Clear invalid daily summary entries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$wpdb->query("DELETE FROM {$wpdb->prefix}followup_email_orders WHERE send_on = '86400' AND email_trigger = 'Daily Summary'");