<?php
/*  Copyright 2013  Your Inspiration Themes  (email : plugins@yithemes.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * API handler class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET_CIM_API' ) ) {
	/**
	 * WooCommerce Authorize.net CIM API handler class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAUTHNET_CIM_API {

		/**
		 * @const Sandbox payment url
		 */
		const AUTHORIZE_NET_XML_SANDBOX_PAYMENT_URL = 'https://apitest.authorize.net/xml/v1/request.api';

		/**
		 * @const Public payment url
		 */
		const AUTHORIZE_NET_XML_PRODUCTION_PAYMENT_URL = 'https://api2.authorize.net/xml/v1/request.api';

		/**
		 * @var string Whether or not we're using a development env
		 */
		public $sandbox;

		/**
		 * @var string Authorize.net Login ID
		 */
		public $login_id;

		/**
		 * @var string Authorize.net transaction key
		 */
		public $transaction_key;

		/**
		 * @var bool Whether or not transactions should be itemized
		 */
		public $itemized;

		/**
		 * @var bool whether or not transaction should handle payment profiles
		 */
		public $cim_handling;

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCAUTHNET_CIM_API
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Instance of the class XMLWriter, used to create xml request to server
		 *
		 * @var \XMLWriter
		 * @since 1.0.0
		 */
		protected $xml_writer = null;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAUTHNET_CIM_API
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAUTHNET_CIM_API
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->xml_writer = new XMLWriter();
		}

		/**
		 * Process a request to Authorize.net servers
		 *
		 * @param $xml string Xml to send through request
		 *
		 * @return string Xml response from the server
		 * @throws Exception
		 * @since 1.0.0
		 */
		public function do_request( $xml ) {
			if ( empty( $this->login_id ) || empty( $this->transaction_key ) ) {
				return false;
			}

			if ( $this->sandbox ) {
				$process_url = self::AUTHORIZE_NET_XML_SANDBOX_PAYMENT_URL;
			} else {
				$process_url = self::AUTHORIZE_NET_XML_PRODUCTION_PAYMENT_URL;
			}

			$wp_http_args = array(
				'timeout'     => apply_filters( 'yith_wcauthnet_cim_api_timeout', 45 ),
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'headers'     => array(
					'accept'       => 'application/xml',
					'content-type' => 'application/xml'
				),
				'body'        => $xml,
				'cookies'     => array(),
				'user-agent'  => "PHP " . PHP_VERSION
			);

			$response = wp_remote_post( $process_url, $wp_http_args );

			// Check for Network timeout, etc.
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			// return blank XML document if response body doesn't exist
			$response = ( isset( $response['body'] ) ) ? $response['body'] : '<?xml version="1.0" encoding="utf-8"?>';

			return $response;
		}

		/**
		 * Parse xml response from the server
		 *
		 * @param $response string XML response from the server
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function parse_response( $response ) {
			// Remove namespace as SimpleXML throws warnings with invalid namespace URI provided by Authorize.net
			$response = preg_replace( '/[[:space:]]xmlns[^=]*="[^"]*"/i', '', $response );

			// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
			$response = new SimpleXMLElement( $response, LIBXML_NOCDATA );

			return $response;
		}

		/**
		 * Execute a request for payment transaction, and return parsed response
		 *
		 * @param $order           \WC_Order Order to pay
		 * @param $payment_details \StdClass Payment details
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function create_payment_transaction( $order, $payment_details, $transaction_mode = 'authCaptureTransaction' ) {
			$response = $this->do_request( $this->get_create_payment_transaction_xml( $order, $payment_details, $transaction_mode ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Execute a request for refund transaction, and return parsed response
		 *
		 * @param $order           \WC_Order Order to refund
		 * @param $amount          float Amount to refund
		 * @param $payment_details \StdClass Payment details, masked
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function crete_refund_transaction( $order, $amount, $payment_details ) {
			$response = $this->do_request( $this->get_create_refund_transaction_xml( $order, $amount, $payment_details ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Execute a request for create customer profile, and return parsed response
		 *
		 * @param $user    \WP_User User to map in Authorize.net servers
		 * @param $payment \StdClass|bool Payment details (false if no payment to add)
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.9
		 */
		public function create_customer_profile( $user, $payment = false ) {
			$response = $this->do_request( $this->get_create_customer_profile_xml( $user, $payment ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Execute a request for create customer payment profile, and return parsed response
		 *
		 * @param $order               \WC_Order Order to use as a base for billTo section
		 * @param $customer_profile_id string Customer profile unique ID
		 * @param $payment             \StdClass Payment details
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function create_customer_payment_profile( $order, $customer_profile_id, $payment ) {
			$response = $this->do_request( $this->get_create_customer_payment_profile_xml( $order, $customer_profile_id, $payment ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Execute a request for create customer payment profile, and return parsed response
		 *
		 * @param $order               \WC_Order Order to use as a base for billTo section
		 * @param $customer_profile_id string Customer profile unique ID
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function update_customer_profile( $order, $customer_profile_id ) {
			$response = $this->do_request( $this->get_update_customer_profile_xml( $order, $customer_profile_id ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Execute a request for update customer payment profile, and return parsed response
		 *
		 * @param $order                       \WC_Order Order to use as a base for billTo section
		 * @param $customer_profile_id         string Customer profile unique ID
		 * @param $customer_payment_profile_id string Customer payment profile unique ID
		 * @param $payment_details             \StdClass Payment details
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function update_customer_payment_profile( $order, $customer_profile_id, $customer_payment_profile_id, $payment_details ) {
			$response = $this->do_request( $this->get_update_customer_payment_profile_xml( $order, $customer_profile_id, $customer_payment_profile_id, $payment_details ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Execute a request for delete customer payment profile, and return parsed response
		 *
		 * @param $customer_profile_id         string Customer profile unique ID
		 * @param $customer_payment_profile_id string Customer payment profile unique ID
		 *
		 * @return \SimpleXMLElement Parsed xml
		 * @since 1.0.0
		 */
		public function delete_customer_payment_profile( $customer_profile_id, $customer_payment_profile_id ) {
			$response = $this->do_request( $this->get_delete_customer_payment_profile_xml( $customer_profile_id, $customer_payment_profile_id ) );

			if ( ! $response ) {
				return false;
			}

			return $this->parse_response( $response );
		}

		/**
		 * Returns xml string to create a payment transaction on Authorize.net
		 *
		 * @param $order            \WC_Order Order to pay
		 * @param $payment_details  \StdClass Payment details
		 * @param $transaction_mode string Transaction mode
		 *
		 * @return string XML request
		 * @since 1.0.0
		 */
		public function get_create_payment_transaction_xml( $order, $payment_details, $transaction_mode = 'authCaptureTransaction' ) {
			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'createTransactionRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			// adds authentication info
			$this->add_auth_xml();

			// <transactionRequest>
			$this->xml_writer->startElement( 'transactionRequest' );

			// <transactionType>authCaptureTransaction</transactionType>
			$this->xml_writer->writeElement( 'transactionType', $transaction_mode );

			// <amount>Order Amount</amount>
			$this->xml_writer->writeElement( 'amount', $order->get_total() );

			// <currencyCode>Order Currency Code</currencyCode>
			$this->xml_writer->writeElement( 'currencyCode', method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency() );

			// adds payment detail
			$this->add_payment_xml( $payment_details );

			// adds profile detail
			$this->add_profile_xml( $payment_details );

			// <order>
			$this->xml_writer->startElement( 'order' );

			// <invoiceNumber>Order ID</invoiceNumber>
			$this->xml_writer->writeElement( 'invoiceNumber', apply_filters( 'yith_wcauthnet_invoice_number', $order->get_order_number(), $order ) );

			// <description>Order description</description>
			$this->xml_writer->writeElement( 'description', apply_filters( 'yith_wcauthnet_order_description', 'Order ' . $order->get_order_number(), $order ) );

			// </order>
			$this->xml_writer->endElement();

			if ( $this->itemized ) {
				// <lineItems>
				$this->xml_writer->startElement( 'lineItems' );

				// add line items informations for itemized transactions
				$this->add_line_items_xml( $order );

				// </lineItems>
				$this->xml_writer->endElement();
			}

			if ( $order->get_total_tax() > 0 ) {
				$this->add_tax_xml( $order );
			}

			if ( ( method_exists( $order, 'get_shipping_total' ) ? $order->get_shipping_total() : $order->get_total_shipping() ) > 0 ) {
				$this->add_shipping_xml( $order );
			}

			// <customer>
			$this->xml_writer->startElement( 'customer' );

			$this->xml_writer->writeElement( 'id', method_exists( $order, 'get_customer_id' ) && $order->get_customer_id() ? $order->get_customer_id() : get_current_user_id() );

			if ( $billing_email = yit_get_prop( $order, 'billing_email', true ) ) {
				$this->xml_writer->writeElement( 'email', $billing_email );
			}

			// </customer>
			$this->xml_writer->endElement();

			// <billTo>
			if ( $payment_details->type != 'profile' ) {
				$this->xml_writer->startElement( 'billTo' );

				// add billing informations
				$this->add_address_xml( $order );

				// </billTo>
				$this->xml_writer->endElement();
			}

			if ( yit_get_prop( $order, 'shipping_country', true ) ) {
				// <shipTo>
				$this->xml_writer->startElement( 'shipTo' );

				// add billing informations
				$this->add_address_xml( $order, 'shipping' );

				// </shipTo>
				$this->xml_writer->endElement();
			}

			if ( $order->get_customer_ip_address() && filter_var( $order->get_customer_ip_address(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
				// <customerIP>192.168.1.1</customerIP>
				$this->xml_writer->writeElement( 'customerIP', $order->get_customer_ip_address() );
			}

			$this->add_user_info( $order );

			// </transactionRequest>
			$this->xml_writer->endElement();

			// </createTransactionRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Returns xml string to create a refund transaction on Authorize.net
		 *
		 * @param $order           \WC_Order Order to refund
		 * @param $amount          float Amount to refund
		 * @param $payment_details \StdClass Payment details
		 *
		 * @return string XML request
		 * @since 1.0.0
		 */
		public function get_create_refund_transaction_xml( $order, $amount, $payment_details ) {
			if ( empty( $amount ) ) {
				$amount = $order->get_total();
			}

			$trans_id    = $order->get_transaction_id();
			$order_total = $order->get_total();

			/**
			 * When processing entire amount refund, transaction should be voided
			 *
			 * @since 1.1.14
			 */
			$is_void = $order_total === $amount;

			if ( empty( $trans_id ) ) {
				return false;
			}

			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <createTransactionRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'createTransactionRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			// adds authentication info
			$this->add_auth_xml();

			// <transactionRequest>
			$this->xml_writer->startElement( 'transactionRequest' );

			// <transactionType>authCaptureTransaction</transactionType>
			$this->xml_writer->writeElement( 'transactionType', $is_void ? 'voidTransaction' : 'refundTransaction' );

			// <refTransId>123456</refTransId>
			$this->xml_writer->writeElement( 'refTransId', $trans_id );

			// set refund amount only for partial refunds
			if ( ! $is_void ) {
				// <amount>Order Amount</amount>
				$this->xml_writer->writeElement( 'amount', $amount );

				// <currencyCode>Order Currency Code</currencyCode>
				$this->xml_writer->writeElement( 'currencyCode', method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency() );

				// adds payment detail
				$this->add_payment_xml( $payment_details );
			}

			// </transactionRequest>
			$this->xml_writer->endElement();

			// </createTransactionRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Returns xml string to create a customer payment transaction on Authorize.net
		 *
		 * @param $user    \WP_User User to map in Authorize.net servers
		 * @param $payment \StdClass|bool Payment details (false if no payment to add)
		 *
		 * @return string XML request
		 * @since 1.0.9
		 */
		public function get_create_customer_profile_xml( $user, $payment = false ) {
			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <createCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'createCustomerProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			$this->add_auth_xml();

			// <profile>
			$this->xml_writer->startElement( 'profile' );

			$this->xml_writer->writeElement( 'merchantCustomerId', $user->ID );
			$this->xml_writer->writeElement( 'email', $user->user_email );

			if ( false !== $payment ) {

				// <paymentProfile>
				$this->xml_writer->startElement( 'paymentProfiles' );

				$this->add_payment_xml( $payment );

				// </paymentProfiles>
				$this->xml_writer->endElement();

			}

			// </profile>
			$this->xml_writer->endElement();

			$this->xml_writer->writeElement( 'validationMode', 'none' );

			// </createCustomerProfileRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Returns xml string to create a customer payment profile transaction on Authorize.net
		 *
		 * @param $order               \WC_Order Order to use as a base for billTo
		 * @param $customer_profile_id string Customer profile unique ID
		 * @param $payment             \StdClass Payment details
		 *
		 * @return string XML request
		 * @since 1.0.0
		 */
		public function get_create_customer_payment_profile_xml( $order, $customer_profile_id, $payment ) {
			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <createCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'createCustomerPaymentProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			$this->add_auth_xml();

			$this->xml_writer->writeElement( 'customerProfileId', $customer_profile_id );

			// <paymentProfile>
			$this->xml_writer->startElement( 'paymentProfile' );

			if ( ! is_null( $order ) ):

				// <billTo>
				$this->xml_writer->startElement( 'billTo' );

				$this->add_address_xml( $order, 'billing' );

				// </billTo>
				$this->xml_writer->endElement();

			endif;

			$this->add_payment_xml( $payment );

			// </paymentProfile>
			$this->xml_writer->endElement();

			$this->xml_writer->writeElement( 'validationMode', 'none' );

			// </createCustomerPaymentProfileRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Returns xml string to update a customer profile transaction on Authorize.net
		 *
		 * @param $order               \WC_Order Order to use as a base for billTo
		 * @param $customer_profile_id string Customer profile unique ID
		 *
		 * @return string XML request
		 * @since 1.0.0
		 */
		public function get_update_customer_profile_xml( $order, $customer_profile_id ) {
			$user_id    = get_current_user_id();
			$user       = wp_get_current_user();
			$user_email = ! empty( $user->billing_email ) ? $user->billing_email : $user->user_email;

			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <updateCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'updateCustomerProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			$this->add_auth_xml();

			$this->xml_writer->startElement( 'profile' );

			$this->xml_writer->writeElement( 'merchantCustomerId', $user_id );
			$this->xml_writer->writeElement( 'email', is_null( $order ) ? $user_email : yit_get_prop( $order, 'billing_email', true ) );
			$this->xml_writer->writeElement( 'customerProfileId', $customer_profile_id );

			$this->xml_writer->endElement();

			// </updateCustomerProfileRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Returns xml string to update a customer payment profile transaction on Authorize.net
		 *
		 * @param $order                       \WC_Order Order to use as a base for billTo
		 * @param $customer_profile_id         string Customer profile unique ID
		 * @param $customer_payment_profile_id string Customer payment profile unique ID
		 * @param $payment_details             \StdClass Payment details
		 * @param $is_default                  bool Wheter method should be default or not
		 *
		 * @return string XML request
		 * @since 1.0.0
		 */
		public function get_update_customer_payment_profile_xml( $order, $customer_profile_id, $payment_profile_id, $payment_details, $is_default = false ) {
			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <updateCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'updateCustomerPaymentProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			$this->add_auth_xml();

			$this->xml_writer->writeElement( 'customerProfileId', $customer_profile_id );

			$this->xml_writer->startElement( 'paymentProfile' );

			if ( ! is_null( $order ) ):

				$this->xml_writer->startElement( 'billTo' );

				$this->add_address_xml( $order );

				$this->xml_writer->endElement();

			endif;

			$this->add_payment_xml( $payment_details );

			$this->xml_writer->writeElement( 'customerPaymentProfileId', $payment_profile_id );

			$this->xml_writer->writeElement( 'defaultPaymentProfile', $is_default );

			$this->xml_writer->endElement();

			$this->xml_writer->writeElement( 'validationMode', 'none' );

			// </updateCustomerProfileRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Returns xml string to delete a customer profile transaction on Authorize.net
		 *
		 * @param $customer_profile_id         string Customer profile unique ID
		 * @param $customer_payment_profile_id string Customer payment profile unique ID
		 *
		 * @return string XML request
		 * @since 1.0.0
		 */
		public function get_delete_customer_payment_profile_xml( $customer_profile_id, $customer_payment_profile_id ) {
			// starts xml document
			$this->xml_writer->openMemory();
			$this->xml_writer->startDocument( '1.0', 'UTF-8' );

			// <deleteCustomerPaymentProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
			$this->xml_writer->startElementNs( null, 'deleteCustomerPaymentProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

			$this->add_auth_xml();

			$this->xml_writer->writeElement( 'customerProfileId', $customer_profile_id );

			$this->xml_writer->writeElement( 'customerPaymentProfileId', $customer_payment_profile_id );

			// </deleteCustomerPaymentProfileRequest>
			$this->xml_writer->endElement();

			// ends xml document and returns it
			$this->xml_writer->endDocument();

			return $this->xml_writer->outputMemory();
		}

		/**
		 * Add xml to authenticate merchant on Authorize.net servers
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_auth_xml() {

			// <merchantAuthentication>
			$this->xml_writer->startElement( 'merchantAuthentication' );

			// <name>{api_login_id}</name>
			$this->xml_writer->writeElement( 'name', $this->login_id );

			// <transactionKey>{api_transaction_key}</transactionKey>
			$this->xml_writer->writeElement( 'transactionKey', $this->transaction_key );

			// </merchantAuthentication>
			$this->xml_writer->endElement();
		}

		/**
		 * Add xml for an address (shipping or billig)
		 *
		 * @param $order  \WC_Order Order from which retrieve address information
		 * @param $type   string billing/shipping
		 * @param $fields array An array of field to print
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_address_xml( $order, $type = 'billing', $fields = array() ) {
			if ( empty( $fields ) ) {
				$fields = array(
					'first_name',
					'last_name',
					'company',
					'address',
					'city',
					'state',
					'zip',
					'country',
					'phone'
				);
			}

			foreach ( $fields as $field ) {

				$field_name = $type . '_' . $field;
				$xml_name   = lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $field ) ) ) );

				if ( 'shipping' == $type && 'phone' == $field ) {
					continue;
				}

				if ( 'address' == $field ) {
					$this->xml_writer->writeElement( 'address', yit_get_prop( $order, $type . '_address_1', true ) . ' ' . yit_get_prop( $order, $type . '_address_2', true ) );
					continue;
				}

				if ( 'zip' == $field ) {
					$this->xml_writer->writeElement( 'zip', yit_get_prop( $order, $type . '_postcode', true ) );
					continue;
				}

				if ( 'phone' == $field ) {
					$this->xml_writer->writeElement( 'phoneNumber', yit_get_prop( $order, 'billing_phone', true ) );
					continue;
				}

				if ( 'country' == $field ) {
					$this->xml_writer->writeElement( 'country', $this->convert_country_code( yit_get_prop( $order, $type . '_country', true ) ) );
					continue;
				}

				if ( $field_value = yit_get_prop( $order, $field_name, true ) ) {
					$this->xml_writer->writeElement( $xml_name, $field_value );
				}
			}
		}

		/**
		 * Add xml for payment information
		 *
		 * @param $payment \StdClass Payment details
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_payment_xml( $payment ) {
			if ( $payment->type != 'profile' ) {
				// <payment>
				$this->xml_writer->startElement( 'payment' );

				if ( 'echeck' === $payment->type ) {

					// <bankAccount>
					$this->xml_writer->startElement( 'bankAccount' );

					if ( ! empty( $payment->routing_number ) ) {
						$this->xml_writer->writeElement( 'routingNumber', $payment->routing_number );
					}

					if ( ! empty( $payment->account_number ) ) {
						$this->xml_writer->writeElement( 'accountNumber', $payment->account_number );
					}

					if ( ! empty( $payment->name_on_account ) ) {
						$this->xml_writer->writeElement( 'nameOnAccount', $payment->name_on_account );
					}


					// $this->xml_writer->writeElement( 'echeckType', 'WEB' );

					// </bankAccount>
					$this->xml_writer->endElement();

				} elseif ( 'credit_card' === $payment->type ) {

					// <creditCard>
					$this->xml_writer->startElement( 'creditCard' );

					if ( ! empty( $payment->card_number ) ) {
						$this->xml_writer->writeElement( 'cardNumber', $payment->card_number );
					}

					if ( ! empty( $payment->expiration_date ) ) {
						$this->xml_writer->writeElement( 'expirationDate', $payment->expiration_date );
					}

					if ( ! empty( $payment->cvv ) ) {
						$this->xml_writer->writeElement( 'cardCode', $payment->cvv );
					}

					// </creditCard>
					$this->xml_writer->endElement();
				}

				// </payment>
				$this->xml_writer->endElement();
			}
		}

		/**
		 * Add xml for profile information
		 *
		 * @param $payment \StdClass Payment details
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_profile_xml( $payment ) {
			if ( $this->cim_handling && is_user_logged_in() ) {
				// <profile>
				$this->xml_writer->startElement( 'profile' );

				if ( $payment->type != 'profile' && empty( $payment->customer_profile_id ) ) {
					$this->xml_writer->writeElement( 'createProfile', true );
				}

				if ( ! empty( $payment->customer_profile_id ) ) {
					$this->xml_writer->writeElement( 'customerProfileId', $payment->customer_profile_id );
				}

				if ( 'profile' === $payment->type ) {
					$this->xml_writer->startElement( 'paymentProfile' );

					$this->xml_writer->writeElement( 'paymentProfileId', $payment->payment_profile_id );

					$this->xml_writer->endElement();
				}

				// </profile>
				$this->xml_writer->endElement();
			}
		}

		/**
		 * Add xml for items information
		 *
		 * @param $order \WC_Order Order to use to retrieve items
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_line_items_xml( $order ) {
			$line_items = $order->get_items( 'line_item' );
			$fees_item  = $order->get_fees();

			$order_items = array_merge( $line_items, $fees_item );
			$counter     = 0;

			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $item_id => $item ) {
					if ( $counter >= 30 ) {
						break;
					}

					// <lineItem>
					$this->xml_writer->startElement( 'lineItem' );

					// <itemId>Item id</itemId>
					$this->xml_writer->writeElement( 'itemId', apply_filters( 'yith_wcauthnet_add_line_item_id', $item_id, $item, $order ) );
					// <name>Item name</name>
					$this->xml_writer->writeElement( 'name', htmlentities( mb_substr( $item['name'], 0, 20 ), ENT_QUOTES, 'UTF-8', false ) );
					// <quantity>Item quantity</quantity>
					$this->xml_writer->writeElement( 'quantity', isset( $item['qty'] ) ? $item['qty'] : 1 );
					// <unitPrice>Item unit price</unitPrice>
					$this->xml_writer->writeElement( 'unitPrice', $order->get_item_total( $item ) );
					// </lineItem>
					$this->xml_writer->endElement();

					$counter ++;
				}
			}
		}

		/**
		 * Add xml for tax information
		 *
		 * @param $order \WC_Order Order to use to retrieve taxes
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_tax_xml( $order ) {

			// <tax>
			$this->xml_writer->startElement( 'tax' );

			// <amount>
			$this->xml_writer->writeElement( 'amount', round( $order->get_total_tax(), wc_get_price_decimals() ) );

			// <name>
			$this->xml_writer->writeElement( 'name', __( 'Taxes', 'yith-woocommerce-authorizenet-payment-gateway' ) );

			$taxes = array();

			foreach ( $order->get_tax_totals() as $tax_code => $tax ) {

				$taxes[] = sprintf( '%s (%s) - %s', $tax->label, $tax_code, $tax->amount );
			}

			// </tax>
			$this->xml_writer->endElement();
		}

		/**
		 * Add xml for shipping costs information
		 *
		 * @param $order \WC_Order Order to use to retrieve shipping costs
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_shipping_xml( $order ) {

			// <shipping>
			$this->xml_writer->startElement( 'shipping' );

			// <amount>
			$this->xml_writer->writeElement( 'amount', method_exists( $order, 'get_shipping_total' ) ? $order->get_shipping_total() : $order->get_total_shipping() );

			// <name>
			$this->xml_writer->writeElement( 'name', __( 'Shipping', 'yith-woocommerce-authorizenet-payment-gateway' ) );

			// </shipping>
			$this->xml_writer->endElement();
		}

		/**
		 * Add xml for user information
		 *
		 * @param $order \WC_Order Order to use to retrieve user informations
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function add_user_info( $order ) {
			$user_info = apply_filters( 'yith_wcauthnet_user_info', array(
				'transaction_email'  => yit_get_prop( $order, 'billing_email', true ),
				'transaction_amount' => $order->get_total()
			), $order );

			if ( ! empty( $user_info ) ) {
				$this->xml_writer->startElement( 'userFields' );
				foreach ( $user_info as $id => $value ) {
					$this->xml_writer->startElement( 'userField' );

					$this->xml_writer->writeElement( 'name', $id );
					$this->xml_writer->writeElement( 'value', $value );

					$this->xml_writer->endElement();
				}
				$this->xml_writer->endElement();
			}
		}

		/**
		 * Convert country code from ISO 3166-1 alpha-2 to ISO_3166-1 alpha-3
		 *
		 * @param $country string Original ISO 3166-1 alpha-2 code
		 *
		 * @return string Translated ISO 3166-1 alpha-3 code
		 */
		protected function convert_country_code( $country ) {
			$countries = array(
				'AF' => 'AFG', //Afghanistan
				'AX' => 'ALA', //&#197;land Islands
				'AL' => 'ALB', //Albania
				'DZ' => 'DZA', //Algeria
				'AS' => 'ASM', //American Samoa
				'AD' => 'AND', //Andorra
				'AO' => 'AGO', //Angola
				'AI' => 'AIA', //Anguilla
				'AQ' => 'ATA', //Antarctica
				'AG' => 'ATG', //Antigua and Barbuda
				'AR' => 'ARG', //Argentina
				'AM' => 'ARM', //Armenia
				'AW' => 'ABW', //Aruba
				'AU' => 'AUS', //Australia
				'AT' => 'AUT', //Austria
				'AZ' => 'AZE', //Azerbaijan
				'BS' => 'BHS', //Bahamas
				'BH' => 'BHR', //Bahrain
				'BD' => 'BGD', //Bangladesh
				'BB' => 'BRB', //Barbados
				'BY' => 'BLR', //Belarus
				'BE' => 'BEL', //Belgium
				'BZ' => 'BLZ', //Belize
				'BJ' => 'BEN', //Benin
				'BM' => 'BMU', //Bermuda
				'BT' => 'BTN', //Bhutan
				'BO' => 'BOL', //Bolivia
				'BQ' => 'BES', //Bonaire, Saint Estatius and Saba
				'BA' => 'BIH', //Bosnia and Herzegovina
				'BW' => 'BWA', //Botswana
				'BV' => 'BVT', //Bouvet Islands
				'BR' => 'BRA', //Brazil
				'IO' => 'IOT', //British Indian Ocean Territory
				'BN' => 'BRN', //Brunei
				'BG' => 'BGR', //Bulgaria
				'BF' => 'BFA', //Burkina Faso
				'BI' => 'BDI', //Burundi
				'KH' => 'KHM', //Cambodia
				'CM' => 'CMR', //Cameroon
				'CA' => 'CAN', //Canada
				'CV' => 'CPV', //Cape Verde
				'KY' => 'CYM', //Cayman Islands
				'CF' => 'CAF', //Central African Republic
				'TD' => 'TCD', //Chad
				'CL' => 'CHL', //Chile
				'CN' => 'CHN', //China
				'CX' => 'CXR', //Christmas Island
				'CC' => 'CCK', //Cocos (Keeling) Islands
				'CO' => 'COL', //Colombia
				'KM' => 'COM', //Comoros
				'CG' => 'COG', //Congo
				'CD' => 'COD', //Congo, Democratic Republic of the
				'CK' => 'COK', //Cook Islands
				'CR' => 'CRI', //Costa Rica
				'CI' => 'CIV', //Côte d\'Ivoire
				'HR' => 'HRV', //Croatia
				'CU' => 'CUB', //Cuba
				'CW' => 'CUW', //Curaçao
				'CY' => 'CYP', //Cyprus
				'CZ' => 'CZE', //Czech Republic
				'DK' => 'DNK', //Denmark
				'DJ' => 'DJI', //Djibouti
				'DM' => 'DMA', //Dominica
				'DO' => 'DOM', //Dominican Republic
				'EC' => 'ECU', //Ecuador
				'EG' => 'EGY', //Egypt
				'SV' => 'SLV', //El Salvador
				'GQ' => 'GNQ', //Equatorial Guinea
				'ER' => 'ERI', //Eritrea
				'EE' => 'EST', //Estonia
				'ET' => 'ETH', //Ethiopia
				'FK' => 'FLK', //Falkland Islands
				'FO' => 'FRO', //Faroe Islands
				'FJ' => 'FIJ', //Fiji
				'FI' => 'FIN', //Finland
				'FR' => 'FRA', //France
				'GF' => 'GUF', //French Guiana
				'PF' => 'PYF', //French Polynesia
				'TF' => 'ATF', //French Southern Territories
				'GA' => 'GAB', //Gabon
				'GM' => 'GMB', //Gambia
				'GE' => 'GEO', //Georgia
				'DE' => 'DEU', //Germany
				'GH' => 'GHA', //Ghana
				'GI' => 'GIB', //Gibraltar
				'GR' => 'GRC', //Greece
				'GL' => 'GRL', //Greenland
				'GD' => 'GRD', //Grenada
				'GP' => 'GLP', //Guadeloupe
				'GU' => 'GUM', //Guam
				'GT' => 'GTM', //Guatemala
				'GG' => 'GGY', //Guernsey
				'GN' => 'GIN', //Guinea
				'GW' => 'GNB', //Guinea-Bissau
				'GY' => 'GUY', //Guyana
				'HT' => 'HTI', //Haiti
				'HM' => 'HMD', //Heard Island and McDonald Islands
				'VA' => 'VAT', //Holy See (Vatican City State)
				'HN' => 'HND', //Honduras
				'HK' => 'HKG', //Hong Kong
				'HU' => 'HUN', //Hungary
				'IS' => 'ISL', //Iceland
				'IN' => 'IND', //India
				'ID' => 'IDN', //Indonesia
				'IR' => 'IRN', //Iran
				'IQ' => 'IRQ', //Iraq
				'IE' => 'IRL', //Republic of Ireland
				'IM' => 'IMN', //Isle of Man
				'IL' => 'ISR', //Israel
				'IT' => 'ITA', //Italy
				'JM' => 'JAM', //Jamaica
				'JP' => 'JPN', //Japan
				'JE' => 'JEY', //Jersey
				'JO' => 'JOR', //Jordan
				'KZ' => 'KAZ', //Kazakhstan
				'KE' => 'KEN', //Kenya
				'KI' => 'KIR', //Kiribati
				'KP' => 'PRK', //Korea, Democratic People\'s Republic of
				'KR' => 'KOR', //Korea, Republic of (South)
				'KW' => 'KWT', //Kuwait
				'KG' => 'KGZ', //Kyrgyzstan
				'LA' => 'LAO', //Laos
				'LV' => 'LVA', //Latvia
				'LB' => 'LBN', //Lebanon
				'LS' => 'LSO', //Lesotho
				'LR' => 'LBR', //Liberia
				'LY' => 'LBY', //Libya
				'LI' => 'LIE', //Liechtenstein
				'LT' => 'LTU', //Lithuania
				'LU' => 'LUX', //Luxembourg
				'MO' => 'MAC', //Macao S.A.R., China
				'MK' => 'MKD', //Macedonia
				'MG' => 'MDG', //Madagascar
				'MW' => 'MWI', //Malawi
				'MY' => 'MYS', //Malaysia
				'MV' => 'MDV', //Maldives
				'ML' => 'MLI', //Mali
				'MT' => 'MLT', //Malta
				'MH' => 'MHL', //Marshall Islands
				'MQ' => 'MTQ', //Martinique
				'MR' => 'MRT', //Mauritania
				'MU' => 'MUS', //Mauritius
				'YT' => 'MYT', //Mayotte
				'MX' => 'MEX', //Mexico
				'FM' => 'FSM', //Micronesia
				'MD' => 'MDA', //Moldova
				'MC' => 'MCO', //Monaco
				'MN' => 'MNG', //Mongolia
				'ME' => 'MNE', //Montenegro
				'MS' => 'MSR', //Montserrat
				'MA' => 'MAR', //Morocco
				'MZ' => 'MOZ', //Mozambique
				'MM' => 'MMR', //Myanmar
				'NA' => 'NAM', //Namibia
				'NR' => 'NRU', //Nauru
				'NP' => 'NPL', //Nepal
				'NL' => 'NLD', //Netherlands
				'AN' => 'ANT', //Netherlands Antilles
				'NC' => 'NCL', //New Caledonia
				'NZ' => 'NZL', //New Zealand
				'NI' => 'NIC', //Nicaragua
				'NE' => 'NER', //Niger
				'NG' => 'NGA', //Nigeria
				'NU' => 'NIU', //Niue
				'NF' => 'NFK', //Norfolk Island
				'MP' => 'MNP', //Northern Mariana Islands
				'NO' => 'NOR', //Norway
				'OM' => 'OMN', //Oman
				'PK' => 'PAK', //Pakistan
				'PW' => 'PLW', //Palau
				'PS' => 'PSE', //Palestinian Territory
				'PA' => 'PAN', //Panama
				'PG' => 'PNG', //Papua New Guinea
				'PY' => 'PRY', //Paraguay
				'PE' => 'PER', //Peru
				'PH' => 'PHL', //Philippines
				'PN' => 'PCN', //Pitcairn
				'PL' => 'POL', //Poland
				'PT' => 'PRT', //Portugal
				'PR' => 'PRI', //Puerto Rico
				'QA' => 'QAT', //Qatar
				'RE' => 'REU', //Reunion
				'RO' => 'ROU', //Romania
				'RU' => 'RUS', //Russia
				'RW' => 'RWA', //Rwanda
				'BL' => 'BLM', //Saint Barth&eacute;lemy
				'SH' => 'SHN', //Saint Helena
				'KN' => 'KNA', //Saint Kitts and Nevis
				'LC' => 'LCA', //Saint Lucia
				'MF' => 'MAF', //Saint Martin (French part)
				'SX' => 'SXM', //Sint Maarten / Saint Matin (Dutch part)
				'PM' => 'SPM', //Saint Pierre and Miquelon
				'VC' => 'VCT', //Saint Vincent and the Grenadines
				'WS' => 'WSM', //Samoa
				'SM' => 'SMR', //San Marino
				'ST' => 'STP', //S&atilde;o Tom&eacute; and Pr&iacute;ncipe
				'SA' => 'SAU', //Saudi Arabia
				'SN' => 'SEN', //Senegal
				'RS' => 'SRB', //Serbia
				'SC' => 'SYC', //Seychelles
				'SL' => 'SLE', //Sierra Leone
				'SG' => 'SGP', //Singapore
				'SK' => 'SVK', //Slovakia
				'SI' => 'SVN', //Slovenia
				'SB' => 'SLB', //Solomon Islands
				'SO' => 'SOM', //Somalia
				'ZA' => 'ZAF', //South Africa
				'GS' => 'SGS', //South Georgia/Sandwich Islands
				'SS' => 'SSD', //South Sudan
				'ES' => 'ESP', //Spain
				'LK' => 'LKA', //Sri Lanka
				'SD' => 'SDN', //Sudan
				'SR' => 'SUR', //Suriname
				'SJ' => 'SJM', //Svalbard and Jan Mayen
				'SZ' => 'SWZ', //Swaziland
				'SE' => 'SWE', //Sweden
				'CH' => 'CHE', //Switzerland
				'SY' => 'SYR', //Syria
				'TW' => 'TWN', //Taiwan
				'TJ' => 'TJK', //Tajikistan
				'TZ' => 'TZA', //Tanzania
				'TH' => 'THA', //Thailand
				'TL' => 'TLS', //Timor-Leste
				'TG' => 'TGO', //Togo
				'TK' => 'TKL', //Tokelau
				'TO' => 'TON', //Tonga
				'TT' => 'TTO', //Trinidad and Tobago
				'TN' => 'TUN', //Tunisia
				'TR' => 'TUR', //Turkey
				'TM' => 'TKM', //Turkmenistan
				'TC' => 'TCA', //Turks and Caicos Islands
				'TV' => 'TUV', //Tuvalu
				'UG' => 'UGA', //Uganda
				'UA' => 'UKR', //Ukraine
				'AE' => 'ARE', //United Arab Emirates
				'GB' => 'GBR', //United Kingdom
				'US' => 'USA', //United States
				'UM' => 'UMI', //United States Minor Outlying Islands
				'UY' => 'URY', //Uruguay
				'UZ' => 'UZB', //Uzbekistan
				'VU' => 'VUT', //Vanuatu
				'VE' => 'VEN', //Venezuela
				'VN' => 'VNM', //Vietnam
				'VG' => 'VGB', //Virgin Islands, British
				'VI' => 'VIR', //Virgin Island, U.S.
				'WF' => 'WLF', //Wallis and Futuna
				'EH' => 'ESH', //Western Sahara
				'YE' => 'YEM', //Yemen
				'ZM' => 'ZMB', //Zambia
				'ZW' => 'ZWE', //Zimbabwe
			);
			$iso_code  = isset( $countries[ $country ] ) ? $countries[ $country ] : $country;

			return $iso_code;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAUTHNET_CIM_API class
 *
 * @return \YITH_WCAUTHNET_CIM_API
 * @since 1.0.0
 */
function YITH_WCAUTHNET_CIM_API() {
	return YITH_WCAUTHNET_CIM_API::get_instance();
}
