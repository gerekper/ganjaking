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
 * FreshBooks API Class
 *
 * Handles sending/receiving/parsing of FreshBooks XML, this is the main API
 * class responsible for communication with the FreshBook's API
 *
 * @since 3.0
 */
class WC_FreshBooks_API extends Framework\SV_WC_API_Base {


	/**
	 * Sets up the request object and endpoint.
	 *
	 * @since 3.0
	 *
	 * @param string $endpoint API endpoint
	 * @param string $authentication_token API auth token
	 */
	public function __construct( $endpoint, $authentication_token ) {

		// request URI does not vary for requests
		$this->request_uri = $endpoint;

		// set headers
		$this->set_request_content_type_header( 'application/xml' );
		$this->set_request_accept_header( 'application/xml' );

		// set response handler
		$this->response_handler = 'WC_FreshBooks_API_Response';

		// set auth creds
		$this->set_http_basic_auth( $authentication_token, '' );
	}


	/** Invoice methods ******************************************************/


	/**
	 * Creates a new invoice.
	 *
	 * Note this requires a client to have already been created for the associated invoice.
	 *
	 * @since 3.0
	 *
	 * @param \WC_FreshBooks_Order order object
	 * @return string created invoice ID
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function create_invoice( \WC_FreshBooks_Order $order ) {

		$request = $this->get_new_request();

		$request->create_invoice( $order );

		$response = $this->perform_request( $request );

		return $response->parse_create_invoice();
	}


	/**
	 * Gets a single invoice.
	 *
	 * @since 3.0
	 *
	 * @param string $invoice_id FreshBooks invoice ID
	 * @return array invoice data
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_invoice( $invoice_id ) {

		$request = $this->get_new_request();

		$request->get_invoice( $invoice_id );

		$response = $this->perform_request( $request );

		return $response->parse_get_invoice();
	}


	/**
	 * Updates a new invoice.
	 *
	 * Note this requires a client to have already been created for the associated invoice.
	 *
	 * @since 3.2.0
	 *
	 * @param \WC_FreshBooks_Order order object
	 * @return string updated invoice ID
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function update_invoice( WC_FreshBooks_Order $order ) {

		$request = $this->get_new_request();

		$request->update_invoice( $order );

		// no response for invoice updates
		$this->perform_request( $request );
	}


	/**
	 * Sends a single invoice to the client.
	 *
	 * @since 3.0
	 *
	 * @param string $invoice_id FreshBooks invoice ID
	 * @param string $method sending method, either `email` or `snail_mail`
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function send_invoice( $invoice_id, $method ) {

		$request = $this->get_new_request();

		$request->send_invoice( $invoice_id, $method );

		// no response for invoice sends
		$this->perform_request( $request );
	}


	/** Client methods ******************************************************/


	/**
	 * Creates a client for the given order.
	 *
	 * @since 3.0
	 *
	 * @param \WC_FreshBooks_Order $order order object
	 * @return array newly-created client info
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function create_client( WC_FreshBooks_Order $order ) {

		$request = $this->get_new_request();

		$request->create_client( $order );

		$response = $this->perform_request( $request );

		return $response->parse_create_client();
	}


	/**
	 * Fetches active clients.
	 *
	 * Results cached for 5 minutes in `wc_freshbooks_active_clients` transient.
	 *
	 * @since 3.0
	 *
	 * @return array
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_active_clients() {

		if ( false === ( $active_clients = get_transient( 'wc_freshbooks_active_clients' ) ) ) {

			$request = $this->get_new_request();

			$request->get_clients( 'active' );

			$response = $this->perform_request( $request );

			$active_clients = $response->parse_clients();

			set_transient( 'wc_freshbooks_active_clients', $active_clients, 5 * MINUTE_IN_SECONDS );
		}

		return $active_clients;
	}


	/**
	 * Fetches clients with the specified email.
	 *
	 * @since 3.0
	 *
	 * @param string $email client email
	 * @return array
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_clients_by_email( $email ) {

		$request = $this->get_new_request();

		$request->get_clients( 'active', $email );

		$response = $this->perform_request( $request );

		return $response->parse_clients();
	}


	/** Payment methods ******************************************************/


	/**
	 * Creates a invoice payment for the given order.
	 *
	 * @since 3.0
	 *
	 * @param \WC_FreshBooks_Order $order
	 * @return string payment ID
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function create_payment( WC_FreshBooks_Order $order ) {

		$request = $this->get_new_request();

		$request->create_payment( $order );

		$response = $this->perform_request( $request );

		return $response->parse_create_payment();
	}


	/**
	 * Gets payment for the given ID.
	 *
	 * @since 3.0
	 *
	 * @param string $payment_id
	 * @return array payment info
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_payment( $payment_id ) {

		$request = $this->get_new_request();

		$request->get_payment( $payment_id );

		$response = $this->perform_request( $request );

		return $response->parse_get_payment();
	}


	/** Item methods ******************************************************/


	/**
	 * Fetches active items.
	 *
	 * Results cached for 5 minutes in `wc_freshbooks_active_items` transient.
	 *
	 * @since 3.0
	 *
	 * @return array
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_active_items() {

		if ( false === ( $active_items = get_transient( 'wc_freshbooks_active_items' ) ) ) {

			$request = $this->get_new_request();

			$request->get_items( 'active' );

			$response = $this->perform_request( $request );

			$active_items = $response->parse_items();

			// get remaining pages, if any
			$page = 2;

			while ( $page <= $response->get_item_pages() ) {

				$request = $this->get_new_request();

				$request->get_items( 'active', $page );

				$response = $this->perform_request( $request );

				$active_items = array_merge( $active_items, $response->parse_items() );

				$page++;
			}

			set_transient( 'wc_freshbooks_active_items', $active_items, 5 * MINUTE_IN_SECONDS );
		}

		return $active_items;
	}


	/** Misc methods ******************************************************/


	/**
	 * Fetches languages available for invoices.
	 *
	 * Results cached for 5 minutes in `wc_freshbooks_languages` transient.
	 *
	 * @since 3.0
	 *
	 * @return array in format { ISO-639-1 lang code => lang name }
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_languages() {

		if ( false === ( $languages = get_transient( 'wc_freshbooks_languages' ) ) ) {

			$request = $this->get_new_request();

			$request->get_languages();

			$response = $this->perform_request( $request );

			$languages = $response->parse_languages();

			set_transient( 'wc_freshbooks_languages', $languages, 5 * MINUTE_IN_SECONDS );
		}

		return $languages;
	}


	/**
	 * Gets all active webhooks.
	 *
	 * @since 3.1
	 *
	 * @return array webhooks
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function get_webhooks() {

		$request = $this->get_new_request();

		$request->get_webhooks();

		$response = $this->perform_request( $request );

		return $response->parse_get_webhooks();
	}


	/**
	 * Creates a webhook for all events.
	 *
	 * @since 3.0
	 *
	 * @return string created webhook ID
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function create_webhook() {

		$request = $this->get_new_request();

		$request->create_webhook();

		$response = $this->perform_request( $request );

		return $response->parse_create_webhook();
	}


	/**
	 * Get all active webhooks.
	 *
	 * @since 3.1
	 *
	 * @param string $webhook_id webhook ID to delete
	 * @throws Framework\SV_WC_API_Exception upon network issues, timeouts, API errors, etc
	 */
	public function delete_webhook( $webhook_id ) {

		$request = $this->get_new_request();

		$request->delete_webhook( $webhook_id );

		$this->perform_request( $request );

		// no response, 200 OK means it was deleted successfully
	}


	/**
	 * Verifies a created webhook.
	 *
	 * Note this isn't for verifying individual webhook events, but rather for verifying that the URL specifed when creating the webhook actually exists and is valid.
	 *
	 * @since 3.0
	 *
	 * @param string $webhook_id to verify
	 * @param string $verifier verification string
	 * @throws Framework\SV_WC_API_Exception on verification failed, network issues, timeouts, API errors, etc
	 */
	public function verify_webhook( $webhook_id, $verifier ) {

		$request = $this->get_new_request();

		$request->verify_webhook( $webhook_id, $verifier );

		// no response for verify, an exception will be raised if verification fails
		$this->perform_request( $request );
	}


	/** Helper methods ******************************************************/


	/**
	 * Check if the response has any errors
	 *
	 * @see \SV_WC_API_Base::do_post_parse_response_validation()
	 *
	 * @since 3.2.0
	 *
	 * @throws Framework\SV_WC_API_Exception if response has API error
	 */
	protected function do_post_parse_response_validation() {

		if ( $this->get_response()->has_api_error() ) {

			throw new Framework\SV_WC_API_Exception( $this->get_response()->get_api_error_message(), $this->get_response()->get_api_error_code() );
		}
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @since 3.0
	 *
	 * @param array $type unused
	 * @return \WC_FreshBooks_API_Request API request object
	 */
	protected function get_new_request( $type = array() ) {

		return new \WC_FreshBooks_API_Request();
	}


	/**
	 * Gets the main plugin class.
	 *
	 * @see \SV_WC_API_Base::get_plugin()
	 *
	 * @since 3.2.0
	 *
	 * @return object
	 */
	protected function get_plugin() {

		return wc_freshbooks();
	}


}
