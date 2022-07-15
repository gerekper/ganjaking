<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Postmark;

use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractSubscriber;
use WP_Error;
use WPMailSMTP\WP;

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
		'Delivery',
		'Bounce',
	];

	/**
	 * Create webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|WP_Error
	 */
	public function subscribe() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$subscription = $this->get_subscription();

		if ( is_wp_error( $subscription ) ) {
			return $subscription;
		}

		if ( $subscription !== false ) {
			$enabled_events = array_keys(
				array_filter(
					$subscription['Triggers'],
					function ( $event ) {
						return $event['Enabled'] === true;
					}
				)
			);

			// Already subscribed.
			if ( empty( array_diff( self::EVENTS, $enabled_events ) ) ) {
				return true;
			}
		}

		$triggers = [];

		foreach ( self::EVENTS as $event ) {
			$triggers[ $event ] = [
				'Enabled' => true,
			];
		}

		$body = [
			'Url'      => $this->provider->get_url(),
			'Triggers' => $triggers,
		];

		if ( ! empty( $this->provider->get_message_stream() ) ) {
			$body['MessageStream'] = $this->provider->get_message_stream();
		}

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
	 * @return true|WP_Error
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
		$response = $this->request( 'DELETE', [], $subscription['ID'] );

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
	 * @return bool|WP_Error
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

		foreach ( self::EVENTS as $event ) {
			if ( $subscription['Triggers'][ $event ]['Enabled'] !== true ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get subscription if available.
	 *
	 * @since 3.3.0
	 *
	 * @return array|false|WP_Error
	 */
	protected function get_subscription() {

		$response = $this->request();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$provider       = $this->provider;
		$message_stream = ! empty( $provider->get_message_stream() ) ? $provider->get_message_stream() : 'outbound';

		$webhooks = array_filter(
			$response['Webhooks'],
			function ( $data ) use ( $message_stream ) {
				return $data['Url'] === $this->provider->get_url() && $data['MessageStream'] === $message_stream;
			}
		);

		return ! empty( $webhooks ) ? array_values( $webhooks )[0] : false;
	}

	/**
	 * Performs Postmark webhooks API HTTP request.
	 *
	 * @since 3.3.0
	 *
	 * @param string $method     Request method.
	 * @param array  $params     Request params.
	 * @param array  $webhook_id Postmark webhooks ID.
	 *
	 * @return mixed|WP_Error
	 */
	protected function request( $method = 'GET', $params = [], $webhook_id = false ) {

		$endpoint = 'https://api.postmarkapp.com/webhooks';

		$args = [
			'method'  => $method,
			'headers' => [
				'X-Postmark-Server-Token' => $this->provider->get_option( 'server_api_token' ),
				'Content-Type'            => 'application/json',
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

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return $this->get_response_error( $response );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Retrieve errors from Postmark API response.
	 *
	 * @since 3.3.0
	 *
	 * @param array $response Response array.
	 *
	 * @return WP_Error
	 */
	protected function get_response_error( $response ) {

		$body = json_decode( wp_remote_retrieve_body( $response ) );

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( ! empty( $body->Message ) ) {
			$message = $body->Message;
			$code    = ! empty( $body->ErrorCode ) ? $body->ErrorCode : '';

			$error_text = Helpers::format_error_message( $message, $code );
		} else {
			$error_text = WP::wp_remote_get_response_error_message( $response );
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return new WP_Error( wp_remote_retrieve_response_code( $response ), $error_text );
	}
}
