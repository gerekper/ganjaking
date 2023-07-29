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
 * @copyright   Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_5 as Framework;

/**
 * Authorize.Net Reporting API Request Class.
 *
 * Generates XML required by Reporting API specs.
 *
 * @link http://www.authorize.net/support/ReportingGuide_XML.pdf
 *
 * @since 1.0
 */
class WC_Authorize_Net_Reporting_API_Request extends \XMLWriter implements Framework\SV_WC_API_Request {


	/** @var string API login ID */
	private $api_login_id;

	/** @var string API transaction key */
	private $api_transaction_key;

	/** @var string generated request XML */
	private $request_xml;


	/**
	 * Opens XML document in memory, set version/encoding, and auth information.
	 *
	 * @since 1.0
	 *
	 * @param string $api_login_id required
	 * @param string $api_transaction_key required
	 */
	public function __construct( $api_login_id, $api_transaction_key ) {

		// Create XML document in memory
		$this->openMemory();

		// Set XML version & encoding
		$this->startDocument( '1.0', 'UTF-8' );

		$this->api_login_id        = $api_login_id;
		$this->api_transaction_key = $api_transaction_key;
	}


	/**
	 * Creates XML for getting a list of batches settled in the provided date ranges.
	 *
	 * @since 1.0
	 *
	 * @param string $start_date
	 * @param string $end_date
	 */
	public function get_settled_batch_list( $start_date, $end_date ) {

		// root element is unique to each request
		$this->startElementNs( null, 'getSettledBatchListRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// no reason to include statistics yet
		// $this->writeElement( 'includeStatistics', true );

		if ( $start_date ) {
			$this->writeElement( 'firstSettlementDate', $start_date );
		}

		if ( $end_date ) {
			$this->writeElement( 'lastSettlementDate', $end_date );
		}

		// </getSettledBatchListRequest>
		$this->endElement();
	}


	/**
	 * Creates XML for getting a list of transactions for the provided batch ID.
	 *
	 * @since 1.0
	 *
	 * @param string $batch_id
	 */
	public function get_transaction_list( $batch_id ) {

		// root element is unique to each request
		$this->startElementNs( null, 'getTransactionListRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// <batchId>
		$this->writeElement( 'batchId', $batch_id );

		// </getTransactionListRequest>
		$this->endElement();
	}


	/**
	 * Creates XML for getting details for a transaction for the provided transaction ID.
	 *
	 * @since 1.0
	 *
	 * @param string $transaction_id
	 */
	public function get_transaction_details( $transaction_id ) {

		// root element is unique to each request
		$this->startElementNs( null, 'getTransactionDetailsRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// <transId>
		$this->writeElement( 'transId', $transaction_id );

		// </getTransactionDetailsRequest>
		$this->endElement();
	}


	/**
	 * Generates authorization XML that is included with every request.
	 *
	 * @since 1.0
	 */
	private function add_auth_xml() {

		// <merchantAuthentication>
		$this->startElement( 'merchantAuthentication' );

		// <name>{api_login_id}</name>
		$this->writeElement( 'name', $this->api_login_id );

		// <transactionKey>{api_transaction_key}</transactionKey>
		$this->writeElement( 'transactionKey', $this->api_transaction_key );

		// </merchantAuthentication>
		$this->endElement();
	}


	/**
	 * Returns the completed XML document.
	 *
	 * @since 1.1.1
	 *
	 * @return string XML
	 */
	public function to_xml() {

		if ( ! empty( $this->request_xml ) ) {

			return $this->request_xml;
		}

		$this->endDocument();

		return $this->request_xml = $this->outputMemory();
	}


	/**
	 * Returns the string representation of this request.
	 *
	 * @see SV_WC_API_Request::to_string()
	 *
	 * @since 1.1.1
	 *
	 * @return string request XML
	 */
	public function to_string() {

		return $this->to_xml();
	}


	/**
	 * Returns the string representation of this request with any and all sensitive elements masked or removed.
	 *
	 * @see SV_WC_API_Request::to_string_safe()
	 *
	 * @since 1.1.1
	 *
	 * @return string the request XML, safe for logging/displaying
	 */
	public function to_string_safe() {

		$request = $this->to_xml();

		$dom = new \DOMDocument();

		// suppress errors for invalid XML syntax issues
		if ( @$dom->loadXML( $request ) ) {
			$dom->formatOutput = true;
			$request = $dom->saveXML();
		}

		// replace API login ID
		if ( preg_match( '/<merchantAuthentication>(\n\s*)<name>(\w+)<\/name>/', $request, $matches ) ) {
			$request = preg_replace( '/<merchantAuthentication>[\n\s]*<name>\w+<\/name>/', "<merchantAuthentication>{$matches[1]}<name>" . str_repeat( '*', strlen( $matches[2] ) ) . '</name>', $request );
		}

		// replace API transaction key
		if ( preg_match( '/<transactionKey>(\w+)<\/transactionKey>/', $request, $matches ) ) {
			$request = preg_replace( '/<transactionKey>\w+<\/transactionKey>/', '<transactionKey>' . str_repeat( '*', strlen( $matches[1] ) ) . '</transactionKey>', $request );
		}

		return $request;
	}


	/**
	 * Returns the method for this request.
	 *
	 * Authorize.Net uses the API default (POST).
	 *
	 * @since 1.3.0
	 *
	 * @return null (uses POST)
	 */
	public function get_method() {

		return null;
	}


	/**
	 * Returns the request path for this request.
	 *
	 * Authorize.Net request paths do not vary per request.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_path() {

		return '';
	}


	/**
	 * Gets the request data (implements interface method).
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function get_data() {

		return array();
	}


	/**
	 * Gets the request params (implements interface method).
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function get_params() {

		return array();
	}


}
