<?php

namespace WPMailSMTP\Pro\Providers\Gmail;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Admin\ConnectionSettings;
use WPMailSMTP\Admin\SetupWizard;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Debug;
use WPMailSMTP\Options as PluginOptions;
use WPMailSMTP\Pro\Providers\Gmail\Api\Client;
use WPMailSMTP\Pro\Providers\Gmail\Api\OneTimeToken;
use WPMailSMTP\Pro\Providers\Gmail\Api\SiteId;
use WPMailSMTP\Providers\AuthAbstract;

/**
 * Class Auth to request access.
 *
 * @since 3.11.0
 */
class Auth extends AuthAbstract {

	/**
	 * Auth constructor.
	 *
	 * @since 3.11.0
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $connection = null ) {

		parent::__construct( $connection );

		if ( $this->mailer_slug !== Options::SLUG ) {
			return;
		}

		$this->options = $this->connection_options->get_group( $this->mailer_slug );
	}

	/**
	 * Get the url, that users will be redirected back to finish the OAuth process.
	 *
	 * @since 3.11.0
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 *
	 * @return string
	 */
	public static function get_plugin_auth_url( $connection = null ) {

		if ( is_null( $connection ) ) {
			$connection = wp_mail_smtp()->get_connections_manager()->get_primary_connection();
		}

		/**
		 * Filters the plugin auth redirect url.
		 *
		 * @since 3.11.0
		 *
		 * @param string $auth_url The plugin auth redirect url.
		 */
		$auth_url = apply_filters(
			'wp_mail_smtp_pro_providers_gmail_auth_get_plugin_auth_url',
			add_query_arg(
				[
					'page' => Area::SLUG,
					'tab'  => 'auth',
				],
				admin_url( 'options-general.php' )
			)
		);

		$state = [
			wp_create_nonce( 'wp_mail_smtp_provider_client_state' ),
			$connection->get_id(),
		];

		return add_query_arg(
			[
				'state'           => implode( '-', $state ),
				'one_click_setup' => 1,
			],
			$auth_url
		);
	}

	/**
	 * Init and get the Client object.
	 *
	 * @since 3.11.0
	 *
	 * @return Client
	 */
	public function get_client() {

		// Doesn't load client twice + gives ability to overwrite.
		if ( ! empty( $this->client ) ) {
			return $this->client;
		}

		$credentials = $this->options['one_click_setup_credentials'] ?? [];
		$site_url    = get_site_url();

		if ( $this->connection->get_id() !== 'primary' ) {
			$site_url = add_query_arg( 'connection_id', $this->connection->get_id(), $site_url );
		}

		$this->client = new Client( $credentials, new OneTimeToken(), new SiteId(), $site_url );

		return $this->client;
	}

	/**
	 * Process authorization.
	 * Redirect user back to settings with an error message, if failed.
	 *
	 * @since 3.11.0
	 */
	public function process() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, Generic.Metrics.CyclomaticComplexity.TooHigh

		$redirect_url         = ( new ConnectionSettings( $this->connection ) )->get_admin_page_url();
		$is_setup_wizard_auth = ! empty( $this->options['is_setup_wizard_auth'] );

		if ( $is_setup_wizard_auth ) {
			$this->update_is_setup_wizard_auth( false );

			$redirect_url = SetupWizard::get_site_url() . '#/step/configure_mailer/gmail';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! ( isset( $_GET['tab'] ) && $_GET['tab'] === 'auth' ) ) {
			wp_safe_redirect( $redirect_url );
			exit;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$key        = ! empty( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		$token      = ! empty( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
		$user_email = ! empty( $_GET['user_email'] ) ? sanitize_email( wp_unslash( $_GET['user_email'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( empty( $key ) || empty( $token ) ) {
			Debug::set( esc_html__( 'Google authorization error. Missing required arguments.', 'wp-mail-smtp-pro' ) );

			wp_safe_redirect(
				add_query_arg(
					'error',
					'google_one_click_setup_unsuccessful_oauth',
					$redirect_url
				)
			);
			exit;
		}

		$data = [
			'one_click_setup_status'       => 'active',
			'one_click_setup_credentials'  => [
				'key'   => $key,
				'token' => $token,
			],
			'one_click_setup_user_details' => [
				'email' => $user_email,
			],
		];

		$site_url = get_site_url();

		if ( $this->connection->get_id() !== 'primary' ) {
			$site_url = add_query_arg( 'connection_id', $this->connection->get_id(), $site_url );
		}

		// Create a client with a new credentials.
		$this->client = new Client( $data['one_click_setup_credentials'], new OneTimeToken(), new SiteId(), $site_url );

		$is_valid = $this->get_client()->verify_auth();

		if ( is_wp_error( $is_valid ) ) {
			$error = $is_valid->get_error_message();

			Debug::set(
				sprintf( /* Translators: %1$s the error code passed from Google. */
					esc_html__( 'Google authorization error. %1$s', 'wp-mail-smtp-pro' ),
					esc_html( $error )
				)
			);

			wp_safe_redirect(
				add_query_arg(
					'error',
					'google_one_click_setup_unsuccessful_oauth',
					$redirect_url
				)
			);

			return;
		}

		$all = $this->connection_options->get_all();

		$all[ $this->mailer_slug ] = PluginOptions::array_merge_recursive( $all[ $this->mailer_slug ], $data );
		$all['mail']['from_email'] = $user_email;

		$this->connection_options->set( $all, false, true );

		// Clear debug log on success auth.
		Debug::clear();

		wp_safe_redirect(
			add_query_arg(
				'success',
				'google_one_click_setup_site_linked',
				$redirect_url
			)
		);
		exit;
	}

	/**
	 * Get the auth URL used to process authorization.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	public function get_auth_url() {

		$settings_url = rawurlencode( ( new ConnectionSettings( $this->connection ) )->get_admin_page_url() );

		return $this->get_client()->get_auth_url(
			[
				'return'              => self::get_plugin_auth_url( $this->connection ),
				'plugin_settings_url' => $settings_url,
			]
		);
	}

	/**
	 * Get the reauth URL used to process reauthorization.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	public function get_reauth_url() {

		$settings_url = rawurlencode( ( new ConnectionSettings( $this->connection ) )->get_admin_page_url() );

		return $this->get_client()->get_reauth_url(
			[
				'return'              => self::get_plugin_auth_url( $this->connection ),
				'plugin_settings_url' => $settings_url,
			]
		);
	}

	/**
	 * Get user information (like email etc) that is associated with the current OAuth connection.
	 *
	 * @since 3.11.0
	 *
	 * @return array
	 */
	public function get_user_info() {

		return $this->connection_options->get( $this->mailer_slug, 'one_click_setup_user_details' );
	}

	/**
	 * Whether client credentials are saved.
	 *
	 * We don't have any settings for One-Click Setup, so it's always `true`.
	 *
	 * @since 3.11.0
	 *
	 * @return true
	 */
	public function is_clients_saved() {

		return true;
	}

	/**
	 * Whether auth is required.
	 *
	 * @since 3.11.0
	 *
	 * @return bool
	 */
	public function is_auth_required() {

		$credentials = $this->options['one_click_setup_credentials'] ?? [];

		return empty( $credentials['key'] ) || empty( $credentials['token'] );
	}

	/**
	 * Whether reauthorization is required.
	 *
	 * @since 3.11.0
	 *
	 * @return bool
	 */
	public function is_reauth_required() {

		if ( $this->is_auth_required() ) {
			return false;
		}

		$transient_key = 'wp_mail_smtp_gmail_one_click_setup_auth_verified_' . $this->connection->get_id();

		if ( empty( get_transient( $transient_key ) ) ) {
			$is_active = $this->get_client()->verify_auth();

			if ( is_wp_error( $is_active ) && $is_active->get_error_code() === 401 ) {
				$this->set_auth_status( 'reauth' );
			}

			set_transient( $transient_key, true, HOUR_IN_SECONDS );
		}

		if (
			isset( $this->options['one_click_setup_status'] ) &&
			$this->options['one_click_setup_status'] === 'reauth'
		) {
			return true;
		}

		return false;
	}

	/**
	 * Set auth status.
	 *
	 * @since 3.11.0
	 *
	 * @param string $status Status name (active or reauth).
	 */
	public function set_auth_status( $status ) {

		$all = $this->connection_options->get_all();

		// To save in DB.
		$all[ $this->mailer_slug ]['one_click_setup_status'] = $status;

		// To save in currently retrieved options array.
		$this->options['one_click_setup_status'] = $status;

		$this->connection_options->set( $all, false, true );
	}
}
