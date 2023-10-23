<?php
/**
 * Base Exchange provider.
 *
 * @since 2.1.0
 */

namespace KoiLab\WC_Currency_Converter\Exchange\Providers;

defined( 'ABSPATH' ) || exit;

use KoiLab\WC_Currency_Converter\Logger;
use WP_Error;

/**
 * Base Exchange Provider class.
 */
abstract class Base_Exchange_Provider {

	/**
	 * Provider ID.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Provider name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Provider privacy URL.
	 *
	 * @var string
	 */
	protected $privacy_url = '';

	/**
	 * Gets the provider ID.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Gets the provider name.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Gets the provider privacy URL.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_privacy_url(): string {
		return $this->privacy_url;
	}

	/**
	 * Validates the given API credentials.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function validate_credentials(): bool {
		return true;
	}

	/**
	 * Gets the rates refresh period in hours.
	 *
	 * @since 2.1.0
	 *
	 * @return int
	 */
	public function get_refresh_period(): int {
		return 12;
	}

	/**
	 * Triggers a remote request and processes the response.
	 *
	 * @since 2.1.0
	 *
	 * @see wp_remote_request()
	 *
	 * @param string $url  The request URL.
	 * @param array  $args The request arguments.
	 * @return mixed The request response. WP_Error on failure.
	 */
	protected function trigger_request( string $url, array $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'method'      => 'GET',
				'httpversion' => '1.1',
				'sslverify'   => false,
				'timeout'     => 60,
				'headers'     => array(),
				'body'        => array(),
			)
		);

		/**
		 * Filters the arguments of the remote request triggered by the exchange provider.
		 *
		 * The dynamic portion of the hook name, $this->id, refers to the provider ID.
		 *
		 * @since 2.1.0
		 *
		 * @param array  $args The request arguments.
		 * @param string $url  The request URL.
		 */
		$args = apply_filters( "wc_currency_converter_{$this->id}_api_request_args", $args, $url );

		// Uppercase the HTTP method.
		$args['method'] = strtoupper( $args['method'] );

		// Clean falsy values.
		$body = array_filter( $args['body'] );

		// Add the body params to the URL.
		if ( 'GET' === $args['method'] && ! empty( $body ) ) {
			$url  = add_query_arg( $body, $url );
			$body = array();
		}

		$args['body'] = ( ! empty( $body ) ? wp_json_encode( $body ) : null );

		$response = wp_remote_request( esc_url_raw( $url ), $args );

		// Request error.
		if ( is_wp_error( $response ) ) {
			return self::log_error( $response );
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$data        = json_decode( wp_remote_retrieve_body( $response ), true );

		// Invalid API request.
		if ( 200 > $status_code || $status_code >= 300 ) {
			if ( isset( $data['message'], $data['description'] ) ) {
				$error = new WP_Error( $data['message'], $data['description'], $response );
			} else {
				$error = new WP_Error( 'invalid_request', 'Invalid API request.', $response );
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
	 * @since 2.1.0
	 *
	 * @param WP_Error $error The error to log.
	 * @return WP_Error
	 */
	protected static function log_error( WP_Error $error ): WP_Error {
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
