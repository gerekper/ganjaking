<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\SMTPcom;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Delivered;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\SMTPcom\Events\Failed;

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
	public function handle( \WP_REST_Request $request ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$events = $request->get_params();

		if ( ! is_array( $events ) ) {
			return false;
		}

		foreach ( $events as $event_data ) {
			if ( empty( $event_data['event_label'] ) || empty( $event_data['tracking_id'] ) ) {
				continue;
			}

			$email = Email::get_by_message_id( $event_data['tracking_id'] );

			if ( empty( $email ) ) {
				continue;
			}

			$event = false;

			if ( $event_data['event_label'] === 'delivery' ) {
				$event = new Delivered();
			} elseif ( $event_data['event_label'] === 'bounce' ) {
				$event = new Failed();
			}

			if ( $event === false ) {
				continue;
			}

			$event->handle( $email, $event_data );
		}

		return true;
	}
}
