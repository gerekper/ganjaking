<?php
/**
 * Class WC_Global_Availability
 *
 * @package WooCommerce/Bookings
 */

/**
 * Class WC_Global_Availability
 */
class WC_Global_Availability extends WC_Bookings_Data implements ArrayAccess {

	const DATA_STORE = 'booking-global-availability';
	const SECONDS_IN_A_MINUTE = 60;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'booking_global_availability';

	protected $cache_group = 'booking-global-availability';

	/**
	 * Data array.
	 *
	 * @var array
	 */
	protected $data = array(
		'gcal_event_id' => '',
		'title'         => '',
		'range_type'    => 'custom',
		'from_date'     => '',
		'to_date'       => '',
		'from_range'    => '',
		'to_range'      => '',
		'bookable'      => 'yes',
		'priority'      => 10,
		'ordering'      => 0,
		'date_created'  => '',
		'date_modified' => '',
		'rrule'         => '',
	);

	/**
	 * Constructor.
	 *
	 * @param int|object|array $id Id.
	 *
	 * @throws Exception When validation fails.
	 */
	public function __construct( $id = 0 ) {
		parent::__construct( $id );

		if ( is_numeric( $id ) && $id > 0 ) {
			$this->set_id( $id );
		} elseif ( $id instanceof self ) {
			$this->set_id( $id->get_id() );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( self::DATA_STORE );

		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Get created date.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get modified date.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return WC_DateTime|null Object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Get title.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_title( $context = 'view' ) {
		return $this->get_prop( 'title', $context );
	}

	/**
	 * Get google calendar event id.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_gcal_event_id( $context = 'view' ) {
		return $this->get_prop( 'gcal_event_id', $context );
	}

	/**
	 * Get Range Type.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_range_type( $context = 'view' ) {
		return $this->get_prop( 'range_type', $context );
	}

	/**
	 * Get From Date
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_from_date( $context = 'view' ) {
		return $this->get_prop( 'from_date', $context );
	}

	/**
	 * Get to Date.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_to_date( $context = 'view' ) {
		return $this->get_prop( 'to_date', $context );
	}

	/**
	 * Get From Range.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_from_range( $context = 'view' ) {
		return $this->get_prop( 'from_range', $context );
	}

	/**
	 * Get To Range.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_to_range( $context = 'view' ) {
		return $this->get_prop( 'to_range', $context );
	}

	/**
	 * Get Bookable. 'yes' or 'no'.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_bookable( $context = 'view' ) {
		return $this->get_prop( 'bookable', $context );
	}

	/**
	 * Get Priority.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_priority( $context = 'view' ) {
		return $this->get_prop( 'priority', $context );
	}

	/**
	 * Get RRULE.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_rrule( $context = 'view' ) {
		return $this->get_prop( 'rrule', $context );
	}

	/**
	 * Get Ordering.
	 *
	 * @param  string $context What the value is for.
	 *                          Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_ordering( $context = 'view' ) {
		return $this->get_prop( 'ordering', $context );
	}

	/**
	 * Get whether or not availability rule applies to full days.
	 *
	 * @return bool True if availability rule affects full day.
	 */
	public function is_all_day() {
		// 'custom' type is a date range, so they will always be all day.
		return ( 'custom' === $this->get_range_type() );
	}

	/**
	 * Get whether or not it's a store availability.
	 *
	 * @return bool True if it's a store availability.
	 */
	public function is_store_availability() {
		return ( 'store_availability' === $this->get_range_type() );
	}

	/**
	 * Get start and end times for global availability rule.
	 *
	 * @param int $date Timestamp of beginning of the day to check.
	 *
	 * @return null|array {
	 *   int $start  Timestamp of start time.
	 *   int $end    Timestamp of end time.
	 */
	public function get_time_range_for_date( $date ) {
		$rule_array   = WC_Product_Booking_Rule_Manager::process_availability_rules( array( $this ), 'global', false );
		$rule         = $rule_array[0];
		$minute_data  = WC_Product_Booking_Rule_Manager::get_rule_minute_range( $rule, $date );
		$minutes      = $minute_data['minutes'];

		if ( ( false === $minute_data['is_bookable'] ) && count( $minutes ) > 0 ) {
			$start_minute = $minutes[0];
			$end_minute   = end( $minutes );

			return array(
				'start' => $date + $start_minute * self::SECONDS_IN_A_MINUTE,
				'end'   => $date + $end_minute * self::SECONDS_IN_A_MINUTE,
			);
		}
		return null;
	}

	/**
	 * Helper method that returns formatted date.
	 *
	 * @param int    $date_ts Timestamp to be formatted to date.
	 * @param string $date_format Format string for date.
	 * @param string $time_format Format string for time.
	 *
	 * @return string Date formatted via date_i18n
	 */
	public function get_formatted_date( $date_ts = null, $date_format= null, $time_format = null ) {
		if ( is_null( $date_format ) ) {
			$date_format = apply_filters( 'woocommerce_bookings_date_format', wc_bookings_date_format() );
		}
		if ( is_null( $time_format ) ) {
			$time_format = apply_filters( 'woocommerce_bookings_time_format', ', ' . wc_bookings_time_format() );
		}

		if ( $this->is_all_day() ) {
			return date_i18n( $date_format, $date_ts );
		} else {
			return date_i18n( $date_format . $time_format, $date_ts );
		}
	}

	/**
	 * For multi-day events, check if the timestamp occurs after 00:00.
	 *
	 * @param int $start_ts Timestamp.
	 *
	 * @return bool True if date starts during the day.
	 */
	public function date_starts_today( $start_ts ) {
		if ( 'custom:daterange' === $this->get_range_type() ) {
			if ('00:00' === date_i18n( 'H:i', $start_ts ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Set Google calendar event id.
	 *
	 * @param string $gcal_event_id Google calendar event id.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_gcal_event_id( $gcal_event_id ) {
		$this->set_prop( 'gcal_event_id', $gcal_event_id );

		return $this;
	}

	/**
	 * Set Title
	 *
	 * @param string $title Title.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_title( $title ) {
		$this->set_prop( 'title', $title );

		return $this;
	}

	/**
	 * Set Range Type.
	 *
	 * @param string $range_type Range Type.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_range_type( $range_type ) {
		$this->set_prop( 'range_type', $range_type );

		return $this;
	}

	/**
	 * Set From Date
	 *
	 * @param string $from_date From Date.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_from_date( $from_date ) {
		$this->set_prop( 'from_date', $from_date );

		return $this;
	}

	/**
	 * Set To Date.
	 *
	 * @param string $to_date To Date.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_to_date( $to_date ) {
		$this->set_prop( 'to_date', $to_date );

		return $this;
	}

	/**
	 * Set From Range.
	 *
	 * @param string $from_range From Range.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_from_range( $from_range ) {
		$this->set_prop( 'from_range', $from_range );

		return $this;
	}

	/**
	 * Set To Range.
	 *
	 * @param string $to_range To Range.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_to_range( $to_range ) {
		$this->set_prop( 'to_range', $to_range );

		return $this;
	}

	/**
	 * Set Bookable.
	 *
	 * @param string $bookable Bookable.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_bookable( $bookable ) {
		$this->set_prop( 'bookable', $bookable );

		return $this;
	}

	/**
	 * Set Priority.
	 *
	 * @param string $priority Priority.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_priority( $priority ) {
		$this->set_prop( 'priority', (int) $priority );

		return $this;
	}

	/**
	 * Set Ordering.
	 *
	 * @param string $ordering Ordering.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_ordering( $ordering ) {
		$this->set_prop( 'ordering', (int) $ordering );

		return $this;
	}

	/**
	 * Set RRULE.
	 *
	 * @param string $rrule RRULE.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_rrule( $rrule ) {
		$this->set_prop( 'rrule', $rrule );

		return $this;
	}

	/**
	 * Set webhook created date.
	 *
	 * @since 3.2.0
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 *                                  If the DateTime string has no timezone or offset,
	 *                                  WordPress site timezone will be assumed.
	 *                                  Null if their is no date.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );

		return $this;
	}

	/**
	 * Set webhook modified date.
	 *
	 * @since 3.2.0
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 *                                  If the DateTime string has no timezone or offset,
	 *                                  WordPress site timezone will be assumed.
	 *                                  Null if their is no date.
	 *
	 * @return WC_Global_Availability
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );

		return $this;
	}

	/**
	 * Check if an availability is in the past.
	 *
	 * @return bool
	 */
	public function has_past() {
		/*
		 * To prevent timezone difference, we subtract a day
		 * to give a 2 day buffer just in case so we don't
		 * negligently remove an availability. For example when
		 * the "to date" lands on same day as your local date
		 * but in the system is using a different server timezone.
		 */
		$today = date( 'Y-m-d', time() );

		$retval = false;

		if ( 'time:range' === $this->get_range_type() && $today > $this->get_to_date() ) {
			$retval = true;
		} elseif ( 'custom' === $this->get_range_type() && $today > $this->get_to_range() ) {
			$retval = true;
		}

		return apply_filters( 'woocommerce_bookings_availability_has_past', $retval, $this );
	}

	/**
	 * Check of offset exists.
	 *
	 * @param mixed $offset Offset.
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		$offset = $this->update_bc_offset( $offset );

		if ( 'id' === $offset ) {
			return true;
		}

		return array_key_exists( $offset, $this->data );
	}

	/**
	 * Get prop based on offset.
	 *
	 * @param mixed $offset Offset.
	 *
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		$offset = $this->update_bc_offset( $offset );

		if ( 'id' === $offset ) {
			return $this->get_id();
		}

		return $this->get_prop( $offset );
	}

	/**
	 * Set offset.
	 *
	 * @param mixed $offset Offset.
	 * @param mixed $value Value.
	 */
	public function offsetSet( $offset, $value ) {
		$offset = $this->update_bc_offset( $offset );

		if ( 'id' === $offset ) {
			$this->set_id();
			return;
		}

		$this->set_prop( $offset, $value );
	}

	/**
	 * Unset Offset.
	 *
	 * @param mixed $offset Offset.
	 */
	public function offsetUnset( $offset ) {
		$offset = $this->update_bc_offset( $offset );

		if ( 'id' === $offset ) {
			$this->set_id( 0 );
			return;
		}

		$this->set_prop( $offset, null );
	}

	/**
	 * Convert offset to it's new name.
	 *
	 * @param mixed $offset Offset.
	 *
	 * @return string
	 */
	private function update_bc_offset( $offset ) {
		if ( 'to' === $offset ) {
			$offset = 'to_range';
		} elseif ( 'from' === $offset ) {
			$offset = 'from_range';
		} elseif ( 'type' === $offset ) {
			$offset = 'range_type';
		} elseif ( 'ID' === $offset ) {
			$offset = 'id';
		}
		return $offset;
	}
}
