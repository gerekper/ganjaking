<?php

/**
 * Handles email sending
 */
class WC_Booking_Email_Manager {

	/**
	 * Constructor sets up actions
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'init_emails' ) );

		add_filter( 'woocommerce_email_attachments', array( $this, 'attach_ics_file' ), 10, 3 );

		add_filter( 'woocommerce_template_directory', array( $this, 'template_directory' ), 10, 2 );

		add_action( 'init', array( $this, 'bookings_email_actions' ) );
	}

	/**
	 * Include our mail templates
	 *
	 * @param  array $emails
	 * @return array
	 */
	public function init_emails( $emails ) {
		if ( ! isset( $emails['WC_Email_New_Booking'] ) ) {
			$emails['WC_Email_New_Booking'] = new WC_Email_New_Booking();
		}

		if ( ! isset( $emails['WC_Email_Booking_Reminder'] ) ) {
			$emails['WC_Email_Booking_Reminder'] = new WC_Email_Booking_Reminder();
		}

		if ( ! isset( $emails['WC_Email_Booking_Confirmed'] ) ) {
			$emails['WC_Email_Booking_Confirmed'] = new WC_Email_Booking_Confirmed();
		}

		if ( ! isset( $emails['WC_Email_Booking_Pending_Confirmation'] ) ) {
			$emails['WC_Email_Booking_Pending_Confirmation'] = new WC_Email_Booking_Pending_Confirmation();
		}

		if ( ! isset( $emails['WC_Email_Booking_Notification'] ) ) {
			$emails['WC_Email_Booking_Notification'] = new WC_Email_Booking_Notification();
		}

		if ( ! isset( $emails['WC_Email_Booking_Cancelled'] ) ) {
			$emails['WC_Email_Booking_Cancelled'] = new WC_Email_Booking_Cancelled();
		}

		if ( ! isset( $emails['WC_Email_Admin_Booking_Cancelled'] ) ) {
			$emails['WC_Email_Admin_Booking_Cancelled'] = new WC_Email_Admin_Booking_Cancelled();
		}

		return $emails;
	}

	/**
	 * Attach the .ics files in the emails.
	 *
	 * @param  array  $attachments
	 * @param  string $email_id
	 * @param  mixed  $booking
	 *
	 * @return array
	 */
	public function attach_ics_file( $attachments, $email_id, $booking ) {
		$available = apply_filters( 'woocommerce_bookings_emails_ics', array( 'booking_confirmed', 'booking_reminder' ) );

		if ( in_array( $email_id, $available ) ) {
			$generate = new WC_Bookings_ICS_Exporter;
			$attachments[] = $generate->get_booking_ics( $booking );
		}

		return $attachments;
	}

	/**
	 * Custom template directory.
	 *
	 * @param  string $directory
	 * @param  string $template
	 *
	 * @return string
	 */
	public function template_directory( $directory, $template ) {
		if ( false !== strpos( $template, '-booking' ) ) {
			return 'woocommerce-bookings';
		}

		return $directory;
	}

	/**
	 * Bookings email actions for transactional emails.
	 *
	 * @since   1.10.5
	 * @version 1.10.5
	 */
	public function bookings_email_actions() {
		// Email Actions
		$email_actions = apply_filters( 'woocommerce_bookings_email_actions', array(
			// New & Pending Confirmation
			'woocommerce_booking_in-cart_to_paid',
			'woocommerce_booking_in-cart_to_pending-confirmation',
			'woocommerce_booking_unpaid_to_paid',
			'woocommerce_booking_unpaid_to_pending-confirmation',
			'woocommerce_booking_confirmed_to_paid',
			'woocommerce_admin_new_booking',
			'woocommerce_admin_confirmed',

			// Confirmed
			'woocommerce_booking_confirmed',

			// Pending Confirmation
			'woocommerce_booking_pending-confirmation',

			// Cancelled
			'woocommerce_booking_pending-confirmation_to_cancelled',
			'woocommerce_booking_confirmed_to_cancelled',
			'woocommerce_booking_paid_to_cancelled',
			'woocommerce_booking_unpaid_to_cancelled',
		));

		foreach ( $email_actions as $action ) {
			add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		}
	}
}
