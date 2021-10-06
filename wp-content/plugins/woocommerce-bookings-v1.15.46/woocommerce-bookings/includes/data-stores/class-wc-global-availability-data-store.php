<?php
/**
 * Class WC_Global_Availability_Data_Store
 *
 * @package Woocommerce/Bookings
 */

/**
 * WC Global Availability Data Store: Stored in Custom table.
 * @todo When 2.6 support is dropped, implement WC_Object_Data_Store_Interface
 */
class WC_Global_Availability_Data_Store extends WC_Data_Store_WP {

	const TABLE_NAME  = 'wc_bookings_availability';
	const CACHE_GROUP = 'wc-bookings-availability';
	const DEFAULT_MIN_DATE = '0000-00-00';
	const DEFAULT_MAX_DATE = '9999-99-99';

	protected $meta_type = 'bookings_availability';

	protected function get_db_info() {
		global $wpdb;
		return array(
			'table'           => $wpdb->prefix . 'wc_bookings_availabilitymeta',
			'object_id_field' => 'bookings_availability_id',
			'meta_id_field'   => 'meta_id',
		);
	}

	/**
	 * Create a new global availability in the database.
	 *
	 * @param WC_Global_Availability $availability WC_Global_Availability instance.
	 */
	public function create( &$availability ) {
		global $wpdb;

		$availability->apply_changes();

		$data = array(
			'gcal_event_id' => $availability->get_gcal_event_id( 'edit' ),
			'title'         => $availability->get_title( 'edit' ),
			'range_type'    => $availability->get_range_type( 'edit' ),
			'from_date'     => $availability->get_from_date( 'edit' ),
			'to_date'       => $availability->get_to_date( 'edit' ),
			'from_range'    => $availability->get_from_range( 'edit' ),
			'to_range'      => $availability->get_to_range( 'edit' ),
			'bookable'      => $availability->get_bookable( 'edit' ),
			'priority'      => $availability->get_priority( 'edit' ),
			'ordering'      => $availability->get_ordering( 'edit' ),
			'rrule'         => $availability->get_rrule( 'edit' ),
			'date_created'  => current_time( 'mysql' ),
			'date_modified' => current_time( 'mysql' ),
		);

		$wpdb->insert( $wpdb->prefix . self::TABLE_NAME, $data );
		$availability->set_id( $wpdb->insert_id );
		$availability->save_meta_data();
		// The function incr_cache_prefix is deprecated in WooCommerce 3.9.
		if ( method_exists( 'WC_Cache_Helper', 'invalidate_cache_group' ) ) {
			WC_Cache_Helper::invalidate_cache_group( self::CACHE_GROUP );
		} else {
			WC_Cache_Helper::incr_cache_prefix( self::CACHE_GROUP );
		}
		WC_Bookings_Cache::delete_booking_slots_transient();
	}

	/**
	 * Read availability from the database.
	 *
	 * @param  WC_Global_Availability $availability Instance.
	 * @throws Exception When webhook is invalid.
	 */
	public function read( &$availability ) {
		global $wpdb;

		$data = wp_cache_get( $availability->get_id(), self::CACHE_GROUP );

		if ( false === $data ) {
			$data = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT
								ID as id,
								gcal_event_id,
								title,
								range_type,
								from_date,
								to_date,
								from_range,
								to_range,
								bookable,
								priority,
								ordering,
								date_created,
								date_modified,
       							rrule
							FROM ' . $wpdb->prefix . self::TABLE_NAME .
							' WHERE ID = %d LIMIT 1;',
					$availability->get_id()
				),
				ARRAY_A
			); // WPCS: unprepared SQL ok.

			if ( empty( $data ) ) {
				throw new Exception( __( 'Invalid event.', 'woocommerce-bookings' ) );
			}

			wp_cache_add( $availability->get_id(), $data, self::CACHE_GROUP );
		}

		if ( is_array( $data ) ) {
			$availability->set_props( $data );
			$availability->set_object_read( true );
		}
	}

	/**
	 * Update a webhook.
	 *
	 * @param WC_Global_Availability $availability Instance.
	 */
	public function update( &$availability ) {
		global $wpdb;

		$changes = $availability->get_changes();

		$changes['date_modified'] = current_time( 'mysql' );

		$wpdb->update(
			$wpdb->prefix . self::TABLE_NAME,
			$changes,
			array(
				'ID' => $availability->get_id(),
			)
		);

		$availability->apply_changes();
		$availability->save_meta_data();

		wp_cache_delete( $availability->get_id(), self::CACHE_GROUP );
		// The function incr_cache_prefix is deprecated in WooCommerce 3.9.
		if ( method_exists( 'WC_Cache_Helper', 'invalidate_cache_group' ) ) {
			WC_Cache_Helper::invalidate_cache_group( self::CACHE_GROUP );
		} else {
			WC_Cache_Helper::incr_cache_prefix( self::CACHE_GROUP );
		}
		WC_Bookings_Cache::delete_booking_slots_transient();
	}

	/**
	 * Remove a webhook from the database.
	 *
	 * @param WC_Global_Availability $availability Instance.
	 * @param array                  $options      Options array.
	 */
	public function delete( &$availability, $options = array() ) {
		global $wpdb;

		do_action( 'woocommerce_bookings_before_delete_global_availability', $availability, $this ); // WC_Data::delete does not trigger an action like save() so we have to do it here.

		$wpdb->delete(
			$wpdb->prefix . self::TABLE_NAME,
			array(
				'ID' => $availability->get_id(),
			),
			array( '%d' )
		);
		$wpdb->delete(
			$wpdb->prefix . self::TABLE_NAME . 'meta',
			array(
				'bookings_availability_id' => $availability->get_id(),
			),
			array( '%d' )
		);
		wp_cache_delete( $availability->get_id(), self::CACHE_GROUP );
		// The function incr_cache_prefix is deprecated in WooCommerce 3.9.
		if ( method_exists( 'WC_Cache_Helper', 'invalidate_cache_group' ) ) {
			WC_Cache_Helper::invalidate_cache_group( self::CACHE_GROUP );
		} else {
			WC_Cache_Helper::incr_cache_prefix( self::CACHE_GROUP );
		}
		WC_Bookings_Cache::delete_booking_slots_transient();
	}

	/**
	 * Get all global availabilties defined in the database as objetcs.
	 *
	 * @param array  $filters { @see self::build_query() }.
	 * @param string $min_date { @see self::build_query() }.
	 * @param string $max_date { @see self::build_query() }.
	 *
	 * @return WC_Global_Availability[]
	 * @throws Exception Validation fails.
	 */
	public function get_all( $filters = array(), $min_date = self::DEFAULT_MIN_DATE, $max_date = self::DEFAULT_MAX_DATE) {
		$data = $this->get_all_as_array( $filters, $min_date, $max_date );

		$availabilities = array();
		foreach ( $data as $row ) {
			$availability = new WC_Global_Availability();
			$availability->set_object_read( false );
			$availability->set_props( $row );
			$availability->set_object_read( true );
			$availabilities[] = $availability;
		}

		return apply_filters( 'woocommerce_bookings_get_all_global_availability', $availabilities );
	}

	/**
	 * Get global availability as array.
	 *
	 * @param array  $filters { @see self::build_query() }.
	 * @param string $min_date { @see self::build_query() }.
	 * @param string $max_date { @see self::build_query() }.
	 *
	 * @return array|null|object
	 */
	public function get_all_as_array( $filters = array(), $min_date = self::DEFAULT_MIN_DATE, $max_date = self::DEFAULT_MAX_DATE ) {
		global $wpdb;

		if ( ! is_array( $filters ) ) {
			$filters = array(); // WC_Data_Store uses call_user_func_array to call this function so the default parameter is not used.
		}

		$sql = $this->build_query( $filters, $min_date, $max_date );

		$cache_key = WC_Cache_Helper::get_cache_prefix( self::CACHE_GROUP ) . 'get_all:' . md5( $sql );
		$array     = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false === $array ) {
			$array = $wpdb->get_results( $sql, ARRAY_A ); // WPCS: unprepared SQL ok.

			foreach ( $array as &$row ) {
				// Set BC keys.
				$row['type'] = $row['range_type'];
				$row['to']   = $row['to_range'];
				$row['from'] = $row['from_range'];
			}

			wp_cache_add( $cache_key, $array, self::CACHE_GROUP );
		}

		return $array;
	}

	/**
	 * Builds query string for availability.
	 *
	 * @param array $filters { @see self::build_query() }.
	 * @param string $min_date Minimum date to select intersecting availability entries for (yyyy-mm-dd format).
	 * @param string $max_date Maximum date to select intersecting availability entries for (yyyy-mm-dd format).
	 *
	 * @return string
	 */
	private function build_query( $filters, $min_date, $max_date ) {
		global $wpdb;

		/*
		 * Build list of fields with virtual fields 'start_date' and 'end_date'.
		 * 'start_date' shall be '0000-00-00' for recurring events.
		 * 'end_date' shall be '9999-99-99' for recurring events.
		 */
		$fields = array(
			'ID',
			'gcal_event_id',
			'title',
			'range_type',
			'from_date',
			'to_date',
			'from_range',
			'to_range',
			'rrule',
			'bookable',
			'priority',
			'ordering',
			'date_created',
			'date_modified',
			'(CASE
				WHEN range_type = \'custom\' THEN from_range
				WHEN range_type = \'time:range\' THEN from_date
				WHEN range_type = \'custom:daterange\' THEN from_date
				WHEN range_type = \'store_availability\' THEN from_date
				ELSE \'0000-00-00\'
			END) AS start_date',
			'(CASE
				WHEN range_type = \'custom\' THEN to_range
				WHEN range_type = \'time:range\' THEN to_date
				WHEN range_type = \'custom:daterange\' THEN to_date
				WHEN range_type = \'store_availability\' THEN to_date
				ELSE \'9999-99-99\'
			END) AS end_date',
		);

		// Identity for WHERE clause.
		$where = array( '1' );

		// Parse WHERE for SQL.
		foreach ( $filters as $filter ) {
			$key     = esc_sql( $filter['key'] );
			$value   = esc_sql( $filter['value'] );
			$compare = $this->validate_compare( $filter['compare'] );
			$where[] = "`{$key}` {$compare} '{$value}'";
		}

		// Query for dates that intersect with the min and max.
		if ( self::DEFAULT_MIN_DATE !== $min_date || self::DEFAULT_MAX_DATE !== $max_date ) {
			$min_max_dates       = array( esc_sql( $min_date ), esc_sql( $max_date ) );
			$date_intersect_or   = array();
			$date_intersect_or[] = vsprintf( "( start_date BETWEEN '%s' AND '%s' )", $min_max_dates );
			$date_intersect_or[] = vsprintf( "( end_date BETWEEN '%s' AND '%s' )", $min_max_dates );
			$date_intersect_or[] = vsprintf( "( start_date <= '%s' AND end_date >= '%s' )", $min_max_dates );
			$where[]             = sprintf( "( %s )", implode( ' OR ', $date_intersect_or ) );
		}
		sort( $where );

		return sprintf(
			'SELECT * FROM ( SELECT %s FROM %s ) AS a_data WHERE %s ORDER BY ordering ASC',
			implode( ', ', $fields ),
			$wpdb->prefix . self::TABLE_NAME,
			implode( ' AND ', $where )
		);
	}

	/**
	 * Validates query filter comparison (defaults to '=')
	 *
	 * @param string $compare Raw compare string.
	 * @return string Validated compare string.
	 */
	private function validate_compare( $compare ) {
		$compare = strtoupper( $compare );
		if ( ! in_array( $compare, array(
			'=', '!=', '>', '>=', '<', '<=',
			'LIKE', 'NOT LIKE',
			'IN', 'NOT IN',
			'BETWEEN', 'NOT BETWEEN'
		) ) ) {
			$compare = '=';
		}
		return $compare;
	}

	/**
	 * Return all bookings and blocked availability for a product in a given range.
	 * @param integer $start_date
	 * @param integer $end_date
	 * @param mixed   $product_or_resource_ids
	 * @param bool    $check_in_cart
	 *
	 * @return array
	 */
	public static function get_events_in_date_range( $start_date, $end_date, $product_or_resource_ids = 0, $check_in_cart = true ) {
		$bookings              = WC_Booking_Data_Store::get_bookings_in_date_range( $start_date, $end_date, $product_or_resource_ids, $check_in_cart );
		$min_date              = date( 'Y-m-d', $start_date );
		$max_date              = date( 'Y-m-d', $end_date );

		// Filter only for events synced from Google Calendar.
		$filters               = array(
			array(
				'key'     => 'gcal_event_id',
				'compare' => '!=',
				'value'   => '',
			),
		);

		$global_availabilities = WC_Data_Store::load( 'booking-global-availability' )->get_all( $filters, $min_date, $max_date );

		return array_merge( $bookings, $global_availabilities );
	}

	/**
	 * Return an array global_availability_rules
	 * @since 1.13.0
	 *
	 * @param  int   $start_date
	 * @param  int . $end_date
	 *
	 * @return array Days that are buffer days and therefor should be un-bookable
	 */
	public static function get_global_availability_in_date_range( $start_date, $end_date ) {

		// Filter only for events not from Google Calendar.
		$filters               = array(
			array(
				'key'     => 'gcal_event_id',
				'compare' => '==',
				'value'   => '',
			),
		);

		$min_date = date( 'Y-m-d', $start_date );
		$max_date = date( 'Y-m-d', $end_date );
		return WC_Data_Store::load( 'booking-global-availability' )->get_all( $filters, $min_date, $max_date );
	}
}
