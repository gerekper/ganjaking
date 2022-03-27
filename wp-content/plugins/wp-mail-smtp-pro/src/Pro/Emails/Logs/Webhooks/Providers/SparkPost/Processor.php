<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\SparkPost;

use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\AbstractProcessor;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Events\Delivered;
use WPMailSMTP\Pro\Emails\Logs\Webhooks\Providers\SparkPost\Events\Failed;

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
			if ( ! isset( $event_data['msys']['message_event'] ) ) {
				continue;
			}

			$event_data = $event_data['msys']['message_event'];

			if ( empty( $event_data['type'] ) || empty( $event_data['transmission_id'] ) ) {
				continue;
			}

			$email = Email::get_by_message_id( $event_data['transmission_id'] );

			if ( empty( $email ) ) {
				continue;
			}

			$event = false;

			$failed_event_types = [ 'out_of_band', 'policy_rejection', 'generation_failure', 'generation_rejection' ];

			if ( $event_data['type'] === 'delivery' ) {
				$event = new Delivered();
			} elseif (
				( // Hard bounce.
					$event_data['type'] === 'bounce' &&
					in_array( (int) $event_data['bounce_class'], [ 1, 10, 25, 30, 80, 90 ], true )
				) ||
				in_array( $event_data['type'], $failed_event_types )
			) {
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
