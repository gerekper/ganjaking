<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Mailgun;

use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractSubscriber;
use WPMailSMTP\Providers\Mailgun\Mailer;

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
		'permanent_fail'
	];

	/**
	 * Create webhook subscription.
	 *
	 * @since 3.3.0
	 *
	 * @return true|\WP_Error
	 */
	public function subscribe() {

		foreach ( self::EVENTS as $event ) {
			$subscribed_urls = $this->get_subscribed_urls( $event );

			if ( is_wp_error( $subscribed_urls ) ) {
				return $subscribed_urls;
			}

			// Already subscribed.
			if ( in_array( $this->provider->get_url(), $subscribed_urls, true ) ) {
				continue;
			}

			$subscribed_urls[] = $this->provider->get_url();

			$body = [
				'url' => $subscribed_urls,
			];

			$method = count( $subscribed_urls ) === 1 ? 'POST' : 'PUT';

			// Create or update subscription.
			$response = $this->request( $event, $method, $body );

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
			$subscribed_urls = $this->get_subscribed_urls( $event );

			if ( is_wp_error( $subscribed_urls ) ) {
				return $subscribed_urls;
			}

			// Already unsubscribed.
			if ( ! in_array( $this->provider->get_url(), $subscribed_urls, true ) ) {
				continue;
			}

			if ( count( $subscribed_urls ) === 1 ) {

				// Delete event.
				$response = $this->request( $event, 'DELETE' );
			} else {

				// Filter out our subscription url, but keep other.
				$subscribed_urls = array_filter(
					$subscribed_urls,
					function ( $url ) {
						return $url !== $this->provider->get_url();
					}
				);

				$body = [
					'url' => $subscribed_urls,
				];

				// Update subscription.
				$response = $this->request( $event, 'PUT', $body );
			}

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
			$subscribed_urls = $this->get_subscribed_urls( $event );

			if ( is_wp_error( $subscribed_urls ) ) {
				return $subscribed_urls;
			}

			if ( ! in_array( $this->provider->get_url(), $subscribed_urls, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get event subscribed urls.
	 *
	 * @since 3.3.0
	 *
	 * @param string $event Event name.
	 *
	 * @return array|\WP_Error
	 */
	protected function get_subscribed_urls( $event ) {

		$response = $this->request( $event );

		if ( is_wp_error( $response ) && $response->get_error_code() !== 404 ) {
			return $response;
		}

		return ! is_wp_error( $response ) ? $response['webhook']['urls'] : [];
	}

	/**
	 * Performs Mailgun webhooks API HTTP request.
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

		// Prepare the API endpoint.
		$endpoint = $this->provider->get_option( 'region' ) === 'EU' ? Mailer::API_BASE_EU : Mailer::API_BASE_US;
		$endpoint .= 'domains/' . sanitize_text_field( $this->provider->get_option( 'domain' ) ) . '/webhooks';

		$args = [
			'method'  => $method,
			'headers' => [
				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
				'Authorization' => 'Basic ' . base64_encode( 'api:' . $this->provider->get_option( 'api_key' ) ),
			],
		];

		if ( $method === 'POST' ) {
			$params['id'] = $event;
		} else {
			$endpoint .= '/' . $event;
		}

		if ( $method === 'GET' ) {
			$endpoint = add_query_arg( $params, $endpoint );
		} else {
			$args['body'] = $this->prepare_body_params( $params );
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
	 * Retrieve errors from Mailgun API response.
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

		if ( ! empty( $body['message'] ) ) {
			$error->add( $response_code, $body['message'] );
		} else {
			$error->add( $response_code, wp_remote_retrieve_response_message( $response ) );
		}

		return $error;
	}

	/**
	 * Prepare body params as a query string.
	 * We need to build such structure to have the ability to pass several `url` params.
	 *
	 * @since 3.3.0
	 *
	 * @param array $params Params array.
	 *
	 * @return string
	 */
	protected function prepare_body_params( $params ) {

		$result = [];

		foreach ( $params as $key => $value ) {
			if ( $key === 'url' && is_array( $value ) ) {
				foreach ( $value as $url ) {
					$result[] = [ $key => $url ];
				}
			} else {
				$result[] = [ $key => $value ];
			}
		}

		return implode( '&', array_map( 'http_build_query', $result ) );
	}
}
