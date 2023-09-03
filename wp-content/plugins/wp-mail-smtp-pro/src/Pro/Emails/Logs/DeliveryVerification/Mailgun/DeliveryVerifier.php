<?php
namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Mailgun;

use Exception;
use WP_Error;
use WPMailSMTP\WP;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\AbstractDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryStatus;
use WPMailSMTP\Providers\Mailgun\Mailer;

/**
 * Class DeliveryVerifier.
 *
 * Delivery verifier for Mailgun.
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
			$begin_timestamp = $this->get_email()->get_date_sent()->getTimestamp();
		} catch ( Exception $exception ) {
			$begin_timestamp = time();
		}

		$options = $this->get_mailer_options();

		// Prepare the API endpoint.
		$endpoint = Mailer::API_BASE_US;

		if ( ! empty( $options ) && $options['region'] === 'EU' ) {
			$endpoint = Mailer::API_BASE_EU;
		}

		$endpoint .= sanitize_text_field( $options['domain'] ) . '/events';

		$response = wp_safe_remote_get(
			add_query_arg(
				[
					'begin'      => $begin_timestamp - ( 45 * MINUTE_IN_SECONDS ),
					'ascending'  => 'yes',
					'limit'      => 10,
					'pretty'     => 'yes',
					'message-id' => trim( $this->get_email()->get_header( 'Message-ID' ), '<>' ),
				],
				$endpoint
			),
			[
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( 'api:' . $options['api_key'] ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
				],
			]
		);

		$validated_response = $this->validate_response( $response );

		if ( is_wp_error( $validated_response ) ) {
			return $validated_response;
		}

		if ( empty( $validated_response['items'] ) ) {
			return new WP_Error( 'mailgun_delivery_verifier_missing_response', WP::wp_remote_get_response_error_message( $response ) );
		}

		return $validated_response['items'];
	}

	/**
	 * Get the DeliveryStatus of the email.
	 *
	 * @since 3.9.0
	 *
	 * @return DeliveryStatus
	 */
	protected function get_delivery_status(): DeliveryStatus { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$delivery_status = new DeliveryStatus();

		foreach ( $this->events as $event ) {
			if ( empty( $event['event'] ) ) {
				continue;
			}

			if ( $event['event'] === 'delivered' ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_DELIVERED );
				break;
			}

			if ( $event['event'] === 'failed' && $event['severity'] === 'permanent' ) { // Hard bounce.
				$delivery_status->set_status( DeliveryStatus::STATUS_FAILED );

				if ( ! empty( $event['delivery-status']['description'] ) ) {
					$delivery_status->set_fail_reason( $event['delivery-status']['description'] );
				} elseif ( ! empty( $event['delivery-status']['message'] ) ) {
					$delivery_status->set_fail_reason( $event['delivery-status']['message'] );
				}
				break;
			}
		}

		return $delivery_status;
	}
}
