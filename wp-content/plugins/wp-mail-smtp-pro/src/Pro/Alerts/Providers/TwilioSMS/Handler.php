<?php

namespace WPMailSMTP\Pro\Alerts\Providers\TwilioSMS;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\Alert;
use WPMailSMTP\Pro\Alerts\Alerts;
use WPMailSMTP\Pro\Alerts\Handlers\HandlerInterface;
use WPMailSMTP\WP;

/**
 * Class Handler. Twilio SMS alerts.
 *
 * @since 3.5.0
 */
class Handler implements HandlerInterface {

	/**
	 * SMS message max length.
	 *
	 * @since 3.5.0
	 *
	 * @var int
	 */
	const MESSAGE_MAX_LENGTH = 160;

	/**
	 * Site title max length.
	 *
	 * @since 3.5.0
	 *
	 * @var int
	 */
	const SITE_TITLE_MAX_LENGTH = 15;

	/**
	 * Whether current handler can handle provided alert.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return bool
	 */
	public function can_handle( Alert $alert ) {

		return in_array(
			$alert->get_type(),
			[
				Alerts::FAILED_EMAIL,
				Alerts::FAILED_PRIMARY_EMAIL,
				Alerts::FAILED_BACKUP_EMAIL,
				Alerts::HARD_BOUNCED_EMAIL,
			],
			true
		);
	}

	/**
	 * Handle alert.
	 * Send alert notification via Twilio SMS.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return bool
	 */
	public function handle( Alert $alert ) {

		$connections = (array) Options::init()->get( 'alert_twilio_sms', 'connections' );

		$connections = array_unique(
			array_filter(
				$connections,
				function ( $connection ) {
					return ! empty( $connection['account_sid'] ) &&
								 ! empty( $connection['auth_token'] ) &&
								 ! empty( $connection['from_phone_number'] ) &&
								 ! empty( $connection['to_phone_number'] );
				}
			),
			SORT_REGULAR
		);

		if ( empty( $connections ) ) {
			return false;
		}

		$result = false;
		$errors = [];

		foreach ( $connections as $connection ) {
			$account_sid       = $connection['account_sid'];
			$auth_token        = $connection['auth_token'];
			$from_phone_number = $connection['from_phone_number'];
			$to_phone_number   = $connection['to_phone_number'];

			$url = "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json";

			$args = [
				'headers' => [
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Authorization' => 'Basic ' . base64_encode( "$account_sid:$auth_token" ),
				],
				'body'    => [
					'Body' => $this->get_message( $alert ),
					'From' => $from_phone_number,
					'To'   => $to_phone_number,
				],
			];

			/**
			 * Filters Twilio SMS request arguments.
			 *
			 * @since 3.5.0
			 *
			 * @param array $args       Twilio SMS request arguments.
			 * @param array $connection Connection settings.
			 * @param Alert $alert      Alert object.
			 */
			$args = apply_filters( 'wp_mail_smtp_pro_alerts_providers_twilio_sms_handler_handle_request_args', $args, $connection, $alert );

			$response      = wp_remote_post( $url, $args );
			$response_code = wp_remote_retrieve_response_code( $response );

			if ( $response_code === 201 ) {
				$result = true;
			} else {
				$errors[] = WP::wp_remote_get_response_error_message( $response );
			}
		}

		DebugEvents::add_debug( esc_html__( 'Twilio SMS alert request was sent.', 'wp-mail-smtp-pro' ) );

		if ( ! empty( $errors ) && DebugEvents::is_debug_enabled() ) {
			DebugEvents::add( esc_html__( 'Alert: Twilio SMS.', 'wp-mail-smtp-pro' ) . WP::EOL . implode( WP::EOL, array_unique( $errors ) ) );
		}

		return $result;
	}

	/**
	 * Build message.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return string
	 */
	private function get_message( Alert $alert ) {

		$data = $alert->get_data();

		// Truncate site title.
		$site_title = $this->truncate_string( get_bloginfo( 'name' ), self::SITE_TITLE_MAX_LENGTH );

		$message[] = sprintf( '[%s]', $site_title );

		switch ( $alert->get_type() ) {
			case Alerts::FAILED_EMAIL:
				$message[] = esc_html__( 'Your Site Failed to Send an Email.', 'wp-mail-smtp-pro' );
				break;

			case Alerts::FAILED_PRIMARY_EMAIL:
				$message[] = esc_html__( 'Your Site failed to send an email via the Primary connection, but the email was sent successfully via the Backup connection.', 'wp-mail-smtp-pro' );
				break;

			case Alerts::FAILED_BACKUP_EMAIL:
				$message[] = esc_html__( 'Your Site failed to send an email via Primary and Backup connection.', 'wp-mail-smtp-pro' );
				break;

			case Alerts::HARD_BOUNCED_EMAIL:
				$message[] = esc_html__( 'An email failed to be delivered', 'wp-mail-smtp-pro' );
				break;
		}

		$subject_placeholder = '{{subject}}';

		$message[] = sprintf(
			/* translators: %s - email subject. */
			esc_html__( 'Subject: “%s”.', 'wp-mail-smtp-pro' ),
			$subject_placeholder
		);

		if ( ! empty( $data['log_id'] ) ) {
			$message[] = sprintf(
				/* translators: %s - Email Log ID. */
				esc_html__( 'Email Log [#%s].', 'wp-mail-smtp-pro' ),
				$data['log_id']
			);
		}

		$message = implode( ' ', $message );

		// Truncate subject to all available space.
		$subject_max_length = self::MESSAGE_MAX_LENGTH - ( strlen( $message ) - strlen( $subject_placeholder ) );
		$subject            = $this->truncate_string( $data['subject'], $subject_max_length );

		$message = str_replace( $subject_placeholder, $subject, $message );

		// Truncate the final message to make sure that it's not exceeded the max length limit.
		return $this->truncate_string( $message, self::MESSAGE_MAX_LENGTH );
	}

	/**
	 * Truncate string.
	 *
	 * @since 3.5.0
	 *
	 * @param string $string Input string.
	 * @param int    $length String max length.
	 *
	 * @return string
	 */
	private function truncate_string( $string, $length ) {

		return substr( $string, 0, $length );
	}
}
