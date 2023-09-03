<?php

namespace WPMailSMTP\Pro\Tasks\Logs;

use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryVerification;
use WPMailSMTP\Tasks\Task;
use WPMailSMTP\Tasks\Meta;

/**
 * Class BulkVerifySentStatusTask.
 *
 * @since 3.9.0
 */
class BulkVerifySentStatusTask extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 3.9.0
	 */
	const ACTION = 'wp_mail_smtp_bulk_verify_sent_status';

	/**
	 * Emails to verify per batch count.
	 *
	 * @since 3.9.0
	 */
	const EMAILS_PER_BATCH = 5;

	/**
	 * Constructor.
	 *
	 * @since 3.9.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task.
	 *
	 * @since 3.9.0
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Perform the emails sent status verification.
	 *
	 * @since 3.9.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 */
	public function process( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive the passed parameter.
		if ( empty( $meta ) || empty( $meta->data ) || count( $meta->data ) !== 1 ) {
			return;
		}

		$email_ids = $meta->data[0];

		if ( empty( $email_ids ) ) {
			return;
		}

		foreach ( $email_ids as $email_id ) {
			$verifier = ( new DeliveryVerification() )->get_verifier( $email_id );

			if ( is_wp_error( $verifier ) ) {
				continue;
			}

			$verifier->verify();
		}
	}

	/**
	 * Schedule emails verify sent status.
	 *
	 * @since 3.9.0
	 *
	 * @param int[] $email_ids Email ids.
	 */
	public function schedule( $email_ids ) {

		// Exit if AS function does not exist.
		if ( ! function_exists( 'as_has_scheduled_action' ) ) {
			return;
		}

		// Schedule the task.
		$this->async()
			->params( $email_ids )
			->register();
	}
}
