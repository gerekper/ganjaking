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
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_15 as Framework;

/**
 * The Payments API oAuth response class.
 *
 * @since 2.1.0
 */
class WC_Intuit_Payments_API_OAuth2_Response extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Gets the access token.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_access_token() {

		return $this->access_token;
	}


	/**
	 * Gets the refresh token.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_refresh_token() {

		return $this->refresh_token;
	}


	/**
	 * Gets the access token expiry.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_access_token_expiry() {

		$expires_in = $this->expires_in;

		if ( empty( $expires_in ) ) {
			$expires_in = 3600;
		}

		return time() + $expires_in;
	}


	/**
	 * Determines if the response has errors.
	 *
	 * @since 2.1.0
	 * @return bool
	 */
	public function has_api_errors() {

		$error = $this->error;

		return ! empty( $error );
	}


	/**
	 * Gets the response errors.
	 *
	 * @since 2.1.0
	 * @return \WP_Error
	 */
	public function get_api_errors() {

		$error = $this->error;

		switch ( $error ) {

			default:
				$message = __( 'Authentication error. Please try again.', 'woocommerce-gateway-intuit-payments' );
		}

		return new \WP_Error( $error, $message );
	}


	/**
	 * Get the string representation of this response with any and all sensitive elements masked
	 * or removed.
	 *
	 * @since 2.1.0
	 * @see Framework\SV_WC_API_Response::to_string_safe()
	 *
	 * @return string
	 */
	public function to_string_safe() {

		$string = $this->to_string();

		// mask the access token
		$string = str_replace( $this->get_access_token(), str_repeat( '*', strlen( $this->get_access_token() ) ), $string );

		// mask the refresh token
		$string = str_replace( $this->get_refresh_token(), str_repeat( '*', strlen( $this->get_refresh_token() ) ), $string );

		return $string;
	}


}
