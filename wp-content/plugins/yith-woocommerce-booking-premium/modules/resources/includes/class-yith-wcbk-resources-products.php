<?php
/**
 * Class YITH_WCBK_Resources_Products
 * Handle products for the Resources module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resources_Products' ) ) {
	/**
	 * YITH_WCBK_Resources_Products class.
	 */
	class YITH_WCBK_Resources_Products {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Resources_Products constructor.
		 */
		protected function __construct() {
			YITH_WCBK_Resources_Product_Data_Extension::get_instance();

			add_action( 'yith_wcbk_admin_ajax_resources_get_resources', array( $this, 'ajax_get_resources' ) );

			// Booking form and availability.
			add_action( 'yith_wcbk_booking_form_content', array( $this, 'maybe_print_resource_field' ), 5, 1 );
			add_action( 'yith_wcbk_frontend_ajax_resources_get_booking_availability', array( $this, 'ajax_get_booking_availability' ) );

			add_filter( 'yith_wcbk_booking_helper_count_booked_bookings_in_period_query_args', array( $this, 'filter_count_booked_bookings_in_period_query_args' ), 10, 2 );

			add_filter( 'yith_wcbk_booking_is_available_data', array( $this, 'filter_product_is_available_data' ), 10, 3 );
			add_filter( 'yith_wcbk_ajax_booking_available_times_availability_args', array( $this, 'filter_availability_args_in_ajax_requests' ), 10, 2 );
			add_filter( 'yith_wcbk_ajax_booking_non_available_dates_availability_args', array( $this, 'filter_availability_args_in_ajax_requests' ), 10, 2 );
			add_filter( 'yith_wcbk_booking_product_calculated_price_totals', array( $this, 'filter_product_calculated_price_totals' ), 10, 4 );
			add_filter( 'yith_wcbk_disable_day_if_no_time_available_bookings_count', array( $this, 'filter_bookings_count_when_checking_for_disabling_day_if_no_time_available' ), 10, 3 );

			// Cart.
			add_filter( 'yith_wcbk_cart_get_booking_data_from_request', array( $this, 'filter_booking_data_from_request' ), 10, 3 );
			add_filter( 'yith_wcbk_cart_get_booking_data_from_booking', array( $this, 'filter_booking_data_from_booking' ), 10, 2 );
			add_filter( 'yith_wcbk_cart_get_availability_args_from_booking_data', array( $this, 'filter_availability_args_from_booking_data' ), 10, 2 );
			add_filter( 'yith_wcbk_cart_booking_item_data_before_totals', array( $this, 'filter_cart_booking_item_data' ), 10, 3 );
			add_filter( 'yith_wcbk_add_to_cart_validation', array( $this, 'filter_add_to_cart_validation' ), 10, 3 );
		}

		/**
		 * Get resources.
		 */
		public function ajax_get_resources() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$search    = sanitize_text_field( wp_unslash( $_REQUEST['search'] ?? '' ) );
			$page      = max( 1, absint( $_REQUEST['page'] ?? 1 ) );
			$resources = yith_wcbk_get_resources(
				array(
					'items_per_page' => 5,
					'search'         => $search,
					'order'          => 'ASC',
					'order_by'       => 'title',
					'return'         => 'objects',
					'paginate'       => true,
					'page'           => $page,
				)
			);

			$resources->items = array_map(
				function ( YITH_WCBK_Resource $resource ) {
					return array(
						'id'    => $resource->get_id(),
						'name'  => $resource->get_name(),
						'image' => $resource->get_image( 'thumbnail', array(), true ),
					);
				},
				$resources->items
			);

			$resources->is_search = ! ! $search;

			wp_send_json_success( $resources );

			// phpcs:enable
		}

		/**
		 * Print resource selector, if the booking product has resources.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function maybe_print_resource_field( WC_Product_Booking $product ) {
			if ( $product->has_resources() ) {
				yith_wcbk_get_module_template( 'resources', 'booking-form/resources.php', compact( 'product' ), 'single-product/add-to-cart/' );
			}
		}

		/**
		 * Filter product is available based on resources.
		 *
		 * @param array              $available_data Available data.
		 * @param array              $args           Arguments.
		 * @param WC_Product_Booking $product        The booking product.
		 *
		 * @return array
		 */
		public function filter_product_is_available_data( array $available_data, array $args, WC_Product_Booking $product ): array {
			$available             = $available_data['available'] ?? true;
			$non_available_reasons = $available_data['non_available_reasons'] ?? array();
			if ( $available ) {
				$resources_data = $product->get_resources_data();
				if ( $resources_data ) {
					$from = $args['parsed_from'] ?? time();
					$to   = $args['parsed_to'] ?? time();

					$assignment = $product->get_resource_assignment();

					if ( 'assign-all' === $assignment ) {
						foreach ( $resources_data as $resource_data ) {
							$resource = $resource_data->get_resource();
							if ( $resource ) {
								$availability = $resource->check_availability( $product, $from, $to, $args );
								if ( ! $availability['available'] ) {
									$available             = false;
									$non_available_reasons = array_merge( $non_available_reasons, $availability['non_available_reasons'] );
									break;
								}
							}
						}
					} elseif ( 'automatically-assign-one' === $assignment && empty( $args['resources'] ) ) {
						$resources_availability       = false;
						$max_booking_per_unit_reasons = array();
						foreach ( $resources_data as $resource_data ) {
							$resource = $resource_data->get_resource();
							if ( $resource ) {
								$availability = $resource->check_availability( $product, $from, $to, $args );
								if ( $availability['available'] ) {
									$resources_availability = true;
									break;
								} else {
									if ( isset( $availability['non_available_reasons_raw']['resource-max-bookings-per-unit'] ) ) {
										$max_booking_per_unit_reasons[ $resource->get_id() ] = $availability['non_available_reasons_raw']['resource-max-bookings-per-unit'];
									}
								}
							}
						}
						if ( ! $resources_availability ) {
							$available = false;

							if ( $max_booking_per_unit_reasons ) {
								yith_wcbk_array_sort( $max_booking_per_unit_reasons, 'remained', 0 );
								$greatest_reason = end( $max_booking_per_unit_reasons );

								$non_available_reasons['resource-max-bookings-per-unit'] = $greatest_reason['message'] ?? '';
							} else {
								// translators: %s is the product name.
								$non_available_reasons['resource-no-resource-available'] = sprintf( __( '%s is not available on the dates selected, since there is no resource available.', 'yith-booking-for-woocommerce' ), $product->get_title() );
							}
						}
					} elseif ( in_array( $assignment, array( 'customer-select-one', 'customer-select-more' ), true ) || ( 'automatically-assign-one' === $assignment && ! empty( $args['resources'] ) ) ) {
						$resources = (array) ( $args['resources'] ?? array() );

						foreach ( $resources as $resource_id ) {
							$resource = yith_wcbk_get_resource( $resource_id );
							if ( $resource ) {
								$availability = $resource->check_availability( $product, $from, $to, $args );
								if ( ! $availability['available'] ) {
									$available             = false;
									$non_available_reasons = array_merge( $non_available_reasons, $availability['non_available_reasons'] );
									break;
								}
							}
						}
					}
				}

				$available_data['available']             = $available;
				$available_data['non_available_reasons'] = $non_available_reasons;
			}

			return $available_data;
		}

		/**
		 * Get booking availability data in AJAX to update the booking form.
		 */
		public function ajax_get_booking_availability() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$product_id   = absint( $_REQUEST['product_id'] ?? 0 );
			$resource_ids = wc_clean( wp_unslash( $_REQUEST['resources'] ?? array() ) );
			$resource_ids = array_filter( array_map( 'absint', $resource_ids ) );

			$product = yith_wcbk_get_booking_product( $product_id );
			if ( ! ! $product ) {
				$date_info = yith_wcbk_get_booking_form_date_info( $product );
				list( $current_year, $current_month, $next_year, $next_month ) = yith_plugin_fw_extract( $date_info, 'current_year', 'current_month', 'next_year', 'next_month' );
				$args = array(
					'range'                  => 'day',
					'exclude_booked'         => false,
					'check_start_date'       => false,
					'check_min_max_duration' => yith_wcbk()->settings->check_min_max_duration_in_calendar(),
					'resources'              => $resource_ids,
				);

				$dates = $product->get_non_available_dates( $current_year, $current_month, $next_year, $next_month, $args );

				wp_send_json_success(
					array(
						'non_available_dates' => $dates,
						'date_info'           => $date_info,
					)
				);
			}
			// phpcs:enable
		}

		/**
		 * Retrieve the first available resource for a booking product.
		 * Useful to automatically assign a resource if the assignment is set to be automatic.
		 *
		 * @param WC_Product_Booking $product The product.
		 * @param int                $from    'From' timestamp.
		 * @param int                $to      'To' timestamp.
		 * @param array              $args    Extra arguments.
		 *
		 * @return false|int The resource ID; or false if no available resource was found.
		 */
		protected function get_first_available_resource( WC_Product_Booking $product, int $from, int $to, array $args = array() ) {
			$resources_data = $product->get_resources_data();

			foreach ( $resources_data as $resource_data ) {
				$resource = $resource_data->get_resource();

				if ( $resource->is_available( $product, $from, $to, array_merge( $args, array( 'include_bookings_in_cart' => true ) ) ) ) {
					return $resource->get_id();
				}
			}

			return false;

		}

		/**
		 * Include resources from request in booking data.
		 *
		 * @param array              $booking_data Booking data.
		 * @param array              $request      The request.
		 * @param WC_Product_Booking $product      The booking product.
		 *
		 * @return array
		 */
		public function filter_booking_data_from_request( array $booking_data, array $request, WC_Product_Booking $product ): array {
			if ( $product->has_resources() ) {
				if ( isset( $request['resource_ids'] ) ) {
					$resources                    = (array) $request['resource_ids'];
					$resources                    = array_filter( array_map( 'absint', $resources ) );
					$booking_data['resource_ids'] = $resources;
				} else {
					if ( 'automatically-assign-one' === $product->get_resource_assignment() && isset( $booking_data['from'], $booking_data['to'] ) ) {
						$args = array();
						if ( ! empty( $request['persons'] ) ) {
							$args['persons'] = $request['persons'];
						}
						$resource_id = $this->get_first_available_resource( $product, $booking_data['from'], $booking_data['to'], $args );
						if ( $resource_id ) {
							$booking_data['resource_ids'] = array( $resource_id );
						}
					}
				}
			}

			return $booking_data;
		}

		/**
		 * Include resources from booking in booking data.
		 * Useful when adding to the cart a booking after confirmation.
		 *
		 * @param array             $booking_data Booking data.
		 * @param YITH_WCBK_Booking $booking      The booking.
		 *
		 * @return array
		 */
		public function filter_booking_data_from_booking( array $booking_data, YITH_WCBK_Booking $booking ): array {
			$resource_ids = $booking->get_resource_ids();
			if ( $resource_ids ) {
				$booking_data['resource_ids'] = $resource_ids;
			}

			return $booking_data;
		}

		/**
		 * Filter availability args from booking data.
		 * This is useful since we have `resource_ids` in booking_data, but we want to have `resources` in availability args.
		 *
		 * @param array $args         Availability args.
		 * @param array $booking_data Booking data.
		 *
		 * @return array
		 */
		public function filter_availability_args_from_booking_data( array $args, array $booking_data ): array {
			if ( isset( $booking_data['resource_ids'] ) ) {
				$resources         = (array) $booking_data['resource_ids'];
				$resources         = array_filter( array_map( 'absint', $resources ) );
				$args['resources'] = $resources;
			}

			return $args;
		}

		/**
		 * Filter availability args in ajax requests.
		 *
		 * @param array $args    Arguments.
		 * @param array $request The request.
		 *
		 * @return array
		 */
		public function filter_availability_args_in_ajax_requests( array $args, array $request ): array {
			if ( ! empty( $request['resource_ids'] ) ) {
				$resources         = (array) $request['resource_ids'];
				$resources         = array_filter( array_map( 'absint', $resources ) );
				$args['resources'] = $resources;
			}

			return $args;
		}

		/**
		 * Filter cart item data.
		 *
		 * @param array              $item_data The booking item data.
		 * @param array              $cart_item The cart item.
		 * @param WC_Product_Booking $product   The booking product.
		 *
		 * @return array
		 */
		public function filter_cart_booking_item_data( array $item_data, array $cart_item, WC_Product_Booking $product ): array {
			$booking_data = $cart_item['yith_booking_data'];

			$resource_ids = array_filter( array_map( 'absint', $booking_data['resource_ids'] ?? array() ) );
			if ( $resource_ids ) {
				$assignment = $product->get_resource_assignment();
				if ( in_array( $assignment, array( 'customer-select-one', 'customer-select-more' ), true ) ) {
					$resources      = array_filter( array_map( 'yith_wcbk_get_resource', $resource_ids ) );
					$label          = $product->get_resources_label();
					$default_label  = 'customer-select-one' === $assignment ? __( 'Resource', 'yith-booking-for-woocommerce' ) : __( 'Resources', 'yith-booking-for-woocommerce' );
					$label          = ! ! $label ? $label : $default_label;
					$resource_names = array_map(
						function ( YITH_WCBK_Resource $resource ) {
							return $resource->get_name();
						},
						$resources
					);

					$item_data['yith_booking_resources'] = array(
						'key'   => $label,
						'value' => implode( ', ', $resource_names ),
					);
				}
			}

			return $item_data;
		}

		/**
		 * Filter totals to add resources' costs.
		 *
		 * @param array              $totals    The totals.
		 * @param array              $args      Arguments.
		 * @param bool               $formatted Formatted flag.
		 * @param WC_Product_Booking $product   The booking product.
		 *
		 * @return array
		 */
		public function filter_product_calculated_price_totals( array $totals, array $args, bool $formatted, WC_Product_Booking $product ): array {
			$resource_ids   = (array) ( $args['resource_ids'] ?? array() );
			$resources_data = $product->get_resources_data();

			$price = 0;

			foreach ( $resource_ids as $id ) {
				$resource_data = $resources_data[ $id ] ?? false;
				if ( $resource_data ) {
					$people_number        = $args['persons'] ?? 1;
					$duration             = $args['duration'] ?? 1;
					$default_people_types = array(
						array(
							'id'     => 0,
							'number' => $people_number,
						),
					);

					$base_price  = $resource_data->get_base_price();
					$fixed_price = $resource_data->get_fixed_price();

					if ( $base_price ) {
						if ( $resource_data->get_multiply_base_price_per_person() ) {
							$people_types = $args['person_types'] ?? $default_people_types;
							$persons      = array_sum( yith_wcbk_booking_person_types_to_id_number_array( $people_types ) );
							$base_price   = $base_price * $persons;
						}

						$base_price = $base_price * $duration;

						$price += $base_price;
					}

					if ( $fixed_price ) {
						if ( $resource_data->get_multiply_fixed_price_per_person() ) {
							$people_types = $args['person_types'] ?? $default_people_types;
							$persons      = array_sum( yith_wcbk_booking_person_types_to_id_number_array( $people_types ) );
							$fixed_price  = $fixed_price * $persons;
						}

						$price += $fixed_price;
					}
				}
			}

			if ( $price ) {
				$label               = $product->get_resources_label();
				$label               = ! ! $label ? $label : __( 'Resources', 'yith-booking-for-woocommerce' );
				$totals['resources'] = array(
					'label' => $label,
					'value' => $price,
				);
			}

			return $totals;
		}

		/**
		 * Filter bookings count when checking for disabling day if no time is available.
		 *
		 * @param int                $number     The number of booked bookings.
		 * @param array              $count_args Count arguments.
		 * @param WC_Product_Booking $product    The booking product.
		 *
		 * @return int
		 */
		public function filter_bookings_count_when_checking_for_disabling_day_if_no_time_available( int $number, array $count_args, WC_Product_Booking $product ): int {
			if ( isset( $count_args['resources'], $count_args['from'], $count_args['to'] ) ) {
				$count_args       = array(
					'resources'         => $count_args['resources'],
					'from'              => $count_args['from'],
					'to'                => $count_args['to'],
					'include_externals' => false,
					'exclude_booked'    => false,
				);
				$booked_resources = yith_wcbk_booking_helper()->count_booked_bookings_in_period( $count_args );

				$number += $booked_resources;
			}

			return $number;
		}

		/**
		 * Filter query args when counting booked bookings in period.
		 * This to allow counting booked resources
		 *
		 * @param array $query_args Query args.
		 * @param array $args       Arguments passed to the function.
		 *
		 * @return array
		 */
		public function filter_count_booked_bookings_in_period_query_args( array $query_args, array $args ): array {
			if ( ! empty( $args['resources'] ) ) {
				$resources               = (array) $args['resources'];
				$resources               = array_filter( array_map( 'absint', $resources ) );
				$query_args['resources'] = $resources;
			}

			return $query_args;
		}

		/**
		 * Filter add-to-cart validation.
		 * This will check for:
		 * 1. validation if resources are required and not set in cart,
		 * 2. validation if there are other resources booked in the same cart.
		 *
		 * @param bool               $passed_validation Validation flag.
		 * @param WC_Product_Booking $product           The booking product.
		 * @param array              $booking_data      The booking data.
		 *
		 * @return bool
		 */
		public function filter_add_to_cart_validation( bool $passed_validation, WC_Product_Booking $product, array $booking_data ): bool {
			if ( $product->has_resources() ) {
				$assignment   = $product->get_resource_assignment();
				$label        = $product->get_resources_label();
				$label        = ! ! $label ? $label : __( 'Resource', 'yith-booking-for-woocommerce' );
				$resource_ids = array_map( 'absint', (array) ( $booking_data['resource_ids'] ?? array() ) );
				asort( $resource_ids );

				$from                      = $booking_data['from'];
				$to                        = $booking_data['to'];
				$persons                   = max( 1, absint( $booking_data['persons'] ?? 1 ) );
				$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();

				if ( $passed_validation ) {
					$product_link = "<a href='{$product->get_permalink()}'>{$product->get_title()}</a>";
					// translators: %s is the product link.
					$generic_error = sprintf( __( 'There was an error while processing the booking. Please try again: %s.', 'yith-booking-for-woocommerce' ), $product_link );

					switch ( $assignment ) {
						case 'automatically-assign-one':
							if ( ! $resource_ids ) {
								$remained_messages = array();
								if ( $count_persons_as_bookings && $product->has_people() ) {
									$resources_data = $product->get_resources_data();
									foreach ( $resources_data as $resource_data ) {
										$resource = $resource_data->get_resource();
										if ( $resource ) {
											$available_quantity = $resource->get_available_quantity();
											if ( $available_quantity ) {
												$handler = $resource->availability_handler( $product );

												$number_of_bookings = $handler->count_booked( $from, $to, array( 'include_bookings_in_cart' => true ) );
												$booking_weight     = $persons;

												if ( $number_of_bookings + $booking_weight > $available_quantity ) {
													$remained = $available_quantity - $number_of_bookings;

													if ( $remained > 0 ) {
														$remained_messages[] = array(
															// translators: %s is the remaining people number.
															'message'  => sprintf( __( 'Too many people selected (%s remaining)', 'yith-booking-for-woocommerce' ), $remained ),
															'remained' => $remained,
														);
													}
												}
											}
										}
									}
								}

								if ( $remained_messages ) {
									yith_wcbk_array_sort( $remained_messages, 'remained', 0 );
									$remained_message = end( $remained_messages )['message'];

									// translators: 1. the product name; 2. list of reasons why the product is not available.
									wc_add_notice( sprintf( __( '%1$s is not available: %2$s', 'yith-booking-for-woocommerce' ), $product->get_title(), $remained_message . '.' ), 'error' );
								} else {
									// translators: %s is the product name.
									wc_add_notice( sprintf( __( '%s is not available on the dates selected, since there is no resource available.', 'yith-booking-for-woocommerce' ), $product->get_title() ), 'error' );
								}
								$passed_validation = false;
							}
							break;
						case 'assign-all':
							if ( ! $resource_ids ) {
								wc_add_notice( $generic_error );
								$passed_validation = false;
							} else {
								$resources_data        = $product->get_resources_data();
								$required_resource_ids = array_map( 'absint', array_keys( $resources_data ) );
								asort( $required_resource_ids );

								if ( $resource_ids !== $required_resource_ids ) {
									wc_add_notice( $generic_error );
									$passed_validation = false;
								}
							}
							break;
						case 'customer-select-one':
							if ( ! $resource_ids && ! ! $product->get_resource_is_required() ) {
								// translators: %s is the label for the "resources". Example: Please select an option for "Employee".
								wc_add_notice( sprintf( __( 'Please select an option for &quot;%s&quot;', 'yith-booking-for-woocommerce' ), $label ), 'error' );
								$passed_validation = false;
							}
							break;
						case 'customer-select-more':
						default:
							// Resources are optionals in this case.
							break;
					}
				}

				if ( $passed_validation ) {

					/**
					 * The resources.
					 *
					 * @var YITH_WCBK_Resource[] $resources
					 */
					$resources = array_filter( array_map( 'yith_wcbk_get_resource', $resource_ids ) );

					foreach ( $resources as $resource ) {
						$available_quantity = $resource->get_available_quantity();
						if ( $available_quantity ) {
							$handler = $resource->availability_handler( $product );

							$number_of_bookings = $handler->count_booked( $from, $to, array( 'include_bookings_in_cart' => true ) );
							$booking_weight     = ! ! $count_persons_as_bookings ? $persons : 1;

							if ( $number_of_bookings + $booking_weight > $available_quantity ) {
								$remained       = $available_quantity - $number_of_bookings;
								$extra_messages = array();

								if ( $remained > 0 ) {
									if ( $product->has_people() && $product->has_count_people_as_separate_bookings_enabled() ) {
										// translators: %s is the remaining people number.
										$extra_messages[] = sprintf( __( 'Too many people selected (%s remaining)', 'yith-booking-for-woocommerce' ), $remained );
									} else {
										// translators: %s is the remaining quantity.
										$extra_messages[] = sprintf( __( '%s remaining', 'yith-booking-for-woocommerce' ), $remained );
									}
								}

								$extra_messages = implode( ' ', $extra_messages );

								$message = sprintf(
								// translators: %s is the product name.
									__( 'You cannot add &quot;%s&quot; to your cart for the dates selected, since it shares one or more resources with other bookings you have in your cart.', 'yith-booking-for-woocommerce' ),
									$product->get_title()
								);

								if ( 'customer-select-one' === $assignment ) {
									if ( ! ! $product->get_resource_is_required() ) {
										$message = sprintf(
										// translators: %s is the resource name.
											__( 'You cannot add another booking with &quot;%s&quot; to your cart in the dates you selected.', 'yith-booking-for-woocommerce' ),
											$resource->get_name()
										);
									}
								}

								if ( $extra_messages ) {
									$message .= ' ' . $extra_messages . '.'; // Add full stop, since the "extra_messages" need to not have the full stop, to allow unique translation with other occurrences in non-available reasons.
								}

								$notice = sprintf(
									'<a href="%s" class="button wc-forward">%s</a> %s',
									wc_get_cart_url(),
									__( 'View cart', 'woocommerce' ),
									$message
								);
								wc_add_notice( $notice, 'error' );
								$passed_validation = false;
							}
						}
					}
				}
			}

			return $passed_validation;
		}

		/**
		 * Get product IDs having specific resources.
		 *
		 * @param int|int[] $resource_ids Resource IDs.
		 * @param string    $operator     The operator (Allowed values: in, and).
		 *
		 * @return array
		 */
		public static function get_product_ids_with_resources( $resource_ids, string $operator = 'in' ): array {
			global $wpdb;
			$resource_ids = (array) $resource_ids;
			$resource_ids = array_filter( array_map( 'absint', $resource_ids ) );
			$product_ids  = array();

			if ( $resource_ids ) {
				$value_placeholder = '(' . substr( str_repeat( ',%d', count( $resource_ids ) ), 1 ) . ')';
				switch ( $operator ) {
					case 'and':
						$num_terms = count( $resource_ids );

						$where_sub_query = "(
									SELECT COUNT(1)
									FROM $wpdb->yith_wcbk_product_resources
									WHERE resource_id IN $value_placeholder
									AND product_id = main.product_id
								) = $num_terms";

						$where_clauses = $wpdb->prepare( $where_sub_query, $resource_ids ); // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.NotPrepared

						$product_ids = $wpdb->get_col(
							$wpdb->prepare(
								"SELECT DISTINCT product_id FROM $wpdb->yith_wcbk_product_resources as main WHERE $where_clauses", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$resource_ids
							)
						);

						$product_ids = array_filter( array_map( 'absint', $product_ids ) );

						break;
					case 'in':
					default:
						$product_ids = $wpdb->get_col(
							$wpdb->prepare(
								"SELECT DISTINCT product_id FROM $wpdb->yith_wcbk_product_resources WHERE resource_id IN $value_placeholder", // phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
								$resource_ids
							)
						);

						$product_ids = array_filter( array_map( 'absint', $product_ids ) );
						break;
				}
			}

			return $product_ids;
		}
	}
}
