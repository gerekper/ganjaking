<?php
/**
 * Functions
 *
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_search_forms' ) ) {
	/**
	 * Get search forms.
	 *
	 * @param array $args The arguments.
	 *
	 * @return int[]|YITH_WCBK_Search_Form[]|false|object
	 * @since 4.0.0
	 */
	function yith_wcbk_get_search_forms( array $args = array() ) {
		try {
			/**
			 * The Data Store
			 *
			 * @var YITH_WCBK_Search_Form_Data_Store $data_store
			 */
			$data_store = WC_Data_Store::load( 'yith-booking-search-form' );

			return $data_store->query( $args );
		} catch ( Exception $e ) {
			return false;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_search_form' ) ) {
	/**
	 * Get the search form object.
	 *
	 * @param int|YITH_WCBK_Search_Form|WP_Post $search_form The object.
	 *
	 * @return YITH_WCBK_Search_Form|bool
	 */
	function yith_wcbk_get_search_form( $search_form ) {
		try {
			$the_search_form = new YITH_WCBK_Search_Form( $search_form );
		} catch ( Exception $e ) {
			$the_search_form = false;
		}

		return apply_filters( 'yith_wcbk_get_search_form', $the_search_form );
	}
}

if ( ! function_exists( 'yith_wcbk_search_booking_products' ) ) {
	/**
	 * Search booking products.
	 *
	 * @param array $args The arguments.
	 *
	 * @return int[]|YITH_WCBK_Search_Form[]|false|object
	 */
	function yith_wcbk_search_booking_products( array $args = array() ) {
		$from           = $args['from'] ?? '';
		$to             = $args['to'] ?? '';
		$persons        = $args['persons'] ?? false;
		$person_types   = $args['person_types'] ?? array();
		$services       = $args['services'] ?? array();
		$categories     = $args['categories'] ?? array();
		$tags           = $args['tags'] ?? array();
		$location       = $args['location'] ?? '';
		$location_range = absint( $args['location_range'] ?? 30 );
		$search         = $args['s'] ?? '';

		$maps = yith_wcbk()->maps();

		$location_coord = ! ! $maps ? $maps->get_location_by_address( $location ) : false;

		if ( ! ! $person_types && is_array( $person_types ) ) {
			$persons = 0;
			foreach ( $person_types as $person_type_id => $person_type_number ) {
				$persons += absint( $person_type_number );
				if ( ! absint( $person_type_number ) ) {
					unset( $person_types[ $person_type_id ] );
				}
			}
		}

		$search_args = array(
			'yith_wcbk_search' => 1,
			'posts_per_page'   => -1,
			'post_type'        => 'product',
			'post_status'      => 'publish',
			'fields'           => 'ids',
			'meta_query'       => array(
				'relation' => 'AND',
			),
			'tax_query'        => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
				),
			),
		);

		if ( $search ) {
			$search_args['s'] = $search;
		}

		if ( $persons > 0 ) {
			$search_args['meta_query'][] = array(
				'key'     => '_yith_booking_min_persons',
				'value'   => $persons,
				'compare' => '<=',
				'type'    => 'numeric',
			);

			$search_args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key'     => '_yith_booking_max_persons',
					'value'   => $persons,
					'compare' => '>=',
					'type'    => 'numeric',
				),
				array(
					'key'     => '_yith_booking_max_persons',
					'value'   => 1,
					'compare' => '<',
					'type'    => 'numeric',
				),
			);
		}

		if ( ! ! $services && is_array( $services ) ) {
			$search_args['tax_query'][] = array(
				'taxonomy' => YITH_WCBK_Post_Types::SERVICE_TAX,
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', $services ),
				'operator' => 'AND',
			);
		}

		if ( ! ! $categories ) {
			$categories                 = is_array( $categories ) ? $categories : explode( ',', $categories );
			$search_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', $categories ),
			);
		}

		if ( ! ! $tags && is_array( $tags ) ) {
			$search_args['tax_query'][] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'term_id',
				'terms'    => array_map( 'absint', $tags ),
				'operator' => 'AND',
			);
		}

		if ( ! ! $location && ! ! ( $location_coord ) ) {
			// Location approximation for database query.
			$earth_radius   = 6371;
			$location_range = min( $location_range, $earth_radius );
			$lat            = $location_coord['lat'];
			$lng            = $location_coord['lng'];
			$delta_lat      = rad2deg( $location_range / $earth_radius );
			$delta_lng      = rad2deg( asin( $location_range / $earth_radius ) / cos( deg2rad( $lat ) ) );
			$max_lat        = min( $lat + $delta_lat, 90 );
			$min_lat        = max( $lat - $delta_lat, -90 );
			$max_lng        = min( $lng + $delta_lng, 180 );
			$min_lng        = max( $lng - $delta_lng, -180 );

			$search_args['meta_query'][] = array(
				'relation' => 'AND',
				array(
					'key'     => '_yith_booking_location_lat',
					'value'   => array( $min_lat, $max_lat ),
					'compare' => 'BETWEEN',
					'type'    => 'DECIMAL(10,5)',
				),
				array(
					'key'     => '_yith_booking_location_lng',
					'value'   => array( $min_lng, $max_lng ),
					'compare' => 'BETWEEN',
					'type'    => 'DECIMAL(10,5)',
				),
			);
		}

		/**
		 * DO_ACTION: yith_wcbk_search_booking_products_before_get_results
		 * Hook to perform any action before retrieving results when searching for bookable products.
		 *
		 * @param array $search_args The search arguments (used by the get_posts function).
		 * @param array $args        The function args.
		 */
		do_action( 'yith_wcbk_search_booking_products_before_get_results', $search_args, $args );

		$search_args = apply_filters( 'yith_wcbk_search_booking_products_search_args', $search_args, $args );

		$product_ids = apply_filters( 'yith_wcbk_search_booking_products_search_results', null, $search_args, $args );
		if ( is_null( $product_ids ) ) {
			$product_ids = get_posts( $search_args );
		}

		/**
		 * DO_ACTION: yith_wcbk_search_booking_products_after_get_results
		 * Hook to perform any action after retrieving results when searching for bookable products.
		 *
		 * @param array $search_args The search arguments (used by the get_posts function).
		 * @param array $args        The function args.
		 */
		do_action( 'yith_wcbk_search_booking_products_after_get_results', $search_args, $args );

		// Remove unavailable bookings.
		if ( ! ! $product_ids && is_array( $product_ids ) ) {
			$availability_args = array(
				'from' => ! ! $from ? strtotime( $from ) : false,
				'to'   => ! ! $to ? strtotime( $to ) : false,
			);
			foreach ( $product_ids as $product_id ) {
				$the_product       = wc_get_product( $product_id );
				$remove_product_id = true;

				if ( $the_product && $the_product instanceof WC_Product_Booking ) {
					$remove_product_id = false;
					if ( ! ! $person_types && $the_product->has_people_types_enabled() ) {
						$persons = 0;
						foreach ( $the_product->get_people_types() as $current_person_type ) {
							$current_person_type_id = $current_person_type['id'];

							if ( yith_plugin_fw_is_true( $current_person_type['enabled'] ) ) {
								if ( isset( $person_types[ $current_person_type_id ] ) && '' !== $person_types[ $current_person_type_id ] ) {
									$requested_number = absint( $person_types[ $current_person_type_id ] );
									$min_number       = absint( $current_person_type['min'] );
									$max_number       = absint( $current_person_type['max'] );
									if ( $requested_number < $min_number || ( $max_number > 0 && $requested_number > $max_number ) ) {
										$remove_product_id = true;
									}
									$persons += $requested_number;
								}
							} else {
								if ( isset( $person_types[ $current_person_type_id ] ) && $person_types[ $current_person_type_id ] > 0 ) {
									$remove_product_id = true;
								}
							}
						}
						$availability_args['persons'] = $persons;
					}

					if ( ! $the_product->has_time() && ! apply_filters( 'yith_wcbk_search_booking_products_show_daily_bookings_with_at_least_one_day_available', false ) ) {
						if ( ! ! $from && ! $the_product->is_available( $availability_args ) ) {
							$remove_product_id = true;
						}
					} else {
						if ( ! ! $from ) {
							$_from                      = strtotime( $from );
							$_to                        = ! ! $to ? strtotime( $to ) : $_from;
							$_current_day               = $_from;
							$has_at_least_one_time_slot = false;
							$relative_minimum_duration  = $the_product->get_minimum_duration() * $the_product->get_duration();
							$duration_unit              = $the_product->get_duration_unit();

							while ( $_current_day <= $_to ) {
								if ( $the_product->has_time() ) {
									if ( $the_product->has_at_least_one_time_slot_available_on( $_current_day ) ) {
										$has_at_least_one_time_slot = true;
										break;
									}
								} else {
									$min_to = yith_wcbk_date_helper()->get_time_sum( $_current_day, $relative_minimum_duration, $duration_unit, true );
									if (
										$min_to <= $_to
										&& $the_product->is_available(
											array(
												'from' => $_current_day,
												'to'   => $min_to,
											)
										)
									) {
										$has_at_least_one_time_slot = true;
										break;
									}
								}

								$_current_day = strtotime( 'tomorrow', $_current_day );
							}

							if ( ! $has_at_least_one_time_slot ) {
								$remove_product_id = true;
							}
						}
					}

					if ( ! $remove_product_id && ! ! $location_coord ) {
						$product_location_coord = $the_product->get_location_coordinates();

						if ( ! ! $product_location_coord ) {
							$distance = ! ! $maps ? $maps->calculate_distance( $location_coord, $product_location_coord ) : false;

							if ( false !== $distance && floatval( $location_range ) < floatval( $distance ) ) {
								$remove_product_id = true;
							}
						} else {
							$remove_product_id = true;
						}
					}
				}

				if ( $remove_product_id ) {
					$product_ids = array_diff( $product_ids, array( $product_id ) );
				}
			}
			unset( $the_product );
		}

		return apply_filters( 'yith_wcbk_search_booking_products', $product_ids, $args );
	}
}
