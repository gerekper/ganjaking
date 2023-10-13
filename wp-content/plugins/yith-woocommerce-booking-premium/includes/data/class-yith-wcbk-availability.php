<?php
/**
 * Availability class
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Availability' ) ) {
	/**
	 * Class YITH_WCBK_Availability
	 *
	 * @since   3.0.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Availability extends YITH_WCBK_Simple_Object {
		/**
		 * The object type.
		 *
		 * @var string
		 */
		protected $object_type = 'availability';

		/**
		 * Data array.
		 *
		 * @var array
		 */
		protected $data = array(
			'day'        => 'all',
			'bookable'   => 'yes',
			'time_slots' => array(),
		);

		/**
		 * Retrieve the day option.
		 *
		 * @param string $context The context.
		 *
		 * @return string|int
		 */
		public function get_day( string $context = 'view' ) {
			return $this->get_prop( 'day', $context );
		}

		/**
		 * Retrieve the bookable option.
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_bookable( string $context = 'view' ): string {
			return $this->get_prop( 'bookable', $context );
		}

		/**
		 * Retrieve the time_slots option.
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_time_slots( string $context = 'view' ): array {
			return $this->get_prop( 'time_slots', $context );
		}

		/**
		 * Set Day
		 *
		 * @param string|int $value The value to set.
		 */
		public function set_day( $value ) {
			$value = 'all' === $value ? $value : absint( $value );
			$this->set_prop( 'day', $value );
		}

		/**
		 * Set Bookable
		 *
		 * @param bool|string $value The value to set.
		 */
		public function set_bookable( $value ) {
			$this->set_prop( 'bookable', wc_bool_to_string( $value ) );
		}

		/**
		 * Set Bookable
		 *
		 * @param array $value The value to set.
		 */
		public function set_time_slots( array $value ) {
			$this->set_prop( 'time_slots', is_array( $value ) ? $value : array() );
		}

		/**
		 * Is all days?
		 *
		 * @return bool
		 */
		public function is_all_days(): bool {
			return 'all' === $this->get_day();
		}

		/**
		 * Is full day?
		 * Return true if this has not time-slots.
		 *
		 * @return bool
		 */
		public function is_full_day(): bool {
			return ! $this->get_time_slots();
		}

		/**
		 * Is bookable?
		 *
		 * @return bool
		 */
		public function is_bookable(): bool {
			return 'yes' === $this->get_bookable();
		}

		/**
		 * Test dates.
		 *
		 * @param int   $from From timestamp.
		 * @param int   $to   To timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool
		 */
		public function test_dates( int $from, int $to, array $args = array() ): bool {
			$defaults = array(
				'min_unit'     => '', // values: month, day, empty string.
				'include_time' => true,
				'intersect'    => ! $this->is_bookable(),
			);

			$args  = wp_parse_args( $args, $defaults );
			$check = true;
			if ( ! $this->is_all_days() ) {
				$check = yith_wcbk_date_helper()->check_date_inclusion_in_range( 'day', $this->get_day(), $this->get_day(), $from, $to, true );
			}

			if ( $check && ! $this->is_full_day() && $args['include_time'] && in_array( $args['min_unit'], array( '', 'time' ), true ) ) {
				$intersect = $args['intersect'];
				$check     = false;

				foreach ( $this->get_time_slots() as $slot ) {
					$check = yith_wcbk_date_helper()->check_date_inclusion_in_range( 'time', $slot['from'], $slot['to'], $from, $to, $intersect );

					if ( $check ) {
						break;
					}
				}
			}

			return $check;
		}
	}
}
if ( ! function_exists( 'yith_wcbk_availability' ) ) {
	/**
	 * Return an availability object.
	 *
	 * @param YITH_WCBK_Availability|array $availability The availability data.
	 *
	 * @return YITH_WCBK_Availability
	 */
	function yith_wcbk_availability( $availability ) {
		return is_a( $availability, 'YITH_WCBK_Availability' ) ? $availability : new YITH_WCBK_Availability( $availability );
	}
}

