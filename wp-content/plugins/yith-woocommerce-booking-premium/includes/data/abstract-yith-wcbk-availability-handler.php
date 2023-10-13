<?php
/**
 * Availability Handler class
 * Allow to handle objects with availability.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Availability_Handler' ) ) {
	/**
	 * Class YITH_WCBK_Availability_Handler
	 *
	 * @author  YITH <plugins@yithemes.com>
	 * @since   4.0.0
	 */
	abstract class YITH_WCBK_Availability_Handler {
		const TYPE = '';

		/**
		 * Data array.
		 *
		 * @var array
		 */
		protected $data = array(
			'availability_rules'   => array(),
			'default_availability' => array(),
			'duration_unit'        => 'day',
		);

		/**
		 * Extra data array.
		 *
		 * @var array
		 */
		protected $extra_data = array();

		/**
		 * Non available reasons.
		 *
		 * @var array
		 */
		protected $non_available_reasons = array();

		/**
		 * Default constructor.
		 */
		public function __construct() {
			$this->data = array_merge( $this->data, $this->extra_data );
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get availability rules
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 */
		public function get_availability_rules(): array {
			$availability_rules = $this->get_prop( 'availability_rules' );
			$remove_time_slots  = ! $this->has_time();

			return array_map(
				function ( YITH_WCBK_Availability_Rule $rule ) use ( $remove_time_slots ) {
					$availabilities = $rule->get_availabilities();
					$availabilities = yith_wcbk_validate_availabilities(
						$availabilities,
						array(
							'remove_time_slots'   => $remove_time_slots,
							'force_first_all_day' => false,
						)
					);
					$rule->set_availabilities( $availabilities );

					return $rule;
				},
				$availability_rules
			);
		}

		/**
		 * Get default availability.
		 *
		 * @return YITH_WCBK_Availability[]
		 */
		public function get_default_availability(): array {
			return $this->get_prop( 'default_availability' );
		}

		/**
		 * Get duration unit.
		 *
		 * @return string
		 */
		public function get_duration_unit(): string {
			return $this->get_prop( 'duration_unit' );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		*/

		/**
		 * Set availability rules
		 *
		 * @param array|YITH_WCBK_Availability_Rule[] $availability_rules Availability rules.
		 */
		public function set_availability_rules( array $availability_rules ) {
			$availability_rules = ! ! $availability_rules ? array_map( 'yith_wcbk_availability_rule', $availability_rules ) : array();

			$this->set_prop( 'availability_rules', $availability_rules );
		}

		/**
		 * Set default availabilities
		 *
		 * @param array|YITH_WCBK_Availability[] $availabilities The default availabilities.
		 */
		public function set_default_availability( array $availabilities ) {
			$availabilities = ! ! $availabilities ? array_map( 'yith_wcbk_availability', $availabilities ) : array();

			$this->set_prop( 'default_availability', $availabilities );
		}

		/**
		 * Set the duration
		 *
		 * @param string $value The value to set.
		 */
		public function set_duration_unit( string $value ) {
			$available_units = array_keys( yith_wcbk_get_duration_units() );
			if ( in_array( $value, $available_units, true ) ) {
				$this->set_prop( 'duration_unit', $value );
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		*/

		/**
		 * Return true if duration unit is hour or minute.
		 *
		 * @return bool
		 */
		public function has_time(): bool {
			return in_array( $this->get_duration_unit(), array( 'hour', 'minute' ), true );
		}

		/*
		|--------------------------------------------------------------------------
		| Availability methods
		|--------------------------------------------------------------------------
		*/

		/**
		 * Check for the default availability.
		 *
		 * @param int   $from The from timestamp.
		 * @param int   $to   The to timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool
		 */
		public function is_available( int $from, int $to, array $args = array() ): bool {
			$this->non_available_reasons = array();

			$exclude_time   = $args['exclude_time'] ?? false;
			$exclude_booked = $args['exclude_booked'] ?? false;
			$is_same_day    = strtotime( 'midnight', $from ) === strtotime( 'midnight', $to - 1 );

			$available = $this->check_default_availability( $from, $to, array( 'include_time' => ! $exclude_time ) );

			if ( ! $is_same_day && $this->has_time() && ! $exclude_time ) {
				// Check availability for each single day to allow "fluid" availability.
				$tmp_from = $from;
				$tmp_to   = $from;
				do {
					if ( ! $available ) {
						break;
					}

					$tmp_to    = min( $to, strtotime( 'tomorrow midnight', $tmp_to ) );
					$available = $this->check_availability_rules( $tmp_from, $tmp_to, $exclude_time );
					$tmp_from  = $tmp_to;

				} while ( $tmp_to < $to );

			} else {
				$available = $this->check_availability_rules( $from, $to, $exclude_time, $available );
			}

			if ( ! $exclude_booked && $available ) {
				$available = $this->check_booked_availability( $from, $to, $args );
			}

			return $available;
		}

		/**
		 * Add a non-available reasons.
		 *
		 * @param string $key     The key.
		 * @param string $message The reason message.
		 * @param array  $data    Additional data.
		 */
		public function add_non_available_reason( string $key, string $message, array $data = array() ) {
			$data['message']                     = $message;
			$this->non_available_reasons[ $key ] = $data;
		}

		/**
		 * Retrieve the non-available reasons.
		 *
		 * @return array
		 */
		public function get_non_available_reasons(): array {
			return $this->non_available_reasons;
		}

		/**
		 * Retrieve the non-available reason messages.
		 *
		 * @return array
		 */
		public function get_non_available_reason_messages(): array {
			return array_filter(
				array_map(
					function ( array $reason ) {
						return $reason['message'] ?? '';
					},
					$this->get_non_available_reasons()
				)
			);
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
		abstract public function check_booked_availability( int $from, int $to, array $args = array() ): bool;

		/**
		 * Check for the default availability.
		 *
		 * @param int   $from The from timestamp.
		 * @param int   $to   The to timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool
		 */
		public function check_default_availability( int $from, int $to, array $args = array() ): bool {
			$defaults       = array(
				'include_time' => true,
			);
			$args           = wp_parse_args( $args, $defaults );
			$include_time   = $args['include_time'];
			$available      = true;
			$availabilities = $this->get_default_availability();

			if ( ! $include_time && $this->has_time() ) {
				$availabilities = array_filter(
					$availabilities,
					function ( $availability ) {
						return $availability->is_full_day() || $availability->is_bookable();
					}
				);
			}

			$to --; // Remove one second to fix days and months availability (include the last rule day).

			if ( 'month' !== $this->get_duration_unit() && $availabilities ) {
				$is_same_day = strtotime( 'midnight', $from ) === strtotime( 'midnight', $to );
				$min_unit    = ! $this->has_time() ? 'day' : '';

				if ( ! $is_same_day && $this->has_time() && $include_time ) {
					// Check availability for each single day to allow "fluid" availability.
					$tmp_from       = $from;
					$tmp_to         = $from;
					$re_adjusted_to = ( $to + 1 );

					do {
						if ( ! $available ) {
							break;
						}

						$tmp_to    = min( $re_adjusted_to, strtotime( 'tomorrow midnight', $tmp_to ) );
						$available = $this->check_default_availability( $tmp_from, $tmp_to, $args );
						$tmp_from  = $tmp_to;

					} while ( $tmp_to < $re_adjusted_to );
				} else {
					if ( $is_same_day && $this->has_time() ) {
						$current_day             = absint( gmdate( 'N', $from ) );
						$filtered_availabilities = array_filter(
							$availabilities,
							function ( $availability ) use ( $current_day ) {
								return $availability->is_all_days() || $current_day === $availability->get_day();
							}
						);
						$available               = true;
						if ( ! $filtered_availabilities ) {
							$has_some_bookable_availability = array_filter(
								$availabilities,
								function ( $availability ) {
									return $availability->is_all_days() || $availability->is_bookable();
								}
							);
							if ( $has_some_bookable_availability ) {
								$available = false;
							}
						}

						foreach ( $filtered_availabilities as $availability ) {
							$test_args = compact( 'min_unit', 'include_time' );
							$test_date = $availability->test_dates( $from, $to, $test_args );
							if ( ! $availability->is_all_days() ) {
								if ( $availability->is_bookable() ) {
									$available = ! ! $test_date;
								} else {
									if ( $test_date ) {
										$available = false;
									}
								}
							} else {
								$available = $available && ( $test_date ? $availability->is_bookable() : ! $availability->is_bookable() );
							}
						}
					} else {
						$availabilities = array_reverse( $availabilities ); // The last rule is more important than the first one.
						foreach ( $availabilities as $availability ) {
							if ( $availability->test_dates( $from, $to, compact( 'min_unit', 'include_time' ) ) ) {
								$available = $availability->is_bookable();
								break;
							}
						}
					}
				}
			}

			return $available;
		}

		/**
		 * Check for availability rules.
		 *
		 * @param int  $from                 The 'from' timestamp.
		 * @param int  $to                   The 'to' timestamp.
		 * @param bool $exclude_time         Set to true to exclude time.
		 * @param bool $default_availability Default availability.
		 *
		 * @return bool
		 */
		public function check_availability_rules( int $from, int $to, bool $exclude_time = false, bool $default_availability = true ): bool {
			$availability_rules = $this->get_availability_rules();

			$tmp_from = $from;
			$tmp_to   = $to - 1; // Remove one second to fix days and months availability (include the last rule day).

			$availability_rules = array_reverse( $availability_rules );
			$duration_unit      = $this->get_duration_unit();
			$min_unit           = in_array( $duration_unit, array( 'month', 'day' ), true ) ? $duration_unit : '';

			$availability_check_args = array(
				'min_unit'     => $min_unit,
				'include_time' => ! $exclude_time,
			);

			foreach ( $availability_rules as $rule ) {
				if ( $rule->is_enabled() && $rule->is_valid() ) {
					$bookable = $rule->is_bookable_in_dates( $tmp_from, $tmp_to, $availability_check_args );
					if ( ! is_null( $bookable ) ) {
						return $bookable;
					}
				}
			}

			return $default_availability;
		}

		/*
		|--------------------------------------------------------------------------
		| Props methods.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Set data.
		 *
		 * @param string $prop The prop to get.
		 *
		 * @return mixed
		 */
		protected function get_prop( string $prop = '' ) {
			return $this->data[ $prop ] ?? null;
		}

		/**
		 * Set prop.
		 *
		 * @param string $prop  The prop.
		 * @param mixed  $value The value.
		 */
		protected function set_prop( string $prop, $value ) {
			$this->data[ $prop ] = $value;
		}

		/**
		 * Set props.
		 *
		 * @param array $props The data to set.
		 */
		protected function set_props( array $props = array() ) {
			foreach ( $props as $prop => $value ) {
				if ( is_null( $value ) ) {
					continue;
				}
				$setter = "set_$prop";

				if ( is_callable( array( $this, $setter ) ) ) {
					$this->{$setter}( $value );
				}
			}
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @return string
		 */
		protected function get_hook_prefix(): string {
			return 'yith_wcbk_availability_' . static::TYPE . '_';
		}

		/**
		 * Prefix for action and filter hooks on data.
		 *
		 * @param string $hook The hook.
		 *
		 * @return string
		 */
		protected function get_hook( string $hook ): string {
			return $this->get_hook_prefix() . $hook;
		}

	}
}
