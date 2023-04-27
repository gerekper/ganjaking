<?php
/**
 * WooCommerce Google Analytics Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API;

use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Google APIs Auth handler.
 *
 * This class functions as a lightweight auth handler that authenticates the plugin with Google APIs via the
 * authentication proxy service.
 *
 * @since 2.0.0
 */
class Auth
{


	/** @var string URL to Google Analytics Pro Authentication proxy */
	protected const PROXY_URL = 'https://wc-ga-pro-proxy.com';

	/** @var string read-only scope for Google Analytics APIs */
	public const SCOPE_ANALYTICS_READONLY = 'https://www.googleapis.com/auth/analytics.readonly';

	/** @var string edit scope for Google Analytics APIs */
	public const SCOPE_ANALYTICS_EDIT = 'https://www.googleapis.com/auth/analytics.edit';

	/** @var array|null the current token */
	protected ?array $token = null;


	/**
	 * Auth class constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->token = $this->parse_json_token();

		// handle Google Client API callbacks
		add_action( 'woocommerce_api_wc-google-analytics-pro/auth', [$this, 'authenticate'] );
	}


	/**
	 * Parses the raw JSON token into an associative array.
	 *
	 * @since 2.0.0
	 *
	 * @return array|null
	 */
	protected function parse_json_token(): ?array {

		return json_decode( $this->get_access_token_json(), true );
	}


	/**
	 * Gets the raw access token JSON
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	protected function get_access_token_json(): ?string {

		return get_option( 'wc_google_analytics_pro_access_token', null );
	}


	/**
	 * Gets the refresh token.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	protected function get_refresh_token(): ?string {

		return $this->token['refresh_token'] ?? null;
	}


	/**
	 * Gets the access token value.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_access_token(): string {

		return $this->token['access_token'] ?? '';
	}


	/**
	 * Gets either the current or a fresh access token if the current one is expired.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_fresh_access_token() : string {

		// refresh token if it's expired
		if ( $this->token && $this->is_access_token_expired() ) {

			try {

				$this->refresh_access_token();

			} catch ( Framework\SV_WC_API_Exception $e ) {

				if ( wc_google_analytics_pro()->get_integration()->debug_mode_on() ) {
					wc_google_analytics_pro()->log( $e->getMessage() );
				}

				return '';
			}
		}

		return $this->token['access_token'] ?? '';
	}


	/**
	 * Determines if the current access token is expired.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_access_token_expired() : bool {

		if ( ! $this->token ) {
			return true;
		}

		// if the token does not have an "expires_in", then it's considered expired
		if ( ! isset( $this->token['expires_in'] ) ) {
			return true;
		}

		$created = $this->token['created'] ?? 0;

		// if the token is set to expire in the next 30 seconds, consider it expired
		return ( $created + ( $this->token['expires_in'] - 30 ) ) < current_time( 'timestamp', true );
	}


	/**
	 * Refreshes the access token.
	 *
	 * @since 2.0.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function refresh_access_token(): void {

		if ( ! $this->get_refresh_token() ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: refresh token not available.' );
		}

		$refresh_url = $this->get_access_token_refresh_url();
		$response    = wp_remote_get( $refresh_url, ['timeout' => MINUTE_IN_SECONDS] );

		// bail out if the request failed
		if ( $response instanceof \WP_Error ) {
			throw new Framework\SV_WC_API_Exception( sprintf( 'Could not refresh access token: %s', json_encode( $response->errors ) ) );
		}

		// bail out if the response was empty
		if ( ! $response || empty( $response['body'] ) ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: response was empty.' );
		}

		// bail out if the Google Analytics proxy produced a 500 server error
		if ( isset( $response['response']['code'] ) && 500 === (int) $response['response']['code'] ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: a server error occurred.' );
		}

		// try to decode the token
		$json_token = base64_decode( $response['body'] );

		// bail out if the token was invalid
		if ( ! ( $this->token = json_decode( $json_token, true ) ) ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: returned token was invalid.' );
		}

		// we're good: update the access token
		$updated = update_option( 'wc_google_analytics_pro_access_token', $json_token );

		// there's a rare possibility we could not store the token
		if ( ! $updated ) {
			throw new Framework\SV_WC_API_Exception( 'Could not refresh access token: a database error occurred.' );
		}
	}


	/**
	 * Authenticates with Google API.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function authenticate(): void {

		// missing token
		if ( ! isset( $_REQUEST['token'] ) || ! $_REQUEST['token'] ) {
			return;
		}

		$json_token = base64_decode( $_REQUEST['token'] );
		$token      = json_decode( $json_token, true );

		// invalid token
		if ( ! $token ) {
			return;
		}

		$this->clear_account_related_settings();

		// update access token
		update_option( 'wc_google_analytics_pro_access_token', $json_token );
		update_option( 'wc_google_analytics_pro_account_id', md5( $json_token ) );

		echo '<script>window.opener.wc_google_analytics_pro.auth_callback(' . $json_token . ')</script>';
		exit();
	}


	/**
	 * Gets the Google API authentication URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string the Google Client API authentication URL
	 */
	public function get_auth_url(): string {

		return self::PROXY_URL . '/auth?callback=' . urlencode( $this->get_callback_url() );
	}


	/**
	 * Gets the Google API refresh access token URL, if a refresh token is available.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	private function get_access_token_refresh_url(): ?string {

		$refresh_url = null;

		if ( $refresh_token = $this->get_refresh_token() ) {
			$refresh_url = self::PROXY_URL . '/auth/refresh?token=' . base64_encode( $refresh_token );
		}

		return $refresh_url;
	}


	/**
	 * Revokes our access to the Google API.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function revoke_access(): void {

		$response = wp_safe_remote_get( $this->get_access_token_revoke_url() );

		// log errors
		if ( is_wp_error( $response ) ) {

			wc_google_analytics_pro()->log( sprintf( 'Could not revoke access token: %s', json_encode( $response->errors ) ) );
		}

		$this->clear_account_related_settings();
	}


	/**
	 * Clear settings, options & cached values related to the authenticated account.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function clear_account_related_settings() : void {

		$settings = get_option( 'woocommerce_google_analytics_pro_settings', [] );

		unset(
			$settings['property'],
			$settings['tracking_id'],
			$settings['ga4_property'],
			$settings['measurement_id']
		);

		update_option( 'woocommerce_google_analytics_pro_settings', $settings );

		delete_option( 'wc_google_analytics_pro_access_token' );
		delete_option( 'wc_google_analytics_pro_account_id' );
		delete_option( 'wc_google_analytics_pro_ga4_data_streams' );
		delete_option( 'wc_google_analytics_pro_ga4_data_streams' );
		delete_option( 'wc_google_analytics_pro_mp_api_secret' );
		delete_transient( 'wc_google_analytics_pro_properties' );
		delete_transient( 'wc_google_analytics_pro_ga4_properties' );
	}


	/**
	 * Gets the Google API revoke access token URL, if a token is available.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null
	 */
	private function get_access_token_revoke_url(): ?string {

		$revoke_url = null;

		if ( $token = $this->get_access_token_json() ) {
			$revoke_url = self::PROXY_URL . '/auth/revoke?token=' . base64_encode( $token );
		}

		return $revoke_url;
	}


	/**
	 * Gets the Google API callback URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string url
	 */
	public function get_callback_url(): string {

		return get_home_url( null, 'wc-api/wc-google-analytics-pro/auth' );
	}


	/**
	 * Gets the API secret for Measurement Protocol for GA4.
	 *
	 * @since 2.0.0
	 *
	 * @return string|null the measurement ID
	 */
	public function get_mp_api_secret() : ?string {

		return get_option( 'wc_google_analytics_pro_mp_api_secret', null );
	}


	/**
	 * Gets the permission scopes the current access token has for the Google Analytics API.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_scopes() : array {

		return explode( ' ', $this->token['scope'] ?? '' );
	}


}
