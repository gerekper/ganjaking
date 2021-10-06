<?php

/**
 * WC Booking Data Store: Stored in CPT.
 *
 * @todo When 2.6 support is dropped, implement WC_Object_Data_Store_Interface
 */
class WC_Booking_Data_Store extends WC_Data_Store_WP {

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	private $booking_meta_key_to_props = array(
		'_booking_all_day'                => 'all_day',
		'_booking_cost'                   => 'cost',
		'_booking_customer_id'            => 'customer_id',
		'_booking_order_item_id'          => 'order_item_id',
		'_booking_parent_id'              => 'parent_id',
		'_booking_persons'                => 'person_counts',
		'_booking_product_id'             => 'product_id',
		'_booking_resource_id'            => 'resource_id',
		'_booking_start'                  => 'start',
		'_booking_end'                    => 'end',
		'_wc_bookings_gcalendar_event_id' => 'google_calendar_event_id',
		'_local_timezone'                 => 'local_timezone',
	);

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Method to create a new booking in the database.
	 *
	 * @param WC_Booking $booking
	 */
	public function create( &$booking ) {
		if ( ! $booking->get_date_created( 'edit' ) ) {
			$booking->set_date_created( current_time( 'timestamp' ) );
		}

		$post_title = sprintf(
			/* translators: %s: Booking date */
			__( 'Booking &ndash; %s', 'woocommerce-bookings' ),
			/* translators: Booking date format parsed by strftime */
			strftime( _x( '%b %d, %Y @ %I:%M %p', 'Booking date format parsed by strftime', 'woocommerce-bookings' ) )
		);

		// @codingStandardsIgnoreStart
		$id = wp_insert_post( apply_filters( 'woocommerce_new_booking_data', array(
			'post_date'     => date( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' ) ),
			'post_date_gmt' => get_gmt_from_date( date( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' ) ) ),
			'post_type'     => 'wc_booking',
			'post_status'   => $booking->get_status( 'edit' ),
			'post_author'   => $booking->get_customer_id( 'edit' ),
			'post_title'    => $post_title,
			'post_parent'   => $booking->get_order_id( 'edit' ),
			'ping_status'   => 'closed',
		) ), true );
		// @codingStandardsIgnoreEnd

		if ( $id && ! is_wp_error( $id ) ) {
			$booking->set_id( $id );
			$this->update_post_meta( $booking );
			$booking->save_meta_data();
			$booking->apply_changes();
			WC_Cache_Helper::get_transient_version( 'bookings', true );

			do_action( 'woocommerce_new_booking', $booking->get_id() );
		}
		WC_Bookings_Cache::delete_booking_slots_transient();
	}

	/**
	 * Method to read an order from the database.
	 *
	 * @param WC_Booking
	 */
	public function read( &$booking ) {
		$booking->set_defaults();
		$booking_id  = $booking->get_id();
		$post_object = $booking_id ? get_post( $booking_id ) : false;

		if ( ! $booking_id || ! $post_object || 'wc_booking' !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid booking.', 'woocommerce-bookings' ) );
		}

		$set_props = array();

		// Read post data.
		$set_props['date_created']  = $post_object->post_date;
		$set_props['date_modified'] = $post_object->post_modified;
		$set_props['status']        = $post_object->post_status;
		$set_props['order_id']      = $post_object->post_parent;

		// Read meta data.
		foreach ( $this->booking_meta_key_to_props as $key => $prop ) {
			$value = get_post_meta( $booking->get_id(), $key, true );

			switch ( $prop ) {
				case 'end':
				case 'start':
					$set_props[ $prop ] = $value ? strtotime( $value ) : '';
					break;
				case 'all_day':
					$set_props[ $prop ] = wc_bookings_string_to_bool( $value );
					break;
				default:
					$set_props[ $prop ] = $value;
					break;
			}
		}

		$booking->set_props( $set_props );
		$booking->set_object_read( true );
	}

	/**
	 * Method to update an order in the database.
	 *
	 * @param WC_Booking $booking
	 */
	public function update( &$booking ) {
		wp_update_post( array(
			'ID'            => $booking->get_id(),
			'post_date'     => date( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' ) ),
			'post_date_gmt' => get_gmt_from_date( date( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' ) ) ),
			'post_status'   => $booking->get_status( 'edit' ),
			'post_author'   => $booking->get_customer_id( 'edit' ),
			'post_parent'   => $booking->get_order_id( 'edit' ),
		) );
		$this->update_post_meta( $booking );
		$booking->save_meta_data();
		$booking->apply_changes();
		WC_Cache_Helper::get_transient_version( 'bookings', true );
		WC_Bookings_Cache::flush_all_booking_connected_transients( $booking );
	}

	/**
	 * Method to delete an order from the database.
	 * @param WC_Booking
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( &$booking, $args = array() ) {
		$id   = $booking->get_id();
		$args = wp_parse_args( $args, array(
			'force_delete' => false,
		) );

		if ( $args['force_delete'] ) {
			wp_delete_post( $id );
			$booking->set_id( 0 );
			do_action( 'woocommerce_delete_booking', $id );
		} else {
			wp_trash_post( $id );
			$booking->set_status( 'trash' );
			do_action( 'woocommerce_trash_booking', $id );
		}
		WC_Bookings_Cache::delete_booking_slots_transient( $booking->get_product_id() );
	}

	/**
	 * Helper method that updates all the post meta for a booking based on it's settings in the WC_Booking class.
	 *
	 * @param WC_Booking
	 */
	protected function update_post_meta( &$booking ) {
		foreach ( $this->booking_meta_key_to_props as $key => $prop ) {
			if ( is_callable( array( $booking, "get_$prop" ) ) ) {
				$value = $booking->{ "get_$prop" }( 'edit' );

				switch ( $prop ) {
					case 'all_day':
						update_post_meta( $booking->get_id(), $key, $value ? 1 : 0 );
						break;
					case 'end':
					case 'start':
						update_post_meta( $booking->get_id(), $key, $value ? date( 'YmdHis', $value ) : '' );
						break;
					default:
						update_post_meta( $booking->get_id(), $key, $value );
						break;
				}
			}
		}
	}

	/**
	 * For a given order ID, get all bookings that belong to it.
	 *
	 * @param  int|array $order_id
	 * @return int
	 */
	public static function get_booking_ids_from_order_id( $order_id ) {
		global $wpdb;

		$order_ids = wp_parse_id_list( is_array( $order_id ) ? $order_id : array( $order_id ) );

		return wp_parse_id_list( $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wc_booking' AND post_parent IN (" . implode( ',', array_map( 'esc_sql', $order_ids ) ) . ');' ) );
	}

	/**
	 * For a given order item ID, get all bookings that belong to it.
	 *
	 * @param  int $order_item_id
	 * @return array
	 */
	public static function get_booking_ids_from_order_item_id( $order_item_id ) {
		global $wpdb;
		return wp_parse_id_list(
			$wpdb->get_col(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_booking_order_item_id' AND meta_value = %d;",
					$order_item_id
				)
			)
		);
	}

	/**
	 * Check if a given order contains only Bookings items.
	 * If the order contains non-booking items, it will return false.
	 * Otherwise, it will return an array of Bookings.
	 *
	 * @param  WC_Order $order
	 * @return bool|array
	 */
	public static function get_order_contains_only_bookings( $order ) {
		$all_booking_ids = array();

		foreach ( array_keys( $order->get_items() ) as $order_item_id ) {
			$booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $order_item_id );

			if ( empty( $booking_ids ) ) {
				return false;
			}

			$all_booking_ids = array_merge( $all_booking_ids, $booking_ids );
		}

		return $all_booking_ids;
	}

	/**
	 * Get booking ids for an object  by ID. e.g. product.
	 *
	 * @param  array
	 * @return array
	 */
	public static function get_booking_ids_by( $filters = array() ) {
		global $wpdb;

		$filters = wp_parse_args(
			$filters,
			array(
				'object_id'                => 0,
				'object_type'              => 'product',
				'status'                   => false,
				'limit'                    => -1,
				'offset'                   => 0,
				'order_by'                 => 'date_created',
				'order'                    => 'DESC',
				'date_before'              => false,
				'date_after'               => false,
				'google_calendar_event_id' => false,
				'date_between'             => array(
					'start' => false,
					'end'   => false,
				),
			)
		);

		$meta_keys            = array();
		$query_where          = array( 'WHERE 1=1', "p.post_type = 'wc_booking'" );
		$filters['object_id'] = array_filter( wp_parse_id_list( is_array( $filters['object_id'] ) ? $filters['object_id'] : array( $filters['object_id'] ) ) );

		if ( ! empty( $filters['object_id'] ) ) {
			switch ( $filters['object_type'] ) {
				case 'product':
					$meta_keys[]   = '_booking_product_id';
					$query_where[] = "_booking_product_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['object_id'] ) ) . "')";
					break;
				case 'resource':
					$meta_keys[]   = '_booking_resource_id';
					$query_where[] = "_booking_resource_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['object_id'] ) ) . "')";
					break;
				case 'product_or_resource':
					$meta_keys[]   = '_booking_product_id';
					$meta_keys[]   = '_booking_resource_id';
					$query_where[] = "(
						_booking_product_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['object_id'] ) ) . "') OR _booking_resource_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['object_id'] ) ) . "')
					)";
					break;
				case 'product_and_resource':
					$meta_keys[]   = '_booking_product_id';
					$meta_keys[]   = '_booking_resource_id';
					if ( $filters['product_id'] ) {
						$query_where[] = "_booking_product_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['product_id'] ) ) . "')";
					}
					if ( $filters['resource_id'] ) {
						$query_where[] = "_booking_resource_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['resource_id'] ) ) . "')";
					}
					break;
				case 'customer':
					$meta_keys[]   = '_booking_customer_id';
					$query_where[] = "_booking_customer_id.meta_value IN ('" . implode( "','", array_map( 'esc_sql', $filters['object_id'] ) ) . "')";
					break;
			}
		}

		if ( ! empty( $filters['status'] ) ) {
			$query_where[] = "p.post_status IN ('" . implode( "','", $filters['status'] ) . "')";
		}

		if ( ! empty( $filters['google_calendar_event_id'] ) ) {
			$meta_keys[]   = '_wc_bookings_gcalendar_event_id';
			$query_where[] = "_wc_bookings_gcalendar_event_id.meta_value IN ('" .
				implode(
					"','",
					array_map(
						'esc_sql',
						(array) $filters['google_calendar_event_id']
					)
				)
				. "')";
		}

		if ( ! empty( $filters['date_between']['start'] ) && ! empty( $filters['date_between']['end'] ) ) {
			$meta_keys[]   = '_booking_start';
			$meta_keys[]   = '_booking_end';
			$meta_keys[]   = '_booking_all_day';
			$query_where[] = "( (
				_booking_start.meta_value <= '" . esc_sql( date( 'YmdHis', $filters['date_between']['end'] ) ) . "' AND
				_booking_end.meta_value >= '" . esc_sql( date( 'YmdHis', $filters['date_between']['start'] ) ) . "' AND
				_booking_all_day.meta_value = '0'
			) OR (
				_booking_start.meta_value <= '" . esc_sql( date( 'Ymd000000', $filters['date_between']['end'] ) ) . "' AND
				_booking_end.meta_value >= '" . esc_sql( date( 'Ymd000000', $filters['date_between']['start'] ) ) . "' AND
				_booking_all_day.meta_value = '1'
			) )";
		}

		if ( ! empty( $filters['date_after'] ) ) {
			$meta_keys[]   = '_booking_start';
			$query_where[] = "_booking_start.meta_value >= '" . esc_sql( date( 'YmdHis', $filters['date_after'] ) ) . "'";
		}

		if ( ! empty( $filters['date_before'] ) ) {
			$meta_keys[]   = '_booking_end';
			$query_where[] = "_booking_end.meta_value <= '" . esc_sql( date( 'YmdHis', $filters['date_before'] ) ) . "'";
		}

		if ( ! empty( $filters['order_by'] ) ) {
			switch ( $filters['order_by'] ) {
				case 'date_created':
					$filters['order_by'] = 'p.post_date';
					break;
				case 'start_date':
					$meta_keys[]         = '_booking_start';
					$filters['order_by'] = '_booking_start.meta_value';
					break;
			}
			$query_order = ' ORDER BY ' . esc_sql( $filters['order_by'] ) . ' ' . esc_sql( $filters['order'] );
		} else {
			$query_order = '';
		}

		if ( $filters['limit'] > 0 ) {
			$query_limit = ' LIMIT ' . absint( $filters['offset'] ) . ',' . absint( $filters['limit'] );
		} else {
			$query_limit = '';
		}

		$query_select = "SELECT p.ID FROM {$wpdb->posts} p";
		$meta_keys    = array_unique( $meta_keys );
		$query_where  = implode( ' AND ', $query_where );

		foreach ( $meta_keys as $index => $meta_key ) {
			$key           = esc_sql( $meta_key );
			$query_select .= " LEFT JOIN {$wpdb->postmeta} {$key} ON p.ID = {$key}.post_id AND {$key}.meta_key = '{$key}'";
		}

		return array_filter( wp_parse_id_list( $wpdb->get_col( "{$query_select} {$query_where} {$query_order} {$query_limit};" ) ) );
	}

	/**
	 * For a given booking ID, get it's linked order ID if set.
	 *
	 * @param  int $booking_id
	 * @return int
	 */
	public static function get_booking_order_id( $booking_id ) {
		return absint( wp_get_post_parent_id( $booking_id ) );
	}

	/**
	 * For a given booking ID, get it's linked order item ID if set.
	 *
	 * @param  int $booking_id
	 * @return int
	 */
	public static function get_booking_order_item_id( $booking_id ) {
		return absint( get_post_meta( $booking_id, '_booking_order_item_id', true ) );
	}

	/**
	 * For a given booking ID, get it's linked order item ID if set.
	 *
	 * @param  int $booking_id
	 * @return int
	 */
	public static function get_booking_customer_id( $booking_id ) {
		return absint( get_post_meta( $booking_id, '_booking_customer_id', true ) );
	}

	/**
	 * Gets bookings for product ids and resource ids.
	 *
	 * @param  array   $ids       Booking ids
	 * @param  array   $status    Booking statuses
	 * @param  integer $date_from Date from
	 * @param  integer $date_to   Date to
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_objects_query( $ids, $status, $date_from = 0, $date_to = 0 ) {
		$status    = ! empty( $status ) ? $status : get_wc_booking_statuses( 'fully_booked' );
		$date_from = ! empty( $date_from ) ? $date_from : strtotime( 'midnight', current_time( 'timestamp' ) );
		$date_to   = ! empty( $date_to ) ? $date_to : strtotime( '+12 month', current_time( 'timestamp' ) );

		$booking_ids = WC_Booking_Data_Store::get_booking_ids_by( array(
			'status'       => $status,
			'object_id'    => $ids,
			'object_type'  => 'product_or_resource',
			'date_between' => array(
				'start' => $date_from,
				'end'   => $date_to,
			),
		) );
		return $booking_ids;
	}

	/**
	 * Gets bookings for product ids and resource ids.
	 *
	 * @param  array   $ids       Booking ids
	 * @param  array   $status    Booking statuses
	 * @param  integer $date_from Date from
	 * @param  integer $date_to   Date to
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_objects( $ids = array(), $status = array(), $date_from = 0, $date_to = 0 ) {
		// TODO: We need to round date_from/date_to to something specific.
		// Otherwise, one might abuse the DB transient cache by calling various combinations from the front-end with min-date/max-date.
		$transient_name = 'book_fo_' . md5( http_build_query( array( $ids, $status, $date_from, $date_to, WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );
		$status         = ! empty( $status ) ? $status : get_wc_booking_statuses( 'fully_booked' );
		$date_from      = ! empty( $date_from ) ? $date_from : strtotime( 'midnight', current_time( 'timestamp' ) );
		$date_to        = ! empty( $date_to ) ? $date_to : strtotime( '+12 month', current_time( 'timestamp' ) );
		$booking_ids    = WC_Bookings_Cache::get( $transient_name );

		if ( false === $booking_ids ) {
			$booking_ids = self::get_bookings_for_objects_query( $ids, $status, $date_from, $date_to );
			WC_Bookings_Cache::set( $transient_name, $booking_ids, DAY_IN_SECONDS * 30 );
		}

		if ( ! empty( $booking_ids ) ) {
			return array_map( 'get_wc_booking', wp_parse_id_list( $booking_ids ) );
		}
		return array();
	}

	/**
	 * Finds existing bookings for a product and its tied resources.
	 *
	 * @param  WC_Product_Booking $bookable_product Bookable product
	 * @param  int                $min_date         Minimum date
	 * @param  int                $max_date         Maximum date
	 * @return array
	 */
	public static function get_all_existing_bookings( $bookable_product, $min_date = 0, $max_date = 0 ) {
		$find_bookings_for = array( $bookable_product->get_id() );

		if ( $bookable_product->has_resources() ) {
			foreach ( $bookable_product->get_resources() as $resource ) {
				$find_bookings_for[] = $resource->get_id();
			}
		}

		if ( empty( $min_date ) ) {
			// Determine a min and max date.
			$min_date = $bookable_product->get_min_date();
			$min_date = empty( $min_date ) ? array(
				'unit' => 'minute',
				'value' => 1,
			) : $min_date ;
			$min_date = strtotime( "midnight +{$min_date['value']} {$min_date['unit']}", current_time( 'timestamp' ) );
		}

		if ( empty( $max_date ) ) {
			$max_date = $bookable_product->get_max_date();
			$max_date = empty( $max_date ) ? array(
				'unit' => 'month',
				'value' => 12,
			) : $max_date;
			$max_date = strtotime( "+{$max_date['value']} {$max_date['unit']}", current_time( 'timestamp' ) );
		}

		return self::get_bookings_for_objects( $find_bookings_for, get_wc_booking_statuses( 'fully_booked' ), $min_date, $max_date );
	}

	/**
	 * Return all bookings for a product in a given range.
	 * @param integer $start_date
	 * @param integer $end_date
	 * @param mixed   $product_or_resource_ids
	 * @param bool    $check_in_cart
	 *
	 * @return array
	 */
	public static function get_bookings_in_date_range( $start_date, $end_date, $product_or_resource_ids = 0, $check_in_cart = true ) {
		$product_or_resources_key = is_array( $product_or_resource_ids ) ? implode( ',', $product_or_resource_ids ) : $product_or_resource_ids;
		$transient_name = 'book_dr_' . md5( http_build_query( array( $start_date, $end_date, $product_or_resources_key,  $check_in_cart, WC_Cache_Helper::get_transient_version( 'bookings' ) ) ) );
		$booking_ids    = WC_Bookings_Cache::get( $transient_name );

		if ( false === $booking_ids ) {
			$args = array(
				'status'       => get_wc_booking_statuses(),
				'object_id'    => $product_or_resource_ids,
				'object_type'  => 'product_and_resource',
				'date_between' => array(
					'start' => $start_date,
					'end'   => $end_date,
				),
			);

			if ( ! $check_in_cart ) {
				$args['status'] = array_diff( $args['status'], array( 'in-cart' ) );
			}

			if ( $product_or_resource_ids ) {
				$args['product_id'] = array();
				$args['resource_id'] = array();
				foreach ( (array)$product_or_resource_ids as $pid ) {
					if ( 'bookable_resource' === get_post_type( $pid ) ) {
						$args['resource_id'][] = absint( $pid );
					} else {
						$args['product_id'][] = absint( $pid );
					}
				}
			}

			$booking_ids = apply_filters( 'woocommerce_bookings_in_date_range_query', self::get_booking_ids_by( $args ) );

			WC_Bookings_Cache::set( $transient_name, $booking_ids, DAY_IN_SECONDS * 30 );
		}

		return array_map( 'get_wc_booking', wp_parse_id_list( $booking_ids ) );
	}

	/**
	 * Gets bookings for a user by ID.
	 *
	 * @param  int   $user_id    The id of the user that we want bookings for
	 * @param  array $query_args The query arguments used to get booking IDs
	 * @return array             Array of WC_Booking objects
	 */
	public static function get_bookings_for_user( $user_id, $query_args = array() ) {
		$booking_ids = self::get_booking_ids_by( array_merge( $query_args, array(
			'status'      => get_wc_booking_statuses( 'user' ),
			'object_id'   => $user_id,
			'object_type' => 'customer',
		) ) );

		return array_map( 'get_wc_booking', $booking_ids );
	}

	/**
	 * Gets bookings for a product by ID.
	 *
	 * @param int $product_id The id of the product that we want bookings for
	 * @param array $status Order statuses
	 * @return array of WC_Booking objects
	 */
	public static function get_bookings_for_product( $product_id, $status = array( 'confirmed', 'paid' ) ) {
		$booking_ids = self::get_booking_ids_by( array(
			'object_id'   => $product_id,
			'object_type' => 'product',
			'status'      => $status,
		) );
		return array_map( 'get_wc_booking', $booking_ids );
	}

	/**
	 * Search booking data for a term and return ids.
	 *
	 * @param  string $term Searched term.
	 * @return array of ids
	 */
	public function search_bookings( $term ) {
		global $wpdb;

		$search_fields = array_map(
			'wc_clean',
			apply_filters( 'woocommerce_booking_search_fields', array() )
		);
		$booking_ids   = array();

		if ( is_numeric( $term ) ) {
			$booking_ids[] = absint( $term );
		}

		if ( ! empty( $search_fields ) ) {
			$booking_ids = array_unique(
				array_merge(
					$booking_ids,
					$wpdb->get_col(
						$wpdb->prepare(
							"SELECT DISTINCT p1.post_id FROM {$wpdb->postmeta} p1 WHERE p1.meta_value LIKE %s AND p1.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "')", // @codingStandardsIgnoreLine
							'%' . $wpdb->esc_like( wc_clean( $term ) ) . '%'
						)
					)
				)
			);
		}

		$booking_ids = array_unique(
			array_merge(
				$booking_ids,
				$wpdb->get_col(
					$wpdb->prepare(
						"SELECT p.id
						FROM {$wpdb->prefix}posts p
						INNER JOIN {$wpdb->prefix}users u ON p.post_author = u.id
						WHERE display_name LIKE %s OR user_nicename LIKE %s",
						'%' . $wpdb->esc_like( wc_clean( $term ) ) . '%',
						'%' . $wpdb->esc_like( wc_clean( $term ) ) . '%'
					)
				),
				$wpdb->get_col(
					$wpdb->prepare(
						"SELECT pm.post_id
						FROM {$wpdb->prefix}postmeta pm
						INNER JOIN {$wpdb->prefix}posts p ON p.id = pm.meta_value
						WHERE meta_key = '_booking_product_id' AND p.post_title LIKE %s",
						'%' . $wpdb->esc_like( wc_clean( $term ) ) . '%'
					)
				)
			)
		);

		return apply_filters( 'woocommerce_booking_search_results', $booking_ids, $term, $search_fields );
	}
}
