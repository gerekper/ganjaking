<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 *  Geocoding response object.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Geocoding_API_Response extends Framework\SV_WC_API_JSON_Response {


	/**
	 * Geocoding API Response constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string|object $raw_response a response as JSON object or JSON encoded string
	 */
	public function __construct( $raw_response ) {

		$raw_response = is_object( $raw_response ) ? json_encode( $raw_response ) : $raw_response;

		parent::__construct( $raw_response );
	}


	/**
	 * Get coordinates from an API response.
	 *
	 * @since 2.0.0
	 *
	 * @return array An associative array with lat, lon keys and coordinates as floats
	 * @throws Framework\SV_WC_Plugin_Exception An exception if an error occurred and coordinates weren't retrieved
	 */
	public function get_coordinates() {

		// Google: "it is OK."
		$success = isset( $this->response_data->response_data->status ) && 'OK' === $this->response_data->response_data->status;

		if ( ! $success && isset( $this->response_data->response_data->error_message ) ) {
			throw new Framework\SV_WC_Plugin_Exception( $this->response_data->response_data->error_message );
		}

		$coordinates = null;

		// Google may return more than one result on fuzzy search results, but we will return only the first result which is likely the best match
		if ( isset( $this->response_data->response_data->results, $this->response_data->response_data->results[0]->geometry->location ) ) {

			$results = $this->response_data->response_data->results[0]->geometry->location;

			if ( isset( $results->lat, $results->lng ) ) {

				$lat = $results->lat;
				$lon = $results->lng;

				$coordinates = array( 'lat' => $lat, 'lon' => $lon );
			}
		}

		return $coordinates;
	}


}
