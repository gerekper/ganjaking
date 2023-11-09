<?php

namespace WPMailSMTP\Pro\Alerts;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\MailCatcherInterface;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\Admin\SettingsTab;
use WPMailSMTP\Pro\Emails\Logs\Email;
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
	 * Failed primary email alert type slug.
	 *
	 * The primary connection failed to send an email, but the backup succeeded.
	 *
	 * @since 3.7.0
	 *
	 * @var string
	 */
	const FAILED_PRIMARY_EMAIL = 'failed_primary_email';

	/**
	 * Failed backup email alert type slug.
	 *
	 * The primary and backup connections failed to send an email.
	 *
	 * @since 3.7.0
	 *
	 * @var string
	 */
	const FAILED_BACKUP_EMAIL = 'failed_backup_email';

	/**
	 * Hard-bounced email alert type slug.
	 *
	 * @since 3.10.0
	 *
	 * @var string
	 */
	const HARD_BOUNCED_EMAIL = 'hard_bounced_email';

	/**
	 * Register hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {

		add_filter( 'wp_mail_smtp_admin_get_pages', [ $this, 'init_settings_tab' ] );
		add_filter( 'wp_mail_smtp_admin_process_ajax_test_alerts_data', [ $this, 'process_ajax_test_alerts_data' ] );

		add_action( 'wp_mail_smtp_options_set', [ $this, 'remove_empty_send_to_for_alert_email' ] );
	}

	/**
	 * Removes the empty send_to values.
	 *
	 * @since 3.6.0
	 *
	 * @param array $options The options array.
	 *
	 * @return array The options array after removing empty send_to values.
	 */
	public function remove_empty_send_to_for_alert_email( $options ) {

		if ( isset( $options['alert_email']['connections'] ) ) {
			$valid_connections = [];

			foreach ( $options['alert_email']['connections'] as $connection ) {
				if ( ! empty( $connection['send_to'] ) ) {
					$valid_connections[] = [
						'send_to' => $connection['send_to'],
					];
				}
			}

			$options['alert_email']['connections'] = $valid_connections;
		}

		return $options;
	}

	/**
	 * Initialize settings tab.
	 *
	 * @since 3.5.0
	 *
	 * @param array $tabs Tabs array.
	 */
	public function init_settings_tab( $tabs ) {

		$tabs['alerts'] = new SettingsTab();

		return $tabs;
	}

	/**
	 * Handle failed email.
	 *
	 * @since 3.5.0
	 * @since 3.8.0 Included `mailer` array containing the primary and backup mailer slugs in the data
	 *                  passed to the notifier task.
	 *
	 * @param string               $error_message Error message.
	 * @param MailCatcherInterface $mailcatcher   The MailCatcher object.
	 * @param string               $mail_mailer   Current mailer slug.
	 * @param string               $failure_type  Failure type.
	 */
	public function handle_failed_email( $error_message, $mailcatcher, $mail_mailer, $failure_type = self::FAILED_EMAIL ) {

		$allowed_types = [
			self::FAILED_EMAIL,
			self::FAILED_PRIMARY_EMAIL,
			self::FAILED_BACKUP_EMAIL,
		];

		// Bail if any of alerts channels is not enabled, or it's a test email.
		if (
			! $this->is_enabled() ||
			$mail_mailer === 'mail' ||
			$mailcatcher->is_test_email() ||
			$mailcatcher->is_setup_wizard_test_email() ||
			! in_array( $failure_type, $allowed_types, true )
		) {
			return;
		}

		$to_email_address = array_map(
			function ( $address ) use ( $mailcatcher ) {
				return $mailcatcher->addrFormat( $address );
			},
			$mailcatcher->getToAddresses()
		);

		$backup_connection = wp_mail_smtp()->get_pro()->get_backup_connections()->get_latest_backup_connection();

		$data = [
			'to_email_addresses' => implode( ',', $to_email_address ),
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			'subject'            => $mailcatcher->Subject,
			'error_message'      => $error_message,
			'mailers'            => [
				'primary' => wp_mail_smtp()->get_connections_manager()->get_primary_connection()->get_mailer_slug(),
				'backup'  => $backup_connection instanceof ConnectionInterface ? $backup_connection->get_mailer_slug() : null,
			],
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
			->params( $failure_type, $data )
			->register();
	}

	/**
	 * Handle hard-bounced email.
	 *
	 * @since 3.10.0
	 *
	 * @param string $error_message Error message.
	 * @param Email  $email         The Email object.
	 */
	public function handle_hard_bounced_email( $error_message, $email ) {

		// Bail if any of alerts channels is not enabled,
		// or hard-bounce alerts are not enabled,
		// or it's a test email.
		if (
			! $this->is_enabled() ||
			! Options::init()->get( 'alert_events', 'email_hard_bounced' ) ||
			$email->is_test()
		) {
			return;
		}

		$to_email_address = $email->get_people( 'to' );

		$data = [
			'to_email_addresses' => implode( ',', $to_email_address ),
			'subject'            => $email->get_subject(),
			'error_message'      => $error_message,
			'mailers'            => [
				'primary' => $email->get_mailer(),
				'backup'  => null,
			],
		];

		$current_email_id = $email->get_id();

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
			->params( self::HARD_BOUNCED_EMAIL, $data )
			->register();
	}

	/**
	 * Handle the AJAX action for the test alerts button.
	 *
	 * @since 3.9.0
	 *
	 * @param string $data Array of submitted data.
	 */
	public function process_ajax_test_alerts_data( $data ) {

		if ( ! check_ajax_referer( 'wp-mail-smtp-admin', 'nonce', false ) ) {
			return;
		}

		// Bail if no alerts channel is enabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->send_test();

		// Store a transient for next page refresh notice.
		update_user_meta( get_current_user_id(), SettingsTab::NOTICE_USER_META, true );

		return $data;
	}

	/**
	 * Handle test run.
	 *
	 * @since 3.9.0
	 */
	private function send_test() {

		$to_email_address  = get_option( 'admin_email' );
		$subject           = __( 'WP Mail SMTP Alerts Test', 'wp-mail-smtp-pro' );
		$error_message     = __( 'This is a test error message triggered from the WP Mail SMTP Alerts settings', 'wp-mail-smtp-pro' );
		$backup_connection = wp_mail_smtp()->get_pro()->get_backup_connections()->get_latest_backup_connection();

		$data = [
			'to_email_addresses' => $to_email_address,
			'subject'            => $subject,
			'error_message'      => $error_message,
			'mailers'            => [
				'primary' => wp_mail_smtp()->get_connections_manager()->get_primary_connection()->get_mailer_slug(),
				'backup'  => $backup_connection instanceof ConnectionInterface ? $backup_connection->get_mailer_slug() : null,
			],
		];

		( new NotifierTask() )
			->async()
			->params( self::FAILED_EMAIL, $data )
			->register();
	}

	/**
	 * Whether at least one alert channel is enabled.
	 *
	 * @since 3.5.0
	 * @since 3.9.0 Change visibility from private to public.
	 *
	 * @return bool
	 */
	public function is_enabled() {

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
