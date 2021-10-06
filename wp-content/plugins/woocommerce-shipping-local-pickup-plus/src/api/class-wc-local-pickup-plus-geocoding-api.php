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
 * API for processing geocoding requests.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Geocoding_API extends Framework\SV_WC_API_Base {


	/** @var bool whether geocoding is enabled */
	private $enabled = false;

	/** @var null|bool whether logging is enabled */
	private $logging;


	/**
	 * Construct the API base.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$local_pickup_plus = $this->get_plugin();
		$shipping_method   = $local_pickup_plus->get_shipping_method_instance();

		if ( $shipping_method && $shipping_method->is_enabled() && $local_pickup_plus->geocoding_enabled() ) {

			$path = $local_pickup_plus->get_plugin_path() . '/src/api/';

			require_once( $path . 'class-wc-local-pickup-plus-geocoding-api-request.php' );
			require_once( $path . 'class-wc-local-pickup-plus-geocoding-api-response.php' );

			$this->response_handler = 'WC_Local_Pickup_Plus_Geocoding_API_Response';
			$this->request_uri      = 'https://maps.googleapis.com/maps/api/geocode';
			$this->enabled          = true;
		}
	}


	/**
	 * Get the plugin instance (implements parent method).
	 *
	 * @since 2.0.0
	 *
	 * @return \WC_Local_Pickup_Plus The extension main instance
	 */
	protected function get_plugin() {
		return wc_local_pickup_plus();
	}


	/**
	 * Check whether geocoding is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	protected function is_enabled() {
		return $this->enabled;
	}


	/**
	 * Whether logging of API requests is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function logging_enabled() {

		if ( ! is_bool( $this->logging ) ) {
			$this->logging = $this->get_plugin()->logging_enabled();
		}

		return (bool) $this->logging;
	}


	/**
	 * Make a geocoding request.
	 *
	 * @link https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of arguments
	 * @return \WC_Local_Pickup_Plus_Geocoding_API_Request
	 */
	protected function get_new_request( $args = array() ) {

		return new \WC_Local_Pickup_Plus_Geocoding_API_Request( $args );
	}


	/**
	 * Maps Local Pickup Plus address pieces into Google Maps API components.
	 *
	 * Google Maps API for geocoding wants a request formed with these components:
	 *
	 * - 'address': can be any string really, ideal for user input, otherwise map street address lines to it
	 * - 'components': optional, this is a formatted string to filter/narrow down the query to a less ambiguous area
	 * - 'key': an optional API key that is important to keep track of usage and pay for extra quota
	 *
	 * Note: key, when present, must be valid; components cannot be partial or truncated, only the address can be.
	 *
	 * @link https://developers.google.com/maps/documentation/geocoding/intro#GeocodingRequests
	 * @link https://developers.google.com/maps/documentation/geocoding/intro#ComponentFiltering
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of arguments formatted according Local Pickup Plus address conventions
	 * @return array array of valid Google Maps API request args
	 */
	private function parse_geocode_request_args( array $args ) {

		$address      = '';
		$components   = [];
		$request_args = [];

		if ( '' !== $args['address_1'] ) {
			$address .= trim( $args['address_1'] );
			if ( '' !== $args['address_2'] ) {
				$address .= ' ' . trim( $args['address_2'] );
			}
		}

		$address        = trim( $address );
		$map_components = [
			'locality'            => trim( $args['city'] ),
			'administrative_area' => trim( $args['state'] ),
			'postal_code'         => trim( $args['postcode'] ),
			'country'             => trim( $args['country'] ),
		];

		foreach ( $map_components as $k => $v ) {
			if ( '' !== $v ) {
				$components[ $k ] = stripslashes( $v );
			}
		}

		if ( empty( $address ) && ! empty( $components ) ) {
			if ( isset( $components['postal_code'] ) && '' !== $components['postal_code'] ) {
				$address = $components['postal_code'];
				unset( $components['postal_code'] );
			} else {
				$address = current( $components );
				unset( $components[ key( $components ) ] );
			}
		}

		if ( '' !== $address ) {

			$request_args['address'] = $address;

			if ( ! empty( $components ) ) {

				$component_parts = [];

				foreach ( $components as $k => $v ) {
					$component_parts[] = $k . ':' . str_replace( [ ':', '|', '&' ], ' ', $v );
				}

				$request_args['components'] = implode( '|', $component_parts );
			}

			$request_args['key'] = $this->get_plugin()->get_shipping_method_instance()->get_google_maps_api_key();
		}

		/**
		 * Filters the geocoding request arguments.
		 *
		 * @since 2.8.3
		 *
		 * @param array $request_args request arguments
		 * @param array $args raw arguments
		 */
		return (array) apply_filters( 'wc_local_pickup_plus_geocoding_request_args', $request_args, $args );
	}


	/**
	 * Get coordinates as an associative array (lat, lon) of two floats.
	 *
	 * @since 2.0.0
	 *
	 * @param array|string $args array of arguments or a freeform text string (more approximate)
	 * @return array|null get coordinates array or null if request is invalid or an error occurred
	 */
	public function get_coordinates( $args ) {

		if ( ! empty( $args ) && $this->is_enabled() ) {

			$errors = array();
			$args   = is_string( $args ) ? array( 'address_1' => trim( $args ) ) : $args;
			$args   = $this->parse_geocode_request_args( wp_parse_args( $args, array(
				'country'   => '',
				'state'     => '',
				'postcode'  => '',
				'city'      => '',
				'address_1' => '',
				'address_2' => '',
			) ) );

			if ( ! empty( $args ) ) {

				try {

					$request = $this->get_new_request( $args );

					if ( ! empty( $request ) && $this->logging_enabled() ) {

						$this->get_plugin()->log( 'New geocoding request...' );
						$this->get_plugin()->log( 'Arguments:' );
						$this->get_plugin()->log( print_r( $args, true ) );
						$this->get_plugin()->log( 'Processed request:' );
						$this->get_plugin()->log( print_r( $request, true ) );
						$this->get_plugin()->log( 'Awaiting response...' );
					}

					try {

						$response_raw     = $this->perform_request( $request );
						$response_handler = new \WC_Local_Pickup_Plus_Geocoding_API_Response( $response_raw );

						try {
							$coordinates = $response_handler->get_coordinates();
						} catch ( Exception $e ) {
							$errors[] = $e->getMessage();
						}

					} catch ( Exception $e ) {
						$errors[] = $e->getMessage();
					}

				} catch ( Exception $e ) {
					$errors[] = $e->getMessage();
				}
			}

			if ( $this->logging_enabled() ) {

				// log coordinates
				if ( ! empty( $coordinates ) ) {
					$this->get_plugin()->log( 'Coordinates:' );
					$this->get_plugin()->log( print_r( $coordinates, true ) );
				}

				// log errors
				if ( ! empty( $errors ) ) {

					$this->get_plugin()->log( count( $errors ) > 1 ? 'Errors:' : 'Error:' );

					foreach ( $errors as $error_message ) {
						if ( is_array( $error_message ) ) {
							$this->get_plugin()->log( print_r( $error_message, true ) );
						} elseif ( is_string( $error_message ) ) {
							$this->get_plugin()->log( $error_message );
						}
					}
				}
			}
		}

		return empty( $coordinates ) ? null : $coordinates;
	}


}
