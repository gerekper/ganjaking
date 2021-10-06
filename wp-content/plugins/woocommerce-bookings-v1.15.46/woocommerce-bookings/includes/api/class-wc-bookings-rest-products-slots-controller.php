<?php
/**
 * REST API for bookings objects.
 *
 * Handles requests to the /bookings endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Products controller class.
 */
class WC_Bookings_REST_Products_Slots_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = WC_Bookings_REST_API::V1_NAMESPACE;

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'products/slots';

	/**
	 * Register the route for bookings slots.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * Abbreviations constants.
	 */
	const AVAILABLE = 'a';
	const BOOKED    = 'b';
	const DATE      = 'd';
	const DURATION  = 'du';
	const ID        = 'i';

	/**
	 * Mapping of abbrieviations to expanded versions of lables.
	 * Used to minimize storred transient size.
	 */
	protected $transient_keys_mapping = array(
		self::AVAILABLE => 'available',
		self::BOOKED    => 'booked',
		self::DATE      => 'date',
		self::DURATION  => 'duration',
		self::ID        => 'product_id',
	);

	/**
	 * @param $availablity with abbreviated lables.
	 *
	 * @return object with lables expanded to their full version.
	 */
	public function transient_expand( $availability ) {
		$expanded_availability = [];
		foreach ( $availability['records'] as $key => $slot ) {
			$expanded_slot = [];
			foreach ( $slot as $abbrieviation  => $value ) {
				$expanded_slot[ $this->transient_keys_mapping[ $abbrieviation ] ] = $value;
			}
			$expanded_availability[] = $expanded_slot;
		}
		return array(
			'records' => $expanded_availability,
			'count'   => $availability['count'],
		);
	}

	/**
	 * Format timestamp to the shortest reasonable format usable in API.
	 *
	 * @param $timestamp
	 * @param $timezone DateTimeZone
	 *
	 * @return string
	 */
	public function get_time( $timestamp, $timezone ) {
		$server_time = new DateTime( date( 'Y-m-d\TH:i:s', $timestamp ), $timezone );
		return $server_time->format( "Y-m-d\TH:i" );
	}

	/**
	 * Get available bookings slots.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {
		$product_ids    = ! empty( $request['product_ids'] ) ? array_map( 'absint', explode( ',', $request['product_ids'] ) ) : array();
		$category_ids   = ! empty( $request['category_ids'] ) ? array_map( 'absint', explode( ',', $request['category_ids'] ) ) : array();
		$resource_ids   = ! empty( $request['resource_ids'] ) ? array_map( 'absint', explode( ',', $request['resource_ids'] ) ) : array();
		$get_past_times = isset( $request['get_past_times'] ) && 'true' === $request['get_past_times'] ? true : false;

		$min_date         = isset( $request['min_date'] ) ? strtotime( urldecode( $request['min_date'] ) ) : 0;
		$max_date         = isset( $request['max_date'] ) ? strtotime( urldecode( $request['max_date'] ) ) : 0;
		$timezone         = new DateTimeZone( wc_booking_get_timezone_string() );

		$page             = isset( $request['page'] ) ? absint( $request['page'] ) : false;
		$records_per_page = isset( $request['limit'] ) ? absint( $request['limit'] ) : 10;
		$hide_unavailable = isset( $request['hide_unavailable'] ) && 'true' === $request['hide_unavailable'] ? true : false;

		// If no product ids are specified, just use all products.
		if ( empty( $product_ids ) ) {
			$product_ids = WC_Data_Store::load( 'product-booking' )->get_bookable_product_ids_for_slots_rest_endpoint();
		}

		$products = array_filter( array_map( function( $product_id ) {
			return wc_get_product( $product_id );
		}, $product_ids ) );

		foreach( $products as $product ) {
			$is_vaild_rest_type = 'booking' === $product->get_type();
			$is_vaild_rest_type = apply_filters( "woocommerce_bookings_product_type_rest_check", $is_vaild_rest_type, $product );
			if ( ! $is_vaild_rest_type ) {
				wp_send_json( __( 'Not a bookable product', 'woocommerce-bookings' ), 400 );
			}
		}

		// If category ids are specified filter the product ids.
		if ( ! empty( $category_ids ) ) {
			$products = array_filter( $products, function( $product ) use ( $category_ids ) {
				$product_id = $product->get_id();

				return array_reduce( $category_ids, function( $is_in_category, $category_id ) use ( $product_id ) {
					$term = get_term_by( 'id', $category_id, 'product_cat' );

					if ( ! $term ) {
						return $is_in_category;
					}

					return $is_in_category || has_term( $term, 'product_cat', $product_id );
				}, false );
			} );
		}

		// Get product ids from products after they filtered by categories.
		$product_ids = array_filter( array_map( function( $product ) {
			return $product->get_id();
		 }, $products ) );

		$transient_name               = 'booking_slots_' . md5( http_build_query( array( $product_ids, $category_ids, $resource_ids, $get_past_times, $min_date, $max_date, $page, $records_per_page ) ) );
		$booking_slots_transient_keys = array_filter( (array) WC_Bookings_Cache::get( 'booking_slots_transient_keys' ) );
		$cached_availabilities        = WC_Bookings_Cache::get( $transient_name );

		if ( $cached_availabilities ) {
			$availability = wc_bookings_paginated_availability( $cached_availabilities, $page, $records_per_page );
			return $this->transient_expand( $availability );
		}

		foreach ( $product_ids as $product_id ) {
			if ( ! isset( $booking_slots_transient_keys[ $product_id ] ) ) {
				$booking_slots_transient_keys[ $product_id ] = array();
			}

			$booking_slots_transient_keys[ $product_id ][] = $transient_name;
		}

		// Give array of keys a long ttl because if it expires we won't be able to flush the keys when needed.
		// We can't use 0 to never expire because then WordPress will autoload the option on every page.
		WC_Bookings_Cache::set( 'booking_slots_transient_keys', $booking_slots_transient_keys, YEAR_IN_SECONDS );

		// Calculate partially booked/fully booked/unavailable days for each product.
		$booked_data = array_values( array_map( function( $bookable_product ) use ( $min_date, $max_date, $resource_ids, $get_past_times, $timezone, $hide_unavailable ) {
			if ( empty( $min_date ) ) {
				// Determine a min and max date
				$min_date = strtotime( 'today' );
			}

			if ( empty( $max_date ) ) {
				$max_date = strtotime( 'tomorrow' );
			}

			$product_resources = $bookable_product->get_resource_ids() ?: array();
			$duration          = $bookable_product->get_duration();
			$availability      = array();

			$resources = empty( $product_resources ) ? array( 0 ) : $product_resources;
			if ( ! empty( $resource_ids ) ) {
				$resources = array_intersect( $resources, $resource_ids );
			}

			// Get slots for days before and after, which accounts for timezone differences.
			$start_date = strtotime( '-1 day', $min_date );
			$end_date   = strtotime( '+1 day', $max_date );

			foreach ( $resources as $resource_id ) {
				$blocks           = $bookable_product->get_blocks_in_range( $start_date, $end_date, array(), $resource_id, array(), $get_past_times );
				$available_blocks = $bookable_product->get_time_slots( $blocks, $resource_id, $start_date, $end_date, true );
				foreach ( $available_blocks as $timestamp => $data ) {

					// Filter blocks outside of timerange.
					if ( $timestamp < $min_date || $timestamp >= $max_date ) {
						continue;
					}

					unset( $data['resources'] );
					if ( ! $hide_unavailable || 1 <= $data['available'] ) {
						$availability[] = array(
							self::DATE      => $this->get_time( $timestamp, $timezone ),
							self::DURATION  => $duration,
							self::AVAILABLE => $data['available'],
							self::BOOKED    => $data['booked'],
						);
					}
				}
			}

			$data = array(
				'product_id'   => $bookable_product->get_id(),
				'availability' => $availability,
			);

			return $data;
		}, $products ) );

		$booked_data = apply_filters( 'woocommerce_bookings_rest_slots_get_items', $booked_data );

		$cached_availabilities = array_merge( ...array_map( function( $value ) {
			return array_map( function( $availability ) use ( $value ) {
				$availability[self::ID]   = $value['product_id'];
				return $availability;
			}, $value['availability'] );
		}, $booked_data ) );

		// Sort by date.
		usort( $cached_availabilities, function( $a, $b ) {
			return $a[self::DATE] > $b[self::DATE];
		} );

		// This transient should be cleared when booking or products are added or updated but keep it short just in case.
		WC_Bookings_Cache::set( $transient_name, $cached_availabilities, HOUR_IN_SECONDS );

		$availability =  wc_bookings_paginated_availability( $cached_availabilities, $page, $records_per_page );
		return $this->transient_expand( $availability );
	}
}
