<?php

namespace WPMailSMTP\Pro\Tasks\Logs\Mailgun;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Providers\Mailgun\Mailer;
use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the Mailgun mailer.
 *
 * @since 2.5.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 2.5.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_mailgun';

	/**
	 * Verify if the email was actually sent via the API.
	 * This will be executed in a separate process via Action Scheduler.
	 *
	 * @since 2.5.0
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

		$message_id = $email->get_header( 'Message-ID' );

		// Get the mailer plugin settings.
		$mailer_options = Options::init()->get_group( $email->get_mailer() );

		// Prepare the API endpoint.
		$endpoint  = ( 'EU' === $mailer_options['region'] ) ? Mailer::API_BASE_EU : Mailer::API_BASE_US;
		$endpoint .= sanitize_text_field( $mailer_options['domain'] ) . '/events';

		try {
			$begin_timestamp = $email->get_date_sent()->getTimestamp();
		} catch ( \Exception $exception ) {
			$begin_timestamp = time();
		}

		// Send the events GET request.
		$response = wp_safe_remote_get(
			add_query_arg(
				[
					'begin'      => $begin_timestamp - ( 45 * MINUTE_IN_SECONDS ),
					'ascending'  => 'yes',
					'limit'      => 10,
					'pretty'     => 'yes',
					'message-id' => trim( $message_id, '<>' ),
				],
				$endpoint
			),
			[
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( 'api:' . $mailer_options['api_key'] ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				],
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['items'] ) ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		// Process the event items and check event types.
		foreach ( $body['items'] as $item ) {
			if ( empty( $item['event'] ) ) {
				continue;
			}

			if ( $item['event'] === 'failed' && $item['severity'] === 'permanent' ) { // Hard bounce.
				$error_text = ! empty( $item['delivery-status']['description'] ) ?
					$item['delivery-status']['description'] :
					esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' );

				$email->set_status( Email::STATUS_UNSENT );
				$email->set_error_text( $error_text );
				$email->save();

				return;
			}

			if ( $item['event'] === 'delivered' ) {
				$email->set_status( Email::STATUS_DELIVERED );
				$email->save();

				return;
			}
		}

		$this->maybe_retry( $email_log_id, $try, $email );
	}
}
