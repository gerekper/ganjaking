<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendinblue;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Delivered;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Sendinblue\Events\Failed;

/**
 * Class Processor.
 *
 * @since 3.3.0
 */
class Processor extends AbstractProcessor {

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

		$event_data = $request->get_params();

		if ( empty( $event_data['event'] ) || empty( $event_data['message-id'] ) ) {
			return false;
		}

		$message_id = trim( $event_data['message-id'], '<>' );
		$email      = Email::get_by_message_id( $message_id );

		if ( empty( $email ) ) {
			return false;
		}

		$event = false;

		if ( $event_data['event'] === 'delivered' ) {
			$event = new Delivered();
		} elseif ( in_array( $event_data['event'], [ 'hard_bounce', 'blocked', 'invalid_email' ], true ) ) {
			$event = new Failed();
		}

		if ( $event === false ) {
			return false;
		}

		$event->handle( $email, $event_data );

		return true;
	}
}
