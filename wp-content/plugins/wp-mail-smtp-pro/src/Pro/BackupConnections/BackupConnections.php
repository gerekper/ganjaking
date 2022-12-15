<?php

namespace WPMailSMTP\Pro\BackupConnections;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\BackupConnections\Admin\SettingsTab;
use WPMailSMTP\Pro\WPMailArgs;

/**
 * Class BackupConnections.
 *
 * @since 3.7.0
 */
class BackupConnections {

	/**
	 * The `wp_mail` function arguments that were used for current email sending.
	 *
	 * @since 3.7.0
	 *
	 * @var WPMailArgs
	 */
	private $current_wp_mail_args;

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		// Init settings.
		( new SettingsTab() )->hooks();

		// Filter options save process.
		add_filter( 'wp_mail_smtp_options_set', [ $this, 'filter_options_set' ] );

		/*
		 * Set the backup connection that should be used for the current email if it was not set before and capture `wp_mail`
		 * function arguments for sending email via backup connection with the exact same data.
		 * Negative hook priority number tries to ensure to capture arguments as early as possible to make sure that we
		 * are getting arguments that were passed to the `wp_mail` function without modifications, since `wp_mail`
		 * filter will be applied again in the backup email.
		 */
		add_filter( 'wp_mail', [ $this, 'set_current_backup_connection' ], - PHP_INT_MAX );
	}

	/**
	 * Set backup connection and capture `wp_mail` function arguments.
	 *
	 * @since 3.7.0
	 *
	 * @param array $args Array of the `wp_mail` function arguments.
	 *
	 * @return array
	 */
	public function set_current_backup_connection( $args ) {

		$this->current_wp_mail_args = new WPMailArgs( $args );

		$backup_connection_id = Options::init()->get( 'backup_connection', 'connection_id' );

		// Bail if the backup connection is not selected.
		if ( empty( $backup_connection_id ) ) {
			return $args;
		}

		$connections_manager = wp_mail_smtp()->get_connections_manager();
		$backup_connection   = $connections_manager->get_mail_backup_connection();

		// Check if the backup connection was not set previously.
		if ( is_null( $backup_connection ) ) {
			$backup_connection = $connections_manager->get_connection( $backup_connection_id, false );

			if ( ! empty( $backup_connection ) ) {
				$connections_manager->set_mail_backup_connection( $backup_connection );
			}
		}

		return $args;
	}

	/**
	 * Whether the backup connection is defined and can be used for sending an email.
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	public function is_ready() {

		$backup_connection = wp_mail_smtp()->get_connections_manager()->get_mail_backup_connection();

		return ! empty( $this->current_wp_mail_args ) && ! empty( $backup_connection );
	}

	/**
	 * Send email via the backup connection.
	 *
	 * @since 3.7.0
	 *
	 * @return bool
	 */
	public function send_email() {

		if ( empty( $this->current_wp_mail_args ) ) {
			return false;
		}

		$connections_manager = wp_mail_smtp()->get_connections_manager();
		$backup_connection   = $connections_manager->get_mail_backup_connection();

		if ( empty( $backup_connection ) ) {
			return false;
		}

		$args = array_merge(
			[
				'to'          => '',
				'subject'     => '',
				'message'     => '',
				'headers'     => '',
				'attachments' => [],
			],
			$this->current_wp_mail_args->get_args()
		);

		$connections_manager->reset_mail_connection();

		// Set backup connection as current mail connection.
		$connections_manager->set_mail_connection( $backup_connection );

		/**
		 * Fires before email sending via the backup connection.
		 *
		 * @since 3.7.0
		 *
		 * @param array $args Array of the `wp_mail` function arguments.
		 */
		do_action( 'wp_mail_smtp_pro_backup_connections_send_email_before', $args );

		$is_sent = wp_mail( $args['to'], $args['subject'], $args['message'], $args['headers'], $args['attachments'] );

		/**
		 * Fires after email sending via the backup connection.
		 *
		 * @since 3.7.0
		 *
		 * @param bool  $is_sent Whether the email was sent successfully or not.
		 * @param array $args    Array of the `wp_mail` function arguments.
		 */
		do_action( 'wp_mail_smtp_pro_backup_connections_send_email_after', $is_sent, $args );

		return $is_sent;
	}

	/**
	 * Sanitize options.
	 *
	 * @since 3.7.0
	 *
	 * @param array $options Currently processed options passed to a filter hook.
	 *
	 * @return array
	 */
	public function filter_options_set( $options ) {

		if ( ! isset( $options['backup_connection'] ) ) {
			$options['backup_connection'] = [
				'connection_id' => false,
			];

			return $options;
		}

		foreach ( $options['backup_connection'] as $key => $value ) {
			if ( $key === 'connection_id' ) {
				$options['backup_connection'][ $key ] = sanitize_key( $value );
			}
		}

		return $options;
	}
}
