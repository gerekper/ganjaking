<?php
/**
 * WooCommerce Bookings Global Availability Tracking.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * This class adds actions to track usage of WooCommerce Bookings Global Availability.
 */
class WC_Bookings_Global_Availability_Tracking {
	/**
	 * Init tracking.
	 */
	public function init() {
		add_action( 'wc_bookings_global_availability_on_save', array( $this, 'global_availability_on_save' ) );
	}

	/**
	 * When global availability page is saved.
	 *
	 * @since 1.15.0
	 * @param object $availability The availability object.
	 */
	public function global_availability_on_save() {
		// @codingStandardsIgnoreStart
		if ( empty( $_POST ) || empty( $_POST['wc_booking_availability_type'] ) ) {
			return;
		}

		$types      = isset( $_POST['wc_booking_availability_type'] ) ? wc_clean( wp_unslash( $_POST['wc_booking_availability_type'] ) ) : array();
		$row_size   = count( $types );
		$properties = array();

		for ( $i = 0; $i < $row_size; $i++ ) {
			$properties[ 'availability_rule_' . $i ] = '';

			if ( isset( $_POST['wc_booking_availability_bookable'][ $i ] ) ) {
				$properties[ 'availability_rule_' . $i ] .= ', bookable: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_bookable'][ $i ] ) );
			}

			if ( isset( $_POST['wc_booking_availability_title'][ $i ] ) ) {
				$properties[ 'availability_rule_' . $i ] .= ', title: ' . sanitize_text_field( wp_unslash( $_POST['wc_booking_availability_title'][ $i ] ) );
			}

			if ( isset( $_POST['wc_booking_availability_priority'][ $i ] ) ) {
				$properties[ 'availability_rule_' . $i ] .= ', priority: ' . intval( $_POST['wc_booking_availability_priority'][ $i ] );
			}

			switch ( $_POST['wc_booking_availability_type'][ $i ] ) {
				case 'custom':
					if ( isset( $_POST['wc_booking_availability_from_date'][ $i ] ) && isset( $_POST['wc_booking_availability_to_date'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_date: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_from_date'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_date: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_to_date'][ $i ] ) );
					}
					break;
				case 'months':
					if ( isset( $_POST['wc_booking_availability_from_month'][ $i ] ) && isset( $_POST['wc_booking_availability_to_month'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_month: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_from_month'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_month: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_to_month'][ $i ] ) );
					}
					break;
				case 'weeks':
					if ( isset( $_POST['wc_booking_availability_from_week'][ $i ] ) && isset( $_POST['wc_booking_availability_to_week'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_week: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_from_week'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_week: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_to_week'][ $i ] ) );
					}
					break;
				case 'days':
					if ( isset( $_POST['wc_booking_availability_from_day_of_week'][ $i ] ) && isset( $_POST['wc_booking_availability_to_day_of_week'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_day_of_week: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_from_day_of_week'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_day_of_week: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_to_day_of_week'][ $i ] ) );
					}
					break;
				case 'rrule':
					// Do nothing rrules are read only for now.
					break;
				case 'time':
				case 'time:1':
				case 'time:2':
				case 'time:3':
				case 'time:4':
				case 'time:5':
				case 'time:6':
				case 'time:7':
					if ( isset( $_POST['wc_booking_availability_from_time'][ $i ] ) && isset( $_POST['wc_booking_availability_to_time'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_time: ' . wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_from_time'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_time: ' . wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_to_time'][ $i ] ) );
					}
					break;
				case 'time:range':
				case 'custom:daterange':
					if ( isset( $_POST['wc_booking_availability_from_time'][ $i ] ) && isset( $_POST['wc_booking_availability_to_time'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_time: ' . wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_from_time'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_time: ' . wc_booking_sanitize_time( wp_unslash( $_POST['wc_booking_availability_to_time'][ $i ] ) );
					}
					if ( isset( $_POST['wc_booking_availability_from_date'][ $i ] ) && isset( $_POST['wc_booking_availability_to_date'][ $i ] ) ) {
						$properties[ 'availability_rule_' . $i ] .= ', from_date: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_from_date'][ $i ] ) );
						$properties[ 'availability_rule_' . $i ] .= ', to_date: ' . wc_clean( wp_unslash( $_POST['wc_booking_availability_to_date'][ $i ] ) );
					}
					break;
			}

			$properties[ 'availability_rule_' . $i ] = ltrim( $properties[ 'availability_rule_' . $i ], ', " "' );
		}

		WC_Bookings_Tracks::record_event( 'global_availability_saved_settings', $properties );
		// @codingStandardsIgnoreEnd
	}
}
