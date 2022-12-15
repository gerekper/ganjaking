<?php

namespace WPMailSMTP\Pro\Emails\Logs\Providers;

use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Emails\Logs\Attachments\Attachments;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Webhooks;
use WPMailSMTP\Providers\MailerAbstract;

/**
 * Class Common to handle saving to log all emails sent by mailers.
 *
 * @since 1.5.0
 */
class Common {

	/**
	 * @since 1.5.0
	 */
	const MAILER = 'gmail';

	/**
	 * The MailCatcher object.
	 *
	 * @since 1.5.0
	 *
	 * @var MailCatcherInterface
	 */
	private $mailcatcher;

	/**
	 * The Mailer object.
	 *
	 * @since 1.5.0
	 *
	 * @var MailerAbstract
	 */
	private $mailer;

	/**
	 * Common constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param MailerAbstract $mailer The Mailer object.
	 */
	public function __construct( MailerAbstract $mailer = null ) {

		$this->mailer = $mailer;
	}

	/**
	 * Preserve the cloned instance of the MailCatcher class.
	 *
	 * @since 1.5.0
	 *
	 * @param MailCatcherInterface $mailcatcher The MailCatcher object.
	 *
	 * @return Common
	 */
	public function set_source( MailCatcherInterface $mailcatcher ) {

		$this->mailcatcher = clone $mailcatcher;

		return $this;
	}

	/**
	 * Save the actual email data before email send.
	 *
	 * @since 2.9.0
	 *
	 * @param int $parent_email_id Parent email log ID.
	 *
	 * @return int
	 */
	public function save_before( $parent_email_id = 0 ) {

		$mailer_slug = wp_mail_smtp()->get_connections_manager()->get_mail_connection()->get_mailer_slug();
		$headers     = explode( $this->mailcatcher->get_line_ending(), $this->mailcatcher->createHeader() );
		$attachments = count( $this->mailcatcher->getAttachments() );
		$people      = $this->get_people();
		$email_id    = 0;

		try {
			$email = new Email();

			$email
				->set_subject( $this->mailcatcher->Subject )
				->set_people( $people )
				->set_headers( array_filter( $headers ) )
				->set_attachments( $attachments )
				->set_mailer( $mailer_slug )
				->set_status( Email::STATUS_UNSENT )
				->set_initiator()
				->set_parent_id( $parent_email_id );

			if ( wp_mail_smtp()->pro->get_logs()->is_enabled_content() ) {
				$email
					->set_content_plain( $this->mailcatcher->ContentType === 'text/plain' ? $this->mailcatcher->Body : $this->mailcatcher->AltBody )
					->set_content_html( $this->mailcatcher->ContentType !== 'text/plain' ? $this->mailcatcher->Body : '' );
			}

			$email_id = $email->save()->get_id();
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Do nothing for now.
		}

		// Return the state.
		return $email_id;
	}

	/**
	 * Save or update the actual email data after email send.
	 *
	 * @since 1.5.0
	 * @since 2.9.0 Added $email_log parameter.
	 *
	 * @param int $email_id Email log ID.
	 *
	 * @return int
	 */
	public function save( $email_id = 0 ) {

		$headers     = explode( $this->mailcatcher->get_line_ending(), $this->mailcatcher->createHeader() );
		$attachments = count( $this->mailcatcher->getAttachments() );
		$people      = $this->get_people();

		try {
			$email = new Email( $email_id );
			$email
				->set_subject( $this->mailcatcher->Subject )
				->set_people( $people )
				->set_headers( array_filter( $headers ) )
				->set_attachments( $attachments )
				->set_mailer( $this->mailer->get_mailer_name() )
				->set_status( $this->get_email_status() )
				->set_message_id( $this->get_message_id() );

			// Set the email error if the email was not sent.
			if ( $email->has_failed() ) {
				$email->set_error_text( $this->mailer->get_response_error() );
			}

			$email_id = $email->save()->get_id();

			// Save attachments to the email log.
			( new Attachments() )->process_attachments( $email_id, $this->mailcatcher->getAttachments() );
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Do nothing for now.
		}

		return $email_id;
	}

	/**
	 * Get email status after email send.
	 *
	 * @since 3.3.0
	 *
	 * @return int Email status.
	 */
	public function get_email_status() {

		$status = Email::STATUS_UNSENT;

		if ( $this->mailer->is_email_sent() ) {
			$status = $this->mailer->should_verify_sent_status() ? Email::STATUS_WAITING : Email::STATUS_SENT;
		}

		return $status;
	}

	/**
	 * Get the people data from the MailCatcher object.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	private function get_people() {

		$people = [];

		foreach ( $this->mailcatcher->getToAddresses() as $to ) {
			$people['to'][] = $to[0];
		}
		foreach ( $this->mailcatcher->getCcAddresses() as $cc ) {
			$people['cc'][] = $cc[0];
		}
		foreach ( $this->mailcatcher->getBccAddresses() as $bcc ) {
			$people['bcc'][] = $bcc[0];
		}

		$people['from'] = $this->mailcatcher->From;

		return $people;
	}

	/**
	 * Get message ID based on mailer.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	private function get_message_id() {

		$message_id = '';

		$custom_id_mailers = [ 'sendlayer', 'smtpcom', 'postmark', 'sparkpost', 'sendgrid' ];

		if ( in_array( $this->mailer->get_mailer_name(), $custom_id_mailers, true ) ) {
			foreach ( $this->mailcatcher->getCustomHeaders() as $header ) {
				if ( $header[0] === 'X-Msg-ID' ) {
					$message_id = $header[1];
					break;
				}
			}
		}

		if ( empty( $message_id ) ) {
			$message_id = trim( $this->mailcatcher->getLastMessageID(), '<>' );
		}

		return $message_id;
	}
}
