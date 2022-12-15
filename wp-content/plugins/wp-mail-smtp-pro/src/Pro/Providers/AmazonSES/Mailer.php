<?php

namespace WPMailSMTP\Pro\Providers\AmazonSES;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Providers\MailerAbstract;

/**
 * Class Mailer implements Mailer functionality.
 *
 * @since 1.5.0
 */
class Mailer extends MailerAbstract {

	/**
	 * The response object from AWS SDK email sending request.
	 *
	 * @since 2.4.0
	 *
	 * @var WPMailSMTP\Vendor\Aws\Result
	 */
	protected $response;

	/**
	 * Not really used since we are using AWS SDK library.
	 * Is here to pass some checks in parent::__construct.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $url = 'https://email.us-east-1.amazonaws.com';

	/**
	 * Set the already configured MailCatcher object.
	 *
	 * @since 1.5.0
	 *
	 * @param MailCatcherInterface $phpmailer The MailerCatcher instance.
	 */
	public function process_phpmailer( $phpmailer ) {

		// Make sure that we have access to PHPMailer class methods.
		if ( ! wp_mail_smtp()->is_valid_phpmailer( $phpmailer ) ) {
			return;
		}

		$this->phpmailer = $phpmailer;
	}

	/**
	 * Use AWS SDK to send emails.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to AWS SDK.
	 * @since 3.5.0 Switch to AWS SDK `SesV2Client`.
	 */
	public function send() {

		// Prepare the auth and client objects.
		$auth = new Auth( $this->connection );

		$data = [
			'Content' => [
				'Raw' => [
					'Data' => $this->phpmailer->getSentMIMEMessage(),
				],
			],
		];

		try {
			$response = $auth->get_client( 'v2' )->sendEmail( $data );

			DebugEvents::add_debug(
				esc_html__( 'An email request was sent to the Amazon SES API.', 'wp-mail-smtp-pro' )
			);

			$this->process_response( $response );
		} catch ( \Exception $e ) {
			$this->error_message = $e->getMessage();
		}
	}

	/**
	 * Check the correct output of the response.
	 *
	 * @since 1.5.0
	 *
	 * @param WPMailSMTP\Vendor\Aws\Result $response Response object from AWS SDK request.
	 */
	protected function process_response( $response ) {

		$this->response = $response;

		$error = '';

		if ( empty( $this->response ) ) {
			$error = esc_html__( 'Amazon SES request failed (empty response).', 'wp-mail-smtp-pro' );
		}

		if ( is_object( $this->response ) && empty( $this->response->get( 'MessageId' ) ) ) {
			$error = esc_html__( 'Something went wrong. Please try again.', 'wp-mail-smtp-pro' );
		}

		// Save the error text.
		if ( ! empty( $error ) ) {
			$this->error_message = $error;
		}
	}

	/**
	 * Whether the email was successfully sent.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_email_sent() {

		$is_sent = false;

		if ( is_object( $this->response ) && ! empty( $this->response->get( 'MessageId' ) ) ) {
			$is_sent = true;
		}

		/** This filter is documented in src/Providers/MailerAbstract.php. */
		return apply_filters( 'wp_mail_smtp_providers_mailer_is_email_sent', $is_sent, $this->mailer );
	}

	/**
	 * Get mailer debug information, that is helpful during support.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_debug_info() {

		$debug_items = [];

		$auth = new Auth( $this->connection );

		$debug_items[] = '<strong>Access Key ID/Secret:</strong> ' . ( $auth->is_clients_saved() ? 'Yes' : 'No' );

		return implode( '<br>', $debug_items );
	}

	/**
	 * Whether the mailer has all its settings correctly set up and saved.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_mailer_complete() {

		if ( ! $this->is_php_compatible() ) {
			return false;
		}

		$auth = new Auth( $this->connection );

		if (
			$auth->is_clients_saved() &&
			! $auth->is_auth_required()
		) {
			return true;
		}

		return false;
	}
}
