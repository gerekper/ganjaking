<?php
/**
 * This class handle ajax callbacks for Bookings.
 *
 * @package WooCommerce Bookings
 */

/**
 * Bookings WC ajax callbacks.
 */
class WC_Bookings_WC_Ajax {
	const AJAX_DEPRECATION_VERSION = '1.14.0';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wc_ajax_wc_bookings_find_booking_slots', array( $this, 'find_booking_slots' ) );
		add_action( 'wc_ajax_wc_bookings_find_booked_day_blocks', array( $this, 'find_booked_day_blocks' ) );
		add_action( 'wc_ajax_wc_bookings_get_all_bookable_products', array( $this, 'get_all_bookable_products' ) );
		add_action( 'wc_ajax_wc_bookings_get_all_categories_with_bookable_products', array( $this, 'get_all_categories_with_bookable_products' ) );
		add_action( 'wc_ajax_wc_bookings_get_all_resources', array( $this, 'get_all_resources' ) );
		add_action( 'wc_ajax_wc_bookings_add_booking_to_cart', array( $this, 'add_booking_to_cart' ) );
	}

	/**
	 * Find booking slots for:
	 * - A list of products
	 * - For a specific date range
	 * - Filtered by categories
	 * - Filtered by resources
	 */
	public function find_booking_slots() {
		wc_deprecated_function( __METHOD__, self::AJAX_DEPRECATION_VERSION, 'REST endpoint /wp-json/wc-bookings/v1/products/slots' );

		check_ajax_referer( 'find-booking-slots', 'security' );

		// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$product_ids  = ! empty( $_GET['product_ids'] ) ? array_map( 'absint', explode( ',', wp_unslash( $_GET['product_ids'] ) ) ) : array();
		$category_ids = ! empty( $_GET['category_ids'] ) ? array_map( 'absint', explode( ',', wp_unslash( $_GET['category_ids'] ) ) ) : array();
		$resource_ids = ! empty( $_GET['resource_ids'] ) ? array_map( 'absint', explode( ',', wp_unslash( $_GET['resource_ids'] ) ) ) : array();

		$min_date = isset( $_GET['min_date'] ) ? strtotime( sanitize_text_field( urldecode( wp_unslash( $_GET['min_date'] ) ) ) ) : 0;
		$max_date = isset( $_GET['max_date'] ) ? strtotime( sanitize_text_field( urldecode( wp_unslash( $_GET['max_date'] ) ) ) ) : 0;

		$intervals = isset( $_GET['intervals'] ) ? array_slice( array_map( 'absint', explode( ',', wp_unslash( $_GET['intervals'] ) ) ), 0, 2 ) : array();

		$timezone_offset = isset( $_GET['timezone_offset'] ) ? absint( wp_unslash( $_GET['timezone_offset'] ) ) : 0;
		// phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$page                         = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : false;
		$records_per_page             = 10;
		$transient_name               = 'booking_slots_' . md5( http_build_query( array( $product_ids, $category_ids, $resource_ids, $min_date, $max_date, $intervals, $timezone_offset ) ) );
		$booking_slots_transient_keys = array_filter( (array) WC_Bookings_Cache::get( 'booking_slots_transient_keys' ) );

		$cached_availabilities = WC_Bookings_Cache::get( $transient_name );

		if ( $cached_availabilities ) {
			wp_send_json( wc_bookings_paginated_availability( $cached_availabilities, $page, $records_per_page ) );
		}

		// If no product ids are specified, just use all products.
		if ( empty( $product_ids ) ) {
			$product_ids = WC_Data_Store::load( 'product-booking' )->get_bookable_product_ids();
		}

		$needs_cache_set = false;
		foreach ( $product_ids as $product_id ) {
			if ( ! isset( $booking_slots_transient_keys[ $product_id ] ) ) {
				$booking_slots_transient_keys[ $product_id ] = array();
			}

			// Don't store in cache if it already exists there.
			if ( ! in_array( $transient_name, $booking_slots_transient_keys[ $product_id ], true ) ) {
				$booking_slots_transient_keys[ $product_id ][] = $transient_name;
				$needs_cache_set                               = true;
			}
		}

		// Only set cache if existing cached data has changed.
		if ( $needs_cache_set ) {
			WC_Bookings_Cache::set( 'booking_slots_transient_keys', $booking_slots_transient_keys, YEAR_IN_SECONDS );
		}

		$products = array_filter(
			array_map(
				function( $product_id ) {
					return get_wc_product_booking( $product_id );
				},
				$product_ids
			)
		);

		// If category ids are specified filter the product ids.
		if ( ! empty( $category_ids ) ) {
			$products = array_filter(
				$products,
				function( $product ) use ( $category_ids ) {
					$product_id = $product->get_id();

					return array_reduce(
						$category_ids,
						function( $is_in_category, $category_id ) use ( $product_id ) {
							$term = get_term_by( 'id', $category_id, 'product_cat' );

							if ( ! $term ) {
								return $is_in_category;
							}

							return $is_in_category || has_term( $term, 'product_cat', $product_id );
						},
						false
					);
				}
			);
		}

		// Calculate partially booked/fully booked/unavailable days for each product.
		$booked_data = array_values(
			array_map(
				function( $bookable_product ) use ( $min_date, $max_date, $timezone_offset, $resource_ids, $intervals ) {
					if ( empty( $min_date ) ) {
						// Determine a min and max date.
						$min_date = strtotime( 'today' );
					}

					if ( empty( $max_date ) ) {
						$max_date = strtotime( 'tomorrow' );
					}

					if ( empty( $intervals ) ) {
						$default_interval = 'hour' === $bookable_product->get_duration_unit() ? $bookable_product->get_duration() * 60 : $bookable_product->get_duration();
						$intervals        = array( $default_interval, $default_interval );
					}

					// phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
					$product_resources = $bookable_product->get_resource_ids() ?: array();
					$availability      = array();

					$resources = empty( $product_resources ) ? array( 0 ) : $product_resources;
					if ( ! empty( $resource_ids ) ) {
						$resources = array_intersect( $resources, $resource_ids );
					}

					foreach ( $resources as $resource_id ) {
						$blocks           = $bookable_product->get_blocks_in_range( $min_date, $max_date );
						$available_blocks = wc_bookings_get_time_slots( $bookable_product, $blocks, $intervals, $resource_id, $min_date, $max_date );
						foreach ( $available_blocks as $timestamp => $data ) {
							$data['resources'] = (object) $data['resources'];
							$availability[]    = array_merge(
								array(
									'date'          => get_time_as_iso8601( $timestamp ),
									'duration'      => $bookable_product->get_duration(),
									'duration_unit' => $bookable_product->get_duration_unit(),
								),
								$data
							);
						}
					}

					$data = array(
						'product_id'   => $bookable_product->get_id(),
						'availability' => $availability,
						'title'        => $bookable_product->get_title(),
						'cost'         => wc_price( $bookable_product->get_cost() ),
					);

					return $data;
				},
				$products
			)
		);

		$cached_availabilities = array_merge(
			...array_map(
				function( $value ) {
					return array_map(
						function( $availability ) use ( $value ) {
							$availability['product_id'] = $value['product_id'];
							$availability['title']      = $value['title'];
							$availability['cost']       = $value['cost'];
							return $availability;
						},
						$value['availability']
					);
				},
				$booked_data
			)
		);

		WC_Bookings_Cache::set( $transient_name, $cached_availabilities, HOUR_IN_SECONDS );

		wp_send_json( wc_bookings_paginated_availability( $cached_availabilities, $page, $records_per_page ) );
	}

	/**
	 * This endpoint is supposed to replace the back-end logic in booking-form.
	 *
	 * @since 1.15.70 When there is no resource ID, supplies `WC_Bookings_Controller::find_booked_day_blocks()` with an empty array.
	 */
	public function find_booked_day_blocks() {
		check_ajax_referer( 'find-booked-day-blocks', 'security' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		$product_id  = ! empty( $_GET['product_id'] ) ? absint( $_GET['product_id'] ) : null;
		$resource_id = ! empty( $_GET['resource_id'] ) ? absint( $_GET['resource_id'] ) : null;

		if ( empty( $product_id ) ) {
			wp_send_json_error( 'Missing product ID' );
			exit;
		}

		try {

			$args                          = array();
			$product                       = get_wc_product_booking( $product_id );
			$args['availability_rules']    = array();
			$args['availability_rules'][0] = $product->get_availability_rules();

			$get_min_date = $product->get_min_date();
			$get_max_date = $product->get_max_date();

			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$min_date_bookable = strtotime( "+{$get_min_date['value']} {$get_min_date['unit']}", current_time( 'timestamp' ) );
			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$max_date_bookable = strtotime( "+{$get_max_date['value']} {$get_max_date['unit']}", current_time( 'timestamp' ) );

			// If a buffer is used, subtract it from the min date bookable in the
			// future to cover and display the bookings made during that time.
			// Fix for https://github.com/woocommerce/woocommerce-bookings/issues/3509.
			$interval_in_minutes   = $product->get_time_interval_in_minutes();
			$amount_of_buffer_days = $product->get_buffer_period();
			$buffer_in_seconds     = $interval_in_minutes * $amount_of_buffer_days * 60;
			$min_date_bookable     = $min_date_bookable - $buffer_in_seconds;

			// If the date is provided, use it only if it is a valid Unix timestamp, and it is after/before the min/max bookable time.
			// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
			$min_date = $args['min_date'] = isset( $_GET['min_date'] )
											&& false !== strtotime( sanitize_text_field( wp_unslash( $_GET['min_date'] ) ) )
											&& strtotime( sanitize_text_field( wp_unslash( $_GET['min_date'] ) ) ) > $min_date_bookable ? strtotime( sanitize_text_field( wp_unslash( $_GET['min_date'] ) ) ) : $min_date_bookable;

			// phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
			$max_date = $args['max_date'] = isset( $_GET['max_date'] )
											&& false !== strtotime( sanitize_text_field( wp_unslash( $_GET['max_date'] ) ) )
											&& strtotime( sanitize_text_field( wp_unslash( $_GET['max_date'] ) ) ) < $max_date_bookable ? strtotime( sanitize_text_field( wp_unslash( $_GET['max_date'] ) ) ) : $max_date_bookable;

			$timezone_offset = isset( $_GET['timezone_offset'] ) ? sanitize_text_field( wp_unslash( $_GET['timezone_offset'] ) ) : 0;

			if ( $product->has_resources() ) {
				foreach ( $product->get_resources() as $resource ) {
					$args['availability_rules'][ $resource->ID ] = $product->get_availability_rules( $resource->ID );
				}
			}

			$booked = WC_Bookings_Controller::find_booked_day_blocks(
				$product_id,
				$min_date,
				$max_date,
				'Y-n-j',
				$timezone_offset,
				$resource_id ? array( $resource_id ) : array()
			);

			$args['partially_booked_days'] = $booked['partially_booked_days'];
			$args['fully_booked_days']     = $booked['fully_booked_days'];
			$args['unavailable_days']      = $booked['unavailable_days'];
			$args['restricted_days']       = $product->has_restricted_days() ? $product->get_restricted_days() : false;
			$args['old_availability']      = isset( $booked['old_availability'] ) && true === $booked['old_availability'];

			$buffer_days = array();
			if ( ! in_array( $product->get_duration_unit(), array( 'minute', 'hour' ), true ) ) {
				$buffer_days = WC_Bookings_Controller::get_buffer_day_blocks_for_booked_days( $product, $args['fully_booked_days'] );
			}

			$args['buffer_days'] = $buffer_days;

			/**
			 * Filter the find booked day blocks results.
			 *
			 * @since 1.15.79
			 *
			 * @param array              $args        Result.
			 * @param array              $booked      Booked blocks.
			 * @param WC_Product_Booking $product     Product.
			 * @param int                $resource_id Resource ID.
			 */
			$args = apply_filters( 'woocommerce_bookings_find_booked_day_blocks', $args, $booked, $product, $resource_id );

			wp_send_json( $args );

		} catch ( Exception $e ) {

			wp_die();

		}
	}

	/**
	 * Ajax request controller.
	 *
	 * This function should return all the bookable products.
	 *
	 * @since 1.13.0
	 */
	public function get_all_bookable_products() {
		wc_deprecated_function( __METHOD__, self::AJAX_DEPRECATION_VERSION, 'REST endpoint /wp-json/wc-bookings/v1/products' );

		check_ajax_referer( 'get-all-bookable-products', 'security' );

		try {
			/**
			 * Filter the arguments used to get all bookable products.
			 *
			 * @since 1.13.0
			 *
			 * @param array $args Arguments used to get all bookable products.
			 */
			$args = apply_filters(
				'get_booking_products_args',
				array(
					'post_status'      => 'publish',
					'post_type'        => 'product',
					'posts_per_page'   => -1,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					'tax_query'        => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => 'booking',
						),
					),
					'suppress_filters' => true,
				)
			);

			$payload = new WP_Query( $args );

			wp_send_json( $payload->posts );
		} catch ( Exception $e ) {
			wp_die();
		}
	}

	/**
	 * Ajax request controller.
	 *
	 * This function should return all categories that contain bookable products.
	 *
	 * @since 1.13.0
	 */
	public function get_all_categories_with_bookable_products() {
		wc_deprecated_function( __METHOD__, self::AJAX_DEPRECATION_VERSION, 'REST endpoint /wp-json/wc-bookings/v1/products/categories' );

		check_ajax_referer( 'get-all-categories-with-bookable-products', 'security' );

		try {
			$categories         = array();
			$product_categories = get_terms( 'product_cat' );

			if ( ! is_array( $product_categories ) ) {
				wp_send_json( $categories );
			}

			foreach ( $product_categories as $product_category ) {
				/**
				 * Filter the get categories booking products args.
				 *
				 * @since 1.13.0
				 *
				 * @param array $args Query args.
				 */
				$args = apply_filters(
					'get_categories_booking_products_args',
					array(
						'posts_per_page' => -1,
						'post_type'      => 'product',
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						'tax_query'      => array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'slug',
								'terms'    => $product_category->slug,
							),
							array(
								'taxonomy' => 'product_type',
								'field'    => 'slug',
								'terms'    => 'booking',
							),
						),
					),
					$product_category
				);

				$products = new WP_Query( $args );

				if ( $products->have_posts() ) {
					$categories[] = array(
						'id'   => $product_category->term_id,
						'name' => $product_category->name,
					);
				}
			}

			wp_send_json( $categories );
		} catch ( Exception $e ) {
			wp_die();
		}
	}

	/**
	 * Ajax request controller.
	 *
	 * This function should return product resources.
	 *
	 * @since 1.13.0
	 */
	public function get_all_resources() {
		wc_deprecated_function( __METHOD__, self::AJAX_DEPRECATION_VERSION, 'REST endpoint /wp-json/wc-bookings/v1/resources' );

		check_ajax_referer( 'get-all-resources', 'security' );

		try {
			/**
			 * Filter the get all resources args.
			 *
			 * @since 1.13.0
			 *
			 * @param array $args Query arguments.
			 */
			$args = apply_filters(
				'get_all_resources_args',
				array(
					'post_status'      => 'publish',
					'post_type'        => 'bookable_resource',
					'posts_per_page'   => -1,
					'suppress_filters' => true,
				)
			);

			$payload = get_posts( $args );

			$payload = array_map(
				function( $post ) {
					return array(
						'id'   => $post->ID,
						'name' => $post->post_title,
					);
				},
				$payload
			);

			wp_send_json( $payload );
		} catch ( Exception $e ) {
			wp_die();
		}
	}

	/**
	 * Adds the booking to the cart using WC().
	 *
	 * @since 1.13.13
	 */
	public function add_booking_to_cart() {
		check_ajax_referer( 'add-booking-to-cart', 'security' );

		$date = isset( $_GET['date'] ) ? sanitize_text_field( wp_unslash( $_GET['date'] ) ) : '';

		if ( empty( $_GET['product_id'] ) || empty( $date ) ) {
			wp_die();
		}

		$product = wc_get_product( absint( $_GET['product_id'] ) );

		if ( ! is_wc_booking_product( $product ) ) {
			wp_die();
		}

		/**
		 * Filter the link to the product page.
		 *
		 * @since 1.13.13
		 *
		 * @param string             $link    The product link.
		 * @param WC_Product_Booking $product The product object.
		 */
		$link = apply_filters( 'woocommerce_loop_product_link', $product->get_permalink(), $product );

		try {
			/*
			 * At this point we need to check if booking can be
			 * made without any further user selection such as
			 * resources, persons or product add-ons...etc. If so
			 * we cannot add booking to cart via AJAX. Redirect them.
			 */
			if ( $product->get_has_persons() && $product->get_has_person_types() ) {
				wp_send_json(
					array(
						'booked' => false,
						'link'   => esc_url( $link ),
					)
				);
			}

			if ( $product->get_has_resources() && $product->is_resource_assignment_type( 'customer' ) ) {
				wp_send_json(
					array(
						'booked' => false,
						'link'   => esc_url( $link ),
					)
				);
			}

			if ( 'customer' === $product->get_duration_type() ) {
				wp_send_json(
					array(
						'booked' => false,
						'link'   => esc_url( $link ),
					)
				);
			}

			if ( 'hour' === $product->get_duration_unit() || 'minute' === $product->get_duration_unit() ) {
				$_POST['wc_bookings_field_start_date_time'] = $date;
			} else {
				$date_time                                   = new DateTime( $date );
				$_POST['wc_bookings_field_start_date_month'] = $date_time->format( 'm' );
				$_POST['wc_bookings_field_start_date_day']   = $date_time->format( 'd' );
				$_POST['wc_bookings_field_start_date_year']  = $date_time->format( 'Y' );
			}

			$added = WC()->cart->add_to_cart(
				$product->get_id()
			);

			wp_send_json(
				array(
					'booked' => false !== $added,
					'link'   => esc_url( $link ),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json(
				array(
					'booked' => false,
					'link'   => esc_url( $link ),
				)
			);
		}
	}
}
