<?php
/**
 * A class for representing a delivery date.
 *
 * @package WC_OD/Classes
 * @since   1.8.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Class WC_OD_Delivery_Date
 */
class WC_OD_Delivery_Date {

	/**
	 * The date's timestamp
	 *
	 * @var string
	 */
	private $timestamp;

	/**
	 * The delivery day object to check with.
	 *
	 * @var WC_OD_Delivery_Day
	 */
	private $delivery_day;

	/**
	 * WC_OD_Delivery_Date constructor.
	 *
	 * @param string             $timestamp The date's timestamp.
	 * @param WC_OD_Delivery_Day $delivery_day The delivery day to check with.
	 */
	public function __construct( $timestamp, $delivery_day ) {
		$this->timestamp    = $timestamp;
		$this->delivery_day = $delivery_day;
	}

	/**
	 * Gets the timestamp
	 *
	 * @return string
	 */
	public function get_timestamp() {
		return $this->timestamp;
	}


	/**
	 * Checks if the day is available for delivery.
	 *
	 * @return bool
	 */
	public function is_valid() {
		if ( wc_od_is_disabled_day( $this->timestamp ) ) {
			return false;
		}

		if ( $this->delivery_day->has_time_frames() ) {
			// We should check if all the time frames are full.
			if ( $this->time_frames_are_full() ) {
				return false;
			}
		} else {
			$number_of_orders = $this->delivery_day->get_number_of_orders();

			// 0 means no limit
			if ( $number_of_orders < 1 ) {
				return true;
			}

			// Disable days that had reached the max number of orders.
			if ( ! $this->delivery_day || $number_of_orders <= wc_od_get_orders_to_deliver( $this->timestamp ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if a delivery day has reached all the available orders for all the timeframes.
	 *
	 * @return bool
	 */
	public function time_frames_are_full() {
		if ( null === $this->delivery_day || ! $this->delivery_day->has_time_frames() ) {
			return false;
		}

		$full_time_frames = 0;
		/* @var WC_OD_Time_Frame $time_frame The time frame object to work with. */
		foreach ( $this->delivery_day->get_time_frames() as $time_frame ) {
			$number_of_orders = $time_frame->get_number_of_orders();

			// 0 means no limit.
			if ( $number_of_orders < 1 ) {
				continue;
			}

			$from = $time_frame->get_time_from();
			$to   = $time_frame->get_time_to();

			$orders = wc_od_get_orders_to_deliver_in_time_frame( $this->timestamp, $from, $to );
			if ( $orders >= $number_of_orders ) {
				$full_time_frames++;
			}
		}

		return $full_time_frames >= count( $this->delivery_day->get_time_frames() );
	}
}
