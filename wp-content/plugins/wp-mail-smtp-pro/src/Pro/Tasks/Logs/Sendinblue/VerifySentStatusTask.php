<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Sendinblue;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Providers\Sendinblue\Api;
use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;
use WPMailSMTP\Vendor\SendinBlue\Client\Model\GetEmailEventReportEvents;

/**
 * Class VerifySentStatusTask for the Sendinblue mailer.
 *
 * @since 2.5.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 2.5.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_sendinblue';

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

		// Get Email and its message ID.
		$email = new Email( $email_log_id );

		// Check if email exists (was not deleted).
		if ( $email->get_id() === 0 ) {
			return;
		}

		$message_id = $email->get_header( 'Message-ID' );

		// Send the events GET request.
		try {
			$api = new Api();

			$response = $api->get_smtp_client()->getEmailEventReport( 10, 0, null, null, 2, null, null, null, $message_id );

		} catch ( \Exception $e ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		if (
			! is_a( $response, 'WPMailSMTP\Vendor\SendinBlue\Client\Model\GetEmailEventReport' ) ||
			! method_exists( $response, 'getEvents' ) ||
			empty( $response->getEvents() )
		) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		// Sendinblue failed event types.
		$failed_event_types = [
			GetEmailEventReportEvents::EVENT_HARD_BOUNCES,
			GetEmailEventReportEvents::EVENT_BLOCKED,
			GetEmailEventReportEvents::EVENT_INVALID,
		];

		// Process the event items and check event types.
		foreach ( $response->getEvents() as $event ) {
			if ( ! is_a( $event, 'WPMailSMTP\Vendor\SendinBlue\Client\Model\GetEmailEventReportEvents' ) ) {
				continue;
			}

			$type = $event->getEvent();

			if ( in_array( $type, $failed_event_types, true ) ) {
				$reason     = $event->getReason();
				$error_text = ! empty( $reason ) ?
					$reason :
					esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' );

				$email->set_status( Email::STATUS_UNSENT );
				$email->set_error_text( $error_text );
				$email->save();

				return;
			}

			if ( GetEmailEventReportEvents::EVENT_DELIVERED === $type ) {
				$email->set_status( Email::STATUS_DELIVERED );
				$email->save();

				return;
			}
		}

		$this->maybe_retry( $email_log_id, $try, $email );
	}
}
