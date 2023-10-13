<?php
/**
 * Class YITH_WCBK_Booking_Helper
 * helper class: retrieve bookings and booking info
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

/**
 * YITH_WCBK_Booking_Helper class.
 */
class YITH_WCBK_Booking_Helper_Legacy {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCBK_Booking_Helper_Legacy
	 */
	protected static $instance;

	/**
	 * The booking post type name
	 *
	 * @var string
	 */
	public $post_type_name;

	/**
	 * Singleton implementation
	 *
	 * @return YITH_WCBK_Booking_Helper_Legacy
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * YITH_WCBK_Booking_Helper constructor.
	 */
	protected function __construct() {
		$this->post_type_name = YITH_WCBK_Post_Types::BOOKING;
	}

	/**
	 * Return count of bookings with specific status
	 *
	 * @param string|array $status The status.
	 *
	 * @return int
	 * @deprecated 3.0.0 | use YITH_WCBK_Booking_Helper::count_bookings_with_status instead
	 */
	public function count_booking_with_status( $status ) {
		return yith_wcbk_booking_helper()->count_bookings_with_status( $status );
	}

	/**
	 * Get all bookings by arguments
	 *
	 * @param array  $args   argument for get_posts.
	 * @param string $return the object type returned.
	 *
	 * @return YITH_WCBK_Booking[]|int[]|WP_Post[]
	 * @deprecated 3.0.0 | use yith_wcbk_get_bookings instead
	 */
	public function get_bookings( $args = array(), $return = 'bookings' ) {
		yith_wcbk_deprecated_function( 'YITH_WCBK_Booking_Helper::get_bookings', '3.0.0', 'yith_wcbk_get_bookings' );

		$ids = yith_wcbk_get_booking_post_ids( $args );

		switch ( $return ) {
			case 'ids':
				return $ids;
			case 'posts':
				return array_filter( array_map( 'get_post', $ids ) );
			case 'bookings':
			default:
				return array_filter( array_map( 'yith_get_booking', $ids ) );
		}
	}

	/**
	 * Get all bookings by meta_query
	 *
	 * @param array  $meta_query The meta query.
	 * @param string $return     The object type returned.
	 *
	 * @return YITH_WCBK_Booking[]
	 * @deprecated 3.0.0 | use yith_wcbk_get_bookings instead.
	 */
	public function get_bookings_by_meta( $meta_query, $return = 'bookings' ) {
		$args = array(
			'meta_query' => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'return'     => 'bookings' === $return ? 'bookings' : 'ids',
		);

		$bookings = yith_wcbk_get_bookings( $args );

		if ( 'posts' === $return ) {
			$bookings = array_filter( array_map( 'get_post', $bookings ) );
		}

		return $bookings;
	}

	/**
	 * Parse posts and return array of YITH_WCBK_Booking
	 *
	 * @param WP_Post[]|int[] $bookings The posts or ids.
	 *
	 * @return YITH_WCBK_Booking[]
	 * @deprecated since 2.0.0 use yith_get_booking to parse to booking objects
	 */
	public function parse_bookings_from_posts( $bookings ) {
		return array_filter( array_map( 'yith_get_booking', $bookings ) );
	}
}
