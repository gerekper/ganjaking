<?php
/**
 * Update Data to 20160602
 *  - Revalidate membership emails in the queue and reschedule if necessary. Fixes scheduled emails in bug #908
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
set_time_limit( 0 );
global $wpdb;

if ( !FUE_Addon_WC_Memberships::is_installed() ) {
	return;
}

$scheduler = Follow_Up_Emails::instance()->scheduler;

// get all membership email ids
$emails = fue_get_emails( 'wc_memberships', 'any', array('fields' => 'ids') );

// get all scheduled membership emails
$items = $scheduler->get_items( array(
	'is_sent'   => 0,
	'email_id'  => $emails
) );

foreach ( $items as $item ) {
	$email      = new FUE_Email( $item->email_id );
	$membership = wc_memberships_get_user_membership( $item->meta['membership_id'] );

	if ( !$membership ) {
		continue;
	}

	$start_date = $membership->get_start_date();

	$schedule = $email->get_send_timestamp( $start_date );

	if ( $schedule != $item->send_on ) {
		$item->send_on = $schedule;
		$item->save();

		$scheduler->unschedule_email( $item->id );
		$scheduler->schedule_email( $item->id, $schedule );
	}
}