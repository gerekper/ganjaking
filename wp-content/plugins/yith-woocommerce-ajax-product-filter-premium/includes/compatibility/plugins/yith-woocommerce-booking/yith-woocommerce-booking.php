<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH WooCommerce Booking plugin support
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Compatibility
 * @version 4.1.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Booking_Compatibility' ) ) {
	/**
	 * Class that implements methods required to integrate YITH WCAN filters in Booking search page
	 *
	 * @since 4.1.0
	 */
	class YITH_WCAN_Booking_Compatibility {

		/**
		 * Main instance
		 *
		 * @var YITH_WCAN_Booking_Compatibility
		 * @since 4.0.0
		 */
		protected static $instance = null;

		/**
		 * Init integration, hooking all required methods
		 *
		 * @return void
		 */
		public function init() {
			add_filter( 'yith_wcan_query_post_in', array( $this, 'filter_post_in' ) );
			add_filter( 'yith_wcan_query_supported_parameters', array( $this, 'filter_supported_query_vars' ) );
			add_filter( 'yith_wcan_should_process_query', array( $this, 'skip_filtering' ), 10, 2 );
		}

		/**
		 * Filters post in for YITH WCAN queries, including post_in parameter as computed by Booking plugin
		 *
		 * @param array $post_in Array of post ids for the query to include.
		 * @return array Filtered post__in param.
		 */
		public function filter_post_in( $post_in ) {
			$request = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! isset( $request['yith-wcbk-booking-search'] ) || 'search-bookings' !== $request['yith-wcbk-booking-search'] ) {
				return $post_in;
			}

			$search_helper = YITH_WCBK()->search_form_helper;

			if ( ! $search_helper ) {
				return $post_in;
			}

			$product_ids = $search_helper->search_booking_products( $request );

			if ( empty( $post_in ) && ! empty( $product_ids ) ) {
				return $product_ids;
			}

			if ( ! empty( $post_in ) && empty( $product_ids ) ) {
				return $post_in;
			}

			$result_set = array_intersect( $post_in, $product_ids );

			if ( ! $result_set ) {
				return array( 0 );
			}

			return $result_set;
		}

		/**
		 * Filters WCAN supported query vars, to add booking ones.
		 *
		 * @param array $supported_params Array of supported query vars.
		 * @return array Filtered supported vars.
		 */
		public function filter_supported_query_vars( $supported_params ) {
			$request = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! function_exists( 'YITH_WCBK' ) || ! isset( $request['yith-wcbk-booking-search'] ) || 'search-bookings' !== $request['yith-wcbk-booking-search'] ) {
				return $supported_params;
			}

			$supported_params = array_merge(
				$supported_params,
				array(
					'from',
					'to',
					'persons',
					'person_types',
					'services',
					'categories',
					'tags',
					'location',
					'location_range',
				)
			);

			return $supported_params;
		}

		/**
		 * Skip query filtering when processing Booking methods (booking retrieves posts to use in post__in clause when filtering main query)
		 *
		 * @param bool     $should_process_query Whether query should be processed.
		 * @param WP_Query $query Current query object.
		 *
		 * @return bool Filtered param.
		 */
		public function skip_filtering( $should_process_query, $query ) {
			if ( $query->get( 'yith_wcbk_search' ) ) {
				return false;
			}

			return $should_process_query;
		}

		/**
		 * Compatibility class instance
		 *
		 * @return YITH_WCAN_Booking_Compatibility Class unique instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

// init compatibility.
YITH_WCAN_Booking_Compatibility::instance()->init();
