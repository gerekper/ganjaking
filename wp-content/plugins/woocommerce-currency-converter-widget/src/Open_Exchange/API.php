<?php
/**
 * Open Exchange Rates API.
 *
 * @since 2.0.0
 */

namespace Themesquad\WC_Currency_Converter\Open_Exchange;

defined( 'ABSPATH' ) || exit;

use Themesquad\WC_Currency_Converter\Logger;
use WP_Error;

/**
 * Open Exchange Rates API class.
 */
class API {

	/**
	 * Base URL to make HTTP requests.
	 *
	 * @var string
	 */
	protected $base_url = 'https://openexchangerates.org/api/';

	/**
	 * App ID.
	 *
	 * @var string
	 */
	protected $app_id;

	/**
	 * The constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $app_id APP ID.
	 */
	public function __construct( $app_id ) {
		$this->app_id = $app_id;
	}

	/**
	 * Validates the given API credentials.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function validate_credentials() {
		$response = $this->get_latest();

		return ( ! is_wp_error( $response ) );
	}

	/**
	 * Gets the latest rates.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args Optional. The request arguments. Default empty.
	 * @return array|WP_Error The request response. WP_Error on failure.
	 */
	public function get_latest( $args = array() ) {
		return $this->request( 'latest.json', $args );
	}

	/**
	 * Makes a request to the specified endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @param string $endpoint The API endpoint.
	 * @param array  $args     Optional. The request arguments. Default empty.
	 * @return array|WP_Error The request response. WP_Error on failure.
	 */
	protected function request( $endpoint, $args = array() ) {
		// Clean arguments with a falsy value.
		$args = array_filter( $args );
		$args = array_merge( array( 'app_id' => $this->app_id ), $args );

		$url = $this->base_url . wp_unslash( $endpoint );
		$url = add_query_arg( $args, $url );

		$response = wp_safe_remote_get(
			$url,
			array(
				'method'  => 'GET',
				'headers' => array(
					'Content-Type' => 'application/json',
				),
			)
		);

		// Request error.
		if ( is_wp_error( $response ) ) {
			return self::log_error( $response );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		// Invalid API request.
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( isset( $data['message'], $data['description'] ) ) {
				$error = new WP_Error( $data['message'], $data['description'], $response );
			} else {
				$error = new WP_Error( 'invalid_request', 'Method Not Allowed', $response );
			}

			return self::log_error( $error );
		}

		return $data;
	}

	/**
	 * Logs an API error.
	 *
	 * Logs the error and return a `WP_Error` object.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Error $error The error to log.
	 * @return WP_Error
	 */
	protected static function log_error( $error ) {
		$error_data = $error->get_error_data();

		$message = sprintf(
			'[API Error] %1$s %2$s %3$s',
			$error->get_error_code(),
			$error->get_error_message(),
			( is_array( $error_data ) ? wc_print_r( $error_data, true ) : '' )
		);

		Logger::error( $message, 'api' );

		return $error;
	}
}
