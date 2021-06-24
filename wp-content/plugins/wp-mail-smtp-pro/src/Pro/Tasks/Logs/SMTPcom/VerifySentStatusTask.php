<?php

namespace WPMailSMTP\Pro\Tasks\Logs\SMTPcom;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Tasks\Logs\VerifySentStatusTaskAbstract;

/**
 * Class VerifySentStatusTask for the SMTP.com mailer.
 *
 * @since 2.5.0
 */
class VerifySentStatusTask extends VerifySentStatusTaskAbstract {

	/**
	 * Action name for this task.
	 *
	 * @since 2.5.0
	 */
	const ACTION = 'wp_mail_smtp_verify_sent_status_smtpcom';

	/**
	 * Number of seconds in the future to schedule the background task in.
	 *
	 * @since 2.5.0
	 */
	const SCHEDULE_TASK_IN = 480;

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

		$message_id = $email->get_header( 'X-Msg-ID' );

		// Get the mailer plugin settings.
		$mailer_options = Options::init()->get_group( $email->get_mailer() );

		if ( empty( $mailer_options ) ) {
			return;
		}

		try {
			$begin_timestamp = $email->get_date_sent()->getTimestamp();
		} catch ( \Exception $exception ) {
			$begin_timestamp = time();
		}

		// Send the message GET request.
		$response = wp_safe_remote_get(
			add_query_arg(
				[
					'channel' => $mailer_options['channel'],
					'start'   => $begin_timestamp - ( 45 * MINUTE_IN_SECONDS ),
					'limit'   => 10,
					'offset'  => 0,
					'msg_id'  => $message_id,
				],
				'https://api.smtp.com/v4/messages'
			),
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $mailer_options['api_key'],
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				],
			]
		);

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['data']['items'] ) ) {
			$this->maybe_retry( $email_log_id, $try, $email );

			return;
		}

		// Process the message items and check event types.
		foreach ( $body['data']['items'] as $item ) {
			if ( empty( $item['details']['delivery']['event'] ) ) {
				continue;
			}

			if ( in_array( $item['details']['delivery']['event'], [ 'failed', 'bounced' ], true ) ) {
				$error_text = ! empty( $item['details']['delivery']['status'] ) ?
					$item['details']['delivery']['status'] :
					esc_html__( 'The email failed to be delivered. No specific reason was provided by the API.', 'wp-mail-smtp-pro' );

				$email->set_status( Email::STATUS_UNSENT );
				$email->set_error_text( $error_text );
				$email->save();

				return;
			}

			if ( $item['details']['delivery']['event'] === 'delivered' ) {
				$email->set_status( Email::STATUS_DELIVERED );
				$email->save();

				return;
			}
		}

		$this->maybe_retry( $email_log_id, $try, $email );
	}
}
