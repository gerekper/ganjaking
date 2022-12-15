<?php

namespace WPMailSMTP\Pro\Alerts\Providers\CustomWebhook;

use WPMailSMTP\Admin\DebugEvents\DebugEvents;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\Alert;
use WPMailSMTP\Pro\Alerts\Alerts;
use WPMailSMTP\Pro\Alerts\Handlers\HandlerInterface;

/**
 * Class Handler. Custom webhook alerts.
 *
 * @since 3.5.0
 */
class Handler implements HandlerInterface {

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
	 * Send alert notification via custom webhook.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return bool
	 */
	public function handle( Alert $alert ) {

		$connections = (array) Options::init()->get( 'alert_custom_webhook', 'connections' );

		$connections = array_unique(
			array_filter(
				$connections,
				function ( $connection ) {
					return isset( $connection['webhook_url'] ) && filter_var( $connection['webhook_url'], FILTER_VALIDATE_URL );
				}
			),
			SORT_REGULAR
		);

		if ( empty( $connections ) ) {
			return false;
		}

		foreach ( $connections as $connection ) {
			$webhook_url = $connection['webhook_url'];

			$args = [
				'method'      => 'POST',
				'timeout'     => MINUTE_IN_SECONDS,
				'redirection' => 0,
				'user-agent'  => sprintf( 'wp-mail-smtp-alerts-webhooks/%s', WPMS_PLUGIN_VER ),
				'headers'     => [
					'Content-Type' => 'application/json',
				],
				'body'        => wp_json_encode( $this->get_message( $alert ) ),
			];

			/**
			 * Filters custom webhook request arguments.
			 *
			 * @since 3.5.0
			 *
			 * @param array $args       Custom webhook request arguments.
			 * @param array $connection Connection settings.
			 * @param Alert $alert      Alert object.
			 */
			$args = apply_filters( 'wp_mail_smtp_pro_alerts_providers_custom_webhook_handler_handle_request_args', $args, $connection, $alert );

			wp_remote_request( $webhook_url, $args );
		}

		DebugEvents::add_debug( esc_html__( 'Custom Webhook alert requests were processed.', 'wp-mail-smtp-pro' ) );

		return true;
	}

	/**
	 * Build message array.
	 *
	 * @since 3.5.0
	 *
	 * @param Alert $alert Alert object.
	 *
	 * @return array
	 */
	private function get_message( Alert $alert ) {

		$alert_message = '';

		switch ( $alert->get_type() ) {
			case Alerts::FAILED_EMAIL:
				$alert_message = esc_html__( 'Your Site Failed to Send an Email', 'wp-mail-smtp-pro' );
				break;

			case Alerts::FAILED_PRIMARY_EMAIL:
				$alert_message = esc_html__( 'Your Site failed to send an email via the Primary connection, but the email was sent successfully via the Backup connection', 'wp-mail-smtp-pro' );
				break;

			case Alerts::FAILED_BACKUP_EMAIL:
				$alert_message = esc_html__( 'Your Site failed to send an email via Primary and Backup connection', 'wp-mail-smtp-pro' );
				break;
		}

		return [
			'event'           => $alert->get_type(),
			'site_title'      => get_bloginfo( 'name' ),
			'site_url'        => home_url(),
			'general_message' => $alert_message,
			'email_data'      => $alert->get_data(),
		];
	}
}
