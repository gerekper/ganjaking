<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * OAuth2 API client for HybridAuth tailored for WP
 *
 * The purpose of this class is to provide an API client that makes use
 * of the WP HTTP API (wp_remote_get/post etc), so that the requests work
 * also on environments with no cURL installed.
 *
 * @since 2.0.0
 */
class WP_OAuth2_Client extends \OAuth2Client {


	/** @var array API request headers **/
	public $request_headers = array();


	/**
	 * Authenticate
	 *
	 * @since 2.0.0
	 * @param string $code
	 * @return mixed
	 * @throws \Exception
	 */
	public function authenticate( $code ) {

		$params = array(
			'client_id'     => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type'    => 'authorization_code',
			'redirect_uri'  => $this->redirect_uri,
			'code'          => $code,
		);

		$response = $this->request( $this->token_url, $params, $this->curl_authenticate_method );

		$response = $this->parseRequestResult( $response );

		if ( ! $response || ! isset( $response->access_token ) ) {
			throw new Exception( "The Authorization Service has return: " . ( is_string( $response->error ) ? $response->error : json_encode( $response->error ) ) );
		}

		if ( isset( $response->access_token  ) ) $this->access_token            = $response->access_token;
		if ( isset( $response->refresh_token ) ) $this->refresh_token           = $response->refresh_token;
		if ( isset( $response->expires_in    ) ) $this->access_token_expires_in = $response->expires_in;

		// calculate when the access token expire
		if ( isset( $response->expires_in ) ) {
			$this->access_token_expires_at = time() + $response->expires_in;
		}

		return $response;
	}


	/**
	 * Get token info
	 *
	 * @since 2.0.0
	 * @param string $accesstoken
	 * @return mixed
	 */
	public function tokenInfo( $accesstoken ) {

		$params['access_token'] = $this->access_token;
		$response               = $this->request( $this->token_info_url, $params );

		return $this->parseRequestResult( $response );
	}


	/**
	 * Get a refresh token
	 *
	 * @since 2.0.0
	 * @param array $parameters
	 * @return mixed
	 */
	public function refreshToken( $parameters = array() ) {

		$params = array(
			'client_id'     => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type'    => 'refresh_token',
		);

		foreach ( $parameters as $k => $v ) {
			$params[ $k ] = $v;
		}

		$response = $this->request( $this->token_url, $params, 'POST' );

		return $this->parseRequestResult( $response );
	}


	/**
	 * Perform an HTTP request
	 *
	 * @since 2.0.0
	 * @param string $url
	 * @param mixed $params Optional.
	 * @param string $type
	 * @return mixed
	 */
	protected function request( $url, $params = false, $type = 'GET' ) {

		\Hybrid_Logger::info( "Enter WP_OAuth2_Client::request( $url )" );
		\Hybrid_Logger::debug( "WP_OAuth2_Client::request(). dump request params: ", serialize( $params ) );

		$wp_http_args = array(
			'method'      => $type,
			'timeout'     => MINUTE_IN_SECONDS,
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify'   => true,
			'blocking'    => true,
			'body'        => $params, // WP will handle urlencoding the params for us
			'headers'     => $this->request_headers,
			'cookies'     => array(),
		);

		// perform request
		$result = wp_safe_remote_request( $url, $wp_http_args );

		// immediately get response headers
		wp_remote_retrieve_headers( $result );

		if ( is_wp_error( $result ) ) {

			\Hybrid_Logger::error( "WP_OAuth2_Client::request(). request failed: ", $result->get_error_message() );

			// network timeout, etc
			$content = 'error=' . $result->get_error_message();
			$this->http_code = $result->get_error_code();

		} else {

			$content = wp_remote_retrieve_body( $result );
			$this->http_code = $result['response']['code'];

		}

		\Hybrid_Logger::debug( "WP_OAuth2_Client::request(). dump request result: ", serialize( $result ) );

		return $content;
	}


	/**
	 * Parse request result
	 *
	 * @since 2.0.0
	 * @param mixed $result
	 * @return mixed
	 */
	protected function parseRequestResult( $result ) {

		if ( json_decode( $result ) ) {
			return json_decode( $result );
		}

		parse_str( $result, $output );

		$result = new StdClass();

		foreach ( $output as $k => $v ) {
			$result->$k = $v;
		}

		return $result;
	}


	/**
	 * Format and sign an OAuth for provider api
	 *
	 * @since 2.0.0
	 * @param string $url
	 * @param string $method
	 * @param array $parameters Optional.
	 * @param bool $decode_json Optional. Defaults to false
	 * @return mixed
	 */
	public function api( $url, $method = "GET", $parameters = array(), $decode_json = true ) {

		if ( strrpos( $url, 'http://' ) !== 0 && strrpos( $url, 'https://' ) !== 0 ) {
			$url = $this->api_base_url . $url;
		}

		// special handling for LinkedIn
		if ( false !== strpos( $url, 'linkedin' ) ) {

			$provider = wc_social_login()->get_provider( 'linkedin' );

			// when using v2 of the LinkedIn API this parameter should not be part of the request
			if ( ! $provider instanceof WC_Social_Login_Provider_LinkedIn || 'v1' === $provider->get_api_version() ) {
				$parameters[ $this->sign_token_name ] = $this->access_token;
			}

		} else {

			$parameters[ $this->sign_token_name ] = $this->access_token;
		}

		$response = null;

		switch ( $method ) {
			case 'GET'    : $response = $this->request( $url, $parameters, "GET"  ); break;
			case 'POST'   : $response = $this->request( $url, $parameters, "POST" ); break;
			case 'DELETE' : $response = $this->request( $url, $parameters, "DELETE" ); break;
			case 'PATCH'  : $response = $this->request( $url, $parameters, "PATCH" ); break;
		}

		if ( $response && $decode_json ) {
			return $this->response = json_decode( $response );
		}

		return $this->response = $response;
	}


}
