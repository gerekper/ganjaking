<?php

/**
 * Update FUE Data to 7.0
 *
 * Convert all existing emails to use the new FollowUpEmail post type
 * and store metadata in the postmeta table.
 *
 * Update all email_id references to use the new POST ID
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$table_check = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}followup_emails'");

if ( count( $table_check ) ) {
	// convert emails to use the new post type
	$emails = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}followup_emails");
	$status_map = array(
		-1  => FUE_Email::STATUS_ARCHIVED,
		0   => FUE_Email::STATUS_INACTIVE,
		1   => FUE_Email::STATUS_ACTIVE
	);

	foreach ( $emails as $email ) {

		$args = array(
			'name'              => $email->name,
			'type'              => $email->email_type,
			'subject'           => $email->subject,
			'message'           => $email->message,
			'status'            => $status_map[ $email->status ],
			'priority'          => $email->priority,
			'product_id'        => $email->product_id,
			'category_id'       => $email->category_id,
			'interval_num'      => $email->interval_num,
			'interval_duration' => $email->interval_duration,
			'interval_type'     => $email->interval_type,
			'send_date'         => $email->send_date,
			'send_date_hour'    => $email->send_date_hour,
			'send_date_minute'  => $email->send_date_minute,
			'tracking_code'     => $email->tracking_code,
			'usage_count'       => $email->usage_count,
			'always_send'       => $email->always_send,
			'meta'              => $email->meta
		);

		$old_email_id = $email->id;
		$new_email_id = fue_create_email( $args );

		// search for all instances of the old email ID and replace them with the new ID
		if ( is_int( $new_email_id ) ) {
			$wpdb->update(
				$wpdb->prefix .'followup_email_coupons',
				array( 'email_id' => $new_email_id ),
				array( 'email_id' => $old_email_id )
			);

			$wpdb->update(
				$wpdb->prefix .'followup_email_excludes',
				array( 'email_id' => $new_email_id ),
				array( 'email_id' => $old_email_id )
			);

			$wpdb->update(
				$wpdb->prefix .'followup_email_logs',
				array( 'email_id' => $new_email_id ),
				array( 'email_id' => $old_email_id )
			);

			$wpdb->update(
				$wpdb->prefix .'followup_email_orders',
				array( 'email_id' => $new_email_id ),
				array( 'email_id' => $old_email_id )
			);

			$wpdb->update(
				$wpdb->prefix .'followup_email_tracking',
				array( 'email_id' => $new_email_id ),
				array( 'email_id' => $old_email_id )
			);
		}

	}

}

