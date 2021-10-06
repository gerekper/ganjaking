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
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Google_Analytics_Pro\API;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API\Account_Summaries_Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API\Profiles_Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API\Request;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Management_API\Response;
use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Google Analytics Management API handler.
 *
 * @link https://developers.google.com/analytics/devguides/config/mgmt/v3/
 *
 * This functions as a lightweight substitute for the Google API client library.
 * @see \WC_Google_Analytics_Pro_Integration::get_management_api()
 *
 * @since 1.7.0
 */
class Management_API extends Framework\SV_WC_API_Base {


	/**
	 * Sets up the API handler.
	 *
	 * @since 1.7.0
	 *
	 * @param string $access_token a Google API access token for authentication
	 */
	public function __construct( $access_token ) {

		$this->request_uri = 'https://www.googleapis.com/analytics/v3/management';

		$this->set_request_header( 'authorization', sprintf( 'Bearer %s', is_string( $access_token ) ? $access_token : '' ) );
	}


	/**
	 * Gets the main plugin instance.
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_Google_Analytics_Pro
	 */
	protected function get_plugin() {

		return wc_google_analytics_pro();
	}


	/**
	 * Validates a response.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 * @throws Framework\SV_WC_API_Exception on errors
	 */
	protected function do_post_parse_response_validation() {

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
	 * @since 1.7.0
	 *
	 * @param array $args request arguments
	 * @return Request object
	 */
	protected function get_new_request( $args = [] ) {

		$args = wp_parse_args( $args, [
			'method' => 'GET',
			'path'   => '/',
		] );

		return new Request( $args['method'], $args['path'] );
	}


	/**
	 * Gets Google Analytics account summaries.
	 *
	 * @link https://developers.google.com/analytics/devguides/config/mgmt/v3/mgmtReference/management/accountSummaries/list
	 *
	 * @since 1.7.0
	 *
	 * @return Account_Summaries_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_account_summaries() {

		$request = $this->get_new_request( [
			'method' => 'GET',
			'path'   => '/accountSummaries',
		] );

		$this->set_response_handler( '\\SkyVerge\\WooCommerce\\Google_Analytics_Pro\\API\\Management_API\\Account_Summaries_Response' );

		/** @var Account_Summaries_Response $account_summaries object */
		$account_summaries = $this->perform_request( $request );

		return $account_summaries;
	}


	/**
	 * Gets Google Analytics views (profiles).
	 *
	 * @link https://developers.google.com/analytics/devguides/config/mgmt/v3/mgmtReference/management/profiles/list
	 *
	 * @since 1.7.0
	 *
	 * @param string $account_id the Google Analytics account ID
	 * @param string $property_id the web property to get views (profiles) for
	 * @return Profiles_Response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_profiles( $account_id, $property_id ) {

		if ( '' === $account_id || ! is_string( $account_id ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid Google Analytics Account ID ID is required to list user profiles.', 'woocommerce-google-analytics-pro' ), 400 );
		}

		if ( '' === $property_id || ! is_string( $property_id ) ) {
			throw new Framework\SV_WC_API_Exception( __( 'A valid Web Property ID is required to list user profiles.', 'woocommerce-google-analytics-pro' ), 400 ) ;
		}

		$request = $this->get_new_request( array(
			'method' => 'GET',
			'path'   => "/accounts/{$account_id}/webproperties/{$property_id}/profiles",
		) );

		$this->set_response_handler( '\\SkyVerge\\WooCommerce\\Google_Analytics_Pro\\API\\Management_API\\Profiles_Response' );

		/** @var Profiles_Response $profiles */
		$profiles = $this->perform_request( $request );

		return $profiles;
	}


}
