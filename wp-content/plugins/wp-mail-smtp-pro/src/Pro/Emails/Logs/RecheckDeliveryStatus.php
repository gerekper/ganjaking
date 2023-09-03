<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryVerification;
use WPMailSMTP\Pro\Tasks\Logs\BulkVerifySentStatusTask;
use WPMailSMTP\WP;

/**
 * Class RecheckDeliveryStatus.
 *
 * @since 3.9.0
 */
class RecheckDeliveryStatus {

	/**
	 * Nonce action for rechecking a single log email delivery status.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const SINGLE_LOG_NONCE_ACTION = 'wp-mail-smtp-pro-logs-single-recheck-email-status';

	/**
	 * Nonce action for rechecking all log emails delivery status in the archive page.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const ARCHIVE_NONCE_ACTION = 'wp-mail-smtp-pro-recheck-all-email-logs-status';

	/**
	 * Hooks.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'maybe_recheck_delivery_status' ] );
		add_action( 'wp_ajax_wp_mail_smtp_recheck_all_email_logs_status', [ $this, 'ajax_recheck_all_email_logs_status' ] );
	}

	/**
	 * Attempt to recheck the delivery status of email log(s).
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	public function maybe_recheck_delivery_status() {

		if (
			! wp_mail_smtp()->get_admin()->is_admin_page( 'logs' ) ||
			! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ||
			empty( $_GET['email_id'] )
		) {
			return;
		}

		// Check if we are doing bulk recheck.
		if (
			(
				isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'recheck_email_status' ||
				isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'recheck_email_status'
			) &&
			isset( $_REQUEST['_wpnonce'] ) &&
			wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'bulk-emails' ) &&
			is_array( $_GET['email_id'] )
		) {
			$email_ids = array_map( 'absint', $_GET['email_id'] );

			$this->bulk_recheck_delivery_status( $email_ids );

			return;
		}

		if (
			! empty( $_GET['recheck_email_status'] ) &&
			wp_verify_nonce( sanitize_key( $_GET['recheck_email_status'] ), self::SINGLE_LOG_NONCE_ACTION )
		) {
			$email_id = absint( $_GET['email_id'] );

			$this->single_recheck_delivery_status( $email_id );
		}
	}

	/**
	 * Recheck the delivery status of email logs in bulk.
	 *
	 * @since 3.9.0
	 *
	 * @param int[] $email_ids Email log IDs to recheck.
	 *
	 * @return void
	 */
	private function bulk_recheck_delivery_status( $email_ids ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$bulk_verifier_task = new BulkVerifySentStatusTask();

		foreach ( array_chunk( $email_ids, BulkVerifySentStatusTask::EMAILS_PER_BATCH ) as $chunk ) {
			$bulk_verifier_task->schedule( $chunk );
		}

		WP::add_admin_notice( esc_html__( 'Re-checking email status was added to queue.', 'wp-mail-smtp-pro' ) );
	}

	/**
	 * Recheck the delivery status of a single email log.
	 *
	 * @since 3.9.0
	 *
	 * @param int $email_id Email log ID.
	 *
	 * @return void
	 */
	private function single_recheck_delivery_status( $email_id ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$verifier = ( new DeliveryVerification() )->get_verifier( $email_id );

		if ( is_wp_error( $verifier ) ) {
			WP::add_admin_notice( $verifier->get_error_message(), WP::ADMIN_NOTICE_ERROR );

			return;
		}

		$verifier->verify();

		if ( $verifier->is_verified() ) {
			WP::add_admin_notice( esc_html__( 'Email delivery status was updated.', 'wp-mail-smtp-pro' ), WP::ADMIN_NOTICE_SUCCESS );
		} else {
			WP::add_admin_notice( esc_html__( 'Unable to verify the email status at this time. Please try again later.', 'wp-mail-smtp-pro' ) );
		}
	}

	/**
	 * Handles the AJAX request to re-check all emails delivery verification status.
	 *
	 * @since 3.9.0
	 *
	 * @return void
	 */
	public function ajax_recheck_all_email_logs_status() {

		if (
			empty( $_POST['nonce'] ) ||
			! check_ajax_referer( self::ARCHIVE_NONCE_ACTION, 'nonce' ) ||
			! current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() )
		) {
			wp_send_json_error( esc_html__( 'Invalid request!', 'wp-mail-smtp-pro' ) );
		}

		$db = WP::wpdb();

		/*
		 * Get all the email IDs only.
		 */
		$results = $db->get_results(
			$db->prepare(
				'SELECT `id` FROM `%1$s` WHERE `status` = %2$d',
				Logs::get_table_name(),
				Email::STATUS_WAITING
			),
			ARRAY_A
		);

		if ( empty( $results ) ) {
			return;
		}

		$bulk_verifier_task = new BulkVerifySentStatusTask();

		foreach ( array_chunk( $results, BulkVerifySentStatusTask::EMAILS_PER_BATCH ) as $chunk ) {
			$email_ids = wp_list_pluck( $chunk, 'id' );

			$bulk_verifier_task->schedule( $email_ids );
		}

		wp_send_json_success( esc_html__( 'Re-checking email status was added to queue.', 'wp-mail-smtp-pro' ) );
	}
}
