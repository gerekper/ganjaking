<?php

/**
 * Update FUE Data to 7.3
 *
 * Scan all email coupons in _followup_email_coupons and store those as email meta in _postmeta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$email_coupons = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}followup_email_coupons");

foreach ( $email_coupons as $email_coupon ) {
	update_post_meta( $email_coupon->email_id, '_send_coupon', $email_coupon->send_coupon );
	update_post_meta( $email_coupon->email_id, '_coupon_id', $email_coupon->coupon_id );
}

$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}followup_email_coupons");