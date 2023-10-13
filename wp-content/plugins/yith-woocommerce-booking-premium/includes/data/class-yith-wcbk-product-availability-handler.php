<?php
/**
 * Class YITH_WCBK_Product_Availability_Handler
 * Handles the product availability.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Product_Availability_Handler' ) ) {
	/**
	 * Class YITH_WCBK_Product_Availability_Handler
	 */
	class YITH_WCBK_Product_Availability_Handler extends YITH_WCBK_Availability_Handler {
		const TYPE = 'product';

		/**
		 * The product.
		 *
		 * @var WC_Product_Booking
		 */
		protected $product;

		/**
		 * Initialization.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function init( WC_Product_Booking $product ) {
			$this->non_available_reasons = array();
			$this->product               = $product;

			$global_availability_rules  = yith_wcbk()->settings->get_global_availability_rules( array( 'product_id' => $product->get_id() ) );
			$product_availability_rules = $product->get_availability_rules();

			if ( ! $product->has_time() ) {
				$global_availability_rules = yith_wcbk_exclude_availability_rules_with_time( $global_availability_rules );
			}

			$availability_rules = array_merge( $global_availability_rules, $product_availability_rules );
			$availability_rules = apply_filters( 'yith_wcbk_product_availability_rules_when_checking_for_availability', $availability_rules, $global_availability_rules, $product_availability_rules, $product );
			$availability_rules = array_map( 'yith_wcbk_availability_rule', $availability_rules );

			$this->set_availability_rules( $availability_rules );
			$this->set_default_availability( $product->get_default_availabilities() );
			$this->set_duration_unit( $product->get_duration_unit() );
		}

		/**
		 * Is this initialized?
		 *
		 * @return bool
		 */
		protected function is_initialized(): bool {
			return isset( $this->product );
		}

		/**
		 * Check for the default availability.
		 *
		 * @param int   $from The 'from' timestamp.
		 * @param int   $to   The 'to' timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool
		 */
		public function check_booked_availability( int $from, int $to, array $args = array() ): bool {
			if ( ! $this->is_initialized() ) {
				return false;
			}

			$product               = $this->product;
			$available             = true;
			$max_bookings_per_unit = $product->get_max_bookings_per_unit();

			if ( $max_bookings_per_unit ) {
				if ( isset( $args['_booking_id'] ) && empty( $args['exclude'] ) ) {
					// Exclude the booking if the customer is paying for his/her confirmed booking.
					$args['exclude'] = absint( $args['_booking_id'] );
				}

				$product_id                = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $product->get_id() );
				$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();
				$unit                      = $this->get_duration_unit();
				$persons                   = isset( $args['persons'] ) ? max( 1, absint( $args['persons'] ) ) : $product->get_minimum_number_of_people();

				if ( ! $product->has_people() ) {
					$persons = 0;
				}

				if ( $product->get_buffer() ) {
					$from = yith_wcbk_date_helper()->get_time_sum( $from, - $product->get_buffer(), $unit );
					$to   = yith_wcbk_date_helper()->get_time_sum( $to, $product->get_buffer(), $unit );
				}

				$count_args = array(
					'product_id'                => $product_id,
					'from'                      => $from,
					'to'                        => $to,
					'unit'                      => $unit,
					'include_externals'         => $product->has_external_calendars(),
					'count_persons_as_bookings' => $count_persons_as_bookings,
					'exclude'                   => $args['exclude'] ?? false,
					'exclude_order_id'          => $args['exclude_order_id'] ?? false,
					'return'                    => $max_bookings_per_unit < 2 ? 'total' : 'max_by_unit',
				);

				$number_of_bookings = yith_wcbk_booking_helper()->count_max_booked_bookings_per_unit_in_period( $count_args );

				$booking_weight = ! ! $count_persons_as_bookings ? $persons : 1;

				if ( $number_of_bookings + $booking_weight > $max_bookings_per_unit ) {
					$available = false;
					$remained  = $max_bookings_per_unit - $number_of_bookings;
					if ( $remained > 0 ) {
						if ( $product->has_people() && $product->has_count_people_as_separate_bookings_enabled() ) {
							// translators: %s is the remaining people number.
							$this->add_non_available_reason( 'max-bookings-per-unit', sprintf( __( 'Too many people selected (%s remaining)', 'yith-booking-for-woocommerce' ), $remained ) );
						} else {
							// translators: %s is the remaining quantity.
							$this->add_non_available_reason( 'max-bookings-per-unit', sprintf( __( '%s remaining', 'yith-booking-for-woocommerce' ), $remained ) );
						}
					}
				}
			}

			return $available;
		}
	}
}
