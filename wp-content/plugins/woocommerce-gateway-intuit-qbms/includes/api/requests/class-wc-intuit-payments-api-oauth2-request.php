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

use SkyVerge\WooCommerce\PluginFramework\v5_10_4 as Framework;

/**
 * The OAuth 2 request class.
 *
 * @since 2.1.0
 */
class WC_Intuit_Payments_API_OAuth2_Request implements Framework\SV_WC_API_Request {


	/** @var string request method, one of HEAD, GET, PUT, PATCH, POST, DELETE */
	protected $method;

	/** @var string request path */
	protected $path = '';

	/** @var array request data */
	protected $data = array();


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
	 * Gets the request method.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_method() {

		return $this->method;
	}


	/**
	 * Gets the request path.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_path() {

		return $this->path;
	}


	/**
	 * Gets the request params.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function get_params() {

		return $this->params;
	}


	/**
	 * Gets the request data.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	public function get_data() {

		return $this->data;
	}


	/**
	 * Get the string representation of this request.
	 *
	 * @since 2.1.0
	 * @see Framework\SV_WC_API_Request::to_string()
	 *
	 * @return string
	 */
	public function to_string() {

		return ! empty( $this->data ) ? http_build_query( $this->data, '', '&' ) : '';
	}


	/**
	 * Get the string representation of this request with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 2.1.0
	 * @see Framework\SV_WC_API_Request::to_string_safe()
	 *
	 * @return string
	 */
	public function to_string_safe() {

		$string = $this->to_string();

		// mask the authorization code
		if ( ! empty( $this->data['code'] ) ) {
			$string = str_replace( $this->data['code'], str_repeat( '*', strlen( $this->data['code'] ) ), $string );
		}

		// mask the refresh token
		if ( ! empty( $this->data['refresh_token'] ) ) {
			$string = str_replace( $this->data['refresh_token'], str_repeat( '*', strlen( $this->data['refresh_token'] ) ), $string );
		}

		return $string;
	}


}
