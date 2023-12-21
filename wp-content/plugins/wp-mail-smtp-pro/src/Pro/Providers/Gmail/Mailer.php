<?php

namespace WPMailSMTP\Pro\Providers\Gmail;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Pro\Providers\Gmail\Api\Response;
use WPMailSMTP\Providers\MailerAbstract;
use Exception;

/**
 * Class Mailer.
 *
 * @since 3.11.0
 */
class Mailer extends MailerAbstract {

	/**
	 * Mailer constructor.
	 *
	 * @since 3.11.0
	 *
	 * @param MailCatcherInterface $phpmailer  The MailCatcher object.
	 * @param ConnectionInterface  $connection The Connection object.
	 */
	public function __construct( MailCatcherInterface $phpmailer, $connection = null ) {

		parent::__construct( $phpmailer, $connection );

		$this->process_phpmailer( $phpmailer );
	}

	/**
	 * Re-use the MailCatcher class methods and properties.
	 *
	 * @since 3.11.0
	 *
	 * @param MailCatcherInterface $phpmailer The MailCatcher object.
	 */
	public function process_phpmailer( $phpmailer ) {

		// Make sure that we have access to PHPMailer class methods.
		if ( ! wp_mail_smtp()->is_valid_phpmailer( $phpmailer ) ) {
			return;
		}

		$this->phpmailer = $phpmailer;
	}

	/**
	 * Send email.
	 *
	 * @since 3.11.0
	 */
	public function send() {

		$auth = new Auth( $this->connection );

		try {
			// Check whether the mailer is ready to send emails.
			$this->check_requirements();

			// Prepare a message for sending if any changes happened above.
			$this->phpmailer->preSend();

			// Get the raw MIME email using MailCatcher data. We need to make base64URL-safe string.
			$base64 = str_replace(
				[ '+', '/', '=' ],
				[ '-', '_', '' ],
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				base64_encode( $this->phpmailer->getSentMIMEMessage() )
			);

			$allow_queue = ! $this->phpmailer->is_test_email();

			$response = $auth->get_client()->send_email( $base64, $allow_queue );

			DebugEvents::add_debug(
				esc_html__( 'An email request was sent to the Gmail API.', 'wp-mail-smtp-pro' )
			);

			$this->process_response( $response );
		} catch ( Exception $e ) {
			$this->error_message = Helpers::format_error_message( $e->getMessage() );
		}

		if ( $this->response instanceof Response ) {
			// Check if we need to reauthenticate.
			$auth_status = $this->response->get_header( 'X-Auth-Status' );

			if (
				$auth_status === 'reauth' &&
				$this->connection_options->get( $this->mailer, 'one_click_setup_status' ) !== 'reauth'
			) {
				$auth->set_auth_status( 'reauth' );
			}

			// Check if license is not valid and set transient to prevent further requests to API on each email send.
			$body = $this->response->get_body();

			if ( isset( $body['error_code'] ) && $body['error_code'] === 'invalid_license' ) {
				set_transient(
					'wp_mail_smtp_gmail_one_click_setup_send_email_invalid_license_lock',
					true,
					24 * HOUR_IN_SECONDS
				);
			}
		}
	}

	/**
	 * Check whether the mailer is ready to send emails.
	 *
	 * @since 3.11.0
	 *
	 * @throws Exception If the mailer is not ready.
	 */
	private function check_requirements() {

		$auth = new Auth( $this->connection );

		if ( $auth->is_auth_required() ) {
			throw new Exception( esc_html__( 'One-Click Setup for Google Mailer requires authorization. Perform authorization before sending emails.', 'wp-mail-smtp-pro' ) );
		} elseif (
			// If license key was not entered or was removed.
			empty( wp_mail_smtp()->get_license_key() ) ||

			/*
			 * If license is not valid. We need to throw error only if license is not valid in the plugin
			 * and on the API side (transient represents invalid license status from API side).
			 * The transient is set based on API response. This technique allows us to prevent requests to API on each
			 * email send when license is invalid and in the same time send one request with invalid license to the API.
			 * This one request required to perform various actions (user notification about expired license,
			 * switch account to reauth status etc.) on the API side.
			 */
			(
				! wp_mail_smtp()->get_pro()->get_license()->is_valid() &&
				! empty( get_transient( 'wp_mail_smtp_gmail_one_click_setup_send_email_invalid_license_lock' ) )
			)
		) {
			throw new Exception( esc_html__( 'One-Click Setup for Google Mailer requires an active license. Verify your license before sending emails.', 'wp-mail-smtp-pro' ) );
		}
	}

	/**
	 * Process API response.
	 *
	 * @since 3.11.0
	 *
	 * @param Response $response Api response object.
	 */
	protected function process_response( $response ) {

		$this->response = $response;

		if ( $response->has_errors() ) {
			$this->error_message = Helpers::format_error_message( $this->response->get_errors()->get_error_message() );
		} else {
			$body = $response->get_body();

			if ( empty( $body['message_id'] ) && empty( $body['queued'] ) ) {
				$this->error_message = esc_html__( 'The email message ID is missing.', 'wp-mail-smtp-pro' );
			}
		}

		if ( ! empty( $body['message_id'] ) ) {
			$this->phpmailer->addCustomHeader( 'X-Msg-ID', $body['message_id'] );
		}

		if ( ! empty( $body['queued'] ) ) {
			$this->phpmailer->addCustomHeader( 'X-Msg-Queued', true );
		}
	}

	/**
	 * Check whether the email was sent.
	 *
	 * @since 3.11.0
	 *
	 * @return bool
	 */
	public function is_email_sent() {

		$is_sent = false;

		if (
			$this->response instanceof Response &&
			! $this->response->has_errors()
		) {
			$is_sent = true;
		}

		/** This filter is documented in src/Providers/MailerAbstract.php. */
		return apply_filters( 'wp_mail_smtp_providers_mailer_is_email_sent', $is_sent, $this->mailer ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Get mailer debug information.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	public function get_debug_info() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		$debug = [];

		$credentials = $this->connection_options->get( 'gmail', 'one_click_setup_credentials' );

		$debug[] = '<strong>Key/Token:</strong> ' . ( ! empty( $credentials['key'] ) && ! empty( $credentials['token'] ) ? 'Yes' : 'No' );

		return implode( '<br>', $debug );
	}

	/**
	 * Whether the mailer has all its settings correctly set up and saved.
	 *
	 * @since 3.11.0
	 *
	 * @return bool
	 */
	public function is_mailer_complete() {

		if ( ! $this->is_php_compatible() ) {
			return false;
		}

		$auth = new Auth( $this->connection );

		if ( ! $auth->is_auth_required() ) {
			return true;
		}

		return false;
	}
}
