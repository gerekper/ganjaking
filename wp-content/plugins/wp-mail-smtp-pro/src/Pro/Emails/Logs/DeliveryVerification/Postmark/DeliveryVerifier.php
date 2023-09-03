<?php
namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\Postmark;

use WP_Error;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\AbstractDeliveryVerifier;
use WPMailSMTP\Pro\Emails\Logs\DeliveryVerification\DeliveryStatus;
use WPMailSMTP\WP;

/**
 * Class DeliveryVerifier.
 *
 * Delivery verifier for Postmark.
 *
 * @since 3.9.0
 */
class DeliveryVerifier extends AbstractDeliveryVerifier {

	/**
	 * Postmark API base endpoint.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	private $api_base_url = 'https://api.postmarkapp.com';

	/**
	 * Get events from the API response.
	 *
	 * @since 3.9.0
	 *
	 * @return mixed|WP_Error Returns `WP_Error` if unable to fetch a valid response from the API.
	 *                        Otherwise, returns an array containing the events.
	 */
	protected function get_events() {

		$response           = wp_safe_remote_get(
			$this->api_base_url . '/messages/outbound/' . $this->get_email()->get_message_id() . '/details',
			$this->get_request_args()
		);
		$validated_response = $this->validate_response( $response );

		if ( is_wp_error( $validated_response ) ) {
			return $validated_response;
		}

		if ( empty( $validated_response['MessageEvents'] ) ) {
			return new WP_Error( 'postmark_delivery_verifier_missing_response', WP::wp_remote_get_response_error_message( $response ) );
		}

		return $validated_response['MessageEvents'];
	}

	/**
	 * Get the DeliveryStatus of the email.
	 *
	 * @since 3.9.0
	 *
	 * @return DeliveryStatus
	 */
	protected function get_delivery_status(): DeliveryStatus { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh, Generic.Metrics.NestingLevel.MaxExceeded

		$delivery_status = new DeliveryStatus();

		// Process the event items and check event types.
		foreach ( $this->events as $event ) {
			if ( empty( $event['Type'] ) ) {
				continue;
			}

			if ( $event['Type'] === 'Delivered' ) {
				$delivery_status->set_status( DeliveryStatus::STATUS_DELIVERED );
				break;
			}

			if ( $event['Type'] === 'Bounced' ) {
				$bounced_event = $this->handle_bounced( $event );

				if (
					is_array( $bounced_event ) &&
					isset( $bounced_event['failed'] ) &&
					$bounced_event['failed']
				) {
					$delivery_status->set_status( DeliveryStatus::STATUS_FAILED );

					if ( ! empty( $bounced_event['reason'] ) ) {
						$delivery_status->set_fail_reason( esc_html( $bounced_event['reason'] ) );
					}
					break;
				}
			}
		}

		return $delivery_status;
	}

	/**
	 * Get the request arguments.
	 *
	 * @since 3.9.0
	 *
	 * @return array
	 */
	private function get_request_args() {

		$mailer_options = $this->get_mailer_options();

		return [
			'headers' => [
				'X-Postmark-Server-Token' => $mailer_options['server_api_token'],
				'Content-Type'            => 'application/json',
			],
		];
	}

	/**
	 * Handle bounced event.
	 *
	 * @since 3.9.0
	 *
	 * @param array $event Array containing the event details.
	 *
	 * @return array|null Returns `null` if the email was not hard bounced, otherwise returns an array with the reason.
	 */
	private function handle_bounced( $event ) {

		if ( empty( $event['Details']['BounceID'] ) ) {
			return [
				'failed' => true,
			];
		}

		// Get bounce details.
		$response = wp_safe_remote_get(
			$this->api_base_url . '/bounces/' . $event['Details']['BounceID'],
			$this->get_request_args()
		);

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			return [
				'failed' => true,
			];
		}

		$bounce = json_decode( wp_remote_retrieve_body( $response ), true );

		// Try later if email was not hard bounced.
		if ( isset( $bounce['Type'] ) && $bounce['Type'] !== 'HardBounce' ) {
			return null;
		}

		return [
			'failed' => true,
			'reason' => ! empty( $bounce['Description'] ) ? $bounce['Description'] : '',
		];
	}
}
