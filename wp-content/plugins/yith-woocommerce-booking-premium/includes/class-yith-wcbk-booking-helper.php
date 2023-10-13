<?php
/**
 * Class YITH_WCBK_Booking_Helper
 * helper class: retrieve bookings and booking info
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

/**
 * Load legacy booking helper class.
 */
require_once YITH_WCBK_DIR . 'includes/legacy/class-yith-wcbk-booking-helper-legacy.php';

/**
 * YITH_WCBK_Booking_Helper class.
 */
class YITH_WCBK_Booking_Helper extends YITH_WCBK_Booking_Helper_Legacy {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCBK_Booking_Helper
	 */
	protected static $instance;

	/**
	 * Singleton implementation
	 *
	 * @return YITH_WCBK_Booking_Helper
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Count the number of booked booking of this product
	 * in a specific period
	 *
	 * @param array $args The arguments.
	 *
	 * @return int
	 */
	public function count_booked_bookings_in_period( $args = array() ) {
		$defaults = array(
			'product_id'                => false,
			'from'                      => false,
			'to'                        => false,
			'include_externals'         => true,
			'count_persons_as_bookings' => false,
			'exclude_order_id'          => 0,
			'exclude'                   => false,
			'get_post_args'             => array(), // Deprecated argument.
		);
		$args     = wp_parse_args( $args, $defaults );

		/**
		 * Extract arguments.
		 *
		 * @var int             $product_id                The product ID.
		 * @var int             $from                      The from timestamp.
		 * @var int             $to                        The to timestamp.
		 * @var bool            $include_externals         True if you want to include externals. False otherwise.
		 * @var bool            $count_persons_as_bookings True to count persons as bookings.
		 * @var int             $exclude_order_id          Order ID to exclude.
		 * @var false|int|array $exclude                   Bookings to exclude.
		 * @var array           $get_post_args             Get post arguments [deprecated]
		 */
		list ( $product_id, $from, $to, $count_persons_as_bookings, $exclude_order_id, $exclude, $get_post_args ) =
			yith_plugin_fw_extract( $args, 'product_id', 'from', 'to', 'count_persons_as_bookings', 'exclude_order_id', 'exclude', 'get_post_args' );

		$query_args = array(
			'status'     => yith_wcbk_get_booked_statuses(),
			'product_id' => $product_id,
			'date_from'  => $from,
			'date_to'    => $to,
		);

		if ( $exclude_order_id ) {
			$query_args['order_id'] = array(
				'value'   => $exclude_order_id,
				'compare' => '!=',
			);
		}

		if ( $exclude ) {
			$query_args['exclude'] = $exclude;
		} elseif ( isset( $get_post_args['exclude'] ) ) {
			$query_args['exclude'] = $get_post_args['exclude'];
		}

		$query_args = apply_filters( 'yith_wcbk_booking_helper_count_booked_bookings_in_period_query_args', $query_args, $args );

		$query_args['return'] = 'count';
		if ( $count_persons_as_bookings ) {
			$query_args['count'] = 'sum_persons';
		}

		$count = yith_wcbk_get_bookings( $query_args );
		$count = absint( $count );

		return apply_filters( 'yith_wcbk_booking_helper_count_booked_bookings_in_period', $count, $args );
	}

	/**
	 * Return the max booked bookings per unit in one period
	 *
	 * @param array $args The arguments.
	 *
	 * @return int|mixed
	 */
	public function count_max_booked_bookings_per_unit_in_period( $args = array() ) {
		$date_helper = yith_wcbk_date_helper();
		$count       = 0;
		$defaults    = array(
			'product_id'                => false,
			'from'                      => false,
			'to'                        => false,
			'unit'                      => 'day',
			'include_externals'         => true,
			'count_persons_as_bookings' => false,
			'exclude_order_id'          => 0,
			'exclude'                   => false,
			'get_post_args'             => array(), // Deprecated argument!
			'return'                    => 'max_by_unit',
		);
		$args        = wp_parse_args( $args, $defaults );
		$args        = apply_filters( 'yith_wcbk_count_booked_booking_in_period_args', $args );

		$from   = $args['from'];
		$to     = $args['to'];
		$unit   = $args['unit'];
		$return = $args['return'];

		if ( $from && $to && in_array( $unit, array( 'month', 'day', 'hour', 'minute' ), true ) ) {

			if ( 'max_by_unit' === $return ) {
				$counter        = array();
				$current_from   = $from;
				$unit_increment = 'minute' === $unit ? yith_wcbk_get_minimum_minute_increment() : 1;

				while ( $current_from < $to ) {
					$current_to   = $date_helper->get_time_sum( $current_from, $unit_increment, $unit );
					$current_args = array_merge(
						$args,
						array(
							'from' => $current_from,
							'to'   => $current_to,
						)
					);
					$counter[]    = $this->count_booked_bookings_in_period( $current_args );

					$current_from = $current_to;
				}

				if ( $counter ) {
					$count = max( $counter );
				}
			} else {
				$count = $this->count_booked_bookings_in_period( $args );
			}
		}

		return $count;
	}

	/**
	 * Return count of bookings with specific status
	 *
	 * @param string|array $status The status.
	 *
	 * @return int
	 */
	public function count_bookings_with_status( $status ) {
		$counter = 0;
		if ( ! is_array( $status ) ) {
			$status = array( $status );
		}
		$counts = (array) wp_count_posts( YITH_WCBK_Post_Types::BOOKING );
		foreach ( $status as $s ) {
			if ( yith_wcbk_is_a_booking_status( $s ) ) {
				$counter += isset( $counts[ 'bk-' . $s ] ) ? absint( $counts[ 'bk-' . $s ] ) : 0;
			}
		}

		return $counter;
	}

	/**
	 * Get all future bookings of a booking product
	 *
	 * @param int    $product_id The id of the product.
	 * @param string $return     The return type.
	 *
	 * @return YITH_WCBK_Booking[]
	 * @since  2.0.0
	 */
	public function get_future_bookings_by_product( $product_id, $return = 'bookings' ) {
		if ( ! $product_id ) {
			return array();
		}

		$today = gmdate( 'Y-m-d H:i:s', strtotime( 'now midnight' ) );
		$args  = array(
			'status'     => yith_wcbk_get_booked_statuses(),
			'product_id' => absint( $product_id ),
			'data_query' => array(
				'relation' => 'OR',
				array(
					'key'      => 'from',
					'value'    => $today,
					'operator' => '>=',
				),
				array(
					'key'      => 'to',
					'value'    => $today,
					'operator' => '>=',
				),
			),
		);
		$args  = apply_filters( 'yith_wcbk_get_future_bookings_by_product_args', $args, $product_id );

		$bookings = yith_wcbk_get_bookings( $args );

		if ( 'posts' === $return ) {
			$bookings = array_filter( array_map( 'get_post', $bookings ) );
		}

		return $bookings;
	}

	/**
	 * Get all bookings of a user
	 *
	 * @param int    $user_id the id of the user.
	 * @param string $return  The return type.
	 *
	 * @return YITH_WCBK_Booking[]
	 */
	public function get_bookings_by_user( $user_id, $return = 'bookings' ) {
		if ( ! $user_id ) {
			return array();
		}

		$bookings = yith_wcbk_get_bookings(
			array(
				'user_id' => $user_id,
				'return'  => 'bookings' === $return ? 'bookings' : 'ids',
			)
		);

		if ( 'posts' === $return ) {
			$bookings = array_filter( array_map( 'get_post', $bookings ) );
		}

		return $bookings;
	}

	/**
	 * Get all bookings of a booking product
	 *
	 * @param int    $product_id the id of the product.
	 * @param string $return     The return type.
	 *
	 * @return YITH_WCBK_Booking[]
	 */
	public function get_bookings_by_product( $product_id, $return = 'bookings' ) {
		if ( ! $product_id ) {
			return array();
		}

		$bookings = yith_wcbk_get_bookings(
			array(
				'product_id' => $product_id,
				'return'     => 'bookings' === $return ? 'bookings' : 'ids',
			)
		);

		if ( 'posts' === $return ) {
			$bookings = array_filter( array_map( 'get_post', $bookings ) );
		}

		return $bookings;
	}

	/**
	 * Get bookings by order
	 *
	 * @param int       $order_id      The id of the order.
	 * @param int|false $order_item_id The id of the order item.
	 *
	 * @return YITH_WCBK_Booking[]
	 */
	public function get_bookings_by_order( $order_id, $order_item_id = false ) {
		if ( ! $order_id ) {
			return array();
		}

		$bookings = apply_filters( 'yith_wcbk_get_bookings_by_order', null, $order_id, $order_item_id );
		if ( is_null( $bookings ) ) {

			$args = array(
				'order_id' => $order_id,
			);

			if ( ! ! $order_item_id ) {
				$args['data_query'] = array(
					array(
						'key'   => '_order_item_id',
						'value' => $order_item_id,
					),
				);
			}

			$args = apply_filters( 'yith_wcbk_get_bookings_by_order_args', $args, $order_id, $order_item_id );

			$args['return'] = 'bookings';

			$bookings = yith_wcbk_get_bookings( $args );
		}

		return $bookings;
	}

	/**
	 * Retrieve bookings in a specific time range.
	 *
	 * @param int          $from              The from date.
	 * @param int          $to                The to date.
	 * @param array|string $duration_unit     The duration unit.
	 * @param bool         $include_externals Set true to include externals.
	 * @param bool|int     $product_id        The product ID.
	 *
	 * @return YITH_WCBK_Booking_Abstract[]
	 */
	public function get_bookings_in_time_range( $from, $to, $duration_unit = 'all', $include_externals = true, $product_id = false ) {
		$params = compact( 'from', 'to', 'duration_unit', 'include_externals', 'product_id' );
		$args   = array(
			'product_id'              => $product_id,
			'date_from'               => $from,
			'date_to'                 => $to,
			'whole_duration_in_range' => false,
			'data_query'              => array(),
			'order_by'                => 'from',
			'order'                   => 'ASC',
			'return'                  => 'bookings',
		);

		if ( 'all' !== $duration_unit ) {
			$args['data_query'][] = array(
				'key'     => '_duration_unit',
				'value'   => (array) $duration_unit,
				'compare' => 'IN',
			);
		}

		$args = apply_filters( 'yith_wcbk_booking_helper_get_bookings_in_time_range_args', $args );

		$bookings = yith_wcbk_get_bookings( $args );

		return apply_filters( 'yith_wcbk_booking_helper_get_bookings_in_time_range', $bookings, $params, $args );
	}

}

/**
 * Unique access to instance of YITH_WCBK_Booking_Helper class
 *
 * @return YITH_WCBK_Booking_Helper
 */
function yith_wcbk_booking_helper() {
	return YITH_WCBK_Booking_Helper::get_instance();
}
