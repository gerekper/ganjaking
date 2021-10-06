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
	 * @since 1.8.0
	 * @since 1.9.3 Added `$args` parameter.
	 *
	 * @param array $args Optional. Additional arguments.
	 * @return bool
	 */
	public function is_valid( $args = array() ) {
		if ( wc_od_is_disabled_day( $this->timestamp ) ) {
			return false;
		}

		if ( $this->delivery_day->has_time_frames() ) {
			$time_frames = wc_od_get_time_frames_for_delivery_day( $this->delivery_day, $args );

			// Check if the time frames are full.
			if ( $this->time_frames_are_full( $time_frames ) ) {
				return false;
			}
		} else {
			$number_of_orders = $this->delivery_day->get_number_of_orders();

			// The maximum number of orders has been reached.
			if ( 0 < $number_of_orders && $number_of_orders <= wc_od_get_orders_to_deliver( $this->timestamp ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if a delivery day has reached all the available orders for all the timeframes.
	 *
	 * @since 1.8.0
	 * @since 1.9.3 Added `$time_frames` parameter.
	 *
	 * @param WC_OD_Collection_Time_Frames $time_frames The time frames collection.
	 * @return bool
	 */
	public function time_frames_are_full( $time_frames ) {
		foreach ( $time_frames as $time_frame ) {
			$number_of_orders = $time_frame->get_number_of_orders();

			// It has no limit.
			if ( 0 === $number_of_orders ) {
				return false;
			}

			$orders = wc_od_get_orders_to_deliver_in_time_frame( $this->timestamp, $time_frame->get_time_from(), $time_frame->get_time_to() );

			// There is a time frame whose limit has not been reached.
			if ( $orders < $number_of_orders ) {
				return false;
			}
		}

		return true;
	}
}
