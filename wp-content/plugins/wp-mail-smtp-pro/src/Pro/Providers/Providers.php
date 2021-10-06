<?php

namespace WPMailSMTP\Pro\Providers;

use WPMailSMTP\Debug;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Providers\AmazonSES\Auth as SESAuth;
use WPMailSMTP\Pro\Providers\AmazonSES\Options as SESOptions;
use WPMailSMTP\Pro\Providers\Outlook\Auth as MSAuth;
use WPMailSMTP\WP;

/**
 * Class Providers to add Pro providers.
 *
 * @since 1.5.0
 */
class Providers {

	/**
	 * Providers constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * WordPress related hooks.
	 *
	 * @since 1.5.0
	 */
	public function init() {

		add_filter( 'wp_mail_smtp_providers_loader_get_providers', array( $this, 'inject_providers' ) );

		add_action( 'load-index.php', array( $this, 'process_auth_code' ) );

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', array( $this, 'enqueue_assets' ) );

		add_action( 'wp_ajax_wp_mail_smtp_pro_providers_ajax', array( $this, 'process_ajax' ) );
	}

	/**
	 * Inject own Pro providers.
	 *
	 * @since 1.5.0
	 * @since 2.3.0 Added Zoho mailer.
	 *
	 * @param array $providers The default providers.
	 *
	 * @return array
	 */
	public function inject_providers( $providers ) {

		$providers['amazonses'] = 'WPMailSMTP\Pro\Providers\AmazonSES\\';
		$providers['outlook']   = 'WPMailSMTP\Pro\Providers\Outlook\\';
		$providers['zoho']      = 'WPMailSMTP\Pro\Providers\Zoho\\';

		return $providers;
	}

	/**
	 * Complete the auth process for the Provider.
	 * Currently used for Microsoft Outlook only.
	 *
	 * @since 1.5.0
	 */
	public function process_auth_code() {

		// Bail if the auth request is not allowed.
		if ( ! $this->allow_auth_request() ) {
			return;
		}

		$state = sanitize_key( $_GET['state'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$code  = preg_replace( '/[^a-zA-Z0-9_\-.]/', '', $_GET['code'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput

		$nonce = str_replace( 'wp-mail-smtp-', '', $state );

		$auth         = new MSAuth();
		$redirect_url = wp_mail_smtp()->get_admin()->get_admin_page_url();

		$plugin_options       = new Options();
		$outlook_options      = $plugin_options->get_group( 'outlook' );
		$is_setup_wizard_auth = ! empty( $outlook_options['is_setup_wizard_auth'] );

		if ( $is_setup_wizard_auth ) {
			$auth->update_is_setup_wizard_auth( false );

			$redirect_url = \WPMailSMTP\Admin\SetupWizard::get_site_url() . '#/step/configure_mailer/outlook';
		}

		if ( ! wp_verify_nonce( $nonce, $auth->state_key ) ) {
			$url = add_query_arg(
				'error',
				'microsoft_no_code',
				$redirect_url
			);
		} else {
			Debug::clear();
			// Save the code.
			$auth->process_auth( $code );
			$auth->get_client( true );

			if ( $is_setup_wizard_auth ) {
				$error = Debug::get_last();

				if ( ! empty( $error ) ) {
					wp_safe_redirect(
						add_query_arg(
							'error',
							'microsoft_unsuccessful_oauth',
							$redirect_url
						)
					);
					exit;
				}
			}

			$url = add_query_arg(
				'success',
				'microsoft_site_linked',
				$redirect_url
			);
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Whether we allow the auth request to be processed.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function allow_auth_request() {

		// Only super admins can do that.
		if ( ! is_super_admin() ) {
			return false;
		}

		// Ajax is not supported.
		if ( WP::is_doing_ajax() ) {
			return false;
		}

		// We should be coming from somewhere.
		if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
			return false;
		}

		// We should have a required GET data.
		if (
			! isset( $_GET['code'] ) || // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			! isset( $_GET['state'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			return false;
		}

		$state = sanitize_key( $_GET['state'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Check whether state contains "wp-mail-smtp-".
		if ( substr( $state, 0, 13 ) !== 'wp-mail-smtp-' ) {
			return false;
		}

		return true;
	}

	/**
	 * Inject Pro features specific assets: CSS & JS.
	 *
	 * @since 1.5.0
	 */
	public function enqueue_assets() {

		// CSS.
		\wp_enqueue_style(
			'wp-mail-smtp-admin-pro-settings',
			\wp_mail_smtp()->pro->assets_url . '/css/smtp-pro-settings.min.css',
			array( 'wp-mail-smtp-admin' ),
			WPMS_PLUGIN_VER,
			false
		);

		/*
		 * JavaScript.
		 */
		\wp_enqueue_script(
			'wp-mail-smtp-admin-pro-settings',
			\wp_mail_smtp()->pro->assets_url . '/js/smtp-pro-settings' . WP::asset_min() . '.js',
			array( 'jquery', 'wp-mail-smtp-admin-jconfirm' ),
			WPMS_PLUGIN_VER,
			false
		);

		\wp_localize_script(
			'wp-mail-smtp-admin-pro-settings',
			'wp_mail_smtp_pro',
			array(
				'ses_text_sending'               => esc_html__( 'Sending...', 'wp-mail-smtp-pro' ),
				'ses_text_sent'                  => esc_html__( 'Sent', 'wp-mail-smtp-pro' ),
				'ses_text_resend'                => esc_html__( 'Resend', 'wp-mail-smtp-pro' ),
				'ses_text_email_delete'          => esc_html__( 'Are you sure you want to delete this email address? You will need to add and verify it again if you want to use it in the future.', 'wp-mail-smtp-pro' ),
				'ses_text_smth_wrong'            => esc_html__( 'Something went wrong, please reload the page and try again.', 'wp-mail-smtp-pro' ),
				'ses_text_email_invalid'         => esc_html__( 'Please make sure that the email address is valid.', 'wp-mail-smtp-pro' ),
				'ok'                             => esc_html__( 'OK', 'wp-mail-smtp-pro' ),
				'plugin_url'                     => esc_url( wp_mail_smtp()->plugin_url ),
				'icon'                           => esc_html__( 'Icon', 'wp-mail-smtp-pro' ),
				'ses_text_resend_failed'         => esc_html__( 'Resend failed!', 'wp-mail-smtp-pro' ),
				'ses_text_cancel'                => esc_html__( 'Cancel', 'wp-mail-smtp-pro' ),
				'ses_text_close'                 => esc_html__( 'Close', 'wp-mail-smtp-pro' ),
				'ses_text_yes'                   => esc_html__( 'Yes', 'wp-mail-smtp-pro' ),
				'ses_text_done'                  => esc_html__( 'Done', 'wp-mail-smtp-pro' ),
				'ses_text_domain_delete'         => esc_html__( 'Are you sure you want to delete this domain? You will need to add and verify it again if you want to use it in the future.', 'wp-mail-smtp-pro' ),
				'ses_text_dns_txt_title'         => esc_html__( 'Add verified domain', 'wp-mail-smtp-pro' ),
				'ses_text_no_identities'         => esc_html__( 'The AWS SES identities could not load because of an error.', 'wp-mail-smtp-pro' ),
				'ses_text_dns_txt_content'       => SESOptions::prepare_domain_txt_record_notice(),
				'ses_add_identity_modal_content' => SESOptions::prepare_add_new_identity_content(),
				'ses_add_identity_modal_title'   => SESOptions::prepare_add_new_identity_title(),
				'loader_white_small'             => wp_mail_smtp()->prepare_loader( 'white', 'sm' ),
				'nonce'                          => wp_create_nonce( 'wp-mail-smtp-pro-admin' ),
			)
		);
	}

	/**
	 * Process AJAX requests fired by a pro version of a plugin and related to providers.
	 * Currently, only AmazonSES has some AJAX.
	 * So we will hard-code this behavior for now.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Major changes (AWS SDK and domain verification changes).
	 */
	public function process_ajax() {

		$generic_error = esc_html__( 'Something went wrong. Please try again later.', 'wp-mail-smtp-pro' );

		// Verify nonce existence.
		if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( $generic_error );
		}

		$mailer = isset( $_POST['mailer'] ) ? sanitize_key( $_POST['mailer'] ) : '';

		if ( $mailer !== 'amazonses' ) {
			wp_send_json_error( $generic_error );
		}

		$task = isset( $_POST['task'] ) ? sanitize_key( $_POST['task'] ) : '';

		switch ( $task ) {
			case 'load_ses_identities':
				if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_mail_smtp_pro_amazonses_load_ses_identities' ) ) { // phpcs:ignore
					wp_send_json_error( $generic_error );
				}

				$ses_options = wp_mail_smtp()->get_providers()->get_options( SESOptions::SLUG );

				wp_send_json_success( $ses_options->prepare_ses_identities_content() );

				break;

			case 'identity_registration':
				if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_mail_smtp_pro_amazonses_register_identity' ) ) { // phpcs:ignore
					wp_send_json_error( $generic_error );
				}

				$type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
				$value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

				if ( $type === 'email' && ! is_email( $value ) ) {
					wp_send_json_error( esc_html__( 'Please provide a valid email address.', 'wp-mail-smtp-pro' ) );
				} elseif ( $type === 'domain' && empty( $value ) ) {
					wp_send_json_error( esc_html__( 'Please provide a domain name.', 'wp-mail-smtp-pro' ) );
				}

				$ses = new SESAuth();

				// Verify domain for easier conditional checking below.
				$domain_txt = ( $type === 'domain' ) ? $ses->do_verify_domain( $value ) : '';

				if ( $type === 'email' && $ses->do_verify_email( $value ) === true ) {
					wp_send_json_success(
						sprintf(
							wp_kses( /* translators: %s - email address. */
								__( 'Please check inbox of <code>%s</code> address for a verification email.', 'wp-mail-smtp-pro' ),
								[ 'code' => [] ]
							),
							esc_html( $value )
						)
					);
				} elseif ( $type === 'domain' && ! empty( $domain_txt ) ) {
					wp_send_json_success(
						SESOptions::prepare_domain_txt_record_notice( $value, $domain_txt )
					);
				} else {
					$error = Debug::get_last();
					Debug::clear();

					wp_send_json_error(
						esc_html( $error )
					);
				}

				break;

			case 'identity_delete':
				if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_mail_smtp_pro_amazonses_identity_delete' ) ) { // phpcs:ignore
					wp_send_json_error( $generic_error );
				}

				$type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
				$value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

				if ( $type === 'email' && ! is_email( $value ) ) {
					wp_send_json_error( esc_html__( 'Please provide a valid email address.', 'wp-mail-smtp-pro' ) );
				} elseif ( $type === 'domain' && empty( $value ) ) {
					wp_send_json_error( esc_html__( 'Please provide a domain name.', 'wp-mail-smtp-pro' ) );
				}

				$ses = new SESAuth();

				if ( $ses->do_delete_identity( $value ) === true ) {
					wp_send_json_success(
						sprintf(
							wp_kses( /* translators: %1$s - "Email address" or "Domain"; %2$s - actual email address or domain name. */
								__( '%1$s <code>%2$s</code> was successfully deleted.', 'wp-mail-smtp-pro' ),
								[ 'code' => [] ]
							),
							( $type === 'email' ) ? esc_html__( 'Email address', 'wp-mail-smtp-pro' ) : esc_html__( 'Domain', 'wp-mail-smtp-pro' ),
							esc_html( $value )
						)
					);
				} else {
					$error = Debug::get_last();
					Debug::clear();

					wp_send_json_error(
						esc_html( $error )
					);
				}

				break;
		}
	}
}
