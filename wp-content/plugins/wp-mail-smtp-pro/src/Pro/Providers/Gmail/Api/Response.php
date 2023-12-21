<?php

namespace WPMailSMTP\Pro\Providers\Gmail\Api;

use WP_Error;

/**
 * API Response class.
 *
 * @since 3.11.0
 */
class Response {

	/**
	 * Response returned by `wp_remote_request` function.
	 *
	 * @since 3.11.0
	 *
	 * @var array|WP_Error
	 */
	private $response;

	/**
	 * Constructor.
	 *
	 * @since 3.11.0
	 *
	 * @param array|WP_Error $response Response array or error.
	 */
	public function __construct( $response ) {

		$this->response = $response;
	}

	/**
	 * Get response body.
	 *
	 * @since 3.11.0
	 *
	 * @return string|array
	 */
	public function get_body() {

		$body = wp_remote_retrieve_body( $this->response );

		if ( ! empty( $body ) && is_string( $body ) ) {
			$body = json_decode( $body, true );
		}

		return $body;
	}

	/**
	 * Get response status code.
	 *
	 * @since 3.11.0
	 *
	 * @return int
	 */
	public function get_status_code() {

		return wp_remote_retrieve_response_code( $this->response );
	}

	/**
	 * Get response errors.
	 *
	 * @since 3.11.0
	 *
	 * @return string|WP_Error
	 */
	public function get_errors() {

		if ( ! $this->has_errors() ) {
			return '';
		}

		if ( is_wp_error( $this->response ) ) {
			return $this->response;
		}

		$body = $this->get_body();

		if ( ! empty( $body['message'] ) ) {
			return new WP_Error( $this->get_status_code(), $body['message'] );
		}

		return new WP_Error( $this->get_status_code(), esc_html__( 'The API was unreachable.', 'wp-mail-smtp-pro' ) );
	}

	/**
	 * Whether response has errors.
	 *
	 * @since 3.11.0
	 *
	 * @return bool
	 */
	public function has_errors() {

		return $this->get_status_code() !== 200;
	}

	/**
	 * Get response header.
	 *
	 * @since 3.11.0
	 *
	 * @param string $header Header name.
	 *
	 * @return string
	 */
	public function get_header( $header ) {

		return wp_remote_retrieve_header( $this->response, $header );
	}
}
