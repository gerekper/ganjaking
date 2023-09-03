<?php

namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\SMTPcom;

use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\AbstractDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryStatus;
use WPMailSMTP\WP;

/**
 * Class DeliveryVerifier.
 *
 * Delivery verifier for SMTP.com.
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

		try {
			$begin_timestamp = $this->get_email()->get_date_sent()->getTimestamp();
		} catch ( \Exception $exception ) {
			$begin_timestamp = time();
		}

		$response           = wp_safe_remote_get(
			add_query_arg(
				[
					'channel' => $mailer_options['channel'],
					'start'   => $begin_timestamp - ( 45 * MINUTE_IN_SECONDS ),
					'limit'   => 10,
					'offset'  => 0,
					'msg_id'  => $this->get_email()->get_header( 'X-Msg-ID' ),
				],
				'https://api.smtp.com/v4/messages'
			),
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $mailer_options['api_key'],
					'Accept'        => 'application/json',
					'Content-Type'  => 'application/json',
				],
			]
		);
		$validated_response = $this->validate_response( $response );

		if ( is_wp_error( $validated_response ) ) {
			return $validated_response;
		}

		if ( empty( $validated_response['data']['items'] ) ) {
			return new WP_Error( 'smtpcom_delivery_verifier_missing_response', WP::wp_remote_get_response_error_message( $response ) );
		}

		return $validated_response['data']['items'];
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
			'failed',
			'bounced',
		];

		foreach ( $this->events as $item ) {
			if ( empty( $item['details']['delivery']['event'] ) ) {
				continue;
			}

			$event = $item['details']['delivery']['event'];

			if ( $event === 'delivered' ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_DELIVERED );
				break;
			}

			if ( in_array( $event, $failed_event_types, true ) ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_FAILED );

				if ( ! empty( $item['details']['delivery']['status'] ) ) {
					$delivery_status->set_fail_reason( $item['details']['delivery']['status'] );
				}
				break;
			}
		}

		return $delivery_status;
	}
}
