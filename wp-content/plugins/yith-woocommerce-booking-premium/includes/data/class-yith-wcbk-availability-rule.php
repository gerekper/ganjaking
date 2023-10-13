<?php
/**
 * Availability Rule class
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Availability_Rule' ) ) {
	/**
	 * Class YITH_WCBK_Availability_Rule
	 *
	 * @version 2.1.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Availability_Rule extends YITH_WCBK_Availability_Rule_Legacy {
		/**
		 * The object type.
		 *
		 * @var string
		 */
		protected $object_type = 'availability_rule';

		/** Object Version
		 *
		 * @var string
		 */
		private $version = '3.0.0';

		/**
		 * The data.
		 *
		 * @var array
		 */
		protected $data = array(
			'name'           => '',
			'enabled'        => 'yes',
			'type'           => 'generic',
			'date_ranges'    => array(),
			'availabilities' => array(),
			'version'        => '',
		);

		/**
		 * YITH_WCBK_Availability_Rule constructor.
		 *
		 * @param array|YITH_WCBK_Simple_Object|stdClass $args Arguments.
		 */
		public function __construct( $args = array() ) {
			if ( $args instanceof YITH_WCBK_Simple_Object ) {
				$args = $args->to_array();
			} elseif ( is_object( $args ) ) {
				// Handle backward compatibility: in Booking 2.0, availability rules are stored as stdClass objects.
				$args = get_object_vars( $args );
			}
			$args = $this->map_from_old_version( $args );
			parent::__construct( $args );
		}

		/**
		 * Map from old version.
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 */
		private function map_from_old_version( array $args ): array {

			if ( ! isset( $args['version'] ) && isset( $args['type'] ) && in_array( $args['type'], array( 'month', 'custom' ), true ) ) {
				$version = '1.0.0';
			} else {
				$version = $args['version'] ?? '3.0.0';
			}

			if ( version_compare( $version, '3.0.0', '<' ) ) {
				$new_args = array(
					'name'    => $args['name'] ?? '',
					'enabled' => $args['enabled'] ?? 'yes',
					'type'    => isset( $args['type'] ) && 'month' === $args['type'] ? 'generic' : 'specific',
				);

				$from          = $args['from'] ?? '';
				$to            = $args['to'] ?? '';
				$days_enabled  = $args['days_enabled'] ?? 'no';
				$times_enabled = $args['times_enabled'] ?? 'no';
				$bookable      = $args['bookable'] ?? 'yes';
				$days          = $args['days'] ?? array();
				$day_time_from = $args['day_time_from'] ?? array();
				$day_time_to   = $args['day_time_to'] ?? array();

				$days_by_months = yith_wcbk_number_of_days_by_month();
				$availabilities = array();

				if ( $from && $to ) {
					if ( 'generic' === $new_args['type'] ) {
						$from = absint( $from );
						$to   = absint( $to );

						$from = sprintf( '%02d-01', $from ); // Format: month-day.
						$to   = sprintf( '%02d-%02d', $to, $days_by_months[ $to ] );
					}

					$new_args['date_ranges'] = array(
						array(
							'from' => $from,
							'to'   => $to,
						),
					);
				}

				if ( 'yes' === $days_enabled && ! ! $days ) {
					$unique_days_bookable = array_unique( array_values( $days ) );
					$unique_day_time_from = array_unique( array_values( $day_time_from ) );
					$unique_day_time_to   = array_unique( array_values( $day_time_to ) );
					if ( 1 === count( $unique_days_bookable ) && ( 'yes' !== $times_enabled || ( 1 === count( $unique_day_time_from ) && 1 === count( $unique_day_time_to ) ) ) ) {
						$bookable = current( $unique_days_bookable );
						if ( 'disabled' !== $bookable ) {
							$availability = new YITH_WCBK_Availability();
							$availability->set_day( 'all' );
							$availability->set_bookable( $bookable );

							if ( 'yes' === $times_enabled ) {
								$availability->set_time_slots(
									array(
										array(
											'from' => current( $unique_day_time_from ),
											'to'   => current( $unique_day_time_to ),
										),
									)
								);
							}

							$availabilities[] = $availability;
						}
					} else {
						foreach ( $days as $day => $day_bookable ) {
							if ( 'disabled' !== $day_bookable ) {
								$availability = new YITH_WCBK_Availability();
								$availability->set_day( $day );
								$availability->set_bookable( $day_bookable );

								if ( 'yes' === $times_enabled ) {
									$availability->set_time_slots(
										array(
											array(
												'from' => $day_time_from[ $day ],
												'to'   => $day_time_to[ $day ],
											),
										)
									);
								}
								$availabilities[] = $availability;
							}
						}
					}
				} else {
					$availability = new YITH_WCBK_Availability();
					$availability->set_day( 'all' );
					$availability->set_bookable( $bookable );
					$availabilities[] = $availability;
				}

				if ( $availabilities ) {
					$new_args['availabilities'] = $availabilities;
				}

				$new_args['version'] = '3.0.0';

				$args = $new_args;
			}

			$args['version'] = $this->version;

			return $args;
		}

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from the object.
		*/

		/**
		 * Get the name of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_name( string $context = 'view' ): string {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Get the enabled value of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_enabled( string $context = 'view' ): string {
			return $this->get_prop( 'enabled', $context );
		}

		/**
		 * Get the type of the rule
		 *
		 * @param string $context The context.
		 *
		 * @return string
		 */
		public function get_type( string $context = 'view' ): string {
			return $this->get_prop( 'type', $context );
		}

		/**
		 * Get the date ranges
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public function get_date_ranges( string $context = 'view' ): array {
			return $this->get_prop( 'date_ranges', $context );
		}

		/**
		 * Get the date ranges
		 *
		 * @param string $context The context.
		 *
		 * @return YITH_WCBK_Availability[]
		 */
		public function get_availabilities( string $context = 'view' ): array {
			return $this->get_prop( 'availabilities', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Methods for setting data to the object.
		*/

		/**
		 * Set name
		 *
		 * @param string $name The name of the rule.
		 */
		public function set_name( string $name ) {
			$this->set_prop( 'name', $name );
		}

		/**
		 * Set enabled
		 *
		 * @param string|bool $enabled The value to be set.
		 */
		public function set_enabled( $enabled ) {
			$this->set_prop( 'enabled', wc_bool_to_string( $enabled ) );
		}

		/**
		 * Set type
		 *
		 * @param string $type The value to be set.
		 */
		public function set_type( string $type ) {
			$old_types_mapping = array(
				'month'  => 'generic',
				'custom' => 'specific',
			);

			if ( in_array( $type, array_keys( $old_types_mapping ), true ) ) {
				$type = $old_types_mapping[ $type ];
			}

			$allowed_types = array( 'generic', 'specific' );
			$type          = in_array( $type, $allowed_types, true ) ? $type : 'generic';
			$this->set_prop( 'type', $type );
		}

		/**
		 * Set date ranges
		 *
		 * @param array $value The value to set.
		 */
		public function set_date_ranges( array $value ) {
			$this->set_prop( 'date_ranges', is_array( $value ) ? $value : array() );
		}

		/**
		 * Set availabilities
		 *
		 * @param array|YITH_WCBK_Availability[] $value The value to set.
		 */
		public function set_availabilities( array $value ) {
			$value = array_map( 'yith_wcbk_availability', $value );
			$this->set_prop( 'availabilities', $value );
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Is the rule enabled?
		 *
		 * @return bool
		 */
		public function is_enabled(): bool {
			return 'yes' === $this->get_enabled();
		}

		/**
		 * Is a valid rule?
		 *
		 * @return bool
		 */
		public function is_valid(): bool {
			$valid = false;

			foreach ( $this->get_date_ranges() as $range ) {
				if ( ! empty( $range['from'] ) && ! empty( $range['to'] ) ) {
					$valid = true;
					break;
				}
			}

			return $valid && ! ! $this->get_availabilities();
		}

		/*
		|--------------------------------------------------------------------------
		| Useful Methods
		|--------------------------------------------------------------------------
		|
		*/
		/**
		 * Check if it's bookable in a range of dates.
		 * It returns true/false if the dates are related to the rule.
		 * If the rule is not referred to the passed dates, it'll return null.
		 *
		 * @param int   $from The from timestamp.
		 * @param int   $to   The to timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool|null true/false (bookable/non-bookable) if the rule is referred to the passed dates. Null otherwise.
		 */
		public function is_bookable_in_dates( int $from, int $to, array $args = array() ) {
			$test_dates = $this->test_dates( $from, $to, $args );

			if ( $test_dates['apply'] ) {
				return $test_dates['bookable'];
			}

			return null;
		}

		/**
		 * Test dates.
		 *
		 * @param int   $from From timestamp.
		 * @param int   $to   To timestamp.
		 * @param array $args Arguments.
		 *
		 * @return array
		 */
		public function test_dates( int $from, int $to, array $args = array() ): array {
			$defaults = array(
				'min_unit'     => '', // values: month, day, empty string.
				'include_time' => true,
			);

			$args         = wp_parse_args( $args, $defaults );
			$min_unit     = $args['min_unit'];
			$include_time = $args['include_time'];

			$bookable  = true;
			$intersect = false;
			$include   = false;
			$apply     = false;

			foreach ( $this->get_date_ranges() as $range ) {
				if ( $range['from'] && $range['to'] && ! ! $this->get_availabilities() ) {
					$intersect = $intersect || yith_wcbk_date_helper()->check_date_inclusion_in_range( $this->get_type(), $range['from'], $range['to'], $from, $to, true );
					$include   = $include || yith_wcbk_date_helper()->check_date_inclusion_in_range( $this->get_type(), $range['from'], $range['to'], $from, $to, false );
				}
			}

			$availabilities = $this->get_availabilities();

			foreach ( $availabilities as $availability ) {
				/**
				 * Check for is_bookable, since we want to include the "bookable" availability,
				 * to allow enabling 'days' in calendar for timely bookings when there are specific
				 * rules that "opens" time-slots on specific dates [ticket #214858].
				 *
				 * @see BK_Tests_Booking_Product_Availability_Hour::test_enabling_time_slots_through_rules
				 */
				if ( ! $availability->is_bookable() && ! $availability->is_full_day() && ! $include_time ) {
					continue;
				}
				$single_apply = $availability->is_bookable() ? $include : $intersect;
				if ( $single_apply && 'month' !== $min_unit ) {
					$single_apply = $availability->test_dates( $from, $to, compact( 'min_unit', 'include_time' ) );
				}

				if ( $single_apply ) {
					$bookable = $bookable && $availability->is_bookable();
					$apply    = true;
				}
			}

			return compact( 'apply', 'bookable' );
		}

	}
}

if ( ! function_exists( 'yith_wcbk_availability_rule' ) ) {
	/**
	 * Return an availability rule object.
	 *
	 * @param array|YITH_WCBK_Availability_Rule $args Arguments.
	 *
	 * @return YITH_WCBK_Availability_Rule
	 */
	function yith_wcbk_availability_rule( $args ): YITH_WCBK_Availability_Rule {
		return $args instanceof YITH_WCBK_Availability_Rule ? $args : new YITH_WCBK_Availability_Rule( $args );
	}
}
