<?php
namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\SparkPost;

use Exception;
use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\AbstractDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryStatus;
use WPMailSMTP\Providers\SparkPost\Mailer;
use WPMailSMTP\WP;

/**
 * Class DeliveryVerifier.
 *
 * Delivery verifier for SparkPost.
 *
 * @since 3.9.0
 */
class DeliveryVerifier extends AbstractDeliveryVerifier {

	/**
	 * Get events from the API response.
	 *
	 * @since 3.9.0
	 *
	 * @return mixed|WP_Error Returns `WP_Error` if unable to fetch a valid response from the API.
	 *                        Otherwise, returns an array containing the events.
	 */
	protected function get_events() {

		try {
			$from_date = $this->get_email()->get_date_sent()->format( 'Y-m-d\TH:i:s\Z' );
		} catch ( Exception $e ) {
			$from_date = gmdate( 'Y-m-d\TH:i:s\Z', strtotime( '-1 day' ) );
		}

		$mailer_options     = $this->get_mailer_options();
		$endpoint           = ( $mailer_options['region'] === 'EU' ? Mailer::API_BASE_EU : Mailer::API_BASE_US ) . '/events/message';
		$response           = wp_safe_remote_get(
			add_query_arg(
				[
					'from'          => $from_date,
					'events'        => 'delivery,bounce',
					'transmissions' => $this->get_email()->get_message_id(),
				],
				$endpoint
			),
			[
				'headers' => [
					'Authorization' => $mailer_options['api_key'],
					'Content-Type'  => 'application/json',
				],
			]
		);
		$validated_response = $this->validate_response( $response );

		if ( is_wp_error( $validated_response ) ) {
			return $validated_response;
		}

		if ( empty( $validated_response['results'] ) ) {
			return new WP_Error( 'sparkpost_delivery_verifier_missing_response', WP::wp_remote_get_response_error_message( $response ) );
		}

		return $validated_response['results'];
	}

	/**
	 * Get the DeliveryStatus of the email.
	 *
	 * @since 3.9.0
	 *
	 * @return DeliveryStatus
	 */
	protected function get_delivery_status(): DeliveryStatus {

		$delivery_status        = new DeliveryStatus();
		$failed_delivery_events = [
			'out_of_band',
			'policy_rejection',
			'generation_failure',
			'generation_rejection',
		];

		foreach ( $this->events as $event ) {
			if ( empty( $event['type'] ) ) {
				continue;
			}

			if ( $event['type'] === 'delivery' ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_DELIVERED );
				break;
			}

			if (
				( // Hard bounce.
					$event['type'] === 'bounce' &&
					in_array( (int) $event['bounce_class'], [ 1, 10, 25, 30, 80, 90 ], true )
				) ||
				in_array( $event['type'], $failed_delivery_events, true )
			) {
				$delivery_status->set_status( DeliveryStatus::STATUS_FAILED );

				if ( ! empty( $event['raw_reason'] ) ) {
					$delivery_status->set_fail_reason( $event['raw_reason'] );
				}
				break;
			}
		}

		return $delivery_status;
	}
}
