<?php

namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Sendlayer;

use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\AbstractDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryStatus;
use WPMailSMTP\WP;

/**
 * Class DeliveryVerifier.
 *
 * Delivery verifier for SendLayer.
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

		$mailer_options = $this->get_mailer_options();
		$response       = wp_safe_remote_get(
			add_query_arg(
				[
					'MessageID' => $this->get_email()->get_message_id(),
				],
				'https://console.sendlayer.com/api/v1/events'
			),
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $mailer_options['api_key'],
					'Content-Type'  => 'application/json',
				],
			]
		);

		$validated_response = $this->validate_response( $response );

		if ( is_wp_error( $validated_response ) ) {
			return $validated_response;
		}

		if ( empty( $validated_response['Events'] ) ) {
			return new WP_Error( 'sendlayer_delivery_verifier_missing_response', WP::wp_remote_get_response_error_message( $response ) );
		}

		return $validated_response['Events'];
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
			'failed',
			'rejected-by-system',
			'rejected-by-mta',
		];

		foreach ( $this->events as $event ) {
			if ( empty( $event['Event'] ) ) {
				continue;
			}

			if ( $event['Event'] === 'delivered' ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_DELIVERED );
				break;
			}

			if ( in_array( $event['Event'], $failed_delivery_events, true ) ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_FAILED );

				if ( ! empty( $event['Reason'] ) ) {
					$delivery_status->set_fail_reason( $event['Reason'] );
				}
				break;
			}
		}

		return $delivery_status;
	}
}
