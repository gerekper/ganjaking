<?php
/**
 * Booking Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_booking_statuses' ) ) {
	/**
	 * Return the list of booking statuses
	 *
	 * @param bool $include_accessory_statuses Set true to include accessory statuses.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function yith_wcbk_get_booking_statuses( $include_accessory_statuses = false ) {
		$statuses = array(
			'unpaid'          => _nx( 'Unpaid', 'Unpaid', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			'paid'            => _nx( 'Paid', 'Paid', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			'completed'       => _nx( 'Completed', 'Completed', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			'cancelled'       => _nx( 'Cancelled', 'Cancelled', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			'pending-confirm' => _nx( 'Pending', 'Pending', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			'confirmed'       => _nx( 'Confirmed', 'Confirmed', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
			'unconfirmed'     => _nx( 'Rejected', 'Rejected', 1, 'Booking status', 'yith-booking-for-woocommerce' ),
		);

		if ( $include_accessory_statuses ) {
			$statuses['cancelled_by_user'] = _nx( 'Cancelled by customer', 'Cancelled by customer', 1, 'Booking Status', 'yith-booking-for-woocommerce' );
		}

		return apply_filters( 'yith_wcbk_booking_statuses', $statuses );
	}
}

if ( ! function_exists( 'yith_wcbk_is_a_booking_status' ) ) {

	/**
	 * Check if booking status is valid.
	 *
	 * @param string $status The status.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	function yith_wcbk_is_a_booking_status( $status ) {
		$booking_statuses = yith_wcbk_get_booking_statuses();

		return isset( $booking_statuses[ $status ] );
	}
}

if ( ! function_exists( 'yith_wcbk_maybe_prefix_booking_status' ) ) {
	/**
	 * Prefix a booking status if needed.
	 *
	 * @param string $status The status.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_maybe_prefix_booking_status( $status ) {
		$status = 'bk-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
		if ( yith_wcbk_is_a_booking_status( $status ) ) {
			$status = 'bk-' . $status;
		}

		return $status;
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_status_name' ) ) {
	/**
	 * Get the booking status name
	 *
	 * @param string $status The status.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function yith_wcbk_get_booking_status_name( $status ) {
		return strtr( $status, yith_wcbk_get_booking_statuses() );
	}
}

if ( ! function_exists( 'yith_get_booking' ) ) {

	/**
	 * Get the booking object.
	 *
	 * @param int|WP_Post|YITH_WCBK_Booking|false $booking The booking.
	 *
	 * @return YITH_WCBK_Booking|false false on failure.
	 */
	function yith_get_booking( $booking = false ) {
		global $post;

		if ( false === $booking && is_a( $post, 'WP_Post' ) && get_post_type( $post ) === YITH_WCBK_Post_Types::BOOKING ) {
			$booking_id = absint( $post->ID );
		} elseif ( is_numeric( $booking ) ) {
			$booking_id = $booking;
		} elseif ( $booking instanceof YITH_WCBK_Booking ) {
			$booking_id = $booking->get_id();
		} elseif ( ! empty( $booking->ID ) ) {
			$booking_id = $booking->ID;
		} else {
			$booking_id = false;
		}

		if ( ! $booking_id ) {
			return false;
		}

		try {
			$booking = new YITH_WCBK_Booking( $booking_id );

			return apply_filters( 'yith_wcbk_booking_object', $booking );
		} catch ( Exception $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_booked_statuses' ) ) {
	/**
	 * Return an array of statuses, in which the booking is considered as booked.
	 */
	function yith_wcbk_get_booked_statuses() {
		$statuses = array(
			'bk-unpaid',
			'bk-paid',
			'bk-completed',
			'bk-confirmed',
		);

		return apply_filters( 'yith_wcbk_get_booked_statuses', $statuses );
	}
}

if ( ! function_exists( 'yith_wcbk_get_mark_action_allowed_booking_statuses' ) ) {
	/**
	 * Return an array of statuses, allowed for mark-actions.
	 */
	function yith_wcbk_get_mark_action_allowed_booking_statuses() {
		$statuses = array( 'paid', 'completed', 'confirmed', 'unconfirmed' );

		return apply_filters( 'yith_wcbk_get_mark_action_allowed_booked_statuses', $statuses );
	}
}

if ( ! function_exists( 'yith_wcbk_get_bookings' ) ) {
	/**
	 * Retrieve bookings
	 *
	 * @param array $args The arguments.
	 *
	 * @return array|int|false|YITH_WCBK_Booking[]
	 * @since 3.0.0
	 */
	function yith_wcbk_get_bookings( $args = array() ) {
		try {
			/**
			 * The Booking Data Store
			 *
			 * @var YITH_WCBK_Booking_Data_Store $data_store
			 */
			$data_store = WC_Data_Store::load( 'yith-booking' );

			return $data_store->query( $args );
		} catch ( Exception $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_post_ids' ) ) {
	/**
	 * Retrieve booking post ids.
	 *
	 * @param array $args The get_posts arguments.
	 *
	 * @return int[]
	 * @since 3.0.0
	 */
	function yith_wcbk_get_booking_post_ids( $args = array() ) {
		$all_booking_statuses = array_keys( yith_wcbk_get_booking_statuses() );

		foreach ( $all_booking_statuses as $key => $value ) {
			$all_booking_statuses[ $key ] = 'bk-' . $value;
		}

		$default_args      = array(
			'post_status'    => $all_booking_statuses,
			'posts_per_page' => - 1,
		);
		$args              = wp_parse_args( $args, $default_args );
		$args['post_type'] = YITH_WCBK_Post_Types::BOOKING;
		$args['fields']    = 'ids';

		$ids = get_posts( $args );

		return ! ! $ids ? $ids : array();
	}
}

if ( ! function_exists( 'yith_wcbk_use_booking_lookup_tables' ) ) {
	/**
	 * Return true if the plugin should use the booking lookup tables.
	 *
	 * @since 3.0.0
	 */
	function yith_wcbk_use_booking_lookup_tables() {
		$use = ! get_option( 'yith_wcbk_booking_meta_lookup_table_is_generating', false );

		return ! ! apply_filters( 'yith_wcbk_use_booking_lookup_tables', $use );
	}
}

if ( ! function_exists( 'yith_wcbk_update_product_lookup_tables_is_running' ) ) {
	/**
	 * See if the lookup table is being generated already.
	 *
	 * @return bool
	 * @since 3.0.0
	 */
	function yith_wcbk_update_product_lookup_tables_is_running() {
		$table_updates_pending = WC()->queue()->search(
			array(
				'status'   => 'pending',
				'group'    => 'yith_wcbk_update_booking_lookup_tables',
				'per_page' => 1,
			)
		);

		return (bool) count( $table_updates_pending );
	}
}

if ( ! function_exists( 'yith_wcbk_update_booking_lookup_tables' ) ) {
	/**
	 * Populate lookup table data for products.
	 *
	 * @param bool $truncate Set to true to clean the table and create it from scratch.
	 *
	 * @since 3.0.0
	 */
	function yith_wcbk_update_booking_lookup_tables( bool $truncate = false ) {
		global $wpdb;

		$is_cli        = defined( 'WP_CLI' ) && WP_CLI;
		$booking_count = array_sum( (array) wp_count_posts( YITH_WCBK_Post_Types::BOOKING ) );
		$use_scheduler = ! $is_cli && $booking_count > 5;

		yith_wcbk_logger()->add( 'Lookup table generation started' );
		update_option( 'yith_wcbk_booking_meta_lookup_table_is_generating', true );

		if ( $truncate ) {
			// Empty the lookup table.
			$wpdb->query( "TRUNCATE TABLE {$wpdb->yith_wcbk_booking_meta_lookup}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		// Make a row per booking in lookup table.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"INSERT IGNORE INTO {$wpdb->yith_wcbk_booking_meta_lookup} (`booking_id`)
				SELECT posts.ID
				FROM {$wpdb->posts} posts
				WHERE
				posts.post_type = %s",
				YITH_WCBK_Post_Types::BOOKING
			)
		);

		// List of column names in the lookup table we need to populate.
		$columns = array(
			'product_id',
			'order_id',
			'user_id',
			'status',
			'from',
			'to',
			'persons', // When last column is updated, yith_wcbk_booking_meta_lookup_table_is_generating is updated.
		);

		foreach ( $columns as $index => $column ) {
			if ( $use_scheduler ) {
				WC()->queue()->schedule_single(
					time() + $index,
					'yith_wcbk_update_booking_lookup_tables_column',
					array(
						'column' => $column,
					),
					'yith_wcbk_update_booking_lookup_tables'
				);
			} else {
				yith_wcbk_update_booking_lookup_tables_column( $column );
			}
		}
	}
}

if ( ! function_exists( 'yith_wcbk_update_booking_lookup_tables_column' ) ) {
	/**
	 * Populate lookup table column data.
	 *
	 * @param string $column Column name to set.
	 *
	 * @since 3.0.0
	 */
	function yith_wcbk_update_booking_lookup_tables_column( $column ) {
		if ( empty( $column ) ) {
			return;
		}
		global $wpdb;
		switch ( $column ) {
			case 'status':
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query(
					"
					UPDATE
						{$wpdb->yith_wcbk_booking_meta_lookup} lookup_table
						LEFT JOIN {$wpdb->posts} posts ON lookup_table.booking_id = posts.ID
					SET
						lookup_table.status = posts.post_status
					"
				);
				break;
			case 'product_id':
			case 'order_id':
			case 'user_id':
			case 'persons':
				$meta_key = '_' . $column;
				$column   = esc_sql( $column );
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query(
					$wpdb->prepare(
						"
					UPDATE
						{$wpdb->yith_wcbk_booking_meta_lookup} lookup_table
						LEFT JOIN {$wpdb->postmeta} meta ON lookup_table.booking_id = meta.post_id AND meta.meta_key = %s
					SET
						lookup_table.`{$column}` = meta.meta_value
					",
						$meta_key
					)
				);
				// phpcs:enable
				break;
			case 'from':
			case 'to':
				$meta_key = '_' . $column;
				$column   = esc_sql( $column );
				// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->query(
					$wpdb->prepare(
						"
					UPDATE
						{$wpdb->yith_wcbk_booking_meta_lookup} lookup_table
						LEFT JOIN {$wpdb->postmeta} meta ON lookup_table.booking_id = meta.post_id AND meta.meta_key = %s
					SET
						lookup_table.`{$column}` = CONVERT_TZ(FROM_UNIXTIME(meta.meta_value), @@SESSION.time_zone,'+00:00')
					",
						$meta_key
					)
				);
				// phpcs:enable
				break;
		}

		// Final column - mark complete.
		if ( 'persons' === $column ) {
			delete_option( 'yith_wcbk_booking_meta_lookup_table_is_generating' );
			yith_wcbk_logger()->add( 'Lookup table generation finished' );
		}
	}
}
add_action( 'yith_wcbk_update_booking_lookup_tables_column', 'yith_wcbk_update_booking_lookup_tables_column' );
