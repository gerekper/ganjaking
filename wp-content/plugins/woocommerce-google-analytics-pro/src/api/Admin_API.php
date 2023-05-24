<?php
/**
 * WooCommerce Google Analytics Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Google Analytics Pro to newer
 * versions in the future. If you wish to customize WooCommerce Google Analytics Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-google-analytics-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API;

use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API\Account_Summaries_Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API\Data_Streams_Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API\Measurement_Protocol_Secrets_Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API\Properties_Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API\Request;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Admin_API\Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Integration;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Google Analytics Admin API handler.
 *
 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest
 *
 * This functions as a lightweight substitute for the Google API client library.
 *
 * @see Integration::get_admin_api()
 *
 * @since 2.0.0
 */
class Admin_API extends Framework\SV_WC_API_Base {


	/**
	 * Sets up the API handler.
	 *
	 * @since 2.0.0
	 *
	 * @param string $access_token a Google API access token for authentication
	 */
	public function __construct( string $access_token = '' ) {

		$this->request_uri = 'https://analyticsadmin.googleapis.com/v1beta';

		$this->set_request_header( 'authorization', "Bearer {$access_token}" );
		$this->set_request_content_type_header( 'application/json' );
		$this->set_request_accept_header( 'application/json' );
	}


	/**
	 * Gets the main plugin instance.
	 *
	 * @since 2.0.0
	 *
	 * @return Plugin
	 */
	protected function get_plugin(): Plugin {

		return wc_google_analytics_pro();
	}


	/**
	 * Validates a response.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 * @throws Framework\SV_WC_API_Exception on errors
	 */
	protected function do_post_parse_response_validation(): bool {

		$response       = $this->get_response();
		$valid_response = false;

		if ( $response instanceof Response ) {

			$valid_response = $response->is_ok();

			if ( ! $valid_response ) {
				throw new Framework\SV_WC_API_Exception( $response->get_error_message(), $this->response->get_error_code() );
			}
		}

		return $valid_response;
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args request arguments
	 * @return Request object
	 */
	protected function get_new_request( $args = [], $data = [], $params = [] ) : Request {

		$args = wp_parse_args( $args, [
			'method' => 'GET',
			'path'   => '/',
		] );

		return new Request( $args['method'], $args['path'], $data, $params );
	}


	/**
	 * Gets Google Analytics account summaries.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/accountSummaries/list
	 *
	 * @since 2.0.0
	 *
	 * @param array $params request query parameters
	 * @return Account_Summaries_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_account_summaries( array $params = [] ) : Account_Summaries_Response {

		$request = $this->get_new_request(
			[
				'method' => 'GET',
				'path'   => '/accountSummaries',
			],
			[],
			array_merge( [ 'pageSize' => 200 ], $params )
		);

		$this->set_response_handler( Account_Summaries_Response::class );

		/** @var Account_Summaries_Response $account_summaries object */
		$account_summaries = $this->perform_request( $request );

		return $account_summaries;
	}


	/**
	 * Gets Google Analytics Data Streams.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams/list
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent the parent name, ie `properties/123456789`
	 * @return Data_Streams_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_data_streams( string $parent ): Data_Streams_Response {

		if ( ! $parent ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid parent name is required to list data streams.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		$request = $this->get_new_request( [
			'method' => 'GET',
			'path'   => "/{$parent}/dataStreams",
		] );

		$this->set_response_handler( Data_Streams_Response::class );

		/** @var Data_Streams_Response $data_streams */
		$data_streams = $this->perform_request( $request );

		return $data_streams;
	}


	/**
	 * Creates a Google Analytics Data Stream.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams/create
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent the parent name, ie `properties/123456789`
	 * @param array $properties properties for the data stream
	 * @return Data_Streams_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function create_data_stream( string $parent, array $properties ): Data_Streams_Response {

		if ( ! $parent ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid parent name is required to create data streams.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		$request = $this->get_new_request( [
			'method' => 'POST',
			'path'   => "/{$parent}/dataStreams",
		], $properties );

		$this->set_response_handler( Data_Streams_Response::class );

		/** @var Data_Streams_Response $data_streams */
		$data_streams = $this->perform_request( $request );

		return $data_streams;
	}


	/**
	 * Gets a Google Analytics Property.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties/get
	 *
	 * @since 2.0.0
	 *
	 * @param string $name the property name, ie `properties/123456789`
	 * @return Properties_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_property( string $name ): Properties_Response {

		if ( ! $name ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid property name is required to get the property.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		$request = $this->get_new_request( array(
			'method' => 'GET',
			'path'   => "/{$name}",
		) );

		$this->set_response_handler( Properties_Response::class );

		/** @var Properties_Response $property */
		$property = $this->perform_request( $request );

		return $property;
	}


	/**
	 * Gets Google Analytics Measurement Protocol Secrets.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets/list
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent the parent name, ie `properties/123456789/dataStreams/987654321`
	 * @return Measurement_Protocol_Secrets_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_measurement_protocol_secrets( string $parent ): Measurement_Protocol_Secrets_Response {

		if ( ! $parent ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid parent name is required to list measurement protocol secrets.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		$request = $this->get_new_request( [
			'method' => 'GET',
			'path'   => "/{$parent}/measurementProtocolSecrets",
		] );

		$this->set_response_handler( Measurement_Protocol_Secrets_Response::class );

		/** @var Measurement_Protocol_Secrets_Response $secrets */
		$secrets = $this->perform_request( $request );

		return $secrets;
	}


	/**
	 * Creates a Google Analytics Measurement Protocol Secret.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties.dataStreams.measurementProtocolSecrets/create
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent the parent name, ie `properties/123456789/dataStreams/987654321`
	 * @param string $display_name the display name for the API secret
	 * @return Measurement_Protocol_Secrets_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function create_measurement_protocol_secret( string $parent, string $display_name ): Measurement_Protocol_Secrets_Response {

		if ( ! $parent ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid parent name is required to create measurement protocol secrets.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		$request = $this->get_new_request( [
			'method' => 'POST',
			'path'   => "/{$parent}/measurementProtocolSecrets",
		], [
			'displayName' => $display_name,
		] );

		$this->set_response_handler( Measurement_Protocol_Secrets_Response::class );

		/** @var Measurement_Protocol_Secrets_Response $secret */
		$secret = $this->perform_request( $request );

		return $secret;
	}


	/**
	 * Acknowledges User Data Collection.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties/acknowledgeUserDataCollection
	 *
	 * @since 2.0.0
	 *
	 * @param string $parent the parent name, ie `properties/123456789`
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function acknowledge_user_data_collection( string $parent ): void {

		if ( ! $parent ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid parent name is required to acknowledge user data collection.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		// https://developers.google.com/analytics/devguides/config/admin/v1/rest/v1beta/properties/acknowledgeUserDataCollection#request-body
		$request = $this->get_new_request( [
			'method' => 'POST',
			'path'   => "/{$parent}:acknowledgeUserDataCollection",
		], [
			'acknowledgement' => 'I acknowledge that I have the necessary privacy disclosures and rights from my end users for the collection and processing of their data, including the association of such data with the visitation information Google Analytics collects from my site and/or app property.',
		] );

		$this->perform_request( $request );
	}


}
