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

use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Measurement_Protocol_API\Response;
use SkyVerge\WooCommerce\Google_Analytics_Pro\API\Measurement_Protocol_API\Request;
use SkyVerge\WooCommerce\Google_Analytics_Pro\Plugin;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The Measurement Protocol API for GA4 wrapper class.
 *
 * A basic wrapper around the GA Measurement Protocol HTTP API used for making
 * server-side API calls to track events.
 *
 * @since 2.0.0
 */
class Measurement_Protocol_API extends Framework\SV_WC_API_Base {


	/** @var string|null Google Analytics measurement ID */
	private ?string $measurement_id;

	/** @var string|null the API secret */
	private ?string $api_secret;


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 *
	 * @param ?string $measurement_id
	 * @param ?string $api_secret
	 */
	public function __construct( ?string $measurement_id, ?string $api_secret ) {

		$this->measurement_id = $measurement_id;
		$this->api_secret     = $api_secret;
		$this->request_uri    = 'https://www.google-analytics.com/mp/collect';
		$this->request_method = 'POST';
	}


	/**
	 * Collects (records) an event via the Measurement Protocol API.
	 *
	 * @since 2.0.0
	 *
	 * @param array $data event data
	 * @return bool whether the event was collected or not
	 */
	public function collect(array $data): bool {

		if (! $this->measurement_id || ! $this->api_secret || empty( $data['client_id'] )) {
			return false;
		}

		try {

			$this->set_response_handler( Response::class );

			$this->perform_request( $this->get_new_request()->set_data( $data ) );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			/* translators: Placeholders: %s - error message */
			$error = sprintf( __( 'Error tracking event: %s', 'woocommerce-google-analytics-pro' ), $e->getMessage() );

			if ( wc_google_analytics_pro()->get_integration()->debug_mode_on() ) {
				wc_google_analytics_pro()->log( $error );
			}

			return false;
		}

		return true;
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args unused
	 *
	 * @return Request
	 */
	protected function get_new_request( $args = null ): Request {

		return new Request( $this->measurement_id, $this->api_secret );
	}


	/**
	 * Gets the plugin instance.
	 *
	 * @see Framework\SV_WC_API_Base::get_plugin()
	 *
	 * @since 2.0.0
	 * @return Plugin
	 */
	protected function get_plugin(): Plugin {

		return wc_google_analytics_pro();
	}


	/**
	 * Gets the request URL query.
	 *
	 * This method is based on Framework\SV_WC_API_Base::get_request_query(), except it does not check the request method.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_request_query(): string {

		$query   = '';
		$request = $this->get_request();

		if ( $request ) {

			$params = $request->get_params();

			if ( ! empty( $params ) ) {
				$query = http_build_query( $params, '', '&' );
			}
		}

		return $query;
	}


	/**
	 * Gets the request data for broadcasting the request.
	 *
	 * Overridden to ensure the API secret is masked in logs, etc.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_request_data_for_broadcast() : array {

		return array_merge(
			parent::get_request_data_for_broadcast(),
			[
				'uri' => add_query_arg( 'api_secret', '***', $this->get_request_uri() ),
			]
		);
	}


}
