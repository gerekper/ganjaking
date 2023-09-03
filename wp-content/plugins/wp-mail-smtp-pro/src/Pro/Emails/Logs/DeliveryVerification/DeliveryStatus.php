<?php

namespace WPMailSMTP\Pro\Emails\Logs\DeliveryVerification;

/**
 * Class DeliveryStatus.
 *
 * @since 3.9.0
 */
class DeliveryStatus {

	/**
	 * Delivered status.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const STATUS_DELIVERED = 'delivered';

	/**
	 * Failed to deliver status.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const STATUS_FAILED = 'failed';

	/**
	 * Unknown or unable to get the delivery status.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const STATUS_UNKNOWN = 'unknown';

	/**
	 * Reason for the delivery failure.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	private $fail_reason = '';

	/**
	 * Delivery status. Default `self::STATUS_UNKNOWN`.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	private $status = self::STATUS_UNKNOWN;

	/**
	 * Whether the delivery status is delivered.
	 *
	 * @since 3.9.0
	 *
	 * @return bool
	 */
	public function is_delivered() {

		return $this->status === self::STATUS_DELIVERED;
	}

	/**
	 * Whether the delivery status is failed.
	 *
	 * @since 3.9.0
	 *
	 * @return bool
	 */
	public function is_failed() {

		return $this->status === self::STATUS_FAILED;
	}

	/**
	 * Whether we are able to get the delivery status.
	 *
	 * @since 3.9.0
	 *
	 * @return bool
	 */
	public function is_verified() {

		return $this->status !== self::STATUS_UNKNOWN;
	}

	/**
	 * Set the delivery status.
	 *
	 * @since 3.9.0
	 *
	 * @param string $status Deliver status.
	 */
	public function set_status( $status ) {

		if ( in_array( $status, [ self::STATUS_DELIVERED, self::STATUS_FAILED ], true ) ) {
			$this->status = $status;
		}
	}

	/**
	 * Get the reason why the delivery failed.
	 *
	 * @since 3.9.0
	 *
	 * @return string
	 */
	public function get_fail_reason() {

		return $this->fail_reason;
	}

	/**
	 * Set the reason why the delivery failed.
	 *
	 * @since 3.9.0
	 *
	 * @param string $reason Reason why the delivery failed.
	 *
	 * @return void
	 */
	public function set_fail_reason( $reason ) {

		$this->fail_reason = $reason;
	}
}
