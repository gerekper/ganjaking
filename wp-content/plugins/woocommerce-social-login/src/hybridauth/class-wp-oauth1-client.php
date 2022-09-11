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
 * OAuth1 API client for HybridAuth tailored for WP
 *
 * @since 2.0.0
 */
class WP_OAuth1_Client extends \OAuth1Client {


	/** @var array API request headers **/
	public $request_headers = array();

	/** @var bool whether to add auth header or not **/
	public $auth_header = true;


	/**
	 * Perform an HTTP request
	 *
	 * @since 2.0.0
	 * @param string $url
	 * @param string $method
	 * @param mixed $params Optional.
	 * @return mixed
	 */
	public function request( $url, $method, $params = null, $auth_header = null, $content_type = null, $multipart = false ) {

		\Hybrid_Logger::info( "Enter WP_OAuth1_Client::request( $url )" );
		\Hybrid_Logger::debug( "WP_OAuth1_Client::request(). dump request params: ", serialize( $params ) );

		$headers = $this->request_headers;

		if ( $multipart ) {
			$headers['Expect'] = $auth_header;
		} elseif ( $content_type ) {
			$headers['Expect'] = 'Content-Type: ' . $content_type;
		}

		if ( 'POST' === $method && ! empty( $auth_header ) && $this->auth_header && ! $multipart ) {

			$headers['Content-Type'] = 'application/atom+xml';

			$auth_header_parts = explode( ': ', $auth_header );

			$headers[ $auth_header_parts[0] ] = $auth_header_parts[1];
		}

		$wp_http_args = array(
			'method'      => $method,
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
		$responseHeaders = wp_remote_retrieve_body( $result );

		if ( is_wp_error( $result ) ) {

			\Hybrid_Logger::error( "WP_OAuth1_Client::request(). request failed: ", $result->get_error_message() );

			// network timeout, etc
			$content = $result->get_error_message();

		} else {

			$content = wp_remote_retrieve_body( $result );
		}

		$this->http_code = $result['response']['code'];

		\Hybrid_Logger::debug( "WP_OAuth1_Client::request(). dump request result: ", serialize( $result ) );

		return $content;
	}


}
