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
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Chase Paymentech Orbital Gateway Customer Profile Delete Response
 *
 * Represents the reponse from a Delete Profile request for removing a tokenized
 * payment method.  This response is a bit of an oddball and mostly different
 * from the others
 *
 * @since 1.0
 */
class WC_Orbital_Gateway_API_Profile_Delete_Response extends WC_Orbital_Gateway_API_Response {


	/**
	 * Checks if the transaction was successful
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		// QuickResponse element Indicates an initial Gateway generated error
		if ( $this->is_quick_response() ) {
			return parent::transaction_approved();
		}

		// Success or "Cannot process profile. Profile does not exist for Cust Ref Num and MID." but we don't care, we just want the token to be gone
		return '0' === $this->get_status_code() || '9581' === $this->get_status_code();
	}


	/**
	 * Gets the profile management status message:  Text Message Associated with
	 * ProfileProcStatus Value
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::get_status_message()
	 * @return string status message
	 */
	public function get_status_message() {

		// QuickResponse element Indicates an initial Gateway generated error
		if ( $this->is_quick_response() ) {
			return parent::get_status_message();
		}

		// normal behavior
		return $this->get_customer_profile_message();
	}


	/**
	 * Gets the transaction status code
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::get_status_code()
	 * @return string status code
	 */
	public function get_status_code() {

		// QuickResponse element Indicates an initial Gateway generated error
		if ( $this->is_quick_response() ) {
			return parent::get_status_code();
		}

		// normal behavior
		return $this->get_profile_proc_status();
	}


	/**
	 * Gets the Profile Process Status of the profile management operation
	 *
	 * + `0` - success
	 *
	 * Status message is given by CustomerProfileMessage
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Profile_Delete_Response::get_profile_proc_status()
	 * @return string process status
	 */
	public function get_profile_proc_status() {

		return $this->get_element( 'ProfileProcStatus' );
	}


	/**
	 * Text Message Associated with ProfileProcStatus Value
	 *
	 * @since 1.0
	 * @return string Text Message Associated with ProfileProcStatus Value
	 */
	public function get_customer_profile_message() {

		return $this->get_element( 'CustomerProfileMessage' );
	}


}
