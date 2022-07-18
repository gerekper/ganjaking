<?php
/**
 * Provider with API key credentials.
 *
 * @package WC_Newsletter_Subscription/Traits
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Newsletter_Subscription_Provider_API_Key.
 */
trait WC_Newsletter_Subscription_Provider_API_Key {

	/**
	 * Sets the provider credentials.
	 *
	 * @since 3.0.0
	 *
	 * @param array $credentials An array with the provider credentials.
	 */
	public function set_credentials( $credentials ) {
		$credentials = wp_parse_args(
			$credentials,
			array(
				'api_key' => '',
			)
		);

		parent::set_credentials( $credentials );
	}

	/**
	 * Gets the provider API key.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_api_key() {
		$credentials = $this->get_credentials();

		return $credentials['api_key'];
	}

	/**
	 * Gets if the provider is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$api_key = $this->get_api_key();

		return ( ! empty( $api_key ) );
	}

	/**
	 * Triggers a remote request and processes the response.
	 *
	 * @since 3.0.0
	 *
	 * @see wp_remote_request()
	 *
	 * @param string $url  The request URL.
	 * @param array  $args The request arguments.
	 * @return mixed|WP_Error
	 */
	protected function trigger_request( $url, $args ) {
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
		 * Filters the arguments of the remote request triggered by the newsletter provider.
		 *
		 * The dynamic portion of the hook name, $this->id, refers to the provider ID.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $args The request arguments.
		 * @param string $url  The request URL.
		 */
		$args = apply_filters( "wc_newsletter_subscription_{$this->id}_api_request_args", $args, $url );

		// Uppercase the HTTP method.
		$args['method'] = strtoupper( $args['method'] );

		// Clean falsy values.
		$body = array_filter( $args['body'] );

		// Backward compatibility with WP 4.5 and lower.
		if ( 'GET' === $args['method'] && ! empty( $body ) ) {
			$url  = add_query_arg( $body, $url );
			$body = array();
		}

		$args['body'] = ( ! empty( $body ) ? wp_json_encode( $body ) : null );

		$response = wp_remote_request( esc_url_raw( $url ), $args );

		// Request error.
		if ( is_wp_error( $response ) ) {
			return wc_newsletter_subscription_log_error( $response );
		}

		$status_code = wp_remote_retrieve_response_code( $response );

		// Invalid API request.
		if ( 200 > $status_code || $status_code >= 300 ) {
			return wc_newsletter_subscription_log_error(
				new WP_Error( $this->id . '_invalid_request', 'Invalid API request.', $response )
			);
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
