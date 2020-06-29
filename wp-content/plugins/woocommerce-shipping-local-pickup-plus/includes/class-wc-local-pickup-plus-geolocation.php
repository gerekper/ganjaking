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
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Geolocation class.
 *
 * This class handles geolocating the customer.
 *
 * Based on WC_Geolocation with higher accuracy level.
 * @see \WC_Geolocation
 *
 * @since 2.1.1
 */
class WC_Local_Pickup_Plus_Geolocation {


	/** @var array API endpoints for looking up user IP address */
	private static $ip_lookup_apis = array(
		'icanhazip'         => 'http://icanhazip.com',
		'ipify'             => 'http://api.ipify.org/',
		'ipecho'            => 'http://ipecho.net/plain',
		'ident'             => 'http://ident.me',
		'whatismyipaddress' => 'http://bot.whatismyipaddress.com',
		'ip.appspot'        => 'http://ip.appspot.com',
	);

	/** @var array API endpoints for geolocating an IP address */
	private static $geoip_apis = array(
		'freegeoip'  => 'https://freegeoip.net/json/%s',
		'ipinfo.io'  => 'https://ipinfo.io/%s/json',
		'ip-api.com' => 'http://ip-api.com/json/%s',
	);


	/**
	 * Checks if the input is a valid IP address.
	 *
	 * @since 2.1.1
	 *
	 * @param string $ip_address IP address
	 * @return string|bool the valid IP address, otherwise false
	 */
	private static function is_ip_address( $ip_address ) {

		// WP 4.7+ only
		if ( function_exists( 'rest_is_ip_address' ) ) {
			return rest_is_ip_address( $ip_address );
		}

		// support for WordPress 4.4 to 4.6
		if ( ! class_exists( 'Requests_IPv6', false ) ) {
			include_once( plugin_dir_path( 'woocommerce/woocommerce.php' ) . '/includes/vendor/class-requests-ipv6.php' );
		}

		$ipv4_pattern = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';

		if ( ! preg_match( $ipv4_pattern, $ip_address ) && ! Requests_IPv6::check_ipv6( $ip_address ) ) {
			return false;
		}

		return $ip_address;
	}


	/**
	 * Returns visitors IP Address.
	 *
	 * @since 2.1.1
	 *
	 * @return string
	 */
	public static function get_ip_address() {

		$ip_address = '';

		if ( isset( $_SERVER['X-Real-IP'] ) ) {

			$ip_address = $_SERVER['X-Real-IP'];

		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

			// proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
			// make sure we always only send through the first IP in the list which should always be the client IP.
			$ip_address = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
			$ip_address = (string) self::is_ip_address( trim( current( $ip_address ) ) );

		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {

			$ip_address = $_SERVER['REMOTE_ADDR'];
		}

		return $ip_address;
	}


	/**
	 * Get user IP Address using an external service.
	 *
	 * This is used mainly as a fallback for users on localhost where `get_ip_address()` will be a local IP and can't be geo-located.
	 * @see \WC_Local_Pickup_Plus_Geolocation::get_ip_address()
	 *
	 * @return string
	 */
	public static function get_external_ip_address() {

		$external_ip_address = '0.0.0.0';

		if ( '' !== self::get_ip_address() ) {
			$transient_name      = 'external_ip_address_' . self::get_ip_address();
			$external_ip_address = get_transient( $transient_name );
		}

		if ( isset( $transient_name ) && false === $external_ip_address ) {

			/**
			 * Filters the available IP lookup services.
			 *
			 * @since 2.1.1
			 *
			 * @param array $services associative array of service id => endpoint urls
			 */
			$ip_lookup_services      = apply_filters( 'woocommerce_geolocation_ip_lookup_apis', self::$ip_lookup_apis );
			$external_ip_address     = '0.0.0.0';
			$ip_lookup_services_keys = array_keys( $ip_lookup_services );

			shuffle( $ip_lookup_services_keys );

			foreach ( $ip_lookup_services_keys as $service_name ) {

				$service_endpoint = $ip_lookup_services[ $service_name ];
				$response         = wp_safe_remote_get( $service_endpoint, array( 'timeout' => 2 ) );
				$response_body    = wp_remote_retrieve_body( $response );

				if ( '' !== $response_body ) {
					// this is a core WC filter
					$external_ip_address = apply_filters( 'woocommerce_geolocation_ip_lookup_api_response', wc_clean( $response_body ), $service_name );
					break;
				}
			}

			set_transient( $transient_name, $external_ip_address, WEEK_IN_SECONDS );
		}

		return $external_ip_address;
	}


	/**
	 * Geolocates an IP address
	 *
	 * @since 2.1.1
	 *
	 * @param string $ip_address
	 * @param bool $fallback if true, falls back to alternative IP detection (can be slower)
	 * @return array lat/lon coordinates
	 */
	public static function geolocate_ip( $ip_address = '', $fallback = true ) {

		/**
		 * Filters the geolocation IP results by means of short-circuiting.
		 *
		 * Allows third parties to provide custom coordinates before the geolocation requests are made.
		 *
		 * @since 2.1.1
		 *
		 * @param array|false $coordinates associative array with lat/lon coordinates or false if no coordinates are determined
		 * @param string $ip_address visitors ip address
		 * @param bool $fallback whether to allow alternative ip detection or not
		 */
		$coordinates = apply_filters( 'woocommerce_local_pickup_plus_geolocate_ip', false, $ip_address, $fallback );

		if ( false === $coordinates ) {

			// support for mode_geoip2
			if ( ! empty( $_SERVER['GEOIP_LATITUDE'] ) && ! empty( $_SERVER['GEOIP_LONGITUDE'] ) ) {

				$coordinates = array(
					'lat' => (float) $_SERVER['GEOIP_LATITUDE'],
					'lon' => (float) $_SERVER['GEOIP_LONGITUDE'],
				);

			} else {

				// TODO: consider adding support for https://github.com/maxmind/MaxMind-DB-Reader-php {IT 2017-07-06}
				$ip_address  = $ip_address ? $ip_address : self::get_ip_address();
				$coordinates = self::geolocate_via_api( $ip_address );

				// may be a local environment - find external IP
				if ( ! $coordinates && $ip_address !== self::get_external_ip_address() ) {
					$coordinates = self::geolocate_ip( self::get_external_ip_address(), false );
				}
			}
		}

		return $coordinates;
	}


	/**
	 * Geolocates the visitor via public APIs.
	 *
	 * @since 2.1.1
	 *
	 * @param string $ip_address
	 * @return array|bool
	 */
	private static function geolocate_via_api( $ip_address ) {

		$coordinates = get_transient( "geoip_coordinates_{$ip_address}" );

		if ( false === $coordinates ) {

			/**
			 * Filters the available geoIP services.
			 *
			 * @since 2.1.1
			 *
			 * @param array $services associative array of service id => endpoint urls
			 */
			$geoip_services      = apply_filters( 'woocommerce_local_pickup_plus_geolocation_geoip_apis', self::$geoip_apis );
			$geoip_services_keys = array_keys( $geoip_services );

			shuffle( $geoip_services_keys );

			foreach ( $geoip_services_keys as $service_name ) {

				$service_endpoint = $geoip_services[ $service_name ];
				$response         = wp_safe_remote_get( sprintf( $service_endpoint, $ip_address ), array( 'timeout' => 2 ) );
				$response_body    = wp_remote_retrieve_body( $response );

				if ( '' !== $response_body ) {

					// try to get raw coordinates
					switch ( $service_name ) {

						case 'ipinfo.io':

							$data = json_decode( $response_body );

							if ( isset( $data->loc ) ) {
								$pieces      = explode( ',', $data->loc );
								$coordinates = isset( $pieces[0], $pieces[1] ) ? array( 'lat' => $pieces[0], 'lon' => $pieces[1] ) : false;
							}

						break;

						case 'ip-api.com':
							$data        = json_decode( $response_body );
							$coordinates = isset( $data->lat, $data->lon ) ? array( 'lat' => $data->lat, 'lon' => $data->lon ) : false;
						break;

						case 'freegeoip':
							$data         = json_decode( $response_body );
							$coordinates = isset( $data->latitude, $data->longitude ) ? array( 'lat' => $data->latitude, 'lon' => $data->longitude ) : false;
						break;
					}

					/**
					 * Filters the geolocation IP results from a geoIP provider.
					 *
					 * @since 2.1.1
					 *
					 * @param array|false $coordinates associative array with two keys: lat and lon, both should have float values, or false if no coordinates could be determined
					 * @param string $response response body from API request
					 */
					$coordinates = apply_filters( "woocommerce_local_pickup_plus_geolocation_geoip_results_{$service_name}", $coordinates, $response_body );

					// if found, cast coordinates to floats and break the loop
					if ( is_array( $coordinates ) && ! empty( $coordinates['lat'] ) && ! empty( $coordinates['lon'] ) ) {
						$coordinates = array( 'lat' => (float) $coordinates['lat'], 'lon' => (float) $coordinates['lon'] );
						break;
					} else {
						$coordinates = false;
					}
				}
			}

			set_transient( "geoip_coordinates_{$ip_address}", $coordinates, WEEK_IN_SECONDS );
		}

		return $coordinates;
	}


}
