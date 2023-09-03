<?php

namespace WPMailSMTP\Pro\Tasks\Logs;

use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryVerification;
use WPMailSMTP\Tasks\Meta;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Tasks\Task;

/**
 * The base VerifySentStatusTask class for all other mailer specific sent status verification tasks.
 *
 * @since 2.5.0
 */
abstract class VerifySentStatusTaskAbstract extends Task {

	/**
	 * Action name for this task.
	 *
	 * @since 2.5.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status';

	/**
	 * Number of seconds in the future to schedule the background task in.
	 *
	 * @since 2.5.0
	 */
	const SCHEDULE_TASK_IN = 180;

	/**
	 * Number of allowed tries.
	 *
	 * @since 2.5.0
	 */
	const ALLOWED_TRIES = 3;

	/**
	 * Class constructor.
	 *
	 * @since 2.5.0
	 */
	public function __construct() {

		parent::__construct( static::ACTION );
	}

	/**
	 * Initialize the task with all the proper checks.
	 *
	 * @since 2.5.0
	 */
	public function init() {

		add_action( static::ACTION, [ $this, 'process' ] );
	}

	/**
	 * Verify if the email was actually sent via the API.
	 * This will be executed in a separate process via Action Scheduler.
	 *
	 * @since 2.5.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 */
	public function process( $meta_id ) {

		$meta = $this->get_meta_data( $meta_id );

		if ( empty( $meta ) ) {
			return;
		}

		list( $email_log_id, $try ) = $meta;

		$verifier = ( new DeliveryVerification() )->get_verifier( $email_log_id );

		if ( is_wp_error( $verifier ) ) {
			return;
		}

		$verifier->verify();

		if ( ! $verifier->is_verified() ) {
			$this->maybe_retry( $email_log_id, $try, new Email( $email_log_id ) );
		}
	}

	/**
	 * Retry the sent status verification.
	 *
	 * @since 2.5.0
	 *
	 * @param int   $email_log_id The Email log ID.
	 * @param int   $try          The number of the try to perform.
	 * @param Email $email        The Email object.
	 *
	 * @throws \Exception When there was an issue with task registration.
	 */
	protected function maybe_retry( $email_log_id, $try, $email ) {

		if ( $try > static::ALLOWED_TRIES ) {
			$email->set_status( Email::STATUS_SENT );
			$email->set_error_text(
				sprintf( /* translators: %d - The number of tries. */
					esc_html__(
						'The email sent status verification timed-out after %d tries. No information on email status (delivered/failed) was provided by the mailer API.',
						'wp-mail-smtp-pro'
					),
					static::ALLOWED_TRIES
				)
			);
			$email->save();

			return;
		}

		// Run a new background task.
		( new static() )
			->params( $email_log_id, $try )
			->once( time() + ( static::SCHEDULE_TASK_IN * $try ) )
			->register();
	}

	/**
	 * Get the correct meta data information in a simple numeric array.
	 *
	 * @since 2.5.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 *
	 * @return array First item - email log ID, second item - number of tries. Empty array in case of error.
	 */
	protected function get_meta_data( $meta_id ) {

		$task_meta = new Meta();
		$meta      = $task_meta->get( (int) $meta_id );

		// We should actually receive the passed parameters.
		if ( empty( $meta ) || empty( $meta->data ) || count( $meta->data ) !== 2 ) {
			return [];
		}

		$email_log_id = (int) $meta->data[0];

		if ( empty( $email_log_id ) ) {
			return [];
		}

		return [
			$email_log_id,
			! empty( $meta->data[1] ) ? (int) $meta->data[1] + 1 : 1,
		];
	}
}
