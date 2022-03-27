<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendinblue;

use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractSubscriber;

/**
 * Class Subscriber.
 *
 * @since 3.3.0
 */
class Subscriber extends AbstractSubscriber {

	/**
	 * Subscription events.
	 *
	 * @since 3.3.0
	 *
	 * @var array
	 */
	const EVENTS = [
		'delivered',
		'hardBounce',
		'blocked',
		'invalid'
	];

	/**
	 * Create webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function subscribe() {

		$subscription = $this->get_subscription();

		if ( is_wp_error( $subscription ) ) {
			return $subscription;
		}

		// Already subscribed.
		if ( $subscription !== false && empty( array_diff( self::EVENTS, $subscription['events'] ) ) ) {
			return true;
		}

		$events = $subscription !== false ? $subscription['events'] : [];
		$events = array_unique( array_merge( $events, self::EVENTS ) );

		$body = [
			'url'         => $this->provider->get_url(),
			'events'      => $events,
			'description' => esc_html__( 'WP Mail SMTP', 'wp-mail-smtp' ),
		];

		// Create subscription.
		$response = $this->request( 'POST', $body );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	/**
	 * Remove webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function unsubscribe() {

		$subscription = $this->get_subscription();

		if ( is_wp_error( $subscription ) ) {
			return $subscription;
		}

		// Already unsubscribed.
		if ( $subscription === false ) {
			return true;
		}

		// Delete subscription.
		$response = $this->request( 'DELETE', [], $subscription['id'] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return true;
	}

	/**
	 * Check webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return bool|\WP_Error
	 */
	public function is_subscribed() {

		$subscription = $this->get_subscription();

		if ( is_wp_error( $subscription ) ) {
			return $subscription;
		}

		// Subscription does not exist.
		if ( $subscription === false ) {
			return false;
		}

		return empty( array_diff( self::EVENTS, $subscription['events'] ) );
	}

	/**
	 * Get subscription if available.
	 *
	 * @since 3.3.0
	 *
	 * @return array|false|\WP_Error
	 */
	protected function get_subscription() {

		$response = $this->request();

		// Not found any subscriptions.
		if ( is_wp_error( $response ) && $response->get_error_code() === 404 ) {
			return false;
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$webhooks = array_filter(
			$response['webhooks'],
			function ( $data ) {
				return $data['url'] === $this->provider->get_url();
			}
		);

		return ! empty( $webhooks ) ? array_values( $webhooks )[0] : false;
	}

	/**
	 * Performs Sendinblue webhooks API HTTP request.
	 *
	 * @since 3.3.0
	 *
	 * @param string $method     Request method.
	 * @param array  $params     Request params.
	 * @param array  $webhook_id Sendinblue webhooks ID.
	 *
	 * @return mixed|\WP_Error
	 */
	protected function request( $method = 'GET', $params = [], $webhook_id = false ) {

		$endpoint = 'https://api.sendinblue.com/v3/webhooks';

		$args = [
			'method'  => $method,
			'headers' => [
				'api-key'      => $this->provider->get_option( 'api_key' ),
				'content-type' => 'application/json',
			],
		];

		if ( $method === 'GET' ) {
			$endpoint = add_query_arg( $params, $endpoint );
		} else {
			$args['body'] = wp_json_encode( $params );
		}

		if ( $webhook_id !== false ) {
			$endpoint .= '/' . $webhook_id;
		}

		$response = wp_remote_request( $endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! in_array( wp_remote_retrieve_response_code( $response ), [ 200, 201, 202, 204 ], true ) ) {
			return $this->get_response_error( $response );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Retrieve errors from Sendinblue API response.
	 *
	 * @since 3.3.0
	 *
	 * @param array $response Response array.
	 *
	 * @return \WP_Error
	 */
	protected function get_response_error( $response ) {

		$error         = new \WP_Error();
		$body          = json_decode( wp_remote_retrieve_body( $response ), true );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( ! empty( $body ) && ( isset( $body['message'] ) || isset( $body['code'] ) ) ) {
			$error_text = [];

			if ( ! empty( $body['code'] ) ) {
				$error_text[] = $body['code'];
			}

			if ( ! empty( $body['message'] ) ) {
				$error_text[] = $body['message'];
			}

			$error->add( $response_code, implode( ' - ', $error_text ) );
		} else {
			$error->add( $response_code, wp_remote_retrieve_response_message( $response ) );
		}

		return $error;
	}
}
