<?php
/**
 * WooCommerce Authorize.Net Reporting
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Reporting to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Reporting for your
 * needs please refer to http://www.skyverge.com/contact/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Authorize.Net Reporting API Request Class.
 *
 * Parses XML received by Reporting API.
 *
 * Note: all the casting to String is ENTIRELY necessary, otherwise you get
 * crazy SimpleXMLElement errors when you try and make use of the values.
 *
 * @link http://www.authorize.net/support/ReportingGuide_XML.pdf
 * @see \SimpleXMLElement
 *
 * @since 1.0
 */
class WC_Authorize_Net_Reporting_API_Response implements Framework\SV_WC_API_Response {


	/** @var string string representation of this response */
	private $raw_response_xml;

	/** @var \SimpleXMLElement response XML object */
	protected $response_xml;


	/**
	 * Builds a response object from the raw response XML.
	 *
	 * @since 1.1.1
	 *
	 * @param string $raw_response_xml the raw response XML
	 */
	public function __construct( $raw_response_xml ) {

		$this->raw_response_xml = $raw_response_xml;

		// Remove namespace as SimpleXML throws warnings with invalid namespace URI provided by Authorize.Net
		$raw_response_xml = preg_replace( '/[[:space:]]xmlns[^=]*="[^"]*"/i', '', $raw_response_xml );

		// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
		$this->response_xml = new \SimpleXMLElement( $raw_response_xml, LIBXML_NOCDATA );
	}


	/**
	 * Set the transaction fields to get from Authorize.Net and output in the CSV.
	 *
	 * @since 1.6.1
	 *
	 * @return string[] transaction fields in the API response
	 */
	public static function get_transaction_fields() {

		$transaction_fields = array( 'transId', 'refTransId', 'splitTenderId', 'submitTimeUTC', 'submitTimeLocal', 'transactionType',
			'transactionStatus', 'responseCode', 'responseReasonCode', 'responseReasonDescription', 'authCode', 'AVSResponse',
			'cardCodeResponse', 'CAVVResponse', 'FDSFilterAction', 'FDSFilters', 'batch_batchId', 'batch_settlementTimeUTC',
			'batch_settlementTimeLocal', 'batch_settlementState', 'order_invoiceNumber', 'order_description', 'order_purchaseOrderNumber',
			'requestedAmount', 'authAmount', 'settleAmount', 'tax_amount', 'tax_name', 'tax_description', 'shipping_amount',
			'shipping_name', 'shipping_description', 'duty_amount', 'lineItems', 'prepaidBalanceRemaining', 'taxExempt',
			'payment_creditCard_cardNumber', 'payment_creditCard_expirationDate', 'payment_creditCard_cardType',
			'payment_bankAccount_routingNumber', 'payment_bankAccount_accountNumber', 'payment_bankAccount_nameOnAccount',
			'payment_bankAccount_echeckType', 'customer_type', 'customer_id', 'customer_email', 'billTo_firstName',
			'billTo_lastName', 'billTo_company', 'billTo_address', 'billTo_city', 'billTo_state', 'billTo_zip', 'billTo_country',
			'billTo_phoneNumber', 'billTo_faxNumber', 'shipTo_firstName', 'shipTo_lastName', 'shipTo_company', 'shipTo_address',
			'shipTo_city', 'shipTo_state', 'shipTo_zip', 'shipTo_country', 'recurringBilling', 'customerIP'
		);

		/**
		 * Filters the fields in the response to output to the CSV.
		 *
		 * @since 1.6.1
		 *
		 * @param string[] $fields the transaction fields
		 */
		return apply_filters( 'wc_authorize_net_reporting_transaction_fields', $transaction_fields );
	}


	/**
	 * Checks if response contains an API error code.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function has_api_error() {

		if ( ! isset( $this->response_xml->messages->resultCode ) ) {
			return true;
		}

		return 'error' == strtolower( (string) $this->response_xml->messages->resultCode );
	}


	/**
	 * Gets the API error code.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_api_error_code() {

		if ( ! isset( $this->response_xml->messages->message->code ) ) {
			return __( 'N/A', 'woocommerce-authorize-net-reporting' );
		}

		return (string) $this->response_xml->messages->message->code;
	}


	/**
	 * Gets the API error message.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_api_error_message() {

		if ( ! isset( $this->response_xml->messages->message->text ) ) {
			return __( 'N/A', 'woocommerce-authorize-net-reporting' );
		}

		return (string) $this->response_xml->messages->message->text;
	}


	/**
	 * Gets the list of batches as an associative array.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_batches() {

		$batches = array();

		if ( isset( $this->response_xml->batchList->batch ) ) {

			foreach ( $this->response_xml->batchList->batch as $batch ) {

				$batches[] = $this->convert_to_array( $batch );
			}
		}

		return $batches;
	}


	/**
	 * Gets the transactions as an associate array.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_transactions() {

		$transactions = array();

		foreach ( $this->response_xml->transactions->transaction as $transaction ) {

			$transactions[] = $this->convert_to_array( $transaction );
		}

		return $transactions;
	}


	/**
	 * Gets the transaction details an associative array.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function get_transaction_details() {

		$details = array();

		// merge the multidimensional array into a simple associative array
		foreach ( $this->convert_to_array( $this->response_xml->transaction ) as $key => $value ) {

			if ( is_array( $value ) ) {

				// handle data with multiple elements individually
				switch ( $key ) {

					// there can be multiple FDSFilter elements, each with a name and action element
					case 'FDSFilters':
						$filters = array();

						// handle the case when only one FDSFilter is present
						$value['FDSFilter'] = is_numeric( key( $value['FDSFilter'] ) ) ? $value['FDSFilter'] : array( $value['FDSFilter'] );

						foreach ( $value['FDSFilter'] as $filter ) {
							$filters[] = sprintf( 'name:%s+action:%s', $filter['name'], $filter['action'] );
						}

						$details['FDSFilters'] = implode( '|', $filters );

					break;

					// there can be multiple lineItem elements, each with itemId, name, description, quantity, unitPrice, and taxable elements
					case 'lineItems':

						$items = array();

						// handle the case when only one line item is present
						$value['lineItem'] = is_numeric( key( $value['lineItem'] ) ) ? $value['lineItem'] : array( $value['lineItem'] );

						foreach ( $value['lineItem'] as $line_item ) {
							$items[] = sprintf(
								'itemId:%s+name:%s+description:%s+quantity:%s+unitPrice:%s+taxable:%s',
								isset( $line_item['itemId'] ) ? $line_item['itemId'] : '',
								isset( $line_item['name'] ) ? $line_item['name'] : '',
								! empty( $line_item['description'] ) ? $line_item['description'] : '',
								isset( $line_item['quantity'] ) ? $line_item['quantity'] : '',
								isset( $line_item['unitPrice'] ) ? $line_item['unitPrice'] : '',
								isset( $line_item['taxable'] ) ? $line_item['taxable'] : ''
							);
						}

						$details['lineItems'] = implode( '|', $items );

					break;

					case 'payment':

						if ( isset( $value['creditCard'] ) ) {

							foreach ( $value['creditCard'] as $credit_card_detail => $credit_card_value ) {
								$details[ 'payment_creditCard_' . $credit_card_detail ] = $credit_card_value;
							}

						} elseif ( isset( $value['bankAccount'] ) ) {

							foreach ( $value['bankAccount'] as $bank_account_detail => $bank_account_value ) {
								$details[ 'payment_bankAccount_' . $bank_account_detail ] = $bank_account_value;
							}
						}

					break;

					default:

						foreach ( $value as $child_key => $child_value ) {
							$details[ $key . '_' . $child_key ] = $child_value;
						}

					break;

				}

			} else {

				$details[ $key ] = $value;
			}
		}

		$transaction_details = array();

		// form the final array using an ordered array of fields so each transaction details array has a consistent set of data
		foreach ( self::get_transaction_fields() as $field_name ) {

			if ( ! empty( $details[ $field_name ] ) ) {
				$transaction_details[ $field_name ] = $details[ $field_name ];
			} else {
				$transaction_details[ $field_name ] = null;
			}
		}

		/**
		 * Filters the transaction details in each row to allow actors to re-format them or adjust information.
		 *
		 * @since 1.6.1
		 *
		 * @param string[] $transaction_details the details for the row's transaction
		 * @param \WC_Authorize_Net_Reporting_API_Response $response the response object
		 */
		return apply_filters( 'wc_authorize_net_reporting_transaction_details', $transaction_details, $this );
	}


	/**
	 * Converts the SimpleXML object into an array (include child elements).
	 *
	 * @since 1.0
	 *
	 * @param object $object
	 * @return array
	 */
	private function convert_to_array( $object ) {

		return json_decode( json_encode( $object ), true );
	}


	/**
	 * Returns the string representation of this response.
	 *
	 * @see SV_WC_API_Response::to_string()
	 *
	 * @since 1.1.1
	 *
	 * @return string response
	 */
	public function to_string() {

		$string = $this->raw_response_xml;

		$dom = new \DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $string ) ) {
			$dom->formatOutput = true;
			$string = $dom->saveXML();
		}

		return $string;
	}


	/**
	 * Returns the string representation of this response with any and all sensitive elements masked or removed.
	 *
	 * @see SV_WC_API_Response::to_string_safe()
	 *
	 * @since 1.1.1
	 *
	 * @return string response safe for logging/displaying
	 */
	public function to_string_safe() {

		// no sensitive data to mask
		return $this->to_string();
	}


}
