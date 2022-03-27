<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\WP;
use WPMailSMTP\Options;

/**
 * Class Webhooks. Emails related webhooks.
 *
 * @since 3.3.0
 */
class Webhooks {

	/**
	 * Success setup identifier.
	 * The subscription was created successfully.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	const SUCCESS_SETUP = 'success';

	/**
	 * Failed setup identifier.
	 * The subscription creation was failed.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	const FAILED_SETUP = 'failed';

	/**
	 * Broken setup identifier.
	 * The subscription was created properly, but for some reason,
	 * it was modified or deleted in the provider dashboard.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	const BROKEN_SETUP = 'broken';

	/**
	 * Manual setup identifier.
	 * The subscription was removed manually.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	const MANUAL_SETUP = 'manual';

	/**
	 * Option key where we save any errors while creating subscription.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	const SUBSCRIPTION_ERROR_OPTION_NAME = 'wp_mail_smtp_logs_webhooks_subscription_error';

	/**
	 * Whether unsubscribe was performed.
	 *
	 * @since 3.3.0
	 *
	 * @var bool
	 */
	protected $is_unsubscribe = false;

	/**
	 * Constructor.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {

		if ( ! self::is_allowed() ) {
			return;
		}

		$provider = $this->get_active_provider();

		if ( $provider !== false ) {
			$provider->init();
		}
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.3.0
	 */
	public function hooks() {

		if ( ! self::is_allowed() ) {
			return;
		}

		// Register REST route to handle incoming webhook requests.
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );

		// Setup subscription.
		add_action( 'admin_init', [ $this, 'setup_subscription' ], 50 );

		// Maybe unsubscribe on settings change.
		add_action(
			'wp_mail_smtp_admin_area_process_actions_process_post_before',
			[ $this, 'maybe_unsubscribe_on_settings_change' ],
			20,
			2
		);

		// Maybe unsubscribe on settings change in Setup Wizard.
		add_action(
			'wp_mail_smtp_admin_setup_wizard_update_settings',
			function ( $data ) {
				$this->maybe_unsubscribe_on_settings_change( $data, 'setup-wizard' );
			}
		);

		// Maybe unsubscribe on settings constants change.
		add_action( 'admin_init', [ $this, 'maybe_unsubscribe_on_settings_constants_change' ] );

		// Unsubscribe on plugin deactivation.
		register_deactivation_hook( WPMS_PLUGIN_FILE, [ $this, 'unsubscribe_on_plugin_deactivation' ] );

		// Create subscription ajax handler.
		add_action( 'wp_ajax_wp_mail_smtp_pro_webhooks_subscribe', [ $this, 'subscribe_ajax' ] );

		// Remove subscription ajax handler.
		add_action( 'wp_ajax_wp_mail_smtp_pro_webhooks_unsubscribe', [ $this, 'unsubscribe_ajax' ] );
	}

	/**
	 * Whether webhooks allowed in current setup.
	 *
	 * @since 3.3.0
	 */
	public static function is_allowed() {

		return ! is_multisite();
	}

	/**
	 * Register REST route to handle incoming webhook requests.
	 *
	 * @since 3.3.0
	 */
	public function register_rest_route() {

		$provider = $this->get_active_provider();

		if ( $provider === false ) {
			return;
		}

		register_rest_route(
			'wp-mail-smtp/v1',
			'/webhooks/' . $provider->get_mailer_name(),
			[
				'methods'             => 'POST',
				'callback'            => [ $provider->get_processor(), 'handle' ],
				'permission_callback' => [ $provider->get_processor(), 'validate' ],
			]
		);
	}

	/**
	 * Setup subscription.
	 *
	 * @since 3.3.0
	 */
	public function setup_subscription() {

		// Bail if:
		//  - current request is AJAX;
		//  - current request is cron job;
		//  - email log is disabled.
		if (
			WP::is_doing_ajax() ||
			wp_doing_cron() ||
			wp_mail_smtp()->get_pro()->get_logs()->is_enabled() === false
		) {
			return;
		}

		/**
		 * Filters whether webhooks subscription setup allowed.
		 *
		 * @since 3.3.0
		 *
		 * @param bool $is_allowed Whether webhooks subscription setup allowed.
		 */
		$is_allowed = apply_filters( 'wp_mail_smtp_pro_emails_logs_webhooks_setup_subscription', true );

		if ( ! $is_allowed ) {
			return;
		}

		$provider = $this->get_active_provider();

		// Bail if:
		//  - current mail provider doesn't support webhooks;
		//  - subscription was already created;
		//  - mailer configuration was not completed.
		if (
			$provider === false ||
			! empty( $provider->get_setup_status() ) ||
			! $provider->get_mailer()->is_mailer_complete()
		) {
			return;
		}

		$rest_availability = Helpers::test_rest_availability();

		// Bail if REST API is not accessible.
		if ( is_wp_error( $rest_availability ) ) {
			update_option( self::SUBSCRIPTION_ERROR_OPTION_NAME, $this->rest_availability_error_message( $rest_availability ) );
			$provider->set_setup_status( self::FAILED_SETUP );

			return;
		}

		$provider->subscribe();
	}

	/**
	 * Maybe unsubscribe on settings change.
	 *
	 * @since 3.3.0
	 *
	 * @param array  $data      POST data.
	 * @param string $page_slug Current page slug.
	 */
	public function maybe_unsubscribe_on_settings_change( $data, $page_slug ) {

		// Bail if unsubscription was already performed.
		if ( $this->is_unsubscribe === true ) {
			return;
		}

		$provider = $this->get_active_provider();

		if ( $provider !== false && ! empty( $provider->get_setup_status() ) ) {
			$options = Options::init();
			$mailer  = $provider->get_mailer_name();

			if (
				(
					$page_slug === 'settings' || $page_slug === 'setup-wizard' &&
					(
						( // If mailer settings was changed.
							isset( $data[ $mailer ] ) &&
							! empty( array_diff( $data[ $mailer ], array_intersect_key( $options->get_group( $mailer ), $data[ $mailer ] ) ) )
						) ||
						( // If mailer was changed.
							! empty( $data['mail']['mailer'] ) &&
							$data['mail']['mailer'] !== $options->get( 'mail', 'mailer' )
						)
					)
				) ||
				(
					$page_slug === 'logs' &&
					( // If email log was disabled.
						! isset( $data['logs']['enabled'] ) &&
						$options->get( 'logs', 'enabled' ) === true
					)
				) ||
				(
					$page_slug === 'setup-wizard' &&
					( // If email log was disabled in the Setup Wizard.
						isset( $data['logs']['enabled'] ) &&
						$data['logs']['enabled'] === false &&
						$options->get( 'logs', 'enabled' ) === true
					)
				)
			) {
				$this->is_unsubscribe = true;
				$provider->unsubscribe();
			}
		}
	}

	/**
	 * Maybe unsubscribe on settings constants change.
	 *
	 * @since 3.3.0
	 */
	public function maybe_unsubscribe_on_settings_constants_change() {

		// Bail if we're not in admin panel.
		if ( ! WP::in_wp_admin() ) {
			return;
		}

		// Bail if unsubscription was already performed.
		if ( $this->is_unsubscribe === true ) {
			return;
		}

		$provider = $this->get_active_provider();

		// Bail if current mail provider doesn't support webhooks or subscription was not created yet.
		if ( $provider === false || empty( $provider->get_setup_status() ) ) {
			return;
		}

		$options = Options::init();

		if ( // If email log was disabled.
			$options->is_const_defined( 'logs', 'enabled' ) &&
			$options->is_const_changed( 'logs', 'enabled' ) &&
			$options->get( 'logs', 'enabled' ) === false
		) {
			$this->is_unsubscribe = true;
			$provider->unsubscribe();
		}
	}

	/**
	 * Unsubscribe on plugin deactivation.
	 *
	 * @since 3.3.0
	 */
	public function unsubscribe_on_plugin_deactivation() {

		$provider = $this->get_active_provider();

		// Bail if current mail provider doesn't support webhooks or subscription was not created yet.
		if ( $provider === false || empty( $provider->get_setup_status() ) ) {
			return;
		}

		$provider->unsubscribe();
	}

	/**
	 * Create subscription ajax handler.
	 *
	 * @since 3.3.0
	 */
	public function subscribe_ajax() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wp-mail-smtp-pro-admin' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled() === false ) {
			wp_send_json_error( esc_html__( 'The email log is disabled. Please first enable the email log.', 'wp-mail-smtp-pro' ) );
		}

		$provider = $this->get_active_provider();

		if (
			$provider === false ||
			! $provider->get_mailer()->is_mailer_complete()
		) {
			wp_send_json_error( esc_html__( 'Mailer setup is not completed. Please complete your mailer configuration first.', 'wp-mail-smtp-pro' ) );
		}

		$rest_availability = Helpers::test_rest_availability();

		// Bail if REST API is not accessible.
		if ( is_wp_error( $rest_availability ) ) {
			wp_send_json_error( $this->rest_availability_error_message( $rest_availability ) );
		}

		$result = $provider->subscribe();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( implode( "\r\n", array_unique( $result->get_error_messages() ) ) );
		}

		wp_send_json_success( esc_html__( 'Subscription created successfully.', 'wp-mail-smtp-pro' ) );
	}

	/**
	 * Remove subscription ajax handler.
	 *
	 * @since 3.3.0
	 */
	public function unsubscribe_ajax() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wp-mail-smtp-pro-admin' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		$provider = $this->get_active_provider();

		if (
			$provider === false ||
			$provider->get_setup_status() !== self::SUCCESS_SETUP
		) {
			wp_send_json_error( esc_html__( 'The subscription was not created yet.', 'wp-mail-smtp-pro' ) );
		}

		$result = $provider->unsubscribe();

		$provider->set_setup_status( self::MANUAL_SETUP );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( implode( "\r\n", array_unique( $result->get_error_messages() ) ) );
		}

		wp_send_json_success( esc_html__( 'Subscription removed successfully.', 'wp-mail-smtp-pro' ) );
	}

	/**
	 * Get current mailer provider if available.
	 *
	 * @since 3.3.0
	 *
	 * @return AbstractProvider|false
	 */
	public function get_active_provider() {

		$mailer = Options::init()->get( 'mail', 'mailer' );

		return $this->get_provider( $mailer );
	}

	/**
	 * Get webhook provider.
	 *
	 * @since 3.3.0
	 *
	 * @param string $mailer_name Mailer name/slug.
	 *
	 * @return AbstractProvider|false
	 */
	public function get_provider( $mailer_name ) {

		$provider = false;

		$providers = [
			'mailgun'    => Providers\Mailgun\Provider::class,
			'smtpcom'    => Providers\SMTPcom\Provider::class,
			'sendinblue' => Providers\Sendinblue\Provider::class,
			'postmark'   => Providers\Postmark\Provider::class,
			'sparkpost'  => Providers\SparkPost\Provider::class,
		];

		if ( isset( $providers[ $mailer_name ] ) ) {
			$provider = new $providers[ $mailer_name ]( $mailer_name );
		}

		/**
		 * Filters webhook provider.
		 *
		 * @since 3.3.0
		 *
		 * @param AbstractProvider $provider    Webhook provider.
		 * @param string           $mailer_name Mailer name/slug.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_webhooks_get_provider', $provider, $mailer_name );
	}

	/**
	 * Generate REST API availability error message.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_Error $error Error object.
	 *
	 * @return string
	 */
	private function rest_availability_error_message( $error ) {

		return sprintf( /* translators: %1$s - error message; %2$s - error code. */
			esc_html__( 'Seems that the REST API on your website is disabled. For correct work of email deliverability status verification via webhooks, REST API must be enabled. Please enable your REST API or whitelist "/wp-mail-smtp/v1" endpoint. The REST API request failed due to an error: %1$s (%2$s).', 'wp-mail-smtp-pro' ),
			$error->get_error_message(),
			$error->get_error_code()
		);
	}
}
