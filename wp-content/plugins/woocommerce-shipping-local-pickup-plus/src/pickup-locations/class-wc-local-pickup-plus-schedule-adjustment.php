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
 * Local Pickup time adjustment.
 *
 * Helper object to adjust scheduling of a local pickup. It can be used to define
 * a lead time or a pickup deadline for scheduling a purchase order collection.
 *
 * This consists of units (integer) of time (an interval expressed as hours, days,
 * weeks or months). When a pickup location has set a lead time, customers in front
 * end that are scheduling a pickup for that location, they will be unable to choose
 * a slot that is before the lead time has past. When a pickup location has a
 * pickup deadline, the set value will be used as boundary for the calendar until
 * when it's possible to schedule a pickup collection.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Schedule_Adjustment {


	/** @var int ID of the corresponding pickup location */
	private $location_id;

	/** @var string the property ID */
	protected $id;

	/** @var string time amount value */
	protected $value = '';


	/**
	 * Lead time constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $id the identifier of the corresponding property and field
	 * @param string $time_string an amount of time as a string (e.g. "2 days", "3 weeks", "1 month", etc.)
	 * @param int $location_id optional, ID of the corresponding pickup location
	 */
	public function __construct( $id, $time_string = null, $location_id = 0 ) {

		$this->id = $id;

		if ( null !== $time_string ) {
			$this->value = $this->parse_value( $time_string );
		}

		$this->location_id = (int) $location_id;
	}


	/**
	 * Parse and validate a time value.
	 *
	 * @since 2.0.0
	 *
	 * @param string $time_interval lead time to validate
	 * @return string
	 */
	private function parse_value( $time_interval ) {

		$value = '';

		if ( is_string( $time_interval ) ) {

			$pieces = explode( ' ', $time_interval );

			if (    isset( $pieces[1] )
			     && is_numeric( $pieces[0] )
			     && $this->is_valid_interval( $pieces[1] ) ) {

				$value = $time_interval;
			}
		}

		return $value;
	}


	/**
	 * Set the time value.
	 *
	 * @since 2.0.0
	 *
	 * @param int $amount the time amount
	 * @param string $interval the time interval
	 */
	public function set_value( $amount, $interval ) {

		$this->value = $this->parse_value( "{$amount} {$interval}" );
	}


	/**
	 * Get the raw value.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_value() {
		return $this->value;
	}


	/**
	 * Get available time intervals.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $with_data whether to return an associative array with labels and time or just the interval keys. Default false
	 * @return string[]|array[] indexed or associative array
	 */
	private function get_intervals( $with_data = false ) {

		$intervals = array(

			'hours'  => array(
				'label' => __( 'Hour(s)', 'woocommerce-shipping-local-pickup-plus' ),
				'time'  => HOUR_IN_SECONDS,
			),

			'days'   => array(
				'label' => __( 'Day(s)', 'woocommerce-shipping-local-pickup-plus' ),
				'time'  => DAY_IN_SECONDS,
			),

			'weeks'  => array(
				'label' => __( 'Week(s)', 'woocommerce-shipping-local-pickup-plus' ),
				'time'  => WEEK_IN_SECONDS,
			),

			'months' => array(
				'label' => __( 'Month(s)', 'woocommerce-shipping-local-pickup-plus' ),
				'time'  => MONTH_IN_SECONDS,
			),

		);

		// deadline of hours is not a practical setting
		if ( 'deadline' === $this->id ) {
			unset( $intervals['hours'] );
		}

		return true === $with_data ? $intervals : array_keys( $intervals );
	}


	/**
	 * Validate a time interval type.
	 *
	 * @since 2.0.0
	 *
	 * @param string $interval should be: 'hours', 'days', 'weeks' or 'months'
	 * @return bool
	 */
	private function is_valid_interval( $interval ) {
		return in_array( $interval, $this->get_intervals(), true );
	}


	/**
	 * Check whether there is a valid time.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_null() {
		return empty( $this->value );
	}


	/**
	 * Get time amount.
	 *
	 * @since 2.0.0
	 *
	 * @return int|null
	 */
	public function get_amount() {

		$amount = null;

		if ( ! $this->is_null() ) {
			$pieces = explode( ' ', $this->value );
			$amount = (int) $pieces[0];
		}

		return $amount;
	}


	/**
	 * Get the time interval.
	 *
	 * @since 2.0.0
	 *
	 * @return null|string
	 */
	public function get_interval() {

		$interval = null;

		if ( ! $this->is_null() ) {
			$pieces   = explode( ' ', $this->value );
			$interval = $pieces[1];
		}

		return $interval;
	}


	/**
	 * Checks whether an interval is the current interval.
	 *
	 * @since 2.3.5
	 *
	 * @param string|array $interval one or more interval types to check
	 * @return bool
	 */
	public function is_interval( $interval ) {
		return is_array( $interval ) ? in_array( $this->get_interval(), $interval, false ) : $interval === $this->get_interval();
	}


	/**
	 * Get the time in seconds.
	 *
	 * @since 2.0.0
	 *
	 * @return int timestamp
	 */
	public function in_seconds() {

		$seconds   = 0;
		$interval  = $this->get_interval();
		$intervals = $this->get_intervals( true );

		if ( isset( $intervals[ $interval ]['time'] ) ) {
			$seconds = (int) $this->get_amount() * $intervals[ $interval ]['time'];
		}

		return $seconds;
	}


	/**
	 * Returns the time in days.
	 *
	 * This can be either an absolute value (default) or a difference between the days between two dates (with optional argument).
	 *
	 * @since 2.3.5
	 *
	 * @return int
	 * @param int|string $relative_time optional time to calculate the days from, if null the days will be absolute
	 */
	public function in_days( $relative_time = 0 ) {

		if ( is_numeric( $relative_time ) ) {
			$relative_time = (int) $relative_time;
		} elseif ( is_string( $relative_time ) ) {
			$relative_time = (int) strtotime( $relative_time );
		}

		if ( $relative_time > 0 ) {
			$offset = $relative_time + $this->in_seconds();
			// years are likely to match and this will be 0, yet over engineering this to rule out a remote bug possibility
			$years  = max( 0, ( (int) date( 'Y', $offset ) - (int) date( 'Y', $relative_time ) ) * 365 );
			$days   = ( (int) date( 'z', $offset ) + $years ) - (int) date( 'z', $relative_time );
		} else {
			$days   = (int) floor( $this->in_seconds() / DAY_IN_SECONDS );
		}

		return max( 0, $days );
	}


	/**
	 * Get a time input field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of input field arguments
	 * @return string HTML
	 */
	public function get_field_html( array $args ) {

		$args = wp_parse_args( $args, array(
			'name'     => '',
			'disabled' => false,
		) );

		if ( empty( $args['name'] ) || ! is_string( $args['name'] ) ) {
			return '';
		}

		ob_start();

		?>
		<div class="wc-local-pickup-plus-field wc-local-pickup-plus-schedule-adjustment-field <?php echo sanitize_html_class( "wc-local-pickup-plus-{$this->id}-field" ); ?>">
			<input
				type="number"
				id="<?php echo esc_attr( $args['name'] . '_amount' ); ?>"
				name="<?php echo esc_attr( $args['name'] . '_amount' ); ?>"
				value="<?php echo max( 0, (int) $this->get_amount() ); ?>"
				style="max-width: 48px; text-align: right;"
				placeholder="0"
				step="1"
				min="0"
				<?php disabled( $args['disabled'], true, true ); ?>
			/>
			<select
				name="<?php echo esc_attr( $args['name'] . '_interval' ); ?>"
				id="<?php echo esc_attr( $args['name'] . '_interval' ); ?>"
				class="select wc-local-pickup-plus-dropdown"
				<?php disabled( $args['disabled'], true, true ); ?>>
				<?php $selected_interval = $this->get_interval(); ?>
				<?php foreach ( $this->get_intervals( true ) as $interval => $data ) : ?>
					<option value="<?php echo esc_attr( $interval ); ?>" <?php selected( $interval, $selected_interval, true ); ?>><?php echo strtolower( esc_html( $data['label'] ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php echo ! empty( $args['desc_tip'] ) ? wc_help_tip( $args['desc_tip'] ) : ''; ?>
		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Output a time adjustment input field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args array of arguments
	 */
	public function output_field_html( array $args ) {

		echo $this->get_field_html( $args );
	}


}
