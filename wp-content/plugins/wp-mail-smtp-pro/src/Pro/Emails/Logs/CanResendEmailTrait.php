<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\Debug;
use WPMailSMTP\Pro\Tasks\Logs\ResendTask;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;

/**
 * Resend email trait.
 *
 * @since 2.9.0
 */
trait CanResendEmailTrait {

	/**
	 * Current processing email instance (in general we need this property for setting attachments).
	 *
	 * @since 2.9.0
	 *
	 * @var Email
	 */
	protected $processing_email = null;

	/**
	 * Email resend from email log instance.
	 *
	 * @since 2.9.0
	 *
	 * @param Email $email Email instance.
	 * @param array $to    Email recipients.
	 *
	 * @return bool|\WP_Error
	 */
	protected function send_email( $email, $to = null ) {

		if ( empty( $email->get_content() ) ) {
			return new \WP_Error( 'empty_email_body', esc_html__( 'Email can\'t be resent with empty content.', 'wp-mail-smtp-pro' ) );
		}

		$this->processing_email = $email;

		if ( $to === null ) {
			$to = $email->get_people( 'to' );
		}

		// These headers will be set by PHPMailer and our mailer.
		$exclude_headers = [
			'To',
			'Date',
			'Message-ID',
			'X-Mailer',
			'X-Mailer-Type',
			'X-Msg-ID',
			'Subject',
			'Content-Type',
			'Content-Transfer-Encoding', // Fix SendGrid error.
		];

		$headers = array_filter(
			json_decode( $email->get_headers() ),
			function ( $header ) use ( $exclude_headers ) {
				return ! in_array( trim( explode( ':', $header )[0] ), $exclude_headers, true );
			}
		);

		// Set content type.
		$headers[] = 'Content-Type: ' . $email->get_content_type();

		add_action( 'phpmailer_init', [ $this, 'set_attachments' ] );
		$is_sent = wp_mail( $to, $email->get_subject(), $email->get_content(), $headers );
		remove_action( 'phpmailer_init', [ $this, 'set_attachments' ] );

		$this->processing_email = null;

		if ( $is_sent === false ) {
			return new \WP_Error( 'email_send_error', Debug::get_last() );
		}

		return $is_sent;
	}

	/**
	 * Set email attachments.
	 *
	 * @since 2.9.0
	 *
	 * @param \PHPMailer $phpmailer PHPMailer instance.
	 */
	public function set_attachments( &$phpmailer ) {

		if ( is_null( $this->processing_email ) ) {
			return;
		}

		$attachments = ( new Attachments() )->get_attachments( $this->processing_email->get_id() );

		if ( empty( $attachments ) ) {
			return;
		}

		foreach ( $attachments as $attachment ) {
			try {
				$phpmailer->addAttachment( $attachment->get_path(), $attachment->get_filename() );
			} catch ( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Schedule emails resend.
	 *
	 * @since 2.9.0
	 *
	 * @param array $email_ids Email ids.
	 */
	public function schedule_emails_send( $email_ids ) {

		$resend_task = new ResendTask();

		// Batch emails sending because it can take some time on slower web hostings or slow SMTP servers. Especially emails with attachments.
		foreach ( array_chunk( $email_ids, ResendTask::EMAILS_PER_BATCH ) as $chunk ) {
			$resend_task->schedule( $chunk );
		}
	}
}
