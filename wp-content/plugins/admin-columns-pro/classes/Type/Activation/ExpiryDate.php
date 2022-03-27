<?php

namespace ACP\Type\Activation;

use DateTime;

class ExpiryDate {

	/**
	 * @var DateTime `null` is lifetime
	 */
	private $expiry_date;

	/**
	 * @var DateTime
	 */
	private $current_date;

	public function __construct( DateTime $expiry_date = null ) {
		$this->expiry_date = $expiry_date;
		$this->current_date = new DateTime();
	}

	/**
	 * @return bool
	 */
	public function exists() {
		return null !== $this->expiry_date;
	}

	/**
	 * @return DateTime
	 */
	public function get_value() {
		return $this->expiry_date;
	}

	/**
	 * @return bool
	 */
	public function is_expired() {
		if ( $this->is_lifetime() ) {
			return false;
		}

		return $this->expiry_date && $this->expiry_date < $this->current_date;
	}

	/**
	 * @return bool
	 */
	public function is_lifetime() {
		if ( null === $this->expiry_date ) {
			return false;
		}

		$lifetime_end_date = DateTime::createFromFormat( 'Y-m-d', '2037-12-30' );

		if ( ! $lifetime_end_date ) {
			return false;
		}

		return $this->expiry_date > $lifetime_end_date;
	}

	/**
	 * @return int
	 */
	public function get_expired_seconds() {
		return $this->current_date->getTimestamp() - $this->expiry_date->getTimestamp();
	}

	/**
	 * @return int
	 */
	public function get_remaining_seconds() {
		return $this->expiry_date->getTimestamp() - $this->current_date->getTimestamp();
	}

	/**
	 * @return float
	 */
	public function get_remaining_days() {
		return $this->get_remaining_seconds() / DAY_IN_SECONDS;
	}

	/**
	 * @param int $seconds
	 *
	 * @return bool
	 */
	public function is_expiring_within_seconds( $seconds ) {
		return $this->get_remaining_seconds() < $seconds;
	}

	/**
	 * @return string
	 */
	public function get_human_time_diff() {
		return human_time_diff( $this->expiry_date->getTimestamp(), $this->current_date->getTimestamp() );
	}

}