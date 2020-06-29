<?php
/**
 * API wrapper class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

use GuzzleHttp\Client as Client;

if ( ! class_exists( 'YITH_WCAC_API' ) ) {
	/**
	 * Wrapper for version 3.0 of Active Campaign API
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_API {
		/**
		 * Pattern that Api URL must match to pass validation
		 *
		 * @var string
		 */
		const API_URL_PATTERN = '/https:\/\/([a-zA-Z0-9-_]+)\.api-[a-z0-9]+\.com/';

		/**
		 * Api access url
		 *
		 * @var string
		 */
		protected $_api_url;

		/**
		 * Api access key
		 *
		 * @var string
		 */
		protected $_api_key;

		/**
		 * HTTP Client
		 * Used to perform all HTTP requests to server
		 *
		 * @var Client
		 */
		protected $_client;

		/**
		 * Connection params used to initialize HTTP client
		 *
		 * @var array
		 */
		protected $_connection_params = [];

		/**
		 * Construct object for API wrapper
		 *
		 * @param string $api_url API access url.
		 * @param string $api_key API key.
		 * @param array  $args Array of optional arguments for wrapper initialization. // TODO: add documentation for possible params.
		 *
		 * @return void
		 */
		public function __construct( $api_url, $api_key, $args = [] ) {
			// register api url and api key inside class properties.
			$this->_api_key = $api_key;
			$this->_api_url = $api_url;

			// filter connection params, and store them in class property.
			$defaults = [
				'timeout' => '5',
				'delay' => null,
				'force_ip_resolve' => null,
				'headers' => [],
				'proxy' => null,
				'read_timeout' => '60',
				'verify' => true,
			];
			$this->_connection_params = wp_parse_args( $args, $defaults );
		}

		/**
		 * Create client for request processing
		 *
		 * @return bool Status of connection
		 */
		public function maybe_connect() {
			// if ( $this->is_connected() ) {
			if ( isset( $this->_connection_params['base_uri'] ) ) {
				return true;
			}

			// create connection url.
			$connection_url = $this->_api_url;

			// check if we have a valid api connection url.
			if ( ! preg_match( self::API_URL_PATTERN, $connection_url ) ) {
				return false;
			}

			// append suffix to connection url, when needed.
			if ( false === strpos( $this->_api_url, 'api/3' ) ) {
				$connection_url = trailingslashit( $connection_url ) . 'api/3/';
			}

			$this->_connection_params['base_uri'] = $connection_url;

			// create authentication token.
			if ( ! isset( $this->_connection_params['headers'] ) ) {
				$this->_connection_params['headers'] = [];
			}

			$this->_connection_params['headers']['Api-Token'] = $this->_api_key;

			// 	temporarily disable Guzzle and use curl instead
			return true;

			// init client.
			$this->_client = new Client( apply_filters( 'yith_wcac_api_connection_params', $this->_connection_params ) );

			// return true, as operation was successfully completed.
			return true;
		}

		/**
		 * Checks whether wrapper is already connected
		 *
		 * @return bool Whether wrapper is already connected.
		 */
		public function is_connected() {
			// 	temporarily disable Guzzle and use curl instead
			return true;

			return ! ! $this->_client;
		}

		/**
		 * Performs an API call to Active Campaign
		 *
		 * @param string $method HTTP method to use for the call.
		 * @param string $endpoint Endpoint to call.
		 * @param array  $body Object to send within request body (will be json-encoded).
		 * @param array  $query Query string params to send within the request.
		 * @param array  $args  Array of additional parameters to send within request.
		 *
		 * @throws GuzzleHttp\Exception\GuzzleException Throws exception when an error occurs with connection (both client, connection and server side @see http://docs.guzzlephp.org/en/stable/quickstart.html#exceptions).
		 * @throws Throwable Throws plain exception when it cannot create client object (wrong url).
		 */
		public function call( $method, $endpoint, $body = [], $query = [], $args = [] ) {
			// first of all, make sure we have a valid client.
			if ( ! $this->maybe_connect() ) {
				throw new Exception( __( 'Error while establishing connection to Active Campaign API server. Please, try again later', 'yith-woocommerce-active-campaign' ), 001 );
			}

			// set up json body of the request.
			if ( ! empty( $body ) ) {
				$args['body'] = json_encode( $body );
			}

			// set up query string for the request.
			if ( ! empty( $query ) ) {
				$args['query'] = $query;
			}

			// perform request.
			// temporarily disable Guzzle
			// $response = $this->_client->request( $method, $endpoint, $args );
			// return @json_decode( (string) $response->getBody() );

			// and use curl instead...
			$destination_url = $this->_connection_params['base_uri'] . $endpoint;

			if ( ! empty( $query ) ) {
				$destination_url = add_query_arg( $query, $destination_url );
			}

			$body = 'GET' === $method ? $body : json_encode( $body );

			$args = array_merge(
				[
					'timeout' => $this->_connection_params['timeout'],
					'reject_unsafe_urls' => true,
					'blocking' => true,
					'sslverify' => true,
					'attempts' => 0,
					'headers' => $this->_connection_params['headers'],
				],
				$args,
				[
					'method' => $method,
					'body' => $body
				]
			);

			$response = wp_remote_request( $destination_url, $args );

			if( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message(), 400 );
			}
			else {
				$resp_body = isset( $response['body'] ) ? @json_decode( $response['body'] ) : '';
				$status    = isset( $response['response'] ) ? $response['response']['code'] : false;

				if ( ! in_array( $status, [ 200, 201 ] ) ) {
					if( isset( $resp_body->errors ) ){
						$message = implode( ' | ', wp_list_pluck( $resp_body->errors, 'title' ) );
					} else {
						$message = __( 'There was an error with your request; please try again later.', 'yith-woocommerce-active-campaign' );
					}

					throw new Exception( $message, $status );
				} else {
					return $resp_body;
				}
			}
		}

		/* === UTILITY METHODS === */

		/**
		 * Returns account name, retrieving from currently set API URL; false on failure
		 *
		 * @return string|bool Account name or false on failure
		 */
		public function get_account_name() {
			if ( ! $this->_api_url ) {
				return false;
			}

			if ( ! preg_match( self::API_URL_PATTERN, $this->_api_url, $matches ) ) {
				return false;
			}

			return isset( $matches[1] ) ? $matches[1] : false;
		}
	}
}