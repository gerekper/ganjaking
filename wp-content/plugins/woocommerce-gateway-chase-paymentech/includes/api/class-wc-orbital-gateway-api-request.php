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

use SkyVerge\WooCommerce\PluginFramework\v5_10_3 as Framework;

/**
 * Chase Paymentech Orbital Gateway API Class
 *
 * Generates XML required by API specs to perform an API request
 *
 * @since 1.0
 */
class WC_Orbital_Gateway_API_Request extends XMLWriter implements Framework\SV_WC_Payment_Gateway_API_Request {

	/** @var string the request xml */
	private $request_xml;

	/** @var string Connection Username set up on Orbital Gateway */
	private $username;

	/** @var string Connection Password used in conjunction with Orbital Username */
	private $password;

	/** @var string 12-digit gateway merchant account number assigned by Chase Paymentech */
	private $merchant_id;

	/** @var string 3-digit merchant terminal ID assigned by Chase Paymentech */
	private $terminal_id;

	/** @var WC_Order optional order object if this request was associated with an order */
	protected $order;

	/** @var int transaction retry trace number */
	protected $retry_trace_number;


	/**
	 * Construct an Orbital Gateway request object
	 *
	 * @since 1.0
	 * @param string $username Orbital Connection Username set up on Orbital Gateway
	 * @param string $password Orbital Connection Password used in conjunction with Orbital Username
	 * @param string $merchant_id 12-digit gateway merchant account number assigned by Chase Paymentech
	 * @param string 3-digit merchant terminal ID assigned by Chase Paymentech
	 */
	public function __construct( $username, $password, $merchant_id, $terminal_id ) {

		$this->username    = $username;
		$this->password    = $password;
		$this->merchant_id = $merchant_id;
		$this->terminal_id = $terminal_id;
	}


	/** API Methods ******************************************************/


	/**
	 * Creates a credit card auth request for the payment method/
	 * customer associated with $order
	 *
	 * @since 1.0
	 * @param WC_Order $order the order object
	 */
	public function credit_card_auth( $order ) {

		if ( $retry_trace_number = $order->get_meta( '_retry_trace_number' ) ) {
			$this->retry_trace_number = $retry_trace_number;
		}

		$this->new_order_request( 'authorization', $order );
	}


	/**
	 * Creates a credit card charge request for the payment method/
	 * customer associated with $order
	 *
	 * @since 1.0
	 * @param WC_Order $order the order object
	 */
	public function credit_card_charge( $order ) {

		if ( $retry_trace_number = $order->get_meta( '_retry_trace_number' ) ) {
			$this->retry_trace_number = $retry_trace_number;
		}

		$this->new_order_request( 'charge', $order );
	}


	/**
	 * Capture funds for a credit card authorization
	 *
	 * @since 1.0
	 * @param WC_Order $order the order object
	 */
	public function credit_card_capture( $order ) {

		$this->init_document();

		// <MarkForCapture>
		$this->startElement( 'MarkForCapture' );


		$this->writeElement( 'OrbitalConnectionUsername', $this->username );
		$this->writeElement( 'OrbitalConnectionPassword', $this->password );

		$this->writeElement( 'OrderID', $this->get_order_number( $order ) );
		$this->writeElement( 'Amount',  $order->get_total() * 100 );  // amount, in pennies

		$this->writeElement( 'BIN',        $this->get_bin() ); // PNS
		$this->writeElement( 'MerchantID', $this->merchant_id );
		$this->writeElement( 'TerminalID', $this->terminal_id ); // PNS

		$this->writeElement( 'TxRefNum', $order->capture->trans_id );

		// </MarkForCapture>
		$this->endElement();

		$this->close_document();
	}


	/**
	 * Sets the refund transaction data.
	 *
	 * @since 1.9.0
	 * @param \WC_Order $order the order object.
	 */
	public function refund( $order ) {

		$this->init_document();

		// <NewOrder>
		$this->startElement( 'NewOrder' );

		$this->writeElement( 'OrbitalConnectionUsername', $this->username );
		$this->writeElement( 'OrbitalConnectionPassword', $this->password );

		$this->writeElement( 'IndustryType', 'EC' );
		$this->writeElement( 'MessageType',  'R' );

		$this->writeElement( 'BIN',        $this->get_bin() );
		$this->writeElement( 'MerchantID', $this->merchant_id );
		$this->writeElement( 'TerminalID', $this->terminal_id );

		$this->writeElement( 'CurrencyCode',     $this->get_currency_code( get_woocommerce_currency() ) );
		$this->writeElement( 'CurrencyExponent', '2' );

		$this->writeElement( 'OrderID', $this->get_order_number( $order ) );
		$this->writeElement( 'Amount',  $order->refund->amount * 100 ); // amount, in pennies

		$this->writeElement( 'TxRefNum', $order->refund->trans_id );

		// </NewOrder>
		$this->endElement();

		$this->close_document();
	}


	/**
	 * Sets the void transaction data.
	 *
	 * @since 1.9.0
	 * @param \WC_Order $order the order object.
	 */
	public function void( $order ) {

		$this->init_document();

		// <Reversal>
		$this->startElement( 'Reversal' );

		$this->writeElement( 'OrbitalConnectionUsername', $this->username );
		$this->writeElement( 'OrbitalConnectionPassword', $this->password );

		$this->writeElement( 'TxRefNum', $order->refund->trans_id );
		$this->writeElement( 'OrderID',  $this->get_order_number( $order ) );

		$this->writeElement( 'BIN',        $this->get_bin() );
		$this->writeElement( 'MerchantID', $this->merchant_id );
		$this->writeElement( 'TerminalID', $this->terminal_id );

		// </Reversal>
		$this->endElement();

		$this->close_document();
	}


	/**
	 * Delete a customer payment profile
	 *
	 * @since 1.0
	 * @param string $customer_ref_num the payment profile identifier
	 */
	public function profile_delete( $customer_ref_num ) {

		$this->init_document();

		// <Profile>
		$this->startElement( 'Profile' );

		$this->writeElement( 'OrbitalConnectionUsername',   $this->username );
		$this->writeElement( 'OrbitalConnectionPassword',   $this->password );
		$this->writeElement( 'CustomerBin',                 $this->get_bin() );
		$this->writeElement( 'CustomerMerchantID',          $this->merchant_id );

		$this->writeElement( 'CustomerRefNum',              $customer_ref_num );

		$this->writeElement( 'CustomerProfileAction',       'D' ); // delete

		// </Profile>
		$this->endElement();

		$this->close_document();
	}


	/**
	 * Returns the ISO-assigned currency code for the named currency
	 *
	 * @since 1.0
	 * @param string $currency only USD is supported
	 * @return string ISO-assigned currency code
	 */
	private function get_currency_code( $currency ) {

		switch( $currency ) {
			case 'USD': return '840';
		}

		return '';
	}


	/**
	 * Add the credit credit card charge or auth elements
	 *
	 * @since 1.0
	 * @param string $request_type one of 'authorization' or 'charge'
	 * @param WC_Order $order the order object
	 */
	private function new_order_request( $request_type, $order ) {

		// store the order object for later use
		$this->order = $order;

		$this->init_document();

		// <NewOrder>
		$this->startElement( 'NewOrder' );

		$this->writeElement( 'OrbitalConnectionUsername',   $this->username );
		$this->writeElement( 'OrbitalConnectionPassword',   $this->password );
		$this->writeElement( 'IndustryType',                'EC' ); // ecommerce
		$this->writeElement( 'MessageType',                 'authorization' == $request_type ? 'A' : 'AC' );  // authorization/charge
		$this->writeElement( 'BIN',                         $this->get_bin() ); // PNS
		$this->writeElement( 'MerchantID',                  $this->merchant_id );
		$this->writeElement( 'TerminalID',                  $this->terminal_id ); // PNS

		/**
		 * Filters the expiry date used for the new order request.
		 *
		 * @since 1.11.2
		 *
		 * @param string $this the credit card expiry date in MMYY format.
		 */
		$expiry_date = apply_filters( 'wc_payment_gateway_chase_paymentech_request_expiry_date', $order->payment->exp_month . substr( $order->payment->exp_year, -2 ) );

		$this->writeElement( 'Exp',                         $expiry_date );
		$this->writeElement( 'CurrencyCode',                $this->get_currency_code( get_woocommerce_currency() ) );  // ISO-assigned code
		$this->writeElement( 'CurrencyExponent',            '2' );  // defined in the Orbital Gateway XML Interface Specification

		$this->writeElement( 'AVSzip', substr( WC_Gateway_Chase_Paymentech::format_postcode( $order->get_billing_postcode( 'edit' ), $order->get_billing_country( 'edit' ) ), 0, 10 ) );

		$this->writeElement( 'AVSaddress1', $this->sanitize_address_field( $order->get_billing_address_1( 'edit' ), 28 ) );

		if ( $billing_address_2 = $order->get_billing_address_2( 'edit' ) ) {
			$this->writeElement( 'AVSaddress2', $this->sanitize_address_field( $billing_address_2, 28 ) );
		}

		$this->writeElement( 'AVScity',        $this->sanitize_address_field( $order->get_billing_city( 'edit' ), 20 ) );
		$this->writeElement( 'AVSstate',       $this->sanitize_address_field( $order->get_billing_state( 'edit' ), 2 ) );
		$this->writeElement( 'AVSphoneNum',    substr( preg_replace( '/\D/', '', $order->get_billing_phone( 'edit' ) ), 0, 14 ) );
		$this->writeElement( 'AVSname',        substr( $order->get_formatted_billing_full_name(), 0, 30 ) );
		$this->writeElement( 'AVScountryCode', substr( $order->get_billing_country( 'edit' ), 0, 2 ) );

		$this->writeElement( 'CustomerRefNum', substr( $order->payment->token, 0, 22 ) );

		// validate order number characters
		$this->writeElement( 'OrderID',        $this->get_order_number( $order ) );

		$this->writeElement( 'Amount',         $order->payment_total * 100 );  // amount, in pennies

		if ( $customer_note = $order->get_customer_note( 'edit' ) ) {
			$this->writeElement( 'Comments', substr( $customer_note, 0, 64 ) );
		}

		$this->writeElement( 'CustomerIpAddress',   substr( $_SERVER['REMOTE_ADDR'], 0, 45 ) );
		$this->writeElement( 'CustomerBrowserName', substr( $_SERVER['HTTP_USER_AGENT'], 0, 60 ) );

		$mit_message_type = $this->get_mit_message_type( $order );

		if ( '' !== $mit_message_type ) {

			// CIT/MIT type string
			$this->writeElement( 'MITMsgType', substr( $mit_message_type, 0, 4 ) );

			// indicates that we are using stored credentials -- direct API usage will always use stored credentials
			$this->writeElement( 'MITStoredCredentialInd', 'Y' );
		}

		// </NewOrder>
		$this->endElement();

		$this->close_document();

	}


	/**
	 * Gets the appropriate MIT type for the given order.
	 *
	 * @since 1.13.0
	 *
	 * @param \WC_Order $order the order
	 * @return string the message type
	 */
	private function get_mit_message_type( $order ) {

		// standard merchant-initiated payment using a saved payment method
		$type = 'MUSE';

		if ( isset( $order->payment->card_type ) && Framework\SV_WC_Payment_Gateway_Helper::CARD_TYPE_AMEX === $order->payment->card_type ) {

			// do not send MIT elements for American Express cards
			$type = '';

		} elseif ( ! empty( $order->payment->recurring ) ) {

			$type = 'CREC';

			/** @see SV_WC_Payment_Gateway_Integration_Subscriptions::add_subscriptions_details_to_order() */
			if ( ! empty( $order->payment->subscriptions ) ) {

				foreach ( $order->payment->subscriptions as $subscription_data ) {

					if ( isset( $subscription_data->is_renewal ) && $subscription_data->is_renewal ) {

						$type = 'MREC';
						break;
					}
				}
			}

		} elseif ( 'checkout' === $order->get_created_via() ) {

			// standard customer-initiated payment using saved credentials
			$type = 'CUSE';
		}

		return $type;
	}


	/**
	 * Strip invalid characters from the address fields.
	 *
	 * @since 1.4.1
	 * @param string $value The field value to sanitize.
	 * @param int $max_length Optional. The max value length.
	 * @return string $value The sanitized value.
	 */
	private function sanitize_address_field( $value, $max_length = null ) {

		$invalid_characters = array( '%', '|', '^', '/', '\\', '<', '>' );

		$value = str_replace( $invalid_characters, '', $value );

		if ( is_numeric( $max_length ) ) {
			$value = Framework\SV_WC_Helper::str_truncate( $value, $max_length, '' );
		}

		return $value;
	}


	/**
	 * Gets the order number for the given order
	 *
	 * @since 1.0
	 * @param WC_Order $order the order
	 * @return string the order number
	 */
	private function get_order_number( $order ) {

		$order_number = ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce-gateway-chase-paymentech' ) );

		if ( preg_match( '/^[a-zA-Z0-9\-$@&, ]+$/', $order_number ) ) {
			// passed chase validations
			return substr( $order_number, 0, 22 );
		} else {
			// otherwise just use the underlying order id
			return substr( $order->get_id(), 0, 22 );
		}

	}


	/**
	 * Helper to return completed XML document
	 *
	 * @since 1.0
	 * @return string XML
	 */
	public function to_xml() {
		return $this->request_xml;
	}


	/**
	 * Returns the string representation of this request
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Request::to_string()
	 * @return string request XML
	 */
	public function to_string() {

		$string = $this->to_xml();

		$dom = new DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $string ) ) {
			$dom->formatOutput = true;
			$string = $dom->saveXML();
		}

		return $string;

	}


	/**
	 * Returns the string representation of this request with any and all
	 * sensitive elements masked or removed
	 *
	 * @since 1.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Request::to_string_safe()
	 * @return string the request XML, safe for logging/displaying
	 */
	public function to_string_safe() {

		$request = $this->to_string();

		// replace merchant authentication
		if ( preg_match( '/<OrbitalConnectionPassword>(.*)<\/OrbitalConnectionPassword>/', $request, $matches ) ) {
			$request = preg_replace( '/<OrbitalConnectionPassword>.*<\/OrbitalConnectionPassword>/', '<OrbitalConnectionPassword>' . str_repeat( '*', strlen( $matches[1] ) ) . '</OrbitalConnectionPassword>', $request );
		}

		return $request;

	}


	/** Helper Methods ******************************************************/


	/**
	 * Initialize the document by opening memory, adding doc encoding, qbmsxml
	 * version, opening the Request root element
	 *
	 * @since 1.0
	 */
	private function init_document() {

		// Create XML document in memory
		$this->openMemory();

		// Set XML version & encoding
		$this->startDocument( '1.0', 'UTF-8' );

		// root element
		$this->startElement( 'Request' );

	}


	/**
	 * Closes the XML document and saves the request XML
	 *
	 * @since 1.0
	 */
	private function close_document() {

		// </Request>
		$this->endElement();

		$this->endDocument();

		// save the request xml
		$this->request_xml = $this->outputMemory();
	}


	/**
	 * Returns the method for this request. Chase Paymentech uses the API default
	 * (POST)
	 *
	 * @since 1.4.0
	 * @return null
	 */
	public function get_method() { }


	/**
	 * Returns the request path for this request. Chase Paymentech request paths
	 * do not vary per request.
	 *
	 * @since 1.4.0
	 * @return string
	 */
	public function get_path() {
		return '';
	}


	/**
	 * Returns the order associated with this request, if there was one
	 *
	 * @since 1.0
	 * @return WC_Order the order object
	 */
	public function get_order() {
		return $this->order;
	}


	/**
	 * Returns the Retry Trace Number for the transaction
	 *
	 * @since 1.0
	 * @return int retry trace number, if any, which is between 1-9999999999999999
	 */
	public function get_retry_trace_number() {
		return $this->retry_trace_number;
	}


	/**
	 * Returns the BIN.
	 *
	 * @since 1.11.1
	 *
	 * @return string the BIN
	 */
	public function get_bin() {

		/**
		 * Filters the BIN sent in requests to Chase Paymentech.
		 *
		 * @since 1.11.1
		 *
		 * @param string $bin the BIN. Accepted values '000001' and '000002'. Defaults to '000002'.
		 */
		return apply_filters( 'wc_payment_gateway_chase_paymentech_request_bin', '000002' );
	}


	/**
	 * Gets the request query params.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function get_params() {

		return [];
	}


	/**
	 * Gets the request data.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function get_data() {

		return [];
	}


}
