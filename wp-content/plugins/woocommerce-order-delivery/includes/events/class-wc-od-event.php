<?php
/**
 * Class to handle a calendar event
 *
 * NOTE: Code inspired by the Event class in the demos/ of the fullcalendar.js library.
 *
 * @class   WC_OD_Event
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Event' ) ) {

	class WC_OD_Event {

		/**
		 * Tests whether the given ISO8601 string has a time-of-day or not.
		 * Matches strings like "2013-12-29"
		 *
		 * @since 1.0.0
		 *
		 * @var string The all day regex.
		 */
		public $all_day_regex = '/^\d{4}-\d\d-\d\d$/';

		/**
		 * The event title.
		 *
		 * @since 1.0.0
		 *
		 * @var string The event title.
		 */
		public $title;

		/**
		 * Gets if the event lasts all day or not.
		 *
		 * @since 1.0.0
		 *
		 * @var boolean
		 */
		public $all_day;

		/**
		 * The start date of the event.
		 *
		 * @since 1.0.0
		 *
		 * @var DateTime The start date.
		 */
		public $start;

		/**
		 * The end date of the event.
		 *
		 * @since 1.0.0
		 *
		 * @var DateTime The end date.
		 */
		public $end;

		/**
		 * Additional properties.
		 *
		 * @since 1.0.0
		 *
		 * @var array
		 */
		public $properties = array();


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @param array $args     The event data.
		 * @param mixed $timezone The event timezone.
		 */
		public function __construct( $args, $timezone = null ) {
			$this->title = $args['title'];

			// all_day has been explicitly specified.
			if ( isset( $args['all_day'] ) ) {
				$this->all_day = (bool) $args['all_day'];
			// Guess all_day based off of ISO8601 date strings.
			} else {
				$this->all_day = preg_match( $this->all_day_regex, $args['start'] ) &&
					( ! isset( $args['end'] ) || preg_match( $this->all_day_regex, $args['end'] ) );
			}

			// If dates are all_day, we want to parse them in UTC to avoid DST issues.
			if ( $this->all_day ) {
				$timezone = null;
			}

			// Parse dates.
			$this->start = wc_od_parse_datetime( $args['start'], $timezone );
			$this->end = isset( $args['end'] ) ? wc_od_parse_datetime( $args['end'], $timezone ) : null;

			// Record additional properties.
			foreach ( $args as $name => $value ) {
				if ( ! in_array( $name, array( 'title', 'all_day', 'start', 'end' ) ) ) {
					$this->properties[ $name ] = $value;
				}
			}
		}

		/**
		 * Gets if the event pass all the filters.
		 *
		 *     filters = array(
		 *         'start' => UTC date with 00:00:00 time
		 *         'end'   => UTC date with 00:00:00 time
		 *     )
		 *
		 * @since 1.0.0
		 *
		 * @param array $filters The filters for validate the event.
		 * @return boolean Gets if the event is valid or not.
		 */
		public function is_valid( $filters = array() ) {
			$is_valid = false;

			// Normalize our event's dates for comparison with the all-day range.
			$event_start = wc_od_strip_time( $this->start );
			$event_end = isset( $this->end ) ? wc_od_strip_time( $this->end ) : null;

			// Check if the two ranges intersect.
			if ( $event_end ) {
				$is_valid = $event_start < $filters['end'] && $event_end >= $filters['start'];
			// No end time? Only check if the start is within range.
			} else {
				$is_valid = $event_start < $filters['end'] && $event_start >= $filters['start'];
			}

			return $is_valid;
		}

		/**
		 * Converts this Event object back to a plain data array.
		 *
		 * @since 1.0.0
		 * @return array An array with the event data.
		 */
		public function to_array() {
			// Start with the misc properties
			$args = $this->properties;

			$args['title'] = $this->title;

			// Figure out the date format. This essentially encodes all_day into the date string.
			if ( $this->all_day ) {
				$format = 'Y-m-d'; // output like "2013-12-29"
			} else {
				$format = 'c'; // full ISO8601 output, like "2013-12-29T09:00:00+08:00"
			}

			// Serialize dates into strings
			$args['start'] = $this->start->format( $format );
			if ( isset( $this->end ) ) {
				$args['end'] = $this->end->format( $format );
			}

			return $args;
		}
	}
}