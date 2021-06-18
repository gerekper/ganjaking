<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Pickup location typical business hours for order collection.
 *
 * This class handles the "opening hours" when a customer can schedule a local
 * pickup at the corresponding location. Each location can have its own schedule
 * or a default global setting may be used. Customers will only be able to choose
 * among days and time slots as defined in the business hours.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Business_Hours {


	/** @var int ID of the corresponding pickup location */
	private $location_id;

	/** @var array associative array with pickup availability schedule */
	private $schedule = array();

	/** @var int starting day of the week as numerical entity (0 = Sunday, 6 = Saturday, default 1 = Monday) */
	private $start_of_week;


	/**
	 * Business hours constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pickup_availability_schedule the availability schedule
	 * @param int $location_id optional, ID of the corresponding pickup location
	 */
	public function __construct( $pickup_availability_schedule = array(), $location_id = 0 ) {

		$this->start_of_week = (int) get_option( 'start_of_week', 1 );

		if ( ! empty( $pickup_availability_schedule ) ) {
			$this->schedule = $this->parse_schedule( $pickup_availability_schedule );
		}

		$this->location_id = (int) $location_id;
	}


	/**
	 * Parse business hours to weekday schedule.
	 *
	 * @since 2.0.0
	 *
	 * @param array $pickup_schedule business hours to parse
	 * @return array validated schedule
	 */
	private function parse_schedule( $pickup_schedule ) {

		$week = array();

		for ( $day = 0; $day <= 6; $day++ ) {

			if ( isset( $pickup_schedule[ $day ] ) && is_array( $pickup_schedule[ $day ] ) ) {

				foreach ( $pickup_schedule[ $day ] as $start => $end ) {

					if ( is_numeric( $start ) && is_numeric( $end ) ) {
						$week[ $day ][ $start ] = $end;
					}
				}
			}

			if ( empty( $week[ $day ] ) ) {
				$week[ $day ] = array();
			}
		}

		// sort days (array keys) according to the set start day of the week.
		uksort( $week, array( $this, 'sort_days_by_start_of_week' ) );

		return $week;
	}


	/**
	 * Set a new schedule.
	 *
	 * @since 2.0.0
	 *
	 * @param array $value schedule that will be parsed first, then set in the current object property
	 */
	public function set_schedule( array $value ) {

		$this->schedule = $this->parse_schedule( $value );
	}


	/**
	 * Get schedule as raw value.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_value() {
		return $this->schedule;
	}


	/**
	 * Get schedule for a given day.
	 *
	 * @since 2.0.0
	 *
	 * @param int|null $day day to get schedule for (returns the raw schedule if null)
	 * @param bool $one_line optional: whether to format the output schedule in a single string (true) instead of an array (false), default false
	 * @param int|null optional: if a timestamp is specified will skip previous slots (used to offset business hours based on other parameters, lead time, etc.)
	 * @return array|string
	 */
	public function get_schedule( $day = null, $one_line = false, $minimum_hours = null ) {

		$schedule = $this->get_value();

		if ( is_numeric( $day ) && isset( $schedule[ $day ] ) ) {

			$opening_hours = (array) $schedule[ (int) $day ];
			$schedule      = array();

			if ( ! empty( $opening_hours ) ) {

				$time_format = wc_time_format();

				foreach ( $opening_hours as $time_start => $time_end ) {

					if ( null !== $minimum_hours ) {
						if ( $minimum_hours > 0 && (int) $time_end <= $minimum_hours ) {
							continue;
						} elseif ( $minimum_hours > (int) $time_start ) {
							$time_start = $minimum_hours;
						}
					}

					if ( $time_start === $time_end ) {
						$schedule[] = date_i18n( $time_format, $time_start );
					} else {
						/* translators: Placeholders: %1$s - %2$s opening hours as time from-to */
						$schedule[] = sprintf( __( 'from %1$s to %2$s', 'woocommerce-shipping-local-pickup-plus' ), date_i18n( $time_format, $time_start ), date_i18n( $time_format, $time_end ) );
					}
				}
			}

			if ( true === $one_line ) {
				if ( ! empty( $schedule ) ) {
					/* translators: Conjunction used to join together the penultimate and last item of a list of opening hours for pickup. */
					array_splice( $schedule, -2, 2, implode( ' ' . __( 'and', 'woocommerce-shipping-local-pickup-plus' ) . ' ', array_slice( $schedule, -2, 2 ) ) );
					$schedule = implode( ', ', $schedule );
				} else {
					$schedule = '';
				}
			}
		}

		return $schedule;
	}


	/**
	 * Whether there are opening hours set.
	 *
	 * @since 2.0.0
	 *
	 * @param null|int $day optional, day of the week in 'w' format (from 0 to 6) or null to check if there's availability for any day of the week
	 * @param array $hours optional, a time range as an associative array to check whether the schedule matches certain times within the specified day
	 * @return bool
	 */
	public function has_schedule( $day = null, $hours = array() ) {

		$has_schedule = false;
		$schedule     = array();
		$from_time    = ! empty( $hours ) ? max( 0,              (int) key( $hours ) )     : 0;              // beginning of the day
		$within_time  = ! empty( $hours ) ? min( DAY_IN_SECONDS, (int) current( $hours ) ) : DAY_IN_SECONDS; // end of the day (24 hr)

		// when a day is specified we check if there's any slots for that day to begin with (otherwise the outcome will return false)
		if ( is_numeric( $day ) ) {
			$day              = (int) $day;
			$schedule[ $day ] = isset( $this->schedule[ $day ] ) ? $this->schedule[ $day ] : array();
		} else {
			$schedule         = $this->schedule;
		}

		// loop time ranges per day and check if they are within the specified $time ranges we need to check for (default range is any)
		if ( ! empty( $schedule ) ) {

			foreach ( $schedule as $times ) {

				if ( ! empty( $times ) && is_array( $times ) ) {

					foreach ( $times as $start_time => $end_time ) {

						if ( $end_time >= $from_time && $start_time <= $within_time ) {

							$has_schedule = true;
							break;
						}
					}
				}
			}
		}

		return $has_schedule;
	}


	/**
	 * Get available slots for the business hours
	 *
	 * @since 2.0.0
	 *
	 * @param int $day day to retrieve available slots for
	 * @param string $format format to return the slots (default site time format)
	 * @param float $fraction fraction of hours (default 0.25 or 15 minutes) to return slots for
	 * @return int[]|string[] array of timestamps or strings according to specified $format
	 */
	public function get_available_slots( $day, $format = null, $fraction = 0.25 ) {

		$slots = array();

		if ( ! is_array( $this->schedule ) || empty( $this->schedule[ $day ] ) ) {
			return $slots;
		}

		if ( null === $format ) {
			$format = wc_time_format();
		}

		for ( $t = 0; $t <= DAY_IN_SECONDS; $t += $fraction * HOUR_IN_SECONDS ) {

			foreach ( $this->schedule[ $day ] as $start => $end ) {

				if ( $t >= $start && $t <= $end ) {

					$slots[ $t ] = date_i18n( $format, $t );

					continue;
				}
			}
		}

		return $slots;
	}


	/**
	 * Convert a numerical representation of a day of the week to its name as a string.
	 *
	 * Useful to convert a number to an entity that `strtotime()` can understand.
	 *
	 * @since 2.0.0
	 *
	 * @param int $day must be an integer between 0 (Sunday) and 6 (Saturday)
	 * @return string|null day of the week or null on error
	 */
	protected function get_day_of_week_name( $day ) {

		$days_of_the_week = array(
			0 => 'Sunday',
			1 => 'Monday',
			2 => 'Tuesday',
			3 => 'Wednesday',
			4 => 'Thursday',
			5 => 'Friday',
			6 => 'Saturday',
		);

		return isset( $days_of_the_week[ (int) $day ] ) ? $days_of_the_week[ (int) $day ] : null;
	}


	/**
	 * Get a business hours input field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args an array of arguments
	 * @return string HTML
	 */
	public function get_field_html( array $args ) {

		$args = wp_parse_args( $args, array(
			'name' => '',
		) );

		if ( empty( $args['name'] ) || ! is_string( $args['name'] ) ) {
			return '';
		}

		$schedule = $this->get_value();

		ob_start();
		?>
		<div class="wc-local-pickup-plus-field wc-local-pickup-plus-business-hours-field" data-field-name="<?php echo esc_attr( $args['name'] ); ?>">

				<?php // rather than doing for loop, distribute days according start of week user setting: ?>
				<?php $days_of_week = array_keys( array_fill( 0, 7, '' ) ); ?>
				<?php uksort( $days_of_week, array( $this, 'sort_days_by_start_of_week' ) ); ?>

				<?php foreach ( $days_of_week as $d ) : ?>

					<div class="wc-local-pickup-plus-business-hours-day-of-week" data-day-of-week="<?php echo esc_attr( $d ); ?>">

						<label class="wc-local-pickup-plus-business-hours-day-of-week-name"><?php echo esc_html( date_i18n( 'l', strtotime( $this->get_day_of_week_name( $d ) ) ) ); ?></label>

						<ul class="wc-local-pickup-plus-business-hours-day-of-week-schedule">
							<?php if ( isset( $schedule[ $d ] ) ) : ?>

								<?php foreach ( $schedule[ $d ] as $start => $end ) : ?>

									<li><?php echo $this->get_time_range_picker_input_html( array(
											'name'           =>  $args['name'] . '_' . $d,
											'selected_start' => $start,
											'selected_end'   => $end, )
										); ?></li>

								<?php endforeach; ?>

							<?php endif; ?>

							<button class="button button-primary wc-local-pickup-plus-business-hours-set" <?php echo ! empty( $schedule[ $d ] ) ? 'style="display:none;"' : ''; ?>><?php esc_html_e( 'Set', 'woocommerce-shipping-local-pickup-plus' ); ?></button>
						</ul>

					</div>

				<?php endforeach; ?>

				<?php // the following elements act as void column cells: ?>
				<div class="wc-local-pickup-plus-business-hours-day-of-week"></div>
				<div class="wc-local-pickup-plus-business-hours-day-of-week"></div>

			<div class="clear"></div>
		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Output a business hours input field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args
	 */
	public function output_field_html( array $args ) {
		echo $this->get_field_html( $args );
	}


	/**
	 * Get a time range picker input.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args an array of arguments
	 * @return string
	 */
	public function get_time_range_picker_input_html( array $args ) {

		$args = wp_parse_args( $args, array(
			'name'           => '',
			'selected_start' => null,
			'selected_end'   => null,
		) );

		if ( empty( $args['name'] ) || ! is_string( $args['name'] ) ) {
			return '';
		}

		ob_start();

		?>
		<div class="wc-local-pickup-plus-field wc-local-pickup-plus-time-range-picker-field">

			<select
				class="start"
				name="<?php echo esc_attr( $args['name'] . '_start[]' ); ?>">
				<?php for ( $t = 0; $t <= DAY_IN_SECONDS; $t += 0.25 * HOUR_IN_SECONDS ) : ?>
					<option value="<?php echo $t; ?>" <?php selected( $t, (int) $args['selected_start'], true ); ?>><?php echo esc_html( date_i18n( wc_time_format(), $t ) ); ?></option>
				<?php endfor; ?>
			</select>

			<select
				class="end"
				name="<?php echo esc_attr( $args['name'] . '_end[]' ); ?>">
				<?php for ( $t = 0; $t <= DAY_IN_SECONDS; $t += 0.25 * HOUR_IN_SECONDS ) : ?>
					<option value="<?php echo $t; ?>" <?php selected( $t, (int) $args['selected_end'], true ); ?>><?php echo esc_html( date_i18n( wc_time_format(), $t ) ); ?></option>
				<?php endfor; ?>
			</select>

			<div class="buttons">
				<button class="button button-secondary wc-local-pickup-plus-business-hours-add"><span class="dashicons dashicons-plus"></span></button>
				<button class="button button-secondary wc-local-pickup-plus-business-hours-remove"><span class="dashicons dashicons-minus"></span></button>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Output a time picker input field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of arguments
	 */
	public function output_time_range_picker_input_html( array $args ) {

		echo $this->get_time_range_picker_input_html( $args );
	}


	/**
	 * Helper method to get a normalized business hours array from field data.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field_name field name
	 * @param array $field_data field posted data
	 * @return array
	 */
	public function get_field_value( $field_name, $field_data ) {

		$week = array();

		if ( ! empty( $field_data ) ) {

			for ( $day = 0; $day <= 6; $day++ ) {

				$slot       = $field_name . '_' . $day;
				$slot_start = $slot . '_start';
				$slot_end   = $slot . '_end';

				if ( ! empty( $field_data[ $slot_start ] ) && ! empty( $field_data [ $slot_end ] ) ) {

					$slots = count( $field_data[ $slot_start ] );

					if ( $slots > 0 ) {
						for ( $i = 0; $i < $slots; $i ++ ) {
							if ( isset( $field_data[ $slot_start ][ $i ], $field_data[ $slot_end ][ $i ] ) ) {
								$week[ (string) $day ][ (int) $field_data[ $slot_start ][ $i ] ] = (int) $field_data[ $slot_end ][ $i ];
							}
						}
					}
				}

				if ( ! isset( $week[ (string) $day ] ) ) {
					$week[ (string) $day ] = array();
				}
			}
		}

		return $week;
	}


	/**
	 * Sorts days according to the set starting day of the week.
	 *
	 * @since 2.0.0
	 *
	 * @param int $day_1 first day to compare as an integer representation
	 * @param int $day_2 second day to compare as an integer representation
	 * @return int 1 if any day is greater than start of week or -1
	 */
	private function sort_days_by_start_of_week( $day_1, $day_2 ) {

		return $this->start_of_week > (int) $day_1 || $this->start_of_week > (int) $day_2 ? 1 : -1;
	}


}
