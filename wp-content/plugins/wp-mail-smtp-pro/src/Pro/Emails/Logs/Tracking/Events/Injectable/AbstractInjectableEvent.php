<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable;

use WPMailSMTP\Helpers\Crypto;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\AbstractEvent;
use WP_REST_Request;

/**
 * Email tracking injectable event class.
 * Injectable events can embed into email's content.
 *
 * @since 2.9.0
 */
abstract class AbstractInjectableEvent extends AbstractEvent {

	/**
	 * Tracking REST Request.
	 *
	 * @since 2.9.0
	 *
	 * @var WP_REST_Request
	 */
	protected $request = null;

	/**
	 * Inject tracking code to email content.
	 *
	 * @since 2.9.0
	 *
	 * @param string $email_content Email content.
	 *
	 * @return string Email content with injected tracking code.
	 */
	abstract public function inject( $email_content );

	/**
	 * Get response based on event implementation.
	 *
	 * @since 2.9.0
	 *
	 * @param array $event_data Event data from request.
	 *
	 * @return mixed REST or custom response.
	 */
	abstract public function get_response( $event_data );

	/**
	 * Generate tracking url based on event data.
	 *
	 * @since 2.9.0
	 *
	 * @param array $extra_data Additional event data.
	 *
	 * @return string Tracking url.
	 */
	public function get_tracking_url( $extra_data = [] ) {

		$data = $this->get_tracking_url_data( $extra_data );
		$hash = $this->generate_signature( $data );

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$encoded_data = base64_encode(
			http_build_query(
				[
					'data' => $data,
					'hash' => $hash,
				]
			)
		);

		return get_rest_url( null, 'wp-mail-smtp/v1/e/' . $encoded_data );
	}

	/**
	 * Get tracking url event data.
	 *
	 * @since 2.9.0
	 *
	 * @param array $extra_data Additional event data.
	 *
	 * @return array Event data.
	 */
	protected function get_tracking_url_data( $extra_data = [] ) {

		return array_merge(
			[
				'email_log_id' => $this->get_email_log_id(),
				'event_type'   => $this->get_type(),
			],
			$extra_data
		);
	}

	/**
	 * Verify HMAC hash from request.
	 *
	 * @since 2.9.0
	 *
	 * @param array  $event_data Event data from request.
	 * @param string $hash       Hash from request.
	 *
	 * @return bool
	 */
	public function verify_signature( $event_data, $hash ) {

		return hash_equals( $this->generate_signature( $event_data ), $hash );
	}

	/**
	 * Generate HMAC hash based on event data.
	 *
	 * @since 2.9.0
	 *
	 * @param array $event_data Event data.
	 *
	 * @return string
	 */
	protected function generate_signature( $event_data ) {

		static $secret_key = null;

		if ( is_null( $secret_key ) ) {
			$secret_key = Crypto::get_secret_key( true );
		}

		return hash_hmac( 'sha256', implode( '-', $event_data ), $secret_key, false );
	}

	/**
	 * Set tracking REST Request.
	 *
	 * @since 2.9.0
	 *
	 * @param WP_REST_Request $request Tracking REST Request.
	 */
	public function set_request( $request ) {

		$this->request = $request;
	}

	/**
	 * Get tracking REST Request.
	 *
	 * @since 2.9.0
	 *
	 * @return WP_REST_Request
	 */
	public function get_request() {

		return $this->request;
	}
}
