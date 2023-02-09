<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directlyw

class WC_SRE_Date_Range {

	private $interval;

	/**
	 * The start date
	 *
	 * @var DateTime
	 */
	private $start_date;

	/**
	 * The end date
	 *
	 * @var DateTime
	 */
	private $end_date;

	/**
	 * The constructor
	 *
	 * @param $interval
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct( $interval ) {

		// Set the interval
		$this->interval = $interval;

		// Set the dates based on interval
		$this->set_dates();
	}

	/**
	 * Set the correct start and end dates.
	 *
	 * @access private
	 * @since  1.0.0
	 */
	private function set_dates() {

		// Set the end date.
		$this->end_date = new DateTime( date( 'Y-m-d' ) . '00:00:00' );

		// Clone end date into start date.
		$this->start_date = clone $this->end_date;

		// Subtract a second from end date.
		$this->end_date->modify( '-1 second' );

		// Modify start date based on interval.
		switch ( $this->interval ) {
			case 'monthly':
				$this->start_date->modify( '-1 month' );
				break;
			case 'weekly':
				$this->start_date->modify( '-1 week' );
				break;
			case 'daily':
			default:
				$this->start_date->modify( '-1 day' );
				break;
		}

	}

	/**
	 * Get the end date
	 *
	 * @access public
	 * @since  1.0.0
	 * @return DateTime
	 */
	public function get_end_date() {
		return $this->end_date;
	}

	/**
	 * Set the end date
	 *
	 * @param DateTime $end_date
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function set_end_date( $end_date ) {
		$this->end_date = $end_date;
	}

	/**
	 * Get the interval
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return String
	 */
	public function get_interval() {
		return $this->interval;
	}

	/**
	 * Set the interval
	 *
	 * @param String $interval
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function set_interval( $interval ) {
		$this->interval = $interval;
	}

	/**
	 * Get the start date
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return DateTime
	 */
	public function get_start_date() {
		return $this->start_date;
	}

	/**
	 * Set the start date
	 *
	 * @param DateTime $start_date
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function set_start_date( $start_date ) {
		$this->start_date = $start_date;
	}

}
