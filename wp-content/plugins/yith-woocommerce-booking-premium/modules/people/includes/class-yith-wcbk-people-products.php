<?php
/**
 * Class YITH_WCBK_People_Products
 * Handle products for the People module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_People_Products' ) ) {
	/**
	 * YITH_WCBK_People_Products class.
	 */
	class YITH_WCBK_People_Products {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Resources_Products constructor.
		 */
		protected function __construct() {
			YITH_WCBK_People_Product_Data_Extension::get_instance();

			// Booking form and availability.
			add_action( 'yith_wcbk_booking_form_content', array( $this, 'maybe_print_people_field' ), 20, 1 );
			add_filter( 'yith_wcbk_booking_is_available_data_static', array( $this, 'filter_product_is_available_data_static' ), 10, 3 );

			// Cart.
			add_filter( 'yith_wcbk_cart_get_booking_data_from_request', array( $this, 'filter_booking_data_from_request' ), 10, 3 );
			add_filter( 'yith_wcbk_cart_get_booking_data_from_booking', array( $this, 'filter_booking_data_from_booking' ), 10, 2 );
			add_filter( 'yith_wcbk_cart_get_booking_props_from_booking_data', array( $this, 'filter_booking_props_from_booking_data' ), 10, 1 );
			add_filter( 'yith_wcbk_cart_get_availability_args_from_booking_data', array( $this, 'filter_availability_args_from_booking_data' ), 10, 2 );
			add_filter( 'yith_wcbk_cart_booking_item_data_before_totals', array( $this, 'filter_cart_booking_item_data' ), 10, 3 );
			add_filter( 'yith_wcbk_add_to_cart_validation', array( $this, 'filter_add_to_cart_validation' ), 10, 3 );
		}

		/**
		 * Print resource selector, if the booking product has resources.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function maybe_print_people_field( WC_Product_Booking $product ) {
			if ( $product->has_people() ) {
				yith_wcbk_get_module_template( 'people', 'booking-form/persons.php', compact( 'product' ), 'single-product/add-to-cart/' );
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
		public function filter_product_is_available_data_static( array $available_data, array $args, WC_Product_Booking $product ): array {
			$available             = $available_data['available'] ?? true;
			$non_available_reasons = $available_data['non_available_reasons'] ?? array();
			$return                = $args['return'] ?? 'bool';
			$include_reasons       = 'array' === $return;
			$check_person_number   = $args['check_person_number'] ?? true;
			$persons               = isset( $args['persons'] ) ? max( 1, absint( $args['persons'] ) ) : $product->get_minimum_number_of_people();

			if ( ( $available || $include_reasons ) && $check_person_number && $product->has_people() ) {
				if ( $persons < $product->get_minimum_number_of_people() ) {
					$available = false;
					// translators: %s is the minimum number of people.
					$non_available_reasons['min-persons'] = sprintf( __( 'Minimum people: %s', 'yith-booking-for-woocommerce' ), $product->get_minimum_number_of_people() );
				}

				if ( $product->get_maximum_number_of_people() && $persons > $product->get_maximum_number_of_people() ) {
					$available = false;
					// translators: %s is the maximum number of people.
					$non_available_reasons['max-persons'] = sprintf( __( 'Maximum people: %s', 'yith-booking-for-woocommerce' ), $product->get_maximum_number_of_people() );
				}

				$available_data['available']             = $available;
				$available_data['non_available_reasons'] = $non_available_reasons;
			}

			return $available_data;
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
			if ( ! empty( $request['person_types'] ) ) {
				$request['person_types'] = yith_wcbk_booking_person_types_to_id_number_array( $request['person_types'] );
			}

			if ( $product->has_people() ) {
				if ( ! empty( $request['person_types'] ) ) {
					$persons = 0;
					foreach ( $request['person_types'] as $person_type_id => $number ) {
						$persons += absint( $number );
					}
					$booking_data['persons'] = $persons;
				}
			} else {
				if ( isset( $booking_data['persons'] ) ) {
					unset( $booking_data['persons'] );
				}
			}

			return $booking_data;
		}

		/**
		 * Include people from booking in booking data.
		 * Useful when adding to the cart a booking after confirmation.
		 *
		 * @param array             $booking_data Booking data.
		 * @param YITH_WCBK_Booking $booking      The booking.
		 *
		 * @return array
		 */
		public function filter_booking_data_from_booking( array $booking_data, YITH_WCBK_Booking $booking ): array {
			$booking_data['persons']      = $booking->get_persons();
			$booking_data['person_types'] = yith_wcbk_booking_person_types_to_id_number_array( $booking->get_person_types() );

			return $booking_data;
		}

		/**
		 * Include people from booking props in booking data.
		 *
		 * @param array $props Props.
		 *
		 * @return array
		 */
		public function filter_booking_props_from_booking_data( array $props ): array {
			if ( isset( $props['person_types'] ) ) {
				$props['person_types'] = yith_wcbk_booking_person_types_to_list( $props['person_types'] );
			}

			return $props;
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
			if ( isset( $booking_data['persons'] ) ) {
				$args['persons'] = $booking_data['persons'];
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

			if ( $product->has_people() ) {
				$persons = $booking_data['persons'] ?? 1;
				if ( ! empty( $booking_data['person_types'] ) ) {
					foreach ( $booking_data['person_types'] as $person_type_id => $person_type_number ) {
						if ( $person_type_number < 1 ) {
							continue;
						}
						$person_type_name = yith_wcbk()->person_type_helper()->get_person_type_title( $person_type_id );

						$item_data[ 'yith_booking_person_type_' . $person_type_id ] = array(
							'key'     => $person_type_name,
							'value'   => $person_type_number,
							'display' => $person_type_number,
						);
					}
				} else {
					$item_data['yith_booking_persons'] = array(
						'key'     => yith_wcbk_get_booking_meta_label( 'persons' ),
						'value'   => $persons,
						'display' => $persons,
					);
				}
			}

			return $item_data;
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
			$persons = max( 1, absint( $booking_data['persons'] ?? 1 ) );

			if ( $product->has_people() ) {
				$min_persons = $product->get_minimum_number_of_people();
				$max_persons = $product->get_maximum_number_of_people();
				if ( $persons < $min_persons ) {
					// translators: %s is the minimum number of people.
					wc_add_notice( sprintf( __( 'Minimum number of people: %s', 'yith-booking-for-woocommerce' ), $min_persons ), 'error' );
					$passed_validation = false;
				}

				if ( $max_persons > 0 && $persons > $max_persons ) {
					// translators: %s is the maximum number of people.
					wc_add_notice( sprintf( __( 'Maximum number of people: %s', 'yith-booking-for-woocommerce' ), $max_persons ), 'error' );
					$passed_validation = false;
				}
			}

			if ( $passed_validation && $product->has_people_types_enabled() ) {
				$people_types         = $product->get_enabled_people_types();
				$request_people_types = yith_wcbk_booking_person_types_to_id_number_array( $booking_data['person_types'] ?? array() );

				foreach ( $people_types as $people_type ) {
					$people_type_id   = $people_type['id'] ?? 0;
					$people_type_name = get_the_title( $people_type_id );
					$min_persons      = absint( $people_type['min'] ?? 0 );
					$max_persons      = absint( $people_type['max'] ?? 0 );
					$current_persons  = absint( $request_people_types[ $people_type_id ] ?? 0 );
					if ( $min_persons && $current_persons < $min_persons ) {
						// translators: 1. person type name; 2. minimum number of people.
						wc_add_notice( sprintf( __( 'Minimum number for "%1$s": %2$s', 'yith-booking-for-woocommerce' ), $people_type_name, $min_persons ), 'error' );
						$passed_validation = false;
					}

					if ( $max_persons > 0 && $current_persons > $max_persons ) {
						// translators: 1. person type name; 2. maximum number of people.
						wc_add_notice( sprintf( __( 'Maximum number for "%1$s": %2$s', 'yith-booking-for-woocommerce' ), $people_type_name, $max_persons ), 'error' );
						$passed_validation = false;
					}
				}
			}

			return $passed_validation;
		}
	}
}
