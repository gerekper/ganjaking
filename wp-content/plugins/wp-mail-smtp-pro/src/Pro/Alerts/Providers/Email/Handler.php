<?php

namespace WPMailSMTP\Pro\Alerts\Providers\Email;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\Alert;
use WPMailSMTP\Pro\Alerts\Alerts;
use WPMailSMTP\Pro\Alerts\Handlers\HandlerInterface;

/**
 * Class Handler. Email API alerts.
 *
 * @since 3.5.0
 */
class Handler implements HandlerInterface {

	/**
	 * The maximum rate at which alerts can be handled by this handler.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const RATE_LIMIT = MINUTE_IN_SECONDS * 30;

	/**
	 * The last time an alert was handled by this handler.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const LAST_EXECUTED_TRANSIENT = 'wp_mail_smtp_email_alert_handler_timestamp';

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
			],
			true
		);
	}

	/**
	 * Handle alert.
	 * Send alert notification via Email API.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return bool
	 */
	public function handle( Alert $alert ) {

		// Bail if license is not valid.
		if ( ! wp_mail_smtp()->pro->get_license()->is_valid() ) {
			return false;
		}

		$connections = (array) Options::init()->get( 'alert_email', 'connections' );

		$connections = array_unique(
			array_filter(
				$connections,
				function ( $connection ) {
					return isset( $connection['send_to'] ) && is_email( $connection['send_to'] );
				}
			),
			SORT_REGULAR
		);

		if ( empty( $connections ) ) {
			return false;
		}

		// Limit max 3 recipients.
		$connections = array_slice( $connections, 0, 3 );

		$url = 'https://connect.wpmailsmtp.com/email-api/v1/send';

		$args = [
			'headers' => [
				'Content-Type'  => 'application/json',
				'X-Licence-Key' => wp_mail_smtp()->get_license_key(),
				'X-Site-Domain' => wp_parse_url( home_url(), PHP_URL_HOST ),
			],
			'user-agent' => Helpers::get_default_user_agent(),
			'body'    => wp_json_encode(
				[
					'recipients_email_addresses' => array_column( $connections, 'send_to' ),
					'event'                      => $alert->get_type(),
					'plugin_version'             => WPMS_PLUGIN_VER,
					'site_title'                 => get_bloginfo( 'name' ),
					'site_url'                   => home_url(),
					'settings_link'              => wp_mail_smtp()->get_admin()->get_admin_page_url(),
					'email_data'                 => $alert->get_data(),
				]
			),
		];

		/**
		 * Filters Email API request arguments.
		 *
		 * @since 3.5.0
		 *
		 * @param array $args  Email API request arguments.
		 * @param Alert $alert Alert object.
		 */
		$args = apply_filters( 'wp_mail_smtp_pro_alerts_providers_email_handler_handle_request_args', $args, $alert );

		wp_remote_post( $url, $args );

		DebugEvents::add_debug( esc_html__( 'An Email alert request was sent.', 'wp-mail-smtp-pro' ) );

		// Start a new rate limit period, if previous one expired.
		if ( self::get_remaining_rate_limit_seconds() === 0 ) {
			set_transient( self::LAST_EXECUTED_TRANSIENT, time(), self::RATE_LIMIT );
		}

		return true;
	}

	/**
	 * Return the number of seconds until rate limit expires.
	 *
	 * @since 3.9.0
	 *
	 * @return int Number of seconds until rate limit expires.
	 */
	public static function get_remaining_rate_limit_seconds() {

		$last_executed_time = get_transient( self::LAST_EXECUTED_TRANSIENT );

		// Bail early if rate limit already expired.
		if ( $last_executed_time === false ) {
			return 0;
		}

		$remaining_time = self::RATE_LIMIT - ( time() - $last_executed_time );

		// Bail early if rate limit just expired.
		if ( $remaining_time <= 0 ) {
			return 0;
		}

		return $remaining_time;
	}
}
