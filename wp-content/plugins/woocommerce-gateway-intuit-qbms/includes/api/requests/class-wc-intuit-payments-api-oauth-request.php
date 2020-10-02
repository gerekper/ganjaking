<?php
/**
 * WooCommerce Intuit Payments
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit QBMS to newer
 * versions in the future. If you wish to customize WooCommerce Intuit QBMS for your
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_8_1 as Framework;

/**
 * The Intuit Payments API OAuth 1 request class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_oAuth_Request implements Framework\SV_WC_API_Request {


	/** @var string The request method, one of HEAD, GET, PUT, PATCH, POST, DELETE */
	protected $method;

	/** @var string the request URL. Necessary when generating the signature */
	protected $request_url;

	/** @var array The request parameters, if any */
	protected $params = array();

	protected $data = array();

	/** @var string the request token secret, if any */
	protected $token_secret = '';

	/** @var \WC_Gateway_Inuit_Payments the gateway object */
	protected $gateway;


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 * @param \WC_Gateway_Inuit_Payments $gateway the gateway object
	 */
	public function __construct( $url, WC_Gateway_Inuit_Payments $gateway ) {

		$this->gateway = $gateway;

		$this->request_url = $url;
	}


	/**
	 * Sets the data needed for generating an access token from an authorization code.
	 *
	 * @since 2.1.0
	 *
	 * @param string $code authorization code from the initial permissions request
	 * @param string $redirect_uri oAuth redirect URL, as defined in the merchant's app settings
	 */
	public function set_authorization_data( $code, $redirect_uri ) {

		$this->data = array(
			'grant_type'   => 'authorization_code',
			'code'         => $code,
			'redirect_uri' => $redirect_uri,
		);
	}


	/**
	 * Sets the data needed to refresh an access token.
	 *
	 * @since 2.1.0
	 *
	 * @param string $refresh_token refresh token
	 */
	public function set_refresh_data( $refresh_token ) {

		$this->data = array(
			'refresh_token' => $refresh_token,
			'grant_type'    => 'refresh_token',
		);
	}


	/**
	 * Sets the data need to get a access request token.
	 *
	 * @since 2.0.0
	 *
	 * @param string $callback_url OAuth callback URL
	 */
	public function set_request_token_params( $callback_url ) {

		$this->method = 'GET';

		$this->set_base_params();

		$this->params['oauth_callback'] = $callback_url;
	}


	/**
	 * Sets the data need to get an access token.
	 *
	 * @since 2.0.0
	 *
	 * @param string $token request token
	 * @param string $secret request token secret
	 * @param string $verifier request verifier
	 */
	public function set_access_token_params( $token, $secret, $verifier ) {

		$this->method = 'GET';

		$this->set_base_params();

		$this->params['oauth_token']    = $token;
		$this->params['oauth_verifier'] = $verifier;

		$this->token_secret = $secret;
	}


	/**
	 * Sets the base oAuth params that are common among all requests.
	 *
	 * @since 2.0.0
	 */
	public function set_base_params() {

		$this->params = WC_Intuit_Payments_oAuth_Helper::get_common_params( $this->get_consumer_key() );
	}


	/**
	 * Gets the request params.
	 *
	 * Overridden to generate a unique signature based on the params.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_params() {

		$params = $this->params;

		$params['oauth_signature'] = WC_Intuit_Payments_oAuth_Helper::generate_signature( array(
			'method'          => $this->get_method(),
			'url'             => $this->get_request_url(),
			'params'          => $params,
			'consumer_secret' => $this->get_consumer_secret(),
			'token_secret'    => $this->token_secret,
		) );

		return $params;
	}


	/**
	 * Gets the request URL.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_request_url() {

		return $this->request_url;
	}


	/**
	 * Gets the request method.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Request::get_method()
	 * @return string
	 */
	public function get_method() {

		return $this->method;
	}


	/**
	 * Gets the request path.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Request::get_path()
	 * @return string
	 */
	public function get_path() {

		return '';
	}


	/**
	 * Gets the request data.
	 *
	 * @since 2.3.0
	 *
	 * @see Framework\SV_WC_API_Request::get_data()
	 * @return array
	 */
	public function get_data() {

		return $this->data;
	}


	/**
	 * Gets the oAuth consumer key.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_consumer_key() {

		return $this->get_gateway()->get_consumer_key();
	}


	/**
	 * Gets the oAuth consumer key.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_consumer_secret() {

		return $this->get_gateway()->get_consumer_secret();
	}


	/**
	 * Gets the gateway object that initiated this request.
	 *
	 * @since 2.0.0
	 * @return \WC_Gateway_Inuit_Payments the gateway object
	 */
	protected function get_gateway() {

		return $this->gateway;
	}


	/**
	 * Get the string representation of this request.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Request::to_string()
	 * @return string
	 */
	public function to_string() {

		return ! empty( $this->data ) ? http_build_query( $this->data, '', '&' ) : '';
	}


	/**
	 * Get the string representation of this request with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 4.3.0
	 * @see Framework\SV_WC_API_Request::to_string_safe()
	 * @return string
	 */
	public function to_string_safe() {

		return $this->to_string();
	}


}
