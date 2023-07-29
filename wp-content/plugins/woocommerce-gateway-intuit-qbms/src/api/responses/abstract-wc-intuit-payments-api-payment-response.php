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

use SkyVerge\WooCommerce\PluginFramework\v5_11_4 as Framework;

/**
 * The base Payments API base payment response class.
 *
 * @since 2.0.0
 */
abstract class WC_Intuit_Payments_API_Payment_Response extends WC_Intuit_Payments_API_Response implements Framework\SV_WC_Payment_Gateway_API_Response {


	/**
	 * Determines if the transaction was approved.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function transaction_approved() {

		return ! $this->has_processing_errors() && in_array( $this->get_status_code(), $this->get_approved_statuses(), true );
	}


	/**
	 * Determines if the response contains processing errors.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function has_processing_errors() {

		$errors = $this->get_processing_errors()->get_error_codes();

		return ! empty( $errors );
	}


	/**
	 * Gets any processing-related errors.
	 *
	 * @since 2.0.0
	 * @return array $error_code => $error_message
	 */
	public function get_processing_errors() {

		$errors = $this->get_errors();

		$valid_types = array(
			'fraud_warning',
			'fraud_error',
			'transaction_declined',
		);

		foreach ( $errors->get_error_codes() as $error_code ) {

			// if the error type is not a processing error, remove it
			if ( ! in_array( $errors->get_error_data( $error_code ), $valid_types, true ) ) {
				$errors->remove( $error_code );
			}
		}

		return $errors;
	}


	/**
	 * Gets a list of the transaction statuses considered "approved".
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_approved_statuses() {

		return array();
	}


	/**
	 * Determines if the transaction was held.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function transaction_held() {

		return false;
	}


	/**
	 * Gets the status message.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_status_message() {

		$message = '';

		if ( $this->has_errors() ) {
			$message = implode( '. ', $this->get_errors()->get_error_messages() );
		}

		return $message;
	}


	/**
	 * Gets the status code.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_status_code() {

		if ( $this->has_errors() ) {
			$code = '[' . implode( '][', $this->get_errors()->get_error_codes() ) . ']';
		} else {
			$code = $this->status;
		}

		return $code;
	}


	/**
	 * Gets the transaction ID.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_transaction_id() {

		return $this->id;
	}


	/**
	 * Gets the customer-friendly message.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_user_message() {

		return '';
	}


	/**
	 * Gets the payment type.
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function get_payment_type() {

		return '';
	}


}
