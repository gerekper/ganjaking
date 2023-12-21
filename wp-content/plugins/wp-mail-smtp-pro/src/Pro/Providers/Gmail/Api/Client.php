<?php

namespace WPMailSMTP\Pro\Providers\Gmail\Api;

use WPMailSMTP\Geo;
use WP_Error;
use WPMailSMTP\Helpers\Helpers;

/**
 * Client for work with One-Click Setup API.
 *
 * @since 3.11.0
 */
class Client {

	/**
	 * The API base URL.
	 *
	 * @since 3.11.0
	 */
	const API_BASE_URL = 'https://api.wpmailsmtp.com/gmail/v1/';

	/**
	 * One-time token instance.
	 *
	 * @since 3.11.0
	 *
	 * @var OneTimeToken
	 */
	private $one_time_token;

	/**
	 * Site ID generator.
	 *
	 * @since 3.11.0
	 *
	 * @var SiteId
	 */
	private $site_id;

	/**
	 * Credentials.
	 *
	 * @since 3.11.0
	 *
	 * @var array
	 */
	private $credentials;

	/**
	 * Site URL.
	 *
	 * @since 3.11.0
	 *
	 * @var string
	 */
	private $site_url;

	/**
	 * Constructor method.
	 *
	 * @since 3.11.0
	 *
	 * @param array        $credentials    Credentials.
	 * @param OneTimeToken $one_time_token One-time token instance.
	 * @param SiteId       $site_id        Site ID instance.
	 * @param string       $site_url       Site URL.
	 */
	public function __construct( array $credentials, OneTimeToken $one_time_token, SiteId $site_id, $site_url = false ) {

		$this->credentials    = $credentials;
		$this->one_time_token = $one_time_token;
		$this->site_id        = $site_id;
		$this->site_url       = $site_url ? $site_url : site_url();
	}

	/**
	 * Get API base URL.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	public static function get_api_base_url() {

		return defined( 'WPMS_GMAIL_ONE_CLICK_SETUP_API_URL' ) ? WPMS_GMAIL_ONE_CLICK_SETUP_API_URL : self::API_BASE_URL;
	}

	/**
	 * Get authorization URL.
	 *
	 * @since 3.11.0
	 *
	 * @param array $args List of arguments.
	 *
	 * @return string
	 */
	public function get_auth_url( $args ) {

		$url = self::get_api_base_url() . 'auth/new/pro/';

		return add_query_arg( $this->prepare_auth_args( $args ), $url );
	}

	/**
	 * Get re-authorization URL.
	 *
	 * @since 3.11.0
	 *
	 * @param array $args List of arguments.
	 *
	 * @return string
	 */
	public function get_reauth_url( $args ) {

		$args = wp_parse_args(
			$args,
			[
				'key'   => ! empty( $this->credentials['key'] ) ? $this->credentials['key'] : '',
				'token' => ! empty( $this->credentials['token'] ) ? $this->credentials['token'] : '',
			]
		);

		$url = self::get_api_base_url() . 'auth/reauth/pro/';

		return add_query_arg( $this->prepare_auth_args( $args ), $url );
	}

	/**
	 * Prepare arguments for authorization and reauthorization URLs.
	 *
	 * @since 3.11.0
	 *
	 * @param array $args Passed arguments for authorization.
	 *
	 * @return array
	 */
	private function prepare_auth_args( $args ) {

		$args = wp_parse_args(
			$args,
			[
				'tt'      => $this->one_time_token->get(),
				'siteid'  => $this->site_id->get(),
				'version' => WPMS_PLUGIN_VER,
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'network' => 'site',
				'siteurl' => $this->site_url,
				'testurl' => self::get_api_base_url() . 'test/',
				'license' => wp_mail_smtp()->get_license_key(),
				'amc'     => defined( 'WPMS_GMAIL_ONE_CLICK_SETUP_AMC' ) ? WPMS_GMAIL_ONE_CLICK_SETUP_AMC : '',
			]
		);

		if ( ! empty( $args['return'] ) ) {
			$args['return'] = rawurlencode( $args['return'] );
		}

		return $args;
	}

	/**
	 * Determine if the connection is valid.
	 *
	 * @since 3.11.0
	 *
	 * @return true|WP_Error
	 */
	public function verify_auth() {

		$args = [
			'tt' => $this->one_time_token->get(),
		];

		$response = $this->request( 'auth/verify/pro/', $args, 'POST' );

		if ( ! $response->has_errors() ) {
			$this->one_time_token->refresh();
		}

		return $response->has_errors() ? $response->get_errors() : true;
	}

	/**
	 * Remove connection on the API side.
	 *
	 * @since 3.11.0
	 *
	 * @return true|WP_Error
	 */
	public function remove_connection() {

		$response = $this->request(
			'auth/delete/pro/',
			[
				'tt' => $this->one_time_token->get(),
			],
			'POST'
		);

		$this->one_time_token->refresh();

		return $response->has_errors() ? $response->get_errors() : true;
	}

	/**
	 * Send an email.
	 *
	 * @since 3.11.0
	 *
	 * @param string $message     Base64 encoded message.
	 * @param bool   $allow_queue Whether to allow to queue message.
	 *
	 * @return Response
	 */
	public function send_email( $message, $allow_queue = true ) {

		return $this->request(
			'endpoints/send-email/',
			[
				'message'     => $message,
				'allow_queue' => $allow_queue,
			],
			'POST'
		);
	}

	/**
	 * Make a request.
	 *
	 * @since 3.11.0
	 *
	 * @param string $route  Endpoint name.
	 * @param array  $args   List of arguments.
	 * @param string $method Request method.
	 *
	 * @return Response
	 */
	private function request( $route, $args, $method = 'GET' ) {

		$body = wp_parse_args(
			$args,
			[
				'key'      => ! empty( $this->credentials['key'] ) ? $this->credentials['key'] : '',
				'token'    => ! empty( $this->credentials['token'] ) ? $this->credentials['token'] : '',
				'siteurl'  => $this->site_url,
				'license'  => wp_mail_smtp()->get_license_key(),
				'plugin'   => 'wp_mail_smtp',
				'version'  => WPMS_PLUGIN_VER,
				'timezone' => gmdate( 'e' ),
				'network'  => 'site',
				'ip'       => Geo::get_ip(),
			]
		);

		/**
		 * Allow modifying request arguments.
		 *
		 * @since 3.11.0
		 *
		 * @param array $args List of args.
		 */
		$request_args = apply_filters(
			'wp_mail_smtp_pro_providers_gmail_api_client_request_send_args',
			[
				'headers'    => [
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0',
					'Pragma'        => 'no-cache',
					'Expires'       => 0,
				],
				'method'     => $method,
				'body'       => $body,
				'timeout'    => 3000,
				'user-agent' => Helpers::get_default_user_agent(),
			]
		);

		$response = wp_remote_request( self::get_api_base_url() . $route, $request_args );

		return new Response( $response );
	}

	/**
	 * Verify the one time token value.
	 *
	 * @since 3.11.0
	 *
	 * @param string $passed_one_time_token A passed one time token value.
	 *
	 * @return bool
	 */
	public function is_valid_one_time_token( $passed_one_time_token ) {

		return $this->one_time_token->validate( $passed_one_time_token );
	}
}
