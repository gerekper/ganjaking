<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendlayer;

use WP_REST_Request;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Delivered;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendlayer\Events\Failed;

/**
 * Class Processor.
 *
 * @since 3.4.0
 */
class Processor extends AbstractProcessor {

	/**
	 * Validate webhook incoming request.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function validate( WP_REST_Request $request ) {

		return true;
	}

	/**
	 * Handle webhook incoming request.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function handle( WP_REST_Request $request ) {

		$event_data = $request->get_param( 'EventData' );

		if ( empty( $event_data['Event'] ) || empty( $event_data['MessageID'] ) ) {
			return false;
		}

		$message_id = $event_data['MessageID'];
		$email      = Email::get_by_message_id( $message_id );

		if ( empty( $email ) ) {
			return false;
		}

		$event = false;

		if ( $event_data['Event'] === 'delivered' ) {
			$event = new Delivered();
		} elseif ( $event_data['Event'] === 'bounced' ) {
			$event = new Failed();
		}

		if ( $event === false ) {
			return false;
		}

		$event->handle( $email, $event_data );

		return true;
	}
}
