<?php
/**
 * WooCommerce FreshBooks
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce FreshBooks to newer
 * versions in the future. If you wish to customize WooCommerce FreshBooks for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-freshbooks/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * FreshBooks API Response Class
 *
 * Parses XML received from the FreshBooks API, the general response XML looks like
 * this for a successful request:
 *
 * <?xml version="1.0" encoding="utf-8"?>
 * <response xmlns="http://www.freshbooks.com/api/" status="ok">
 * </response>
 *
 * For an unsuccessful request, there will be one or more error messages:
 *
 * <?xml version="1.0" encoding="utf-8"?>
 * <response xmlns="http://www.freshbooks.com/api/" status="fail">
 *   <error>Error Message</error>
 *   <code>40010</code>
 * </response>
 *
 * Note: the (string) casts here are critical, without these you'll tend to get untraceable
 * errors like "Serialization of 'SimpleXMLElement' is not allowed"
 *
 * @link http://developers.freshbooks.com/
 *
 * @since 3.0
 */
class WC_FreshBooks_API_Response implements Framework\SV_WC_API_Response {


	/** @var string string representation of this response */
	private $raw_response_xml;

	/** @var \SimpleXMLElement response XML object */
	private $response_xml;


	/**
	 * Builds a response object from the raw response XML.
	 *
	 * @since 3.0
	 *
	 * @param string $raw_response_xml the raw response XML
	 * @throws Framework\SV_WC_API_Exception if invalid XML is received
	 */
	public function __construct( $raw_response_xml ) {

		$this->raw_response_xml = $raw_response_xml;

		try {

			// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
			$this->response_xml = new \SimpleXMLElement( $raw_response_xml, LIBXML_NOCDATA );

		} catch ( \Exception $e ) {

			throw new Framework\SV_WC_API_Exception( $e->getMessage(), $e->getCode(), $e );
		}
	}


	/**
	 * Checks if response contains an API error
	 *
	 * @since 3.0
	 * @return bool true if has API error, false otherwise
	 */
	public function has_api_error() {

		if ( ! isset( $this->response_xml->attributes()->status ) ) {
			return true;
		}

		return 'fail' === strtolower( (string) $this->response_xml->attributes()->status );
	}


	/**
	 * Gets the API error code
	 *
	 * @since 3.0
	 * @return string API error code
	 */
	public function get_api_error_code() {

		return isset( $this->response_xml->code ) ? (string) $this->response_xml->code : -1;
	}


	/**
	 * Returns the API error message
	 *
	 * @since 3.0
	 * @return string api error message
	 */
	public function get_api_error_message() {

		if ( ! isset( $this->response_xml->error ) ) {
			return __( 'N/A', 'woocommerce-freshbooks' );
		}

		return (string) $this->response_xml->error;
	}


	/** Invoice methods ******************************************************/


	/**
	 * Gets the created invoice ID from a create invoice API call.
	 *
	 * @since 3.0
	 *
	 * @return string created invoice ID
	 * @throws Framework\SV_WC_API_Exception upon missing invoice ID
	 */
	public function parse_create_invoice() {

		if ( ! isset( $this->response_xml->invoice_id ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'Created invoice ID is missing', 'woocommerce-freshbooks' ) );
		}

		return (string) $this->response_xml->invoice_id;
	}


	/**
	 * Returns a single invoice
	 *
	 * @since 3.0
	 * @return array invoice data
	 */
	public function parse_get_invoice() {

		$invoice = array();

		$invoice_properties = array(
			'invoice_id',
			'client_id',
			'number',
			'amount',
			'amount_oustanding',
			'status',
			'date',
			'po_number',
			'discount',
			'notes',
			'terms',
			'currency_code',
			'folder',
			'language',
			'return_uri',
			'updated',
			'recurring_id',
			'organization',
			'first_name',
			'last_name',
			'p_street1',
			'p_street2',
			'p_city',
			'p_state',
			'p_country',
			'p_code',
			'vat_name',
			'vat_number',
			'staff_id',
		);

		foreach ( $invoice_properties as $property ) {

			$invoice[ $property ] = (string) $this->response_xml->invoice->$property;
		}

		// add links
		$invoice['client_view_url'] = (string) $this->response_xml->invoice->links->client_view;
		$invoice['view_url'] = (string) $this->response_xml->invoice->links->view;
		$invoice['edit_url'] = (string) $this->response_xml->invoice->links->edit;

		// add line items
		foreach ( $this->response_xml->invoice->lines->line as $line ) {

			$invoice['line_items'][] = array(
				'line_id'      => (string) $line->line_id,
				'amount'       => (string) $line->amount,
				'name'         => (string) $line->name,
				'description'  => (string) $line->description,
				'unit_cost'    => (string) $line->unit_cost,
				'quantity'     => (string) $line->quantity,
				'tax1_name'    => (string) $line->tax1_name,
				'tax2_name'    => (string) $line->tax2_name,
				'tax1_percent' => (string) $line->tax1_percent,
				'tax2_percent' => (string) $line->tax2_percent,
			);
		}

		return $invoice;
	}


	/** Client methods ******************************************************/


	/**
	 * Gets the created client ID from a create client API call.
	 *
	 * @since 3.0
	 *
	 * @return string created client ID
	 * @throws Framework\SV_WC_API_Exception upon missing client ID
	 */
	public function parse_create_client() {

		if ( ! isset( $this->response_xml->client_id ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'Created client ID is missing', 'woocommerce-freshbooks' ) );
		}

		return (string) $this->response_xml->client_id;
	}


	/**
	 * Returns a list of clients
	 *
	 * @since 3.0
	 * @return array clients
	 */
	public function parse_clients() {

		$clients = array();

		foreach ( $this->response_xml->clients->client as $client ) {

			$clients[] = array(
				'client_id'       => (string) $client->client_id,
				'first_name'      => (string) $client->first_name,
				'last_name'       => (string) $client->last_name,
				'organization'    => (string) $client->organization,
				'name'            => trim( (string) $client->first_name . ' ' . $client->last_name . ( (string) $client->organization ? ' ' . $client->organization : '' ) ),
				'email'           => (string) $client->email,
				'username'        => (string) $client->username,
				'contacts'        => array(), // implement if needed
				'work_phone'      => (string) $client->work_phone,
				'home_phone'      => (string) $client->home_phone,
				'mobile'          => (string) $client->mobile,
				'fax'             => (string) $client->fax,
				'language'        => (string) $client->language,
				'currency_code'   => (string) $client->currency_code,
				'credits'         => array(), // implement if needed
				'notes'           => (string) $client->notes,
				'p_street1'       => (string) $client->p_street1,
				'p_street2'       => (string) $client->p_street2,
				'p_city'          => (string) $client->p_city,
				'p_state'         => (string) $client->p_state,
				'p_country'       => (string) $client->p_country,
				'p_code'          => (string) $client->p_code,
				's_street1'       => (string) $client->s_street1,
				's_street2'       => (string) $client->s_street2,
				's_city'          => (string) $client->s_city,
				's_state'         => (string) $client->s_state,
				's_country'       => (string) $client->s_country,
				's_code'          => (string) $client->s_code,
				'client_view_url' => (string) $client->links->client_view,
				'view_url'        => (string) $client->links->view,
				'statement_url'   => (string) $client->links->statement,
				'vat_name'        => (string) $client->vat_name,
				'vat_number'      => (string) $client->vat_number,
				'updated'         => (string) $client->updated,
				'folder'          => (string) $client->folder,
			);
		}

		return $clients;
	}


	/** Payment methods ******************************************************/


	/**
	 * Gets the created payment ID from a create payment API call.
	 *
	 * @since 3.0
	 *
	 * @return string created payment ID
	 * @throws Framework\SV_WC_API_Exception upon missing payment ID
	 */
	public function parse_create_payment() {

		if ( ! isset( $this->response_xml->payment_id ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'Created payment ID is missing', 'woocommerce-freshbooks' ) );
		}

		return (string) $this->response_xml->payment_id;
	}


	/**
	 * Returns a single payment
	 *
	 * @since 3.0
	 * @return array payment data
	 */
	public function parse_get_payment() {

		$payment = array();

		$payment_properties = array(
			'payment_id',
			'client_id',
			'invoice_id',
			'date',
			'amount',
			'currency_code',
			'type',
			'notes',
			'updated',
		);

		foreach ( $payment_properties as $property ) {

			if ( isset( $this->response_xml->payment->$property ) ) {

				$payment[ $property ] = (string) $this->response_xml->payment->$property;
			}
		}

		return $payment;
	}


	/** Item methods ******************************************************/


	/**
	 * Returns invoice items
	 *
	 * @since 3.0
	 * @return array items
	 */
	public function parse_items() {

		$items = array();

		foreach ( $this->response_xml->items->item as $item ) {

			$items[ (string) $item->item_id ] = array(
				'name'        => (string) $item->name,
				'description' => (string) $item->description,
				'unit_cost'   => (string) $item->unit_cost,
				'quantity'    => (string) $item->quantity,
				'folder'      => (string) $item->folder,
				'inventory'   => (string) $item->inventory,
			);
		}

		return $items;
	}


	/**
	 * Returns number of pages in the items request
	 *
	 * @since 3.2.1
	 * @return int number of pages
	 */
	public function get_item_pages() {
		return isset( $this->response_xml->items ) ? (int) $this->response_xml->items['pages'] : 0;
	}


	/** Webhook methods ******************************************************/


	/**
	 * Returns active webhooks for this site
	 *
	 * @since 3.1
	 * @return array webhooks
	 */
	public function parse_get_webhooks() {

		$webhooks = array();

		if ( isset( $this->response_xml->callbacks->callback ) ) {

			foreach ( $this->response_xml->callbacks->callback as $callback ) {

				$webhooks[] = array(
					'id'       => (string) $callback->callback_id,
					'uri'      => (string) $callback->uri,
					'event'    => (string) $callback->event,
					'verified' => (string) $callback->verified,
				);
			}
		}

		return $webhooks;
	}


	/**
	 * Gets the created webhook ID from a create webhook API call.
	 *
	 * @since 3.0
	 *
	 * @return string created webhook ID
	 * @throws Framework\SV_WC_API_Exception upon missing webhook ID
	 */
	public function parse_create_webhook() {

		if ( ! isset( $this->response_xml->callback_id ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'Created webhook ID is missing', 'woocommerce-freshbooks' ) );
		}

		return (string) $this->response_xml->callback_id;
	}


	/** Misc methods ******************************************************/


	/**
	 * Returns languages available for the FreshBooks account
	 *
	 * @since 3.0
	 * @return array languages
	 */
	public function parse_languages() {

		$languages = array();

		foreach ( $this->response_xml->languages->language as $language ) {

			$languages[ (string) $language->code ] = (string) $language->name;
		}

		return $languages;
	}


	/**
	 * Returns the string representation of this response
	 *
	 * @since 3.0
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
	 * @since 3.2.0
	 * @see SV_WC_API_Response::to_string_safe()
	 * @return string response safe for logging/displaying
	 */
	public function to_string_safe() {

		// no sensitive data to mask
		return $this->to_string();
	}


}
