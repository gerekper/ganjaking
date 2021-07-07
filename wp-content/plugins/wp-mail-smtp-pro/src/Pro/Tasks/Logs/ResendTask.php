<?php

namespace WPMailSMTP\Pro\Tasks\Logs;

use WPMailSMTP\Tasks\Task;
use WPMailSMTP\Tasks\Meta;
use WPMailSMTP\Pro\Emails\Logs\CanResendEmailTrait;
use WPMailSMTP\Pro\Emails\Logs\EmailsCollection;

/**
 * Class ResendTask.
 *
 * @since 2.9.0
 */
class ResendTask extends Task {

	use CanResendEmailTrait;

	/**
	 * Action name for this task.
	 *
	 * @since 2.9.0
	 */
	const ACTION = 'wp_mail_smtp_resend_emails';

	/**
	 * Emails per batch count.
	 *
	 * @since 2.9.0
	 */
	const EMAILS_PER_BATCH = 5;

	/**
	 * Class constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {

		parent::__construct( self::ACTION );
	}

	/**
	 * Initialize the task.
	 *
	 * @since 2.9.0
	 */
	public function init() {

		// Register the action handler.
		add_action( self::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Schedule emails resend.
	 *
	 * @since 2.9.0
	 *
	 * @param int[] $email_ids Email ids.
	 */
	public function schedule( $email_ids ) {

		// Exit if AS function does not exist.
		if ( ! function_exists( 'as_next_scheduled_action' ) ) {
			return;
		}

		// Schedule the task.
		$this->async()
			->params( $email_ids )
			->register();
	}

	/**
	 * Perform the emails resend.
	 *
	 * @since 2.9.0
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

		$collection = new EmailsCollection(
			[
				'ids'      => $email_ids,
				'per_page' => count( $email_ids ),
			]
		);

		foreach ( $collection->get() as $email ) {
			$this->send_email( $email );
		}
	}
}
