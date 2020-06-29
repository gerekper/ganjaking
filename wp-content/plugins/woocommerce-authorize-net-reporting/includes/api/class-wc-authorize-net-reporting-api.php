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
 * Authorize.Net Reporting API Class
 *
 * Handles sending/receiving/parsing of Reporting XML
 *
 * @since 1.0
 */
class WC_Authorize_Net_Reporting_API extends Framework\SV_WC_API_Base {


	/** string API production endpoint */
	const PRODUCTION_ENDPOINT = 'https://api2.authorize.net/xml/v1/request.api';

	/** string API test endpoint */
	const TEST_ENDPOINT = 'https://apitest.authorize.net/xml/v1/request.api';

	/** @var string request URI */
	public $request_uri;

	/** var string API login ID */
	private $api_login_id;

	/** @var string API tranasction key */
	private $api_transaction_key;


	/**
	 * Sets up the request object and endpoint.
	 *
	 * @since 1.0
	 *
	 * @param string $api_login_id
	 * @param string $api_transaction_key
	 * @param string $environment API environment to POST transactions to
	 */
	public function __construct( $api_login_id, $api_transaction_key, $environment ) {

		// request URI does not vary for requests
		$this->request_uri = ( 'production' === $environment ) ? self::PRODUCTION_ENDPOINT : self::TEST_ENDPOINT;

		$this->set_request_content_type_header( 'application/xml' );
		$this->set_request_accept_header( 'application/xml' );

		// set response handler
		$this->response_handler = 'WC_Authorize_Net_Reporting_API_Response';

		// set auth creds
		$this->api_login_id        = $api_login_id;
		$this->api_transaction_key = $api_transaction_key;
	}


	/**
	 * Gets a list of settled batches in the provided date ranges.
	 *
	 * If a start date *and* end date is not provided, any batches settled in the past 24 hours will be retrieved.
	 *
	 * @since 1.0
	 *
	 * @param string $start_date optional start date in format required by API (yyyy-mm-ddTHH:MM:SS)
	 * @param string $end_date optional end date in format required by API (yyyy-mm-ddTHH:MM:SS)
	 * @return array batches
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_batches_by_date( $start_date, $end_date ) {

		// set start/end dates to format required by API
		if ( empty( $start_date ) && empty( $end_date ) ) {

			// return settled batches from the last 24 hours
			$start_date = $end_date = null;

		} elseif ( empty( $start_date ) && ! empty( $end_date ) ) {

			// set start date to 31 days prior to now
			$start_date = date( 'Y-m-d\T00:00:00', strtotime( 'now -31 days' ) );
			$end_date   = date( 'Y-m-d\T23:59:59', strtotime( $end_date ) );

		} elseif ( ! empty( $start_date ) && empty( $end_date ) ) {

			// set end date to today
			$start_date = date( 'Y-m-d\T00:00:00', strtotime( $start_date ) );
			$end_date   = date( 'Y-m-d\T23:59:59', current_time( 'timestamp' ) );

		} else {

			// use the provided dates
			$start_date = date( 'Y-m-d\T00:00:00', strtotime( $start_date ) );
			$end_date   = date( 'Y-m-d\T23:59:59', strtotime( $end_date ) );
		}

		$request = $this->get_new_request();

		$request->get_settled_batch_list( $start_date, $end_date );

		return $this->perform_request( $request )->get_batches();
	}


	/**
	 * Gets a list of transactions for the given batch ID.
	 *
	 * @since 1.0
	 *
	 * @param string $batch_id
	 * @return array transactions
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_transactions_by_batch_id( $batch_id ) {

		$request = $this->get_new_request();

		$request->get_transaction_list( $batch_id );

		return $this->perform_request( $request )->get_transactions();
	}


	/**
	 * Gets the details for a specific transaction ID.
	 *
	 * @since 1.0
	 *
	 * @param string $transaction_id
	 * @return array transaction details
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_transaction_details_by_transaction_id( $transaction_id ) {

		$request = $this->get_new_request();

		$request->get_transaction_details( $transaction_id );

		return $this->perform_request( $request )->get_transaction_details();
	}


	/**
	 * Get a list of transaction details for the provided date ranges.
	 *
	 * If either a start date or end date is not provided, any transactions settled in the past 24 hours will be retrieved.
	 *
	 * @since 1.0
	 *
	 * @param string $start_date optional start date in format required by API (yyyy-mm-ddTHH:MM:SS)
	 * @param string $end_date optional end date in format required by API (yyyy-mm-ddTHH:MM:SS)
	 * @return array transaction details
	 * @throws Framework\SV_WC_Plugin_Exception
	 */
	public function get_transaction_details_by_date_range( $start_date, $end_date ) {

		$transaction_details = array();

		// first get the settled batches during the time period provided
		$batches = $this->get_batches_by_date( $start_date, $end_date );

		// iterate through each batch
		foreach ( $batches as $batch ) {

			// get the list of transactions for each batch
			$transactions = $this->get_transactions_by_batch_id( $batch['batchId'] );

			// iterate through each transaction
			foreach ( $transactions as $transaction ) {

				// get the details for each transaction in the batch
				$transaction_details[] = $this->get_transaction_details_by_transaction_id( $transaction['transId'] );
			}
		}

		return $transaction_details;
	}


	/**
	 * Checks if the response has any errors.
	 *
	 * @see SV_WC_API_Base::do_post_parse_response_validation()
	 *
	 * @since 1.1.1
	 *
	 * @throws Framework\SV_WC_API_Exception
	 */
	protected function do_post_parse_response_validation() {

		if ( $this->get_response()->has_api_error() ) {

			throw new Framework\SV_WC_API_Exception( sprintf( __( 'Code: %s, Message: %s', 'woocommerce-authorize-net-reporting' ), $this->get_response()->get_api_error_code(), $this->get_response()->get_api_error_message() ) );
		}
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @see SV_WC_API_Base::get_new_request()
	 *
	 * @since 1.1.1
	 *
	 * @param array $args unused
	 * @return \WC_Authorize_Net_Reporting_API_Request API request object
	 */
	protected function get_new_request( $args = array() ) {

		return new \WC_Authorize_Net_Reporting_API_Request( $this->api_login_id, $this->api_transaction_key );
	}


	/**
	 * Returns the main plugin class.
	 *
	 * @see SV_WC_API_Base::get_plugin()
	 *
	 * @since 1.1.1
	 *
	 * @return \WC_Authorize_Net_Reporting
	 */
	protected function get_plugin() {

		return wc_authorize_net_reporting();
	}


}
