<?php
/**
 * MaxMind Geolocation
 *
 * @package UAEL
 */

namespace UltimateElementor\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use UltimateElementor\Classes\UAEL_Maxmind_Database;
use UltimateElementor\Classes\UAEL_Geolite_Integration;

/**
 * Class UAEL_Geolocation
 */
class UAEL_Geolocation {
	/**
	 * GeoLite IPv4 DB.
	 *
	 * @since 1.35.1
	 */
	const GEOLITE_DB = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';

	/**
	 * GeoLite IPv6 DB.
	 *
	 * @since 1.35.1
	 */
	const GEOLITE_IPV6_DB = 'http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz';

	/**
	 * GeoLite2 DB.
	 *
	 * @since 1.35.1
	 */
	const GEOLITE2_DB = 'http://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.tar.gz';

	/**
	 * API endpoints for looking up user IP address.
	 *
	 * @var array
	 * @since 1.35.1
	 */
	private $ip_lookup_apis = array(
		'icanhazip'         => 'http://icanhazip.com',
		'ipify'             => 'http://api.ipify.org/',
		'ipecho'            => 'http://ipecho.net/plain',
		'ident'             => 'http://ident.me',
		'whatismyipaddress' => 'http://bot.whatismyipaddress.com',
	);

	/**
	 * API endpoints for geolocating an IP address
	 *
	 * @var array
	 * @since 1.35.1
	 */
	private $geoip_apis = array(
		'ipinfo.io'  => 'https://ipinfo.io/%s/json',
		'ip-api.com' => 'http://ip-api.com/json/%s',
	);

	/**
	 * Instance of UAEL_Maxmind_Database class.
	 *
	 * @var UAEL_Maxmind_Database
	 *
	 * @since 1.35.1
	 */
	private $geolite_db;

	/**
	 * Check if server supports MaxMind GeoLite2 Reader.
	 *
	 * @since 1.35.1
	 * @return bool
	 */
	private function supports_geolite2() {
		return version_compare( PHP_VERSION, '5.4.0', '>=' );
	}

	/**
	 * Hook in geolocation functionality.
	 */
	public function __construct() {
		if ( $this->supports_geolite2() ) {

			// Check Geolite2 option present and license key present or not.
			$this->get_geolite2_option();
		}

		$this->geolite_db = new UAEL_Maxmind_Database();
	}

	/**
	 * Get current user IP Address.
	 *
	 * @return string
	 * @since 1.35.1
	 */
	public function get_ip_address() {
		$get_ip_address = '';
		if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
			$get_ip_address = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) ); // WPCS: input var ok, CSRF ok.
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // phpcs:ignore WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders
			// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
			// Make sure we always only send through the first IP in the list which should always be the client IP.
			$get_ip_address = (string) rest_is_ip_address( trim( current( preg_split( '/[,:]/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // phpcs:ignore WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) { // phpcs:ignore WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders
			$get_ip_address = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ); // phpcs:ignore WordPressVIPMinimum.Variables.ServerVariables.UserControlledHeaders, WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___SERVER__REMOTE_ADDR__
		}
		return $get_ip_address;
	}

	/**
	 * Get user IP Address using an external service.
	 * This is used mainly as a fallback for users on localhost where
	 * get_ip_address() will be a local IP and non-geolocatable.
	 *
	 * @return string
	 * @since 1.35.1
	 */
	public function get_external_ip_address() {
		$external_ip_address = '0.0.0.0';

		if ( '' !== $this->get_ip_address() ) {
			$transient_name      = 'external_ip_address_' . $this->get_ip_address();
			$external_ip_address = get_transient( $transient_name );
		}

		if ( false === $external_ip_address ) {
			$external_ip_address     = '0.0.0.0';
			$ip_lookup_services      = $this->ip_lookup_apis;
			$ip_lookup_services_keys = array_keys( $ip_lookup_services );
			shuffle( $ip_lookup_services_keys );

			foreach ( $ip_lookup_services_keys as $service_name ) {
				$service_endpoint = $ip_lookup_services[ $service_name ];
				$response         = wp_safe_remote_get( $service_endpoint, array( 'timeout' => 2 ) );

				if ( ! is_wp_error( $response ) && rest_is_ip_address( $response['body'] ) ) {
					$external_ip_address = sanitize_text_field( $response['body'] );
					break;
				}
			}

			set_transient( $transient_name, $external_ip_address, WEEK_IN_SECONDS );
		}

		return $external_ip_address;
	}

	/**
	 * Geolocate an IP address.
	 *
	 * @param  string $ip_address   IP Address.
	 * @param  bool   $fallback     If true, fallbacks to alternative IP detection (can be slower).
	 * @param  bool   $api_fallback If true, uses geolocation APIs if the database file doesn't exist (can be slower).
	 * @return array
	 * @since 1.35.1
	 */
	public function geolocate_ip( $ip_address = '', $fallback = true, $api_fallback = true ) {
		$country_code = false;
		if ( false === $country_code ) {
			// If GEOIP is enabled in CloudFlare, we can use that (Settings -> CloudFlare Settings -> Settings Overview).
			if ( ! empty( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) { // WPCS: input var ok, CSRF ok.
				$country_code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_IPCOUNTRY'] ) ) ); // WPCS: input var ok, CSRF ok.
			} elseif ( ! empty( $_SERVER['GEOIP_COUNTRY_CODE'] ) ) { // WPCS: input var ok, CSRF ok.
				// WP.com VIP has a variable available.
				$country_code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['GEOIP_COUNTRY_CODE'] ) ) ); // WPCS: input var ok, CSRF ok.
			} elseif ( ! empty( $_SERVER['HTTP_X_COUNTRY_CODE'] ) ) { // WPCS: input var ok, CSRF ok.
				// VIP Go has a variable available also.
				$country_code = strtoupper( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_COUNTRY_CODE'] ) ) ); // WPCS: input var ok, CSRF ok.
			} else {

				$ip_address = $ip_address ? $ip_address : $this->get_ip_address();
				$database   = $this->geolite_db->get_uael_database_path();

				if ( $this->supports_geolite2() && file_exists( $database ) ) {
					$country_code = $this->geolocate_via_db( $ip_address, $database );
				} elseif ( $api_fallback ) {
					$country_code = $this->geolocate_via_api( $ip_address );
				} else {
					$country_code = '';
				}

				if ( ! $country_code && $fallback ) {
					// May be a local environment - find external IP.
					return $this->geolocate_ip( $this->get_external_ip_address(), false, $api_fallback );
				}
			}
		}

		return array(
			'country' => $country_code,
			'state'   => '',
		);
	}


	/**
	 * Get the Geolite2 option and license key.
	 *
	 * @since 1.35.1
	 */
	public function get_geolite2_option() {
		$uae_maxmind_option = UAEL_Helper::get_integrations_options();
		if ( ! isset( $uae_maxmind_option['uael_maxmind_geolocation_license_key'] ) || '' === $uae_maxmind_option['uael_maxmind_geolocation_license_key'] ) {
			return __( 'The MaxMind Geolocation Integration - License key is not activated in UAE. Find Settings under Settings-> UAE -> Display Conditions -> Settings -> MaxMind Geolocation', 'uael' );
		}
	}

	/**
	 * Use MAXMIND GeoLite database to geolocation the user.
	 *
	 * @param  string $ip_address IP address.
	 * @param  string $database   Database path.
	 * @return string
	 * @since 1.35.1
	 */
	private function geolocate_via_db( $ip_address, $database ) {

		$geolite = new UAEL_Geolite_Integration( $database );

		return $geolite->get_country_iso( $ip_address );
	}


	/**
	 * Use APIs to Geolocate the user.
	 * If APIs are defined, one will be chosen at random to fulfil the request. After completing, the result
	 * will be cached in a transient.
	 *
	 * @param  string $ip_address IP address.
	 * @return string
	 * @since 1.35.1
	 */
	private function geolocate_via_api( $ip_address ) {
		$country_code = get_transient( 'geoip_' . $ip_address );

		if ( false === $country_code ) {
			$geoip_services = $this->geoip_apis;

			if ( empty( $geoip_services ) ) {
				return '';
			}

			$geoip_services_keys = array_keys( $geoip_services );

			shuffle( $geoip_services_keys );

			foreach ( $geoip_services_keys as $service_name ) {
				$service_endpoint = $geoip_services[ $service_name ];
				$response         = wp_safe_remote_get( sprintf( $service_endpoint, $ip_address ), array( 'timeout' => 2 ) );

				if ( ! is_wp_error( $response ) && $response['body'] ) {
					switch ( $service_name ) {
						case 'ipinfo.io':
							$data         = json_decode( $response['body'] );
							$country_code = isset( $data->country ) ? $data->country : '';
							break;
						case 'ip-api.com':
							$data         = json_decode( $response['body'] );
							$country_code = isset( $data->countryCode ) ? $data->countryCode : ''; // @codingStandardsIgnoreLine
							break;
						default:
							$country_code = '';
							break;
					}

					$country_code = sanitize_text_field( strtoupper( $country_code ) );

					if ( $country_code ) {
						break;
					}
				}
			}

			set_transient( 'geoip_' . $ip_address, $country_code, WEEK_IN_SECONDS );
		}

		return $country_code;
	}
}
new UAEL_Geolocation();
