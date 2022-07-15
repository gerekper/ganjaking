<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendlayer;

use WP_Error;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractSubscriber;
use WPMailSMTP\WP;

/**
 * Class Subscriber.
 *
 * @since 3.4.0
 */
class Subscriber extends AbstractSubscriber {

	/**
	 * Subscription events.
	 *
	 * @since 3.4.0
	 *
	 * @var array
	 */
	const EVENTS = [
		'delivered',
		'bounced',
	];

	/**
	 * Create webhook subscription.
	 *
	 * @since 3.4.0
	 *
	 * @return true|WP_Error
	 */
	public function subscribe() {

		foreach ( self::EVENTS as $event ) {
			$subscription = $this->get_subscription( $event );

			if ( is_wp_error( $subscription ) ) {
				return $subscription;
			}

			// Already subscribed.
			if ( $subscription !== false ) {
				continue;
			}

			$body = [
				'Event'      => $event,
				'WebhookURL' => $this->provider->get_url(),
			];

			// Create subscription.
			$response = $this->request( 'POST', $body );

			if ( is_wp_error( $response ) ) {
				return $response;
			}
		}

		return true;
	}

	/**
	 * Remove webhook subscription.
	 *
	 * @since 3.4.0
	 *
	 * @return true|WP_Error
	 */
	public function unsubscribe() {

		foreach ( self::EVENTS as $event ) {
			$subscription = $this->get_subscription( $event );

			if ( is_wp_error( $subscription ) ) {
				return $subscription;
			}

			// Already unsubscribed.
			if ( $subscription === false ) {
				continue;
			}

			$response = $this->request( 'DELETE', [], $subscription['WebhookID'] );

			if ( is_wp_error( $response ) ) {
				return $response;
			}
		}

		return true;
	}

	/**
	 * Check webhook subscription.
	 *
	 * @since 3.4.0
	 *
	 * @return bool|WP_Error
	 */
	public function is_subscribed() {

		foreach ( self::EVENTS as $event ) {
			$subscription = $this->get_subscription( $event );

			if ( is_wp_error( $subscription ) ) {
				return $subscription;
			}

			if ( $subscription === false ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get subscription if available.
	 *
	 * @since 3.4.0
	 *
	 * @param static $event Event name.
	 *
	 * @return array|false|WP_Error
	 */
	protected function get_subscription( $event ) {

		$response = $this->request();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$webhooks = array_filter(
			$response['Webhooks'],
			function ( $data ) use ( $event ) {
				return $data['WebhookURL'] === $this->provider->get_url() && $data['Event'] === $event;
			}
		);

		return ! empty( $webhooks ) ? array_values( $webhooks )[0] : false;
	}

	/**
	 * Performs SendLayer webhooks API HTTP request.
	 *
	 * @since 3.4.0
	 *
	 * @param string $method     Request method.
	 * @param array  $params     Request params.
	 * @param array  $webhook_id SendLayer webhooks ID.
	 *
	 * @return mixed|WP_Error
	 */
	protected function request( $method = 'GET', $params = [], $webhook_id = false ) {

		$endpoint = 'https://console.sendlayer.com/api/v1/webhooks';

		$args = [
			'method'  => $method,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->provider->get_option( 'api_key' ),
				'Content-Type'  => 'application/json',
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
	 * Retrieve errors from SendLayer API response.
	 *
	 * @since 3.4.0
	 *
	 * @param array $response Response array.
	 *
	 * @return WP_Error
	 */
	protected function get_response_error( $response ) {

		$body       = json_decode( wp_remote_retrieve_body( $response ) );
		$error_text = [];

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( ! empty( $body->Errors ) && is_array( $body->Errors ) ) {
			foreach ( $body->Errors as $error ) {
				if ( ! empty( $error->Message ) ) {
					$message = $error->Message;
					$code    = ! empty( $error->Code ) ? $error->Code : '';

					$error_text[] = Helpers::format_error_message( $message, $code );
				}
			}
		} else {
			$error_text[] = WP::wp_remote_get_response_error_message( $response );
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return new WP_Error( wp_remote_retrieve_response_code( $response ), implode( WP::EOL, $error_text ) );
	}
}
