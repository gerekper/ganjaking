<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\SMTPcom;

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
		'delivered',
		'hard_bounced',
	];

	/**
	 * Create webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function subscribe() {

		// Already subscribed.
		if ( $this->is_subscribed() ) {
			return true;
		}

		foreach ( self::EVENTS as $event ) {
			$body = [
				'channel' => $this->provider->get_option( 'channel' ),
				'address' => $this->provider->get_url(),
				'medium'  => 'http',
			];

			$response = $this->request( $event, 'POST', $body );

			if ( is_wp_error( $response ) ) {
				return $response;
			}
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

		foreach ( self::EVENTS as $event ) {

			// Already unsubscribed.
			if ( $this->is_event_subscribed( $event ) === false ) {
				continue;
			}

			$body = [
				'channel' => $this->provider->get_option( 'channel' ),
			];

			$response = $this->request( $event, 'DELETE', $body );

			if ( is_wp_error( $response ) ) {
				return $response;
			}
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

		foreach ( self::EVENTS as $event ) {

			$is_subscribed = $this->is_event_subscribed( $event );

			if ( is_wp_error( $is_subscribed ) ) {
				return $is_subscribed;
			}

			if ( $is_subscribed === false ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check particular event webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @param string $event Event name.
	 *
	 * @return bool|\WP_Error
	 */
	protected function is_event_subscribed( $event ) {

		$body = [
			'channel' => $this->provider->get_option( 'channel' ),
		];

		$response = $this->request( $event, 'GET', $body );

		// Not found any subscriptions.
		if ( is_wp_error( $response ) && $response->get_error_code() === 400 ) {
			return false;
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return isset( $response['data']['address'] ) && $response['data']['address'] === $this->provider->get_url();
	}

	/**
	 * Performs SMTP.com webhooks API HTTP request.
	 *
	 * @since 3.3.0
	 *
	 * @param string $event  Event name.
	 * @param string $method Request method.
	 * @param array  $params Request params.
	 *
	 * @return mixed|\WP_Error
	 */
	protected function request( $event, $method = 'GET', $params = [] ) {

		$endpoint = 'https://api.smtp.com/v4/callbacks/' . $event;

		$args = [
			'method'  => $method,
			'headers' => [
				'Authorization' => 'Bearer ' . $this->provider->get_option( 'api_key' ),
				'Accept'        => 'application/json',
				'content-type'  => 'application/json',
			],
		];

		if ( $method === 'GET' ) {
			$endpoint = add_query_arg( $params, $endpoint );
		} else {
			$args['body'] = wp_json_encode( $params );
		}

		$response = wp_remote_request( $endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return $this->get_response_error( $response );
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Retrieve errors from SMTP.com API response.
	 *
	 * @since 3.3.0
	 *
	 * @param array $response Response array.
	 *
	 * @return WP_Error
	 */
	protected function get_response_error( $response ) {

		$body       = json_decode( wp_remote_retrieve_body( $response ) );
		$error_text = [];

		if ( ! empty( $body->data ) ) {
			foreach ( (array) $body->data as $error_key => $error_message ) {
				$error_text[] = Helpers::format_error_message( $error_message, $error_key );
			}
		} else {
			$error_text[] = WP::wp_remote_get_response_error_message( $response );
		}

		return new WP_Error( wp_remote_retrieve_response_code( $response ), implode( WP::EOL, $error_text ) );
	}
}
