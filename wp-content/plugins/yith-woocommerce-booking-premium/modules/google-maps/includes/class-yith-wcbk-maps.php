<?php
/**
 * Class YITH_WCBK_Maps
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleMaps
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Maps' ) ) {
	/**
	 * Class YITH_WCBK_Maps
	 */
	class YITH_WCBK_Maps {
		use YITH_WCBK_Singleton_Trait;

		const GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

		/**
		 * API Key
		 *
		 * @var string
		 */
		private $api_key = '';

		/**
		 * Geocode API Key.
		 *
		 * @var string
		 */
		private $geocode_api_key = '';

		/**
		 * YITH_WCBK_Maps constructor.
		 */
		protected function __construct() {
			$this->api_key         = get_option( 'yith-wcbk-google-maps-api-key', '' );
			$this->geocode_api_key = get_option( 'yith-wcbk-google-maps-geocode-api-key', '' );
		}

		/**
		 * Get location coordinate by an address
		 *
		 * @param string $address The address.
		 *
		 * @return array
		 */
		public function get_location_by_address( $address = '' ) {
			$location = apply_filters( 'yith_wcbk_maps_pre_get_location_by_address', null, $address );

			if ( is_null( $location ) ) {
				$use_transient = apply_filters( 'yith_wcbk_maps_get_location_by_address_use_transients', true );
				$transient     = 'yith_wcbk_maps_location_by_address_' . md5( $address );

				$location = $use_transient ? get_transient( $transient ) : false;
				if ( false === $location ) {

					$location = array();
					if ( $address && ( $this->api_key || $this->geocode_api_key ) ) {
						$data = $this->get_data_by_address( $address );

						if ( isset( $data['status'] ) && 'OK' === $data['status'] && isset( $data['results'][0]['geometry']['location'] ) ) {
							$location = $data['results'][0]['geometry']['location'];

							if ( $use_transient ) {
								set_transient( $transient, $location, MONTH_IN_SECONDS );
							}

							/**
							 * DO_ACTION: yith_wcbk_maps_get_location_by_address_success
							 * Hook to perform some action after successfully retrieving the location coordinates by the address.
							 *
							 * @param array  $location The location retrieved by Google Maps.
							 * @param string $address  The address.
							 * @param array  $data     Data provided by the request to the Google Geocode API.
							 */
							do_action( 'yith_wcbk_maps_get_location_by_address_success', $location, $address, $data );
						} else {
							$error = sprintf(
								'Error while getting Google Map Location by address "%s" %s',
								$address,
								print_r( $data, true ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
							);
							yith_wcbk_add_log( $error, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GOOGLE_MAPS );
						}
					}
				}
			}

			return apply_filters( 'yith_wcbk_maps_get_location_by_address', $location, $address );
		}

		/**
		 * Get data by address
		 *
		 * @param string $address The address.
		 *
		 * @return array|mixed|object
		 */
		public function get_data_by_address( $address = '' ) {
			$data = array();
			if ( $address ) {
				$address = str_replace( ' ', '+', $address );

				$place_detail_args = array(
					'address' => $address,
				);

				if ( $this->geocode_api_key ) {
					$place_detail_args['key'] = $this->geocode_api_key;
				}

				$place_detail_url = add_query_arg( $place_detail_args, self::GEOCODE_URL );

				$json    = wp_remote_fopen( $place_detail_url );
				$decoded = ! ! $json ? json_decode( $json, true ) : false;

				if ( $decoded ) {
					$data = $decoded;
				}
			}

			return $data;
		}

		/**
		 * Calculate distance between two coordinates
		 *
		 * @param array $c1 First Coordinate.
		 * @param array $c2 Second Coordinate.
		 *
		 * @return bool|int
		 */
		public function calculate_distance( $c1, $c2 ) {
			if ( isset( $c1['lat'] ) && isset( $c1['lng'] ) && isset( $c2['lat'] ) && isset( $c2['lng'] ) ) {
				$deglen = 110.25;
				$x      = $c1['lat'] - $c2['lat'];
				$y      = ( $c1['lng'] - $c2['lng'] ) * cos( $c2['lat'] );

				return $deglen * sqrt( $x * $x + $y * $y );
			}

			return false;
		}
	}
}
