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

use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

/**
 * Pickup Location holidays calendar.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Public_Holidays {


	/** @var int ID of the corresponding pickup location */
	private $location_id;

	/** @var int[] array of dates as timestamps representing a closure days calendar */
	private $calendar;


	/**
	 * Closure days calendar constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string[]|int[] $dates indexed array of dates as n-j dates or timestamps
	 * @param int $location_id optional, ID of the corresponding pickup location
	 */
	public function __construct( $dates = array(), $location_id = 0 ) {

		$this->calendar    = $this->parse_calendar( $dates );
		$this->location_id = (int) $location_id;
	}


	/**
	 * Parse calendar values.
	 *
	 * @since 2.0.0
	 *
	 * @param string[]|int[] $dates array of dates in n-j format or timestamps
	 * @return string[] parsed values
	 */
	private function parse_calendar( array $dates ) {

		$parsed_values = array();

		foreach ( $dates as $date ) {

			if ( is_numeric( $date ) ) {

				$parsed_values[] = date( 'n-j', $date );

			} elseif ( is_string( $date ) ) {

				// a date in a format different than n-j might be from an import.
				if ( $parsed_date = strtotime( $date ) ) {
					$date = date( 'n-j', $parsed_date );
				}

				$date = $date ? explode( '-', $date ) : array();

				if ( empty( $date[0] ) || empty( $date[1] ) ) {
					continue;
				}

				if ( $date[0] >= 1 && $date[0] <= 12 && $date[1] >= 1 && $date[1] <= 31 ) {
					$parsed_values[] = str_pad( $date[0], 2, '0', STR_PAD_LEFT ) . '-' . str_pad( $date[1], 2, '0', STR_PAD_LEFT );
				}
			}
		}

		return $parsed_values;
	}


	/**
	 * Set a calendar.
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $calendar array of dates in n-j format
	 */
	public function set_calendar( array $calendar ) {

		$this->calendar = $this->parse_calendar( $calendar );
	}


	/**
	 * Whether there is calendar data.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_calendar() {

		$calendar = $this->get_calendar();

		return ! empty( $calendar );
	}


	/**
	 * Returns the calendar data in array form.
	 *
	 * @since 2.0.0
	 *
	 * @param int|string|null $start_date optional start date to get dates for (defaults to 1st January 1980, a convenient leap year)
	 * @param int|string|null $end_date optional end date to get dates for (default to one year from the start date)
	 * @return array|int[] array of dates as timestamps for the specified range, the keys will be ordered by YYYY-MM
	 */
	public function get_calendar( $start_date = null, $end_date = null ) {

		if ( is_numeric( $start_date ) ) {
			$start_date = (int) $start_date;
		} elseif ( is_string( $start_date ) ) {
			$start_date = strtotime( $start_date );
		}

		if ( is_numeric( $end_date ) ) {
			$end_date = (int) $end_date;
		} elseif ( is_string( $end_date ) ) {
			$end_date = strtotime( $end_date );
		}

		if ( ! $start_date ) {
			$start_date = strtotime( '1980-01-01' );
		}

		if ( ! $end_date ) {
			$end_date   = strtotime( 'next year', $start_date ) - 1;
		} elseif ( $end_date < $start_date ) {
			$end_date   = $start_date;
		}

		$start_year = (int) date( 'Y', $start_date );
		$end_year   = (int) date( 'Y', $end_date );
		$dates      = $this->calendar;
		$calendar   = array();

		for ( $year = $start_year; $year <= $end_year; $year++ ) {

			if ( ! empty( $dates ) ) {

				foreach ( $dates as $date ) {

					$timestamp = strtotime( $year . '-' . $date );

					if ( $timestamp && $timestamp >= $start_date && $timestamp <= $end_date ) {
						$calendar[ $year . '-' . $date ] = $timestamp;
					}
				}
			}
		}

		return $calendar;
	}


	/**
	 * Get calendar dates.
	 *
	 * @since 2.0.0
	 *
	 * @param string $format optional: PHP date format, default n-j, e.g. 1-1 for January 01, 31-12 for December 31
	 * @param string|int $start_date optional: the start date or timestamp to get dates from. Defaults to 01-01-1980 (a convenience leap year which we use to extract general dates without year information)
	 * @param string|int $end_date optional: the end date or timestamp to get dates to. Defaults to one year from start date
	 * @return string[] array of dates
	 */
	public function get_calendar_dates( $format = 'n-j', $start_date = null, $end_date = null ) {

		$dates    = array();
		$format   = 'mysql' === $format ? 'Y-m-d H:i:s' : $format;
		$calendar = $this->get_calendar( $start_date, $end_date );

		if ( ! empty( $calendar ) ) {
			foreach ( $calendar as $timestamp ) {
				$dates[ $timestamp ] = 'timestamp' === $format ? $timestamp : date( $format, $timestamp );
			}
		}

		return $dates;
	}


	/**
	 * Checks whether a date falls within the public holidays calendar.
	 *
	 * @since 2.3.5
	 *
	 * @param \DateTime|string|int $date DateTime object, timestamp, or date in PHP format
	 * @return bool
	 */
	public function is_public_holiday( $date ) {

		$is_holiday = false;

		if ( $date instanceof \DateTime ) {

			$is_holiday = in_array( $date->format( 'n-j' ), array_values( $this->get_calendar_dates() ), false );

		} else {

			if ( is_numeric( $date ) ) {
				$date = (int) $date;
			} elseif ( is_string( $date ) ) {
				$date = strtotime( $date );
			}

			if ( is_int( $date ) ) {
				$is_holiday = in_array( date( 'n-j', $date ), array_values( $this->get_calendar_dates() ), false );
			}
		}

		return $is_holiday;
	}


	/**
	 * Get raw dates.
	 *
	 * @since 2.0.0
	 *
	 * @return string[]
	 */
	public function get_value() {
		return $this->calendar;
	}


	/**
	 * Get calendar table HTML for the admin input field.
	 *
	 * @see \WC_Local_Pickup_Plus_Public_Holidays::get_field_html()
	 *
	 * @since 2.0.0
	 *
	 * @param array $chosen_dates selected default dates
	 * @return string HTML
	 */
	private function get_calendar_table_html( array $chosen_dates ) {

		$html = '';
		$html .= '<table>';

		// table Head with navigation and months.
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th colspan="1" class="nav prev-month" data-value="12"><span class="dashicons dashicons-arrow-left-alt2"></span></th>';

		for ( $month = 1; $month <= 12; $month++ ) {
			$hidden = $month > 1 ? ' style="display: none;"' : '';
			$html .= '<th colspan="5" class="month" data-value="' . $month . '" ' . $hidden .'>' . date_i18n( 'F', strtotime( '1980-' . $month . '-01' ) ) . '</th>';
		}

		$html .= '<th colspan="1" class="nav next-month" data-value="2"><span class="dashicons dashicons-arrow-right-alt2"></span></th>';
		$html .= '</tr>';
		$html .= '</thead>';

		// multiple table bodies with individual months
		for ( $month = 1; $month <= 12; $month++ ) {

			$hidden = $month > 1 ? ' style="display: none;" ' : '';

			$html .= '<tbody class="month" data-value="' . $month . '" ' . $hidden . '>';

			$html .= '<tr class="week">';

			for ( $week_day = 1; $week_day <= 7; $week_day ++ ) {

				$week_day_text = date_i18n( 'D', strtotime( 'Sunday +' . $week_day . ' days' ) );

				$html .= '<td class="void">' . $week_day_text . '</td>';
			}

			$html .= '</tr>';

			$week_starts   = 1;
			$week_ends     = 7;
			// "1980" is used a generic leap year, years are not considered in this calendar
			$days_in_month = $this->get_number_of_days_in_month( $month, 1980 );

			for ( $day = 1; $day <= $days_in_month; $day++ ) {

				if ( 1 === $week_starts ) {
					$html .= '<tr class="week">';
					$week_ends = 6;
				}

				// fill the days before the first day
				if ( 1 === $day ) {
					$week_day = date( 'N', strtotime( date( 'Y-' . $month . '-' . $day ) ) );
					for ( $skip_day = 1; $skip_day < $week_day; $skip_day ++ ) {
						$html .= '<td class="void">&nbsp;</td>';
						$week_starts++;
						$week_ends--;
					}
				}

				$value = $month . '-' . $day;
				$class = in_array( $value, $chosen_dates, true ) ? ' class="day selected" ' : ' class="day" ';

				$html .= '<td ' . $class . ' data-value="' . $value . '">' . $day . '</td>';

				if ( 0 === $week_ends ) {
					$html .= '</tr>';
					$week_starts = 0;
				}

				$week_starts++;
				$week_ends--;
			}

			// complete the row
			while ( $week_ends >= 0 ) {
				$html .= '<td class="void"></td>';
				$week_ends --;
			}

			// place UI shortcuts in a new row
			$html .= '<tr class="week">';
			$html .= '<td class="void"></td>';
			$html .= '<td class="void"></td>';
			$html .= '<td class="void jump first-month" title="' . esc_html__( 'Go to January', 'woocommerce-shipping-local-pickup-plus' ) . '"><span class="dashicons dashicons-controls-skipback"></span></td>';
			/* translators: Placeholder: %s - today's month */
			$html .= '<td class="void jump today" data-month="' . (int) date( 'n', current_time( 'timestamp' ) ) .'" title="' . sprintf( esc_html__( 'Go to %s', 'woocommerce-shipping-local-pickup-plus' ), date_i18n( 'F', current_time( 'timestamp' ) ) ) . '"><span class="dashicons dashicons-marker"></span></td>';
			$html .= '<td class="void jump last-month" title="' . esc_html__( 'Go to December', 'woocommerce-shipping-local-pickup-plus' ) . '"><span class="dashicons dashicons-controls-skipforward"></span></td>';
			$html .= '<td class="void"></td>';
			$html .= '<td class="void"></td>';
			$html .= '</tr>';

			$html .= '</tbody>';
		}

		$html .= '</table>';

		return $html;
	}


	/**
	 * Get calendar days in a given month.
	 *
	 * This method offers an alternative fallback for PHP installation lacking of calendar support.
	 *
	 * @since 2.0.0
	 *
	 * @param int $month month (1-12)
	 * @param int $year year (yyyy)
	 * @return int
	 */
	private function get_number_of_days_in_month( $month, $year ) {

		if ( defined( 'CAL_GREGORIAN' ) && function_exists( 'cal_days_in_month' ) ) {
			$days = cal_days_in_month( CAL_GREGORIAN, $month, 1980 );
		} else {
			$days = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
		}

		return is_numeric( $days ) ? (int) $days : 30;
	}


	/**
	 * Determine which day-month format to use according to the site date format.
	 *
	 * @see \WC_Local_Pickup_Plus_Public_Holidays::get_field_html()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_date_format() {

		$format      = wc_date_format();
		$length      = strlen( $format );
		$day_flags   = array( 'd', 'j' );
		$day_pos     = $length;
		$month_flags = array( 'F', 'm', 'M', 'n ');
		$month_pos   = $length;

		foreach ( $day_flags as $flag ) {
			$pos = strpos( $format, $flag );
			if ( false !== $pos && $pos < $day_pos ) {
				$day_pos = $pos;
			}
		}

		foreach ( $month_flags as $flag ) {
			$pos = strpos( $format, $flag );
			if ( false !== $pos && $pos < $day_pos ) {
				$month_pos = $pos;
			}
		}

		if ( $day_pos > $month_pos ) {
			$format = 'F j';
		} else {
			$format = 'j F';
		}

		return $format;
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

		$calendar_dates = $this->get_calendar_dates();

		ob_start();
		?>
		<div class="wc-local-pickup-plus-field wc-local-pickup-plus-public-holidays-field">

			<div class="calendar">
				<?php echo $this->get_calendar_table_html( $calendar_dates ); ?>
			</div>

			<div class="dates">
				<select
					id="<?php echo esc_attr( $args['name'] ); ?>"
					name="<?php echo esc_attr( $args['name'] ); ?>[]"
					style="width: 100%;"
					multiple="multiple"
					data-placeholder="<?php esc_attr_e( 'Choose dates&hellip;', 'woocommerce-shipping-local-pickup-plus' ); ?>">
					<?php $format = $this->get_date_format(); ?>
					<?php for ( $d = 0; $d < 366; $d++ ) : ?>
						<?php $time  = strtotime( "+{$d} days", strtotime( '1980-01-01' ) ); ?>
						<?php $value = date( 'n-j', $time ); ?>
						<?php $label = date_i18n( $format, $time ); ?>
						<option
							value="<?php echo $value; ?>"
							<?php selected( in_array( $value, $calendar_dates, true ), true, true  );
							?>><?php echo esc_html( $label ); ?></option>
					<?php endfor; ?>
				</select>
			</div>

		</div>
		<div class="clear"></div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Output a business hours input field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of arguments
	 */
	public function output_field_html( array $args ) {

		echo $this->get_field_html( $args );
	}


}
