<?php
/**
 * Class YITH_WCBK_External_Sync_Bookings
 * Handle booking for the External Sync module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\ExternalSync
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_External_Sync_Bookings' ) ) {
	/**
	 * YITH_WCBK_External_Sync_Bookings class.
	 */
	class YITH_WCBK_External_Sync_Bookings {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			add_filter( 'yith_wcbk_booking_helper_count_booked_bookings_in_period', array( $this, 'filter_count_booked_bookings_in_period' ), 10, 2 );
			add_filter( 'yith_wcbk_booking_helper_get_bookings_in_time_range', array( $this, 'filter_bookings_in_time_range' ), 10, 3 );
		}

		/**
		 * Filter the count of booked bookings in period to include externals, if needed.
		 *
		 * @param int   $count The count.
		 * @param array $args  The arguments.
		 *
		 * @return int
		 */
		public function filter_count_booked_bookings_in_period( int $count, array $args ): int {
			$include_externals = $args['include_externals'] ?? true;
			$product_id        = $args['product_id'] ?? false;
			$from              = $args['from'] ?? false;
			$to                = $args['to'] ?? false;
			if ( $include_externals ) {
				$product = yith_wcbk_get_booking_product( $product_id );
				if ( $product && $product->has_external_calendars() ) {
					$product->maybe_load_externals();
					$count += yith_wcbk_booking_externals()->count_externals_in_period( $from, $to, $product_id );
				}
			}

			return $count;
		}

		/**
		 * Filter bookings retrieved for a specific time-range to include externals, if needed.
		 *
		 * @param YITH_WCBK_Booking_Abstract[] $bookings The bookings.
		 * @param array                        $params   The params.
		 * @param array                        $args     The arguments.
		 *
		 * @return array
		 */
		public function filter_bookings_in_time_range( array $bookings, array $params, array $args ): array {
			$include_externals = $params['include_externals'] ?? true;
			$duration_unit     = $params['duration_unit'] ?? false;
			$from              = $params['from'] ?? false;
			$to                = $params['to'] ?? false;
			$product_id        = $params['product_id'] ?? false;

			$include_externals = apply_filters( 'yith_wcbk_booking_helper_get_bookings_in_time_range_include_externals', $include_externals, $args );

			if ( $include_externals ) {
				yith_wcbk_booking_externals()->maybe_load_all_externals();
				$externals = yith_wcbk_booking_externals()->get_externals_in_period( $from, $to, $product_id );

				$duration_unit = (array) $duration_unit;
				if ( in_array( 'hour', $duration_unit, true ) || in_array( 'minute', $duration_unit, true ) ) {
					$externals = array_filter(
						$externals,
						function ( $external ) {
							return $external->has_time();
						}
					);
				}

				$bookings = array_merge( $bookings, $externals );
			}

			return $bookings;
		}

	}
}
