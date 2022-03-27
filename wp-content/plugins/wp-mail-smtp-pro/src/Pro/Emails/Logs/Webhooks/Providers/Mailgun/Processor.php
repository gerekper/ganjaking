<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Mailgun;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Delivered;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Mailgun\Events\Failed;

/**
 * Class Processor.
 *
 * @since 3.3.0
 */
class Processor extends AbstractProcessor {

	/**
	 * Validate webhook incoming request.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function validate( \WP_REST_Request $request ) {

		$signature_data = $request->get_param( 'signature' );
		$timestamp      = isset( $signature_data['timestamp'] ) ? $signature_data['timestamp'] : '';
		$token          = isset( $signature_data['token'] ) ? $signature_data['token'] : '';
		$signature      = isset( $signature_data['signature'] ) ? $signature_data['signature'] : '';

		if ( empty( $timestamp ) || empty( $token ) || empty( $signature ) ) {
			return false;
		}

		$hmac = hash_hmac( 'sha256', $timestamp . $token, $this->provider->get_option( 'api_key' ) );

		return hash_equals( $hmac, $signature );
	}

	/**
	 * Handle webhook incoming request.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function handle( \WP_REST_Request $request ) {

		$event_data = $request->get_param( 'event-data' );

		if ( empty( $event_data['event'] ) || empty( $event_data['message']['headers']['message-id'] ) ) {
			return false;
		}

		$message_id = $event_data['message']['headers']['message-id'];
		$email      = Email::get_by_message_id( $message_id );

		if ( empty( $email ) ) {
			return false;
		}

		$event = false;

		if ( $event_data['event'] === 'delivered' ) {
			$event = new Delivered();
		} elseif ( $event_data['event'] === 'failed' && $event_data['severity'] === 'permanent' ) {
			$event = new Failed();
		}

		if ( $event === false ) {
			return false;
		}

		$event->handle( $email, $event_data );

		return true;
	}
}
