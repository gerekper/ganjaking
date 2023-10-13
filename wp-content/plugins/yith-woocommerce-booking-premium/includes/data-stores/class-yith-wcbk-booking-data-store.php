<?php
/**
 * Class YITH_WCBK_Booking_Data_Store
 * Data store for Bookings
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Booking_Data_Store
 *
 * @since 3.0.0
 */
class YITH_WCBK_Booking_Data_Store extends WC_Data_Store_WP implements WC_Object_Data_Store_Interface {

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $meta_keys_to_props = array(
		'_product_id'         => 'product_id',
		'_title'              => 'raw_title',
		'_from'               => 'from',
		'_to'                 => 'to',
		'_duration'           => 'duration',
		'_duration_unit'      => 'duration_unit',
		'_order_id'           => 'order_id',
		'_order_item_id'      => 'order_item_id',
		'_user_id'            => 'user_id',
		'_can_be_cancelled'   => 'can_be_cancelled',
		'_cancelled_duration' => 'cancelled_duration',
		'_cancelled_unit'     => 'cancelled_unit',
		'_location'           => 'location',
		'_all_day'            => 'all_day',
	);

	/**
	 * Data stored in meta keys, but not considered "meta".
	 *
	 * @var array
	 */
	protected $internal_meta_keys = array(
		'_product_id',
		'_title',
		'_from',
		'_to',
		'_duration',
		'_duration_unit',
		'_order_id',
		'_order_item_id',
		'_user_id',
		'_can_be_cancelled',
		'_cancelled_duration',
		'_cancelled_unit',
		'_location',
		'_all_day',
		'_wp_old_slug',
		'_edit_last',
		'_edit_lock',
	);

	/**
	 * Stores updated props.
	 *
	 * @var array
	 */
	protected $updated_props = array();

	/**
	 * YITH_WCBK_Product_Booking_Data_Store_CPT constructor.
	 */
	public function __construct() {
		if ( is_callable( array( parent::class, '__construct' ) ) ) {
			parent::__construct();
		}

		$this->internal_meta_keys = apply_filters( 'yith_wcbk_booking_data_store_internal_meta_keys', $this->internal_meta_keys, $this );
	}

	/**
	 * Create
	 *
	 * @param YITH_WCBK_Booking $booking The Booking.
	 */
	public function create( &$booking ) {
		if ( ! $booking->get_date_created( 'edit' ) ) {
			$booking->set_date_created( time() );
		}

		$id = wp_insert_post(
			apply_filters(
				'yith_wcbk_new_booking_data',
				array(
					'post_type'     => YITH_WCBK_Post_Types::BOOKING,
					'post_status'   => $this->validate_booking_status( $booking->get_status() ),
					'post_title'    => $booking->get_raw_title(),
					'post_date'     => gmdate( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' )->getOffsetTimestamp() ),
					'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' )->getTimestamp() ),
				)
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$booking->set_id( $id );
			$booking->update_product_data();

			$this->force_meta_values( $booking );
			$this->update_post_meta( $booking, true );
			$this->handle_updated_props( $booking );
			$this->clear_caches( $booking );

			$booking->save_meta_data();
			$booking->apply_changes();

			$booking->add_note( 'new', __( 'Booking successfully created.', 'yith-booking-for-woocommerce' ) );

			do_action( 'yith_wcbk_booking_created', $booking );
			do_action( 'yith_wcbk_new_booking', $id, $booking );
		}
	}

	/**
	 * Read
	 *
	 * @param YITH_WCBK_Booking $booking The Booking.
	 *
	 * @throws Exception If passed booking is invalid.
	 */
	public function read( &$booking ) {
		$booking->set_defaults();
		$post_object = get_post( $booking->get_id() );
		if ( ! $booking->get_id() || ! $post_object || YITH_WCBK_Post_Types::BOOKING !== $post_object->post_type ) {
			throw new Exception(
				__( 'Invalid booking.', 'yith-booking-for-woocommerce' ) .
				print_r( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					array(
						'id'                      => $booking->get_id(),
						'$post_object'            => $post_object,
						'$post_object->post_type' => ! ! $post_object ? $post_object->post_type : false,
					),
					true
				)
			);
		}

		$booking->set_props(
			array(
				'raw_title'     => $post_object->post_title,
				'date_created'  => $this->string_to_timestamp( $post_object->post_date_gmt ),
				'date_modified' => $this->string_to_timestamp( $post_object->post_modified_gmt ),
				'status'        => $post_object->post_status,
			)
		);

		$this->read_booking_data( $booking );
		$booking->set_object_read( true );

		do_action( 'yith_wcbk_booking_read', $booking );
	}

	/**
	 * Update
	 *
	 * @param YITH_WCBK_Booking $booking The Booking.
	 */
	public function update( &$booking ) {
		$booking->save_meta_data();
		$changes = $booking->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array( 'raw_title', 'status', 'date_created', 'date_modified' ), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_type'   => YITH_WCBK_Post_Types::BOOKING,
				'post_status' => $this->validate_booking_status( $booking->get_status( 'edit' ) ),
				'post_title'  => $booking->get_raw_title( 'edit' ),
			);
			if ( $booking->get_date_created( 'edit' ) ) {
				$post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' )->getOffsetTimestamp() );
				$post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $booking->get_date_created( 'edit' )->getTimestamp() );
			}
			if ( isset( $changes['date_modified'] ) && $booking->get_date_modified( 'edit' ) ) {
				$post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $booking->get_date_modified( 'edit' )->getOffsetTimestamp() );
				$post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $booking->get_date_modified( 'edit' )->getTimestamp() );
			} else {
				$post_data['post_modified']     = current_time( 'mysql' );
				$post_data['post_modified_gmt'] = current_time( 'mysql', 1 );
			}

			/**
			 * When updating this object, to prevent infinite loops, use $wpdb
			 * to update data, since wp_update_post spawns more calls to the
			 * save_post action.
			 * This ensures hooks are fired by either WP itself (admin screen save),
			 * or an update purely from CRUD.
			 */
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $booking->get_id() ) );
				clean_post_cache( $booking->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $booking->get_id() ), $post_data ) );
			}
			$booking->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		} else { // Only update post modified time to record this save event.
			$GLOBALS['wpdb']->update(
				$GLOBALS['wpdb']->posts,
				array(
					'post_modified'     => current_time( 'mysql' ),
					'post_modified_gmt' => current_time( 'mysql', 1 ),
				),
				array(
					'ID' => $booking->get_id(),
				)
			);
			clean_post_cache( $booking->get_id() );
		}

		$special_post_props = array( 'raw_title', 'status', 'date_created', 'date_modified' );
		foreach ( $special_post_props as $prop ) {
			if ( in_array( $prop, array_keys( $changes ), true ) ) {
				$this->updated_props[] = $prop;
			}
		}

		$this->force_meta_values( $booking );
		$this->update_post_meta( $booking );
		$this->handle_updated_props( $booking );
		$this->clear_caches( $booking );

		$booking->apply_changes();

		do_action( 'yith_wcbk_booking_updated', $booking );
		do_action( 'yith_wcbk_update_booking', $booking->get_id(), $booking );
	}

	/**
	 * Delete
	 *
	 * @param YITH_WCBK_Booking $booking The Booking.
	 * @param array             $args    Arguments.
	 */
	public function delete( &$booking, $args = array() ) {
		$id = $booking->get_id();

		$args = wp_parse_args(
			$args,
			array(
				'force_delete' => false,
			)
		);

		if ( ! $id ) {
			return;
		}

		// We don't need to clear product data cache here, since it's done when deleting/trashing the post.

		if ( $args['force_delete'] ) {
			do_action( 'yith_wcbk_before_delete_booking', $id, $booking );
			wp_delete_post( $id );
			$booking->set_id( 0 );
			do_action( 'yith_wcbk_delete_booking', $id );
		} else {
			wp_trash_post( $id );
			$booking->set_status( 'trash' );
			do_action( 'yith_wcbk_trash_booking', $id, $booking );
		}
	}

	/**
	 * Read booking data.
	 *
	 * @param YITH_WCBK_Booking $booking The Booking.
	 */
	protected function read_booking_data( &$booking ) {
		$id               = $booking->get_id();
		$post_meta_values = get_post_meta( $id );
		$set_props        = array();

		foreach ( $this->meta_keys_to_props as $meta_key => $prop ) {
			$meta_value         = $post_meta_values[ $meta_key ][0] ?? null;
			$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only un-serializes single values.
		}

		$booking->set_props( $set_props );

		do_action( 'yith_wcbk_booking_data_store_read_data', $booking, $this );
	}

	/**
	 * Helper method that updates all the post meta.
	 *
	 * @param YITH_WCBK_Booking $booking Booking object.
	 * @param bool              $force   Force update. Used during create.
	 */
	protected function update_post_meta( &$booking, $force = false ) {
		$meta_key_to_props = $this->meta_keys_to_props;
		$props_to_update   = $force ? $meta_key_to_props : $this->get_props_to_update( $booking, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $booking->{"get_$prop"}( 'edit' );
			$value = is_string( $value ) ? wp_slash( $value ) : $value;
			switch ( $prop ) {
				case 'can_be_cancelled':
				case 'all_day':
				case 'has_persons':
					$value = wc_bool_to_string( $value );
					break;
			}

			$updated = update_post_meta( $booking->get_id(), $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		/**
		 * This filter allows third-party plugins (and plugin modules) to update custom props.
		 * Important: you MUST add the props you updated to the first param.
		 */
		$extra_updated_props = apply_filters( 'yith_wcbk_booking_data_store_update_props', array(), $booking, $force, $this );
		if ( $extra_updated_props ) {
			$this->updated_props = array_merge( $this->updated_props, $extra_updated_props );
		}
	}

	/**
	 * Force meta values
	 *
	 * @param YITH_WCBK_Booking $booking Product Object.
	 */
	protected function force_meta_values( &$booking ) {
		$changes = $booking->get_changes();

		if ( array_intersect( array( 'from', 'to' ), array_keys( $changes ) ) ) {
			$booking->update_duration();
			$booking->maybe_adjust_all_day_to();
		}
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param YITH_WCBK_Booking $booking Product Object.
	 */
	protected function handle_updated_props( &$booking ) {

		if ( array_intersect( $this->updated_props, $this->get_columns_for_lookup_table( YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE ) ) ) {
			$this->update_booking_meta_lookup_table( $booking->get_id() );
		}

		yith_wcbk_do_deprecated_action( 'yith_wcbk_booking_object_updated_props', array( $booking ), '4.0.0', 'yith_wcbk_booking_data_store_updated_props' );

		// Trigger action so 3rd parties can deal with updated props.
		do_action( 'yith_wcbk_booking_data_store_updated_props', $booking, $this->updated_props );

		// After handling, we can reset the props array.
		$this->updated_props = array();
	}

	/**
	 * Update the booking meta lookup table
	 *
	 * @param int $booking_id The Booking ID.
	 */
	public function update_booking_meta_lookup_table( $booking_id ) {
		// Cache delete is required to set correct data for lookup table, which reads from cache.
		wp_cache_delete( $booking_id, 'post_meta' );
		$this->update_lookup_table( $booking_id, YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE );
	}

	/**
	 * Clear any caches.
	 *
	 * @param YITH_WCBK_Booking $booking Booking object.
	 */
	protected function clear_caches( &$booking ) {
		yith_wcbk_regenerate_product_data( $booking->get_product_id() );

		do_action( 'yith_wcbk_booking_data_store_clear_caches', $booking, $this );
	}

	/**
	 * Validate a booking status
	 *
	 * @param string $status The status.
	 *
	 * @return string
	 */
	protected function validate_booking_status( $status ) {
		$status = ! ! $status ? $status : 'unpaid';
		$status = 'bk-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
		if ( yith_wcbk_is_a_booking_status( $status ) ) {
			$status = 'bk-' . $status;
		} elseif ( 'trash' !== $status ) {
			$status = 'bk-unpaid';
		}

		return $status;
	}

	/**
	 * Get data to save to a lookup table.
	 *
	 * @param int    $id    ID of object to update.
	 * @param string $table Lookup table name.
	 *
	 * @return array
	 */
	protected function get_data_for_lookup_table( $id, $table ) {
		if ( YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE === $table ) {
			$booking_id = absint( $id );
			if ( $booking_id ) {
				return array(
					'booking_id' => $booking_id,
					'product_id' => absint( get_post_meta( $booking_id, '_product_id', true ) ),
					'order_id'   => absint( get_post_meta( $booking_id, '_order_id', true ) ),
					'user_id'    => absint( get_post_meta( $booking_id, '_user_id', true ) ),
					'status'     => $this->validate_booking_status( get_post_status( $booking_id ) ),
					'from'       => gmdate( 'Y-m-d H:i:s', absint( get_post_meta( $booking_id, '_from', true ) ) ),
					'to'         => gmdate( 'Y-m-d H:i:s', absint( get_post_meta( $booking_id, '_to', true ) ) ),
					'persons'    => absint( get_post_meta( $booking_id, '_persons', true ) ),
				);
			}
		}

		return array();
	}

	/**
	 * Get primary key name for lookup table.
	 *
	 * @param string $table Lookup table name.
	 *
	 * @return string
	 */
	protected function get_primary_key_for_lookup_table( $table ) {
		if ( YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE === $table ) {
			return 'booking_id';
		}

		return '';
	}

	/**
	 * Get column types for lookup table.
	 *
	 * @param string $table Lookup table name.
	 *
	 * @return array
	 */
	protected function get_column_types_for_lookup_table( $table ) {
		$types = array();
		if ( YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE === $table ) {
			$primary = $this->get_primary_key_for_lookup_table( $table );
			$types   = array(
				$primary     => 'INT',
				'product_id' => 'INT',
				'order_id'   => 'INT',
				'user_id'    => 'INT',
				'status'     => 'CHAR',
				'from'       => 'DATETIME',
				'to'         => 'DATETIME',
				'persons'    => 'INT',
			);
		}

		return $types;
	}

	/**
	 * Get columns for lookup table.
	 *
	 * @param string $table           Lookup table name.
	 * @param bool   $include_primary True to include the primary key.
	 *
	 * @return array
	 */
	protected function get_columns_for_lookup_table( $table, $include_primary = false ) {
		$columns = array_keys( $this->get_column_types_for_lookup_table( $table ) );

		if ( $include_primary ) {
			$primary_key = $this->get_primary_key_for_lookup_table( $table );
			$columns     = array_diff( $columns, array( $primary_key ) );
		}

		return $columns;
	}

	/**
	 * Retrieve the default query args.
	 *
	 * @return array
	 */
	public function get_default_query_args() {
		return array(
			'items_per_page'          => - 1,
			'paginate'                => false,
			'page'                    => 1,
			'product_id'              => false,
			'order_id'                => false,
			'user_id'                 => false,
			'resources'               => false,
			'status'                  => array_keys( yith_wcbk_get_booking_statuses() ),
			'include'                 => false,
			'exclude'                 => false,
			'from'                    => false,
			'to'                      => false,
			'date_from'               => false,
			'date_to'                 => false,
			'whole_duration_in_range' => false,
			'order'                   => 'DESC',
			'order_by'                => 'id',
			'return'                  => 'ids', // allowed values: ids, bookings, count.
			'count'                   => 'booking_id', // allowed values: booking_id, sum_persons.
			'data_query'              => array(),
		);
	}

	/**
	 * Legacy query used if lookup table doesn't exist.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array|int|false|object
	 */
	protected function wp_query( $args ) {
		$args = $this->get_wp_query_args( $args );

		$query = new WP_Query( $args );

		$return   = $args['yith_wcbk_return'] ?? 'ids';
		$count_by = $args['yith_wcbk_count'] ?? 'booking_id';

		if ( 'count' === $return ) {
			$count = 0;

			if ( 'sum_persons' === $count_by ) {
				$bookings = array_filter( array_map( 'yith_get_booking', $query->posts ) );
				foreach ( $bookings as $_booking ) {
					$count += $_booking->get_persons();
				}
			} else {
				$count = ! ! $query->posts ? count( $query->posts ) : 0;
			}

			return $count;
		} else {
			$results = 'bookings' === $return ? array_map( 'yith_get_booking', $query->posts ) : $query->posts;

			if ( $args['paginate'] ) {
				$posts_per_page = $args['posts_per_page'];
				$results        = (object) array(
					'items'         => $results,
					'total'         => $query->found_posts,
					'max_num_pages' => $posts_per_page > 0 ? ceil( $query->found_posts / $posts_per_page ) : 1,
				);
			}

			return $results;
		}
	}

	/**
	 * Get WP Query Vars
	 *
	 * @param array $query_vars The query vars.
	 *
	 * @return array
	 */
	public function get_wp_query_args( $query_vars ) {
		$defaults   = $this->get_default_query_args();
		$query_vars = wp_parse_args( $query_vars, $defaults );

		// Validate status values.
		if ( ! $query_vars['status'] ) {
			$query_vars['status'] = 'any';
		} elseif ( 'any' !== $query_vars['status'] ) {
			$query_vars['status'] = array_map( 'yith_wcbk_maybe_prefix_booking_status', (array) $query_vars['status'] );
		}

		$unset_default_keys = array(
			'product_id',
			'order_id',
			'user_id',
			'include',
			'exclude',
			'from',
			'to',
			'whole_duration_in_range',
			'return',
			'count',
		);

		foreach ( $unset_default_keys as $key ) {
			if ( isset( $query_vars[ $key ], $defaults[ $key ] ) && $query_vars[ $key ] === $defaults[ $key ] ) {
				unset( $query_vars[ $key ] );
			}
		}

		$key_mapping = array(
			'status'         => 'post_status',
			'page'           => 'paged',
			'exclude'        => 'post__not_in',
			'include'        => 'post__in',
			'items_per_page' => 'posts_per_page',
			'return'         => 'yith_wcbk_return',
			'count'          => 'yith_wcbk_count',
			'order_by'       => 'orderby',
		);

		foreach ( $key_mapping as $key => $wp_key ) {
			if ( isset( $query_vars[ $key ] ) ) {
				$query_vars[ $key_mapping[ $key ] ] = $query_vars[ $key ];
				unset( $query_vars[ $key ] );
			}
		}

		if ( isset( $query_vars['orderby'] ) && 'id' === $query_vars['orderby'] ) {
			$query_vars['orderby'] = 'ID';
		}

		// From and To needs to be handled individually, since they are date ranges.
		$date_from               = $query_vars['date_from'] ?? false;
		$date_to                 = $query_vars['date_to'] ?? false;
		$whole_duration_in_range = $query_vars['whole_duration_in_range'] ?? false;

		$unset_keys = array( 'from', 'to', 'whole_duration_in_range' );

		foreach ( $unset_keys as $key ) {
			if ( isset( $query_vars[ $key ] ) ) {
				unset( $query_vars[ $key ] );
			}
		}

		$wp_query_args = parent::get_wp_query_args( $query_vars );
		$meta_query    = $wp_query_args['meta_query'] ?? array();
		if ( isset( $query_vars['meta_query'] ) ) {
			$meta_query = array_merge( $meta_query, $query_vars['meta_query'] );
		}

		if ( $date_from ) {
			$date_from = is_numeric( $date_from ) ? $date_from : wc_string_to_timestamp( $date_from );
			if ( $whole_duration_in_range ) {
				$meta_query[] = array(
					'key'     => '_from',
					'value'   => $date_from,
					'compare' => '>=',
				);
			} else {
				$meta_query[] = array(
					'key'     => '_to',
					'value'   => $date_from,
					'compare' => '>',
				);
			}
		}

		if ( $date_to ) {
			$date_to = is_numeric( $date_to ) ? $date_to : wc_string_to_timestamp( $date_to );
			if ( $whole_duration_in_range ) {
				$meta_query[] = array(
					'key'     => '_to',
					'value'   => $date_to,
					'compare' => '<=',
				);
			} else {
				$meta_query[] = array(
					'key'     => '_from',
					'value'   => $date_to,
					'compare' => '<',
				);
			}
		}

		if ( $meta_query ) {
			$wp_query_args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		$wp_query_args['tax_query'] = $query_vars['tax_query'] ?? array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

		if ( isset( $query_vars['data_query'] ) ) {
			/**
			 * Set the 'yith_wcbk_booking_data_query' param, so posts can be filtered by using
			 * 'posts_where' and 'posts_join' hooks, hooked to:
			 * - YITH_WCBK_Booking_Data_Store::filter_posts_where_for_data_query
			 * - YITH_WCBK_Booking_Data_Store::filter_posts_join_for_data_query
			 */
			$wp_query_args['yith_wcbk_booking_data_query'] = $query_vars['data_query'];
		}

		$wp_query_args['post_type'] = YITH_WCBK_Post_Types::BOOKING;
		$wp_query_args['fields']    = 'ids';

		// Handle paginate.
		if ( ! isset( $query_vars['paginate'] ) || ! $query_vars['paginate'] ) {
			$wp_query_args['no_found_rows'] = true;
		}

		return $wp_query_args;
	}

	/**
	 * Map arguments if any WP arg is set, such as meta_query, tax_query, meta_key, meta_value, and so on...
	 * This will map it to the standard query (or data query) and log a deprecated notice.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function map_args_from_wp_args( $args ) {
		$data_query = isset( $args['data_query'] ) && is_array( $args['data_query'] ) ? $args['data_query'] : array();

		if ( isset( $args['meta_key'] ) ) {
			yith_wcbk_doing_it_wrong( 'YITH_WCBK_Booking_Data_Store::query', 'meta_key, meta_value and meta_compare should not be used, since you can use the correct data query instead.', '3.0.0' );
			$key     = $args['meta_key'];
			$value   = $args['meta_value'] ?? '';
			$compare = $args['meta_compare'] ?? '=';

			$data_query[] = array(
				'key'      => $key,
				'value'    => $value,
				'operator' => $compare,
			);
		}

		if ( isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
			yith_wcbk_doing_it_wrong( 'YITH_WCBK_Booking_Data_Store::query', 'meta_query should not be used, since you can use the correct data query instead.', '3.0.0' );
			$data_query[] = $args['meta_query'];
		}

		if ( isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) ) {
			yith_wcbk_doing_it_wrong( 'YITH_WCBK_Booking_Data_Store::query', 'tax_query should not be used, since you can use the correct data query instead.', '3.0.0' );
			$data_query[] = $this->get_data_query_from_wp_tax_query( $args['tax_query'] );
		}

		if ( $data_query ) {
			$args['data_query'] = $data_query;
		}

		return $args;
	}

	/**
	 * Retrieve data query from a WP Tax Query.
	 *
	 * @param array $tax_query The tax query.
	 *
	 * @return array
	 */
	private function get_data_query_from_wp_tax_query( $tax_query ) {
		$data_query = $tax_query;

		if ( is_array( $tax_query ) ) {
			if ( isset( $tax_query['taxonomy'] ) ) {
				$taxonomy = $tax_query['taxonomy'];
				$field    = $tax_query['field'] ?? 'term_id';
				$terms    = $tax_query['terms'] ?? array();
				$operator = $tax_query['operator'] ?? 'IN';

				$data_query = array(
					'data-type' => 'term',
					'taxonomy'  => $taxonomy,
					'terms'     => $terms,
					'operator'  => $operator,
					'field'     => $field,
				);
			} else {
				$data_query = array_map( array( $this, 'get_data_query_from_wp_tax_query' ), $tax_query );
			}
		}

		return $data_query;
	}

	/**
	 * Query for Bookings matching specific criteria.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array|int|false|object
	 */
	public function query( $args ) {
		$args = apply_filters( 'yith_wcbk_pre_get_bookings_initial_args', $args );
		if ( ! yith_wcbk_use_booking_lookup_tables() || ! empty( $args['bk_disable_lookup'] ) ) {
			return $this->wp_query( $args );
		}

		global $wpdb;

		$args = wp_parse_args( $args, $this->get_default_query_args() );
		$args = $this->map_args_from_wp_args( $args ); // Handle deprecated wp args, such as meta_query, tax_query, meta_key, meta_value, and so on...
		$args = apply_filters( 'yith_wcbk_pre_get_bookings_args', $args );

		$select        = "SELECT lookup_table.booking_id FROM {$wpdb->yith_wcbk_booking_meta_lookup} as lookup_table ";
		$select_count  = "SELECT COUNT(*) FROM {$wpdb->yith_wcbk_booking_meta_lookup} as lookup_table ";
		$where         = '';
		$join          = '';
		$group_by      = '';
		$where_clauses = array();

		foreach ( array( 'product_id', 'order_id', 'user_id' ) as $id_key ) {
			if ( false !== $args[ $id_key ] ) {
				if ( is_array( $args[ $id_key ] ) && isset( $args[ $id_key ]['value'] ) ) {
					$ids             = (array) $args[ $id_key ]['value'];
					$allowed_compare = array(
						'IN'     => 'IN',
						'NOT IN' => 'NOT IN',
						'='      => 'IN',
						'!='     => 'NOT IN',
					);
					$compare         = isset( $args[ $id_key ]['compare'] ) ? $args[ $id_key ]['compare'] : 'IN';
					$compare         = array_key_exists( $compare, $allowed_compare ) ? $allowed_compare[ $compare ] : 'IN';
				} else {
					$ids     = (array) $args[ $id_key ];
					$compare = 'IN';
				}
				if ( $ids ) {
					$ids             = implode( ',', array_map( 'absint', $ids ) );
					$where_clauses[] = "lookup_table.$id_key $compare ($ids)";
				} else {
					$where_clauses[] = '0 == 1';
				}
			}
		}

		if ( 'any' !== $args['status'] ) {
			$statuses        = (array) $args['status'];
			$statuses        = array_map( 'yith_wcbk_maybe_prefix_booking_status', $statuses );
			$statuses        = '"' . implode( '","', array_filter( array_map( 'sanitize_title_for_query', $statuses ) ) ) . '"';
			$where_clauses[] = "lookup_table.status IN ($statuses)";
		}

		if ( false !== $args['include'] ) {
			$include         = (array) $args['include'];
			$include         = implode( ',', array_filter( array_map( 'absint', $include ) ) );
			$where_clauses[] = "lookup_table.booking_id IN ($include)";
		}

		if ( false !== $args['exclude'] ) {
			$exclude         = (array) $args['exclude'];
			$exclude         = implode( ',', array_filter( array_map( 'absint', $exclude ) ) );
			$where_clauses[] = "lookup_table.booking_id NOT IN ($exclude)";
		}

		if ( false !== $args['from'] ) {
			$from            = is_numeric( $args['from'] ) ? $args['from'] : wc_string_to_timestamp( $args['from'] );
			$from            = gmdate( 'Y-m-d H:i:s', $from );
			$where_clauses[] = $wpdb->prepare( 'lookup_table.from = %s', $from );
		}

		if ( false !== $args['to'] ) {
			$to              = is_numeric( $args['to'] ) ? $args['to'] : wc_string_to_timestamp( $args['to'] );
			$to              = gmdate( 'Y-m-d H:i:s', $to );
			$where_clauses[] = $wpdb->prepare( 'lookup_table.to = %s', $to );
		}

		if ( false !== $args['date_from'] ) {
			$date_from = is_numeric( $args['date_from'] ) ? $args['date_from'] : wc_string_to_timestamp( $args['date_from'] );
			$date_from = gmdate( 'Y-m-d H:i:s', $date_from );
			if ( $args['whole_duration_in_range'] ) {
				$where_clauses[] = $wpdb->prepare( 'lookup_table.from >= %s', $date_from );
			} else {
				$where_clauses[] = $wpdb->prepare( 'lookup_table.to > %s', $date_from );
			}
		}

		if ( false !== $args['date_to'] ) {
			$date_to = is_numeric( $args['date_to'] ) ? $args['date_to'] : wc_string_to_timestamp( $args['date_to'] );
			$date_to = gmdate( 'Y-m-d H:i:s', $date_to );
			if ( $args['whole_duration_in_range'] ) {
				$where_clauses[] = $wpdb->prepare( 'lookup_table.to <= %s', $date_to );
			} else {
				$where_clauses[] = $wpdb->prepare( 'lookup_table.from < %s', $date_to );
			}
		}

		if ( ! empty( $args['data_query'] ) && is_array( $args['data_query'] ) ) {
			$data_query     = new YITH_WCBK_Booking_Data_Query( $args['data_query'] );
			$sql_data_query = $data_query->get_sql(
				'lookup_table',
				'booking_id',
				$this->get_column_types_for_lookup_table( YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE )
			);

			if ( $sql_data_query['join'] ) {
				$join .= ' ' . $sql_data_query['join'] . ' ';
			}

			if ( $sql_data_query['where'] ) {
				$where_clauses[] = $sql_data_query['where'];
			}
		}

		if ( ! ! $args['resources'] ) {
			$resources = (array) $args['resources'];
			$resources = array_filter( array_map( 'absint', $resources ) );
			if ( $resources ) {
				$join .= ' LEFT JOIN ' . $wpdb->yith_wcbk_booking_resources . ' as resources ON ( lookup_table.booking_id = resources.booking_id ) ';

				$resources       = '"' . implode( '","', $resources ) . '"';
				$where_clauses[] = "resources.resource_id IN ($resources)";
			}
		}

		if ( $where_clauses ) {
			$where = ' WHERE ' . implode( ' AND ', $where_clauses ) . ' ';
		}

		$order_sql        = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		$args['order_by'] = 'id' === $args['order_by'] ? 'booking_id' : sanitize_sql_orderby( $args['order_by'] );
		$order_by         = $args['order_by'];
		$order_by_sql     = sanitize_sql_orderby( "{$order_by} {$order_sql}" );
		$order            = " ORDER BY lookup_table.{$order_by_sql} ";

		$limits = '';
		if ( $args['items_per_page'] >= 0 ) {
			$offset = $args['page'] > 1 ? absint( ( $args['page'] - 1 ) * $args['items_per_page'] ) . ', ' : '';
			$limits = ' LIMIT ' . $offset . absint( $args['items_per_page'] );
		}

		$is_joining          = ! ! trim( $join );
		$use_calc_found_rows = $is_joining;

		if ( $is_joining ) {
			$group_by = ' GROUP BY booking_id';
		}

		if ( 'count' === $args['return'] ) {
			$count_by = in_array( $args['count'], array( 'booking_id', 'sum_persons' ), true ) ? $args['count'] : 'booking_id';
			if ( $is_joining && 'sum_persons' === $count_by ) {
				// When joining the query could retrieve more than one row per booking, so we need a specific query to get the correct count.
				$child_select = "SELECT lookup_table.booking_id, lookup_table.persons FROM {$wpdb->yith_wcbk_booking_meta_lookup} as lookup_table";
				$child_select = '( ' . $child_select . $join . $where . ' GROUP BY booking_id )';
				$query_count  = "SELECT SUM(people_counting.persons) FROM {$child_select} as people_counting";
			} else {
				switch ( $count_by ) {
					case 'sum_persons':
						$count_by = 'SUM(lookup_table.persons)';
						break;
					case 'booking_id':
						// Using DISTINCT to prevent duplicated calculation when joining.
						$count_by = 'COUNT(DISTINCT lookup_table.booking_id)';
						break;
				}
				$select_count = "SELECT {$count_by} FROM {$wpdb->yith_wcbk_booking_meta_lookup} as lookup_table";
				$query_count  = $select_count . $join . $where;
			}

			$results = $wpdb->get_var( $query_count ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
			$results = ! ! $results ? absint( $results ) : false;
		} else {
			if ( $use_calc_found_rows ) {
				$select = str_replace( 'SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $select ); // Just in case of "group by".
			}

			$query       = $select . $join . $where . $group_by . $order . $limits;
			$query_count = $select_count . $join . $where;

			$results = $wpdb->get_col( $query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
			$results = array_map( 'absint', $results );

			if ( 'bookings' === $args['return'] ) {
				$results = array_map( 'yith_get_booking', $results );
			}

			if ( $args['paginate'] ) {
				if ( $use_calc_found_rows ) {
					$total = absint( $wpdb->get_var( 'SELECT FOUND_ROWS()' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				} else {
					// Use specific query with COUNT(*) instead of FOUND_ROWS(), since COUNT(*) is subject to certain optimizations. SQL_CALC_FOUND_ROWS causes some optimizations to be disabled.
					$total = absint( $wpdb->get_var( $query_count ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
				}

				$results = (object) array(
					'items'         => $results,
					'total'         => $total,
					'max_num_pages' => $args['items_per_page'] > 0 ? ceil( $total / $args['items_per_page'] ) : 1,
				);
			}
		}

		return $results;
	}

	/**
	 * Filter posts where if there is a data_query.
	 *
	 * @param string   $where The WHERE clause of the query.
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 *
	 * @return string
	 * @see 'posts_where' hook in WP_Query::get_posts
	 */
	public static function filter_posts_where_for_data_query( $where, $query ) {
		$data_query_args = $query->get( 'yith_wcbk_booking_data_query' );
		if ( $data_query_args ) {
			global $wpdb;
			$data_query     = new YITH_WCBK_Booking_Data_Query( $data_query_args );
			$sql_data_query = $data_query->get_sql( $wpdb->posts, 'ID', array() );

			if ( $sql_data_query['where'] ) {
				$where .= ' AND (' . $sql_data_query['where'] . ') ';
			}
		}

		return $where;
	}

	/**
	 * Filter posts where if there is a data_query.
	 *
	 * @param string   $join  The JOIN clause of the query.
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 *
	 * @return string
	 * @see 'posts_join' hook in WP_Query::get_posts
	 */
	public static function filter_posts_join_for_data_query( $join, $query ) {
		$data_query_args = $query->get( 'yith_wcbk_booking_data_query' );
		if ( $data_query_args ) {
			global $wpdb;
			$data_query     = new YITH_WCBK_Booking_Data_Query( $data_query_args );
			$sql_data_query = $data_query->get_sql( $wpdb->posts, 'ID', array() );

			if ( $sql_data_query['join'] ) {
				$join .= ' ' . $sql_data_query['join'] . ' ';
			}
		}

		return $join;
	}

	/**
	 * Converts a WP post date string into a timestamp.
	 * Added here since in WooCommerce exists since WC 4.8.
	 * TODO: remove, since it exists since WC 4.8.
	 *
	 * @param string $time_string The WP post date string.
	 *
	 * @return int|null The date string converted to a timestamp or null.
	 */
	protected function string_to_timestamp( $time_string ) {
		return '0000-00-00 00:00:00' !== $time_string ? wc_string_to_timestamp( $time_string ) : null;
	}
}

add_filter( 'posts_where', array( 'YITH_WCBK_Booking_Data_Store', 'filter_posts_where_for_data_query' ), 10, 2 );
add_filter( 'posts_join', array( 'YITH_WCBK_Booking_Data_Store', 'filter_posts_join_for_data_query' ), 10, 2 );
