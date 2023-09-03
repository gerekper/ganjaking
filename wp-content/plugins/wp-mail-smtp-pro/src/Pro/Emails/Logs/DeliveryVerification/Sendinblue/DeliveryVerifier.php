<?php

namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Sendinblue;

use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\AbstractDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryStatus;
use WPMailSMTP\WP;

/**
 * Class DeliveryVerifier.
 *
 * Delivery verifier for Sendinblue.
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

		$mailer_options     = $this->get_mailer_options();
		$response           = wp_safe_remote_get(
			'https://api.brevo.com/v3/smtp/statistics/events',
			[
				'body'    => [
					'limit'     => 10,
					'messageId' => $this->get_email()->get_header( 'Message-ID' ),
				],
				'headers' => [
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json',
					'api-key'      => $mailer_options['api_key'],
				],
			]
		);
		$validated_response = $this->validate_response( $response );

		if ( is_wp_error( $validated_response ) ) {
			return $validated_response;
		}

		if ( empty( $validated_response['events'] ) ) {
			return new WP_Error( 'sendinblue_delivery_verifier_missing_response', WP::wp_remote_get_response_error_message( $response ) );
		}

		return $validated_response['events'];
	}

	/**
	 * Get the DeliveryStatus of the email.
	 *
	 * @since 3.9.0
	 *
	 * @return DeliveryStatus
	 */
	protected function get_delivery_status(): DeliveryStatus {

		$delivery_status    = new DeliveryStatus();
		$failed_event_types = [
			'hardBounces',
			'blocked',
			'invalid',
		];

		foreach ( $this->events as $event ) {
			if ( empty( $event['event'] ) ) {
				continue;
			}

			if ( $event['event'] === 'delivered' ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_DELIVERED );
				break;
			}

			if ( in_array( $event['event'], $failed_event_types, true ) ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_FAILED );

				if ( ! empty( $event['reason'] ) ) {
					$delivery_status->set_fail_reason( $event['reason'] );
				}
				break;
			}
		}

		return $delivery_status;
	}
}
