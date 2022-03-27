<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Postmark;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the Postmark mailer.
 *
 * @since 3.3.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 3.3.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_postmark';

	/**
	 * Verify if the email was actually sent via the API.
	 * This will be executed in a separate process via Action Scheduler.
	 *
	 * @since 3.3.0
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

		$api_base_url = 'https://api.postmarkapp.com';

		$request_args = [
			'headers' => [
				'X-Postmark-Server-Token' => $mailer_options['server_api_token'],
				'Content-Type'            => 'application/json',
			],
		];

		// Send the message GET request.
		$response = wp_safe_remote_get( "$api_base_url/messages/outbound/$message_id/details", $request_args );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		$message = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $message['MessageEvents'] ) ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		// Process the event items and check event types.
		foreach ( $message['MessageEvents'] as $event ) {
			if ( empty( $event['Type'] ) ) {
				continue;
			}

			if ( $event['Type'] === 'Bounced' ) {
				$error_text = esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' );

				if ( isset( $event['Details']['BounceID'] ) ) {

					// Get bounce details.
					$response = wp_safe_remote_get( "$api_base_url/bounces/{$event['Details']['BounceID']}", $request_args );

					if ( wp_remote_retrieve_response_code( $response ) === 200 ) {
						$bounce = json_decode( wp_remote_retrieve_body( $response ), true );

						// Try later if email was not hard bounced.
						if ( isset( $bounce['Type'] ) && $bounce['Type'] !== 'HardBounce' ) {
							$this->maybe_retry( $email_log_id, $try, $email );

							return;
						}

						if ( ! empty( $bounce['Description'] ) ) {
							$error_text = $bounce['Description'];
						}
					}
				}

				$email->set_status( Email::STATUS_UNSENT );
				$email->set_error_text( $error_text );
				$email->save();

				return;
			}

			if ( $event['Type'] === 'Delivered' ) {
				$email->set_status( Email::STATUS_DELIVERED );
				$email->save();

				return;
			}
		}

		$this->maybe_retry( $email_log_id, $try, $email );
	}
}
