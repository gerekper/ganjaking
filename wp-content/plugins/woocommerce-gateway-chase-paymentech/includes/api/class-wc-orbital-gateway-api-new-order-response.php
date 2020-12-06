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
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * Chase Paymentech Orbital Gateway API New Order Response Class
 *
 * Parses XML received from the Orbital Gateway API for performing a credit
 * card authorization or charge request
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @since 1.0
 * @see Framework\SV_WC_Payment_Gateway_API_Response
 */
class WC_Orbital_Gateway_API_New_Order_Response extends WC_Orbital_Gateway_API_Capture_Response {


	/**
	 * Gets the transaction status message:  Text Message Associated with
	 * RespCode Value, and Profile Management operation status/message if
	 * available
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::get_status_message()
	 * @return string status message
	 */
	public function get_status_message() {

		$message = parent::get_status_message();

		// profile message
		if ( $this->has_profile_proc_status() ) {
			/* translators: Placeholders: %1$s - profile status code, %2$s - profile status message */
			$message .= '. ' . sprintf( __( 'Profile Management Status: %1$s - %2$s', 'woocommerce-gateway-chase-paymentech' ), $this->get_profile_proc_status(), $this->get_customer_profile_message() );
		}

		return $message;
	}


	/**
	 * Returns the result status of the profile management operation, with 0
	 * indicating success.
	 *
	 * Message description given in get_customer_profile_message()
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_New_Order_Response::get_customer_profile_message()
	 * @return string status of profile management operation
	 */
	public function get_profile_proc_status() {

		return $this->get_element( 'ProfileProcStatus' );
	}


	/**
	 * Returns true if this message has the ProfileProcStatus element
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_New_Order_Response::get_profile_proc_status()
	 * @return boolean true if this message has the ProfileProcStatus element, false otherwise
	 */
	public function has_profile_proc_status() {

		return ! is_null( $this->get_profile_proc_status() );
	}


	/**
	 * Returns the text message associated with ProfileProcStatus
	 *
	 * @since 1.0
	 * @return string text message associated with ProfileProcStatus
	 */
	public function get_customer_profile_message() {

		return $this->get_element( 'CustomerProfileMessage' );
	}


	/**
	 * Returns the result of the CSC check
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::get_csc_result()
	 * @return string result of CSC check
	 */
	public function get_csc_result() {

		return trim( $this->get_element( 'CVV2RespCode' ) );
	}


	/**
	 * Returns true if the CSC check was successful
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Authorization_Response::csc_match()
	 * @return boolean true if the CSC check was successful
	 */
	public function csc_match() {

		return 'M' === $this->get_csc_result();
	}


	/**
	 * Returns the last four digits of the account number
	 *
	 * @since 1.0
	 * @return string last four digits of the account number
	 */
	public function get_account_last_four() {

		return substr( $this->get_element( 'AccountNum' ), -4 );
	}


}
