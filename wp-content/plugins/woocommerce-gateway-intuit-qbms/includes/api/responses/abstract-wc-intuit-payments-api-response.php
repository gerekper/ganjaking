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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * The base Payments API response class.
 *
 * @since 2.0.0
 */
abstract class WC_Intuit_Payments_API_Response extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Determines if the response contains API errors.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function has_api_errors() {

		$errors = $this->get_api_errors()->get_error_codes();

		return ! empty( $errors );
	}


	/**
	 * Gets any API-related errors.
	 *
	 * @since 2.0.0
	 * @return \WP_Error
	 */
	public function get_api_errors() {

		$errors = $this->get_errors();

		$valid_types = array(
			'account_error',
			'system_error',
			'invalid_request',
		);

		foreach ( $errors->get_error_codes() as $error_code ) {

			// if the error type is not an API error, remove it
			if ( ! in_array( $errors->get_error_data( $error_code ), $valid_types, true ) ) {
				$errors->remove( $error_code );
			}
		}

		return $errors;
	}


	/**
	 * Determines if the response contains errors.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function has_errors() {

		$errors = $this->get_errors()->get_error_codes();

		return ! empty( $errors );
	}


	/**
	 * Gets the errors, if any.
	 *
	 * @since 2.0.0
	 * @return \WP_Error
	 */
	public function get_errors() {

		$errors = new \WP_Error();

		if ( ! empty( $this->response_data->errors ) ) {

			foreach ( $this->response_data->errors as $error ) {

				$message = $error->message;

				if ( ! empty( $error->moreInfo ) ) {
					$message .= ' ' . $error->moreInfo;
				}

				$errors->add( $error->code, $message, $error->type );
			}
		}

		return $errors;
	}


}
