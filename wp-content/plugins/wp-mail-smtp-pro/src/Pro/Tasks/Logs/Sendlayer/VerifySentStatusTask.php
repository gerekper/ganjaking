<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Sendlayer;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the SendLayer mailer.
 *
 * @since 3.4.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 3.4.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_sendlayer';

	/**
	 * Verify if the email was actually sent via the API.
	 * This will be executed in a separate process via Action Scheduler.
	 *
	 * @since 3.4.0
	 *
	 * @param int $meta_id The Meta ID with the stored task parameters.
	 */
	public function process( $meta_id ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

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

		$message_id = $email->get_message_id();

		// Get the mailer plugin settings.
		$mailer_options = Options::init()->get_group( $email->get_mailer() );

		if ( empty( $mailer_options ) ) {
			return;
		}

		$endpoint = 'https://console.sendlayer.com/api/v1/events';

		$request_args = [
			'headers' => [
				'Authorization' => 'Bearer ' . $mailer_options['api_key'],
				'Content-Type'  => 'application/json',
			],
		];

		// Send the message GET request.
		$response = wp_safe_remote_get(
			add_query_arg(
				[
					'MessageID' => $message_id,
				],
				$endpoint
			),
			$request_args
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['Events'] ) ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		// Process the event items and check event types.
		foreach ( $body['Events'] as $event ) {
			if ( empty( $event['Event'] ) ) {
				continue;
			}

			if ( in_array( $event['Event'], [ 'rejected-by-system', 'rejected-by-mta', 'failed' ], true ) ) {
				$error_text = ! empty( $event['Reason'] ) ?
					$event['Reason'] :
					esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' );

				$email->set_status( Email::STATUS_UNSENT );
				$email->set_error_text( $error_text );
				$email->save();

				return;
			}

			if ( $event['Event'] === 'delivered' ) {
				$email->set_status( Email::STATUS_DELIVERED );
				$email->save();

				return;
			}
		}

		$this->maybe_retry( $email_log_id, $try, $email );
	}
}
