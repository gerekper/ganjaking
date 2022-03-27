<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Postmark;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Delivered;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\Postmark\Events\Failed;

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

		if ( empty( $event_data['RecordType'] ) || empty( $event_data['MessageID'] ) ) {
			return false;
		}

		$email = Email::get_by_message_id( $event_data['MessageID'] );

		if ( empty( $email ) ) {
			return false;
		}

		$event = false;

		if ( $event_data['RecordType'] === 'Delivery' ) {
			$event = new Delivered();
		} elseif ( $event_data['RecordType'] === 'Bounce' && $event_data['Type'] === 'HardBounce' ) {
			$event = new Failed();
		}

		if ( $event === false ) {
			return false;
		}

		$event->handle( $email, $event_data );

		return true;
	}
}
