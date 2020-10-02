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
 * The Payments API oAuth response class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_oAuth_Response implements Framework\SV_WC_API_Response {


	/** @var string the string representation of this response */
	protected $raw_response;

	/** @var mixed decoded response data */
	public $response_data;


	/**
	 * Constructs the response.
	 *
	 * @since 2.0.0
	 * @param string $raw_response The raw response string
	 */
	public function __construct( $raw_response ) {

		$this->raw_response = $raw_response;

		parse_str( $raw_response, $response_data );

		$this->response_data = $response_data;
	}


	/**
	 * Determines if the response has errors.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function has_api_errors() {

		return isset( $this->response_data['oauth_problem'] );
	}


	/**
	 * Gets the response errors.
	 *
	 * @since 2.0.0
	 * @return \WP_Error
	 */
	public function get_api_errors() {

		$problem = $this->response_data['oauth_problem'];

		if ( 'parameter_rejected' === $problem && ! empty( $this->response_data['oauth_parameters_rejected'] ) ) {
			$problem = $this->response_data['oauth_parameters_rejected'];
		}

		switch ( $problem ) {

			case 'consumer_key_unknown':
			case 'oauth_consumer_key':
				$message = __( 'Invalid Consumer Key', 'woocommerce-gateway-intuit-payments' );
			break;

			default:
				$message = __( 'Authentication error. Please try again.', 'woocommerce-gateway-intuit-payments' );
		}

		return new \WP_Error( $problem, $message );
	}


	/**
	 * Gets the oAuth token.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_token() {

		return isset( $this->response_data['oauth_token'] ) ? $this->response_data['oauth_token'] : '';
	}


	/**
	 * Gets the oAuth token secret.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_token_secret() {

		return isset( $this->response_data['oauth_token_secret'] ) ? $this->response_data['oauth_token_secret'] : '';
	}


	/**
	 * Get the string representation of this response.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Response::to_string()
	 * @return string
	 */
	public function to_string() {

		return $this->raw_response;
	}


	/**
	 * Get the string representation of this response with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_API_Response::to_string_safe()
	 * @return string
	 */
	public function to_string_safe() {

		$string = $this->to_string();

		// mask the returned token
		$string = str_replace( $this->get_token(), str_repeat( '*', strlen( $this->get_token() ) ), $string );

		// mask the returned token secret
		$string = str_replace( $this->get_token_secret(), str_repeat( '*', strlen( $this->get_token_secret() ) ), $string );

		return $string;
	}


}
