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
 * Chase Paymentech Orbital Gateway API Base Response Class
 *
 * Parses XML received from the Orbital Gateway API
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @since 1.0
 * @see Framework\SV_WC_Payment_Gateway_API_Response
 */
class WC_Orbital_Gateway_API_Response implements Framework\SV_WC_Payment_Gateway_API_Response {


	/** @var WC_Orbital_Gateway_API_Request the request that resulted in this response */
	protected $request;

	/** @var string string representation of this response */
	private $raw_response_xml;

	/** @var SimpleXMLElement response XML object */
	private $response_xml;


	/**
	 * Build a response object from the raw response xml
	 *
	 * @since 1.0
	 * @param WC_Orbital_Gateway_API_Request $request the request that resulted in this response
	 * @param string $raw_response_xml the raw response XML
	 */
	public function __construct( $request, $raw_response_xml ) {

		$this->request = $request;

		$this->raw_response_xml = $raw_response_xml;

		// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
		$this->response_xml     = new SimpleXMLElement( $raw_response_xml, LIBXML_NOCDATA );
	}


	/**
	 * Gets the response transaction id
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_transaction_id()
	 * @return string transaction id
	 */
	public function get_transaction_id() {

		// apparently QuickResp can conditionally return a transaction ref num
		return $this->get_element( 'TxRefNum' );
	}


	/**
	 * Checks if the transaction was successful
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		// most message responses include a ProcStatus
		return '0' === $this->get_process_status();
	}


	/**
	 * Gets the transaction status code
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_status_code()
	 * @return string status code
	 */
	public function get_status_code() {

		// most messages responses include a ProcStatus
		return $this->get_process_status();
	}


	/**
	 * Gets the transaction status message, message associated with get_status_code()
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_status_message()
	 * @return string status message
	 */
	public function get_status_message() {

		return $this->get_element( 'StatusMsg' );
	}


	/**
	 * QuickResponse message can be returned for any request and indicates
	 * an initial Gateway generated error, which doesn't have a response
	 * code, though it does have a process status
	 *
	 * @since 1.0
	 * @return boolean true if this is a QuickResponse message
	 */
	public function is_quick_response() {
		return isset( $this->response_xml->QuickResp );
	}


	/**
	 * Gets the Process Status
	 *
	 * This is the first element that should be checked to determine the result
	 * of a request.  Its the only element that is returned in all response
	 * scenarios and identifies whether transactions have successfully passed
	 * all of the Gateway edit checks:
	 *
	 * + `0` - success
	 *
	 * Status is described by StatusMsg
	 *
	 * @since 1.0
	 * @see WC_Orbital_Gateway_API_Response::get_status_message()
	 * @return string process status
	 */
	public function get_process_status() {

		return $this->get_element( 'ProcStatus' );
	}


	/**
	 * Returns the main response element.  Orbital Gateway XML API response
	 * main element can vary for a given request.  For instance, it might
	 * normally be NewOrderResp but depending upon an error condition could be
	 * QuickResp.
	 *
	 * @since 1.0
	 * @return SimpleXMLElement the main response element
	 */
	protected function get_main_response_element() {

		// get the first child (the main response element)
		return current( $this->response_xml->children() );
	}


	/**
	 * Helper methiod to safely get an element
	 *
	 * @since 1.0
	 * @param string $element_name the element name to retrieve
	 * @return string element value, or null if the element does not exist
	 */
	protected function get_element( $element_name ) {

		// most message responses include a StatusMsg element
		$element = $this->get_main_response_element();

		if ( isset( $element->$element_name ) ) {
			return (string) $element->$element_name;
		}

		return null;
	}


	/**
	 * Returns the string representation of this response
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::to_string()
	 * @return string response
	 */
	public function to_string() {

		$string = $this->raw_response_xml;

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $string ) ) {
			$dom->formatOutput = true;
			$string = $dom->saveXML();
		}

		return $string;
	}


	/**
	 * Returns the string representation of this response with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::to_string_safe()
	 * @return string response safe for logging/displaying
	 */
	public function to_string_safe() {

		$response = $this->to_string();

		// replace merchant authentication
		if ( preg_match( '/<AccountNum>(.*)<\/AccountNum>/', $response, $matches ) ) {
			$response = preg_replace( '/<AccountNum>.*<\/AccountNum>/', '<AccountNum>' . str_repeat( '*', strlen( $matches[1] ) - 4 ) . substr( $matches[1], -4 ) . '</AccountNum>', $response );
		}

		return $response;
	}


	/** No-op methods ******************************************************/


	/**
	 * Returns a message appropriate for a frontend user.  This should be used
	 * to provide enough information to a user to allow them to resolve an
	 * issue on their own, but not enough to help nefarious folks fishing for
	 * info.
	 *
	 * @since 1.1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::get_user_message()
	 * @return string user message, if there is one
	 */
	public function get_user_message() {

		$helper = new WC_Chase_Paymentech_Response_Message_Helper;

		$message_ids = $helper->get_message_ids( array( $this->get_status_code() ) );

		return $helper->get_user_messages( $message_ids );
	}


	/**
	 * Returns false, Orbital Gateway doesn't seem to return a transaction held
	 * status.
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_held()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_held() {
		return false;
	}


	/**
	 * Get the response's payment type.
	 *
	 * @since 1.6.0
	 * @return string
	 */
	public function get_payment_type() {
		return 'credit-card';
	}


}
