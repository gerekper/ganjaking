<?php
/**
 * WooCommerce Chase Paymentech
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_7 as Framework;

/**
 * Chase Paymentech Orbital Gateway API Credit Card Capture Response Class
 *
 * Parses XML received from the Orbital Gateway API
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @since 1.0
 * @see WC_Orbital_Gateway_API_Response
 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response
 */
class WC_Orbital_Gateway_API_Capture_Response extends WC_Orbital_Gateway_API_Response implements Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	/**
	 * Gets the transaction status code
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::get_status_code()
	 * @return string status code
	 */
	public function get_status_code() {

		// QuickResponse error
		if ( $this->is_quick_response() ) {
			return parent::get_status_code();
		}

		// normal response
		return $this->get_response_code();
	}


	/**
	 * Checks if the transaction was successful
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		// QuickResponse error
		if ( $this->is_quick_response() ) {
			return parent::transaction_approved();
		}

		// normal response
		return parent::transaction_approved() && '1' === $this->get_approval_status() && '00' === $this->get_status_code();
	}


	/**
	 * Returns false, Orbital Gateway doesn't seem to return a transaction held
	 * status.
	 *
	 * @since 1.1.0
	 * @see WC_Orbital_Gateway_API_Response::transaction_held()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_held() {

		// QuickResponse error
		if ( $this->is_quick_response() ) {
			return parent::transaction_held();
		}

		// technically these status codes are listed as HostRespCode's, but they all seem to indicate a held order
		return '0' === $this->get_approval_status() && in_array( $this->get_status_code(), array( '01', '04', '19',  ) );
	}


	/**
	 * Gets the Approval Status
	 *
	 * Conditional on Process Status returning a 0 (or successful) response. If
	 * so, the Approval Status identifies the result of the authorization
	 * request to the host system:
	 *
	 * + `0` - declined
	 * + `1` - approved
	 * + `2` - message/system error
	 *
	 * @since 1.0
	 * @return string approval status
	 */
	public function get_approval_status() {

		return $this->get_element( 'ApprovalStatus' );
	}


	/**
	 * Gets the Response Code
	 *
	 * Normalized authorization response code issued by the host system
	 * (Salem/PNS), which identifies an approval (00) or the reason for a
	 * decline or error.
	 *
	 * Code is described by StatusMsg
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::get_status_message()
	 * @return string approval status
	 */
	public function get_response_code() {

		return $this->get_element( 'RespCode' );
	}


	/**
	 * Gets the Issuer Approval Code
	 *
	 * Unique transactional-level code issued by the bank or service establishment for approvals
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_authorization_code()
	 * @return string approval status
	 */
	public function get_authorization_code() {

		return $this->get_element( 'AuthCode' );
	}


	/**
	 * Returns the result of the AVS check
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_avs_result()
	 * @return string result of the AVS check, if any
	 */
	public function get_avs_result() {

		return trim( $this->get_element( 'AVSRespCode' ) );
	}


	/** No-op methods ******************************************************/


	/**
	 * Returns the result of the CSC check
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CSC check
	 */
	public function get_csc_result() {
		// Mark for Capture doesn't include a CSC result
		return null;
	}


	/**
	 * Returns true if the CSC check was successful
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 * @return boolean true if the CSC check was successful
	 */
	public function csc_match() {
		// Mark for Capture doesn't include a CSC result
		return null;
	}


}
