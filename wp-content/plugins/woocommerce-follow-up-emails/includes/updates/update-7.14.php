<?php

/**
 * Update Data to 7.14
 *  - Convert 'pending' triggers for booking emails to 'pending-confirmation'
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$wpdb->query("UPDATE {$wpdb->postmeta} SET meta_value = 'booking_status_pending-confirmation' WHERE meta_key = '_interval_type' AND meta_value = 'booking_status_pending'");