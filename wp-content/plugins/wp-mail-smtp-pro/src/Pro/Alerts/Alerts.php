<?php

namespace WPMailSMTP\Pro\Alerts;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Tasks\NotifierTask;
use WPMailSMTP\WP;

/**
 * Class Alerts.
 *
 * @since 3.5.0
 */
class Alerts {

	/**
	 * Failed email alert type slug.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	const FAILED_EMAIL = 'failed_email';

	/**
	 * Register hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {

		add_filter( 'wp_mail_smtp_admin_get_pages', [ $this, 'init_settings_tab' ] );

		add_action( 'wp_mail_smtp_mailcatcher_send_failed', [ $this, 'handle_failed_email' ], 10, 3 );
	}

	/**
	 * Initialize settings tab.
	 *
	 * @since 3.5.0
	 *
	 * @param array $tabs Tabs array.
	 */
	public function init_settings_tab( $tabs ) {

		$tabs['alerts'] = new Admin\SettingsTab();

		return $tabs;
	}

	/**
	 * Handle failed email.
	 *
	 * @since 3.5.0
	 *
	 * @param string               $error_message Error message.
	 * @param MailCatcherInterface $mailcatcher   The MailCatcher object.
	 * @param string               $mail_mailer   Current mailer slug.
	 */
	public function handle_failed_email( $error_message, $mailcatcher, $mail_mailer ) {

		// Bail if any of alerts channels is not enabled or it's a test email.
		if (
			! $this->is_enabled() ||
			$mailcatcher->is_test_email() ||
			$mailcatcher->is_setup_wizard_test_email()
		) {
			return;
		}

		$to_email_address = array_map(
			function ( $address ) use ( $mailcatcher ) {
				return $mailcatcher->addrFormat( $address );
			},
			$mailcatcher->getToAddresses()
		);

		$data = [
			'to_email_addresses' => implode( ',', $to_email_address ),
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'subject'            => $mailcatcher->Subject,
			'error_message'      => $error_message,
		];

		$debug_event_id = $mailcatcher->get_debug_event_id();

		if ( ! empty( $debug_event_id ) ) {
			$data['debug_event_id']   = $debug_event_id;
			$data['debug_event_link'] = add_query_arg(
				[
					'tab'            => 'debug-events',
					'debug_event_id' => $debug_event_id,
				],
				WP::admin_url( 'admin.php?page=' . Area::SLUG . '-tools' )
			);
		}

		$current_email_id = wp_mail_smtp()->get_pro()->get_logs()->get_current_email_id();

		if ( ! empty( $current_email_id ) ) {
			$data['log_id']   = $current_email_id;
			$data['log_link'] = add_query_arg(
				[
					'email_id' => $current_email_id,
					'mode'     => 'view',
				],
				wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
			);
		}

		( new NotifierTask() )
			->async()
			->params( self::FAILED_EMAIL, $data )
			->register();
	}

	/**
	 * Whether at least one alert channel is enabled.
	 *
	 * @since 3.5.0
	 *
	 * @return bool
	 */
	private function is_enabled() {

		$options = Options::init();
		$loader  = new Loader();

		$enabled_alerts = array_filter(
			array_keys( $loader->get_providers() ),
			function ( $provider_slug ) use ( $options ) {
				return $options->get( 'alert_' . $provider_slug, 'enabled' );
			}
		);

		return count( $enabled_alerts ) > 0;
	}
}
