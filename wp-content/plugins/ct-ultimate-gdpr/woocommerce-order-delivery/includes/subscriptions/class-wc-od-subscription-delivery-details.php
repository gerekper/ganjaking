<?php
/**
 * Class to handle the subscription's delivery details.
 *
 * @package WC_OD/Subscriptions
 * @since   2.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_OD_Subscription_Delivery_Details.
 */
class WC_OD_Subscription_Delivery_Details {

	/**
	 * Subscription object.
	 *
	 * @var WC_Subscription
	 */
	protected $subscription;

	/**
	 * The first available date to deliver the next renewal.
	 *
	 * @var WC_DateTime
	 */
	protected $first_delivery_date;

	/**
	 * The maximum date to deliver the next renewal.
	 *
	 * @var WC_DateTime
	 */
	protected $max_delivery_date;

	/**
	 * Constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param WC_Subscription $subscription Subscription object.
	 */
	public function __construct( $subscription ) {
		$this->subscription = $subscription;
	}

	/**
	 * Gets the subscription's delivery date.
	 *
	 * @since 2.3.0
	 *
	 * @return WC_DateTime|false
	 */
	public function get_delivery_date() {
		$delivery_date = $this->subscription->get_meta( '_delivery_date' );

		return ( $delivery_date ? wc_string_to_datetime( $delivery_date ) : false );
	}

	/**
	 * Sets the subscription's delivery date.
	 *
	 * Provide a 'falsy' value to delete the meta.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed $date A WC_Datetime object.
	 * @return bool
	 */
	public function set_delivery_date( $date ) {
		if ( ! $date ) {
			return wc_od_delete_order_meta( $this->subscription, '_delivery_date', true );
		}

		if ( $date instanceof WC_DateTime ) {
			return wc_od_update_order_meta( $this->subscription, '_delivery_date', $date->format( 'Y-m-d' ), true );
		}

		return false;
	}

	/**
	 * Gets the first available date to deliver the next renewal.
	 *
	 * @since 2.3.0
	 *
	 * @return WC_DateTime|false
	 */
	public function get_first_delivery_date() {
		if ( is_null( $this->first_delivery_date ) ) {
			$timestamp = wc_od_get_subscription_first_delivery_date( $this->subscription );

			$this->first_delivery_date = ( $timestamp ? wc_od_timestamp_to_datetime( $timestamp ) : false );
		}

		return ( $this->first_delivery_date ? clone $this->first_delivery_date : false );
	}

	/**
	 * Gets the maximum date to deliver the next renewal.
	 *
	 * @since 2.3.0
	 *
	 * @return WC_DateTime|false
	 */
	public function get_max_delivery_date() {
		if ( is_null( $this->max_delivery_date ) ) {
			$timestamp = wc_od_get_subscription_max_delivery_date( $this->subscription );

			$this->max_delivery_date = ( $timestamp ? wc_od_timestamp_to_datetime( $timestamp ) : false );
		}

		return ( $this->max_delivery_date ? clone $this->max_delivery_date : false );
	}

	/**
	 * Calculates the delivery date for the next subscription's renewal.
	 *
	 * @since 2.3.0
	 *
	 * @param array $args Optional. The arguments to calculate the delivery date. Default empty.
	 * @return WC_DateTime|false
	 */
	public function calculate_delivery_date( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'assign'       => 'first',
				'same_weekday' => false,
			)
		);

		/**
		 * Filters the arguments to calculate the delivery date of the next subscription's renewal.
		 *
		 * @since 2.3.0
		 *
		 * @param array           $args         The arguments.
		 * @param WC_Subscription $subscription Subscription object.
		 */
		$args = apply_filters( 'wc_od_subscription_calculate_delivery_date_args', $args, $this->subscription );

		$first_date   = $this->get_first_delivery_date();
		$current_date = $this->get_delivery_date();

		// Use the first delivery date.
		if ( ! $first_date || ! $current_date || ( 'first' === $args['assign'] && ! $args['same_weekday'] ) ) {
			return $first_date;
		}

		$validate_date   = true;
		$max_date        = $this->get_max_delivery_date();
		$first_timestamp = $first_date->getTimestamp();
		$max_timestamp   = $max_date->getTimestamp();
		$weekday_name    = $current_date->format( 'l' );

		if ( 'first' === $args['assign'] ) {
			/*
			 * Calculate the first weekday.
			 * Remove one millisecond to the $from_timestamp value to accept the first delivery date too.
			 */
			$delivery_date = wc_od_string_to_datetime( "next {$weekday_name}", $first_timestamp - 1 );
		} else {
			// Calculate the delivery date keeping the billing interval.
			$delivery_date = clone $current_date;
			$delivery_date->modify( sprintf( '+%1$s %2$s', $this->subscription->get_billing_interval(), $this->subscription->get_billing_period() ) );

			// Try to assign the same weekday.
			if ( $args['same_weekday'] ) {
				/*
				 * Get the previous and next weekday.
				 * Remove or add one millisecond to the $from_timestamp value to also accept the $delivery_date value.
				 */
				$date_timestamp     = $delivery_date->getTimestamp();
				$previous_timestamp = wc_string_to_timestamp( "previous {$weekday_name}", $date_timestamp + 1 );
				$next_timestamp     = wc_string_to_timestamp( "next {$weekday_name}", $date_timestamp - 1 );

				// Use the closest date to the delivery date.
				if ( ( $date_timestamp - $previous_timestamp <= $next_timestamp - $date_timestamp ) && $this->validate_delivery_date( $previous_timestamp ) ) {
					$delivery_date = wc_od_timestamp_to_datetime( $previous_timestamp );
					$validate_date = false;
				} elseif ( $this->validate_delivery_date( $next_timestamp ) ) {
					$delivery_date = wc_od_timestamp_to_datetime( $next_timestamp );
					$validate_date = false;
				}
			} elseif ( $delivery_date->getTimestamp() < $first_timestamp ) {
				$delivery_date = clone $first_date;
			} elseif ( $delivery_date->getTimestamp() > $max_timestamp ) {
				$delivery_date = clone $max_date;
			}
		}

		// Validate the date and use the first date as a fallback.
		if ( $validate_date && ! $this->validate_delivery_date( $delivery_date ) ) {
			$delivery_date = $first_date;
		}

		return $delivery_date;
	}

	/**
	 * Validates the specified delivery date for the current subscription.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed $delivery_date WC_Datetime object, string, or timestamp.
	 * @return bool
	 */
	public function validate_delivery_date( $delivery_date ) {
		$timestamp = ( $delivery_date instanceof WC_DateTime ? $delivery_date->getTimestamp() : $delivery_date );

		return wc_od_validate_subscription_delivery_date( $this->subscription, $timestamp );
	}
}
